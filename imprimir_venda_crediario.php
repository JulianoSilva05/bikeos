<?php
// ARQUIVO: imprimir_venda_crediario.php - Impressão combinada de venda e dívida para crediário

require_once 'auth_check.php';
require_once 'api/db_connection.php';

$venda_id = (int)($_GET['id'] ?? 0);

if ($venda_id <= 0) {
    die('ID da venda inválido.');
}

$empresa_id = $_SESSION['empresa_id'];

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
        'endereco' => '',
        'telefone' => '',
        'cnpj' => ''
    ];
}

// Buscar dados da venda
$stmt = $pdo->prepare("
    SELECT v.*, c.nome as cliente_nome, c.telefone as cliente_telefone, c.cpf as cliente_cpf, 
           c.cep as cliente_cep, c.logradouro as cliente_logradouro, c.numero as cliente_numero,
           c.complemento as cliente_complemento, c.bairro as cliente_bairro, 
           c.cidade as cliente_cidade, c.estado as cliente_estado,
           f.nome as vendedor_nome
    FROM vendas v 
    LEFT JOIN clientes c ON v.cliente_id = c.id 
    JOIN funcionarios f ON v.vendedor_id = f.id
    WHERE v.id = ? AND v.empresa_id = ?
");
$stmt->execute([$venda_id, $empresa_id]);
$venda = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venda) {
    die('Venda não encontrada.');
}

