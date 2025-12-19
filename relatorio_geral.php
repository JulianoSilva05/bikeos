<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Relatório Geral'); ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Bibliotecas para exportação -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <style>
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-set {
            display: none;
        }

        .filter-set.active {
            display: contents;
        }

        /* Usa contents para se integrar ao grid pai */
        .relatorio-resumo {
            margin-top: 2rem;
            padding: 1rem;
            background-color: #e9ecef;
            border-radius: var(--raio-borda);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Estilos para indicadores contábeis */
        .indicadores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .indicador {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            text-align: center;
        }
        
        .indicador h4 {
            margin: 0 0 0.5rem 0;
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .indicador .valor {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
        
        .indicador .valor.negativo {
            color: #dc3545;
        }
        
        .valor-total-container {
            margin: 15px 0;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .valor-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .valor-total .label {
            font-size: 16px;
            font-weight: 600;
            color: white;
        }
        
        .valor-total .valor {
            font-size: 24px;
            font-weight: bold;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .export-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        
        .export-buttons .btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        /* Responsividade para indicadores */
        @media (max-width: 768px) {
            .indicadores-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            
            .export-buttons {
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php"><img src="assets/img/images.jpeg" alt="Logo" class="navbar-logo"></a>
        <div class="navbar-user-info"><i class="fa-solid fa-user-circle"></i><span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></div>
        <a href="logout.php" class="btn btn-destaque"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
    </nav>
    <main class="container">
        <div class="page-navigation">
            <button onclick="history.back()" class="btn btn-secundario" title="Voltar"><i class="fa-solid fa-arrow-left"></i> Voltar</button>
            <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Dashboard"><i class="fa-solid fa-house"></i></a>
        </div>
        <div class="page-header">
            <h1><i class="fa-solid fa-magnifying-glass-chart"></i> Relatório Geral</h1>
        </div>

        <div class="card">
            <h2><i class="fa-solid fa-filter"></i> Filtros do Relatório</h2>
            <form id="form-filtros">
                <div class="filter-grid">
                    <div class="form-group">
                        <label for="tipo-relatorio" class="form-label">Tipo de Relatório</label>
                        <select id="tipo-relatorio" class="form-input">
                            <option value="vendas">Relatório de Vendas</option>
                            <option value="os">Relatório de Ordens de Serviço</option>
                            <option value="financeiro">Relatório Financeiro</option>
                            <option value="dre">DRE - Demonstração do Resultado</option>
                            <option value="fluxo_caixa">Fluxo de Caixa</option>
                            <option value="balancete">Balancete de Verificação</option>
                        </select>
                    </div>
                    
                    <!-- Filtros de Período Contábil -->
                    <div class="form-group">
                        <label for="periodo-tipo" class="form-label">Período Contábil</label>
                        <select id="periodo-tipo" class="form-input">
                            <option value="personalizado">Período Personalizado</option>
                            <option value="mensal">Mensal</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="semestral">Semestral</option>
                            <option value="anual">Anual</option>
                            <option value="exercicio">Exercício Fiscal</option>
                        </select>
                    </div>
                    
                    <div id="periodo-personalizado" class="form-group">
                        <label for="data-inicio" class="form-label">Data Início</label>
                        <input type="date" id="data-inicio" name="data_inicio" class="form-input">
                    </div>
                    
                    <div id="periodo-personalizado-fim" class="form-group">
                        <label for="data-fim" class="form-label">Data Fim</label>
                        <input type="date" id="data-fim" name="data_fim" class="form-input">
                    </div>
                    
                    <div id="periodo-mes" class="form-group" style="display:none;">
                        <label for="mes-ano" class="form-label">Mês/Ano</label>
                        <input type="month" id="mes-ano" class="form-input">
                    </div>
                    
                    <div id="periodo-trimestre" class="form-group" style="display:none;">
                        <label for="trimestre" class="form-label">Trimestre</label>
                        <select id="trimestre" class="form-input">
                            <option value="1">1º Trimestre (Jan-Mar)</option>
                            <option value="2">2º Trimestre (Abr-Jun)</option>
                            <option value="3">3º Trimestre (Jul-Set)</option>
                            <option value="4">4º Trimestre (Out-Dez)</option>
                        </select>
                    </div>
                    
                    <div id="periodo-semestre" class="form-group" style="display:none;">
                        <label for="semestre" class="form-label">Semestre</label>
                        <select id="semestre" class="form-input">
                            <option value="1">1º Semestre (Jan-Jun)</option>
                            <option value="2">2º Semestre (Jul-Dez)</option>
                        </select>
                    </div>
                    
                    <div id="periodo-ano" class="form-group" style="display:none;">
                        <label for="ano" class="form-label">Ano</label>
                        <select id="ano" class="form-input">
                            <!-- Será preenchido via JavaScript -->
                        </select>
                    </div>
                    
                    <!-- Filtros por Tipo de Operação -->
                    <div class="form-group">
                        <label for="tipo-operacao" class="form-label">Tipo de Operação</label>
                        <select id="tipo-operacao" class="form-input">
                            <option value="">Todas</option>
                            <option value="receita">Receitas</option>
                            <option value="despesa">Despesas</option>
                            <option value="imposto">Impostos</option>
                            <option value="investimento">Investimentos</option>
                        </select>
                    </div>
                    
                    <!-- Filtros de Conformidade Fiscal -->
                    <div class="form-group">
                        <label for="documento-fiscal" class="form-label">Documento Fiscal</label>
                        <select id="documento-fiscal" class="form-input">
                            <option value="">Todos</option>
                            <option value="cpf">CPF</option>
                            <option value="cnpj">CNPJ</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="numero-documento" class="form-label">Número do Documento</label>
                        <input type="text" id="numero-documento" class="form-input" placeholder="CPF/CNPJ do cliente">
                    </div>
                    
                    <div class="form-group">
                        <label for="natureza-operacao" class="form-label">Natureza da Operação</label>
                        <select id="natureza-operacao" class="form-input">
                            <option value="">Todas</option>
                            <option value="venda_mercadoria">Venda de Mercadoria</option>
                            <option value="prestacao_servico">Prestação de Serviço</option>
                            <option value="venda_servico">Venda + Serviço</option>
                            <option value="devolucao">Devolução</option>
                            <option value="garantia">Garantia</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="regime-tributario" class="form-label">Regime Tributário</label>
                        <select id="regime-tributario" class="form-input">
                            <option value="">Todos</option>
                            <option value="simples_nacional">Simples Nacional</option>
                            <option value="lucro_presumido">Lucro Presumido</option>
                            <option value="lucro_real">Lucro Real</option>
                            <option value="mei">MEI</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="cfop" class="form-label">CFOP</label>
                        <select id="cfop" class="form-input">
                            <option value="">Todos</option>
                            <option value="5102">5.102 - Venda de mercadoria adquirida</option>
                            <option value="5405">5.405 - Prestação de serviços</option>
                            <option value="5949">5.949 - Outra saída de mercadoria</option>
                            <option value="6102">6.102 - Venda de mercadoria (interestadual)</option>
                            <option value="6405">6.405 - Prestação de serviços (interestadual)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="situacao-tributaria" class="form-label">Situação Tributária</label>
                        <select id="situacao-tributaria" class="form-input">
                            <option value="">Todas</option>
                            <option value="00">00 - Tributada integralmente</option>
                            <option value="10">10 - Tributada com cobrança do ICMS por ST</option>
                            <option value="20">20 - Com redução de base de cálculo</option>
                            <option value="40">40 - Isenta</option>
                            <option value="41">41 - Não tributada</option>
                            <option value="60">60 - ICMS cobrado anteriormente por ST</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="cliente-id" class="form-label">Cliente</label>
                        <select id="cliente-id" class="form-input">
                            <option value="">Todos</option>
                        </select>
                    </div>

                    <div class="filter-set active" data-type="vendas">
                        <div class="form-group"><label for="vendedor-id" class="form-label">Vendedor</label><select id="vendedor-id" class="form-input">
                                <option value="">Todos</option>
                            </select></div>
                        <div class="form-group"><label for="metodo-pagamento" class="form-label">Pagamento</label><select id="metodo-pagamento" class="form-input">
                                <option value="">Todos</option>
                                <option value="DINHEIRO">Dinheiro</option>
                                <option value="PIX">PIX</option>
                                <option value="DEBITO">Débito</option>
                                <option value="CREDITO">Crédito</option>
                                <option value="CREDIARIO">Crediário</option>
                            </select></div>
                        <div class="form-group"><label for="produto-id" class="form-label">Produto Vendido</label><select id="produto-id" class="form-input">
                                <option value="">Todos</option>
                            </select></div>
                    </div>

                    <div class="filter-set" data-type="os">
                        <div class="form-group"><label for="mecanico-id" class="form-label">Mecânico</label><select id="mecanico-id" class="form-input">
                                <option value="">Todos</option>
                            </select></div>
                        <div class="form-group"><label for="status-os" class="form-label">Status da OS</label><select id="status-os" class="form-input">
                                <option value="">Todos</option>
                                <option value="ORCAMENTO">Orçamento</option>
                                <option value="AGUARDANDO_PECA">Aguardando Peça</option>
                                <option value="EM_SERVICO">Em Serviço</option>
                                <option value="PRONTA">Pronta</option>
                                <option value="FINALIZADA">Finalizada</option>
                                <option value="CANCELADA">Cancelada</option>
                            </select></div>
                        <div class="form-group"><label for="servico-id" class="form-label">Serviço Realizado</label><select id="servico-id" class="form-input">
                                <option value="">Todos</option>
                            </select></div>
                    </div>
                    
                    <!-- Filtros para Relatórios Contábeis -->
                    <div class="filter-set" data-type="financeiro">
                        <div class="form-group">
                            <label for="categoria-financeira" class="form-label">Categoria</label>
                            <select id="categoria-financeira" class="form-input">
                                <option value="">Todas</option>
                                <option value="vendas">Vendas de Produtos</option>
                                <option value="servicos">Prestação de Serviços</option>
                                <option value="despesas_operacionais">Despesas Operacionais</option>
                                <option value="impostos">Impostos e Taxas</option>
                                <option value="investimentos">Investimentos</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="centro-custo" class="form-label">Centro de Custo</label>
                            <select id="centro-custo" class="form-input">
                                <option value="">Todos</option>
                                <option value="vendas">Vendas</option>
                                <option value="servicos">Serviços</option>
                                <option value="administrativo">Administrativo</option>
                                <option value="estoque">Estoque</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-set" data-type="dre">
                        <div class="form-group">
                            <label for="nivel-dre" class="form-label">Nível de Detalhamento</label>
                            <select id="nivel-dre" class="form-input">
                                <option value="sintetico">Sintético</option>
                                <option value="analitico">Analítico</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="comparativo" class="form-label">Comparativo</label>
                            <select id="comparativo" class="form-input">
                                <option value="nao">Não</option>
                                <option value="periodo_anterior">Período Anterior</option>
                                <option value="ano_anterior">Ano Anterior</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-set" data-type="fluxo_caixa">
                        <div class="form-group">
                            <label for="tipo-fluxo" class="form-label">Tipo de Fluxo</label>
                            <select id="tipo-fluxo" class="form-input">
                                <option value="operacional">Operacional</option>
                                <option value="investimento">Investimento</option>
                                <option value="financiamento">Financiamento</option>
                                <option value="todos">Todos</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="projecao" class="form-label">Incluir Projeção</label>
                            <select id="projecao" class="form-input">
                                <option value="nao">Não</option>
                                <option value="30_dias">30 dias</option>
                                <option value="60_dias">60 dias</option>
                                <option value="90_dias">90 dias</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-set" data-type="balancete">
                        <div class="form-group">
                            <label for="plano-contas" class="form-label">Plano de Contas</label>
                            <select id="plano-contas" class="form-input">
                                <option value="completo">Completo</option>
                                <option value="ativo">Apenas Ativo</option>
                                <option value="passivo">Apenas Passivo</option>
                                <option value="resultado">Apenas Resultado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="saldos-zerados" class="form-label">Saldos Zerados</label>
                            <select id="saldos-zerados" class="form-input">
                                <option value="nao">Não Exibir</option>
                                <option value="sim">Exibir</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primario"><i class="fa-solid fa-play"></i> Gerar Relatório</button>
                </div>
            </form>
        </div>

        <div id="area-resultados" style="display: none;">
            <!-- Resumo Executivo -->
            <div class="card" style="margin-bottom: 1rem;">
                <h3><i class="fa-solid fa-chart-line"></i> Resumo Executivo</h3>
                <div class="relatorio-resumo">
                    <div id="resumo-relatorio"></div>
                    <div class="valor-total-container">
                        <div class="valor-total">
                            <span class="label">Valor Total:</span>
                            <span id="valor-total-display" class="valor">R$ 0,00</span>
                        </div>
                    </div>
                    <div class="export-buttons">
                        <button id="btn-exportar-csv" class="btn btn-sucesso"><i class="fa-solid fa-file-csv"></i> CSV</button>
                        <button id="btn-exportar-excel" class="btn btn-sucesso"><i class="fa-solid fa-file-excel"></i> Excel</button>
                        <button id="btn-exportar-pdf" class="btn btn-sucesso"><i class="fa-solid fa-file-pdf"></i> PDF</button>
                        <button id="btn-exportar-sped" class="btn btn-info" style="display:none;"><i class="fa-solid fa-file-code"></i> SPED</button>
                    </div>
                </div>
            </div>
            
            <!-- Indicadores Contábeis -->
            <div id="indicadores-contabeis" class="card" style="margin-bottom: 1rem; display: none;">
                <h3><i class="fa-solid fa-calculator"></i> Indicadores Contábeis</h3>
                <div class="indicadores-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="indicador">
                        <h4>Receita Bruta</h4>
                        <span id="receita-bruta" class="valor">R$ 0,00</span>
                    </div>
                    <div class="indicador">
                        <h4>Receita Líquida</h4>
                        <span id="receita-liquida" class="valor">R$ 0,00</span>
                    </div>
                    <div class="indicador">
                        <h4>Despesas Totais</h4>
                        <span id="despesas-totais" class="valor">R$ 0,00</span>
                    </div>
                    <div class="indicador">
                        <h4>Resultado Líquido</h4>
                        <span id="resultado-liquido" class="valor">R$ 0,00</span>
                    </div>
                    <div class="indicador">
                        <h4>Margem Líquida</h4>
                        <span id="margem-liquida" class="valor">0%</span>
                    </div>
                    <div class="indicador">
                        <h4>Impostos</h4>
                        <span id="total-impostos" class="valor">R$ 0,00</span>
                    </div>
                </div>
            </div>
            
            <!-- Dados Detalhados -->
            <div class="card">
                <h3><i class="fa-solid fa-table"></i> Dados Detalhados</h3>
                <div style="overflow-x:auto;">
                    <table class="tabela">
                        <thead id="tabela-resultados-head"></thead>
                        <tbody id="tabela-resultados-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <?php include '_partials/menu_admin.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const formFiltros = document.getElementById('form-filtros');
            const tipoRelatorioSelect = document.getElementById('tipo-relatorio');
            const periodoTipoSelect = document.getElementById('periodo-tipo');
            const areaResultados = document.getElementById('area-resultados');
            const hoje = new Date().toISOString().split('T')[0];
            let reportData = [];
            let reportHeaders = [];

            // Inicializar campos de data
            document.getElementById('data-inicio').value = hoje;
            document.getElementById('data-fim').value = hoje;
            
            // Preencher anos disponíveis
            const anoSelect = document.getElementById('ano');
            const anoAtual = new Date().getFullYear();
            for (let ano = anoAtual; ano >= anoAtual - 10; ano--) {
                anoSelect.add(new Option(ano, ano));
            }
            
            // Definir mês atual
            const mesAtual = hoje.substring(0, 7);
            document.getElementById('mes-ano').value = mesAtual;

            const carregarFiltros = async () => {
                try {
                    const response = await fetch('api/api_relatorios.php?acao=get_filtros');
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    const {
                        vendedores,
                        mecanicos,
                        clientes,
                        produtos,
                        servicos
                    } = resultado.dados;

                    const populateSelect = (selectId, data) => {
                        const select = document.getElementById(selectId);
                        data.forEach(item => select.add(new Option(item.nome, item.id)));
                    };

                    populateSelect('vendedor-id', vendedores);
                    populateSelect('mecanico-id', mecanicos);
                    populateSelect('cliente-id', clientes);
                    populateSelect('produto-id', produtos);
                    populateSelect('servico-id', servicos);
                } catch (error) {
                    console.error("Erro ao carregar filtros:", error);
                }
            };

            const gerarRelatorio = async () => {
                const formData = new FormData(formFiltros);
                const params = new URLSearchParams();
                for (const pair of formData) {
                    params.append(pair[0], pair[1]);
                }
                params.append('tipo_relatorio', tipoRelatorioSelect.value);

                areaResultados.style.display = 'block';
                document.getElementById('tabela-resultados-body').innerHTML = `<tr><td colspan="10" style="text-align:center;">Gerando relatório...</td></tr>`;
                document.getElementById('resumo-relatorio').innerHTML = '';

                try {
                    const response = await fetch(`api/api_relatorios.php?acao=gerar_relatorio&${params.toString()}`);
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    renderizarResultados(resultado.dados, resultado.resumo, tipoRelatorioSelect.value);
                } catch (error) {
                    document.getElementById('tabela-resultados-body').innerHTML = `<tr><td colspan="10" class="error-message">${error.message}</td></tr>`;
                }
            };

            const renderizarResultados = (dados, resumo, tipo) => {
                const thead = document.getElementById('tabela-resultados-head');
                const tbody = document.getElementById('tabela-resultados-body');
                const indicadoresContabeis = document.getElementById('indicadores-contabeis');
                reportData = dados;

                thead.innerHTML = '';
                tbody.innerHTML = '';
                
                // Resumo básico
                const dataInicio = document.getElementById('data-inicio').value;
                const dataFim = document.getElementById('data-fim').value;
                const tipoTexto = document.getElementById('tipo-relatorio').selectedOptions[0].text;
                
                document.getElementById('resumo-relatorio').innerHTML = `
                    <div><strong>Período:</strong> ${dataInicio} a ${dataFim}</div>
                    <div><strong>Tipo:</strong> ${tipoTexto}</div>
                    <div><strong>Registros:</strong> ${resumo.total_registros}</div>
                `;

                if (dados.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="10" style="text-align:center;">Nenhum resultado encontrado para os filtros selecionados.</td></tr>`;
                    indicadoresContabeis.style.display = 'none';
                    document.getElementById('valor-total-display').textContent = 'R$ 0,00';
                    return;
                }
                
                // Calcular e exibir valor total
                const valorTotal = calcularValorTotal(dados);
                document.getElementById('valor-total-display').textContent = formatarMoeda(valorTotal);
                
                // Exibir indicadores contábeis para relatórios financeiros
                if (['relatorio_financeiro', 'dre', 'fluxo_caixa', 'balancete'].includes(tipo)) {
                    const indicadores = calcularIndicadoresContabeis(dados, tipo);
                    exibirIndicadoresContabeis(indicadores);
                    indicadoresContabeis.style.display = 'block';
                    document.getElementById('btn-exportar-sped').style.display = 'inline-block';
                } else {
                    indicadoresContabeis.style.display = 'none';
                    document.getElementById('btn-exportar-sped').style.display = 'none';
                }

                const headers = Object.keys(dados[0]);
                reportHeaders = headers;
                thead.innerHTML = `<tr>${headers.map(h => `<th>${formatarNomeColuna(h)}</th>`).join('')}</tr>`;

                dados.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = headers.map(header => `<td>${formatarValorColuna(row[header], header)}</td>`).join('');
                    tbody.appendChild(tr);
                });
            };
            
            // Função para calcular valor total dos dados
            const calcularValorTotal = (dados) => {
                let total = 0;
                dados.forEach(item => {
                    // Procurar por campos que representam valores monetários
                    const valor = item.valor || item.total || item.preco || item.valor_total || 0;
                    const valorNumerico = parseFloat(valor);
                    if (!isNaN(valorNumerico)) {
                        total += valorNumerico;
                    }
                });
                return total;
            };
            
            // Função para calcular indicadores contábeis
            const calcularIndicadoresContabeis = (dados, tipo) => {
                let receitaBruta = 0;
                let receitaLiquida = 0;
                let despesasTotais = 0;
                let impostos = 0;
                
                dados.forEach(item => {
                    const valor = parseFloat(item.valor || item.total || 0);
                    const tipoOperacao = item.tipo_operacao || item.tipo || '';
                    
                    if (tipoOperacao.toLowerCase().includes('receita') || valor > 0) {
                        receitaBruta += valor;
                        receitaLiquida += valor * 0.85; // Estimativa após impostos
                    } else if (tipoOperacao.toLowerCase().includes('despesa') || valor < 0) {
                        despesasTotais += Math.abs(valor);
                    }
                    
                    if (tipoOperacao.toLowerCase().includes('imposto')) {
                        impostos += Math.abs(valor);
                    }
                });
                
                const resultadoLiquido = receitaLiquida - despesasTotais;
                const margemLiquida = receitaLiquida > 0 ? (resultadoLiquido / receitaLiquida) * 100 : 0;
                
                return {
                    receitaBruta,
                    receitaLiquida,
                    despesasTotais,
                    resultadoLiquido,
                    margemLiquida,
                    impostos
                };
            };
            
            // Função para exibir indicadores contábeis
            const exibirIndicadoresContabeis = (indicadores) => {
                document.getElementById('receita-bruta').textContent = formatarMoeda(indicadores.receitaBruta);
                document.getElementById('receita-liquida').textContent = formatarMoeda(indicadores.receitaLiquida);
                document.getElementById('despesas-totais').textContent = formatarMoeda(indicadores.despesasTotais);
                document.getElementById('total-impostos').textContent = formatarMoeda(indicadores.impostos);
                
                const resultadoElement = document.getElementById('resultado-liquido');
                resultadoElement.textContent = formatarMoeda(indicadores.resultadoLiquido);
                resultadoElement.className = indicadores.resultadoLiquido >= 0 ? 'valor' : 'valor negativo';
                
                const margemElement = document.getElementById('margem-liquida');
                margemElement.textContent = indicadores.margemLiquida.toFixed(2) + '%';
                margemElement.className = indicadores.margemLiquida >= 0 ? 'valor' : 'valor negativo';
            };
            
            // Função para formatar nomes de colunas
            const formatarNomeColuna = (nome) => {
                return nome.replace(/_/g, ' ')
                          .replace(/\b\w/g, l => l.toUpperCase())
                          .replace('Id', 'ID')
                          .replace('Cpf', 'CPF')
                          .replace('Cnpj', 'CNPJ');
            };
            
            // Função para formatar valores de colunas
            const formatarValorColuna = (valor, coluna) => {
                if (!valor) return '';
                
                // Formatar valores monetários
                if (coluna.includes('valor') || coluna.includes('preco') || coluna.includes('total')) {
                    const num = parseFloat(valor);
                    if (!isNaN(num)) {
                        return formatarMoeda(num);
                    }
                }
                
                // Formatar datas
                if (coluna.includes('data')) {
                    const data = new Date(valor);
                    if (!isNaN(data.getTime())) {
                        return data.toLocaleDateString('pt-BR');
                    }
                }
                
                return valor;
            };
            
            // Função para formatar moeda
            const formatarMoeda = (valor) => {
                return new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }).format(valor);
            };

            const exportarParaCSV = () => {
                if (reportData.length === 0) {
                    alert("Não há dados para exportar.");
                    return;
                }
                const csvRows = [];
                csvRows.push(reportHeaders.join(',')); // Adiciona o cabeçalho
                reportData.forEach(row => {
                    const values = reportHeaders.map(header => {
                        const escaped = ('' + (row[header] || '')).replace(/"/g, '""');
                        return `"${escaped}"`;
                    });
                    csvRows.push(values.join(','));
                });

                const csvString = csvRows.join('\n');
                const blob = new Blob([csvString], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', `relatorio_${tipoRelatorioSelect.value}_${hoje}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            };
            
            const exportarParaExcel = () => {
                if (reportData.length === 0) {
                    alert("Não há dados para exportar.");
                    return;
                }
                
                // Criar workbook e worksheet
                const wb = XLSX.utils.book_new();
                
                // Preparar dados para o Excel
                const wsData = [];
                
                // Adicionar cabeçalho
                wsData.push(reportHeaders.map(header => formatarNomeColuna(header)));
                
                // Adicionar dados
                reportData.forEach(row => {
                    const rowData = reportHeaders.map(header => {
                        let value = row[header] || '';
                        
                        // Converter valores monetários para números
                        if (header.includes('valor') || header.includes('preco') || header.includes('total')) {
                            const num = parseFloat(value);
                            if (!isNaN(num)) {
                                return num;
                            }
                        }
                        
                        return value;
                    });
                    wsData.push(rowData);
                });
                
                // Criar worksheet
                const ws = XLSX.utils.aoa_to_sheet(wsData);
                
                // Definir larguras das colunas
                const colWidths = reportHeaders.map(() => ({ wch: 15 }));
                ws['!cols'] = colWidths;
                
                // Adicionar worksheet ao workbook
                const sheetName = document.getElementById('tipo-relatorio').selectedOptions[0].text;
                XLSX.utils.book_append_sheet(wb, ws, sheetName);
                
                // Salvar arquivo
                const fileName = `relatorio_${tipoRelatorioSelect.value}_${hoje}.xlsx`;
                XLSX.writeFile(wb, fileName);
            };
            
            const exportarParaPDF = () => {
                if (reportData.length === 0) {
                    alert("Não há dados para exportar.");
                    return;
                }
                
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('l', 'mm', 'a4'); // Landscape para mais espaço
                
                // Configurações
                const pageWidth = doc.internal.pageSize.getWidth();
                const pageHeight = doc.internal.pageSize.getHeight();
                const margin = 10;
                
                // Título
                doc.setFontSize(16);
                doc.setFont('helvetica', 'bold');
                const titulo = document.getElementById('tipo-relatorio').selectedOptions[0].text;
                doc.text(titulo, pageWidth / 2, 20, { align: 'center' });
                
                // Período
                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                const dataInicio = document.getElementById('data-inicio').value;
                const dataFim = document.getElementById('data-fim').value;
                doc.text(`Período: ${dataInicio} a ${dataFim}`, pageWidth / 2, 30, { align: 'center' });
                
                // Preparar dados da tabela
                const tableData = reportData.map(row => {
                    return reportHeaders.map(header => {
                        let value = row[header] || '';
                        
                        // Formatar valores para exibição
                        if (header.includes('valor') || header.includes('preco') || header.includes('total')) {
                            const num = parseFloat(value);
                            if (!isNaN(num)) {
                                return formatarMoeda(num);
                            }
                        }
                        
                        if (header.includes('data')) {
                            const data = new Date(value);
                            if (!isNaN(data.getTime())) {
                                return data.toLocaleDateString('pt-BR');
                            }
                        }
                        
                        return value.toString();
                    });
                });
                
                // Cabeçalhos da tabela
                const tableHeaders = reportHeaders.map(header => formatarNomeColuna(header));
                
                // Adicionar tabela usando autoTable
                doc.autoTable({
                    head: [tableHeaders],
                    body: tableData,
                    startY: 40,
                    margin: { left: margin, right: margin },
                    styles: {
                        fontSize: 8,
                        cellPadding: 2
                    },
                    headStyles: {
                        fillColor: [41, 128, 185],
                        textColor: 255,
                        fontStyle: 'bold'
                    },
                    alternateRowStyles: {
                        fillColor: [245, 245, 245]
                    },
                    columnStyles: {
                        // Aplicar formatação específica para colunas monetárias
                        ...Object.fromEntries(
                            reportHeaders.map((header, index) => {
                                if (header.includes('valor') || header.includes('preco') || header.includes('total')) {
                                    return [index, { halign: 'right' }];
                                }
                                return [index, {}];
                            })
                        )
                    }
                });
                
                // Rodapé
                const finalY = doc.lastAutoTable.finalY || 40;
                doc.setFontSize(8);
                doc.text(`Gerado em: ${new Date().toLocaleString('pt-BR')}`, margin, pageHeight - 10);
                doc.text(`Total de registros: ${reportData.length}`, pageWidth - margin, pageHeight - 10, { align: 'right' });
                
                // Salvar PDF
                const fileName = `relatorio_${tipoRelatorioSelect.value}_${hoje}.pdf`;
                doc.save(fileName);
            };

            // Função para gerenciar períodos contábeis
            const gerenciarPeriodos = () => {
                const periodoTipo = periodoTipoSelect.value;
                
                // Ocultar todos os campos de período
                document.getElementById('periodo-personalizado').style.display = 'none';
                document.getElementById('periodo-personalizado-fim').style.display = 'none';
                document.getElementById('periodo-mes').style.display = 'none';
                document.getElementById('periodo-trimestre').style.display = 'none';
                document.getElementById('periodo-semestre').style.display = 'none';
                document.getElementById('periodo-ano').style.display = 'none';
                
                // Mostrar campos relevantes
                switch(periodoTipo) {
                    case 'personalizado':
                        document.getElementById('periodo-personalizado').style.display = 'block';
                        document.getElementById('periodo-personalizado-fim').style.display = 'block';
                        break;
                    case 'mensal':
                        document.getElementById('periodo-mes').style.display = 'block';
                        document.getElementById('periodo-ano').style.display = 'block';
                        break;
                    case 'trimestral':
                        document.getElementById('periodo-trimestre').style.display = 'block';
                        document.getElementById('periodo-ano').style.display = 'block';
                        break;
                    case 'semestral':
                        document.getElementById('periodo-semestre').style.display = 'block';
                        document.getElementById('periodo-ano').style.display = 'block';
                        break;
                    case 'anual':
                    case 'exercicio':
                        document.getElementById('periodo-ano').style.display = 'block';
                        break;
                }
            };
            
            // Função para calcular datas baseadas no período
            const calcularDatasPeriodo = () => {
                const periodoTipo = periodoTipoSelect.value;
                const ano = document.getElementById('ano').value || new Date().getFullYear();
                
                switch(periodoTipo) {
                    case 'mensal':
                        const mesAno = document.getElementById('mes-ano').value;
                        if (mesAno) {
                            const [anoMes, mes] = mesAno.split('-');
                            const ultimoDia = new Date(anoMes, mes, 0).getDate();
                            document.getElementById('data-inicio').value = `${anoMes}-${mes}-01`;
                            document.getElementById('data-fim').value = `${anoMes}-${mes}-${ultimoDia.toString().padStart(2, '0')}`;
                        }
                        break;
                    case 'trimestral':
                        const trimestre = document.getElementById('trimestre').value;
                        const trimestres = {
                            1: { inicio: '01-01', fim: '03-31' },
                            2: { inicio: '04-01', fim: '06-30' },
                            3: { inicio: '07-01', fim: '09-30' },
                            4: { inicio: '10-01', fim: '12-31' }
                        };
                        const dadosTrimestre = trimestres[trimestre];
                        if (dadosTrimestre) {
                            document.getElementById('data-inicio').value = `${ano}-${dadosTrimestre.inicio}`;
                            document.getElementById('data-fim').value = `${ano}-${dadosTrimestre.fim}`;
                        }
                        break;
                    case 'semestral':
                        const semestre = document.getElementById('semestre').value;
                        const inicioSemestre = semestre === '1' ? '01-01' : '07-01';
                        const fimSemestre = semestre === '1' ? '06-30' : '12-31';
                        document.getElementById('data-inicio').value = `${ano}-${inicioSemestre}`;
                        document.getElementById('data-fim').value = `${ano}-${fimSemestre}`;
                        break;
                    case 'anual':
                    case 'exercicio':
                        document.getElementById('data-inicio').value = `${ano}-01-01`;
                        document.getElementById('data-fim').value = `${ano}-12-31`;
                        break;
                }
            };
            
            // Event listeners
            periodoTipoSelect.addEventListener('change', () => {
                gerenciarPeriodos();
                calcularDatasPeriodo();
            });
            
            document.getElementById('mes-ano').addEventListener('change', calcularDatasPeriodo);
            document.getElementById('trimestre').addEventListener('change', calcularDatasPeriodo);
            document.getElementById('semestre').addEventListener('change', calcularDatasPeriodo);
            document.getElementById('ano').addEventListener('change', calcularDatasPeriodo);
            
            tipoRelatorioSelect.addEventListener('change', (e) => {
                document.querySelectorAll('.filter-set').forEach(el => el.classList.remove('active'));
                const filterSet = document.querySelector(`.filter-set[data-type="${e.target.value}"]`);
                if (filterSet) {
                    filterSet.classList.add('active');
                }
            });
            
            // Inicializar períodos
            gerenciarPeriodos();

            formFiltros.addEventListener('submit', (e) => {
                e.preventDefault();
                gerarRelatorio();
            });

            document.getElementById('btn-exportar-csv').addEventListener('click', exportarParaCSV);
            document.getElementById('btn-exportar-excel').addEventListener('click', exportarParaExcel);
            document.getElementById('btn-exportar-pdf').addEventListener('click', exportarParaPDF);
            document.getElementById('btn-exportar-sped').addEventListener('click', () => {
                alert('Funcionalidade de exportação SPED em desenvolvimento');
            });

            carregarFiltros();
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>