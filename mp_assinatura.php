<?php
// ARQUIVO: mp_assinatura.php - Configuração e chamadas da Assinatura Mercado Pago
//
// Usa a API de Assinaturas (preapproval) no modelo "sem plano, pendente":
// o BikeOS cria a assinatura e o cliente cadastra o cartão na página do Mercado Pago.
//
// Variáveis no .env do servidor (NUNCA no código nem no GitHub):
//   MP_ACCESS_TOKEN=...   Access Token (Credenciais de produção do app BikeOS)
//   APP_URL=https://bikeos.com.br

require_once __DIR__ . '/helpers/env_loader.php';
EnvLoader::load();

// Catálogo de ofertas confiável (definido só no servidor).
// O navegador envia apenas o ID da oferta; valor, moeda e frequência vêm daqui.
const SUBSCRIPTION_OFFERS = [
    'profissional' => [
        'reason'         => 'BikeOS - Plano Profissional',
        'amount'         => 69.90,
        'currency'       => 'BRL',
        'frequency'      => 1,
        'frequency_type' => 'months',
    ],
];

// Operações permitidas para pausar/reativar/cancelar → status enviado ao Mercado Pago
const SUBSCRIPTION_LIFECYCLE = [
    'pause'      => 'paused',
    'reactivate' => 'authorized',
    'cancel'     => 'canceled',
];

const SUBSCRIPTION_STATUSES = ['pending', 'authorized', 'paused', 'canceled'];

function resolveOffer(string $offerId): ?array
{
    return SUBSCRIPTION_OFFERS[$offerId] ?? null;
}

function subscriptionBackUrl(): string
{
    $appUrl = rtrim((string) EnvLoader::get('APP_URL', 'https://bikeos.com.br'), '/');
    return $appUrl . '/assinar.php?retorno=1';
}

/**
 * Chamada à API do Mercado Pago (servidor). Retorna ['http' => int, 'body' => array|null].
 * O Access Token nunca é enviado ao navegador nem registrado em log.
 */
function mpFetch(string $method, string $url, ?array $body = null): array
{
    $token = (string) EnvLoader::get('MP_ACCESS_TOKEN', '');
    if ($token === '') {
        throw new RuntimeException('MP_ACCESS_TOKEN não configurado');
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $raw  = curl_exec($ch);
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($raw === false) {
        throw new RuntimeException('Falha de conexão com o Mercado Pago: ' . $err);
    }
    return ['http' => $http, 'body' => json_decode($raw, true)];
}

/** Cria a assinatura pendente e devolve a resposta do Mercado Pago (id, status, init_point). */
function createPendingSubscription(array $trustedOffer, string $payerEmail, string $externalReference): array
{
    $body = [
        'reason'             => $trustedOffer['reason'],
        'external_reference' => $externalReference,
        'payer_email'        => $payerEmail,
        'auto_recurring'     => [
            'frequency'          => $trustedOffer['frequency'],
            'frequency_type'     => $trustedOffer['frequency_type'],
            'transaction_amount' => $trustedOffer['amount'],
            'currency_id'        => $trustedOffer['currency'],
        ],
        'back_url'           => subscriptionBackUrl(),
        'status'             => 'pending',
    ];
    // Único ponto do sistema que cria assinaturas
    return mpFetch('POST', 'https://api.mercadopago.com/preapproval', $body);
}

/** Consulta uma assinatura: GET /preapproval/{id} */
function getSubscription(string $preapprovalId): array
{
    $id = rawurlencode($preapprovalId);
    return mpFetch('GET', "https://api.mercadopago.com/preapproval/{$id}");
}

/** Pausa, reativa ou cancela: PUT /preapproval/{id} com status da lista permitida. */
function updateSubscriptionLifecycle(string $preapprovalId, string $operation): array
{
    if (!isset(SUBSCRIPTION_LIFECYCLE[$operation])) {
        throw new InvalidArgumentException('Operação não permitida');
    }
    $id = rawurlencode($preapprovalId);
    return mpFetch('PUT', "https://api.mercadopago.com/preapproval/{$id}", [
        'status' => SUBSCRIPTION_LIFECYCLE[$operation],
    ]);
}
