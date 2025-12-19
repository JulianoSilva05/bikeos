<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php 
        require_once 'helpers/empresa_helper.php';
        echo getTituloComEmpresa('Financeiro');
    ?></title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .tabs {
            display: flex;
            border-bottom: 2px solid var(--cor-borda);
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tab-link {
            padding: 1rem 1.5rem;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--cor-texto-secundario);
        }

        .tab-link.active {
            border-bottom: 3px solid var(--cor-primaria);
            font-weight: 700;
            color: var(--cor-primaria);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s;
        }

        .tab-content.active {
            display: block;
        }

        .caixa-status {
            padding: 2rem;
            border-radius: var(--raio-borda);
            text-align: center;
            margin-bottom: 2rem;
        }

        .caixa-fechado {
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
        }

        .caixa-aberto {
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
        }

        .filter-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            align-items: flex-end;
            margin-bottom: 1.5rem;
        }

        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
            margin-top: 2rem;
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
        }
        
        #movimentacoes-lista table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            table-layout: fixed;
        }
        
        #movimentacoes-lista td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
            word-wrap: break-word;
        }
        
        #movimentacoes-lista th {
            padding: 1rem;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            text-align: left;
            word-wrap: break-word;
        }
        
        #movimentacoes-lista th:nth-child(1) { width: 12%; } /* Data */
        #movimentacoes-lista th:nth-child(2) { width: 10%; } /* Hora */
        #movimentacoes-lista th:nth-child(3) { width: 15%; } /* Tipo */
        #movimentacoes-lista th:nth-child(4) { width: 30%; } /* Cliente/Funcionário */
        #movimentacoes-lista th:nth-child(5) { width: 18%; } /* Método */
        #movimentacoes-lista th:nth-child(6) { width: 15%; } /* Valor */
        
        #movimentacoes-lista tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        #movimentacoes-lista tbody tr:last-child td {
            border-bottom: none;
        }
        
        @media (max-width: 768px) {
            #movimentacoes-lista {
                overflow-x: auto;
            }
            
            #movimentacoes-lista table {
                min-width: 100%;
                width: 100%;
            }
            
            #movimentacoes-lista th,
            #movimentacoes-lista td {
                padding: 0.5rem;
                font-size: 0.875rem;
            }
            
            /* Ajustar larguras das colunas em mobile */
            #movimentacoes-lista th:nth-child(1) { width: 15%; } /* Data */
            #movimentacoes-lista th:nth-child(2) { width: 12%; } /* Hora */
            #movimentacoes-lista th:nth-child(3) { width: 18%; } /* Tipo */
            #movimentacoes-lista th:nth-child(4) { width: 25%; } /* Cliente/Funcionário */
            #movimentacoes-lista th:nth-child(5) { width: 15%; } /* Método */
            #movimentacoes-lista th:nth-child(6) { width: 15%; } /* Valor */
        }
        
        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
        
        .badge-info {
            color: #fff;
            background-color: #8b5cf6;
        }
        
        .badge-warning {
            color: #fff;
            background-color: #ff6b35;
        }
        
        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }

        .resumo-conferencia td.diferenca-negativa {
            color: var(--cor-erro);
            font-weight: bold;
        }

        .resumo-conferencia td.diferenca-positiva {
            color: var(--cor-sucesso);
            font-weight: bold;
        }

        .valor-restante-negativo {
            color: var(--cor-erro);
            font-weight: bold;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Estilos para status das contas */
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-vencido {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-pago {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Botões pequenos */
        .btn-pequeno {
            padding: 5px 8px;
            font-size: 12px;
            margin: 0 2px;
        }

        .btn-sucesso {
            background-color: #28a745;
            color: white;
            border: none;
        }

        .btn-sucesso:hover {
            background-color: #218838;
        }

        .btn-perigo {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn-perigo:hover {
            background-color: #c82333;
        }

        /* Resumo das contas */
        .resumo-contas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .resumo-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .resumo-item h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }

        .resumo-item .valor {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .resumo-item.pendente .valor {
            color: #856404;
        }

        .resumo-item.vencido .valor {
            color: #721c24;
        }

        .resumo-item.pago .valor {
            color: #155724;
        }

        /* Filtros */
        .filtros-contas {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .filtros-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .text-center {
            text-align: center;
        }

        /* Modal styles */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 2rem;
            border: none;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            background-color: #f8f9fa;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .modal-body {
            padding: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 15px 20px;
            border-top: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .btn-secundario {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-secundario:hover {
            background-color: #5a6268;
        }

        /* Estilos para o campo de busca */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-container input {
            padding: 0.5rem 2.5rem 0.5rem 1rem;
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            font-size: 0.9rem;
            width: 250px;
            transition: all 0.3s ease;
        }

        .search-container input:focus {
            outline: none;
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 2px rgba(var(--cor-primaria-rgb), 0.2);
        }

        .search-icon {
            position: absolute;
            right: 1rem;
            color: var(--cor-texto-secundario);
            pointer-events: none;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php"><img src="assets/img/logo.png" alt="Logo" class="navbar-logo"></a>

        <div class="navbar-user-info"><i class="fa-solid fa-user-circle"></i><span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></div>
        <a href="logout.php" class="btn btn-destaque"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
    </nav>
    <main class="container">
        <div class="page-navigation">
            <button onclick="history.back()" class="btn btn-secundario" title="Voltar"><i class="fa-solid fa-arrow-left"></i> Voltar</button>
            <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Dashboard"><i class="fa-solid fa-house"></i></a>
        </div>
        <div class="page-header">
            <h1><i class="fa-solid fa-chart-line"></i> Financeiro</h1>
            <div class="search-container">
                <input type="text" id="search-financeiro" placeholder="Buscar...">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-link active" data-tab="caixa">Caixa</button>
            <button class="tab-link" data-tab="vendas">Vendas do Período</button>
            <button class="tab-link" data-tab="crediario">Crediário</button>
            <button class="tab-link" data-tab="contas-pagar">Contas a Pagar</button>
            <button class="tab-link" data-tab="relatorios">Relatórios Gráficos</button>
        </div>

        <div id="caixa" class="tab-content active">
            <div class="card" style="width: 95%; max-width: 1400px; margin: 2rem auto; padding: 1.5rem;">
                <p>Carregando status do caixa...</p>
            </div>
        </div>
        <div id="vendas" class="tab-content">
            <div class="card" style="width: 95%; max-width: 1400px; margin: 2rem auto; padding: 1.5rem;">
                <h2>Filtrar Vendas</h2>
                <div class="filter-bar">
                    <div class="form-group"><label for="vendas-inicio">De:</label><input type="date" id="vendas-inicio" class="form-input"></div>
                    <div class="form-group"><label for="vendas-fim">Até:</label><input type="date" id="vendas-fim" class="form-input"></div>
                    <div class="form-group"><label for="vendas-cliente">Cliente:</label><input type="text" id="vendas-cliente" class="form-input" placeholder="Nome do cliente..."></div>
                    <div class="form-group"><label for="vendas-vendedor">Vendedor:</label><select id="vendas-vendedor" class="form-input">
                            <option value="">Todos</option>
                        </select></div>
                    <div class="form-group"><label for="vendas-tipo-produto">Tipo de Produto:</label><select id="vendas-tipo-produto" class="form-input">
                            <option value="">Todos</option>
                        </select></div>
                    <button id="btn-filtrar-vendas" class="btn btn-primario">Filtrar</button>
                </div>
                <div style="overflow-x:auto;">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Valor</th>
                                <th>Pagamento</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody id="vendas-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="crediario" class="tab-content">
            <div class="card" style="width: 95%; max-width: 1400px; margin: 2rem auto; padding: 1.5rem;">
                <h2>Contas a Receber (Crediário)</h2>
                <div style="overflow-x:auto;">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>Venda ID</th>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Valor Total</th>
                                <th>Valor Pago</th>
                                <th>Restante</th>
                                <th colspan="2">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="crediario-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="contas-pagar" class="tab-content">
            <div class="card" style="width: 95%; max-width: 1400px; margin: 2rem auto; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2>Contas a Pagar</h2>
                    <button id="btn-nova-conta" class="btn btn-primario">
                        <i class="fa-solid fa-plus"></i> Nova Conta
                    </button>
                </div>
                
                <div class="filter-bar">
                    <div class="form-group">
                        <label for="contas-data-inicio">Data Início:</label>
                        <input type="date" id="contas-data-inicio" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="contas-data-fim">Data Fim:</label>
                        <input type="date" id="contas-data-fim" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="contas-fornecedor">Fornecedor:</label>
                        <select id="contas-fornecedor" class="form-input">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contas-tipo">Tipo de Conta:</label>
                        <select id="contas-tipo" class="form-input">
                            <option value="">Todos</option>
                            <option value="FIXA">Contas Fixas</option>
                            <option value="VARIAVEL">Contas Variáveis</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contas-status">Status:</label>
                        <select id="contas-status" class="form-input">
                            <option value="">Todos</option>
                            <option value="PENDENTE">Pendente</option>
                            <option value="PAGO">Pago</option>
                            <option value="VENCIDO">Vencido</option>
                        </select>
                    </div>
                    <button id="btn-filtrar-contas" class="btn btn-primario">Filtrar</button>
                </div>
                
                <div style="overflow-x:auto;">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descrição</th>
                                <th>Fornecedor</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Tipo</th>
                                <th>Tipo de Pagamento</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="contas-pagar-table-body">
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 2rem;">Carregando contas...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div id="resumo-contas" style="margin-top: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="card" style="text-align: center; padding: 1rem;">
                        <h4>Total Pendente</h4>
                        <p id="total-pendente" style="font-size: 1.5rem; font-weight: bold; color: var(--cor-aviso);">R$ 0,00</p>
                    </div>
                    <div class="card" style="text-align: center; padding: 1rem;">
                        <h4>Total Vencido</h4>
                        <p id="total-vencido" style="font-size: 1.5rem; font-weight: bold; color: var(--cor-erro);">R$ 0,00</p>
                    </div>
                    <div class="card" style="text-align: center; padding: 1rem;">
                        <h4>Total Pago</h4>
                        <p id="total-pago" style="font-size: 1.5rem; font-weight: bold; color: var(--cor-sucesso);">R$ 0,00</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="relatorios" class="tab-content">
            <div class="card" style="width: 95%; max-width: 1400px; margin: 2rem auto; padding: 1.5rem;">
                <h2>Relatórios por Período</h2>
                <div class="filter-bar">
                    <div class="form-group"><label for="relatorio-inicio">De:</label><input type="date" id="relatorio-inicio" class="form-input"></div>
                    <div class="form-group"><label for="relatorio-fim">Até:</label><input type="date" id="relatorio-fim" class="form-input"></div>
                    <button id="btn-filtrar-relatorio" class="btn btn-primario">Gerar Gráficos</button>
                </div>
                <div class="dashboard-layout" style="gap: 3rem;">
                    <div>
                        <h3>Vendas por Segmento</h3>
                        <div class="chart-container"><canvas id="graficoPizza"></canvas></div>
                    </div>
                    <div>
                        <h3>Faturamento por Dia</h3>
                        <div class="chart-container"><canvas id="graficoBarra"></canvas></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="fechar-caixa-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-calculator"></i> Conferência de Fechamento</h2>
            <p>Informe os valores totais apurados para cada meio de pagamento.</p>
            <form id="form-fechar-caixa-detalhado">
                <div class="form-group">
                    <label for="total-dinheiro-informado" class="form-label">Total em Dinheiro</label>
                    <input type="number" step="0.01" id="total-dinheiro-informado" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="total-pix-informado" class="form-label">Total em PIX</label>
                    <input type="number" step="0.01" id="total-pix-informado" class="form-input" required value="0">
                </div>
                <div class="form-group">
                    <label for="total-debito-informado" class="form-label">Total em Débito</label>
                    <input type="number" step="0.01" id="total-debito-informado" class="form-input" required value="0">
                </div>
                <div class="form-group">
                    <label for="total-credito-informado" class="form-label">Total em Crédito</label>
                    <input type="number" step="0.01" id="total-credito-informado" class="form-input" required value="0">
                </div>
                <div class="form-group">
                    <label for="total-crediario-informado" class="form-label">Total em Crediário</label>
                    <input type="number" step="0.01" id="total-crediario-informado" class="form-input" required value="0">
                </div>
                <div class="form-group">
                    <label for="obs-fechamento" class="form-label">Observações</label>
                    <textarea id="obs-fechamento" class="form-input" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-destaque" style="width:100%;">Conferir Valores</button>
            </form>
        </div>
    </div>

    <div id="confirmacao-fechamento-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-clipboard-check"></i> Resumo da Conferência</h2>
            <p>Verifique as diferenças antes de fechar o caixa permanentemente.</p>
            <div id="resumo-fechamento-container" style="margin: 2rem 0;"></div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" id="btn-voltar-conferencia" class="btn btn-secundario">Voltar e Corrigir</button>
                <button type="button" id="btn-confirmar-fechamento-final" class="btn btn-destaque">Confirmar e Fechar Caixa</button>
            </div>
        </div>
    </div>

    <div id="sangria-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-money-bill-transfer"></i> Sangria do Caixa</h2>
            <p>Registre a retirada de dinheiro do caixa.</p>
            <form id="form-sangria">
                <div class="form-group">
                    <label for="valor-sangria" class="form-label">Valor da Sangria (R$)</label>
                    <input type="number" step="0.01" id="valor-sangria" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="metodo-sangria" class="form-label">Método de Pagamento</label>
                    <select id="metodo-sangria" class="form-input" readonly>
                        <option value="DINHEIRO" selected>Dinheiro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="descricao-sangria" class="form-label">Descrição/Motivo</label>
                    <textarea id="descricao-sangria" class="form-input" rows="3" placeholder="Ex: Pagamento de fornecedor, despesas operacionais, etc."></textarea>
                </div>
                <button type="submit" class="btn btn-destaque" style="width:100%;">Registrar Sangria</button>
            </form>
        </div>
    </div>

    <div id="pagamento-crediario-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2>Registrar Pagamento de Crediário</h2>
            <p>Venda #<span id="pagamento-venda-id-display"></span> | Cliente: <strong id="pagamento-cliente-nome"></strong></p>
            <p>Saldo Devedor: <strong id="pagamento-saldo-devedor"></strong></p>
            <form id="form-pagamento-crediario">
                <input type="hidden" id="pagamento-venda-id">
                <div class="form-group">
                    <label for="valor-pago" class="form-label">Valor a Pagar (R$)</label>
                    <input type="number" step="0.01" id="valor-pago" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="metodo-pagamento-parcela" class="form-label">Método de Pagamento</label>
                    <select id="metodo-pagamento-parcela" class="form-input">
                        <option value="DINHEIRO">Dinheiro</option>
                        <option value="PIX">PIX</option>
                        <option value="DEBITO">Débito</option>
                        <option value="CREDITO">Crédito</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-destaque" style="width:100%;">Confirmar Pagamento</button>
            </form>
        </div>
    </div>

    <!-- Seção de Movimentações do Caixa -->
    <div class="card" style="width: 95%; max-width: 1400px; margin: 2rem auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3><i class="fa-solid fa-list"></i> Movimentações do Caixa (desde a última abertura)</h3>
        </div>
        
        <!-- Somatório por método -->
          <div id="somatorio-movimentacoes" style="display: none; margin-bottom: 2rem;">
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem;">
                  <div style="background-color: #28a745; color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                      <h6 style="margin: 0 0 0.5rem 0; font-size: 0.9rem;">Dinheiro</h6>
                      <h4 id="total-dinheiro" style="margin: 0; font-size: 1.5rem;">R$ 0,00</h4>
                  </div>
                  <div style="background-color: #8b5cf6; color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                      <h6 style="margin: 0 0 0.5rem 0; font-size: 0.9rem;">PIX</h6>
                      <h4 id="total-pix" style="margin: 0; font-size: 1.5rem;">R$ 0,00</h4>
                  </div>
                  <div style="background-color: #dc3545; color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                      <h6 style="margin: 0 0 0.5rem 0; font-size: 0.9rem;">Cartão Crédito</h6>
                      <h4 id="total-credito" style="margin: 0; font-size: 1.5rem;">R$ 0,00</h4>
                  </div>
                  <div style="background-color: #6f42c1; color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                      <h6 style="margin: 0 0 0.5rem 0; font-size: 0.9rem;">Cartão Débito</h6>
                      <h4 id="total-debito" style="margin: 0; font-size: 1.5rem;">R$ 0,00</h4>
                  </div>
                  <div style="background-color: #fd7e14; color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                      <h6 style="margin: 0 0 0.5rem 0; font-size: 0.9rem;">Crediário</h6>
                      <h4 id="total-crediario" style="margin: 0; font-size: 1.5rem;">R$ 0,00</h4>
                  </div>
                  <div style="background-color: #3498db; color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                      <h6 style="margin: 0 0 0.5rem 0; font-size: 0.9rem;">Total Geral</h6>
                      <h4 id="total-geral" style="margin: 0; font-size: 1.5rem;">R$ 0,00</h4>
                  </div>
              </div>
          </div>
        
        <div id="movimentacoes-loading" style="text-align: center; padding: 2rem; display: none;">
            <i class="fa-solid fa-spinner fa-spin"></i> Carregando movimentações...
        </div>
        
        <div id="movimentacoes-container">
            <div id="movimentacoes-lista" class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Tipo</th>
                            <th>Cliente/Funcionário</th>
                            <th>Método</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody id="movimentacoes-tbody">
                        <!-- Movimentações serão carregadas aqui -->
                    </tbody>
                </table>
            </div>
            
            <div id="movimentacoes-vazio" style="text-align: center; padding: 2rem; color: #666; display: none;">
                <i class="fa-solid fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p>Nenhuma movimentação encontrada desde a última abertura do caixa.</p>
            </div>
        </div>
    </div>

    <?php include '_partials/menu_admin.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.tab-link');
            const contents = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(item => item.classList.remove('active'));
                    contents.forEach(item => item.classList.remove('active'));
                    tab.classList.add('active');
                    document.getElementById(tab.dataset.tab).classList.add('active');
                });
            });

            // Verificar se há hash na URL para abrir aba específica
            const hash = window.location.hash.substring(1);
            if (hash) {
                const targetTab = document.querySelector(`[data-tab="${hash}"]`);
                if (targetTab) {
                    tabs.forEach(item => item.classList.remove('active'));
                    contents.forEach(item => item.classList.remove('active'));
                    targetTab.classList.add('active');
                    document.getElementById(hash).classList.add('active');
                    
                    // Se for a aba contas-pagar, carregar os dados
                    if (hash === 'contas-pagar') {
                        setTimeout(() => {
                            carregarContasPagar();
                            carregarFornecedoresFiltro();
                        }, 100);
                    }
                }
            }

            let estadoCaixa = null;
            let graficoPizzaInstance = null;
            let graficoBarraInstance = null;
            let totaisInformadosParaSalvar = null;
            const fecharCaixaModal = document.getElementById('fechar-caixa-modal');
            const fecharCaixaForm = document.getElementById('form-fechar-caixa-detalhado');
            const confirmacaoModal = document.getElementById('confirmacao-fechamento-modal');
            const pagamentoModal = document.getElementById('pagamento-crediario-modal');
            const pagamentoForm = document.getElementById('form-pagamento-crediario');
            const sangriaModal = document.getElementById('sangria-modal');
            const sangriaForm = document.getElementById('form-sangria');
            const CHAVE_PIX = "seu-email@provedor.com ou seu CNPJ";
            const hoje = new Date().toISOString().split('T')[0];

            const carregarStatusCaixa = async () => {
                const container = document.getElementById('caixa');
                try {
                    const response = await fetch('api/api_financeiro.php?acao=get_status_caixa');
                    const resultado = await response.json();
                    estadoCaixa = resultado.sucesso ? resultado.dados : null;
                    if (estadoCaixa) {
                        const valorAtual = estadoCaixa.valor_atual_dinheiro !== undefined ? estadoCaixa.valor_atual_dinheiro : estadoCaixa.valor_inicial;
                        container.innerHTML = `<div class="caixa-status caixa-aberto"><h2>Caixa Aberto</h2><p>Aberto por <strong>${estadoCaixa.nome_usuario}</strong> em ${new Date(estadoCaixa.data_abertura).toLocaleString('pt-BR')}</p><p>Valor Inicial: <strong>R$ ${parseFloat(estadoCaixa.valor_inicial).toFixed(2).replace('.',',')}</strong></p><p>Valor Atual em Dinheiro: <strong>R$ ${parseFloat(valorAtual).toFixed(2).replace('.',',')}</strong></p></div><div class="card"><h3>Ações do Caixa</h3><div style="display: grid; gap: 10px;"><button id="btn-sangria-caixa" class="btn" style="width:100%; background-color: #007bff; color: white;">Sangria do Caixa</button><button id="btn-iniciar-fechamento" class="btn btn-erro" style="width:100%">Fechar Caixa</button></div></div>`;
                        document.getElementById('btn-iniciar-fechamento').addEventListener('click', () => {
                            fecharCaixaModal.style.display = 'flex';
                        });
                        document.getElementById('btn-sangria-caixa').addEventListener('click', () => {
                            sangriaModal.style.display = 'flex';
                        });
                    } else {
                        container.innerHTML = `<div class="caixa-status caixa-fechado"><h2>Caixa Fechado</h2><p>Nenhuma sessão de caixa ativa.</p></div><div class="card"><h3>Abrir Novo Caixa</h3><form id="form-abrir-caixa"><div class="form-group"><label class="form-label">Valor Inicial (Fundo de Troco)</label><input type="number" step="0.01" id="valor-inicial" class="form-input" required></div><button type="submit" class="btn btn-sucesso" style="width:100%">Abrir Caixa</button></form></div>`;
                        document.getElementById('form-abrir-caixa').addEventListener('submit', abrirCaixa);
                    }
                } catch (e) {
                    container.innerHTML = '<p class="error-message">Erro ao carregar o status do caixa.</p>';
                }
            };
            const abrirCaixa = async (e) => {
                e.preventDefault();
                const valor = document.getElementById('valor-inicial').value;
                try {
                    const response = await fetch('api/api_financeiro.php?acao=abrir_caixa', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            valor_inicial: valor
                        })
                    });
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    alert(resultado.mensagem);
                    carregarStatusCaixa();
                } catch (error) {
                    alert("Erro: " + error.message);
                }
            };

            fecharCaixaModal.querySelector('.modal-close').onclick = () => fecharCaixaModal.style.display = 'none';
            fecharCaixaForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = fecharCaixaForm.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Calculando...';
                try {
                    totaisInformadosParaSalvar = {
                        dinheiro: parseFloat(document.getElementById('total-dinheiro-informado').value || 0),
                        pix: parseFloat(document.getElementById('total-pix-informado').value || 0),
                        debito: parseFloat(document.getElementById('total-debito-informado').value || 0),
                        credito: parseFloat(document.getElementById('total-credito-informado').value || 0),
                        crediario: parseFloat(document.getElementById('total-crediario-informado').value || 0)
                    };
                    const response = await fetch(`api/api_financeiro.php?acao=calcular_fechamento_caixa&sessao_id=${estadoCaixa.id}`);
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    const totaisCalculados = resultado.dados;
                    const valorInicial = parseFloat(totaisCalculados.VALOR_INICIAL);
                    const resumoDiv = document.getElementById('resumo-fechamento-container');
                    let resumoHTML = '<table class="tabela resumo-conferencia"><thead><tr><th>Método</th><th>Sistema (R$)</th><th>Informado (R$)</th><th>Diferença (R$)</th></tr></thead><tbody>';
                    const addRow = (label, calculado, informado) => {
                        const diferenca = informado - calculado;
                        let classe = (diferenca < 0) ? 'diferenca-negativa' : (diferenca > 0 ? 'diferenca-positiva' : '');
                        return `<tr><td>${label}</td><td>${calculado.toFixed(2).replace('.',',')}</td><td>${informado.toFixed(2).replace('.',',')}</td><td class="${classe}">${diferenca.toFixed(2).replace('.',',')}</td></tr>`;
                    };
                    resumoHTML += addRow('Dinheiro em Caixa', valorInicial + parseFloat(totaisCalculados.DINHEIRO), totaisInformadosParaSalvar.dinheiro);
                    resumoHTML += addRow('PIX', parseFloat(totaisCalculados.PIX), totaisInformadosParaSalvar.pix);
                    resumoHTML += addRow('Débito', parseFloat(totaisCalculados.DEBITO), totaisInformadosParaSalvar.debito);
                    resumoHTML += addRow('Crédito', parseFloat(totaisCalculados.CREDITO), totaisInformadosParaSalvar.credito);
                    resumoHTML += addRow('Crediário', parseFloat(totaisCalculados.CREDIARIO), totaisInformadosParaSalvar.crediario);
                    resumoDiv.innerHTML = resumoHTML + '</tbody></table>';
                    fecharCaixaModal.style.display = 'none';
                    confirmacaoModal.style.display = 'flex';
                } catch (error) {
                    alert("Erro ao calcular fechamento: " + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Conferir Valores';
                }
            });

            document.getElementById('btn-confirmar-fechamento-final').addEventListener('click', async () => {
                const btn = document.getElementById('btn-confirmar-fechamento-final');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
                const payload = {
                    sessao_id: estadoCaixa.id,
                    totais_informados: totaisInformadosParaSalvar,
                    observacoes: document.getElementById('obs-fechamento').value
                };
                try {
                    const response = await fetch('api/api_financeiro.php?acao=fechar_caixa', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    alert(resultado.mensagem);
                    
                    // Gerar backup após fechamento bem-sucedido
                    await gerarBackupSistema();
                    
                    confirmacaoModal.style.display = 'none';
                    fecharCaixaForm.reset();
                    carregarStatusCaixa();
                } catch (error) {
                    alert("Erro ao fechar o caixa: " + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Confirmar e Fechar Caixa';
                }
            });
            document.getElementById('btn-voltar-conferencia').addEventListener('click', () => {
                confirmacaoModal.style.display = 'none';
                fecharCaixaModal.style.display = 'flex';
            });
            confirmacaoModal.querySelector('.modal-close').onclick = () => confirmacaoModal.style.display = 'none';

            // Event listeners para o modal de sangria
            sangriaModal.querySelector('.modal-close').onclick = () => sangriaModal.style.display = 'none';
            sangriaForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = sangriaForm.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processando...';
                
                const payload = {
                    valor: document.getElementById('valor-sangria').value,
                    descricao: document.getElementById('descricao-sangria').value,
                    metodo_pagamento: document.getElementById('metodo-sangria').value
                };
                
                try {
                    const response = await fetch('api/api_financeiro.php?acao=sangria_caixa', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams(payload)
                    });
                    const resultado = await response.json();
                    
                    if (!resultado.success) throw new Error(resultado.message);
                    
                    alert(resultado.message);
                    sangriaModal.style.display = 'none';
                    sangriaForm.reset();
                    carregarMovimentacoes(); // Recarregar movimentações para mostrar a sangria
                    carregarStatusCaixa(); // Atualizar status do caixa
                } catch (error) {
                    alert('Erro ao registrar sangria: ' + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Registrar Sangria';
                }
            });

            const carregarVendas = async () => {
                const inicio = document.getElementById('vendas-inicio').value;
                const fim = document.getElementById('vendas-fim').value;
                const cliente_nome = document.getElementById('vendas-cliente').value;
                const vendedor_id = document.getElementById('vendas-vendedor').value;
                const tipo_produto = document.getElementById('vendas-tipo-produto').value;
                const tableBody = document.getElementById('vendas-table-body');
                tableBody.innerHTML = '<tr><td colspan="7">Carregando...</td></tr>';
                try {
                    const response = await fetch(`api/api_financeiro.php?acao=get_relatorio_vendas&data_inicio=${inicio}&data_fim=${fim}&cliente_nome=${cliente_nome}&vendedor_id=${vendedor_id}&tipo_produto=${tipo_produto}`);
                    const resultado = await response.json();
                    tableBody.innerHTML = '';
                    if (resultado.sucesso) {
                        if (document.getElementById('vendas-vendedor').options.length <= 1 && resultado.filtros.vendedores) {
                            const filtroVendedor = document.getElementById('vendas-vendedor');
                            resultado.filtros.vendedores.forEach(v => filtroVendedor.add(new Option(v.nome, v.id)));
                            const filtroTipo = document.getElementById('vendas-tipo-produto');
                            resultado.filtros.tipos_produto.forEach(t => filtroTipo.add(new Option(t, t)));
                        }
                        if (resultado.dados.length > 0) {
                            resultado.dados.forEach(v => {
                                tableBody.innerHTML += `<tr><td>${v.id}</td><td>${new Date(v.data_venda).toLocaleString('pt-BR')}</td><td>${v.cliente_nome || 'Consumidor Final'}</td><td>${v.vendedor_nome}</td><td>R$ ${parseFloat(v.valor_total).toFixed(2).replace('.',',')}</td><td>${v.metodo_pagamento}</td><td><a href="imprimir_venda.php?id=${v.id}" target="_blank" class="btn btn-sm btn-secundario"><i class="fa-solid fa-print"></i></a></td></tr>`;
                            });
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Nenhuma venda encontrada para os filtros.</td></tr>';
                        }
                    }
                } catch (e) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="error-message">Erro ao carregar vendas.</td></tr>';
                }
            };

            const carregarCrediario = async () => {
                const tableBody = document.getElementById('crediario-table-body');
                tableBody.innerHTML = '<tr><td colspan="8">Carregando...</td></tr>';
                try {
                    const response = await fetch('api/api_financeiro.php?acao=get_vendas_crediario');
                    const resultado = await response.json();
                    tableBody.innerHTML = '';
                    if (resultado.sucesso && resultado.dados.length > 0) {
                        resultado.dados.forEach(v => {
                            const valorTotal = parseFloat(v.valor_total);
                            const valorPago = parseFloat(v.valor_pago);
                            const valorRestante = valorTotal - valorPago;
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${v.id}</td>
                                    <td>${new Date(v.data_venda).toLocaleDateString('pt-BR')}</td>
                                    <td>${v.cliente_nome}</td>
                                    <td>R$ ${valorTotal.toFixed(2).replace('.',',')}</td>
                                    <td>R$ ${valorPago.toFixed(2).replace('.',',')}</td>
                                    <td class="${valorRestante > 0 ? 'valor-restante-negativo' : ''}">R$ ${valorRestante.toFixed(2).replace('.',',')}</td>
                                    <td><button class="btn btn-sm btn-sucesso btn-registrar-pagamento" data-venda-json='${JSON.stringify(v)}'><i class="fa-solid fa-hand-holding-dollar"></i> Registrar Pagamento</button></td>
                                    <td><button class="btn btn-sm btn-secundario btn-cobrar" data-venda-json='${JSON.stringify(v)}'><i class="fa-brands fa-whatsapp"></i> Cobrar</button></td>
                                </tr>`;
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">Nenhuma conta em aberto.</td></tr>';
                    }
                } catch (e) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="error-message">Erro ao carregar crediário.</td></tr>';
                }
            };

            const enviarCobrancaWhatsApp = async (venda) => {
                if (!venda.cliente_telefone) {
                    alert("Cliente sem telefone para cobrança.");
                    return;
                }
                try {
                    const response = await fetch(`api/api_financeiro.php?acao=get_itens_venda&venda_id=${venda.id}`);
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    const nomeCliente = venda.cliente_nome.split(' ')[0];
                    const dataVenda = new Date(venda.data_venda).toLocaleDateString('pt-BR');
                    const valorRestante = (parseFloat(venda.valor_total) - parseFloat(venda.valor_pago)).toFixed(2).replace('.', ',');
                    let mensagem = `Olá, ${nomeCliente}! Passando para lembrar sobre sua compra em aberto conosco na BikeOS, do dia ${dataVenda}. O valor restante é de *R$ ${valorRestante}*.

Para facilitar, você pode pagar via PIX. Nossa chave é:
*${CHAVE_PIX}*

Obrigado!`;
                    window.open(`https://wa.me/55${venda.cliente_telefone.replace(/\D/g,'')}?text=${encodeURIComponent(mensagem)}`, '_blank');
                } catch (error) {
                    alert("Erro ao gerar mensagem: " + error.message);
                }
            };

            function abrirModalPagamento(venda) {
                const valorRestante = parseFloat(venda.valor_total) - parseFloat(venda.valor_pago);
                document.getElementById('pagamento-venda-id-display').textContent = venda.id;
                document.getElementById('pagamento-cliente-nome').textContent = venda.cliente_nome;
                document.getElementById('pagamento-saldo-devedor').textContent = `R$ ${valorRestante.toFixed(2).replace('.', ',')}`;
                document.getElementById('pagamento-venda-id').value = venda.id;
                const valorPagoInput = document.getElementById('valor-pago');
                valorPagoInput.value = valorRestante.toFixed(2);
                valorPagoInput.max = valorRestante.toFixed(2);
                pagamentoModal.style.display = 'flex';
            }

            pagamentoModal.querySelector('.modal-close').onclick = () => pagamentoModal.style.display = 'none';
            pagamentoForm.addEventListener('submit', async e => {
                e.preventDefault();
                const btn = e.target.querySelector('button[type="submit"]');
                btn.disabled = true;
                const payload = {
                    venda_id: document.getElementById('pagamento-venda-id').value,
                    valor_pago: document.getElementById('valor-pago').value,
                    metodo_pagamento: document.getElementById('metodo-pagamento-parcela').value
                };
                try {
                    const response = await fetch('api/api_financeiro.php?acao=registrar_pagamento_crediario', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);

                    alert(resultado.mensagem);

                    if (resultado.pagamento_id) {
                        window.open(`imprimir_recibo_pagamento.php?id=${resultado.pagamento_id}`, '_blank');
                    }

                    pagamentoModal.style.display = 'none';
                    carregarCrediario();
                } catch (error) {
                    alert('Erro: ' + error.message);
                } finally {
                    btn.disabled = false;
                }
            });

            const gerarGraficoPizza = async () => {
                const inicio = document.getElementById('relatorio-inicio').value;
                const fim = document.getElementById('relatorio-fim').value;
                const response = await fetch(`api/api_financeiro.php?acao=get_vendas_por_segmento&data_inicio=${inicio}&data_fim=${fim}`);
                const resultado = await response.json();
                if (graficoPizzaInstance) graficoPizzaInstance.destroy();
                const ctx = document.getElementById('graficoPizza').getContext('2d');
                if (resultado.sucesso && resultado.dados.length > 0) {
                    graficoPizzaInstance = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: resultado.dados.map(d => d.tipo),
                            datasets: [{
                                label: 'Total Vendido',
                                data: resultado.dados.map(d => d.total),
                                backgroundColor: ['#3498db', '#2ecc71', '#e74c3c', '#9b59b6', '#f1c40f', '#1abc9c', '#34495e']
                            }]
                        }
                    });
                } else {
                    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                    ctx.font = '16px Poppins';
                    ctx.textAlign = 'center';
                    ctx.fillText('Nenhum dado para exibir', ctx.canvas.width / 2, ctx.canvas.height / 2);
                }
            };

            const gerarGraficoBarra = async () => {
                const inicio = document.getElementById('relatorio-inicio').value;
                const fim = document.getElementById('relatorio-fim').value;
                const response = await fetch(`api/api_financeiro.php?acao=get_relatorio_vendas_por_dia&data_inicio=${inicio}&data_fim=${fim}`);
                const resultado = await response.json();
                if (graficoBarraInstance) graficoBarraInstance.destroy();
                const ctx = document.getElementById('graficoBarra').getContext('2d');
                if (resultado.sucesso && resultado.dados.length > 0) {
                    graficoBarraInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: resultado.dados.map(d => new Date(d.dia + 'T12:00:00').toLocaleDateString('pt-BR')),
                            datasets: [{
                                label: 'Faturamento Diário (R$)',
                                data: resultado.dados.map(d => d.total),
                                backgroundColor: 'rgba(52, 152, 219, 0.8)',
                                borderColor: 'rgba(52, 152, 219, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } else {
                    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                    ctx.font = '16px Poppins';
                    ctx.textAlign = 'center';
                    ctx.fillText('Nenhum dado para exibir', ctx.canvas.width / 2, ctx.canvas.height / 2);
                }
            };

            // Função para carregar movimentações do caixa
            const carregarMovimentacoes = async () => {
                const loadingDiv = document.getElementById('movimentacoes-loading');
                const containerDiv = document.getElementById('movimentacoes-container');
                const tbody = document.getElementById('movimentacoes-tbody');
                const vazioDiv = document.getElementById('movimentacoes-vazio');
                
                loadingDiv.style.display = 'block';
                containerDiv.style.display = 'none';
                
                try {
                    const response = await fetch(`api/api_financeiro.php?acao=get_movimentacoes_caixa`);
                    const resultado = await response.json();
                    
                    if (resultado.sucesso) {
                        tbody.innerHTML = '';
                        
                        // Calcular somatórios por método
                          const totais = {
                              dinheiro: 0,
                              pix: 0,
                              credito: 0,
                              debito: 0,
                              crediario: 0,
                              geral: 0
                          };
                         
                         if (resultado.dados.length > 0) {
                             resultado.dados.forEach(mov => {
                                  const valor = parseFloat(mov.valor_total) || 0;
                                  
                                  // Verificar se o valor é válido
                                  if (isNaN(valor) || valor < 0) {
                                      console.warn('Valor inválido encontrado:', mov);
                                      return; // Pular esta movimentação
                                  }
                                  
                                  // Somar ou subtrair aos totais por método de pagamento
                                   const metodo = mov.metodo_pagamento ? mov.metodo_pagamento.toUpperCase() : '';
                                   const valorCalculado = (mov.tipo === 'CONTA_PAGA' || mov.tipo === 'SANGRIA') ? -valor : valor;
                                   
                                   switch(metodo) {
                                       case 'DINHEIRO':
                                           totais.dinheiro += valorCalculado;
                                           break;
                                       case 'PIX':
                                           totais.pix += valorCalculado;
                                           break;
                                       case 'CREDITO':
                                           totais.credito += valorCalculado;
                                           break;
                                       case 'DEBITO':
                                           totais.debito += valorCalculado;
                                           break;
                                       case 'CREDIARIO':
                                           totais.crediario += valorCalculado;
                                           break;
                                   }
                                  totais.geral += valorCalculado;
                                
                                const row = document.createElement('tr');
                                
                                // Formatação da data e hora
                                const dataHora = new Date(mov.data_hora);
                                const data = dataHora.toLocaleDateString('pt-BR');
                                const hora = dataHora.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                                
                                // Formatação do tipo
                                let tipoTexto = '';
                                let tipoClass = '';
                                switch(mov.tipo) {
                                    case 'VENDA':
                                        tipoTexto = 'Venda';
                                        tipoClass = 'badge-success';
                                        break;
                                    case 'PAGAMENTO_CREDIARIO':
                                        tipoTexto = 'Pagamento Crediário';
                                        tipoClass = 'badge-info';
                                        break;
                                    case 'ABERTURA_CAIXA':
                                        tipoTexto = 'Abertura de Caixa';
                                        tipoClass = 'badge-warning';
                                        break;
                                    case 'FECHAMENTO_CAIXA':
                                        tipoTexto = 'Fechamento de Caixa';
                                        tipoClass = 'badge-danger';
                                        break;
                                    case 'CONTA_PAGA':
                                        tipoTexto = 'Conta Paga';
                                        tipoClass = 'badge-warning';
                                        break;
                                    case 'SANGRIA':
                                        tipoTexto = 'Sangria';
                                        tipoClass = 'badge-danger';
                                        break;
                                    default:
                                        tipoTexto = mov.tipo || 'Desconhecido';
                                        tipoClass = 'badge-secondary';
                                        break;
                                }
                                
                                // Formatação do método de pagamento
                                let metodoTexto = '';
                                switch(mov.metodo_pagamento) {
                                    case 'DINHEIRO': metodoTexto = 'Dinheiro'; break;
                                    case 'PIX': metodoTexto = 'PIX'; break;
                                    case 'DEBITO': metodoTexto = 'Débito'; break;
                                    case 'CREDITO': metodoTexto = 'Crédito'; break;
                                    case 'CREDIARIO': metodoTexto = 'Crediário'; break;
                                    default: metodoTexto = mov.metodo_pagamento;
                                }
                                
                                row.innerHTML = `
                                    <td style="font-weight: 500;">${data}</td>
                                    <td style="font-weight: 500;">${hora}</td>
                                    <td><span class="badge ${tipoClass}">${tipoTexto}</span></td>
                                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${mov.cliente_nome || '-'}">${mov.cliente_nome || '-'}</td>
                                    <td><span style="background-color: #e9ecef; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem;">${metodoTexto}</span></td>
                                    <td style="font-weight: 600; color: ${(mov.tipo === 'CONTA_PAGA' || mov.tipo === 'SANGRIA') ? '#ff6b35' : '#28a745'};">R$ ${parseFloat(mov.valor_total).toFixed(2).replace('.', ',')}</td>
                                `;
                                
                                tbody.appendChild(row);
                            });
                            
                            // Atualizar somatórios
                              // Log dos totais para debug
                              console.log('Totais calculados:', totais);
                              
                              document.getElementById('total-dinheiro').textContent = `R$ ${totais.dinheiro.toFixed(2).replace('.', ',')}`;
                              document.getElementById('total-pix').textContent = `R$ ${totais.pix.toFixed(2).replace('.', ',')}`;
                              document.getElementById('total-credito').textContent = `R$ ${totais.credito.toFixed(2).replace('.', ',')}`;
                              document.getElementById('total-debito').textContent = `R$ ${totais.debito.toFixed(2).replace('.', ',')}`;
                              document.getElementById('total-crediario').textContent = `R$ ${totais.crediario.toFixed(2).replace('.', ',')}`;
                              document.getElementById('total-geral').textContent = `R$ ${totais.geral.toFixed(2).replace('.', ',')}`;
                              
                              document.getElementById('somatorio-movimentacoes').style.display = 'block';
                            vazioDiv.style.display = 'none';
                        } else {
                            document.getElementById('somatorio-movimentacoes').style.display = 'none';
                            vazioDiv.style.display = 'block';
                        }
                    } else {
                        alert('Erro ao carregar movimentações: ' + resultado.mensagem);
                    }
                } catch (error) {
                    console.error('Erro ao carregar movimentações:', error);
                    alert('Erro ao carregar movimentações.');
                } finally {
                    loadingDiv.style.display = 'none';
                    containerDiv.style.display = 'block';
                }
            };

            const carregarTudo = () => {
                carregarStatusCaixa();
                carregarVendas();
                carregarCrediario();
                gerarGraficoPizza();
                gerarGraficoBarra();
                carregarMovimentacoes();
            };

            document.getElementById('vendas-inicio').value = hoje;
            document.getElementById('vendas-fim').value = hoje;
            document.getElementById('relatorio-inicio').value = hoje;
            document.getElementById('relatorio-fim').value = hoje;
            document.getElementById('btn-filtrar-vendas').addEventListener('click', carregarVendas);
            document.getElementById('btn-filtrar-relatorio').addEventListener('click', () => {
                gerarGraficoPizza();
                gerarGraficoBarra();
            });
            // Event listeners removidos - não há mais filtro de data
            document.getElementById('crediario-table-body').addEventListener('click', e => {
                const btnPagar = e.target.closest('.btn-registrar-pagamento');
                const btnCobrar = e.target.closest('.btn-cobrar');
                if (btnPagar) {
                    abrirModalPagamento(JSON.parse(btnPagar.dataset.vendaJson));
                }
                if (btnCobrar) {
                    enviarCobrancaWhatsApp(JSON.parse(btnCobrar.dataset.vendaJson));
                }
            });

            carregarTudo();
        });
    </script>

    <!-- Modal Nova Conta a Pagar -->
    <div id="modal-nova-conta" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nova Conta a Pagar</h3>
                <span class="close" onclick="fecharModalNovaConta()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="form-nova-conta">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nova-descricao">Descrição *</label>
                            <input type="text" id="nova-descricao" name="descricao" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="nova-valor">Valor *</label>
                            <input type="number" id="nova-valor" name="valor" class="form-input" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nova-data-vencimento">Data de Vencimento *</label>
                            <input type="date" id="nova-data-vencimento" name="data_vencimento" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="nova-tipo-conta">Tipo de Conta *</label>
                            <select id="nova-tipo-conta" name="tipo_conta" class="form-input" required>
                                <option value="">Selecione...</option>
                                <option value="FIXA">Conta Fixa</option>
                                <option value="VARIAVEL">Conta Variável</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nova-fornecedor">Fornecedor</label>
                            <select id="nova-fornecedor" name="fornecedor_id" class="form-input">
                                <option value="">Selecione um fornecedor...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nova-fornecedor-nome">Ou digite o nome</label>
                            <input type="text" id="nova-fornecedor-nome" name="fornecedor_nome" class="form-input" placeholder="Nome do fornecedor">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nova-categoria">Categoria</label>
                            <select id="nova-categoria" name="categoria" class="form-input">
                                <option value="">Selecione...</option>
                                <option value="Aluguel">Aluguel</option>
                                <option value="Energia">Energia Elétrica</option>
                                <option value="Água">Água</option>
                                <option value="Internet">Internet</option>
                                <option value="Telefone">Telefone</option>
                                <option value="Fornecedores">Fornecedores</option>
                                <option value="Impostos">Impostos</option>
                                <option value="Manutenção">Manutenção</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nova-metodo-pagamento">Tipo de Pagamento</label>
                            <select id="nova-metodo-pagamento" name="metodo_pagamento" class="form-input">
                                <option value="">Selecione...</option>
                                <option value="CREDITO">Cartão de Crédito</option>
                                <option value="CREDIARIO">Crediário</option>
                                <option value="BOLETO">Boleto</option>
                                <option value="CHEQUE">Cheque</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nova-observacoes">Observações</label>
                        <textarea id="nova-observacoes" name="observacoes" class="form-input" rows="3" placeholder="Observações adicionais..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secundario" onclick="fecharModalNovaConta()">Cancelar</button>
                <button type="button" class="btn btn-primario" onclick="salvarNovaConta()">Salvar Conta</button>
            </div>
        </div>
    </div>

    <!-- Modal Editar Conta a Pagar -->
    <div id="modal-editar-conta" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Conta a Pagar</h3>
                <span class="close" onclick="fecharModalEditarConta()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="form-editar-conta">
                    <input type="hidden" id="editar-id" name="id">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editar-descricao">Descrição *</label>
                            <input type="text" id="editar-descricao" name="descricao" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="editar-valor">Valor *</label>
                            <input type="number" id="editar-valor" name="valor" class="form-input" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editar-data-vencimento">Data de Vencimento *</label>
                            <input type="date" id="editar-data-vencimento" name="data_vencimento" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="editar-tipo-conta">Tipo de Conta *</label>
                            <select id="editar-tipo-conta" name="tipo_conta" class="form-input" required>
                                <option value="">Selecione...</option>
                                <option value="FIXA">Conta Fixa</option>
                                <option value="VARIAVEL">Conta Variável</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editar-fornecedor">Fornecedor</label>
                            <select id="editar-fornecedor" name="fornecedor_id" class="form-input">
                                <option value="">Selecione um fornecedor...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editar-fornecedor-nome">Ou digite o nome</label>
                            <input type="text" id="editar-fornecedor-nome" name="fornecedor_nome" class="form-input" placeholder="Nome do fornecedor">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editar-categoria">Categoria</label>
                            <select id="editar-categoria" name="categoria" class="form-input">
                                <option value="">Selecione...</option>
                                <option value="Aluguel">Aluguel</option>
                                <option value="Energia">Energia Elétrica</option>
                                <option value="Água">Água</option>
                                <option value="Internet">Internet</option>
                                <option value="Telefone">Telefone</option>
                                <option value="Fornecedores">Fornecedores</option>
                                <option value="Impostos">Impostos</option>
                                <option value="Manutenção">Manutenção</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editar-metodo-pagamento">Tipo de Pagamento</label>
                            <select id="editar-metodo-pagamento" name="metodo_pagamento" class="form-input">
                                <option value="">Selecione...</option>
                                <option value="CREDITO">Cartão de Crédito</option>
                                <option value="CREDIARIO">Crediário</option>
                                <option value="BOLETO">Boleto</option>
                                <option value="CHEQUE">Cheque</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editar-status">Status</label>
                            <select id="editar-status" name="status" class="form-input">
                                <option value="PENDENTE">Pendente</option>
                                <option value="PAGO">Pago</option>
                                <option value="VENCIDO">Vencido</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editar-data-pagamento">Data de Pagamento</label>
                            <input type="date" id="editar-data-pagamento" name="data_pagamento" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editar-observacoes">Observações</label>
                        <textarea id="editar-observacoes" name="observacoes" class="form-input" rows="3" placeholder="Observações adicionais..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secundario" onclick="fecharModalEditarConta()">Cancelar</button>
                <button type="button" class="btn btn-primario" onclick="salvarEdicaoConta()">Salvar Alterações</button>
            </div>
        </div>
    </div>

    <script>
        // Funções para o modal de nova conta
        function abrirModalNovaConta() {
            document.getElementById('modal-nova-conta').style.display = 'block';
            carregarFornecedores();
        }

        function fecharModalNovaConta() {
            document.getElementById('modal-nova-conta').style.display = 'none';
            document.getElementById('form-nova-conta').reset();
        }

        function carregarFornecedores() {
            fetch('api/api_financeiro.php?acao=get_fornecedores')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('nova-fornecedor');
                    select.innerHTML = '<option value="">Selecione um fornecedor...</option>';
                    
                    if (data.success && data.fornecedores) {
                        data.fornecedores.forEach(fornecedor => {
                            const option = document.createElement('option');
                            option.value = fornecedor.id;
                            option.textContent = fornecedor.nome;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar fornecedores:', error);
                });
        }

        function salvarNovaConta() {
            const form = document.getElementById('form-nova-conta');
            const formData = new FormData(form);
            
            // Validação básica
            if (!formData.get('descricao') || !formData.get('valor') || !formData.get('data_vencimento') || !formData.get('tipo_conta')) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }

            fetch('api/api_financeiro.php?acao=criar_conta_pagar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Conta criada com sucesso!');
                    fecharModalNovaConta();
                    carregarContasPagar(); // Recarregar a lista
                } else {
                    alert('Erro ao criar conta: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro ao salvar conta:', error);
                alert('Erro ao salvar conta. Tente novamente.');
            });
        }

        // Funções para o modal de editar conta
        function editarConta(id) {
            // Buscar dados da conta
            fetch(`api/api_financeiro.php?acao=get_conta_pagar&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        preencherModalEdicao(data.conta);
                        document.getElementById('modal-editar-conta').style.display = 'block';
                        carregarFornecedoresEdicao();
                    } else {
                        alert('Erro ao carregar dados da conta: ' + (data.message || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar conta:', error);
                    alert('Erro ao carregar dados da conta.');
                });
        }

        function preencherModalEdicao(conta) {
            document.getElementById('editar-id').value = conta.id;
            document.getElementById('editar-descricao').value = conta.descricao;
            document.getElementById('editar-valor').value = conta.valor;
            document.getElementById('editar-data-vencimento').value = conta.data_vencimento;
            document.getElementById('editar-tipo-conta').value = conta.tipo_conta;
            document.getElementById('editar-categoria').value = conta.categoria || '';
            document.getElementById('editar-metodo-pagamento').value = conta.metodo_pagamento || '';
            document.getElementById('editar-observacoes').value = conta.observacoes || '';
            document.getElementById('editar-status').value = conta.status;
            document.getElementById('editar-data-pagamento').value = conta.data_pagamento || '';
            
            // Preencher fornecedor
            if (conta.fornecedor_id) {
                document.getElementById('editar-fornecedor').value = conta.fornecedor_id;
                document.getElementById('editar-fornecedor-nome').value = '';
            } else {
                document.getElementById('editar-fornecedor').value = '';
                document.getElementById('editar-fornecedor-nome').value = conta.fornecedor_nome || '';
            }
        }

        function carregarFornecedoresEdicao() {
            fetch('api/api_financeiro.php?acao=get_fornecedores')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('editar-fornecedor');
                        // Manter a primeira opção
                        const firstOption = select.querySelector('option[value=""]');
                        select.innerHTML = '';
                        select.appendChild(firstOption);
                        
                        data.fornecedores.forEach(fornecedor => {
                            const option = document.createElement('option');
                            option.value = fornecedor.id;
                            option.textContent = fornecedor.nome;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar fornecedores:', error);
                });
        }

        function fecharModalEditarConta() {
            document.getElementById('modal-editar-conta').style.display = 'none';
            document.getElementById('form-editar-conta').reset();
        }

        function salvarEdicaoConta() {
            const form = document.getElementById('form-editar-conta');
            const formData = new FormData(form);
            
            // Validação básica
            if (!formData.get('descricao') || !formData.get('valor') || !formData.get('data_vencimento') || !formData.get('tipo_conta')) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }

            fetch('api/api_financeiro.php?acao=editar_conta_pagar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Conta atualizada com sucesso!');
                    fecharModalEditarConta();
                    carregarContasPagar(); // Recarregar a lista
                } else {
                    alert('Erro ao atualizar conta: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar conta:', error);
                alert('Erro ao atualizar conta. Tente novamente.');
            });
        }

        // Funções para carregar contas a pagar
        function carregarContasPagar() {
            const filtros = new URLSearchParams();
            
            const dataInicio = document.getElementById('contas-data-inicio').value;
            const dataFim = document.getElementById('contas-data-fim').value;
            const fornecedorId = document.getElementById('contas-fornecedor').value;
            const tipoConta = document.getElementById('contas-tipo').value;
            const status = document.getElementById('contas-status').value;
            
            if (dataInicio) filtros.append('data_inicio', dataInicio);
            if (dataFim) filtros.append('data_fim', dataFim);
            if (fornecedorId) filtros.append('fornecedor_id', fornecedorId);
            if (tipoConta) filtros.append('tipo_conta', tipoConta);
            if (status) filtros.append('status', status);
            
            fetch(`api/api_financeiro.php?acao=listar_contas_pagar&${filtros.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        preencherTabelaContas(data.contas);
                        atualizarResumoContas(data.totais);
                    } else {
                        console.error('Erro ao carregar contas:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar contas:', error);
                });
        }
        
        function preencherTabelaContas(contas) {
            const tbody = document.getElementById('contas-pagar-table-body');
            if (!tbody) {
                console.error('Elemento contas-pagar-table-body não encontrado');
                return;
            }
            tbody.innerHTML = '';
            
            if (contas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Nenhuma conta encontrada</td></tr>';
                return;
            }
            
            contas.forEach(conta => {
                const tr = document.createElement('tr');
                const fornecedorNome = conta.fornecedor_nome_db || conta.fornecedor_nome || '-';
                const statusClass = conta.status === 'PAGO' ? 'status-pago' : 
                                  conta.status === 'VENCIDO' ? 'status-vencido' : 'status-pendente';
                
                const tipoPagamentoTexto = conta.metodo_pagamento ? 
                    conta.metodo_pagamento.replace('CREDITO', 'Cartão de Crédito')
                                          .replace('CREDIARIO', 'Crediário')
                                          .replace('BOLETO', 'Boleto')
                                          .replace('CHEQUE', 'Cheque') : '-';
                
                tr.innerHTML = `
                    <td>${conta.id}</td>
                    <td>${conta.descricao}</td>
                    <td>${fornecedorNome}</td>
                    <td>R$ ${parseFloat(conta.valor).toFixed(2)}</td>
                    <td>${formatarData(conta.data_vencimento)}</td>
                    <td>${conta.tipo_conta}</td>
                    <td>${tipoPagamentoTexto}</td>
                    <td><span class="status ${statusClass}">${conta.status}</span></td>
                    <td>
                        <button class="btn btn-pequeno btn-primario" onclick="editarConta(${conta.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${conta.status !== 'PAGO' ? `
                            <button class="btn btn-pequeno btn-sucesso" onclick="marcarComoPaga(${conta.id})" title="Marcar como Paga">
                                <i class="fas fa-check"></i>
                            </button>
                        ` : ''}
                        <button class="btn btn-pequeno btn-perigo" onclick="excluirConta(${conta.id})" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
        
        function atualizarResumoContas(totais) {
            document.getElementById('total-pendente').textContent = `R$ ${parseFloat(totais.total_pendente || 0).toFixed(2)}`;
            document.getElementById('total-vencido').textContent = `R$ ${parseFloat(totais.total_vencido || 0).toFixed(2)}`;
            document.getElementById('total-pago').textContent = `R$ ${parseFloat(totais.total_pago || 0).toFixed(2)}`;
        }
        
        function formatarData(data) {
            if (!data) return '-';
            const date = new Date(data + 'T00:00:00');
            return date.toLocaleDateString('pt-BR');
        }
        
        function marcarComoPaga(id) {
            // Criar modal customizado para seleção de forma de pagamento
            const modalHtml = `
                <div id="modalFormaPagamento" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                ">
                    <div style="
                        background: white;
                        padding: 2rem;
                        border-radius: 8px;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        max-width: 400px;
                        width: 90%;
                    ">
                        <div style="margin-bottom: 1rem;">
                            <h3 style="margin: 0; color: #333;">Forma de Pagamento</h3>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <p style="margin-bottom: 0.5rem; color: #666;">Selecione a forma de pagamento:</p>
                            <select id="selectFormaPagamento" style="
                                width: 100%;
                                padding: 0.5rem;
                                border: 1px solid #ddd;
                                border-radius: 4px;
                                font-size: 1rem;
                            ">
                                <option value="">Selecione...</option>
                                <option value="PIX">PIX</option>
                                <option value="DINHEIRO">Dinheiro</option>
                                <option value="DEBITO">Débito</option>
                                <option value="CREDITO">Cartão de Crédito</option>
                                <option value="BOLETO">Boleto</option>
                                <option value="CHEQUE">Cheque</option>
                            </select>
                        </div>
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button type="button" id="btnCancelarPagamento" style="
                                padding: 0.5rem 1rem;
                                border: 1px solid #ddd;
                                background: #f8f9fa;
                                color: #333;
                                border-radius: 4px;
                                cursor: pointer;
                            ">Cancelar</button>
                            <button type="button" id="btnConfirmarPagamento" style="
                                padding: 0.5rem 1rem;
                                border: none;
                                background: #007bff;
                                color: white;
                                border-radius: 4px;
                                cursor: pointer;
                            ">Confirmar Pagamento</button>
                        </div>
                    </div>
                </div>
            `;
            
            // Remover modal existente se houver
            const modalExistente = document.getElementById('modalFormaPagamento');
            if (modalExistente) {
                modalExistente.remove();
            }
            
            // Adicionar modal ao body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Configurar eventos dos botões
            document.getElementById('btnCancelarPagamento').onclick = function() {
                document.getElementById('modalFormaPagamento').remove();
            };
            
            document.getElementById('btnConfirmarPagamento').onclick = function() {
                const metodoPagamento = document.getElementById('selectFormaPagamento').value;
                
                if (!metodoPagamento) {
                    alert('Por favor, selecione uma forma de pagamento!');
                    return;
                }
                
                if (!confirm(`Deseja marcar esta conta como paga via ${metodoPagamento}?`)) return;
                
                // Fechar modal
                document.getElementById('modalFormaPagamento').remove();
                
                const formData = new FormData();
                formData.append('id', id);
                formData.append('data_pagamento', new Date().toISOString().split('T')[0]);
                formData.append('metodo_pagamento', metodoPagamento);
                
                fetch('api/api_financeiro.php?acao=marcar_conta_paga', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Conta marcada como paga!');
                        carregarContasPagar();
                    } else {
                        alert('Erro: ' + (data.message || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao marcar conta como paga.');
                });
            };
            
            // Fechar modal ao clicar no fundo
            document.getElementById('modalFormaPagamento').onclick = function(e) {
                if (e.target === this) {
                    this.remove();
                }
            };
        }
        
        function excluirConta(id) {
            if (!confirm('Deseja realmente excluir esta conta? Esta ação não pode ser desfeita.')) return;
            
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('api/api_financeiro.php?acao=excluir_conta_pagar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Conta excluída com sucesso!');
                    carregarContasPagar();
                } else {
                    alert('Erro: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao excluir conta.');
            });
        }
        
        function editarConta(id) {
            // Implementar modal de edição (funcionalidade futura)
            alert('Funcionalidade de edição será implementada em breve.');
        }
        
        function carregarFornecedoresFiltro() {
            fetch('api/api_financeiro.php?acao=get_fornecedores')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('contas-fornecedor');
                    if (!select) {
                        console.error('Elemento contas-fornecedor não encontrado');
                        return;
                    }
                    select.innerHTML = '<option value="">Todos</option>';
                    
                    if (data.success && data.fornecedores) {
                        data.fornecedores.forEach(fornecedor => {
                            const option = document.createElement('option');
                            option.value = fornecedor.id;
                            option.textContent = fornecedor.nome;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar fornecedores:', error);
                });
        }

        // Variáveis globais para busca
        let searchInput;
        let dadosOriginais = {
            vendas: [],
            crediario: [],
            contasPagar: [],
            movimentacoesCaixa: []
        };

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar campo de busca
            searchInput = document.getElementById('search-financeiro');
            
            // Carregar dados iniciais
            carregarContasPagar();
            carregarFornecedoresFiltro();
            
            // Verificar se os elementos existem antes de adicionar event listeners
            const btnNovaConta = document.getElementById('btn-nova-conta');
            if (btnNovaConta) {
                btnNovaConta.addEventListener('click', abrirModalNovaConta);
            }
            
            // Carregar dados quando a aba de contas a pagar for ativada
            const tabContasPagar = document.querySelector('[data-tab="contas-pagar"]');
            if (tabContasPagar) {
                tabContasPagar.addEventListener('click', function() {
                    setTimeout(() => {
                        carregarContasPagar();
                        carregarFornecedoresFiltro();
                    }, 100);
                });
            }
            
            // Event listeners para filtros
            const filtroDataInicio = document.getElementById('contas-data-inicio');
            const filtroDataFim = document.getElementById('contas-data-fim');
            const filtroFornecedor = document.getElementById('contas-fornecedor');
            const filtroTipoConta = document.getElementById('contas-tipo');
            const filtroStatus = document.getElementById('contas-status');
            
            if (filtroDataInicio) filtroDataInicio.addEventListener('change', carregarContasPagar);
            if (filtroDataFim) filtroDataFim.addEventListener('change', carregarContasPagar);
            if (filtroFornecedor) filtroFornecedor.addEventListener('change', carregarContasPagar);
            if (filtroTipoConta) filtroTipoConta.addEventListener('change', carregarContasPagar);
            if (filtroStatus) filtroStatus.addEventListener('change', carregarContasPagar);
            
            // Event listener para o botão de filtrar
            const btnFiltrarContas = document.getElementById('btn-filtrar-contas');
            if (btnFiltrarContas) btnFiltrarContas.addEventListener('click', carregarContasPagar);
            
            // Event listener para o campo de busca
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const termo = e.target.value.trim().toLowerCase();
                    filtrarDadosAtivos(termo);
                });
            }
        });
        
        // Função para filtrar dados da aba ativa
        function filtrarDadosAtivos(termo) {
            const abaAtiva = document.querySelector('.tab-content.active');
            if (!abaAtiva) return;
            
            const tabelas = abaAtiva.querySelectorAll('table tbody');
            tabelas.forEach(tbody => {
                const linhas = tbody.querySelectorAll('tr');
                linhas.forEach(linha => {
                    const texto = linha.textContent.toLowerCase();
                    if (termo === '' || texto.includes(termo)) {
                        linha.style.display = '';
                    } else {
                        linha.style.display = 'none';
                    }
                });
            });
        }
        
        // Função para gerar backup do sistema
        async function gerarBackupSistema() {
            try {
                // Mostrar mensagem de progresso
                const progressMsg = document.createElement('div');
                progressMsg.style.cssText = `
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                    z-index: 10000;
                    text-align: center;
                    min-width: 300px;
                `;
                progressMsg.innerHTML = `
                    <i class="fa-solid fa-spinner fa-spin" style="font-size: 24px; color: #007bff; margin-bottom: 10px;"></i>
                    <p style="margin: 0; font-weight: bold;">Gerando backup do sistema...</p>
                    <p style="margin: 5px 0 0 0; font-size: 14px; color: #666;">Por favor, aguarde...</p>
                `;
                document.body.appendChild(progressMsg);
                
                // Chamar API para gerar backup
                const response = await fetch('api/api_backup.php?acao=gerar_backup');
                const resultado = await response.json();
                
                if (!resultado.sucesso) {
                    throw new Error(resultado.mensagem);
                }
                
                // Atualizar mensagem de progresso
                progressMsg.innerHTML = `
                    <i class="fa-solid fa-check-circle" style="font-size: 24px; color: #28a745; margin-bottom: 10px;"></i>
                    <p style="margin: 0; font-weight: bold;">Backup gerado com sucesso!</p>
                    <p style="margin: 5px 0 0 0; font-size: 14px; color: #666;">Iniciando download...</p>
                `;
                
                // Aguardar um momento antes de iniciar o download
                setTimeout(() => {
                    // Criar formulário temporário para download
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = 'download_backup.php';
                    form.style.display = 'none';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'arquivo';
                    input.value = resultado.arquivo;
                    
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                    
                    // Remover mensagem de progresso
                    document.body.removeChild(progressMsg);
                    
                    // Mostrar mensagem de sucesso
                    alert(`Backup realizado com sucesso!\n\nArquivo: ${resultado.arquivo}\nTamanho: ${resultado.tamanho}\nData: ${resultado.data}`);
                }, 1000);
                
            } catch (error) {
                // Remover mensagem de progresso se existir
                const progressMsg = document.querySelector('div[style*="position: fixed"]');
                if (progressMsg) {
                    document.body.removeChild(progressMsg);
                }
                
                alert('Erro ao gerar backup: ' + error.message);
                console.error('Erro no backup:', error);
            }
        }
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
    
    <!-- Sistema de Controle de Zoom -->

</body>

</html>