<?php
session_start();
require_once 'api/db_connection.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$empresa_id = $_SESSION['empresa_id'];
$venda_id = $_GET['venda_id'] ?? 0;

// Buscar dados da NFe
$stmt = $pdo->prepare("
    SELECT v.*, c.nome as cliente_nome, c.documento as cpf_cnpj, c.email, c.telefone,
           c.logradouro, c.numero, c.complemento, c.bairro, c.cidade, c.estado, c.cep,
           CONCAT(c.logradouro, ', ', c.numero, ' - ', c.bairro, ' - ', c.cidade, '/', c.estado, ' - CEP: ', c.cep) as endereco,
           e.nome as empresa_nome, e.cnpj as empresa_cnpj, e.endereco as empresa_endereco,
           e.numero as empresa_numero, e.bairro as empresa_bairro, e.cidade as empresa_cidade,
           e.estado as empresa_estado, e.cep as empresa_cep, e.inscricao_estadual
    FROM vendas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    JOIN empresas e ON v.empresa_id = e.id
    WHERE v.id = ? AND v.empresa_id = ? AND v.nfe_numero IS NOT NULL
");
$stmt->execute([$venda_id, $empresa_id]);
$nfe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$nfe) {
    echo "<script>alert('NFe não encontrada'); window.close();</script>";
    exit;
}

