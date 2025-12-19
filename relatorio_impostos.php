<?php
session_start();
require_once 'api/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$empresa_id = $_SESSION['empresa_id'];
$usuario_id = $_SESSION['user_id'];

// Filtros
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');

// Buscar vendas com impostos calculados
$stmt = $pdo->prepare("
    SELECT 
        v.id as venda_id,
        v.data_venda,
        v.valor_total,
        v.cfop,
        v.natureza_operacao,
        c.nome as cliente_nome,
        u.nome as vendedor_nome,
        SUM(vi.valor_icms) as total_icms,
        SUM(vi.valor_pis) as total_pis,
        SUM(vi.valor_cofins) as total_cofins,
        SUM(vi.valor_icms + vi.valor_pis + vi.valor_cofins) as total_impostos
    FROM vendas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    LEFT JOIN usuarios u ON v.vendedor_id = u.id
    LEFT JOIN venda_itens vi ON v.id = vi.venda_id
    WHERE v.empresa_id = ? 
        AND v.status = 'ATIVA'
        AND DATE(v.data_venda) BETWEEN ? AND ?
    GROUP BY v.id
    ORDER BY v.data_venda DESC
");
$stmt->execute([$empresa_id, $data_inicio, $data_fim]);
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular totais do período
$total_vendas = array_sum(array_column($vendas, 'valor_total'));
$total_icms_periodo = array_sum(array_column($vendas, 'total_icms'));
$total_pis_periodo = array_sum(array_column($vendas, 'total_pis'));
$total_cofins_periodo = array_sum(array_column($vendas, 'total_cofins'));
$total_impostos_periodo = array_sum(array_column($vendas, 'total_impostos'));

// Buscar detalhes por produto
$stmt_produtos = $pdo->prepare("
    SELECT 
        p.nome as produto_nome,
        p.ncm,
        SUM(vi.quantidade) as quantidade_vendida,
        SUM(vi.quantidade * vi.preco_unitario_cobrado) as valor_total_produto,
        SUM(vi.valor_icms) as icms_produto,
        SUM(vi.valor_pis) as pis_produto,
        SUM(vi.valor_cofins) as cofins_produto,
        AVG(vi.aliquota_icms) as aliquota_icms_media,
        AVG(vi.aliquota_pis) as aliquota_pis_media,
        AVG(vi.aliquota_cofins) as aliquota_cofins_media
    FROM venda_itens vi
    JOIN produtos p ON vi.produto_id = p.id
    JOIN vendas v ON vi.venda_id = v.id
    WHERE v.empresa_id = ? 
        AND v.status = 'ATIVA'
        AND DATE(v.data_venda) BETWEEN ? AND ?
    GROUP BY p.id, p.nome, p.ncm
    ORDER BY valor_total_produto DESC
");
$stmt_produtos->execute([$empresa_id, $data_inicio, $data_fim]);
$produtos_impostos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Impostos - BikeOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include '_partials/navigation_header.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                            Relatório de Impostos
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success" onclick="exportarRelatorio()">
                                <i class="fa-solid fa-file-excel me-2"></i>
                                Exportar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filtros -->
                        <form method="GET" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="data_inicio" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= $data_inicio ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="data_fim" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= $data_fim ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-filter me-2"></i>
                                    Filtrar
                                </button>
                            </div>
                        </form>
                        
                        <!-- Resumo do Período -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h6>Total Vendas</h6>
                                        <h4>R$ <?= number_format($total_vendas, 2, ',', '.') ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h6>ICMS</h6>
                                        <h4>R$ <?= number_format($total_icms_periodo, 2, ',', '.') ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h6>PIS</h6>
                                        <h4>R$ <?= number_format($total_pis_periodo, 2, ',', '.') ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6>COFINS</h6>
                                        <h4>R$ <?= number_format($total_cofins_periodo, 2, ',', '.') ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card bg-dark text-white">
                                    <div class="card-body text-center">
                                        <h6>Total Impostos</h6>
                                        <h4>R$ <?= number_format($total_impostos_periodo, 2, ',', '.') ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6>% Impostos</h6>
                                        <h4><?= $total_vendas > 0 ? number_format(($total_impostos_periodo / $total_vendas) * 100, 2, ',', '.') : '0,00' ?>%</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Abas -->
                        <ul class="nav nav-tabs" id="relatorioTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="vendas-tab" data-bs-toggle="tab" data-bs-target="#vendas" type="button" role="tab">
                                    <i class="fa-solid fa-shopping-cart me-2"></i>
                                    Vendas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="produtos-tab" data-bs-toggle="tab" data-bs-target="#produtos" type="button" role="tab">
                                    <i class="fa-solid fa-box me-2"></i>
                                    Por Produto
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="relatorioTabsContent">
                            <!-- Aba Vendas -->
                            <div class="tab-pane fade show active" id="vendas" role="tabpanel">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Data</th>
                                                <th>Cliente</th>
                                                <th>Vendedor</th>
                                                <th>CFOP</th>
                                                <th>Valor Total</th>
                                                <th>ICMS</th>
                                                <th>PIS</th>
                                                <th>COFINS</th>
                                                <th>Total Impostos</th>
                                                <th>% Impostos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($vendas as $venda): ?>
                                            <tr>
                                                <td><?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></td>
                                                <td><?= htmlspecialchars($venda['cliente_nome'] ?? 'Cliente Avulso') ?></td>
                                                <td><?= htmlspecialchars($venda['vendedor_nome']) ?></td>
                                                <td><?= htmlspecialchars($venda['cfop']) ?></td>
                                                <td>R$ <?= number_format($venda['valor_total'], 2, ',', '.') ?></td>
                                                <td>R$ <?= number_format($venda['total_icms'], 2, ',', '.') ?></td>
                                                <td>R$ <?= number_format($venda['total_pis'], 2, ',', '.') ?></td>
                                                <td>R$ <?= number_format($venda['total_cofins'], 2, ',', '.') ?></td>
                                                <td>R$ <?= number_format($venda['total_impostos'], 2, ',', '.') ?></td>
                                                <td><?= $venda['valor_total'] > 0 ? number_format(($venda['total_impostos'] / $venda['valor_total']) * 100, 2, ',', '.') : '0,00' ?>%</td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Aba Produtos -->
                            <div class="tab-pane fade" id="produtos" role="tabpanel">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Produto</th>
                                                <th>NCM</th>
                                                <th>Qtd Vendida</th>
                                                <th>Valor Total</th>
                                                <th>ICMS (<?= number_format(array_sum(array_column($produtos_impostos, 'aliquota_icms_media')) / count($produtos_impostos ?: [1]), 2, ',', '.') ?>%)</th>
                                                <th>PIS (<?= number_format(array_sum(array_column($produtos_impostos, 'aliquota_pis_media')) / count($produtos_impostos ?: [1]), 2, ',', '.') ?>%)</th>
                                                <th>COFINS (<?= number_format(array_sum(array_column($produtos_impostos, 'aliquota_cofins_media')) / count($produtos_impostos ?: [1]), 2, ',', '.') ?>%)</th>
                                                <th>Total Impostos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($produtos_impostos as $produto): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($produto['produto_nome']) ?></td>
                                                <td><?= htmlspecialchars($produto['ncm'] ?? 'N/A') ?></td>
                                                <td><?= number_format($produto['quantidade_vendida'], 0, ',', '.') ?></td>
                                                <td>R$ <?= number_format($produto['valor_total_produto'], 2, ',', '.') ?></td>
                                                <td>R$ <?= number_format($produto['icms_produto'], 2, ',', '.') ?></td>
                                                <td>R$ <?= number_format($produto['pis_produto'], 2, ',', '.') ?></td>
                                                <td>R$ <?= number_format($produto['cofins_produto'], 2, ',', '.') ?></td>
                                                <td>R$ <?= number_format($produto['icms_produto'] + $produto['pis_produto'] + $produto['cofins_produto'], 2, ',', '.') ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportarRelatorio() {
            const dataInicio = document.getElementById('data_inicio').value;
            const dataFim = document.getElementById('data_fim').value;
            
            const url = `api/exportar_impostos.php?data_inicio=${dataInicio}&data_fim=${dataFim}`;
            window.open(url, '_blank');
        }
    </script>
</body>
</html>