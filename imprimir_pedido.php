<?php
require_once 'api/db_connection.php';
session_start();

$pedido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($pedido_id <= 0) {
    die("ID do pedido inválido.");
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

$stmt = $pdo->prepare("SELECT p.*, f.nome_empresa, f.cnpj, f.contato_nome FROM pedidos p JOIN fornecedores f ON p.fornecedor_id = f.id WHERE p.id = ?");
$stmt->execute([$pedido_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pedido) {
    die("Pedido não encontrado.");
}

$stmt_itens = $pdo->prepare("SELECT pi.quantidade_solicitada, p.nome as produto_nome, p.sku FROM pedido_itens pi JOIN produtos p ON pi.produto_id = p.id WHERE pi.pedido_id = ?");
$stmt_itens->execute([$pedido_id]);
$itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Pedido #<?php echo $pedido['id']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
        }

        body {
            width: 280px;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .header p {
            font-size: 11px;
            margin: 2px 0;
        }

        .section {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }

        .section h2 {
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .info-item {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            text-align: left;
            padding: 2px 0;
        }

        .text-right {
            text-align: right;
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
            <h2>Pedido de Compra N° <?php echo $pedido['id']; ?></h2>
        </div>
        <div class="section">
            <div class="info-item"><strong>Data:</strong> <?php echo date("d/m/Y", strtotime($pedido['data_pedido'])); ?></div>
            <div class="info-item"><strong>Fornecedor:</strong> <?php echo htmlspecialchars($pedido['nome_empresa']); ?></div>
            <?php if (!empty($pedido['cnpj'])): ?>
                <div class="info-item"><strong>CNPJ:</strong> <?php echo htmlspecialchars($pedido['cnpj']); ?></div>
            <?php endif; ?>
            <?php if (!empty($pedido['contato_nome'])): ?>
                <div class="info-item"><strong>A/C:</strong> <?php echo htmlspecialchars($pedido['contato_nome']); ?></div>
            <?php endif; ?>
        </div>
        <div class="section">
            <h2>Itens Solicitados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Produto (SKU)</th>
                        <th class="text-right">Qtd.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['produto_nome']); ?> (<?php echo htmlspecialchars($item['sku'] ?: 'N/A'); ?>)</td>
                            <td class="text-right"><?php echo $item['quantidade_solicitada']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="footer" style="margin-top: 20px;">
            <p>Agradecemos a parceria!</p>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>