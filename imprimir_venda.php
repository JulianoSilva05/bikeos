<?php
// ARQUIVO: imprimir_venda.php (VERSÃO ATUALIZADA PARA IMPRESSORA TÉRMICA)
require_once 'api/db_connection.php';
session_start();

$venda_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($venda_id <= 0) {
    die("ID da venda inválido.");
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
        'endereco' => 'R. Américo Deodoro Brighenti, 322\nSão João del Rei/MG',
        'telefone' => '(32) 99962-8568',
    
    ];
}

// Busca dados da venda, cliente e vendedor
$stmt = $pdo->prepare("
    SELECT v.*, c.nome as cliente_nome, c.cpf as cliente_cpf, f.nome as vendedor_nome
    FROM vendas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    LEFT JOIN funcionarios f ON v.vendedor_id = f.id
    WHERE v.id = ?
");
$stmt->execute([$venda_id]);
$venda = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venda) {
    die("Venda não encontrada.");
}

// Busca itens da venda (produtos e serviços)
$itens = [];

if ($venda['os_id']) {
    // Se a venda tem OS vinculada, busca produtos e serviços da OS
    $stmt_produtos = $pdo->prepare("
        SELECT op.quantidade, op.preco_unitario_cobrado, p.nome as item_nome, 'produto' as tipo
        FROM os_produtos op
        JOIN produtos p ON op.produto_id = p.id
        WHERE op.os_id = ?
    ");
    $stmt_produtos->execute([$venda['os_id']]);
    $produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt_servicos = $pdo->prepare("
        SELECT 1 as quantidade, os_s.preco_cobrado as preco_unitario_cobrado, s.nome as item_nome, 'servico' as tipo
        FROM os_servicos os_s
        JOIN servicos s ON os_s.servico_id = s.id
        WHERE os_s.os_id = ?
    ");
    $stmt_servicos->execute([$venda['os_id']]);
    $servicos = $stmt_servicos->fetchAll(PDO::FETCH_ASSOC);
    
    $itens = array_merge($produtos, $servicos);
} else {
    // Se é venda direta, busca apenas produtos
    $stmt_itens = $pdo->prepare("
        SELECT vi.quantidade, vi.preco_unitario_cobrado, p.nome as item_nome, 'produto' as tipo
        FROM venda_itens vi
        JOIN produtos p ON vi.produto_id = p.id
        WHERE vi.venda_id = ?
    ");
    $stmt_itens->execute([$venda_id]);
    $itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Comprovante de Venda #<?php echo $venda_id; ?></title>
    <style>
        /* --- ESTILO OTIMIZADO PARA IMPRESSORA TÉRMICA 80mm --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace; /* Fonte monoespaçada */
            font-size: 12px;
            line-height: 1.4;
        }
        body {
            width: 280px; /* Largura aproximada para 80mm */
            margin: 0 auto;
            padding: 10px;
        }
        .header, .footer, .info {
            text-align: center;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        .header p, .info p {
            font-size: 11px;
            margin: 2px 0;
            text-align: left;
        }
        h2 {
            text-align: center;
            font-size: 14px;
            margin: 10px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            text-align: left;
            padding: 2px 0;
        }
        .text-right {
            text-align: right;
        }
        .total-geral {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .total-geral td {
            padding-top: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
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
    <div class="comprovante">
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

        </div>
        
        <h2>Comprovante de Venda</h2>
        
        <div class="info">
            <p><strong>Venda N°:</strong> <?php echo $venda['id']; ?></p>
            <p><strong>Data:</strong> <?php echo date("d/m/Y H:i", strtotime($venda['data_venda'])); ?></p>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($venda['cliente_nome'] ?? 'Consumidor Final'); ?></p>
            <p><strong>Pagamento:</strong> <?php echo $venda['metodo_pagamento']; ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Qtd</th>
                    <th>Item</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?php echo $item['quantidade']; ?>x</td>
                        <td>
                            <?php echo htmlspecialchars($item['item_nome']); ?>
                            <?php if ($item['tipo'] == 'servico'): ?>
                                <br><small>(Serviço)</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">R$ <?php echo number_format($item['quantidade'] * $item['preco_unitario_cobrado'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table style="margin-top: 10px;">
             <tr class="total-geral">
                <td>TOTAL:</td>
                <td class="text-right">R$ <?php echo number_format($venda['valor_total'], 2, ',', '.'); ?></td>
            </tr>
        </table>

        <p class="footer">Obrigado pela preferência!<br>Este comprovante não tem valor fiscal.</p>
    </div>

    <script>
        window.onload = function() {
            <?php if (!$config_loja || empty($config_loja['nome_loja'])): ?>
                 if (confirm('ATENÇÃO: Algumas configurações da loja não estão preenchidas.\n\nDeseja ir para a página de Funcionalidades para configurar os dados da loja (nome, endereço, telefone)?')) {
                     window.open('funcionalidades.php', '_blank');
                 }
             <?php endif; ?>
            window.print();
        };
    </script>
</body>

</html>