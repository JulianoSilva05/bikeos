<?php
require_once 'api/db_connection.php';
session_start();

$pagamento_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($pagamento_id <= 0) {
    die("ID do pagamento inválido.");
}

// Obter empresa_id da sessão
$empresa_id = $_SESSION['empresa_id'] ?? 1;

// Busca configurações da loja baseadas no empresa_id
$stmt_config = $pdo->prepare("SELECT * FROM configuracoes_loja WHERE empresa_id = ? LIMIT 1");
$stmt_config->execute([$empresa_id]);
$config_loja = $stmt_config->fetch(PDO::FETCH_ASSOC);

// Se não encontrar, busca qualquer configuração disponível
if (!$config_loja) {
    $stmt_config = $pdo->prepare("SELECT * FROM configuracoes_loja ORDER BY id DESC LIMIT 1");
    $stmt_config->execute();
    $config_loja = $stmt_config->fetch(PDO::FETCH_ASSOC);
}

// Define valores padrão caso não haja configurações
if (!$config_loja) {
    $config_loja = [
        'nome_loja' => 'BikeOS',
        'endereco' => 'R. Américo Deodoro Brighenti, 322\nJardim Aeroporto, São João del Rei/MG',
        'telefone' => '(32) 99962-8568',
    
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Recibo de Pagamento - BikeOS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Consolas', 'Lucida Console', 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
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
            font-size: 16px;
            font-weight: bold;
        }

        .section {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }

        h2 {
            font-size: 14px;
            text-align: center;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        td {
            text-align: left;
            padding: 2px 0;
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
    <div id="recibo-container">
        <p style="text-align: center;">Carregando recibo...</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const pagamentoId = <?php echo $pagamento_id; ?>;
            const container = document.getElementById('recibo-container');

            try {
                const response = await fetch(`api/api_financeiro.php?acao=get_dados_recibo_pagamento&id=${pagamentoId}`);
                const resultado = await response.json();
                if (!resultado.sucesso) throw new Error(resultado.mensagem);

                const dados = resultado.dados;
                const valorPagoHoje = parseFloat(dados.pagamento.valor_pago);
                const valorTotalVenda = parseFloat(dados.venda.valor_total);

                let valorPagoAnteriormente = 0;
                let pagamentosAnterioresHtml = '';
                if (dados.pagamentos_anteriores && dados.pagamentos_anteriores.length > 0) {
                    dados.pagamentos_anteriores.forEach(pag => {
                        const valor = parseFloat(pag.valor_pago);
                        valorPagoAnteriormente += valor;
                        const dataFormatada = new Date(pag.data_pagamento).toLocaleDateString('pt-BR');
                        pagamentosAnterioresHtml += `<tr><td>(-) Pgto. em ${dataFormatada}:</td><td class="text-right">R$ ${valor.toFixed(2).replace('.',',')}</td></tr>`;
                    });
                } else {
                    pagamentosAnterioresHtml = `<tr><td>(-) Valor Pago Anteriormente:</td><td class="text-right">R$ 0,00</td></tr>`;
                }

                const saldoDevedorAtual = valorTotalVenda - valorPagoAnteriormente - valorPagoHoje;

                let itensHtml = '';
                dados.itens_venda.forEach(item => {
                    itensHtml += `<tr><td>${item.quantidade}x ${item.produto_nome}</td><td class="text-right">R$ ${(item.quantidade * item.preco_unitario_cobrado).toFixed(2).replace('.',',')}</td></tr>`;
                });

                let outrasDividasHtml = '';
                let totalOutrasDividas = 0;
                if (dados.outras_dividas.length > 0) {
                    outrasDividasHtml = '<div class="section"><h2>Outros Débitos Pendentes</h2>';
                    dados.outras_dividas.forEach(divida => {
                        const restante = parseFloat(divida.valor_total) - parseFloat(divida.valor_pago);
                        if (restante > 0.01) { // Evita mostrar débitos residuais de centavos
                            outrasDividasHtml += `<p>Venda #${divida.id}: Restam R$ ${restante.toFixed(2).replace('.',',')}</p>`;
                            totalOutrasDividas += restante;
                        }
                    });
                    outrasDividasHtml += '</div>';
                }

                const configLoja = <?php echo json_encode($config_loja); ?>;
                const enderecoLinhas = configLoja.endereco.split('\n').map(linha => `<p>${linha}</p>`).join('');
                const pixInfo = '';
                
                container.innerHTML = `
                <div class="header"><h1>${configLoja.nome_loja}</h1>${enderecoLinhas}<p>Telefone: ${configLoja.telefone}</p>${configLoja.cnpj ? `<p>CNPJ: ${configLoja.cnpj}</p>` : ''}</div>
                <h2>Recibo de Pagamento</h2>
                <p><strong>Recebemos de:</strong> ${dados.venda.cliente_nome}</p>
                <p><strong>Valor Recebido:</strong> R$ ${valorPagoHoje.toFixed(2).replace('.',',')}</p>
                <p><strong>Data do Pagamento:</strong> ${new Date(dados.pagamento.data_pagamento).toLocaleString('pt-BR')}</p>
                
                <div class="section">
                    <h2>Referente à Venda #${dados.venda.id}</h2>
                    <table><tbody>${itensHtml}</tbody></table>
                    <table style="margin-top:10px;">
                        <tr><td>Valor Total da Nota:</td><td class="text-right">R$ ${valorTotalVenda.toFixed(2).replace('.',',')}</td></tr>
                        ${pagamentosAnterioresHtml}
                        <tr><td>(-) Pagamento de Hoje:</td><td class="text-right">R$ ${valorPagoHoje.toFixed(2).replace('.',',')}</td></tr>
                        <tr class="total-geral"><td>SALDO DEVEDOR (desta nota):</td><td class="text-right">R$ ${saldoDevedorAtual.toFixed(2).replace('.',',')}</td></tr>
                    </table>
                </div>

                ${outrasDividasHtml}
                
                <div class="section total-geral">
                    <span>SALDO DEVEDOR TOTAL:</span>
                    <span style="float: right;">R$ ${(saldoDevedorAtual + totalOutrasDividas).toFixed(2).replace('.',',')}</span>
                    <div style="clear: both;"></div>
                </div>

                <div class="footer" style="margin-top: 40px;">
                    <p>_________________________</p>
                    <p>Assinatura (Dengo Bike)</p>
                </div>
            `;

                window.print();

            } catch (e) {
                container.innerHTML = `<p class="error-message">Erro ao gerar recibo: ${e.message}</p>`;
            }
        });
    </script>
</body>

</html>