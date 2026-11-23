<?php
// ARQUIVO: api_assinatura.php - Rotas da assinatura Mercado Pago (servidor)
//
//   POST api_assinatura.php?acao=criar                 → cria assinatura pendente e devolve init_point
//   GET  api_assinatura.php?acao=consultar&id={id}     → consulta (equivale a GET /api/subscriptions/:id)
//   POST api_assinatura.php?acao=ciclo&id={id}         → operacao = pause | reactivate | cancel (só admin)
//
// Sempre exige usuário logado e token CSRF da sessão nas ações POST.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

function responder(int $http, array $dados): void
{
    http_response_code($http);
    echo json_encode($dados);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    responder(401, ['erro' => 'nao_autenticado', 'mensagem' => 'Faça login novamente para continuar.']);
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mp_assinatura.php';

$acao   = $_GET['acao'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'];
$userId = (int) $_SESSION['user_id'];

if ($metodo === 'POST') {
    $csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (empty($_SESSION['assinatura_csrf']) || !hash_equals($_SESSION['assinatura_csrf'], $csrf)) {
        responder(403, ['erro' => 'csrf', 'mensagem' => 'Sessão expirada. Recarregue a página e tente novamente.']);
    }
}

// E-mail e nível vêm do cadastro do usuário logado, nunca do navegador
$stmt = $pdo->prepare('SELECT email, nivel FROM usuarios WHERE id = ? AND ativo = 1');
$stmt->execute([$userId]);
$usuario = $stmt->fetch();
if (!$usuario) {
    responder(403, ['erro' => 'usuario_invalido', 'mensagem' => 'Usuário sem permissão.']);
}

function buscarAssinaturaDoUsuario(PDO $pdo, int $userId, string $preapprovalId): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM assinaturas_mp WHERE usuario_id = ? AND preapproval_id = ?');
    $stmt->execute([$userId, $preapprovalId]);
    return $stmt->fetch() ?: null;
}

function salvarStatus(PDO $pdo, int $id, string $status): void
{
    if (in_array($status, SUBSCRIPTION_STATUSES, true)) {
        $pdo->prepare('UPDATE assinaturas_mp SET status = ? WHERE id = ?')->execute([$status, $id]);
    }
}

try {
    // ---------- Criar assinatura ----------
    if ($acao === 'criar' && $metodo === 'POST') {
        $entrada = json_decode(file_get_contents('php://input'), true) ?: [];
        $ofertaId = (string) ($entrada['oferta'] ?? '');
        $trustedOffer = resolveOffer($ofertaId);
        if ($trustedOffer === null) {
            responder(400, ['erro' => 'oferta_invalida', 'mensagem' => 'Plano não encontrado.']);
        }

        // Evita duplicidade: reaproveita assinatura pendente ou ativa do mesmo usuário
        $stmt = $pdo->prepare("SELECT preapproval_id, status, init_point FROM assinaturas_mp
                               WHERE usuario_id = ? AND oferta = ? AND status IN ('creating','pending','authorized','paused')
                               ORDER BY id DESC LIMIT 1");
        $stmt->execute([$userId, $ofertaId]);
        $existente = $stmt->fetch();
        if ($existente && $existente['status'] === 'creating') {
            responder(409, ['erro' => 'em_andamento', 'mensagem' => 'Sua assinatura já está sendo criada. Aguarde alguns segundos e tente novamente.']);
        }
        if ($existente) {
            responder(200, [
                'id'         => $existente['preapproval_id'],
                'status'     => $existente['status'],
                'init_point' => $existente['status'] === 'pending' ? $existente['init_point'] : null,
            ]);
        }

        $externalReference = 'bikeos-' . $userId . '-' . bin2hex(random_bytes(8));
        $pdo->prepare('INSERT INTO assinaturas_mp (usuario_id, oferta, external_reference, payer_email, status) VALUES (?, ?, ?, ?, ?)')
            ->execute([$userId, $ofertaId, $externalReference, $usuario['email'], 'creating']);
        $registroId = (int) $pdo->lastInsertId();

        $resp = createPendingSubscription($trustedOffer, $usuario['email'], $externalReference);
        $mp = $resp['body'] ?? [];
        if ($resp['http'] >= 300 || empty($mp['id']) || empty($mp['init_point'])) {
            $pdo->prepare("UPDATE assinaturas_mp SET status = 'error' WHERE id = ?")->execute([$registroId]);
            error_log('MP preapproval falhou: HTTP ' . $resp['http'] . ' ref ' . $externalReference);
            responder(502, ['erro' => 'mp_falhou', 'mensagem' => 'Não foi possível iniciar a assinatura agora. Tente novamente em alguns minutos.']);
        }

        $pdo->prepare('UPDATE assinaturas_mp SET preapproval_id = ?, status = ?, init_point = ? WHERE id = ?')
            ->execute([$mp['id'], in_array($mp['status'] ?? '', SUBSCRIPTION_STATUSES, true) ? $mp['status'] : 'pending', $mp['init_point'], $registroId]);

        responder(201, ['id' => $mp['id'], 'status' => $mp['status'] ?? 'pending', 'init_point' => $mp['init_point']]);
    }

    // ---------- Consultar assinatura (GET /api/subscriptions/:id) ----------
    if ($acao === 'consultar' && $metodo === 'GET') {
        $registro = buscarAssinaturaDoUsuario($pdo, $userId, (string) ($_GET['id'] ?? ''));
        if (!$registro) {
            responder(404, ['erro' => 'nao_encontrada', 'mensagem' => 'Assinatura não encontrada.']);
        }
        $resp = getSubscription($registro['preapproval_id']);
        if ($resp['http'] >= 300) {
            responder(502, ['erro' => 'mp_falhou', 'mensagem' => 'Não foi possível consultar a assinatura. Tente novamente.']);
        }
        $status = (string) ($resp['body']['status'] ?? '');
        salvarStatus($pdo, (int) $registro['id'], $status);
        responder(200, ['id' => $registro['preapproval_id'], 'status' => $status]);
    }

    // ---------- Pausar / reativar / cancelar (somente admin) ----------
    if ($acao === 'ciclo' && $metodo === 'POST') {
        if ($usuario['nivel'] !== 'admin') {
            responder(403, ['erro' => 'sem_permissao', 'mensagem' => 'Somente administradores podem alterar a assinatura.']);
        }
        $entrada = json_decode(file_get_contents('php://input'), true) ?: [];
        $operacao = (string) ($entrada['operacao'] ?? '');
        if (!isset(SUBSCRIPTION_LIFECYCLE[$operacao])) {
            responder(400, ['erro' => 'operacao_invalida', 'mensagem' => 'Operação não permitida.']);
        }
        $registro = buscarAssinaturaDoUsuario($pdo, $userId, (string) ($_GET['id'] ?? ''));
        if (!$registro) {
            responder(404, ['erro' => 'nao_encontrada', 'mensagem' => 'Assinatura não encontrada.']);
        }
        $resp = updateSubscriptionLifecycle($registro['preapproval_id'], $operacao);
        if ($resp['http'] >= 300) {
            responder(502, ['erro' => 'mp_falhou', 'mensagem' => 'Não foi possível alterar a assinatura. Tente novamente.']);
        }
        $status = SUBSCRIPTION_LIFECYCLE[$operacao];
        salvarStatus($pdo, (int) $registro['id'], $status);
        responder(200, ['id' => $registro['preapproval_id'], 'status' => $status]);
    }

    responder(404, ['erro' => 'rota_invalida', 'mensagem' => 'Ação não encontrada.']);
} catch (Throwable $e) {
    error_log('Erro assinatura MP: ' . $e->getMessage());
    responder(500, ['erro' => 'interno', 'mensagem' => 'Erro inesperado. Tente novamente em alguns minutos.']);
}
