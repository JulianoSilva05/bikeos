<?php
session_start();
require_once 'api/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$empresa_id = $_SESSION['empresa_id'];
$usuario_id = $_SESSION['user_id'];

// Buscar dados da empresa para emissão
$stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ?");
$stmt->execute([$empresa_id]);
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar vendas pendentes de emissão de NFe
$stmt = $pdo->prepare("
    SELECT 
        v.id,
        v.data_venda,
        v.valor_total,
        v.cfop,
        v.natureza_operacao,
        v.metodo_pagamento,
        c.nome as cliente_nome,
        c.documento as cpf_cnpj,
        c.email,
        c.telefone,
        c.logradouro,
        c.numero,
        c.complemento,
        c.bairro,
        c.cidade,
        c.estado,
        c.cep,
        u.nome as vendedor_nome
    FROM vendas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    LEFT JOIN usuarios u ON v.vendedor_id = u.id
    WHERE v.empresa_id = ? 
        AND v.status = 'ATIVA'
        AND v.nfe_numero IS NULL
    ORDER BY v.data_venda DESC
    LIMIT 50
");
$stmt->execute([$empresa_id]);
$vendas_pendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar NFes já emitidas
$stmt = $pdo->prepare("
    SELECT 
        v.id,
        v.data_venda,
        v.valor_total,
        v.nfe_numero,
        v.nfe_chave,
        v.nfe_status,
        v.nfe_data_emissao,
        c.nome as cliente_nome
    FROM vendas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    WHERE v.empresa_id = ? 
        AND v.nfe_numero IS NOT NULL
    ORDER BY v.nfe_data_emissao DESC
    LIMIT 50
");
$stmt->execute([$empresa_id]);
$nfes_emitidas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emissão de NFe - BikeOS</title>
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
                            <i class="fa-solid fa-file-invoice me-2"></i>
                            Emissão de NFe/NFCe
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-info" onclick="configurarCertificado()">
                                <i class="fa-solid fa-certificate me-2"></i>
                                Configurar Certificado
                            </button>
                            <button class="btn btn-warning" onclick="testarConexaoSefaz()">
                                <i class="fa-solid fa-wifi me-2"></i>
                                Testar SEFAZ
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Alertas de Configuração -->
                        <?php if (empty($empresa['cnpj']) || empty($empresa['inscricao_estadual'])): ?>
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            <strong>Atenção:</strong> Para emitir NFe/NFCe é necessário configurar o CNPJ e Inscrição Estadual da empresa.
                            <a href="funcionalidades.php" class="btn btn-sm btn-warning ms-2">Configurar Agora</a>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Abas -->
                        <ul class="nav nav-tabs" id="nfeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pendentes-tab" data-bs-toggle="tab" data-bs-target="#pendentes" type="button" role="tab">
                                    <i class="fa-solid fa-clock me-2"></i>
                                    Vendas Pendentes (<?= count($vendas_pendentes) ?>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="emitidas-tab" data-bs-toggle="tab" data-bs-target="#emitidas" type="button" role="tab">
                                    <i class="fa-solid fa-check-circle me-2"></i>
                                    NFes Emitidas (<?= count($nfes_emitidas) ?>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="configuracoes-tab" data-bs-toggle="tab" data-bs-target="#configuracoes" type="button" role="tab">
                                    <i class="fa-solid fa-cog me-2"></i>
                                    Configurações
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="nfeTabsContent">
                            <!-- Aba Vendas Pendentes -->
                            <div class="tab-pane fade show active" id="pendentes" role="tabpanel">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Data Venda</th>
                                                <th>Cliente</th>
                                                <th>CPF/CNPJ</th>
                                                <th>CFOP</th>
                                                <th>Valor Total</th>
                                                <th>Tipo NFe</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($vendas_pendentes as $venda): ?>
                                            <tr>
                                                <td><?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></td>
                                                <td><?= htmlspecialchars($venda['cliente_nome'] ?? 'Cliente Avulso') ?></td>
                                                <td><?= htmlspecialchars($venda['cpf_cnpj'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($venda['cfop']) ?></td>
                                                <td>R$ <?= number_format($venda['valor_total'], 2, ',', '.') ?></td>
                                                <td>
                                                    <?php if (empty($venda['cpf_cnpj']) || strlen($venda['cpf_cnpj']) == 11): ?>
                                                        <span class="badge bg-info">NFCe</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary">NFe</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-success" onclick="emitirNFe(<?= $venda['id'] ?>, 'nfce')">
                                                            <i class="fa-solid fa-file-plus me-1"></i>
                                                            NFCe
                                                        </button>
                                                        <button class="btn btn-sm btn-primary" onclick="emitirNFe(<?= $venda['id'] ?>, 'nfe')">
                                                            <i class="fa-solid fa-file-invoice me-1"></i>
                                                            NFe
                                                        </button>
                                                        <button class="btn btn-sm btn-info" onclick="visualizarVenda(<?= $venda['id'] ?>)">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Aba NFes Emitidas -->
                            <div class="tab-pane fade" id="emitidas" role="tabpanel">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Número NFe</th>
                                                <th>Data Emissão</th>
                                                <th>Cliente</th>
                                                <th>Valor</th>
                                                <th>Status</th>
                                                <th>Chave de Acesso</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($nfes_emitidas as $nfe): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($nfe['nfe_numero']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($nfe['nfe_data_emissao'])) ?></td>
                                                <td><?= htmlspecialchars($nfe['cliente_nome'] ?? 'Cliente Avulso') ?></td>
                                                <td>R$ <?= number_format($nfe['valor_total'], 2, ',', '.') ?></td>
                                                <td>
                                                    <?php 
                                                    $status_class = [
                                                        'AUTORIZADA' => 'bg-success',
                                                        'REJEITADA' => 'bg-danger',
                                                        'PENDENTE' => 'bg-warning',
                                                        'CANCELADA' => 'bg-secondary'
                                                    ];
                                                    $class = $status_class[$nfe['nfe_status']] ?? 'bg-secondary';
                                                    ?>
                                                    <span class="badge <?= $class ?>"><?= htmlspecialchars($nfe['nfe_status']) ?></span>
                                                </td>
                                                <td>
                                                    <small class="font-monospace"><?= htmlspecialchars($nfe['nfe_chave']) ?></small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-info" onclick="baixarXML(<?= $nfe['id'] ?>)">
                                                            <i class="fa-solid fa-download me-1"></i>
                                                            XML
                                                        </button>
                                                        <button class="btn btn-sm btn-primary" onclick="imprimirDANFE(<?= $nfe['id'] ?>)">
                                                            <i class="fa-solid fa-print me-1"></i>
                                                            DANFE
                                                        </button>
                                                        <?php if ($nfe['nfe_status'] == 'AUTORIZADA'): ?>
                                                        <button class="btn btn-sm btn-danger" onclick="cancelarNFe(<?= $nfe['id'] ?>)">
                                                            <i class="fa-solid fa-times me-1"></i>
                                                            Cancelar
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Aba Configurações -->
                            <div class="tab-pane fade" id="configuracoes" role="tabpanel">
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Configurações do Certificado Digital</h6>
                                            </div>
                                            <div class="card-body">
                                                <form id="formCertificado">
                                                    <div class="mb-3">
                                                        <label for="certificado_arquivo" class="form-label">Arquivo do Certificado (.pfx)</label>
                                                        <input type="file" class="form-control" id="certificado_arquivo" accept=".pfx,.p12">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="certificado_senha" class="form-label">Senha do Certificado</label>
                                                        <input type="password" class="form-control" id="certificado_senha">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="ambiente" class="form-label">Ambiente</label>
                                                        <select class="form-select" id="ambiente">
                                                            <option value="2">Homologação</option>
                                                            <option value="1">Produção</option>
                                                        </select>
                                                    </div>
                                                    <button type="button" class="btn btn-primary" onclick="salvarCertificado()">
                                                        <i class="fa-solid fa-save me-2"></i>
                                                        Salvar Certificado
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Configurações de Numeração</h6>
                                            </div>
                                            <div class="card-body">
                                                <form id="formNumeracao">
                                                    <div class="mb-3">
                                                        <label for="serie_nfe" class="form-label">Série NFe</label>
                                                        <input type="number" class="form-control" id="serie_nfe" value="1" min="1" max="999">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="numero_nfe" class="form-label">Próximo Número NFe</label>
                                                        <input type="number" class="form-control" id="numero_nfe" value="1" min="1">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="serie_nfce" class="form-label">Série NFCe</label>
                                                        <input type="number" class="form-control" id="serie_nfce" value="1" min="1" max="999">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="numero_nfce" class="form-label">Próximo Número NFCe</label>
                                                        <input type="number" class="form-control" id="numero_nfce" value="1" min="1">
                                                    </div>
                                                    <button type="button" class="btn btn-primary" onclick="salvarNumeracao()">
                                                        <i class="fa-solid fa-save me-2"></i>
                                                        Salvar Numeração
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Visualização de Venda -->
    <div class="modal fade" id="modalVisualizarVenda" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes da Venda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="conteudoVenda">
                    <!-- Conteúdo carregado via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function emitirNFe(vendaId, tipo) {
            if (!confirm(`Confirma a emissão da ${tipo.toUpperCase()} para esta venda?`)) {
                return;
            }
            
            try {
                const response = await fetch('api/api_nfe.php?acao=emitir', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ venda_id: vendaId, tipo: tipo })
                });
                
                const resultado = await response.json();
                
                if (resultado.sucesso) {
                    alert('NFe emitida com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao emitir NFe: ' + resultado.erro);
                }
            } catch (error) {
                alert('Erro de conexão: ' + error.message);
            }
        }
        
        async function visualizarVenda(vendaId) {
            try {
                const response = await fetch(`api/api_vendas.php?acao=detalhes_venda&venda_id=${vendaId}`);
                const resultado = await response.json();
                
                if (resultado.sucesso) {
                    document.getElementById('conteudoVenda').innerHTML = resultado.html;
                    new bootstrap.Modal(document.getElementById('modalVisualizarVenda')).show();
                } else {
                    alert('Erro ao carregar detalhes: ' + resultado.erro);
                }
            } catch (error) {
                alert('Erro de conexão: ' + error.message);
            }
        }
        
        async function testarConexaoSefaz() {
            try {
                const response = await fetch('api/api_nfe.php?acao=testar_sefaz');
                const resultado = await response.json();
                
                if (resultado.sucesso) {
                    alert('Conexão com SEFAZ OK!');
                } else {
                    alert('Erro na conexão: ' + resultado.erro);
                }
            } catch (error) {
                alert('Erro de conexão: ' + error.message);
            }
        }
        
        function salvarCertificado() {
            alert('Funcionalidade de certificado em desenvolvimento. Requer integração com biblioteca NFe.');
        }
        
        function salvarNumeracao() {
            alert('Funcionalidade de numeração em desenvolvimento.');
        }
        
        function baixarXML(vendaId) {
            window.open(`api/api_nfe.php?acao=baixar_xml&venda_id=${vendaId}`, '_blank');
        }
        
        function imprimirDANFE(vendaId) {
            window.open(`api/api_nfe.php?acao=imprimir_danfe&venda_id=${vendaId}`, '_blank');
        }
        
        function cancelarNFe(vendaId) {
            const motivo = prompt('Informe o motivo do cancelamento:');
            if (!motivo) return;
            
            // Implementar cancelamento
            alert('Funcionalidade de cancelamento em desenvolvimento.');
        }
        
        // Fechar automaticamente o modal do menu administrativo ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const adminModal = document.getElementById('adminModal');
            if (adminModal) {
                const modalInstance = bootstrap.Modal.getInstance(adminModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
                // Remover backdrop se existir
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                // Remover classe modal-open do body
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        });
    </script>
</body>
</html>