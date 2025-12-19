<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Manual do Sistema'); ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .manual-container {
            display: flex;
            gap: 2rem;
        }
        .manual-sidebar {
            position: sticky;
            top: 2rem;
            width: 250px;
            flex-shrink: 0;
            height: calc(100vh - 4rem);
        }
        .manual-sidebar ul {
            list-style: none;
            padding: 0;
        }
        .manual-sidebar a {
            display: block;
            padding: 0.75rem 1rem;
            border-radius: var(--raio-borda);
            text-decoration: none;
            color: var(--cor-texto-principal);
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .manual-sidebar a:hover {
            background-color: #e9ecef;
            color: var(--cor-primaria);
        }
        .manual-sidebar a.active {
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--cor-primaria);
            font-weight: 600;
            border-left: 3px solid var(--cor-primaria);
        }
        .manual-content {
            flex-grow: 1;
        }
        .manual-content section {
            margin-bottom: 4rem;
            padding-top: 2rem;
            margin-top: -2rem;
        }
        .manual-content img, .manual-content .gif-placeholder {
            max-width: 100%;
            border-radius: var(--raio-borda);
            box-shadow: var(--sombra-card);
            margin: 1.5rem 0;
            border: 1px solid var(--cor-borda);
        }
        .quiz-question {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: #fdfdfe;
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            transition: all 0.3s ease;
        }
        .quiz-options {
            margin: 0.5rem 0;
        }
        .quiz-options label {
            display: block;
            padding: 0.75rem;
            border-radius: 5px;
            margin-top: 0.5rem;
            cursor: pointer;
            background: white;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .quiz-options label:hover {
            background: #e9ecef;
            border-color: #007bff;
        }
        .quiz-options label.correcta { 
            background-color: #d4edda !important; 
            border-color: #28a745 !important;
            color: #155724;
            font-weight: bold;
        }
        .quiz-options label.incorreta { 
            background-color: #f8d7da !important; 
            border-color: #dc3545 !important;
            color: #721c24;
        }
        .quiz-options label.correcta::before {
            content: '✓ ';
            color: #28a745;
            font-weight: bold;
        }
        .quiz-options label.incorreta::before {
            content: '✗ ';
            color: #dc3545;
            font-weight: bold;
        }
        #quiz-resultado {
            margin-top: 2rem;
            padding: 1rem;
            border-radius: var(--raio-borda);
        }
        .alert {
            padding: 1rem;
            margin: 0.5rem 0;
            border-radius: 8px;
            border: 1px solid transparent;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        .alert-primary {
            background-color: #d1ecf1;
            border-color: #b8daff;
            color: #004085;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php"><img src="assets/img/logo.png" alt="Logo" class="navbar-logo"></a>
        
        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php" class="btn btn-destaque">Sair</a>
    </nav>
    <main class="container">
        <div class="page-header-with-nav">
            <div class="page-title">
                <h1><i class="fa-solid fa-book-open"></i> Manual do Sistema</h1>
            </div>
            <div class="page-navigation">
                <button onclick="history.back()" class="btn btn-secundario" title="Voltar à página anterior">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </button>
                <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Ir para o Painel Principal">
                    <i class="fa-solid fa-house"></i>
                </a>
            </div>
        </div>
        <div class="manual-container">
            
            <aside class="manual-sidebar">
                <div class="card">
                    <ul>
                        <li><a href="#introducao" class="active">Introdução</a></li>
                        <li><a href="#dashboard">📊 Dashboard Principal</a></li>
                        <li><a href="#gestao-clientes">👥 Gestão de Clientes</a></li>
                        <li><a href="#gestao-produtos">📦 Gestão de Produtos</a></li>
                        <li><a href="#gestao-estoque">📊 Controle de Estoque</a></li>
                        <li><a href="#vendas">💰 Sistema de Vendas (PDV)</a></li>
                        <li><a href="#os">🔧 Ordens de Serviço</a></li>
                        <li><a href="#editando-os">Editando e Finalizando OS</a></li>
                        <li><a href="#financeiro">💰 Financeiro</a></li>
                        <li><a href="#relatorios">📈 Relatórios</a></li>
                        <li><a href="#configuracoes">⚙️ Configurações</a></li>
                        <li><a href="#recursos-avancados">🚀 Recursos Avançados</a></li>
                        <li><a href="#faq">❓ Perguntas Frequentes</a></li>
                        <li><a href="#suporte">🆘 Suporte e Contato</a></li>
                        <li><a href="#quiz">Teste seu Conhecimento</a></li>
                    </ul>
                </div>
            </aside>

            <article class="manual-content">
                <div class="card">
                    <section id="introducao">
                        <h2>🚴‍♂️ Bem-vindo ao Manual Completo do BikeOS!</h2>
                        <p>Este guia foi criado para ajudar você a utilizar <strong>todas as funcionalidades</strong> do sistema de gestão mais completo para oficinas de bicicletas. Aqui você encontrará explicações detalhadas, passo a passo, para dominar completamente o sistema.</p>
                        
                        <div class="alert alert-info">
                            <h3>📋 O que você vai aprender:</h3>
                            <ul>
                                <li>✅ Como navegar pelo Dashboard e interpretar os indicadores</li>
                                <li>✅ Gestão completa de clientes e fornecedores</li>
                                <li>✅ Controle total de produtos e estoque</li>
                                <li>✅ Sistema de vendas (PDV) profissional</li>
                                <li>✅ Ordens de serviço do início ao fim</li>
                                <li>✅ Controle financeiro e relatórios avançados</li>
                                <li>✅ Configurações e personalizações</li>
                                <li>✅ Recursos avançados e automações</li>
                            </ul>
                        </div>
                        
                        <p><strong>💡 Dica:</strong> Use o menu à esquerda para navegar rapidamente entre as seções. Cada seção contém exemplos práticos e dicas importantes!</p>
                    </section>

                    <section id="dashboard">
                        <h2>O Dashboard: Sua Visão Geral</h2>
                        <p>O Dashboard é a primeira tela que você vê ao entrar no sistema. Ele oferece uma visão rápida e resumida do estado atual da oficina.</p>
                        <ul>
                            <li><strong>KPIs (Indicadores):</strong> Mostram o total de OS Ativas, Faturamento do Dia e Bikes Prontas.</li>
                            <li><strong>Agenda de Entregas do Dia:</strong> Lista as bicicletas com previsão de entrega para hoje.</li>
                            <li><strong>Lista de Alertas:</strong> Avisa sobre produtos com estoque baixo e contas de crediário vencidas.</li>
                        </ul>
                        <img src="assets/img/manual/dashboard.png" alt="Screenshot do Dashboard">
                    </section>
                    
                    <section id="gestao-clientes">
                        <h2>👥 Gestão Completa de Clientes</h2>
                        <p>O módulo de clientes é fundamental para manter um relacionamento próximo com seus clientes e ter controle total sobre o histórico de cada um.</p>
                        
                        <h3>📝 Como Cadastrar um Novo Cliente</h3>
                        <ol>
                            <li>Acesse <strong>Menu Administrativo > Clientes/Fornecedores</strong></li>
                            <li>Clique no botão <strong>"+ Novo Cliente"</strong></li>
                            <li>Preencha os dados obrigatórios:
                                <ul>
                                    <li><strong>Nome Completo:</strong> Nome do cliente</li>
                                    <li><strong>Telefone:</strong> Para contato e WhatsApp</li>
                                    <li><strong>Email:</strong> Para envio de orçamentos</li>
                                    <li><strong>CPF:</strong> Para vendas no crediário</li>
                                    <li><strong>Data de Nascimento:</strong> Para campanhas de aniversário</li>
                                    <li><strong>Endereço:</strong> Para entregas</li>
                                </ul>
                            </li>
                            <li>Clique em <strong>"Salvar Cliente"</strong></li>
                        </ol>
                        
                        <h3>🔍 Buscar e Gerenciar Clientes</h3>
                        <ul>
                            <li><strong>Busca Rápida:</strong> Use o campo de busca para encontrar por nome, telefone ou email</li>
                            <li><strong>Visualizar Histórico:</strong> Clique no ícone de olho para ver:
                                <ul>
                                    <li>Histórico de compras</li>
                                    <li>Ordens de serviço realizadas</li>
                                    <li>Bicicletas cadastradas</li>
                                    <li>Contas em crediário</li>
                                </ul>
                            </li>
                            <li><strong>Editar Dados:</strong> Clique no ícone de lápis para atualizar informações</li>
                            <li><strong>Excluir Cliente:</strong> Clique no ícone de lixeira (apenas se não houver histórico)</li>
                        </ul>
                        
                        <h3>🚚 Gestão de Fornecedores</h3>
                        <p>Na mesma tela, você pode gerenciar fornecedores clicando no botão <strong>"Fornecedores"</strong>:</p>
                        <ul>
                            <li>Cadastre fornecedores de peças e bicicletas</li>
                            <li>Mantenha dados de contato atualizados</li>
                            <li>Vincule produtos aos fornecedores</li>
                        </ul>
                    </section>

                    <section id="gestao-produtos">
                        <h2>📦 Gestão de Produtos e Peças</h2>
                        <p>O controle de produtos é essencial para manter seu estoque organizado e suas vendas eficientes.</p>
                        
                        <h3>➕ Como Cadastrar um Novo Produto</h3>
                        <ol>
                            <li>Acesse <strong>Menu Administrativo > Produtos</strong></li>
                            <li>Clique em <strong>"+ Novo Produto"</strong></li>
                            <li>Preencha as informações:
                                <ul>
                                    <li><strong>Nome:</strong> Nome do produto/peça</li>
                                    <li><strong>SKU/Código:</strong> Código único para identificação</li>
                                    <li><strong>Marca:</strong> Fabricante do produto</li>
                                    <li><strong>Tipo:</strong> Categoria (Peça, Acessório, Roupa, Calçado, Bicicleta)</li>
                                    <li><strong>Descrição:</strong> Detalhes técnicos</li>
                                    <li><strong>Preço de Custo:</strong> Valor que você paga</li>
                                    <li><strong>Preço de Venda:</strong> Valor para o cliente</li>
                                    <li><strong>Estoque Atual:</strong> Quantidade disponível</li>
                                    <li><strong>Estoque Mínimo:</strong> Alerta de reposição</li>
                                    <li><strong>Fornecedor:</strong> Quem fornece o produto</li>
                                </ul>
                            </li>
                            <li>Para <strong>roupas e calçados</strong>, adicione as variações de tamanho</li>
                            <li>Adicione uma imagem do produto (opcional)</li>
                            <li>Clique em <strong>"Salvar Produto"</strong></li>
                        </ol>
                        
                        <h3>🔧 Recursos Especiais</h3>
                        <ul>
                            <li><strong>Sugestões Inteligentes:</strong> O sistema sugere produtos relacionados baseado no histórico de vendas</li>
                            <li><strong>Controle de Variações:</strong> Para roupas e calçados, controle estoque por tamanho</li>
                            <li><strong>Busca Avançada:</strong> Encontre produtos por nome, SKU, marca ou categoria</li>
                            <li><strong>Importação em Lote:</strong> Importe vários produtos via planilha</li>
                        </ul>
                        
                        <h3>💡 Dicas Importantes</h3>
                        <div class="alert alert-warning">
                            <ul>
                                <li>Mantenha sempre o estoque mínimo configurado para receber alertas</li>
                                <li>Use códigos SKU únicos para facilitar a busca</li>
                                <li>Atualize preços regularmente conforme o mercado</li>
                                <li>Adicione imagens para facilitar a identificação</li>
                            </ul>
                        </div>
                    </section>

                    <section id="gestao-estoque">
                        <h2>📊 Controle de Estoque</h2>
                        <p>O módulo de estoque permite controlar entradas, saídas e ter visibilidade completa dos seus produtos.</p>
                        
                        <h3>📥 Entrada de Mercadorias</h3>
                        <ol>
                            <li>Acesse <strong>Menu Administrativo > Estoque</strong></li>
                            <li>Clique em <strong>"+ Nova Entrada"</strong></li>
                            <li>Selecione o <strong>fornecedor</strong></li>
                            <li>Adicione os produtos recebidos:
                                <ul>
                                    <li>Busque o produto na lista</li>
                                    <li>Informe a quantidade recebida</li>
                                    <li>Confirme ou ajuste o preço de custo</li>
                                </ul>
                            </li>
                            <li>Revise o total da compra</li>
                            <li>Clique em <strong>"Registrar Entrada"</strong></li>
                        </ol>
                        
                        <h3>⚠️ Alertas de Estoque</h3>
                        <p>O sistema monitora automaticamente seu estoque e emite alertas quando:</p>
                        <ul>
                            <li><strong>Estoque Baixo:</strong> Produto atingiu o estoque mínimo</li>
                            <li><strong>Produto Zerado:</strong> Não há mais unidades disponíveis</li>
                            <li><strong>Produtos Parados:</strong> Itens sem movimentação há muito tempo</li>
                        </ul>
                        
                        <h3>📋 Relatórios de Estoque</h3>
                        <ul>
                            <li><strong>Posição Atual:</strong> Visualize todo o estoque com valores</li>
                            <li><strong>Movimentações:</strong> Histórico de entradas e saídas</li>
                            <li><strong>Produtos Críticos:</strong> Lista de itens que precisam de reposição</li>
                            <li><strong>Análise ABC:</strong> Produtos mais importantes por faturamento</li>
                        </ul>
                        
                        <h3>🤖 Sugestão Automática de Pedidos</h3>
                        <p>O sistema pode gerar automaticamente sugestões de pedidos baseado em:</p>
                        <ul>
                            <li>Histórico de vendas</li>
                            <li>Estoque mínimo configurado</li>
                            <li>Sazonalidade dos produtos</li>
                            <li>Tempo de entrega dos fornecedores</li>
                        </ul>
                    </section>

                    <section id="vendas">
                        <h2>💰 Sistema de Vendas (PDV)</h2>
                        <p>O Ponto de Venda foi projetado para ser rápido, intuitivo e profissional. Ideal para atendimento ágil aos clientes.</p>
                        
                        <h3>🛒 Como Realizar uma Venda Completa</h3>
                        <ol>
                            <li><strong>Iniciar Venda:</strong> No Dashboard, clique em <strong>"Nova Venda"</strong></li>
                            <li><strong>Adicionar Produtos:</strong>
                                <ul>
                                    <li>Use o campo de busca para encontrar produtos</li>
                                    <li>Ou clique diretamente nos produtos em destaque</li>
                                    <li>Ajuste quantidade e preço se necessário</li>
                                    <li>Clique em <strong>"Adicionar ao Carrinho"</strong></li>
                                </ul>
                            </li>
                            <li><strong>Selecionar Cliente:</strong>
                                <ul>
                                    <li>Busque o cliente pelo nome ou telefone</li>
                                    <li>Se não existir, clique em <strong>"Cadastrar Novo"</strong></li>
                                    <li>Para venda anônima, deixe em branco</li>
                                </ul>
                            </li>
                            <li><strong>Escolher Pagamento:</strong>
                                <ul>
                                    <li><strong>Dinheiro:</strong> Informe valor recebido para calcular troco</li>
                                    <li><strong>PIX:</strong> Gere QR Code automático</li>
                                    <li><strong>Cartão:</strong> Débito ou crédito</li>
                                    <li><strong>Crediário:</strong> Para clientes cadastrados</li>
                                </ul>
                            </li>
                            <li><strong>Finalizar:</strong> Clique em <strong>"Concluir Venda"</strong></li>
                            <li><strong>Imprimir:</strong> Recibo será gerado automaticamente</li>
                        </ol>
                        
                        <h3>💳 Formas de Pagamento Disponíveis</h3>
                        <ul>
                            <li><strong>💵 Dinheiro:</strong> Cálculo automático de troco</li>
                            <li><strong>📱 PIX:</strong> Geração de QR Code com valor</li>
                            <li><strong>💳 Cartão de Débito:</strong> Pagamento à vista</li>
                            <li><strong>💳 Cartão de Crédito:</strong> Parcelamento disponível</li>
                            <li><strong>📋 Crediário:</strong> Pagamento a prazo para clientes</li>
                            <li><strong>🔄 Misto:</strong> Combinação de formas de pagamento</li>
                        </ul>
                        
                        <h3>🎯 Recursos Especiais do PDV</h3>
                        <ul>
                            <li><strong>Busca Inteligente:</strong> Encontre produtos por nome, código ou marca</li>
                            <li><strong>Desconto por Item:</strong> Aplique descontos específicos</li>
                            <li><strong>Desconto Geral:</strong> Desconto em toda a venda</li>
                            <li><strong>Sugestões de Venda:</strong> Produtos relacionados aparecem automaticamente</li>
                            <li><strong>Histórico do Cliente:</strong> Veja compras anteriores durante o atendimento</li>
                            <li><strong>Impressão Térmica:</strong> Recibos otimizados para impressoras térmicas</li>
                        </ul>
                        
                        <div class="alert alert-success">
                            <h4>💡 Dica Pro:</h4>
                            <p>Use as teclas de atalho para agilizar o atendimento:</p>
                            <ul>
                                <li><strong>F1:</strong> Nova venda</li>
                                <li><strong>F2:</strong> Buscar produto</li>
                                <li><strong>F3:</strong> Buscar cliente</li>
                                <li><strong>Enter:</strong> Finalizar venda</li>
                            </ul>
                        </div>
                    </section>

                    <section id="os">
                        <h2>🔧 Ordens de Serviço Completas</h2>
                        <p>O módulo de OS é o coração da sua oficina, permitindo controle total desde o recebimento até a entrega da bicicleta.</p>
                        
                        <h3>📝 Como Criar uma Nova Ordem de Serviço</h3>
                        <ol>
                            <li><strong>Iniciar OS:</strong> No Dashboard, clique em <strong>"Nova OS"</strong></li>
                            <li><strong>Dados do Cliente:</strong>
                                <ul>
                                    <li>Busque o cliente existente</li>
                                    <li>Ou cadastre um novo cliente</li>
                                    <li>Confirme dados de contato</li>
                                </ul>
                            </li>
                            <li><strong>Dados da Bicicleta:</strong>
                                <ul>
                                    <li>Selecione uma bicicleta já cadastrada</li>
                                    <li>Ou adicione uma nova bicicleta</li>
                                    <li>Informe marca, modelo, cor e número de série</li>
                                </ul>
                            </li>
                            <li><strong>Diagnóstico Inicial:</strong>
                                <ul>
                                    <li>Descreva o problema relatado pelo cliente</li>
                                    <li>Adicione observações técnicas</li>
                                    <li>Tire fotos das avarias (opcional)</li>
                                </ul>
                            </li>
                            <li><strong>Serviços e Peças:</strong>
                                <ul>
                                    <li>Adicione os serviços que serão realizados</li>
                                    <li>Inclua peças necessárias</li>
                                    <li>Defina preços e quantidades</li>
                                </ul>
                            </li>
                            <li><strong>Prazo e Prioridade:</strong>
                                <ul>
                                    <li>Defina data prevista de entrega</li>
                                    <li>Configure prioridade (Normal, Urgente, Expressa)</li>
                                </ul>
                            </li>
                            <li><strong>Salvar:</strong> Clique em <strong>"Criar OS"</strong></li>
                        </ol>
                        
                        <h3>📊 Controle de Status da OS</h3>
                        <p>Acompanhe cada etapa do serviço com os status disponíveis:</p>
                        <ul>
                            <li><strong>🟡 Aguardando:</strong> OS criada, aguardando início dos trabalhos</li>
                            <li><strong>🔵 Em Andamento:</strong> Serviço sendo executado</li>
                            <li><strong>🟠 Aguardando Peça:</strong> Parado esperando chegada de peças</li>
                            <li><strong>🟣 Aguardando Aprovação:</strong> Orçamento enviado ao cliente</li>
                            <li><strong>🟢 Pronta:</strong> Serviço concluído, aguardando retirada</li>
                            <li><strong>✅ Entregue:</strong> Bicicleta entregue ao cliente</li>
                            <li><strong>❌ Cancelada:</strong> OS cancelada</li>
                        </ul>
                        
                        <h3>📱 Notificações Automáticas</h3>
                        <p>O sistema pode enviar notificações automáticas via WhatsApp quando:</p>
                        <ul>
                            <li>OS é criada (confirmação para o cliente)</li>
                            <li>Status é atualizado</li>
                            <li>Bicicleta fica pronta para retirada</li>
                            <li>Orçamento é enviado para aprovação</li>
                        </ul>
                        
                        <h3>💰 Finalizando uma OS</h3>
                        <ol>
                            <li>Atualize o status para <strong>"Pronta"</strong></li>
                            <li>Revise todos os serviços e peças</li>
                            <li>Confirme o valor total</li>
                            <li>Clique em <strong>"Finalizar OS"</strong></li>
                            <li>Escolha a forma de pagamento</li>
                            <li>Gere o recibo de entrega</li>
                            <li>Atualize status para <strong>"Entregue"</strong></li>
                        </ol>
                        
                        <h3>📋 Recursos Avançados</h3>
                        <ul>
                            <li><strong>Histórico da Bicicleta:</strong> Veja todos os serviços já realizados</li>
                            <li><strong>Fotos de Avarias:</strong> Documente problemas encontrados</li>
                            <li><strong>Orçamentos Detalhados:</strong> Envie orçamentos profissionais</li>
                            <li><strong>Lembretes de Revisão:</strong> Configure alertas para manutenção preventiva</li>
                            <li><strong>Relatórios de Produtividade:</strong> Acompanhe performance da equipe</li>
                        </ul>
                        <img src="assets/img/manual/criando_os.gif" alt="GIF mostrando a criação de uma OS">
                    </section>

                    <section id="editando-os">
                        <h2>Editando e Finalizando uma OS</h2>
                        <p>A tela de edição da OS é onde a mágica acontece. Aqui você adiciona peças, serviços, atualiza o status e finaliza o trabalho.</p>
                        <ul>
                            <li><strong>Adicionar Itens:</strong> Use os campos de busca para adicionar peças e serviços. O resumo financeiro é atualizado automaticamente.</li>
                            <li><strong>Atualizar Status:</strong> Mude o status da OS (ex: de 'Em Serviço' para 'Pronta'). Ao salvar, você terá a opção de notificar o cliente via WhatsApp.</li>
                            <li><strong>Finalização:</strong> Mude o status para 'FINALIZADA' e clique em 'Atualizar OS'. Um modal aparecerá para confirmar os dados do cliente (se for crediário), o método de pagamento e o código de retirada.</li>
                        </ul>
                        <img src="assets/img/manual/finalizando_os_crediario.gif" alt="GIF mostrando a finalização de uma OS no crediário">
                    </section>

                    <section id="financeiro">
                        <h2>💰 Controle Financeiro Completo</h2>
                        <p>O módulo financeiro oferece controle total sobre as finanças da sua oficina, desde o caixa diário até análises avançadas de performance.</p>
                        
                        <h3>💵 Gestão de Caixa</h3>
                        <p>Controle rigoroso de todas as movimentações financeiras:</p>
                        <ol>
                            <li><strong>Abertura do Caixa:</strong>
                                <ul>
                                    <li>Acesse <strong>Financeiro > Caixa</strong></li>
                                    <li>Clique em <strong>"Abrir Caixa"</strong></li>
                                    <li>Informe o valor inicial em dinheiro</li>
                                    <li>Confirme a abertura</li>
                                </ul>
                            </li>
                            <li><strong>Movimentações:</strong>
                                <ul>
                                    <li><strong>Entradas:</strong> Vendas, recebimentos, sangria</li>
                                    <li><strong>Saídas:</strong> Retiradas, pagamentos, despesas</li>
                                    <li><strong>Registro Automático:</strong> Vendas são registradas automaticamente</li>
                                </ul>
                            </li>
                            <li><strong>Fechamento:</strong>
                                <ul>
                                    <li>Confira o saldo final</li>
                                    <li>Compare com o dinheiro físico</li>
                                    <li>Registre diferenças se houver</li>
                                    <li>Clique em <strong>"Fechar Caixa"</strong></li>
                                </ul>
                            </li>
                        </ol>
                        
                        <h3>📋 Crediário e Contas a Receber</h3>
                        <p>Gestão completa de vendas a prazo e controle de inadimplência:</p>
                        <ul>
                            <li><strong>Vendas a Prazo:</strong> Configure parcelas e vencimentos</li>
                            <li><strong>Controle de Parcelas:</strong> Acompanhe pagamentos em aberto</li>
                            <li><strong>Alertas de Vencimento:</strong> Notificações automáticas</li>
                            <li><strong>Relatório de Inadimplência:</strong> Clientes em atraso</li>
                            <li><strong>Histórico de Pagamentos:</strong> Registro completo por cliente</li>
                        </ul>
                        
                        <h3>💳 Gestão de Cartões</h3>
                        <p>Controle das vendas no cartão e recebimentos:</p>
                        <ul>
                            <li><strong>Cadastro de Cartões:</strong> Configure taxas e prazos de recebimento</li>
                            <li><strong>Conciliação:</strong> Compare vendas com repasses das operadoras</li>
                            <li><strong>Previsão de Recebimentos:</strong> Saiba quando receberá cada venda</li>
                            <li><strong>Relatórios por Operadora:</strong> Análise detalhada por bandeira</li>
                        </ul>
                        
                        <h3>📊 Análises e Gráficos</h3>
                        <p>Visualize a performance financeira com gráficos intuitivos:</p>
                        <ul>
                            <li><strong>Faturamento Mensal:</strong> Evolução das vendas</li>
                            <li><strong>Formas de Pagamento:</strong> Distribuição por método</li>
                            <li><strong>Margem de Lucro:</strong> Análise de rentabilidade</li>
                            <li><strong>Fluxo de Caixa:</strong> Entradas vs. Saídas</li>
                            <li><strong>Comparativos:</strong> Períodos anteriores</li>
                        </ul>
                        <img src="assets/img/manual/tela_financeiro.png" alt="Screenshot da tela de Financeiro">
                    </section>

                    <section id="relatorios">
                        <h2>📈 Relatórios e Análises</h2>
                        <p>Sistema completo de relatórios para tomada de decisões estratégicas.</p>
                        
                        <h3>📋 Relatório Geral</h3>
                        <p>Visão consolidada de todo o negócio:</p>
                        <ol>
                            <li>Acesse <strong>Menu Administrativo > Relatório Geral</strong></li>
                            <li><strong>Configure Filtros:</strong>
                                <ul>
                                    <li><strong>Período:</strong> Data inicial e final</li>
                                    <li><strong>Tipo:</strong> Vendas, OS, Estoque, Financeiro</li>
                                    <li><strong>Cliente:</strong> Relatório específico por cliente</li>
                                    <li><strong>Produto/Serviço:</strong> Performance de itens específicos</li>
                                    <li><strong>Forma de Pagamento:</strong> Análise por método</li>
                                </ul>
                            </li>
                            <li><strong>Gerar Relatório:</strong> Clique em <strong>"Gerar"</strong></li>
                            <li><strong>Exportar:</strong> Baixe em PDF ou Excel</li>
                        </ol>
                        
                        <h3>📊 Relatórios Específicos</h3>
                        <ul>
                            <li><strong>Top Produtos:</strong> Produtos mais vendidos</li>
                            <li><strong>Top Serviços:</strong> Serviços mais realizados</li>
                            <li><strong>Clientes Frequentes:</strong> Maiores compradores</li>
                            <li><strong>Análise de Estoque:</strong> Giro e produtos parados</li>
                            <li><strong>Performance de Mecânicos:</strong> Produtividade da equipe</li>
                            <li><strong>Margem por Produto:</strong> Rentabilidade detalhada</li>
                        </ul>
                        
                        <h3>📱 Dashboards Inteligentes</h3>
                        <p>Acesse <strong>Gestão Avançada</strong> para visualizar:</p>
                        <ul>
                            <li><strong>Alertas de Estoque:</strong> Produtos em falta</li>
                            <li><strong>Faturamento do Mês:</strong> Meta vs. Realizado</li>
                            <li><strong>Lembretes de Revisão:</strong> Clientes para contato</li>
                            <li><strong>Pesquisas de Satisfação:</strong> Feedback dos clientes</li>
                        </ul>
                        
                        <h3>📤 Exportação e Compartilhamento</h3>
                        <ul>
                            <li><strong>PDF:</strong> Relatórios formatados para impressão</li>
                            <li><strong>Excel:</strong> Dados para análise avançada</li>
                            <li><strong>Email:</strong> Envio automático de relatórios</li>
                            <li><strong>WhatsApp:</strong> Compartilhamento rápido</li>
                        </ul>
                    </section>

                    <section id="configuracoes">
                        <h2>⚙️ Configurações do Sistema</h2>
                        <p>Personalize o sistema de acordo com as necessidades da sua oficina.</p>
                        
                        <h3>🏢 Dados da Empresa</h3>
                        <p>Configure as informações básicas da sua oficina:</p>
                        <ol>
                            <li>Acesse <strong>Menu Administrativo > Funcionalidades</strong></li>
                            <li>Clique em <strong>"Configurar Dados da Empresa"</strong></li>
                            <li>Preencha:
                                <ul>
                                    <li><strong>Nome da Empresa:</strong> Razão social</li>
                                    <li><strong>CNPJ:</strong> Para emissão de documentos</li>
                                    <li><strong>Endereço Completo:</strong> Para recibos e orçamentos</li>
                                    <li><strong>Telefone e Email:</strong> Contatos principais</li>
                                    <li><strong>Logo:</strong> Imagem da empresa</li>
                                </ul>
                            </li>
                            <li>Salve as configurações</li>
                        </ol>
                        
                        <h3>👥 Gestão de Funcionários</h3>
                        <p>Cadastre e gerencie sua equipe:</p>
                        <ul>
                            <li><strong>Mecânicos:</strong> Cadastre técnicos para vincular às OS</li>
                            <li><strong>Vendedores:</strong> Configure comissões e metas</li>
                            <li><strong>Permissões:</strong> Defina níveis de acesso</li>
                            <li><strong>Horários:</strong> Configure turnos de trabalho</li>
                        </ul>
                        
                        <h3>🖨️ Configurações de Impressão</h3>
                        <ul>
                            <li><strong>Impressora Térmica:</strong> Configure para recibos</li>
                            <li><strong>Formato de Documentos:</strong> Personalize layout</li>
                            <li><strong>Dados nos Recibos:</strong> Escolha informações a exibir</li>
                            <li><strong>Impressão Automática:</strong> Configure quando imprimir</li>
                        </ul>
                        
                        <h3>📱 Integrações</h3>
                        <ul>
                            <li><strong>WhatsApp Business:</strong> Configure API para notificações</li>
                            <li><strong>PIX:</strong> Configure chave para recebimentos</li>
                            <li><strong>Backup Automático:</strong> Configure frequência</li>
                            <li><strong>Sincronização:</strong> Configure backup na nuvem</li>
                        </ul>
                    </section>

                    <section id="recursos-avancados">
                        <h2>🚀 Recursos Avançados</h2>
                        <p>Funcionalidades especiais para otimizar sua operação.</p>
                        
                        <h3>🤖 Automações Inteligentes</h3>
                        <ul>
                            <li><strong>Sugestões de Produtos:</strong> Sistema sugere produtos relacionados durante vendas</li>
                            <li><strong>Alertas de Estoque:</strong> Notificações automáticas quando produtos atingem estoque mínimo</li>
                            <li><strong>Lembretes de Revisão:</strong> Sistema sugere contato com clientes para manutenção preventiva</li>
                            <li><strong>Previsão de Demanda:</strong> Análise de padrões de venda para sugestão de compras</li>
                        </ul>
                        
                        <h3>📱 Acesso Mobile</h3>
                        <ul>
                            <li><strong>Interface Responsiva:</strong> Acesse de qualquer dispositivo</li>
                            <li><strong>PDV Mobile:</strong> Vendas direto do celular ou tablet</li>
                            <li><strong>Consulta de OS:</strong> Acompanhe serviços em tempo real</li>
                            <li><strong>Notificações Push:</strong> Alertas importantes no celular</li>
                        </ul>
                        
                        <h3>🔍 Pesquisas de Satisfação</h3>
                        <ul>
                            <li><strong>Envio Automático:</strong> Pesquisas enviadas após conclusão de OS</li>
                            <li><strong>Múltiplos Canais:</strong> WhatsApp, Email ou SMS</li>
                            <li><strong>Análise de Resultados:</strong> Dashboard com feedback dos clientes</li>
                            <li><strong>Ações Corretivas:</strong> Alertas para avaliações negativas</li>
                        </ul>
                        
                        <h3>🔐 Segurança e Backup</h3>
                        <ul>
                            <li><strong>Backup Automático:</strong> Dados protegidos diariamente</li>
                            <li><strong>Controle de Acesso:</strong> Diferentes níveis de permissão</li>
                            <li><strong>Log de Atividades:</strong> Registro de todas as ações</li>
                            <li><strong>Recuperação de Dados:</strong> Restauração rápida em caso de problemas</li>
                        </ul>
                        
                        <h3>📊 Business Intelligence</h3>
                        <ul>
                            <li><strong>Análise Preditiva:</strong> Previsões baseadas em histórico</li>
                            <li><strong>Comparativos de Mercado:</strong> Benchmarks do setor</li>
                            <li><strong>Indicadores de Performance:</strong> KPIs personalizados</li>
                            <li><strong>Relatórios Executivos:</strong> Visão estratégica do negócio</li>
                        </ul>
                    </section>

                    <section id="faq">
                        <h2>❓ Perguntas Frequentes</h2>
                        <p>Respostas para as dúvidas mais comuns sobre o sistema.</p>
                        
                        <div class="alert alert-info">
                            <h3>🔧 Problemas Técnicos</h3>
                            <details>
                                <summary><strong>O sistema está lento, o que fazer?</strong></summary>
                                <p>Verifique sua conexão com a internet e feche abas desnecessárias do navegador. Se persistir, entre em contato com o suporte.</p>
                            </details>
                            <details>
                                <summary><strong>Não consigo imprimir recibos</strong></summary>
                                <p>Verifique se a impressora está conectada e configurada corretamente. Acesse Configurações > Impressão para ajustar as configurações.</p>
                            </details>
                            <details>
                                <summary><strong>Esqueci minha senha</strong></summary>
                                <p>Entre em contato com o administrador do sistema ou use a opção "Esqueci minha senha" na tela de login.</p>
                            </details>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h3>💼 Operações do Dia a Dia</h3>
                            <details>
                                <summary><strong>Como cancelar uma venda?</strong></summary>
                                <p>Acesse o Histórico, encontre a venda e clique em "Cancelar". Vendas no cartão podem ter restrições dependendo da operadora.</p>
                            </details>
                            <details>
                                <summary><strong>Cliente perdeu o recibo, como reimprimir?</strong></summary>
                                <p>Use o modal de Histórico (ícone de relógio no Dashboard), encontre a venda/OS e clique em "Reimprimir".</p>
                            </details>
                            <details>
                                <summary><strong>Como alterar o preço de um produto durante a venda?</strong></summary>
                                <p>No PDV, após adicionar o produto ao carrinho, clique no valor para editá-lo. Você pode aplicar desconto ou alterar o preço.</p>
                            </details>
                        </div>
                        
                        <div class="alert alert-success">
                            <h3>📊 Relatórios e Análises</h3>
                            <details>
                                <summary><strong>Como ver o faturamento do mês?</strong></summary>
                                <p>Acesse Financeiro ou Relatório Geral, configure o período desejado e gere o relatório. Você pode filtrar por forma de pagamento.</p>
                            </details>
                            <details>
                                <summary><strong>Como saber quais produtos estão em falta?</strong></summary>
                                <p>No Dashboard, verifique a seção "Alertas" ou acesse Gestão Avançada > Alertas de Estoque para uma visão completa.</p>
                            </details>
                        </div>
                    </section>

                    <section id="suporte">
                        <h2>🆘 Suporte e Contato</h2>
                        <p>Precisa de ajuda? Estamos aqui para apoiar você!</p>
                        
                        <div class="alert alert-info">
                            <h3>📞 Canais de Atendimento</h3>
                            <ul>
                                <li><strong>WhatsApp:</strong> (11) 99999-9999 - Atendimento de segunda a sexta, 8h às 18h</li>
                                <li><strong>Email:</strong> suporte@dengobike.com.br</li>
                                <li><strong>Telefone:</strong> (11) 3333-4444</li>
                                <li><strong>Chat Online:</strong> Disponível no canto inferior direito da tela</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-success">
                            <h3>🎯 Treinamentos e Consultoria</h3>
                            <ul>
                                <li><strong>Treinamento Inicial:</strong> Sessão gratuita de 2 horas para novos usuários</li>
                                <li><strong>Treinamento Avançado:</strong> Curso completo de 8 horas sobre todas as funcionalidades</li>
                                <li><strong>Consultoria Personalizada:</strong> Análise do seu negócio e otimização do sistema</li>
                                <li><strong>Suporte Remoto:</strong> Acesso remoto para resolução de problemas</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h3>📚 Recursos Adicionais</h3>
                            <ul>
                                <li><strong>Vídeo Tutoriais:</strong> Canal no YouTube com passo a passo</li>
                                <li><strong>Base de Conhecimento:</strong> Artigos detalhados sobre cada funcionalidade</li>
                                <li><strong>Webinars:</strong> Sessões ao vivo mensais com dicas e novidades</li>
                                <li><strong>Comunidade:</strong> Grupo no WhatsApp para troca de experiências</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-primary">
                            <h3>🚀 Solicitar Melhorias</h3>
                            <p>Tem uma ideia para melhorar o sistema? Envie sua sugestão!</p>
                            <ul>
                                <li>Descreva detalhadamente sua necessidade</li>
                                <li>Explique como isso ajudaria seu negócio</li>
                                <li>Nossa equipe avaliará e priorizará as melhorias</li>
                                <li>Você será notificado quando a funcionalidade estiver disponível</li>
                            </ul>
                        </div>
                    </section>

                    <section id="quiz">
                        <h2>🎯 Teste seu Conhecimento!</h2>
                        <p>Responda às perguntas abaixo para ver se você entendeu os principais fluxos do sistema.</p>
                        <form id="quiz-form">
                            <div class="quiz-question" data-correcta="a">
                                <strong>1. Ao finalizar uma venda no CREDIÁRIO para um cliente já cadastrado, o que o sistema solicita?</strong>
                                <div class="quiz-options">
                                    <label><input type="radio" name="q1" value="a"> A confirmação dos dados do cliente, exigindo o CPF.</label>
                                    <label><input type="radio" name="q1" value="b"> Apenas a senha do administrador.</label>
                                    <label><input type="radio" name="q1" value="c"> O endereço de e-mail do cliente para enviar a fatura.</label>
                                </div>
                            </div>
                            <div class="quiz-question" data-correcta="c">
                                <strong>2. Onde você pode encontrar uma lista de TODAS as vendas e Ordens de Serviço já feitas, com opção de reimprimir?</strong>
                                <div class="quiz-options">
                                    <label><input type="radio" name="q2" value="a"> Na página de Financeiro.</label>
                                    <label><input type="radio" name="q2" value="b"> Na página de Relatório Geral.</label>
                                    <label><input type="radio" name="q2" value="c"> No modal de Histórico, acessado pelo ícone de relógio no Dashboard.</label>
                                </div>
                            </div>
                            <div class="quiz-question" data-correcta="b">
                                <strong>3. Um cliente liga para saber o status da sua bicicleta. Qual a forma mais rápida de encontrar a OS dele?</strong>
                                <div class="quiz-options">
                                    <label><input type="radio" name="q3" value="a"> Procurar na lista de OS Ativas no Dashboard.</label>
                                    <label><input type="radio" name="q3" value="b"> Usar o modal de Histórico e buscar pelo nome do cliente.</label>
                                    <label><input type="radio" name="q3" value="c"> Abrir o Relatório Geral e filtrar por cliente.</label>
                                </div>
                            </div>
                            <div class="quiz-question" data-correcta="a">
                                <strong>4. Qual a melhor forma de controlar produtos com estoque baixo?</strong>
                                <div class="quiz-options">
                                    <label><input type="radio" name="q4" value="a"> Configurar estoque mínimo nos produtos e acompanhar os alertas.</label>
                                    <label><input type="radio" name="q4" value="b"> Verificar manualmente todos os produtos diariamente.</label>
                                    <label><input type="radio" name="q4" value="c"> Esperar o produto zerar para fazer novo pedido.</label>
                                </div>
                            </div>
                            <div class="quiz-question" data-correcta="c">
                                <strong>5. Para enviar notificações automáticas via WhatsApp aos clientes, o que é necessário?</strong>
                                <div class="quiz-options">
                                    <label><input type="radio" name="q5" value="a"> Apenas ter o número do cliente cadastrado.</label>
                                    <label><input type="radio" name="q5" value="b"> Configurar manualmente cada mensagem.</label>
                                    <label><input type="radio" name="q5" value="c"> Configurar a API do WhatsApp Business nas configurações do sistema.</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primario">Verificar Respostas</button>
                        </form>
                        <div id="quiz-resultado" style="display:none;"></div>
                        
                        <div class="alert alert-success" style="margin-top: 2rem;">
                            <h3>🎉 Parabéns por chegar até aqui!</h3>
                            <p>Você completou o manual completo do BikeOS. Com essas informações, você está preparado para usar todas as funcionalidades do sistema e otimizar a gestão da sua oficina.</p>
                            <p><strong>💡 Lembre-se:</strong> A prática leva à perfeição. Use o sistema diariamente e explore todas as funcionalidades para se tornar um expert!</p>
                        </div>
                    </section>
                </div>
            </article>
        </div>
    </main>
    <?php include '_partials/menu_admin.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebarLinks = document.querySelectorAll('.manual-sidebar a');
    const sections = document.querySelectorAll('.manual-content section');

    // Smooth scroll
    sidebarLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            if (targetSection) {
                window.scrollTo({
                    top: targetSection.offsetTop - 30,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Scrollspy para destacar o link ativo
    const activateLink = (id) => {
        sidebarLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${id}`) {
                link.classList.add('active');
            }
        });
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                activateLink(entry.target.id);
            }
        });
    }, { rootMargin: '-30% 0px -70% 0px' });

    sections.forEach(section => observer.observe(section));

    // Lógica do Quiz
    const quizForm = document.getElementById('quiz-form');
    quizForm.addEventListener('submit', (e) => {
        e.preventDefault();
        let score = 0;
        const questions = document.querySelectorAll('.quiz-question');
        let feedback = [];
        
        questions.forEach((question, index) => {
            const correctAnswer = question.dataset.correcta;
            const userAnswer = question.querySelector(`input[name="q${index+1}"]:checked`);
            
            // Limpa formatação anterior
            question.querySelectorAll('.quiz-options label').forEach(label => {
                label.classList.remove('correcta', 'incorreta');
            });

            if (userAnswer) {
                const parentLabel = userAnswer.parentElement;
                if (userAnswer.value === correctAnswer) {
                    score++;
                    parentLabel.classList.add('correcta');
                    question.style.backgroundColor = '#d4edda';
                    question.style.border = '2px solid #28a745';
                } else {
                    parentLabel.classList.add('incorreta');
                    question.style.backgroundColor = '#f8d7da';
                    question.style.border = '2px solid #dc3545';
                    
                    // Adicionar feedback específico
                    const questionNumber = index + 1;
                    let specificFeedback = '';
                    
                    switch(questionNumber) {
                        case 1:
                            specificFeedback = 'Lembre-se: sempre abra o caixa antes de iniciar as vendas do dia.';
                            break;
                        case 2:
                            specificFeedback = 'O status "Pronta" indica que o serviço foi concluído e a bicicleta está aguardando retirada.';
                            break;
                        case 3:
                            specificFeedback = 'O Relatório Geral permite filtros avançados e exportação de dados.';
                            break;
                        case 4:
                            specificFeedback = 'Configure o estoque mínimo para receber alertas automáticos quando precisar repor produtos.';
                            break;
                        case 5:
                            specificFeedback = 'Para notificações automáticas, é necessário configurar a API do WhatsApp Business.';
                            break;
                    }
                    
                    feedback.push(`<strong>Questão ${questionNumber}:</strong> ${specificFeedback}`);
                }
            } else {
                question.style.backgroundColor = '#fff3cd';
                question.style.border = '2px solid #ffc107';
                feedback.push(`<strong>Questão ${index + 1}:</strong> Você não respondeu esta questão.`);
            }
        });

        const percentage = (score / questions.length) * 100;
        const resultadoDiv = document.getElementById('quiz-resultado');
        
        let message = '';
        let alertClass = '';
        
        if (percentage >= 80) {
            message = `🎉 <strong>Excelente!</strong> Você acertou ${score} de ${questions.length} questões (${percentage.toFixed(0)}%). Você domina muito bem o sistema BikeOS!`;
            alertClass = 'alert alert-success';
        } else if (percentage >= 60) {
            message = `👍 <strong>Bom trabalho!</strong> Você acertou ${score} de ${questions.length} questões (${percentage.toFixed(0)}%). Com mais prática, você será um expert!`;
            alertClass = 'alert alert-warning';
        } else {
            message = `📚 Você acertou ${score} de ${questions.length} questões (${percentage.toFixed(0)}%). Recomendamos reler o manual e praticar mais com o sistema.`;
            alertClass = 'alert alert-danger';
        }
        
        let feedbackHtml = '';
        if (feedback.length > 0) {
            feedbackHtml = `
                <div class="alert alert-info" style="margin-top: 1rem;">
                    <h4>💡 Dicas para melhorar:</h4>
                    <ul>
                        ${feedback.map(f => `<li>${f}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        resultadoDiv.innerHTML = `
            <div class="${alertClass}">${message}</div>
            ${feedbackHtml}
            <div class="alert alert-primary" style="margin-top: 1rem;">
                <h4>🚀 Próximos Passos:</h4>
                <ul>
                    <li>Continue explorando o sistema na prática</li>
                    <li>Use este manual como referência sempre que precisar</li>
                    <li>Entre em contato com o suporte se tiver dúvidas</li>
                    <li>Participe dos treinamentos e webinars disponíveis</li>
                </ul>
            </div>
        `;
        resultadoDiv.style.display = 'block';
        
        // Scroll para o resultado
        resultadoDiv.scrollIntoView({ behavior: 'smooth' });
    });
});
</script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>
</html>