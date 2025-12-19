<?php
require_once 'auth_check.php';
require_once 'api/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];
$produto_id = isset($_GET['produto_id']) ? (int)$_GET['produto_id'] : 0;

if (!$produto_id) {
    header('Location: gestao_produtos.php');
    exit;
}

// Buscar informações do produto
try {
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.nome,
            p.sku,
            p.tipo,
            p.marca,
            p.estoque_atual,
            p.estoque_minimo,
            f.nome_empresa as fornecedor_nome
        FROM produtos p
        LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
        WHERE p.id = ? AND p.empresa_id = ?
    ");
    $stmt->execute([$produto_id, $empresa_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produto) {
        header('Location: gestao_produtos.php');
        exit;
    }
    
    // Buscar histórico de movimentações
    $stmt = $pdo->prepare("
        SELECT 
            m.id,
            m.tipo,
            m.quantidade,
            m.observacao,
            m.data_movimentacao,
            u.nome as usuario_nome
        FROM movimentacoes_estoque m
        LEFT JOIN usuarios u ON m.usuario_id = u.id
        WHERE m.produto_id = ? AND m.empresa_id = ?
        ORDER BY m.data_movimentacao DESC
    ");
    $stmt->execute([$produto_id, $empresa_id]);
    $movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $erro = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Estoque - <?= htmlspecialchars($produto['nome'] ?? 'Produto') ?> - BikeOS</title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .produto-info {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border: 1px solid #eee;
        }
        
        .produto-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .produto-detalhes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .detalhe-item {
            display: flex;
            flex-direction: column;
        }
        
        .detalhe-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.25rem;
        }
        
        .detalhe-valor {
            font-weight: bold;
            color: #333;
        }
        
        .historico-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #eee;
        }
        
        .historico-header {
            background: var(--cor-primaria);
            color: white;
            padding: 1rem;
            font-weight: bold;
        }
        
        .movimentacao-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .movimentacao-item:last-child {
            border-bottom: none;
        }
        
        .movimentacao-info {
            flex: 1;
        }
        
        .movimentacao-tipo {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .tipo-entrada {
            background: #d4edda;
            color: #155724;
        }
        
        .tipo-saida {
            background: #f8d7da;
            color: #721c24;
        }
        
        .tipo-ajuste {
            background: #fff3cd;
            color: #856404;
        }
        
        .movimentacao-detalhes {
            font-size: 0.9rem;
            color: #666;
        }
        
        .movimentacao-quantidade {
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            min-width: 80px;
        }
        
        .quantidade-positiva {
            color: #28a745;
        }
        
        .quantidade-negativa {
            color: #dc3545;
        }
        
        .movimentacao-data {
            text-align: right;
            font-size: 0.9rem;
            color: #666;
            min-width: 120px;
        }
        
        .sem-movimentacoes {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .voltar-btn {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include '_partials/navigation_header.php'; ?>
    
    <div class="container">
        <div class="voltar-btn">
            <a href="gestao_produtos.php" class="btn btn-secundario">
                <i class="fa-solid fa-arrow-left"></i> Voltar para Gestão de Produtos
            </a>
        </div>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-erro">
                <i class="fa-solid fa-exclamation-triangle"></i>
                Erro ao carregar histórico: <?= htmlspecialchars($erro) ?>
            </div>
        <?php else: ?>
            
            <!-- Informações do Produto -->
            <div class="produto-info">
                <div class="produto-header">
                    <h1><i class="fa-solid fa-clock-rotate-left"></i> Histórico de Estoque</h1>
                </div>
                
                <div class="produto-detalhes">
                    <div class="detalhe-item">
                        <span class="detalhe-label">Nome do Produto</span>
                        <span class="detalhe-valor"><?= htmlspecialchars($produto['nome']) ?></span>
                    </div>
                    <div class="detalhe-item">
                        <span class="detalhe-label">Código/SKU</span>
                        <span class="detalhe-valor"><?= htmlspecialchars($produto['sku'] ?: '-') ?></span>
                    </div>
                    <div class="detalhe-item">
                        <span class="detalhe-label">Tipo</span>
                        <span class="detalhe-valor"><?= htmlspecialchars($produto['tipo']) ?></span>
                    </div>
                    <div class="detalhe-item">
                        <span class="detalhe-label">Marca</span>
                        <span class="detalhe-valor"><?= htmlspecialchars($produto['marca'] ?: '-') ?></span>
                    </div>
                    <div class="detalhe-item">
                        <span class="detalhe-label">Estoque Atual</span>
                        <span class="detalhe-valor"><?= $produto['estoque_atual'] ?> unidades</span>
                    </div>
                    <div class="detalhe-item">
                        <span class="detalhe-label">Estoque Mínimo</span>
                        <span class="detalhe-valor"><?= $produto['estoque_minimo'] ?> unidades</span>
                    </div>
                    <div class="detalhe-item">
                        <span class="detalhe-label">Fornecedor</span>
                        <span class="detalhe-valor"><?= htmlspecialchars($produto['fornecedor_nome'] ?: '-') ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Histórico de Movimentações -->
            <div class="historico-container">
                <div class="historico-header">
                    <i class="fa-solid fa-list"></i> Histórico de Movimentações
                </div>
                
                <?php if (empty($movimentacoes)): ?>
                    <div class="sem-movimentacoes">
                        <i class="fa-solid fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; color: #ccc;"></i>
                        <p>Nenhuma movimentação encontrada para este produto.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($movimentacoes as $mov): 
                        // Determinar tipo e classe CSS
                        $tipo_class = '';
                        $quantidade_class = '';
                        $sinal = '';
                        
                        switch ($mov['tipo']) {
                            case 'ENTRADA':
                            case 'AJUSTE_AUMENTO':
                                $tipo_class = 'tipo-entrada';
                                $quantidade_class = 'quantidade-positiva';
                                $sinal = '+';
                                break;
                            case 'SAIDA':
                            case 'SAIDA_VENDA':
                            case 'SAIDA_OS':
                            case 'AJUSTE_REDUCAO':
                                $tipo_class = 'tipo-saida';
                                $quantidade_class = 'quantidade-negativa';
                                $sinal = '-';
                                break;
                            default:
                                $tipo_class = 'tipo-ajuste';
                                $quantidade_class = '';
                        }
                    ?>
                        <div class="movimentacao-item">
                            <div class="movimentacao-info">
                                <div>
                                    <span class="movimentacao-tipo <?= $tipo_class ?>">
                                        <?= htmlspecialchars($mov['tipo']) ?>
                                    </span>
                                </div>
                                <div class="movimentacao-detalhes">
                                    <?php if ($mov['observacao']): ?>
                                        <strong>Observação:</strong> <?= htmlspecialchars($mov['observacao']) ?><br>
                                    <?php endif; ?>
                                    <?php if ($mov['usuario_nome']): ?>
                                        <strong>Usuário:</strong> <?= htmlspecialchars($mov['usuario_nome']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="movimentacao-quantidade <?= $quantidade_class ?>">
                                <?= $sinal ?><?= abs($mov['quantidade']) ?>
                            </div>
                            
                            <div class="movimentacao-data">
                                <?= date('d/m/Y', strtotime($mov['data_movimentacao'])) ?><br>
                                <small><?= date('H:i:s', strtotime($mov['data_movimentacao'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
        <?php endif; ?>
    </div>
</body>
</html>