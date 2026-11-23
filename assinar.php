<?php
// ARQUIVO: assinar.php - Página de assinatura do Plano Profissional (Mercado Pago)
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mp_assinatura.php';

if (empty($_SESSION['assinatura_csrf'])) {
    $_SESSION['assinatura_csrf'] = bin2hex(random_bytes(32));
}

$ofertaId = 'profissional';
$oferta   = resolveOffer($ofertaId);
$valor    = number_format($oferta['amount'], 2, ',', '.');

// Assinatura mais recente do usuário (para mostrar o status ao voltar do Mercado Pago)
$stmt = $pdo->prepare('SELECT preapproval_id, status FROM assinaturas_mp WHERE usuario_id = ? AND preapproval_id IS NOT NULL ORDER BY id DESC LIMIT 1');
$stmt->execute([(int) $_SESSION['user_id']]);
$ultima = $stmt->fetch() ?: null;
$retorno = isset($_GET['retorno']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assinar Plano Profissional - BikeOS</title>
    <style>
        :root { --primary: #fbcd00; --bg: #0a0a0a; --card: #1a1a1a; --text: #fff; --muted: #a0a0a0; --ok: #28a745; --err: #dc3545; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: grid; place-items: center; padding: 24px 16px; }
        main { width: 100%; max-width: 480px; background: var(--card); border: 2px solid var(--primary); border-radius: 24px; padding: 2rem 1.5rem; text-align: center; }
        h1 { font-size: 1.5rem; margin-bottom: .25rem; }
        .total { margin: 1.25rem 0; color: var(--muted); }
        .total strong { display: block; font-size: 2.6rem; color: var(--primary); }
        ul { text-align: left; margin: 0 0 1.5rem 1.2rem; color: #ddd; line-height: 1.8; }
        button { width: 100%; padding: 1rem; border: 0; border-radius: 50px; background: var(--primary); color: #0a0a0a; font-weight: 800; font-size: 1rem; cursor: pointer; }
        button:disabled { opacity: .6; cursor: wait; }
        .estado { margin-top: 1rem; padding: .9rem 1rem; border-radius: 12px; display: none; text-align: left; }
        .estado.on { display: block; }
        .estado.info { background: rgba(251,205,0,.12); }
        .estado.ok { background: rgba(40,167,69,.18); }
        .estado.erro { background: rgba(220,53,69,.18); }
        a { color: var(--primary); }
        .voltar { display: inline-block; margin-top: 1.25rem; }
    </style>
</head>
<body>
<main data-mp-subscriptions-page="without-plan-pending">
    <h1>Plano Profissional</h1>
    <p>Assinatura mensal do BikeOS</p>

    <p class="total">Total: <strong>R$ <?= $valor ?></strong> por mês</p>

    <ul>
        <li>Cobrança automática todo mês no cartão</li>
        <li>Pagamento seguro na página do Mercado Pago</li>
        <li>Sem fidelidade: cancele quando quiser</li>
    </ul>

    <button type="button" id="btn-assinar" data-mp-subscription-cta="without-plan-pending">
        Assinar por R$ <?= $valor ?>/mês
    </button>

    <div id="estado" class="estado" role="status" aria-live="polite"></div>

    <a class="voltar" href="dashboard.php">Voltar ao sistema</a>
</main>

<script>
(function () {
    var csrf = <?= json_encode($_SESSION['assinatura_csrf']) ?>;
    var oferta = <?= json_encode($ofertaId) ?>;
    var ultima = <?= json_encode($ultima) ?>;
    var retorno = <?= $retorno ? 'true' : 'false' ?>;
    var botao = document.getElementById('btn-assinar');
    var estado = document.getElementById('estado');
    var inFlight = false;

    var mensagens = {
        pending: ['info', 'Assinatura aguardando o cadastro do cartão no Mercado Pago. Clique em "Assinar" para concluir.'],
        authorized: ['ok', 'Assinatura ativa! Pagamento autorizado com sucesso. Obrigado por assinar o BikeOS.'],
        paused: ['info', 'Sua assinatura está pausada. Fale com o suporte para reativar.'],
        canceled: ['erro', 'Sua assinatura foi cancelada. Você pode assinar novamente quando quiser.']
    };

    function mostrar(tipo, texto) {
        estado.className = 'estado on ' + tipo;
        estado.textContent = texto;
    }

    function mostrarStatus(status) {
        var m = mensagens[status];
        if (m) mostrar(m[0], m[1]);
        // Assinatura ativa ou pausada: não deixa criar outra
        botao.hidden = (status === 'authorized' || status === 'paused');
    }

    function consultar(id) {
        mostrar('info', 'Carregando o status da sua assinatura...');
        fetch('api_assinatura.php?acao=consultar&id=' + encodeURIComponent(id), { cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (d) { d.status ? mostrarStatus(d.status) : mostrar('erro', d.mensagem || 'Erro ao consultar. Tente novamente.'); })
            .catch(function () { mostrar('erro', 'Erro de conexão ao consultar a assinatura. Tente novamente.'); });
    }

    botao.addEventListener('click', function () {
        if (inFlight) return;
        inFlight = true;
        botao.disabled = true;
        botao.textContent = 'Processando...';
        mostrar('info', 'Processando: preparando sua assinatura no Mercado Pago...');

        fetch('api_assinatura.php?acao=criar', {
            method: 'POST',
            cache: 'no-store',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify({ oferta: oferta })
        })
        .then(function (r) { return r.json(); })
        .then(function (d) {
            if (d.init_point) {
                mostrar('info', 'Redirecionando para o Mercado Pago...');
                window.location.href = d.init_point;
                return;
            }
            if (d.status) { mostrarStatus(d.status); } else { mostrar('erro', d.mensagem || 'Não foi possível iniciar a assinatura. Tente novamente.'); }
            inFlight = false;
            botao.disabled = false;
            botao.textContent = 'Assinar por R$ <?= $valor ?>/mês';
        })
        .catch(function () {
            mostrar('erro', 'Erro de conexão. Verifique sua internet e tente novamente.');
            inFlight = false;
            botao.disabled = false;
            botao.textContent = 'Assinar por R$ <?= $valor ?>/mês';
        });
    });

    if (ultima && ultima.preapproval_id) {
        if (retorno) { consultar(ultima.preapproval_id); } else { mostrarStatus(ultima.status); }
    }
})();
</script>
</body>
</html>
