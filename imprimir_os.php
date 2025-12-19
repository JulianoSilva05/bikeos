<?php
// ARQUIVO: imprimir_os.php (VERSÃO FINAL COMPLETA E CORRIGIDA)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'api/db_connection.php';

$os_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($os_id <= 0) {
    die("ID da Ordem de Serviço inválido.");
}

// Busca configurações da loja
$stmt_config = $pdo->prepare("SELECT * FROM configuracoes_loja WHERE empresa_id = 1 ORDER BY id DESC LIMIT 1");
$stmt_config->execute();
$config_loja = $stmt_config->fetch(PDO::FETCH_ASSOC);

// Define valores padrão caso não haja configurações
if (!$config_loja) {
    $config_loja = [
        'nome_loja' => 'BikeOS',
        'endereco' => 'R. Américo Deodoro Brighenti, 322\nJardim Aeroporto, São João del Rei/MG',
        'telefone' => '(32) 99962-8568',
    
    ];
}

// Busca os dados principais da OS, cliente e bicicleta, incluindo as avarias
$stmt = $pdo->prepare("
    SELECT 
        os.*, 
        c.nome as cliente_nome, 
        c.cpf as cliente_cpf,
        c.telefone as cliente_telefone,
        c.cep as cliente_cep, c.logradouro as cliente_logradouro, c.numero as cliente_numero,
        c.complemento as cliente_complemento, c.bairro as cliente_bairro, 
        c.cidade as cliente_cidade, c.estado as cliente_estado,
        b.marca as bicicleta_marca,
        b.modelo as bicicleta_modelo,
        b.cor as bicicleta_cor,
        b.avarias as bicicleta_avarias
    FROM ordens_servico os
    JOIN clientes c ON os.cliente_id = c.id
    JOIN bicicletas b ON os.bicicleta_id = b.id
    WHERE os.id = ?
");
$stmt->execute([$os_id]);
$os = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$os) {
    die("Ordem de Serviço não encontrada.");
}

// Verifica se a OS não tem um código de retirada e, se não tiver, gera um novo e salva no banco.
if (empty($os['codigo_retirada'])) {
    $novo_codigo = rand(1000, 9999);
    $stmt_update = $pdo->prepare("UPDATE ordens_servico SET codigo_retirada = ? WHERE id = ?");
    $stmt_update->execute([$novo_codigo, $os_id]);
    $os['codigo_retirada'] = $novo_codigo; // Atualiza a variável local para usar na página
}

// Decodifica o JSON de avarias
$avarias = !empty($os['bicicleta_avarias']) ? json_decode($os['bicicleta_avarias'], true) : [];

// Busca as peças/produtos usados na OS
$stmt_produtos = $pdo->prepare("SELECT op.quantidade, op.preco_unitario_cobrado, p.nome as produto_nome FROM os_produtos op JOIN produtos p ON op.produto_id = p.id WHERE op.os_id = ?");
$stmt_produtos->execute([$os_id]);
$produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

// Busca os serviços realizados na OS
$stmt_servicos = $pdo->prepare("SELECT os_s.preco_cobrado, s.nome as servico_nome FROM os_servicos os_s JOIN servicos s ON os_s.servico_id = s.id WHERE os_s.os_id = ?");
$stmt_servicos->execute([$os_id]);
$servicos = $stmt_servicos->fetchAll(PDO::FETCH_ASSOC);

// Gera o link de acompanhamento
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$link_acompanhamento = "{$protocol}://{$host}{$uri}/acompanhar_os.php?id={$os['id']}&codigo={$os['codigo_retirada']}";

// Coloque aqui o link para o seu formulário de avaliação
$link_avaliacao = "https://SEU_LINK_DE_AVALIACAO_AQUI";

