<?php
require_once 'auth_check.php';
require_once 'api/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];
$usuario_id = $_SESSION['user_id'];

// Buscar dados para o relatório
try {
    // Produtos com estoque
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.nome,
            p.sku,
            p.tipo,
            p.marca,
            p.estoque_atual,
            p.estoque_minimo,
            p.preco_custo,
            p.preco_venda,
            f.nome_empresa as fornecedor_nome
        FROM produtos p
        LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
        WHERE p.empresa_id = ? AND p.id != 1
        ORDER BY p.nome ASC
    ");
    $stmt->execute([$empresa_id]);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Movimentações recentes
    $stmt = $pdo->prepare("
        SELECT 
            m.id,
            m.produto_id,
            m.tipo,
            m.quantidade,
            m.observacao,
            m.data_movimentacao,
            p.nome as produto_nome,
            u.nome as usuario_nome
        FROM movimentacoes_estoque m
        INNER JOIN produtos p ON m.produto_id = p.id
        LEFT JOIN usuarios u ON m.usuario_id = u.id
        WHERE m.empresa_id = ?
        ORDER BY m.data_movimentacao DESC
        LIMIT 50
    ");
    $stmt->execute([$empresa_id]);
    $movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Estatísticas
    $total_produtos = count($produtos);
    $estoque_baixo = 0;
    $estoque_zerado = 0;
    $valor_total_estoque = 0;
    
    foreach ($produtos as $produto) {
        $estoque = (int)$produto['estoque_atual'];
        $estoque_minimo = (int)$produto['estoque_minimo'];
        $preco_custo = (float)$produto['preco_custo'];
        
        if ($estoque <= 0) {
            $estoque_zerado++;
        } elseif ($estoque <= $estoque_minimo) {
            $estoque_baixo++;
        }
        
        $valor_total_estoque += $estoque * $preco_custo;
    }
    
} catch (Exception $e) {
    $erro = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Estoque - BikeOS</title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
            .page-break { page-break-before: always; }
        }
        
        .relatorio-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--cor-primaria);
        }
        
        .estatisticas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .estatistica-card {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #eee;
            text-align: center;
        }
        
        .estatistica-valor {
            font-size: 2rem;
            font-weight: bold;
            color: var(--cor-primaria);
        }
        
        .tabela-relatorio {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        
        .tabela-relatorio th,
        .tabela-relatorio td {
            padding: 0.5rem;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 0.9rem;
        }
        
        .tabela-relatorio th {
            background-color: var(--cor-primaria);
            color: white;
            font-weight: bold;
        }
        
        .tabela-relatorio tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-estoque {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .estoque-ok { background: #d4edda; color: #155724; }
        .estoque-baixo { background: #fff3cd; color: #856404; }
        .estoque-zerado { background: #f8d7da; color: #721c24; }
        
        .tipo-movimentacao {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .entrada { background: #d4edda; color: #155724; }
        .saida { background: #f8d7da; color: #721c24; }
        .ajuste { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="relatorio-header">
            <h1><i class="fa-solid fa-chart-bar"></i> Relatório de Estoque</h1>
            <p>Gerado em: <?= date('d/m/Y H:i:s') ?></p>
        </div>
        
        <div class="no-print" style="margin-bottom: 2rem; text-align: center;">
            <button onclick="window.print()" class="btn btn-primario">
                <i class="fa-solid fa-print"></i> Imprimir Relatório
            </button>
            <button onclick="window.close()" class="btn btn-secundario">
                <i class="fa-solid fa-times"></i> Fechar
            </button>
        </div>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-erro">
                <i class="fa-solid fa-exclamation-triangle"></i>
                Erro ao gerar relatório: <?= htmlspecialchars($erro) ?>
            </div>
        <?php else: ?>
            
            <!-- Estatísticas -->
            <div class="estatisticas">
                <div class="estatistica-card">
                    <div class="estatistica-valor"><?= $total_produtos ?></div>
                    <div>Total de Produtos</div>
                </div>
                <div class="estatistica-card">
                    <div class="estatistica-valor"><?= $estoque_baixo ?></div>
                    <div>Estoque Baixo</div>
                </div>
                <div class="estatistica-card">
                    <div class="estatistica-valor"><?= $estoque_zerado ?></div>
                    <div>Estoque Zerado</div>
                </div>
                <div class="estatistica-card">
                    <div class="estatistica-valor">R$ <?= number_format($valor_total_estoque, 2, ',', '.') ?></div>
                    <div>Valor Total do Estoque</div>
                </div>
            </div>
            
            <!-- Tabela de Produtos -->
            <h2><i class="fa-solid fa-boxes"></i> Produtos em Estoque</h2>
            <table class="tabela-relatorio">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Marca</th>
                        <th>Estoque Atual</th>
                        <th>Estoque Mínimo</th>
                        <th>Status</th>
                        <th>Preço Custo</th>
                        <th>Preço Venda</th>
                        <th>Fornecedor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): 
                        $estoque = (int)$produto['estoque_atual'];
                        $estoque_minimo = (int)$produto['estoque_minimo'];
                        
                        if ($estoque <= 0) {
                            $status_class = 'estoque-zerado';
                            $status_text = 'Zerado';
                        } elseif ($estoque <= $estoque_minimo) {
                            $status_class = 'estoque-baixo';
                            $status_text = 'Baixo';
                        } else {
                            $status_class = 'estoque-ok';
                            $status_text = 'OK';
                        }
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($produto['sku'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($produto['nome']) ?></td>
                            <td><?= htmlspecialchars($produto['tipo']) ?></td>
                            <td><?= htmlspecialchars($produto['marca'] ?: '-') ?></td>
                            <td><?= $estoque ?></td>
                            <td><?= $estoque_minimo ?></td>
                            <td><span class="status-estoque <?= $status_class ?>"><?= $status_text ?></span></td>
                            <td>R$ <?= number_format($produto['preco_custo'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($produto['preco_venda'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($produto['fornecedor_nome'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="page-break"></div>
            
            <!-- Movimentações Recentes -->
            <h2><i class="fa-solid fa-clock-rotate-left"></i> Movimentações Recentes (Últimas 50)</h2>
            <table class="tabela-relatorio">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Observação</th>
                        <th>Usuário</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimentacoes as $mov): 
                        $tipo_class = '';
                        switch ($mov['tipo']) {
                            case 'ENTRADA':
                            case 'AJUSTE_AUMENTO':
                                $tipo_class = 'entrada';
                                break;
                            case 'SAIDA':
                            case 'SAIDA_VENDA':
                            case 'SAIDA_OS':
                            case 'AJUSTE_REDUCAO':
                                $tipo_class = 'saida';
                                break;
                            default:
                                $tipo_class = 'ajuste';
                        }
                    ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($mov['data_movimentacao'])) ?></td>
                            <td><?= htmlspecialchars($mov['produto_nome']) ?></td>
                            <td><span class="tipo-movimentacao <?= $tipo_class ?>"><?= htmlspecialchars($mov['tipo']) ?></span></td>
                            <td><?= $mov['quantidade'] ?></td>
                            <td><?= htmlspecialchars($mov['observacao'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($mov['usuario_nome'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
        <?php endif; ?>
    </div>
</body>
</html>