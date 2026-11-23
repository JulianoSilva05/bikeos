<?php
// ARQUIVO: promocao.php - Promoção Black Friday do Plano Profissional
//
// O anúncio aparece sozinho no site (index.php) entre PROMO_INICIO e PROMO_FIM
// (horário de Brasília) e some sozinho depois. Fora desse período nada é exibido.
//
// Para conferir o visual antes da data, rode o site no PC e abra:
//   http://localhost:8000/?preview_promo=1   (só funciona em localhost)

const PROMO_INICIO  = '2026-11-26 00:00:00';
const PROMO_FIM     = '2026-11-27 23:59:59';
const PROMO_FUSO    = 'America/Sao_Paulo';
const PROMO_PRECO   = '34,00';
const PROMO_PRECO_HTML = 'R$&nbsp;34,00';
const PLANO_PRECO_HTML = 'R$&nbsp;69,90';
const PLANO_PRECO   = '69,90';

// Link de pagamento do Mercado Pago (R$ 34,00 do primeiro mês).
// Enquanto estiver vazio, o botão leva para o formulário de cadastro.
const PROMO_LINK_MERCADO_PAGO = '';

function promocao_ativa(): bool
{
    $fuso   = new DateTimeZone(PROMO_FUSO);
    $agora  = new DateTimeImmutable('now', $fuso);
    $inicio = new DateTimeImmutable(PROMO_INICIO, $fuso);
    $fim    = new DateTimeImmutable(PROMO_FIM, $fuso);

    if ($agora >= $inicio && $agora <= $fim) {
        return true;
    }

    // Pré-visualização: só no PC de desenvolvimento
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $local = preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/', $host) === 1;
    return $local && isset($_GET['preview_promo']);
}

// Momento do fim em milissegundos, para o contador e para esconder o anúncio
// se a página ficar aberta depois do horário de encerramento.
function promocao_fim_ms(): int
{
    $fim = new DateTimeImmutable(PROMO_FIM, new DateTimeZone(PROMO_FUSO));
    return ($fim->getTimestamp() + 1) * 1000;
}

function promocao_link(): string
{
    return PROMO_LINK_MERCADO_PAGO !== '' ? PROMO_LINK_MERCADO_PAGO : '#cadastro';
}

function promocao_render_nav(): void
{
    if (!promocao_ativa()) {
        return;
    }
    echo '<li role="none" class="promo-auto"><a href="#promocao" role="menuitem" class="promo-nav-link">🔥 Promoção</a></li>';
}

