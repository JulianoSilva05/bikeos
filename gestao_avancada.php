<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Gestão Avançada'); ?></title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos copiados diretamente de dashboard.php para consistência */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .kpi-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card-kpi {
            background-color: var(--cor-branco);
            padding: 1.5rem;
            border-radius: var(--raio-borda);
            box-shadow: var(--sombra-card);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .card-kpi .icon {
            font-size: 2rem;
            color: var(--cor-primaria);
        }

        .card-kpi .info h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }

        .card-kpi .info p {
            color: var(--cor-texto-secundario);
            margin: 0;
        }

        .faturamento-detalhado {
            font-size: 0.9rem;
            width: 100%;
        }

        .faturamento-detalhado .total-dia {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: block;
        }

        .faturamento-detalhado .detalhe-item {
            display: flex;
            justify-content: space-between;
            line-height: 1.6;
        }

        .faturamento-detalhado .detalhe-item strong {
            font-weight: 600;
        }

        .faturamento-detalhado hr {
            border: 0;
            border-top: 1px solid #eee;
            margin: 0.5rem 0;
        }

        /* Estilo para as estrelas de satisfação */
        .satisfaction-stars {
            font-size: 1.5rem;
            color: #ffc107;
            margin-top: 5px;
        }

        .satisfaction-stars .fa-star-o {
            color: #ccc;
        }

        .dashboard-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 992px) {
            .dashboard-layout {
                grid-template-columns: 2fr 1fr;
            }
        }

        .lista-itens {
            list-style: none;
            padding: 0;
        }

        .lista-itens li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--cor-borda);
            transition: background-color 0.2s;
        }

        .lista-itens li:last-child {
            border-bottom: none;
        }

        .lista-itens .item-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .icon-item {
            font-size: 1.5rem;
            width: 40px;
            text-align: center;
        }

        .icon-agenda {
            color: var(--cor-primaria);
        }

        .icon-alerta {
            color: var(--cor-alerta);
        }

        .icon-birthday {
            color: #e83e8c;
        }

        .info-item .titulo {
            font-weight: 600;
            margin: 0;
        }

        .info-item .subtitulo {
            font-size: 0.9rem;
            color: var(--cor-texto-secundario);
            margin: 0;
        }

        .status {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--cor-branco);
            text-transform: uppercase;
        }

        .status-EM_SERVICO,
        .status-ORCAMENTO,
        .status-ATIVA {
            background-color: var(--cor-primaria);
        }

        .status-PRONTA {
            background-color: #ffc107;
            color: #333;
        }

        .status-AGUARDANDO_PECA {
            background-color: var(--cor-alerta);
            color: #333;
        }

        .status-CANCELADA {
            background-color: var(--cor-texto-secundario);
        }

        .status-FINALIZADA {
            background-color: #28a745;
        }

        .card-link {
            text-decoration: none;
            color: inherit;
        }

        #card-os-ativas,
        #card-faturamento-link,
        .card-kpi,
        .lista-itens .clickable {
            cursor: pointer;
        }

        #card-os-ativas:hover,
        #card-faturamento-link:hover,
        .card-kpi:hover,
        .lista-itens .clickable:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
            background-color: var(--cor-fundo-claro);
        }

        .fab-container-history {
            position: fixed;
            bottom: 95px;
            right: 25px;
            z-index: 1050;
        }

        .fab-container-birthday {
            position: fixed;
            bottom: 160px;
            right: 25px;
            z-index: 1050;
        }

        .caixa-status {
            padding: 0.3rem;
            border-radius: var(--raio-borda);
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .caixa-fechado {
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            color: #721c24;
        }

        .caixa-aberto {
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            color: #0f5132;
        }

        .agenda-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .agenda-header h2 {
            margin: 0;
            font-size: 1.2em;
            flex-grow: 1;
            text-align: center;
        }

        .agenda-nav-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--cor-primaria);
            padding: 0 10px;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid var(--cor-borda);
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .tab-link {
            padding: 0.8rem 1.5rem;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 1.1rem;
        }

        .tab-link.active {
            border-bottom: 3px solid var(--cor-primaria);
            font-weight: 600;
            color: var(--cor-primaria);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .text-muted {
            text-decoration: line-through;
            color: #6c757d;
        }

        /* ESTILO PARA O NOVO FOOTER */
        .page-footer {
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: var(--cor-texto-secundario);
            border-top: 1px solid var(--cor-borda);
        }

        .page-footer a {
            color: var(--cor-primaria);
            text-decoration: none;
            font-weight: 600;
        }

        .page-footer a:hover {
            text-decoration: underline;
        }

        /* Estilos específicos para a página de Gestão Avançada */
        .dashboard-grid { /* Sobrescreve o layout padrão para a página de gestão */
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .dashboard-grid {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: repeat(3, 1fr); /* 3 colunas para telas maiores */
            }
            .dashboard-grid .span-2 {
                grid-column: span 2;
            }
            .dashboard-grid .span-3 {
                grid-column: span 3;
            }
        }

        .card { /* Estilo do card padrão, mas ajustado para o layout de gestão */
            background-color: var(--cor-branco);
            padding: 1.5rem;
            border-radius: var(--raio-borda);
            box-shadow: var(--sombra-card);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card h2 {
            font-size: 1.5rem;
            color: var(--cor-primaria);
            margin-top: 0;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--cor-borda);
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-weight: 600;
        }

        .card h2 i {
            color: var(--cor-primaria);
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--cor-borda);
            color: var(--cor-texto-primario);
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .list-group-item strong {
            color: var(--cor-primaria);
        }

        .list-group-item .sub-info {
            font-size: 0.85rem;
            color: var(--cor-texto-secundario);
        }

        .alert-item {
            background-color: #f8d7da; /* Alerta de perigo */
            border: 1px solid #f5c2c7;
            padding: 0.8rem;
            border-radius: var(--raio-borda);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: #721c24;
        }
        .alert-item i {
            color: #721c24;
            font-size: 1.2rem;
        }
        .alert-item span {
            color: inherit;
        }
        .alert-item.warning {
            background-color: #fff3cd; /* Alerta de atenção */
            border-color: #ffeeba;
            color: #856404;
        }
        .alert-item.warning[style*="cursor: pointer"] {
            transition: all 0.3s ease;
        }
        .alert-item.warning[style*="cursor: pointer"]:hover {
            background-color: #ffeaa7;
            border-color: #ffdf7e;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .alert-item.info[style*="cursor: pointer"] {
            transition: all 0.3s ease;
        }
        .alert-item.info[style*="cursor: pointer"]:hover {
            background-color: #c3e6cb;
            border-color: #a3d9a4;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .alert-item.warning i {
            color: #856404;
        }
        .alert-item.info {
            background-color: #d1e7dd; /* Alerta de informação */
            border-color: #badbcc;
            color: #0a3622;
        }
        .alert-item.info i {
            color: #0a3622;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            margin-top: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        .action-buttons .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        .list-group-item .status-badge {
            padding: 4px 8px;
            border-radius: var(--raio-borda);
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--cor-branco);
            text-transform: uppercase;
        }
        .status-badge.ENVIADA { background-color: var(--cor-primaria); } /* Usando cor primaria do dashboard */
        .status-badge.RESPONDIDA { background-color: #28a745; } /* Verde de sucesso */
        .status-badge.PENDENTE { background-color: var(--cor-texto-secundario); color: var(--cor-branco); }
        .status-badge.EXPIRADA { background-color: #dc3545; } /* Vermelho de perigo */

        .review-stars-small {
            color: #ffc107;
            font-size: 1rem;
        }
        .review-stars-small .fa-star-o {
            color: #ccc;
        }

        /* Ajustes para telas menores (mantidos do tema anterior, se aplicável) */
        @media (max-width: 767px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .header-actions {
                width: 100%;
                justify-content: space-around;
                margin-top: 1rem;
            }
            .header-actions .btn {
                flex-grow: 1;
            }
            .list-group-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .list-group-item > div:first-child {
                width: 100%;
            }
            .list-group-item > div:last-child {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                gap: 0.5rem;
            }
            .action-buttons {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php"><img src="assets/img/logo.png" alt="Logo" class="navbar-logo"></a>
        <div class="navbar-user-info"><i class="fa-solid fa-user-circle"></i><span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span></div>
        <a href="logout.php" class="btn btn-destaque"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
    </nav>
    <main class="container">
        <div class="page-header-with-nav">
            <div class="page-title">
                <h1><i class="fa-solid fa-chart-line"></i> Gestão Avançada</h1>
            </div>
            <div class="page-navigation">
                <button onclick="history.back()" class="btn btn-secundario"><i class="fa-solid fa-arrow-left"></i> Voltar</button>
                <a href="dashboard.php" class="btn btn-secundario"><i class="fa-solid fa-house"></i> Dashboard</a>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Visão Geral de Estoque -->
            <div class="card">
                <h2><i class="fa-solid fa-boxes-stacked"></i> Alertas de Estoque</h2>
                <div id="estoque-critico-list" class="list-group">
                    <p class="sub-info" style="text-align: center;">Carregando alertas de estoque...</p>
                </div>
            </div>

            <!-- Análise de Serviços e Produtos -->
            <div class="card span-2">
                <h2><i class="fa-solid fa-chart-bar"></i> Faturamento Mensal</h2>
                <div class="chart-container">
                    <canvas id="faturamentoMensalChart"></canvas>
                </div>
            </div>

            <div class="card">
                <h2><i class="fa-solid fa-wrench"></i> Top 5 Serviços</h2>
                <ul id="top-servicos-list" class="list-group">
                    <p class="sub-info" style="text-align: center;">Carregando serviços populares...</p>
                </ul>
            </div>

            <div class="card">
                <h2><i class="fa-solid fa-bicycle"></i> Top 5 Produtos</h2>
                <ul id="top-produtos-list" class="list-group">
                    <p class="sub-info" style="text-align: center;">Carregando produtos mais vendidos...</p>
                </ul>
            </div>

            <!-- Gestão de Clientes -->
            <div class="card">
                <h2><i class="fa-solid fa-users"></i> Clientes Frequentes</h2>
                <ul id="clientes-frequentes-list" class="list-group">
                    <p class="sub-info" style="text-align: center;">Carregando clientes...</p>
                </div>
            </div>

            <!-- NOVO CARD: Lembretes de Revisão -->
            <div class="card">
                <h2><i class="fa-solid fa-calendar-check"></i> Lembretes de Revisão</h2>
                <ul id="lembretes-revisao-list" class="list-group">
                    <p class="sub-info" style="text-align: center;">Carregando lembretes...</p>
                </ul>
            </div>

            <!-- NOVO CARD: Pesquisas de Satisfação -->
            <div class="card span-2">
                <h2><i class="fa-solid fa-comments"></i> Pesquisas de Satisfação</h2>
                <ul id="pesquisas-satisfacao-list" class="list-group">
                    <p class="sub-info" style="text-align: center;">Carregando pesquisas...</p>
                </ul>
            </div>

        </div>
    </main>

    <footer class="page-footer">
        Desenvolvido por <a href="https://bikeos.com.br" target="_blank" rel="noopener noreferrer">BikeOS</a> - Todos os direitos reservados 2025 | <a href="mailto:contato@bikeos.com.br">contato@bikeos.com.br</a>
    </footer>

    <?php include '_partials/menu_admin.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const estoqueCriticoList = document.getElementById('estoque-critico-list');
            const topServicosList = document.getElementById('top-servicos-list');
            const topProdutosList = document.getElementById('top-produtos-list');
            const clientesFrequentesList = document.getElementById('clientes-frequentes-list');
            const lembretesRevisaoList = document.getElementById('lembretes-revisao-list'); // NOVO
            const pesquisasSatisfacaoList = document.getElementById('pesquisas-satisfacao-list'); // NOVO
            const faturamentoMensalChartCanvas = document.getElementById('faturamentoMensalChart').getContext('2d');

            // Função auxiliar para formatar moeda
            const formatCurrency = (value) => `R$ ${parseFloat(value || 0).toFixed(2).replace('.', ',')}`;
            
            // Função para renderizar estrelas
            const renderStars = (nota) => {
                let starsHtml = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= nota) {
                        starsHtml += '<i class="fa-solid fa-star"></i>';
                    } else {
                        starsHtml += '<i class="fa-regular fa-star fa-star-o"></i>';
                    }
                }
                return `<span class="review-stars-small">${starsHtml}</span>`;
            };

            // Mapeia variáveis CSS para uso no Chart.js e logs
            const style = getComputedStyle(document.body);
            // As variáveis CSS do dashboard.php são usadas aqui
            const var_cor_primaria = style.getPropertyValue('--cor-primaria').trim();
            const var_cor_texto_secundario = style.getPropertyValue('--cor-texto-secundario').trim();
            const var_cor_borda = style.getPropertyValue('--cor-borda').trim();
            const var_cor_texto_primario = style.getPropertyValue('--cor-texto-primario').trim(); // Adicionado


            // --- Carregar Alertas de Estoque ---
            async function carregarAlertasEstoque() {
                try {
                    console.log('Fetching stock alerts...');
                    const response = await fetch('api/api_gestao_avancada.php?acao=listar_alertas_estoque_avancado');
                    
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Response Error:', response.status, errorText);
                        throw new Error(`Erro HTTP: ${response.status} - ${errorText}`);
                    }

                    const resultado = await response.json();
                    console.log('Stock alerts API response:', resultado);

                    estoqueCriticoList.innerHTML = ''; // Limpa o carregando

                    if (resultado.sucesso && resultado.dados) {
                        const { critico, baixo, sem_movimentacao } = resultado.dados;

                        if (critico.length > 0) {
                            critico.forEach(item => {
                                estoqueCriticoList.innerHTML += `<div class="alert-item"><i class="fa-solid fa-triangle-exclamation"></i> <span><strong>Crítico:</strong> ${item.nome} (Estoque: ${item.estoque_atual})</span></div>`;
                            });
                        }
                        if (baixo.length > 0) {
                            baixo.forEach(item => {
                                estoqueCriticoList.innerHTML += `<div class="alert-item warning" style="cursor: pointer;" onclick="window.location.href='entrada_estoque.php'"><i class="fa-solid fa-exclamation-triangle"></i> <span><strong>Baixo:</strong> ${item.nome} (Estoque: ${item.estoque_atual} / Mín: ${item.estoque_minimo})</span></div>`;
                            });
                        }
                        if (sem_movimentacao.length > 0) {
                            sem_movimentacao.forEach(item => {
                                estoqueCriticoList.innerHTML += `<div class="alert-item info" style="cursor: pointer;" onclick="abrirModalSugestoes(${item.id}, '${item.nome.replace(/'/g, "\\'")}')"><i class="fa-solid fa-box-open"></i> <span><strong>Sem Mov.:</strong> ${item.nome} (Estoque: ${item.estoque_atual})</span></div>`;
                            });
                        }

                        if (critico.length === 0 && baixo.length === 0 && sem_movimentacao.length === 0) {
                            estoqueCriticoList.innerHTML = '<p class="sub-info" style="text-align: center;">Nenhum alerta de estoque no momento.</p>';
                        }

                    } else {
                        estoqueCriticoList.innerHTML = `<p class="sub-info error-message" style="text-align: center;">Erro ao carregar alertas de estoque: ${resultado.mensagem || 'Resposta inesperada da API.'}</p>`;
                    }
                } catch (error) {
                    console.error('Erro ao carregar alertas de estoque:', error);
                    estoqueCriticoList.innerHTML = `<p class="sub-info error-message" style="text-align: center;">Falha na comunicação com a API de estoque. Detalhes: ${error.message}</p>`;
                }
            }

            // --- Carregar Top 5 Serviços ---
            async function carregarTopServicos() {
                try {
                    console.log('Fetching top services...');
                    const response = await fetch('api/api_gestao_avancada.php?acao=listar_servicos_populares');
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Response Error (Top Services):', response.status, errorText);
                        throw new Error(`Erro HTTP: ${response.status} - ${errorText}`);
                    }
                    const resultado = await response.json();
                    console.log('Top services API response:', resultado);
                    topServicosList.innerHTML = '';

                    if (resultado.sucesso && resultado.dados.length > 0) {
                        resultado.dados.forEach(servico => {
                            topServicosList.innerHTML += `
                                <li class="list-group-item">
                                    <span>${servico.nome}</span>
                                    <span class="sub-info">${formatCurrency(servico.receita_total)} (${servico.total_realizado}x)</span>
                                </li>
                            `;
                        });
                    } else {
                        topServicosList.innerHTML = '<p class="sub-info" style="text-align: center;">Nenhum serviço popular encontrado.</p>';
                    }
                } catch (error) {
                    console.error('Erro ao carregar top serviços:', error);
                    topServicosList.innerHTML = `<p class="sub-info error-message" style="text-align: center;">Falha na comunicação com a API de serviços. Detalhes: ${error.message}</p>`;
                }
            }

            // --- Carregar Top 5 Produtos ---
            async function carregarTopProdutos() {
                try {
                    console.log('Fetching top products...');
                    const response = await fetch('api/api_gestao_avancada.php?acao=listar_produtos_mais_vendidos');
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Response Error (Top Products):', response.status, errorText);
                        throw new Error(`Erro HTTP: ${response.status} - ${errorText}`);
                    }
                    const resultado = await response.json();
                    console.log('Top products API response:', resultado);
                    topProdutosList.innerHTML = '';

                    if (resultado.sucesso && resultado.dados.length > 0) {
                        resultado.dados.forEach(produto => {
                            topProdutosList.innerHTML += `
                                <li class="list-group-item">
                                    <span>${produto.nome}</span>
                                    <span class="sub-info">${formatCurrency(produto.receita_total)} (${produto.total_vendido} un.)</span>
                                </li>
                            `;
                        });
                    } else {
                        topProdutosList.innerHTML = '<p class="sub-info" style="text-align: center;">Nenhum produto mais vendido encontrado.</p>';
                    }
                } catch (error) {
                    console.error('Erro ao carregar top produtos:', error);
                    topProdutosList.innerHTML = `<p class="sub-info error-message" style="text-align: center;">Falha na comunicação com a API de produtos. Detalhes: ${error.message}</p>`;
                }
            }

            // --- Carregar Clientes Frequentes ---
            async function carregarClientesFrequentes() {
                try {
                    console.log('Fetching frequent clients...');
                    const response = await fetch('api/api_gestao_avancada.php?acao=listar_clientes_frequentes');
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Response Error (Frequent Clients):', response.status, errorText);
                        throw new Error(`Erro HTTP: ${response.status} - ${errorText}`);
                    }
                    const resultado = await response.json();
                    console.log('Frequent clients API response:', resultado);
                    clientesFrequentesList.innerHTML = '';

                    if (resultado.sucesso && resultado.dados.length > 0) {
                        resultado.dados.forEach(cliente => {
                            clientesFrequentesList.innerHTML += `
                                <li class="list-group-item">
                                    <span><strong>${cliente.nome}</strong></span>
                                    <span class="sub-info">${cliente.total_ordens} OS/Vendas</span>
                                </li>
                            `;
                        });
                    } else {
                        clientesFrequentesList.innerHTML = '<p class="sub-info" style="text-align: center;">Nenhum cliente frequente encontrado.</p>';
                    }
                } catch (error) {
                    console.error('Erro ao carregar clientes frequentes:', error);
                    clientesFrequentesList.innerHTML = `<p class="sub-info error-message" style="text-align: center;">Falha na comunicação com a API de clientes. Detalhes: ${error.message}</p>`;
                }
            }

            // --- Carregar Faturamento Mensal e Renderizar Gráfico ---
            async function carregarFaturamentoMensal() {
                try {
                    console.log('Fetching monthly revenue...');
                    const response = await fetch('api/api_gestao_avancada.php?acao=listar_faturamento_mensal');
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Response Error (Monthly Revenue):', response.status, errorText);
                        throw new Error(`Erro HTTP: ${response.status} - ${errorText}`);
                    }
                    const resultado = await response.json();
                    console.log('Monthly revenue API response:', resultado);

                    if (resultado.sucesso && resultado.dados.length > 0) {
                        const labels = resultado.dados.map(item => item.label);
                        const data = resultado.dados.map(item => item.total);

                        new Chart(faturamentoMensalChartCanvas, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Faturamento Mensal',
                                    data: data,
                                    backgroundColor: var_cor_primaria, // Usando a variável CSS
                                    borderColor: var_cor_primaria,
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        labels: {
                                            color: var_cor_texto_primario // Cor da legenda
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            color: var_cor_borda // Cor da grade X
                                        },
                                        ticks: {
                                            color: var_cor_texto_secundario // Cor dos rótulos X
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: var_cor_borda // Cor da grade Y
                                        },
                                        ticks: {
                                            color: var_cor_texto_secundario, // Cor dos rótulos Y
                                            callback: function(value) {
                                                return 'R$ ' + value.toFixed(2).replace('.', ',');
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        faturamentoMensalChartCanvas.canvas.parentNode.innerHTML = '<p class="sub-info" style="text-align: center;">Nenhum dado de faturamento mensal disponível.</p>';
                    }
                } catch (error) {
                    console.error('Erro ao carregar faturamento mensal:', error);
                    faturamentoMensalChartCanvas.canvas.parentNode.innerHTML = `<p class="sub-info error-message" style="text-align: center;">Falha na comunicação com a API de faturamento. Detalhes: ${error.message}</p>`;
                }
            }

            // --- NOVO: Carregar Lembretes de Revisão ---
            async function carregarLembretesRevisao() {
                try {
                    console.log('Fetching review reminders...');
                    const response = await fetch('api/api_gestao_avancada.php?acao=listar_lembretes_revisao');
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Response Error (Review Reminders):', response.status, errorText);
                        throw new Error(`Erro HTTP: ${response.status} - ${errorText}`);
                    }
                    const resultado = await response.json();
                    console.log('Review reminders API response:', resultado);
                    lembretesRevisaoList.innerHTML = '';

                    if (resultado.sucesso && resultado.dados.length > 0) {
                        resultado.dados.forEach(lembrete => {
                            const dataFinalizacao = new Date(lembrete.data_finalizacao).toLocaleDateString('pt-BR');
                            lembretesRevisaoList.innerHTML += `
                                <li class="list-group-item">
                                    <div>
                                        <span><strong>${lembrete.cliente_nome}</strong></span>
                                        <span class="sub-info">Bike: ${lembrete.bicicleta_modelo} (OS #${lembrete.os_id})</span>
                                        <span class="sub-info">Última revisão: ${dataFinalizacao}</span>
                                    </div>
                                    <div class="action-buttons">
                                        <a href="https://wa.me/55${lembrete.cliente_telefone.replace(/\D/g, '')}?text=${encodeURIComponent(`Olá ${lembrete.cliente_nome}! Sua bicicleta ${lembrete.bicicleta_modelo} (OS #${lembrete.os_id}) teve a última revisão em ${dataFinalizacao}. Que tal agendarmos a próxima?`)}" target="_blank" class="btn btn-destaque"><i class="fa-brands fa-whatsapp"></i> Lembrar</a>
                                    </div>
                                </li>
                            `;
                        });
                    } else {
                        lembretesRevisaoList.innerHTML = '<p class="sub-info" style="text-align: center;">Nenhum lembrete de revisão pendente.</p>';
                    }
                } catch (error) {
                    console.error('Erro ao carregar lembretes de revisão:', error);
                    lembretesRevisaoList.innerHTML = `<p class="sub-info error-message" style="text-align: center;">Falha na comunicação com a API de lembretes. Detalhes: ${error.message}</p>`;
                }
            }

            // --- NOVO: Carregar Pesquisas de Satisfação ---
            async function carregarPesquisasSatisfacao() {
                try {
                    console.log('Fetching satisfaction surveys...');
                    const response = await fetch('api/api_gestao_avancada.php?acao=listar_pesquisas_pendentes_e_recentes');
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Response Error (Satisfaction Surveys):', response.status, errorText);
                        throw new Error(`Erro HTTP: ${response.status} - ${errorText}`);
                    }
                    const resultado = await response.json();
                    console.log('Satisfaction surveys API response:', resultado);
                    pesquisasSatisfacaoList.innerHTML = '';

                    if (resultado.sucesso && resultado.dados.length > 0) {
                        resultado.dados.forEach(pesquisa => {
                            const dataEnvio = new Date(pesquisa.data_envio).toLocaleDateString('pt-BR');
                            const dataFinalizacaoOS = new Date(pesquisa.data_finalizacao).toLocaleDateString('pt-BR');
                            let statusBadge = `<span class="status-badge ${pesquisa.status}">${pesquisa.status}</span>`;
                            let acoes = '';

                            if (pesquisa.status === 'ENVIADA' || pesquisa.status === 'PENDENTE') { // Permite reenviar se PENDENTE ou ENVIADA
                                acoes = `
                                    <button class="btn btn-primario btn-enviar-pesquisa" 
                                            data-os-id="${pesquisa.os_id}" 
                                            data-cliente-nome="${pesquisa.cliente_nome}" 
                                            data-cliente-telefone="${pesquisa.cliente_telefone}">
                                            <i class="fa-brands fa-whatsapp"></i> Enviar
                                    </button>
                                `;
                            } else if (pesquisa.status === 'RESPONDIDA') {
                                acoes = `
                                    ${renderStars(pesquisa.nota)}
                                    <a href="editar_os.php?id=${pesquisa.os_id}" target="_blank" class="btn btn-secundario"><i class="fa-solid fa-eye"></i> OS</a>
                                `;
                            } 

                            pesquisasSatisfacaoList.innerHTML += `
                                <li class="list-group-item">
                                    <div>
                                        <span><strong>${pesquisa.cliente_nome}</strong> (OS #${pesquisa.os_id})</span>
                                        <span class="sub-info">Finalizada em: ${dataFinalizacaoOS}</span>
                                        ${pesquisa.status === 'RESPONDIDA' && pesquisa.comentario ? `<span class="sub-info">Comentário: ${pesquisa.comentario}</span>` : ''}
                                    </div>
                                    <div>
                                        ${statusBadge}
                                        <div class="action-buttons">${acoes}</div>
                                    </div>
                                </li>
                            `;
                        });

                        // Adiciona event listeners para os botões de "Enviar Pesquisa"
                        document.querySelectorAll('.btn-enviar-pesquisa').forEach(button => {
                            button.addEventListener('click', async (e) => {
                                const osId = e.target.dataset.osId;
                                const clienteNome = e.target.dataset.clienteNome;
                                const clienteTelefone = e.target.dataset.clienteTelefone;
                                
                                e.target.disabled = true;
                                e.target.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enviando...';

                                try {
                                    const response = await fetch('api/api_gestao_avancada.php?acao=enviar_pesquisa_satisfacao', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ os_id: osId })
                                    });
                                    const resultado = await response.json();

                                    if (resultado.sucesso) {
                                        alert(`Pesquisa para OS #${osId} enviada para ${clienteNome}!`);
                                        // Abre o WhatsApp para o envio manual
                                        window.open(resultado.link_whatsapp, '_blank');
                                        carregarPesquisasSatisfacao(); // Recarrega a lista
                                    } else {
                                        throw new Error(resultado.mensagem);
                                    }
                                } catch (error) {
                                    alert('Erro ao enviar pesquisa: ' + error.message);
                                    e.target.disabled = false;
                                    e.target.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Enviar';
                                }
                            });
                        });

                    } else {
                        pesquisasSatisfacaoList.innerHTML = '<p class="sub-info" style="text-align: center;">Nenhuma pesquisa de satisfação pendente ou recente.</p>';
                    }
                } catch (error) {
                    console.error('Erro ao carregar pesquisas de satisfação:', error);
                    pesquisasSatisfacaoList.innerHTML = `<p class="sub-info error-message" style="text-align: center;">Falha na comunicação com a API de pesquisas. Detalhes: ${error.message}</p>`;
                }
            }


            // Função para abrir modal de sugestões
            window.abrirModalSugestoes = async function(produtoId, produtoNome) {
                const modal = document.getElementById('modalSugestoes');
                const produtoNomeSpan = document.getElementById('produtoNomeSugestoes');
                const clientesList = document.getElementById('clientesSugestoesList');
                
                produtoNomeSpan.textContent = produtoNome;
                clientesList.innerHTML = '<p style="text-align: center;">Carregando clientes...</p>';
                modal.style.display = 'block';
                
                try {
                    const response = await fetch(`api/api_sugestoes.php?acao=buscar_clientes_com_sugestoes&produto_id=${produtoId}`);
                    const resultado = await response.json();
                    
                    if (resultado.sucesso && resultado.dados.length > 0) {
                        let html = '';
                        resultado.dados.forEach(cliente => {
                            const telefone = cliente.telefone ? cliente.telefone.replace(/\D/g, '') : '';
                            const whatsappDisponivel = telefone && telefone.length >= 10;
                            
                            html += `
                                <div class="cliente-sugestao-item">
                                    <div class="cliente-info">
                                        <h4>${cliente.nome}</h4>
                                        <p><strong>Produto que comprou:</strong> ${cliente.produto_gatilho}</p>
                                        ${cliente.telefone ? `<p><strong>Telefone:</strong> ${cliente.telefone}</p>` : ''}
                                        ${cliente.email ? `<p><strong>Email:</strong> ${cliente.email}</p>` : ''}
                                    </div>
                                    <div class="cliente-acoes">
                                        ${whatsappDisponivel ? 
                                            `<button class="btn btn-whatsapp" onclick="enviarWhatsAppSugestao('${telefone}', '${cliente.nome}', '${produtoNome}', '${cliente.produto_imagem || ''}')">
                                                <i class="fab fa-whatsapp"></i> WhatsApp
                                            </button>` : 
                                            '<span class="sem-whatsapp">Sem WhatsApp</span>'
                                        }
                                    </div>
                                </div>
                            `;
                        });
                        clientesList.innerHTML = html;
                    } else {
                        clientesList.innerHTML = '<p style="text-align: center; color: #666;">Nenhum cliente encontrado com sugestões para este produto.</p>';
                    }
                } catch (error) {
                    console.error('Erro ao buscar clientes:', error);
                    clientesList.innerHTML = '<p style="text-align: center; color: #e74c3c;">Erro ao carregar clientes. Tente novamente.</p>';
                }
            };
            
            // Função para enviar WhatsApp
            window.enviarWhatsAppSugestao = function(telefone, clienteNome, produtoNome, imagemUrl) {
                let mensagem = `Olá ${clienteNome}! 😊\n\nEspero que esteja tudo bem! Tenho uma sugestão especial para você.\n\nBaseado no seu histórico de compras, acredito que você pode ter interesse no produto: *${produtoNome}*`;
                
                // Adiciona a imagem se disponível
                if (imagemUrl && imagemUrl.trim() !== '') {
                    const urlCompleta = imagemUrl.startsWith('http') ? imagemUrl : `${window.location.origin}/${imagemUrl}`;
                    mensagem += `\n\n📸 Veja a imagem do produto: ${urlCompleta}`;
                }
                
                mensagem += `\n\nGostaria de saber mais detalhes? Estou à disposição para esclarecer qualquer dúvida!\n\nObrigado pela confiança! 🚴‍♂️`;
                
                const linkWhatsApp = `https://wa.me/55${telefone}?text=${encodeURIComponent(mensagem)}`;
                window.open(linkWhatsApp, '_blank');
            };
            
            // Fechar modal
            window.fecharModalSugestoes = function() {
                document.getElementById('modalSugestoes').style.display = 'none';
            };

            // Chamar todas as funções de carregamento
            carregarAlertasEstoque();
            carregarTopServicos();
            carregarTopProdutos();
            carregarClientesFrequentes();
            carregarLembretesRevisao(); // NOVO
            carregarPesquisasSatisfacao(); // NOVO
            carregarFaturamentoMensal();
        });
    </script>
    
    <!-- Modal de Sugestões -->
    <div id="modalSugestoes" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 800px; max-height: 80vh; overflow-y: auto;">
            <div class="modal-header">
                <h2><i class="fa-solid fa-lightbulb"></i> Clientes com Sugestões</h2>
                <span class="close" onclick="fecharModalSugestoes()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Produto: <strong><span id="produtoNomeSugestoes"></span></strong></p>
                <p style="color: #666; margin-bottom: 20px;">Clientes que compraram produtos relacionados e podem ter interesse neste item:</p>
                <div id="clientesSugestoesList"></div>
            </div>
        </div>
    </div>
    
    <style>
        .cliente-sugestao-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        
        .cliente-info h4 {
            margin: 0 0 8px 0;
            color: #333;
        }
        
        .cliente-info p {
            margin: 4px 0;
            font-size: 0.9rem;
            color: #666;
        }
        
        .btn-whatsapp {
            background-color: #25d366;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        
        .btn-whatsapp:hover {
            background-color: #1da851;
        }
        
        .sem-whatsapp {
            color: #999;
            font-style: italic;
            font-size: 0.9rem;
        }
        
        .modal {
            display: none;
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
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #ddd;
            background-color: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }
        
        .modal-header h2 {
            margin: 0;
            color: #333;
        }
        
        .modal-body {
            padding: 20px;
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
    </style>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>