// Buscar itens da NFe
$stmt = $pdo->prepare("
    SELECT vi.*, p.nome as produto_nome, p.unidade
    FROM venda_itens vi
    JOIN produtos p ON vi.produto_id = p.id
    WHERE vi.venda_id = ?
");
$stmt->execute([$venda_id]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DANFE - NFe <?= $nfe['nfe_numero'] ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 10px;
            color: #000;
        }
        
        .danfe-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            border: 1px solid #000;
        }
        
        .header {
            display: flex;
            border-bottom: 1px solid #000;
        }
        
        .empresa-info {
            flex: 1;
            padding: 5px;
            border-right: 1px solid #000;
        }
        
        .danfe-info {
            width: 200px;
            padding: 5px;
            text-align: center;
        }
        
        .chave-acesso {
            width: 100%;
            padding: 5px;
            border-bottom: 1px solid #000;
            text-align: center;
            font-weight: bold;
        }
        
        .destinatario {
            padding: 5px;
            border-bottom: 1px solid #000;
        }
        
        .produtos-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        
        .produtos-table th,
        .produtos-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: left;
        }
        
        .produtos-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .totais {
            display: flex;
            border-top: 1px solid #000;
        }
        
        .impostos {
            flex: 1;
            padding: 5px;
            border-right: 1px solid #000;
        }
        
        .total-nfe {
            width: 200px;
            padding: 5px;
            text-align: right;
        }
        
        .observacoes {
            padding: 5px;
            border-top: 1px solid #000;
            min-height: 50px;
        }
        
        .rodape {
            padding: 5px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 8px;
        }
        
        .btn-imprimir {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
        }
        
        .btn-imprimir:hover {
            background-color: #0056b3;
        }
        
        .field-label {
            font-weight: bold;
            font-size: 8px;
        }
        
        .field-value {
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir DANFE</button>
        <button class="btn-imprimir" onclick="window.close()">❌ Fechar</button>
    </div>
    
    <div class="danfe-container">
        <!-- Cabeçalho -->
        <div class="header">
            <div class="empresa-info">
                <div class="field-label">RAZÃO SOCIAL / NOME</div>
                <div class="field-value"><?= htmlspecialchars($nfe['empresa_nome']) ?></div>
                
                <div class="field-label">ENDEREÇO</div>
                <div class="field-value">
                    <?= htmlspecialchars($nfe['empresa_endereco']) ?>, <?= htmlspecialchars($nfe['empresa_numero']) ?><br>
                    <?= htmlspecialchars($nfe['empresa_bairro']) ?> - <?= htmlspecialchars($nfe['empresa_cidade']) ?>/<?= htmlspecialchars($nfe['empresa_estado']) ?><br>
                    CEP: <?= htmlspecialchars($nfe['empresa_cep']) ?>
                </div>
                
                <div style="display: flex; margin-top: 5px;">
                    <div style="flex: 1;">
                        <div class="field-label">CNPJ</div>
                        <div class="field-value"><?= htmlspecialchars($nfe['empresa_cnpj']) ?></div>
                    </div>
                    <div style="flex: 1;">
                        <div class="field-label">INSCRIÇÃO ESTADUAL</div>
                        <div class="field-value"><?= htmlspecialchars($nfe['inscricao_estadual']) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="danfe-info">
                <div style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">
                    DANFE<br>
                    Documento Auxiliar da<br>
                    Nota Fiscal Eletrônica
                </div>
                
                <div class="field-label">TIPO</div>
                <div class="field-value"><?= strtoupper($nfe['nfe_tipo']) ?></div>
                
                <div class="field-label">SÉRIE</div>
                <div class="field-value"><?= $nfe['nfe_serie'] ?></div>
                
                <div class="field-label">NÚMERO</div>
                <div class="field-value"><?= $nfe['nfe_numero'] ?></div>
                
                <div class="field-label">DATA DE EMISSÃO</div>
                <div class="field-value"><?= date('d/m/Y H:i:s', strtotime($nfe['nfe_data_emissao'])) ?></div>
            </div>
        </div>
        
        <!-- Chave de Acesso -->
        <div class="chave-acesso">
            <div class="field-label">CHAVE DE ACESSO</div>
            <div style="font-size: 12px; letter-spacing: 2px;"><?= chunk_split($nfe['nfe_chave'], 4, ' ') ?></div>
        </div>
        
        <!-- Destinatário -->
        <div class="destinatario">
            <div class="field-label">DESTINATÁRIO</div>
            <div style="display: flex; margin-top: 5px;">
                <div style="flex: 2;">
                    <div class="field-label">NOME/RAZÃO SOCIAL</div>
                    <div class="field-value"><?= htmlspecialchars($nfe['cliente_nome'] ?: 'CONSUMIDOR FINAL') ?></div>
                </div>
                <div style="flex: 1; margin-left: 10px;">
                    <div class="field-label">CPF/CNPJ</div>
                    <div class="field-value"><?= htmlspecialchars($nfe['cpf_cnpj'] ?: 'NÃO INFORMADO') ?></div>
                </div>
            </div>
            
            <?php if ($nfe['endereco']): ?>
            <div style="margin-top: 5px;">
                <div class="field-label">ENDEREÇO</div>
                <div class="field-value">
                    <?= htmlspecialchars($nfe['endereco']) ?>, <?= htmlspecialchars($nfe['numero']) ?><br>
                    <?= htmlspecialchars($nfe['bairro']) ?> - <?= htmlspecialchars($nfe['cidade']) ?>/<?= htmlspecialchars($nfe['estado']) ?><br>
                    CEP: <?= htmlspecialchars($nfe['cep']) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Produtos/Serviços -->
        <table class="produtos-table">
            <thead>
                <tr>
                    <th style="width: 5%;">ITEM</th>
                    <th style="width: 10%;">CÓDIGO</th>
                    <th style="width: 25%;">DESCRIÇÃO</th>
                    <th style="width: 8%;">NCM</th>
                    <th style="width: 8%;">CFOP</th>
                    <th style="width: 5%;">UN</th>
                    <th style="width: 8%;">QTDE</th>
                    <th style="width: 10%;">VL UNIT</th>
                    <th style="width: 10%;">VL TOTAL</th>
                    <th style="width: 11%;">VL IMPOSTOS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($item['produto_id']) ?></td>
                    <td><?= htmlspecialchars($item['produto_nome']) ?></td>
                    <td><?= htmlspecialchars($item['ncm'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($nfe['cfop'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($item['unidade'] ?: 'UN') ?></td>
                    <td><?= number_format($item['quantidade'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format(($item['valor_icms'] ?? 0) + ($item['valor_pis'] ?? 0) + ($item['valor_cofins'] ?? 0), 2, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Totais -->
        <div class="totais">
            <div class="impostos">
                <div class="field-label">CÁLCULO DO IMPOSTO</div>
                <div style="display: flex; margin-top: 5px;">
                    <div style="flex: 1;">
                        <div class="field-label">BASE CÁLC. ICMS</div>
                        <div class="field-value">R$ <?= number_format($nfe['valor_total'], 2, ',', '.') ?></div>
                    </div>
                    <div style="flex: 1;">
                        <div class="field-label">VALOR ICMS</div>
                        <div class="field-value">R$ <?= number_format(array_sum(array_column($itens, 'valor_icms')), 2, ',', '.') ?></div>
                    </div>
                </div>
                <div style="display: flex; margin-top: 5px;">
                    <div style="flex: 1;">
                        <div class="field-label">VALOR PIS</div>
                        <div class="field-value">R$ <?= number_format(array_sum(array_column($itens, 'valor_pis')), 2, ',', '.') ?></div>
                    </div>
                    <div style="flex: 1;">
                        <div class="field-label">VALOR COFINS</div>
                        <div class="field-value">R$ <?= number_format(array_sum(array_column($itens, 'valor_cofins')), 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>
            
            <div class="total-nfe">
                <div class="field-label">VALOR TOTAL DOS PRODUTOS</div>
                <div class="field-value" style="font-size: 12px;">R$ <?= number_format($nfe['valor_total'], 2, ',', '.') ?></div>
                
                <div class="field-label" style="margin-top: 10px;">VALOR TOTAL DA NFe</div>
                <div class="field-value" style="font-size: 14px; font-weight: bold;">R$ <?= number_format($nfe['valor_total'], 2, ',', '.') ?></div>
            </div>
        </div>
        
        <!-- Observações -->
        <div class="observacoes">
            <div class="field-label">INFORMAÇÕES ADICIONAIS</div>
            <div class="field-value">
                <?= htmlspecialchars($nfe['observacoes_fiscais'] ?: 'Documento emitido por ME ou EPP optante pelo Simples Nacional.') ?><br>
                Natureza da Operação: <?= htmlspecialchars($nfe['natureza_operacao'] ?: 'Venda de mercadoria') ?><br>
                Método de Pagamento: <?= htmlspecialchars($nfe['metodo_pagamento']) ?>
            </div>
        </div>
        
        <!-- Rodapé -->
        <div class="rodape">
            <strong>DANFE - Documento Auxiliar da Nota Fiscal Eletrônica</strong><br>
            Esta é uma representação gráfica de uma NFe. Consulte a autenticidade no site da SEFAZ.<br>
            Chave de Acesso: <?= $nfe['nfe_chave'] ?>
        </div>
    </div>
    
    <script>
        // Auto-imprimir quando abrir em nova janela
        if (window.opener) {
            setTimeout(() => {
                window.print();
            }, 1000);
        }
    </script>
</body>
</html>