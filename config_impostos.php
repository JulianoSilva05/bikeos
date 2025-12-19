<?php
session_start();
require_once 'api/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$empresa_id = $_SESSION['empresa_id'];
$usuario_id = $_SESSION['user_id'];

// Buscar produtos para configuração
$stmt = $pdo->prepare("SELECT id, nome, ncm, cst_icms_padrao, cst_pis_padrao, cst_cofins_padrao, aliquota_icms_padrao, aliquota_pis_padrao, aliquota_cofins_padrao FROM produtos WHERE empresa_id = ? ORDER BY nome");
$stmt->execute([$empresa_id]);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração de Impostos - BikeOS</title>
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
                            <i class="fa-solid fa-calculator me-2"></i>
                            Configuração de Impostos
                        </h5>
                        <button class="btn btn-success" onclick="salvarTodasConfiguracoes()">
                            <i class="fa-solid fa-save me-2"></i>
                            Salvar Todas as Configurações
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Configure os dados fiscais padrão para cada produto. Estes dados serão utilizados automaticamente no cálculo de impostos nas vendas.
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Produto</th>
                                        <th>NCM</th>
                                        <th>CST ICMS</th>
                                        <th>Alíquota ICMS (%)</th>
                                        <th>CST PIS</th>
                                        <th>Alíquota PIS (%)</th>
                                        <th>CST COFINS</th>
                                        <th>Alíquota COFINS (%)</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $produto): ?>
                                    <tr id="produto-<?= $produto['id'] ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($produto['nome']) ?></strong>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" 
                                                   value="<?= htmlspecialchars($produto['ncm'] ?? '') ?>"
                                                   data-field="ncm" data-produto="<?= $produto['id'] ?>"
                                                   placeholder="Ex: 8712.00.10">
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" 
                                                    data-field="cst_icms_padrao" data-produto="<?= $produto['id'] ?>">
                                                <option value="">Selecione</option>
                                                <option value="00" <?= $produto['cst_icms_padrao'] == '00' ? 'selected' : '' ?>>00 - Tributada integralmente</option>
                                                <option value="10" <?= $produto['cst_icms_padrao'] == '10' ? 'selected' : '' ?>>10 - Tributada com cobrança do ICMS por ST</option>
                                                <option value="20" <?= $produto['cst_icms_padrao'] == '20' ? 'selected' : '' ?>>20 - Com redução de base de cálculo</option>
                                                <option value="30" <?= $produto['cst_icms_padrao'] == '30' ? 'selected' : '' ?>>30 - Isenta ou não tributada com cobrança do ICMS por ST</option>
                                                <option value="40" <?= $produto['cst_icms_padrao'] == '40' ? 'selected' : '' ?>>40 - Isenta</option>
                                                <option value="41" <?= $produto['cst_icms_padrao'] == '41' ? 'selected' : '' ?>>41 - Não tributada</option>
                                                <option value="50" <?= $produto['cst_icms_padrao'] == '50' ? 'selected' : '' ?>>50 - Suspensão</option>
                                                <option value="51" <?= $produto['cst_icms_padrao'] == '51' ? 'selected' : '' ?>>51 - Diferimento</option>
                                                <option value="60" <?= $produto['cst_icms_padrao'] == '60' ? 'selected' : '' ?>>60 - ICMS cobrado anteriormente por ST</option>
                                                <option value="70" <?= $produto['cst_icms_padrao'] == '70' ? 'selected' : '' ?>>70 - Com redução de base de cálculo e cobrança do ICMS por ST</option>
                                                <option value="90" <?= $produto['cst_icms_padrao'] == '90' ? 'selected' : '' ?>>90 - Outras</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" 
                                                   value="<?= $produto['aliquota_icms_padrao'] ?? '0.00' ?>"
                                                   data-field="aliquota_icms_padrao" data-produto="<?= $produto['id'] ?>"
                                                   step="0.01" min="0" max="100">
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" 
                                                    data-field="cst_pis_padrao" data-produto="<?= $produto['id'] ?>">
                                                <option value="">Selecione</option>
                                                <option value="01" <?= $produto['cst_pis_padrao'] == '01' ? 'selected' : '' ?>>01 - Operação Tributável com Alíquota Básica</option>
                                                <option value="02" <?= $produto['cst_pis_padrao'] == '02' ? 'selected' : '' ?>>02 - Operação Tributável com Alíquota por Unidade de Medida de Produto</option>
                                                <option value="03" <?= $produto['cst_pis_padrao'] == '03' ? 'selected' : '' ?>>03 - Operação Tributável com Alíquota por Unidade de Medida de Produto</option>
                                                <option value="04" <?= $produto['cst_pis_padrao'] == '04' ? 'selected' : '' ?>>04 - Operação Tributável Monofásica - Revenda a Alíquota Zero</option>
                                                <option value="05" <?= $produto['cst_pis_padrao'] == '05' ? 'selected' : '' ?>>05 - Operação Tributável por Substituição Tributária</option>
                                                <option value="06" <?= $produto['cst_pis_padrao'] == '06' ? 'selected' : '' ?>>06 - Operação Tributável a Alíquota Zero</option>
                                                <option value="07" <?= $produto['cst_pis_padrao'] == '07' ? 'selected' : '' ?>>07 - Operação Isenta da Contribuição</option>
                                                <option value="08" <?= $produto['cst_pis_padrao'] == '08' ? 'selected' : '' ?>>08 - Operação sem Incidência da Contribuição</option>
                                                <option value="09" <?= $produto['cst_pis_padrao'] == '09' ? 'selected' : '' ?>>09 - Operação com Suspensão da Contribuição</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" 
                                                   value="<?= $produto['aliquota_pis_padrao'] ?? '0.00' ?>"
                                                   data-field="aliquota_pis_padrao" data-produto="<?= $produto['id'] ?>"
                                                   step="0.01" min="0" max="100">
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" 
                                                    data-field="cst_cofins_padrao" data-produto="<?= $produto['id'] ?>">
                                                <option value="">Selecione</option>
                                                <option value="01" <?= $produto['cst_cofins_padrao'] == '01' ? 'selected' : '' ?>>01 - Operação Tributável com Alíquota Básica</option>
                                                <option value="02" <?= $produto['cst_cofins_padrao'] == '02' ? 'selected' : '' ?>>02 - Operação Tributável com Alíquota por Unidade de Medida de Produto</option>
                                                <option value="03" <?= $produto['cst_cofins_padrao'] == '03' ? 'selected' : '' ?>>03 - Operação Tributável com Alíquota por Unidade de Medida de Produto</option>
                                                <option value="04" <?= $produto['cst_cofins_padrao'] == '04' ? 'selected' : '' ?>>04 - Operação Tributável Monofásica - Revenda a Alíquota Zero</option>
                                                <option value="05" <?= $produto['cst_cofins_padrao'] == '05' ? 'selected' : '' ?>>05 - Operação Tributável por Substituição Tributária</option>
                                                <option value="06" <?= $produto['cst_cofins_padrao'] == '06' ? 'selected' : '' ?>>06 - Operação Tributável a Alíquota Zero</option>
                                                <option value="07" <?= $produto['cst_cofins_padrao'] == '07' ? 'selected' : '' ?>>07 - Operação Isenta da Contribuição</option>
                                                <option value="08" <?= $produto['cst_cofins_padrao'] == '08' ? 'selected' : '' ?>>08 - Operação sem Incidência da Contribuição</option>
                                                <option value="09" <?= $produto['cst_cofins_padrao'] == '09' ? 'selected' : '' ?>>09 - Operação com Suspensão da Contribuição</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" 
                                                   value="<?= $produto['aliquota_cofins_padrao'] ?? '0.00' ?>"
                                                   data-field="aliquota_cofins_padrao" data-produto="<?= $produto['id'] ?>"
                                                   step="0.01" min="0" max="100">
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="salvarProduto(<?= $produto['id'] ?>)">
                                                <i class="fa-solid fa-save"></i>
                                            </button>
                                        </td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function salvarProduto(produtoId) {
            const row = document.getElementById(`produto-${produtoId}`);
            const inputs = row.querySelectorAll('input, select');
            
            const dados = {
                produto_id: produtoId
            };
            
            inputs.forEach(input => {
                const field = input.getAttribute('data-field');
                if (field) {
                    dados[field] = input.value;
                }
            });
            
            try {
                const response = await fetch('api/api_impostos.php?acao=salvar_config_produto', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dados)
                });
                
                const resultado = await response.json();
                
                if (resultado.sucesso) {
                    // Feedback visual de sucesso
                    row.style.backgroundColor = '#d4edda';
                    setTimeout(() => {
                        row.style.backgroundColor = '';
                    }, 2000);
                } else {
                    alert('Erro ao salvar: ' + resultado.erro);
                }
            } catch (error) {
                alert('Erro de conexão: ' + error.message);
            }
        }
        
        async function salvarTodasConfiguracoes() {
            const produtos = document.querySelectorAll('[id^="produto-"]');
            let sucessos = 0;
            let erros = 0;
            
            for (const row of produtos) {
                const produtoId = row.id.replace('produto-', '');
                const inputs = row.querySelectorAll('input, select');
                
                const dados = {
                    produto_id: produtoId
                };
                
                inputs.forEach(input => {
                    const field = input.getAttribute('data-field');
                    if (field) {
                        dados[field] = input.value;
                    }
                });
                
                try {
                    const response = await fetch('api/api_impostos.php?acao=salvar_config_produto', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(dados)
                    });
                    
                    const resultado = await response.json();
                    
                    if (resultado.sucesso) {
                        sucessos++;
                        row.style.backgroundColor = '#d4edda';
                    } else {
                        erros++;
                        row.style.backgroundColor = '#f8d7da';
                    }
                } catch (error) {
                    erros++;
                    row.style.backgroundColor = '#f8d7da';
                }
            }
            
            alert(`Configurações salvas: ${sucessos} sucessos, ${erros} erros`);
            
            // Limpar cores após 3 segundos
            setTimeout(() => {
                produtos.forEach(row => {
                    row.style.backgroundColor = '';
                });
            }, 3000);
        }
    </script>
</body>
</html>