function promocao_render_secao(): void
{
    if (!promocao_ativa()) {
        return;
    }
    $link     = htmlspecialchars(promocao_link(), ENT_QUOTES, 'UTF-8');
    $externo  = PROMO_LINK_MERCADO_PAGO !== '' ? ' target="_blank" rel="noopener noreferrer"' : '';
    $fimMs    = promocao_fim_ms();
    ?>
    <style>
        .promo { padding: 5rem 0; background: var(--dark-bg); }
        .promo-card {
            max-width: 760px; margin: 0 auto; padding: 2.5rem 2rem;
            border-radius: 30px; text-align: center; color: #fff;
            background: linear-gradient(135deg, rgba(111,66,193,0.35) 0%, rgba(10,10,10,0.9) 60%);
            border: 2px solid var(--primary-color);
            box-shadow: 0 20px 60px rgba(251, 205, 0, 0.15);
        }
        .promo-tag {
            display: inline-block; padding: 0.35rem 1rem; border-radius: 50px;
            background: var(--primary-color); color: #0a0a0a;
            font-weight: 800; letter-spacing: 1px; text-transform: uppercase; font-size: 0.85rem;
        }
        .promo-card h2 { margin: 1rem 0 0.5rem; font-size: clamp(1.6rem, 4vw, 2.4rem); }
        .promo-preco { margin: 1.25rem 0 0.25rem; color: var(--text-secondary); }
        .promo-preco s { color: var(--text-secondary); }
        .promo-preco-novo {
            display: block; font-size: clamp(2.8rem, 9vw, 4rem); font-weight: 900;
            color: var(--primary-color); line-height: 1.1;
        }
        .promo-preco-novo small { font-size: 1.1rem; color: var(--text-secondary); font-weight: 500; }
        .promo-contador { margin: 1rem 0 1.5rem; font-weight: 600; font-variant-numeric: tabular-nums; }
        .promo-contador span { color: var(--primary-color); }
        .promo-card .btn { color: #0a0a0a; font-weight: 800; padding: 1rem 2.5rem; }
        .promo-regras {
            margin-top: 2rem; text-align: left; background: rgba(255,255,255,0.04);
            border-radius: 16px; padding: 1.25rem 1.5rem;
        }
        .promo-regras summary { cursor: pointer; font-weight: 700; }
        .promo-regras ul { margin: 0.75rem 0 0 1.1rem; color: #ddd; }
        .promo-regras li { padding: 0.25rem 0; }
        @media (max-width: 600px) { .promo-preco-novo small { display: block; } }
        .promo-nav-link { color: var(--primary-color) !important; font-weight: 700; }
    </style>

    <section id="promocao" class="promo promo-auto" aria-label="Promoção Black Friday">
        <div class="container">
            <article class="promo-card">
                <span class="promo-tag">Black Friday BikeOS</span>
                <h2>Plano Profissional com o 1º mês por <?= PROMO_PRECO_HTML ?></h2>
                <p>Somente <strong>26 e 27 de novembro de 2026</strong>.</p>

                <p class="promo-preco">
                    De <s><?= PLANO_PRECO_HTML ?></s> por
                    <span class="promo-preco-novo"><?= PROMO_PRECO_HTML ?> <small>no 1º mês</small></span>
                    Depois, R$ <?= PLANO_PRECO ?>/mês.
                </p>

                <p class="promo-contador" id="promo-contador" aria-live="off">
                    Termina em <span id="promo-tempo">--</span>
                </p>

                <a href="<?= $link ?>"<?= $externo ?> class="btn" aria-label="Contratar o Plano Profissional com desconto">
                    Quero o 1º mês por <?= PROMO_PRECO_HTML ?>
                </a>

                <details class="promo-regras">
                    <summary>Regras da promoção</summary>
                    <ul>
                        <li>Válida para contratações do Plano Profissional feitas entre <strong>00h00 de 26/11/2026</strong> e <strong>23h59 de 27/11/2026</strong> (horário de Brasília).</li>
                        <li>O primeiro mês custa <strong>R$ <?= PROMO_PRECO ?></strong>. A partir do segundo mês, a mensalidade é a do plano normal: <strong>R$ <?= PLANO_PRECO ?>/mês</strong>.</li>
                        <li>O pagamento do primeiro mês deve ser feito pelo link da promoção dentro do período acima.</li>
                        <li>Válida para novos clientes, uma vez por empresa.</li>
                        <li>Não acumulativa com outras promoções ou descontos, incluindo o primeiro mês grátis.</li>
                        <li>Sem fidelidade: o plano pode ser cancelado a qualquer momento.</li>
                    </ul>
                </details>
            </article>
        </div>
    </section>

    <script>
    (function () {
        var fim = <?= $fimMs ?>;
        var previa = <?= isset($_GET['preview_promo']) ? 'true' : 'false' ?>;
        var tempo = document.getElementById('promo-tempo');

        function atualizar() {
            var resta = fim - Date.now();
            if (resta <= 0 && !previa) {
                // Encerrou com a página aberta: remove o anúncio
                document.querySelectorAll('.promo-auto').forEach(function (el) { el.remove(); });
                return;
            }
            resta = Math.max(resta, 0);
            var d = Math.floor(resta / 86400000);
            var h = Math.floor((resta % 86400000) / 3600000);
            var m = Math.floor((resta % 3600000) / 60000);
            var s = Math.floor((resta % 60000) / 1000);
            tempo.textContent = (d > 0 ? d + 'd ' : '') + h + 'h ' + String(m).padStart(2, '0') + 'min ' + String(s).padStart(2, '0') + 's';
            setTimeout(atualizar, 1000);
        }
        atualizar();
    })();
    </script>
    <?php
}
