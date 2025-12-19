<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban de Serviços | BikeOS</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* --- ESTILOS ATUALIZADOS --- */
        body {
            background-color: #f4f7f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
            /* Previne scroll horizontal na página inteira */
        }

        .container {
            max-width: 100%;
            padding: 0 0.5rem;
            /* Reduz o padding lateral em telas menores */
        }

        .kanban-board {
            display: grid;
            /* Layout inicial de uma coluna para celulares na vertical */
            grid-template-columns: 1fr;
            gap: 1rem;
            padding: 1rem 0;
        }

        /* Layout para telas pequenas na horizontal (celulares) e tablets na vertical */
        @media (min-width: 600px) {
            .kanban-board {
                /* Muda para flexbox para permitir scroll horizontal, ideal para tablet na vertical ou celular na horizontal */
                display: flex;
                overflow-x: auto;
                padding-bottom: 15px;
                /* Espaço para a barra de scroll não sobrepor o conteúdo */
                scroll-snap-type: x mandatory;
                /* Efeito de "parada" suave ao rolar */
            }

            .kanban-column {
                min-width: 320px;
                /* Largura mínima das colunas */
                flex-shrink: 0;
                scroll-snap-align: start;
            }

            .kanban-cards {
                display: block !important;
                /* Garante que os cards fiquem visíveis */
                max-height: none !important;
            }

            .kanban-column h2 {
                cursor: default;
                /* Remove o cursor de clique em telas maiores */
            }
        }

        /* Layout para telas grandes (tablets na horizontal e desktops) */
        @media (min-width: 1280px) {
            .kanban-board {
                display: grid;
                /* Volta para grid com 4 colunas para não ter scroll */
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .kanban-column {
            background-color: #e9ecef;
            border-radius: var(--raio-borda);
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }

        .kanban-column h2 {
            font-size: 1.25rem;
            /* Fonte do título maior e mais nítida */
            text-align: center;
            margin: 0 0 1rem 0;
            padding-bottom: 1rem;
            border-bottom: 3px solid;
            flex-shrink: 0;
            cursor: pointer;
            /* Adiciona cursor de clique para a funcionalidade de acordeão */
            user-select: none;
            /* Impede que o texto seja selecionado ao clicar */
        }

        #coluna-ORCAMENTO h2 {
            border-color: #6c757d;
        }

        #coluna-AGUARDANDO_PECA h2 {
            border-color: #fd7e14;
        }

        #coluna-EM_SERVICO h2 {
            border-color: var(--cor-primaria);
        }

        #coluna-PRONTA h2 {
            border-color: #ffc107;
        }

        .kanban-cards {
            flex-grow: 1;
            overflow-y: hidden;
            max-height: 0;
            /* Começa recolhido em telas pequenas */
            transition: max-height 0.3s ease-out;
            /* Animação suave */
        }

        .kanban-column.expanded .kanban-cards {
            max-height: 100vh;
            /* Altura máxima para expandir */
            overflow-y: auto;
        }


        .kanban-card {
            background-color: var(--cor-branco);
            border-radius: var(--raio-borda);
            padding: 1.25rem;
            /* Mais espaço interno */
            margin-bottom: 1rem;
            box-shadow: var(--sombra-card);
            cursor: grab;
            border-left: 6px solid transparent;
            /* Borda lateral mais espessa */
        }

        .kanban-card:active {
            cursor: grabbing;
        }

        .kanban-card p {
            margin: 0 0 0.6rem 0;
            word-wrap: break-word;
        }

        .card-title {
            font-weight: 700;
            /* Mais negrito */
            font-size: 1.2rem;
            /* Fonte maior para o nome do cliente */
        }

        .card-subtitle {
            font-size: 1rem;
            /* Fonte maior para o modelo da bike */
            color: var(--cor-texto-secundario);
            font-weight: 500;
        }

        .card-problem {
            font-size: 1rem;
            /* Fonte maior para o problema */
            font-style: italic;
            color: #495057;
            border-top: 1px solid #eee;
            padding-top: 0.75rem;
            margin-top: 0.75rem;
        }

        .card-footer {
            font-size: 0.9rem;
            /* Fonte maior para o rodapé */
            color: #6c757d;
            border-top: 1px solid var(--cor-borda);
            padding-top: 0.75rem;
            margin-top: 0.75rem;
            font-weight: 500;
        }

        .dragging {
            opacity: 0.5;
        }

        .drag-over {
            background-color: #d1e7dd;
        }

        .card-prazo-ok {
            border-left-color: var(--cor-sucesso);
        }

        .card-prazo-hoje {
            border-left-color: var(--cor-alerta);
        }

        .card-prazo-atrasado {
            border-left-color: var(--cor-erro);
        }

        /* Estilos para o cabeçalho e campo de busca */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .page-header h1 {
            margin: 0;
            color: var(--cor-texto-primario);
            font-size: 1.8rem;
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

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .search-container {
                justify-content: center;
            }

            .search-container input {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php"><img src="assets/img/images.jpeg" alt="Logo" class="navbar-logo"></a>

        <div class="navbar-user-info"><i class="fa-solid fa-user-circle"></i><span>Mecânico: <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></div>
        <a href="logout.php" class="btn btn-destaque"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
    </nav>
    <main class="container">
        <div class="page-header">
            <h1><i class="fa-solid fa-columns"></i> Kanban de Serviços</h1>
            <div class="search-container">
                <input type="text" id="search-kanban" placeholder="Buscar serviços...">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
            </div>
        </div>
        <div id="kanban-board" class="kanban-board">
            <div class="kanban-column" id="coluna-ORCAMENTO" data-status="ORCAMENTO">
                <h2><i class="fa-solid fa-file-invoice-dollar"></i> Orçamento</h2>
                <div class="kanban-cards"></div>
            </div>
            <div class="kanban-column" id="coluna-AGUARDANDO_PECA" data-status="AGUARDANDO_PECA">
                <h2><i class="fa-solid fa-box"></i> Aguardando Peça</h2>
                <div class="kanban-cards"></div>
            </div>
            <div class="kanban-column" id="coluna-EM_SERVICO" data-status="EM_SERVICO">
                <h2><i class="fa-solid fa-gears"></i> Em Serviço</h2>
                <div class="kanban-cards"></div>
            </div>
            <div class="kanban-column" id="coluna-PRONTA" data-status="PRONTA">
                <h2><i class="fa-solid fa-flag-checkered"></i> Pronta para Retirada</h2>
                <div class="kanban-cards"></div>
            </div>
        </div>
    </main>

    <?php include '_partials/menu_admin.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const kanbanBoard = document.getElementById('kanban-board');
            const columns = document.querySelectorAll('.kanban-column');
            const searchInput = document.getElementById('search-kanban');
            let draggedCard = null;
            let todosOsCards = [];

            const fetchAndRenderBoard = async () => {
                try {
                    const response = await fetch('api/api_kanban.php?acao=get_os_kanban');
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);

                    // Armazenar todos os dados para busca
                    todosOsCards = [];
                    for (const status in resultado.dados) {
                        todosOsCards = todosOsCards.concat(resultado.dados[status]);
                    }

                    renderizarCards(resultado.dados);
                } catch (error) {
                    console.error('Erro ao carregar kanban:', error);
                }
            };

            const renderizarCards = (data) => {
                columns.forEach(col => col.querySelector('.kanban-cards').innerHTML = '');

                const hoje = new Date();
                hoje.setHours(0, 0, 0, 0);

                    for (const status in data) {
                        const column = document.getElementById(`coluna-${status}`);
                        if (column) {
                            const cardsContainer = column.querySelector('.kanban-cards');
                            data[status].forEach(os => {
                                const card = document.createElement('div');
                                card.className = 'kanban-card';
                                card.draggable = true;
                                card.dataset.osId = os.id;
                                card.dataset.clienteNome = os.cliente_nome;
                                card.dataset.clienteTelefone = os.cliente_telefone;
                                card.dataset.codigoRetirada = os.codigo_retirada;
                                card.dataset.bicicletaModelo = os.bicicleta_modelo;

                                const previsao = new Date(os.previsao_entrega + 'T00:00:00');
                                const previsaoFormatada = previsao.toLocaleDateString('pt-BR');

                                if (previsao < hoje) {
                                    card.classList.add('card-prazo-atrasado');
                                } else if (previsao.getTime() === hoje.getTime()) {
                                    card.classList.add('card-prazo-hoje');
                                } else {
                                    card.classList.add('card-prazo-ok');
                                }

                                card.innerHTML = `
                                    <p class="card-title">${os.cliente_nome}</p>
                                    <p class="card-subtitle">${os.bicicleta_modelo}</p>
                                    <p class="card-problem">${os.problema_relatado || 'Nenhum problema relatado.'}</p>
                                    <div class="card-footer">
                                        <span>OS #${os.id}</span> | <span>Entrega: ${previsaoFormatada}</span>
                                    </div>
                                `;
                                cardsContainer.appendChild(card);
                            });
                        }
                    }
            };

            const filtrarCards = (termo) => {
                const termoLower = termo.toLowerCase();
                const cardsFiltrados = {};
                
                // Inicializar objeto com arrays vazios para cada status
                ['ORCAMENTO', 'AGUARDANDO_PECA', 'EM_SERVICO', 'PRONTA'].forEach(status => {
                    cardsFiltrados[status] = [];
                });
                
                // Filtrar cards baseado no termo de busca
                todosOsCards.forEach(os => {
                    const textoCard = `${os.cliente_nome} ${os.bicicleta_modelo} ${os.problema_relatado || ''} OS #${os.id}`.toLowerCase();
                    if (textoCard.includes(termoLower)) {
                        cardsFiltrados[os.status].push(os);
                    }
                });
                
                renderizarCards(cardsFiltrados);
            };

            const enviarNotificacaoWhatsApp = (card, novoStatus) => {
                if (!card) return;
                const clienteTelefone = card.dataset.clienteTelefone;
                if (!clienteTelefone) {
                    console.log("Cliente sem telefone para notificar.");
                    return;
                }

                const nomeCliente = card.dataset.clienteNome.split(' ')[0];
                const osId = card.dataset.osId;
                const bikeModelo = card.dataset.bicicletaModelo;
                const codigoRetirada = card.dataset.codigoRetirada;
                const statusTexto = novoStatus.replace(/_/g, ' ');

                let mensagem = `Olá, ${nomeCliente}! Sua OS #${osId} (${bikeModelo}) foi atualizada.\n\nNovo Status: *${statusTexto}*`;

                if (novoStatus === 'PRONTA' && codigoRetirada) {
                    mensagem += `\n\nSua bicicleta está pronta para retirada! Apresente o código *${codigoRetirada}* no balcão.`;
                }

                const linkAcompanhamento = `${window.location.origin}/acompanhar_os.php?id=${osId}&codigo=${codigoRetirada}`;
                mensagem += `\n\nAcompanhe os detalhes aqui: ${linkAcompanhamento}`;

                const telefoneFormatado = '55' + clienteTelefone.replace(/\D/g, '');
                window.open(`https://wa.me/${telefoneFormatado}?text=${encodeURIComponent(mensagem)}`, '_blank');
            };

            kanbanBoard.addEventListener('dragstart', (e) => {
                if (e.target.classList.contains('kanban-card')) {
                    draggedCard = e.target;
                    setTimeout(() => e.target.classList.add('dragging'), 0);
                }
            });

            kanbanBoard.addEventListener('dragend', (e) => {
                if (draggedCard) {
                    draggedCard.classList.remove('dragging');
                }
                draggedCard = null;
            });

            columns.forEach(column => {
                column.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    column.classList.add('drag-over');
                });
                column.addEventListener('dragleave', () => {
                    column.classList.remove('drag-over');
                });
                column.addEventListener('drop', async (e) => {
                    e.preventDefault();
                    column.classList.remove('drag-over');

                    const currentCard = draggedCard;
                    if (currentCard) {
                        const osId = currentCard.dataset.osId;
                        const novoStatus = column.dataset.status;

                        try {
                            const response = await fetch('api/api_kanban.php?acao=update_os_status', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    os_id: osId,
                                    novo_status: novoStatus
                                })
                            });
                            const resultado = await response.json();
                            if (!resultado.sucesso) throw new Error(resultado.mensagem);

                            enviarNotificacaoWhatsApp(currentCard, novoStatus);
                            fetchAndRenderBoard();
                        } catch (error) {
                            alert('Erro ao atualizar status: ' + error.message);
                            fetchAndRenderBoard();
                        }
                    }
                });
            });

            kanbanBoard.addEventListener('click', (e) => {
                const card = e.target.closest('.kanban-card');
                if (card) {
                    const osId = card.dataset.osId;
                    window.location.href = `editar_os.php?id=${osId}`;
                }
            });

            // Lógica do Acordeão para telas pequenas
            columns.forEach(column => {
                const header = column.querySelector('h2');
                header.addEventListener('click', () => {
                    // Só ativa o acordeão se o layout não for de 4 colunas (telas maiores)
                    if (window.innerWidth < 1280) {
                        const isExpanded = column.classList.contains('expanded');

                        // Fecha todas as colunas
                        columns.forEach(col => col.classList.remove('expanded'));

                        // Abre a coluna clicada (se ela não estava aberta)
                        if (!isExpanded) {
                            column.classList.add('expanded');
                        }
                    }
                });
            });

            // Event listener para o campo de busca
            searchInput.addEventListener('input', (e) => {
                const termo = e.target.value.trim();
                if (termo === '') {
                    // Se não há termo de busca, mostrar todos os dados originais
                    const dadosCompletos = {};
                    ['ORCAMENTO', 'AGUARDANDO_PECA', 'EM_SERVICO', 'PRONTA'].forEach(status => {
                        dadosCompletos[status] = todosOsCards.filter(os => os.status === status);
                    });
                    renderizarCards(dadosCompletos);
                } else {
                    filtrarCards(termo);
                }
            });

            fetchAndRenderBoard();
        });
    </script>
    
    <!-- Sistema de Controle de Zoom -->

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>