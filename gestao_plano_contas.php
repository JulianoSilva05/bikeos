<?php
session_start();
require_once 'config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão do Plano de Contas - BikeOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .conta-nivel-1 { padding-left: 0px; font-weight: bold; }
        .conta-nivel-2 { padding-left: 20px; font-weight: 600; }
        .conta-nivel-3 { padding-left: 40px; }
        .conta-nivel-4 { padding-left: 60px; }
        .conta-nivel-5 { padding-left: 80px; }
        
        .conta-grupo { background-color: #f8f9fa; }
        .conta-subgrupo { background-color: #e9ecef; }
        .conta-analitica { background-color: #ffffff; }
        
        .tree-toggle {
            cursor: pointer;
            color: #007bff;
        }
        
        .tree-toggle:hover {
            color: #0056b3;
        }
        
        .collapsed .tree-content {
            display: none;
        }
        
        .badge-tipo {
            font-size: 0.7em;
        }
        
        .form-floating select {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }
        
        .form-floating select ~ label {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }
    </style>
</head>
<body>
    <?php include '_partials/navigation_header.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-chart-tree me-2"></i>Plano de Contas Contábil</h2>
                    <button class="btn btn-primary" onclick="abrirModalConta()">
                        <i class="fas fa-plus me-1"></i>Nova Conta
                    </button>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="filtro_busca" placeholder="Buscar...">
                                    <label for="filtro_busca">Buscar por código ou nome</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select" id="filtro_tipo">
                                        <option value="">Todos os tipos</option>
                                        <option value="ATIVO">Ativo</option>
                                        <option value="PASSIVO">Passivo</option>
                                        <option value="PATRIMONIO_LIQUIDO">Patrimônio Líquido</option>
                                        <option value="RECEITA">Receita</option>
                                        <option value="DESPESA">Despesa</option>
                                    </select>
                                    <label for="filtro_tipo">Tipo de conta</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select" id="filtro_nivel">
                                        <option value="">Todos os níveis</option>
                                        <option value="1">Nível 1 (Grupos)</option>
                                        <option value="2">Nível 2 (Subgrupos)</option>
                                        <option value="3">Nível 3 (Contas)</option>
                                        <option value="4">Nível 4 (Analíticas)</option>
                                    </select>
                                    <label for="filtro_nivel">Nível hierárquico</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary w-100 h-100" onclick="aplicarFiltros()">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Contas -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="15%">Código</th>
                                        <th width="35%">Nome da Conta</th>
                                        <th width="12%">Tipo</th>
                                        <th width="10%">Natureza</th>
                                        <th width="8%">Nível</th>
                                        <th width="10%">Lançamento</th>
                                        <th width="10%">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tabela_contas">
                                    <!-- Contas serão carregadas aqui -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Conta -->
    <div class="modal fade" id="modalConta" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalContaTitle">Nova Conta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formConta">
                    <div class="modal-body">
                        <input type="hidden" id="conta_id" name="id">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="conta_codigo" name="codigo" required>
                                    <label for="conta_codigo">Código da Conta *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="conta_pai_id" name="conta_pai_id">
                                        <option value="">Nenhuma (Conta Raiz)</option>
                                    </select>
                                    <label for="conta_pai_id">Conta Pai</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="conta_nome" name="nome" required>
                                    <label for="conta_nome">Nome da Conta *</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="conta_tipo" name="tipo" required>
                                        <option value="">Selecione...</option>
                                        <option value="ATIVO">Ativo</option>
                                        <option value="PASSIVO">Passivo</option>
                                        <option value="PATRIMONIO_LIQUIDO">Patrimônio Líquido</option>
                                        <option value="RECEITA">Receita</option>
                                        <option value="DESPESA">Despesa</option>
                                    </select>
                                    <label for="conta_tipo">Tipo *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="conta_natureza" name="natureza" required>
                                        <option value="">Selecione...</option>
                                        <option value="DEBITO">Débito</option>
                                        <option value="CREDITO">Crédito</option>
                                    </select>
                                    <label for="conta_natureza">Natureza *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="conta_subtipo" name="subtipo">
                                    <label for="conta_subtipo">Subtipo</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" id="conta_aceita_lancamento" name="aceita_lancamento" checked>
                                    <label class="form-check-label" for="conta_aceita_lancamento">
                                        Aceita Lançamentos
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="conta_status" name="status">
                                        <option value="ATIVO">Ativo</option>
                                        <option value="INATIVO">Inativo</option>
                                    </select>
                                    <label for="conta_status">Status</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campos Fiscais -->
                        <hr class="mt-4">
                        <h6 class="text-muted mb-3">Informações Fiscais (Opcional)</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="conta_classificacao_fiscal" name="classificacao_fiscal">
                                    <label for="conta_classificacao_fiscal">Classificação Fiscal</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="conta_cfop_padrao" name="cfop_padrao">
                                    <label for="conta_cfop_padrao">CFOP Padrão</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="conta_ncm_padrao" name="ncm_padrao">
                                    <label for="conta_ncm_padrao">NCM Padrão</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="conta_cst_padrao" name="cst_padrao">
                                    <label for="conta_cst_padrao">CST Padrão</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let contas = [];
        let modalConta;

        document.addEventListener('DOMContentLoaded', function() {
            modalConta = new bootstrap.Modal(document.getElementById('modalConta'));
            carregarContas();
            carregarContasPai();
            
            // Event listeners para filtros
            document.getElementById('filtro_busca').addEventListener('input', aplicarFiltros);
            document.getElementById('filtro_tipo').addEventListener('change', aplicarFiltros);
            document.getElementById('filtro_nivel').addEventListener('change', aplicarFiltros);
        });

        async function carregarContas() {
            try {
                const response = await fetch('api/api_plano_contas.php?acao=listar');
                const result = await response.json();
                
                if (result.success) {
                    contas = result.data;
                    exibirContas(contas);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erro ao carregar contas:', error);
                alert('Erro ao carregar contas: ' + error.message);
            }
        }

        async function carregarContasPai() {
            try {
                const response = await fetch('api/api_plano_contas.php?acao=listar');
                const result = await response.json();
                
                if (result.success) {
                    const select = document.getElementById('conta_pai_id');
                    select.innerHTML = '<option value="">Nenhuma (Conta Raiz)</option>';
                    
                    result.data.forEach(conta => {
                        const option = document.createElement('option');
                        option.value = conta.id;
                        option.textContent = `${conta.codigo} - ${conta.nome}`;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Erro ao carregar contas pai:', error);
            }
        }

        function exibirContas(contasParaExibir) {
            const tbody = document.getElementById('tabela_contas');
            tbody.innerHTML = '';
            
            contasParaExibir.forEach(conta => {
                const tr = document.createElement('tr');
                tr.className = getClasseNivel(conta.nivel);
                
                const badgeTipo = getBadgeTipo(conta.tipo);
                const badgeNatureza = getBadgeNatureza(conta.natureza);
                const aceitaLancamento = conta.aceita_lancamento ? 
                    '<span class="badge bg-success">Sim</span>' : 
                    '<span class="badge bg-secondary">Não</span>';
                
                tr.innerHTML = `
                    <td>
                        <span class="conta-nivel-${conta.nivel}">
                            ${conta.codigo}
                        </span>
                    </td>
                    <td>
                        <span class="conta-nivel-${conta.nivel}">
                            ${conta.nome}
                        </span>
                    </td>
                    <td>${badgeTipo}</td>
                    <td>${badgeNatureza}</td>
                    <td><span class="badge bg-info">${conta.nivel}</span></td>
                    <td>${aceitaLancamento}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editarConta(${conta.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="excluirConta(${conta.id})" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                
                tbody.appendChild(tr);
            });
        }

        function getClasseNivel(nivel) {
            switch(nivel) {
                case 1: return 'conta-grupo';
                case 2: return 'conta-subgrupo';
                default: return 'conta-analitica';
            }
        }

        function getBadgeTipo(tipo) {
            const cores = {
                'ATIVO': 'bg-primary',
                'PASSIVO': 'bg-danger',
                'PATRIMONIO_LIQUIDO': 'bg-warning',
                'RECEITA': 'bg-success',
                'DESPESA': 'bg-info'
            };
            return `<span class="badge ${cores[tipo]} badge-tipo">${tipo.replace('_', ' ')}</span>`;
        }

        function getBadgeNatureza(natureza) {
            const cor = natureza === 'DEBITO' ? 'bg-primary' : 'bg-success';
            return `<span class="badge ${cor} badge-tipo">${natureza}</span>`;
        }

        function aplicarFiltros() {
            const busca = document.getElementById('filtro_busca').value.toLowerCase();
            const tipo = document.getElementById('filtro_tipo').value;
            const nivel = document.getElementById('filtro_nivel').value;
            
            let contasFiltradas = contas.filter(conta => {
                const matchBusca = !busca || 
                    conta.codigo.toLowerCase().includes(busca) || 
                    conta.nome.toLowerCase().includes(busca);
                
                const matchTipo = !tipo || conta.tipo === tipo;
                const matchNivel = !nivel || conta.nivel.toString() === nivel;
                
                return matchBusca && matchTipo && matchNivel;
            });
            
            exibirContas(contasFiltradas);
        }

        function abrirModalConta(id = null) {
            document.getElementById('formConta').reset();
            document.getElementById('modalContaTitle').textContent = id ? 'Editar Conta' : 'Nova Conta';
            
            if (id) {
                carregarDadosConta(id);
            }
            
            modalConta.show();
        }

        async function carregarDadosConta(id) {
            try {
                const response = await fetch(`api/api_plano_contas.php?acao=buscar_por_id&id=${id}`);
                const result = await response.json();
                
                if (result.success) {
                    const conta = result.data;
                    
                    document.getElementById('conta_id').value = conta.id;
                    document.getElementById('conta_codigo').value = conta.codigo;
                    document.getElementById('conta_nome').value = conta.nome;
                    document.getElementById('conta_tipo').value = conta.tipo;
                    document.getElementById('conta_natureza').value = conta.natureza;
                    document.getElementById('conta_subtipo').value = conta.subtipo || '';
                    document.getElementById('conta_pai_id').value = conta.conta_pai_id || '';
                    document.getElementById('conta_aceita_lancamento').checked = conta.aceita_lancamento;
                    document.getElementById('conta_status').value = conta.status;
                    document.getElementById('conta_classificacao_fiscal').value = conta.classificacao_fiscal || '';
                    document.getElementById('conta_cfop_padrao').value = conta.cfop_padrao || '';
                    document.getElementById('conta_ncm_padrao').value = conta.ncm_padrao || '';
                    document.getElementById('conta_cst_padrao').value = conta.cst_padrao || '';
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erro ao carregar dados da conta:', error);
                alert('Erro ao carregar dados da conta: ' + error.message);
            }
        }

        function editarConta(id) {
            abrirModalConta(id);
        }

        async function excluirConta(id) {
            if (!confirm('Tem certeza que deseja excluir esta conta?')) {
                return;
            }
            
            try {
                const response = await fetch(`api/api_plano_contas.php?acao=excluir&id=${id}`);
                const result = await response.json();
                
                if (result.success) {
                    alert('Conta excluída com sucesso!');
                    carregarContas();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erro ao excluir conta:', error);
                alert('Erro ao excluir conta: ' + error.message);
            }
        }

        // Event listener para o formulário
        document.getElementById('formConta').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const dados = Object.fromEntries(formData.entries());
            
            // Converter checkbox para boolean
            dados.aceita_lancamento = document.getElementById('conta_aceita_lancamento').checked;
            
            try {
                const response = await fetch('api/api_plano_contas.php?acao=salvar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Conta salva com sucesso!');
                    modalConta.hide();
                    carregarContas();
                    carregarContasPai();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erro ao salvar conta:', error);
                alert('Erro ao salvar conta: ' + error.message);
            }
        });
    </script>
</body>
</html>