// Monta o endereço completo do cliente
$endereco_completo = '';
if ($os['cliente_logradouro']) {
    $endereco_completo = $os['cliente_logradouro'];
    if ($os['cliente_numero']) $endereco_completo .= ', ' . $os['cliente_numero'];
    if ($os['cliente_complemento']) $endereco_completo .= ', ' . $os['cliente_complemento'];
    if ($os['cliente_bairro']) $endereco_completo .= ', ' . $os['cliente_bairro'];
    if ($os['cliente_cidade']) $endereco_completo .= ', ' . $os['cliente_cidade'];
    if ($os['cliente_estado']) $endereco_completo .= ' - ' . $os['cliente_estado'];
    if ($os['cliente_cep']) $endereco_completo .= ', CEP: ' . $os['cliente_cep'];
}

// Lógica para formatar o método de pagamento
$metodos_pagamento = [
    'DINHEIRO' => 'Dinheiro',
    'PIX' => 'PIX',
    'CREDITO' => 'Cartão de Crédito',
    'DEBITO' => 'Cartão de Débito',
    'CREDIARIO' => 'Crediário'
];
$metodo_pagamento_formatado = 'Não informado';
if (!empty($os['metodo_pagamento'])) {
    $metodo_pagamento_formatado = $metodos_pagamento[$os['metodo_pagamento']] ?? ucfirst(strtolower(str_replace('_', ' ', $os['metodo_pagamento'])));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>OS #<?php echo $os['id']; ?> | <?php require_once 'helpers/empresa_helper.php'; require_once 'api/db_connection.php'; echo getNomeEmpresa($pdo); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Consolas', 'Lucida Console', 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.4;
        }

        body {
            width: 280px;
            margin: 0 auto;
            padding: 10px;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .header h2 {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            font-size: 12px;
            margin: 2px 0;
        }

        .section {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }

        .section h2 {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .info-item {
            margin-bottom: 5px;
        }

        ul {
            list-style-position: inside;
            padding-left: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }

        td {
            text-align: left;
            padding: 2px 0;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .total-geral {
            font-size: 15px;
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .clausula,
        .aviso {
            margin-top: 15px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            text-align: justify;
            font-size: 10px;
        }

        .aviso {
            font-weight: bold;
        }

        .assinatura {
            margin-top: 40px;
            text-align: center;
        }

        .qr-code {
            text-align: center;
            margin-top: 15px;
        }

        .qr-code img {
            width: 100px;
            height: 100px;
        }

        .data-entrega-destaque {
            font-size: 22px !important;
            font-weight: bold !important;
            text-align: center;
            border: 2px solid #000;
            padding: 5px;
            margin-top: 10px;
        }

        .via-loja {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #000;
            /* ALTERAÇÃO 2: Força a impressão em uma nova página */
            page-break-before: always;
        }

        .via-loja .info-item {
            font-size: 14px;
        }

        @media print {
            @page {
                margin: 0;
            }

            body {
                margin: 0.5cm;
            }
        }
    </style>
</head>

<body>
    <?php if ($os['status'] === 'FINALIZADA'): ?>
        <div class="container">
            <div class="header">
                <h1><?php echo htmlspecialchars($config_loja['nome_loja']); ?></h1>
                <?php 
                $endereco_linhas = explode('\n', $config_loja['endereco']);
                foreach ($endereco_linhas as $linha): 
                ?>
                    <p><?php echo htmlspecialchars($linha); ?></p>
                <?php endforeach; ?>
                <p>Telefone: <?php echo htmlspecialchars($config_loja['telefone']); ?></p>
                <?php if (!empty($config_loja['cnpj'])): ?>
                    <p>CNPJ: <?php echo htmlspecialchars($config_loja['cnpj']); ?></p>
                <?php endif; ?>
      
                <hr style="border: 1px dashed #000; margin: 10px 0;">
                <h2>Recibo de Conclusão de Serviço</h2>
                <p><strong>OS N°: <?php echo $os['id']; ?></strong> | <strong>Data:</strong> <?php echo !empty($os['data_finalizacao']) ? date("d/m/Y H:i", strtotime($os['data_finalizacao'])) : 'Não informada'; ?></p>
            </div>
            <div class="section">
                <h2>Dados do Cliente</h2>
                <div class="info-item"><strong>Nome:</strong> <?php echo htmlspecialchars($os['cliente_nome']); ?></div>
                <div class="info-item"><strong>Telefone:</strong> <?php echo htmlspecialchars($os['cliente_telefone']); ?></div>
                <div class="info-item"><strong>Endereço:</strong> <?php echo htmlspecialchars($endereco_completo ?: 'Não informado'); ?></div>
            </div>
            <div class="section">
                <h2>Dados da Bicicleta</h2>
                <div class="info-item"><strong>Bicicleta:</strong> <?php echo htmlspecialchars($os['bicicleta_marca'] . ' ' . $os['bicicleta_modelo'] . ' - ' . $os['bicicleta_cor']); ?></div>
            </div>
            <?php if (!empty($avarias)): ?>
                <div class="section">
                    <h2>Avarias Registradas na Entrada</h2>
                    <ul>
                        <?php foreach ($avarias as $avaria): if (!empty($avaria['texto'])): ?>
                                <li>- <?php echo htmlspecialchars($avaria['texto']); ?></li>
                        <?php endif;
                        endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="section">
                <h2>Serviços e Produtos</h2>
                <table>
                    <tbody>
                        <?php foreach ($servicos as $servico): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($servico['servico_nome']); ?></td>
                                <td class="text-right">R$ <?php echo number_format($servico['preco_cobrado'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?php echo $produto['quantidade']; ?>x <?php echo htmlspecialchars($produto['produto_nome']); ?></td>
                                <td class="text-right">R$ <?php echo number_format($produto['quantidade'] * $produto['preco_unitario_cobrado'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total-geral">
                    <span style="float: left;">VALOR TOTAL PAGO:</span>
                    <span style="float: right;">R$ <?php echo number_format($os['valor_total'], 2, ',', '.'); ?></span>
                    <div style="clear: both;"></div>
                </div>
                <div class="info-item text-right" style="margin-top: 5px;">
                    <strong>Pagamento:</strong> <?php echo $metodo_pagamento_formatado; ?>
                </div>
            </div>

            <div class="section footer">
                <p><strong>SUA OPINIÃO É IMPORTANTE!</strong></p>
                <p>Aponte a câmera do seu celular para o QR Code abaixo para avaliar nosso serviço.</p>
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($link_avaliacao); ?>" alt="QR Code para Avaliação">
                </div>
            </div>

            <div class="clausula">
                <strong>AVISO DE RETIRADA:</strong> A bicicleta deverá ser retirada em até 90 (noventa) dias. Após este prazo, a oficina reserva-se o direito de vender o bem para cobrir despesas, conforme o Art. 644 do Código Civil.
            </div>
            <div class="footer">
                <p>Obrigado pela sua confiança!</p>
            </div>
        </div>

    <?php else: ?>
        <div class="container">
            <div class="header">
                <h1><?php echo htmlspecialchars($config_loja['nome_loja']); ?></h1>
                <?php 
                $endereco_linhas = explode('\n', $config_loja['endereco']);
                foreach ($endereco_linhas as $linha): 
                ?>
                    <p><?php echo htmlspecialchars($linha); ?></p>
                <?php endforeach; ?>
                <p>Telefone: <?php echo htmlspecialchars($config_loja['telefone']); ?></p>
                <?php if (!empty($config_loja['cnpj'])): ?>
                    <p>CNPJ: <?php echo htmlspecialchars($config_loja['cnpj']); ?></p>
                <?php endif; ?>
      
                <hr style="border: 1px dashed #000; margin: 10px 0;">
                <h2>Ordem de Serviço N° <?php echo $os['id']; ?></h2>
            </div>
            <div class="section">
                <div class="info-item"><strong>Data Entrada:</strong> <?php echo date("d/m/Y H:i", strtotime($os['data_entrada'])); ?></div>
                <div class="info-item"><strong>Previsão Entrega:</strong> <?php echo $os['previsao_entrega'] ? date("d/m/Y", strtotime($os['previsao_entrega'])) : 'N/D'; ?></div>
                <div class="info-item"><strong>Status:</strong> <?php echo str_replace('_', ' ', $os['status']); ?></div>
            </div>
            <div class="section">
                <h2>Cliente e Bicicleta</h2>
                <div class="info-item"><strong>Cliente:</strong> <?php echo htmlspecialchars($os['cliente_nome']); ?></div>
                <div class="info-item"><strong>Telefone:</strong> <?php echo htmlspecialchars($os['cliente_telefone']); ?></div>
                <div class="info-item"><strong>Bicicleta:</strong> <?php echo htmlspecialchars($os['bicicleta_marca'] . ' ' . $os['bicicleta_modelo']); ?></div>
            </div>
            <div class="section">
                <h2>Problema Relatado</h2>
                <p><?php echo nl2br(htmlspecialchars($os['problema_relatado'])); ?></p>
            </div>
            <?php if (!empty($avarias)): ?>
                <div class="section">
                    <h2>Avarias Registradas</h2>
                    <ul>
                        <?php foreach ($avarias as $avaria): if (!empty($avaria['texto'])): ?>
                                <li>- <?php echo htmlspecialchars($avaria['texto']); ?></li>
                        <?php endif;
                        endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="section">
                <h2>Itens e Serviços Iniciais</h2>
                <?php if (empty($servicos) && empty($produtos)): ?>
                    <p style="text-align:center;">Nenhum item adicionado até o momento.</p>
                <?php else: ?>
                    <table>
                        <tbody>
                            <?php foreach ($servicos as $servico): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($servico['servico_nome']); ?></td>
                                    <td class="text-right">R$ <?php echo number_format($servico['preco_cobrado'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td><?php echo $produto['quantidade']; ?>x <?php echo htmlspecialchars($produto['produto_nome']); ?></td>
                                    <td class="text-right">R$ <?php echo number_format($produto['quantidade'] * $produto['preco_unitario_cobrado'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <div class="total-geral">
                    <span style="float:left;">TOTAL ATÉ O MOMENTO:</span>
                    <span style="float:right;">R$ <?php echo number_format($os['valor_total'], 2, ',', '.'); ?></span>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <div class="aviso">
                <p><strong>ATENÇÃO:</strong> Os valores e itens descritos acima são preliminares e podem ser alterados. Qualquer alteração no orçamento será comunicada via WhatsApp. Acompanhe todas as atualizações em tempo real pelo QR Code abaixo.</p>
            </div>

            <div class="clausula"><strong>IMPORTANTE:</strong> A bicicleta deverá ser retirada em até 90 (noventa) dias após a notificação de que o serviço foi concluído. Após este prazo, a oficina reserva-se o direito de vender o bem para cobrir as despesas do serviço, conforme o Art. 644 do Código Civil.</div>
            <div class="section footer">
                <p>Aponte a câmera do seu celular para o QR Code abaixo ou acesse o link:</p>
                <div class="qr-code"><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($link_acompanhamento); ?>" alt="QR Code para Acompanhamento"></div>
                <p style="font-size: 10px; word-wrap: break-word;"><?php echo htmlspecialchars($link_acompanhamento); ?></p>
                <p style="margin-top: 10px;">Seu código de retirada é: <strong><?php echo $os['codigo_retirada']; ?></strong></p>
            </div>
        </div>

        <div class="via-loja">
            <div class="header">
                <h2>VIA DA OFICINA - OS #<?php echo $os['id']; ?></h2>
            </div>
            <div class="section">
                <div class="info-item"><strong>Cliente:</strong> <?php echo htmlspecialchars($os['cliente_nome']); ?></div>
                <div class="info-item"><strong>Telefone:</strong> <?php echo htmlspecialchars($os['cliente_telefone']); ?></div>
                <div class="info-item"><strong>Endereço:</strong> <?php echo htmlspecialchars($endereco_completo ?: 'Não informado'); ?></div>
            </div>
            <div class="section">
                <div class="info-item"><strong>Bicicleta:</strong> <?php echo htmlspecialchars($os['bicicleta_marca'] . ' ' . $os['bicicleta_modelo'] . ' - ' . $os['bicicleta_cor']); ?></div>
            </div>

            <div class="section">
                <h2>Problema Relatado</h2>
                <p><?php echo nl2br(htmlspecialchars($os['problema_relatado'])); ?></p>
            </div>

            <?php if (!empty($avarias)): ?>
                <div class="section">
                    <h2>Avarias</h2>
                    <ul>
                        <?php foreach ($avarias as $avaria): if (!empty($avaria['texto'])): ?>
                                <li>- <?php echo htmlspecialchars($avaria['texto']); ?></li>
                        <?php endif;
                        endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="section">
                <h2>Serviços e Valores Iniciais</h2>
                <?php if (empty($servicos) && empty($produtos)): ?><p style="text-align:center;">Nenhum item adicionado.</p><?php else: ?><ul><?php foreach ($servicos as $servico): ?><li><?php echo htmlspecialchars($servico['servico_nome']); ?>: R$ <?php echo number_format($servico['preco_cobrado'], 2, ',', '.'); ?></li><?php endforeach; ?><?php foreach ($produtos as $produto): ?><li><?php echo $produto['quantidade']; ?>x <?php echo htmlspecialchars($produto['produto_nome']); ?>: R$ <?php echo number_format($produto['quantidade'] * $produto['preco_unitario_cobrado'], 2, ',', '.'); ?></li><?php endforeach; ?></ul><?php endif; ?>
                <div class="total-geral"><span style="float:left;">TOTAL:</span><span style="float:right;">R$ <?php echo number_format($os['valor_total'], 2, ',', '.'); ?></span>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <div class="section">
                <h2 style="margin-bottom: 5px;">DATA DE ENTREGA</h2>
                <div class="data-entrega-destaque"><?php echo $os['previsao_entrega'] ? date("d / m / Y", strtotime($os['previsao_entrega'])) : 'A DEFINIR'; ?></div>
            </div>

            <div class="assinatura">
                <p>___________________________________</p>
                <p><?php echo htmlspecialchars($os['cliente_nome']); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <script>
        window.onload = function() {
            // Validação das configurações da loja
            const configLoja = <?php echo json_encode($config_loja); ?>;
            
            // Verifica se alguma configuração importante está vazia ou não configurada
            const configsVazias = [];
            if (!configLoja.nome_loja || configLoja.nome_loja === 'BikeOS') {
                configsVazias.push('Nome da Loja');
            }
            if (!configLoja.endereco || configLoja.endereco.includes('R. Américo Deodoro Brighenti')) {
                configsVazias.push('Endereço');
            }
            if (!configLoja.telefone || configLoja.telefone === '(32) 99962-8568') {
                configsVazias.push('Telefone');
            }

            
            if (configsVazias.length > 0) {
                const confirmar = confirm(
                    'Atenção! As seguintes configurações da loja não estão preenchidas:\n\n' +
                    configsVazias.join(', ') +
                    '\n\nRecomendamos configurar esses dados em Funcionalidades antes de imprimir.\n\n' +
                    'Deseja ir para a página de Funcionalidades agora?'
                );
                
                if (confirmar) {
                    window.location.href = 'funcionalidades.php';
                } else {
                    window.print();
                }
            } else {
                window.print();
            }
        };
    </script>
</body>

</html>