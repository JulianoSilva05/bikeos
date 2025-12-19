<?php
require_once 'config.php';

// Verificar se os parâmetros foram passados
$tipo = $_GET['tipo'] ?? '';
$numero = $_GET['numero'] ?? 0;

if (empty($tipo) || empty($numero)) {
    die('Parâmetros inválidos');
}

// Buscar o documento
$documento = null;

switch($tipo) {
    case 'os':
        $stmt = $pdo->prepare("
            SELECT os.*, c.nome as cliente_nome, c.telefone as cliente_telefone, c.email as cliente_email,
                   c.cep as cliente_cep, c.logradouro as cliente_logradouro, c.numero as cliente_numero,
                   c.complemento as cliente_complemento, c.bairro as cliente_bairro, 
                   c.cidade as cliente_cidade, c.estado as cliente_estado, c.cpf as cliente_cpf
            FROM ordens_servico os 
            LEFT JOIN clientes c ON os.cliente_id = c.id 
            WHERE os.id = ?
        ");
        $stmt->execute([$numero]);
        $documento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Buscar serviços da OS
        if ($documento) {
            $stmt = $pdo->prepare("
                SELECT oss.*, s.nome as servico_nome, s.preco as servico_preco
                FROM os_servicos oss
                LEFT JOIN servicos s ON oss.servico_id = s.id
                WHERE oss.os_id = ?
            ");
            $stmt->execute([$numero]);
            $documento['servicos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        break;
        
    case 'venda':
        $stmt = $pdo->prepare("
            SELECT v.*, c.nome as cliente_nome, c.telefone as cliente_telefone, c.email as cliente_email,
                   c.cep as cliente_cep, c.logradouro as cliente_logradouro, c.numero as cliente_numero,
                   c.complemento as cliente_complemento, c.bairro as cliente_bairro, 
                   c.cidade as cliente_cidade, c.estado as cliente_estado, c.cpf as cliente_cpf
            FROM vendas v 
            LEFT JOIN clientes c ON v.cliente_id = c.id 
            WHERE v.id = ?
        ");
        $stmt->execute([$numero]);
        $documento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Buscar produtos da venda
        if ($documento) {
            $stmt = $pdo->prepare("
                SELECT vi.*, p.nome as produto_nome, p.preco_venda as produto_preco
                FROM venda_itens vi
                LEFT JOIN produtos p ON vi.produto_id = p.id
                WHERE vi.venda_id = ?
            ");
            $stmt->execute([$numero]);
            $documento['produtos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        break;
        
    case 'divida':
        $stmt = $pdo->prepare("
            SELECT v.*, c.nome as cliente_nome, c.telefone as cliente_telefone, c.email as cliente_email,
                   c.cep as cliente_cep, c.logradouro as cliente_logradouro, c.numero as cliente_numero,
                   c.complemento as cliente_complemento, c.bairro as cliente_bairro, 
                   c.cidade as cliente_cidade, c.estado as cliente_estado, c.cpf as cliente_cpf
            FROM vendas v 
            LEFT JOIN clientes c ON v.cliente_id = c.id 
            WHERE v.id = ? AND v.metodo_pagamento = 'CREDIARIO'
        ");
        $stmt->execute([$numero]);
        $documento = $stmt->fetch(PDO::FETCH_ASSOC);
        break;
        
    case 'pedido':
        $stmt = $pdo->prepare("
            SELECT p.*, f.nome_empresa as fornecedor_nome, f.telefone as fornecedor_telefone, f.email as fornecedor_email,
                   f.endereco as fornecedor_endereco, f.cnpj as fornecedor_cnpj
            FROM pedidos p 
            LEFT JOIN fornecedores f ON p.fornecedor_id = f.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$numero]);
        $documento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Buscar produtos do pedido
        if ($documento) {
            $stmt = $pdo->prepare("
                SELECT pi.*, pr.nome as produto_nome, pi.preco_custo_unitario as produto_preco
                FROM pedido_itens pi
                LEFT JOIN produtos pr ON pi.produto_id = pr.id
                WHERE pi.pedido_id = ?
            ");
            $stmt->execute([$numero]);
            $documento['produtos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        break;
        
    default:
        die('Tipo de documento inválido');
}

if (!$documento) {
    die('Documento não encontrado');
}

// Buscar configurações da loja
$stmt = $pdo->prepare("SELECT * FROM configuracoes_loja LIMIT 1");
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($tipo) ?> #<?= $numero ?> - Impressão - BikeOS</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 20px;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            max-height: 80px;
            margin-bottom: 10px;
        }
        
        .company-info {
            margin-bottom: 20px;
        }
        
        .document-info {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .client-info {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f2f2f2;
        }
        
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .print-buttons {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button class="btn" onclick="window.print()">🖨️ Imprimir</button>
        <a href="funcionalidades.php" class="btn">← Voltar</a>
    </div>
    
    <div class="header">
        <?php if ($config && $config['logo']): ?>
            <img src="<?= $config['logo'] ?>" alt="Logo" class="logo">
        <?php endif; ?>
        
        <div class="company-info">
            <h2><?= $config['nome_loja'] ?? 'Dengo Bike' ?></h2>
            <?php if ($config): ?>
                <?php if ($config['cnpj']): ?><p>CNPJ: <?= $config['cnpj'] ?></p><?php endif; ?>
                <?php if ($config['telefone']): ?><p>Telefone: <?= $config['telefone'] ?></p><?php endif; ?>
                <?php if ($config['endereco']): ?><p>Endereço: <?= $config['endereco'] ?></p><?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="document-info">
        <h3><?= strtoupper($tipo) ?> #<?= $numero ?></h3>
        <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($documento['data_entrada'] ?? $documento['data_venda'] ?? $documento['data_pedido'] ?? $documento['data_criacao'] ?? $documento['data'])) ?></p>
        <?php if (isset($documento['status'])): ?>
            <p><strong>Status:</strong> <?= ucfirst($documento['status']) ?></p>
        <?php endif; ?>
    </div>
    
    <?php if ($documento['cliente_nome'] ?? $documento['fornecedor_nome']): ?>
    <div class="client-info">
        <h4><?= $tipo == 'pedido' ? 'Dados do Fornecedor' : 'Dados do Cliente' ?></h4>
        <p><strong>Nome:</strong> <?= $documento['cliente_nome'] ?? $documento['fornecedor_nome'] ?></p>
        <?php if ($documento['cliente_telefone'] ?? $documento['fornecedor_telefone']): ?>
            <p><strong>Telefone:</strong> <?= $documento['cliente_telefone'] ?? $documento['fornecedor_telefone'] ?></p>
        <?php endif; ?>
        <?php if ($documento['cliente_email'] ?? $documento['fornecedor_email']): ?>
            <p><strong>E-mail:</strong> <?= $documento['cliente_email'] ?? $documento['fornecedor_email'] ?></p>
        <?php endif; ?>
        <?php if ($documento['cliente_cpf'] ?? $documento['fornecedor_cnpj']): ?>
            <p><strong><?= $tipo == 'pedido' ? 'CNPJ' : 'CPF' ?>:</strong> <?= $documento['cliente_cpf'] ?? $documento['fornecedor_cnpj'] ?></p>
        <?php endif; ?>
        <?php 
        $endereco_completo = '';
        if ($documento['cliente_logradouro']) {
            $endereco_completo = $documento['cliente_logradouro'];
            if ($documento['cliente_numero']) $endereco_completo .= ', ' . $documento['cliente_numero'];
            if ($documento['cliente_complemento']) $endereco_completo .= ', ' . $documento['cliente_complemento'];
            if ($documento['cliente_bairro']) $endereco_completo .= ', ' . $documento['cliente_bairro'];
            if ($documento['cliente_cidade']) $endereco_completo .= ', ' . $documento['cliente_cidade'];
            if ($documento['cliente_estado']) $endereco_completo .= ' - ' . $documento['cliente_estado'];
            if ($documento['cliente_cep']) $endereco_completo .= ', CEP: ' . $documento['cliente_cep'];
        } elseif ($documento['fornecedor_endereco']) {
            $endereco_completo = $documento['fornecedor_endereco'];
        }
        if ($endereco_completo): ?>
            <p><strong>Endereço:</strong> <?= $endereco_completo ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($tipo == 'os' && isset($documento['servicos'])): ?>
    <h4>Serviços</h4>
    <table class="items-table">
        <thead>
            <tr>
                <th>Serviço</th>
                <th>Descrição</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documento['servicos'] as $servico): ?>
            <tr>
                <td><?= $servico['servico_nome'] ?></td>
                <td><?= $servico['descricao'] ?? '-' ?></td>
                <td>R$ <?= number_format($servico['preco'] ?? $servico['servico_preco'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    
    <?php if (($tipo == 'venda' || $tipo == 'pedido') && isset($documento['produtos'])): ?>
    <h4>Produtos</h4>
    <table class="items-table">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Valor Unitário</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documento['produtos'] as $produto): ?>
            <tr>
                <td><?= $produto['produto_nome'] ?></td>
                <td><?= $produto['quantidade'] ?? $produto['quantidade_solicitada'] ?></td>
                <td>R$ <?= number_format($produto['preco_unitario_cobrado'] ?? $produto['produto_preco'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format(($produto['preco_unitario_cobrado'] ?? $produto['produto_preco']) * ($produto['quantidade'] ?? $produto['quantidade_solicitada']), 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    
    <?php if ($tipo == 'divida'): ?>
    <div class="client-info">
        <h4>Detalhes da Dívida</h4>
        <p><strong>Descrição:</strong> <?= $documento['descricao'] ?></p>
        <p><strong>Data de Vencimento:</strong> <?= date('d/m/Y', strtotime($documento['data_vencimento'])) ?></p>
        <?php if ($documento['observacoes']): ?>
            <p><strong>Observações:</strong> <?= $documento['observacoes'] ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($documento['valor_total']) || isset($documento['valor'])): ?>
    <div class="total">
        <p>TOTAL: R$ <?= number_format($documento['valor_total'] ?? $documento['valor'], 2, ',', '.') ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (isset($documento['observacoes']) && $documento['observacoes'] && $tipo != 'divida'): ?>
    <div class="client-info">
        <h4>Observações</h4>
        <p><?= nl2br($documento['observacoes']) ?></p>
    </div>
    <?php endif; ?>
    
    <div class="footer">
        <p>Documento gerado em <?= date('d/m/Y H:i') ?></p>
    </div>
</body>
</html>