// Buscar itens da venda
$stmt = $pdo->prepare("
    SELECT vi.*, p.nome as produto_nome, p.sku
    FROM venda_itens vi 
    JOIN produtos p ON vi.produto_id = p.id 
    WHERE vi.venda_id = ?
");
$stmt->execute([$venda_id]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para formatar CPF
function formatarCPF($cpf) {
    if (strlen($cpf) == 11) {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    return $cpf;
}

// Função para formatar CNPJ
function formatarCNPJ($cnpj) {
    if (strlen($cnpj) == 14) {
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }
    return $cnpj;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venda e Dívida #<?= $venda_id ?></title>
    <style>
        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
            body {
                margin: 0;
                padding: 5mm;
                font-family: 'Courier New', monospace;
                font-size: 12px;
                line-height: 1.2;
            }
            .no-print {
                display: none;
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
            max-width: 300px;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .empresa-nome {
            font-weight: bold;
            font-size: 14px;
        }
        
        .linha {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }
        
        .item {
            margin: 5px 0;
        }
        
        .total {
            font-weight: bold;
            font-size: 14px;
            text-align: right;
            margin-top: 10px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
        }
        
        .corte {
            text-align: center;
            margin: 20px 0;
            font-size: 10px;
            color: #666;
        }
        
        .divida-header {
            text-align: center;
            font-weight: bold;
            margin: 20px 0 10px 0;
            font-size: 14px;
        }
        
        .divida-info {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <!-- COMPROVANTE DE VENDA -->
    <div class="header">
        <div class="empresa-nome"><?= htmlspecialchars($config_loja['nome_loja']) ?></div>
        <?php if ($config_loja['cnpj']): ?>
            <div>CNPJ: <?= formatarCNPJ($config_loja['cnpj']) ?></div>
        <?php endif; ?>
        <?php if ($config_loja['endereco']): ?>
            <div><?= htmlspecialchars($config_loja['endereco']) ?></div>
        <?php endif; ?>
        <?php if ($config_loja['telefone']): ?>
            <div>Tel: <?= htmlspecialchars($config_loja['telefone']) ?></div>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; font-weight: bold; margin: 10px 0;">
        COMPROVANTE DE VENDA #<?= $venda_id ?>
    </div>
    
    <div>
        <strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?><br>
        <strong>Vendedor:</strong> <?= htmlspecialchars($venda['vendedor_nome']) ?><br>
        <strong>Cliente:</strong> <?= htmlspecialchars($venda['cliente_nome'] ?: 'Não informado') ?><br>
        <?php if ($venda['cliente_cpf']): ?>
            <strong>CPF:</strong> <?= formatarCPF($venda['cliente_cpf']) ?><br>
        <?php endif; ?>
        <strong>Pagamento:</strong> <?= $venda['metodo_pagamento'] ?>
    </div>
    
    <div class="linha"></div>
    
    <?php foreach ($itens as $item): ?>
        <div class="item">
            <?= htmlspecialchars($item['produto_nome']) ?>
            <?php if ($item['tamanho']): ?>
                (<?= htmlspecialchars($item['tamanho']) ?>)
            <?php endif; ?><br>
            <?= $item['quantidade'] ?>x R$ <?= number_format($item['preco_unitario_cobrado'], 2, ',', '.') ?> = 
            R$ <?= number_format($item['quantidade'] * $item['preco_unitario_cobrado'], 2, ',', '.') ?>
        </div>
    <?php endforeach; ?>
    
    <div class="linha"></div>
    
    <div class="total">
        TOTAL: R$ <?= number_format($venda['valor_total'], 2, ',', '.') ?>
    </div>
    
    <div class="footer">
        Obrigado pela preferência!<br>
        <?= date('d/m/Y H:i:s') ?>
    </div>
    
    <!-- COMANDO DE CORTE PARA IMPRESSORA TÉRMICA -->
    <div class="corte">
        <script>
            // Comando ESC/POS para corte parcial
            if (window.print) {
                document.write('\x1B\x6D'); // Comando de corte parcial
            }
        </script>
        ✂️ -------------------------------- ✂️
    </div>
    
    <!-- COMPROVANTE DE DÍVIDA -->
    <div class="divida-header">
        COMPROVANTE DE DÍVIDA
    </div>
    
    <div class="header">
        <div class="empresa-nome"><?= htmlspecialchars($config_loja['nome_loja']) ?></div>
        <?php if ($config_loja['cnpj']): ?>
            <div>CNPJ: <?= formatarCNPJ($config_loja['cnpj']) ?></div>
        <?php endif; ?>
    </div>
    
    <div class="divida-info">
        <strong>Venda:</strong> #<?= $venda_id ?><br>
        <strong>Data:</strong> <?= date('d/m/Y', strtotime($venda['data_venda'])) ?><br>
        <strong>Cliente:</strong> <?= htmlspecialchars($venda['cliente_nome']) ?><br>
        <?php if ($venda['cliente_cpf']): ?>
            <strong>CPF:</strong> <?= formatarCPF($venda['cliente_cpf']) ?><br>
        <?php endif; ?>
        <?php if ($venda['cliente_telefone']): ?>
            <strong>Telefone:</strong> <?= htmlspecialchars($venda['cliente_telefone']) ?><br>
        <?php endif; ?>
        <?php 
        $endereco_completo = '';
        if ($venda['cliente_logradouro']) {
            $endereco_completo = $venda['cliente_logradouro'];
            if ($venda['cliente_numero']) $endereco_completo .= ', ' . $venda['cliente_numero'];
            if ($venda['cliente_complemento']) $endereco_completo .= ', ' . $venda['cliente_complemento'];
            if ($venda['cliente_bairro']) $endereco_completo .= ', ' . $venda['cliente_bairro'];
            if ($venda['cliente_cidade']) $endereco_completo .= ', ' . $venda['cliente_cidade'];
            if ($venda['cliente_estado']) $endereco_completo .= ' - ' . $venda['cliente_estado'];
            if ($venda['cliente_cep']) $endereco_completo .= ', CEP: ' . $venda['cliente_cep'];
        }
        if ($endereco_completo): ?>
            <strong>Endereço:</strong> <?= htmlspecialchars($endereco_completo) ?><br>
        <?php endif; ?>
    </div>
    
    <div class="linha"></div>
    
    <div style="text-align: center; margin: 10px 0;">
        <strong>VALOR DA DÍVIDA</strong><br>
        <span style="font-size: 16px;">R$ <?= number_format($venda['valor_total'] - $venda['valor_pago'], 2, ',', '.') ?></span>
    </div>
    
    <?php if ($venda['valor_pago'] > 0): ?>
        <div>
            <strong>Valor Total:</strong> R$ <?= number_format($venda['valor_total'], 2, ',', '.') ?><br>
            <strong>Valor Pago:</strong> R$ <?= number_format($venda['valor_pago'], 2, ',', '.') ?><br>
            <strong>Saldo Devedor:</strong> R$ <?= number_format($venda['valor_total'] - $venda['valor_pago'], 2, ',', '.') ?>
        </div>
    <?php endif; ?>
    
    <div class="linha"></div>
    
    <div style="text-align: center; font-size: 10px; margin-top: 10px;">
        Este documento comprova a dívida do cliente<br>
        referente à venda realizada em crediário.<br><br>
        
        _________________________________<br>
        Assinatura do Cliente<br><br>
        
        Data: ___/___/______
    </div>
    
    <div class="footer">
        <?= date('d/m/Y H:i:s') ?>
    </div>
    
    <div class="no-print" style="text-align: center; margin: 20px 0;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Imprimir
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            ✖️ Fechar
        </button>
    </div>
    
    <script>
        // Auto-imprimir quando a página carregar
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>