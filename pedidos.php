<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Pedidos de Compra'); ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .fornecedor-header {
            background-color: var(--cor-fundo-claro);
            padding: 1rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            border-radius: var(--raio-borda);
            font-size: 1.2em;
            font-weight: bold;
        }

        .fornecedor-header .fa-truck {
            color: var(--cor-primaria);
            margin-right: 10px;
        }

        .pedido-card {
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            border-bottom: 1px solid var(--cor-borda);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .pedido-header h3 {
            margin: 0;
        }

        .pedido-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .info-item strong {
            color: var(--cor-texto-secundario);
        }

        .pedido-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            border-top: 1px solid var(--cor-borda);
            padding-top: 1rem;
        }

        .modal-body ul {
            list-style: none;
            padding: 0;
        }

        .modal-body li {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        /* NOVOS ESTILOS PARA A FUNCIONALIDADE DE OCULTAR */
        .pedidos-ocultos {
            display: none;
            margin-top: 1.5rem;
            border-top: 2px solid var(--cor-borda);
            padding-top: 1.5rem;
        }

        .toggle-historico-btn {
            display: block;
            width: 100%;
            text-align: center;
            cursor: pointer;
            color: var(--cor-primaria);
            font-weight: 600;
            padding: 0.5rem;
            background: none;
            border: none;
            font-size: 1rem;
        }

        .toggle-historico-btn .fa-chevron-down {
            transition: transform 0.3s ease;
        }

        .toggle-historico-btn.expanded .fa-chevron-down {
            transform: rotate(180deg);
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php"><img src="assets/img/images.jpeg" alt="Logo" class="navbar-logo"></a>
        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php" class="btn btn-destaque">Sair</a>
    </nav>
    <main class="container">
        <div class="page-navigation">
            <a href="entrada_estoque.php" class="btn btn-secundario" title="Voltar"><i class="fa-solid fa-arrow-left"></i> Voltar ao Estoque</a>
            <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Dashboard"><i class="fa-solid fa-house"></i></a>
        </div>
        <div class="page-header">
            <h1><i class="fa-solid fa-clipboard-list"></i> Pedidos de Compra</h1>
        </div>
        <div id="pedidos-container"></div>
    </main>

    <div id="realizar-pedido-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="realizar-pedido-title">Resumo do Pedido</h2>
            <div class="modal-body">
                <p><strong>Fornecedor:</strong> <span id="resumo-fornecedor"></span></p>
                <p><strong>Valor Total (Custo):</strong> <span id="resumo-valor"></span></p>
                <hr>
                <h4>Itens do Pedido:</h4>
                <ul id="resumo-itens-lista"></ul>
            </div>
            <button id="btn-enviar-whatsapp" class="btn btn-sucesso" style="width:100%; margin-top:1rem;"><i class="fa-brands fa-whatsapp"></i> Enviar Pedido via WhatsApp</button>
        </div>
    </div>

    <div id="receber-pedido-modal" class="modal-backdrop">
        <div class="modal-content modal-content-large">
            <span class="modal-close">&times;</span>
            <h2 id="receber-pedido-title">Receber Mercadoria</h2>
            <form id="receber-pedido-form">
                <input type="hidden" id="receber-pedido-id">
                <p>Confirme as quantidades recebidas e os preços de custo atualizados.</p>
                <div style="max-height: 40vh; overflow-y: auto;">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Qtd. Pedida</th>
                                <th>Qtd. Recebida</th>
                                <th>Custo Unit. (R$)</th>
                            </tr>
                        </thead>
                        <tbody id="receber-pedido-tbody"></tbody>
                    </table>
                </div>
                
                <!-- Seção de Pagamento -->
                <div style="border-top: 1px solid #ddd; padding-top: 1rem; margin-top: 1rem;">
                    <h4><i class="fa-solid fa-credit-card"></i> Forma de Pagamento</h4>
                    <div class="form-group">
                        <label for="metodo-pagamento">Método de Pagamento:</label>
                        <select id="metodo-pagamento" class="form-input" required>
                            <option value="">Selecione...</option>
                            <option value="DINHEIRO">Dinheiro</option>
                            <option value="PIX">PIX</option>
                            <option value="CREDITO">Cartão de Crédito</option>
                        <option value="DEBITO">Cartão de Débito</option>
                            <option value="CHEQUE">Cheque</option>
                            <option value="A_PRAZO">A Prazo</option>
                            <option value="BOLETO">Boleto</option>
                        </select>
                    </div>
                    
                    <!-- Campos condicionais para A Prazo -->
                    <div id="campos-prazo" style="display: none;">
                        <div class="form-group">
                            <label for="data-vencimento">Data de Vencimento:</label>
                            <input type="date" id="data-vencimento" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="observacoes-prazo">Observações:</label>
                            <textarea id="observacoes-prazo" class="form-input" rows="2" placeholder="Observações sobre o pagamento a prazo..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Campos condicionais para Cartão de Crédito -->
                    <div id="campos-cartao-credito" style="display: none;">
                        <div class="form-group">
                            <label for="parcelas-credito">Quantidade de Parcelas:</label>
                            <select id="parcelas-credito" class="form-input">
                                <option value="1">1x à vista</option>
                                <option value="2">2x</option>
                                <option value="3">3x</option>
                                <option value="4">4x</option>
                                <option value="5">5x</option>
                                <option value="6">6x</option>
                                <option value="7">7x</option>
                                <option value="8">8x</option>
                                <option value="9">9x</option>
                                <option value="10">10x</option>
                                <option value="11">11x</option>
                                <option value="12">12x</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="button" id="btn-cadastrar-cartao" class="btn btn-secundario">
                                <i class="fa-solid fa-credit-card"></i> Cadastrar/Selecionar Cartão
                            </button>
                        </div>
                        <div class="form-group">
                            <label for="cartao-selecionado">Cartão Selecionado:</label>
                            <input type="text" id="cartao-selecionado" class="form-input" readonly placeholder="Nenhum cartão selecionado">
                            <input type="hidden" id="cartao-id">
                        </div>
                    </div>

                    <!-- Campos condicionais para Cartão de Débito -->
                    <div id="campos-cartao-debito" style="display: none;">
                        <div class="form-group">
                            <button type="button" id="btn-cadastrar-cartao-debito" class="btn btn-secundario">
                                <i class="fa-solid fa-credit-card"></i> Cadastrar/Selecionar Cartão
                            </button>
                        </div>
                        <div class="form-group">
                            <label for="cartao-debito-selecionado">Cartão Selecionado:</label>
                            <input type="text" id="cartao-debito-selecionado" class="form-input" readonly placeholder="Nenhum cartão selecionado">
                            <input type="hidden" id="cartao-debito-id">
                        </div>
                    </div>

                    <!-- Campos condicionais para Cheque -->
                    <div id="campos-cheque" style="display: none;">
                        <div class="form-group">
                            <label for="data-deposito-cheque">Data para Depósito:</label>
                            <input type="date" id="data-deposito-cheque" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="observacoes-cheque">Observações:</label>
                            <textarea id="observacoes-cheque" class="form-input" rows="2" placeholder="Banco, número do cheque, etc."></textarea>
                        </div>
                    </div>

                    <!-- Campos condicionais para Boleto -->
                    <div id="campos-boleto" style="display: none;">
                        <div class="form-group">
                            <label for="data-vencimento-boleto">Data de Vencimento:</label>
                            <input type="date" id="data-vencimento-boleto" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Código de Barras do Boleto:</label>
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <input type="text" id="codigo-barras" class="form-input" placeholder="Digite ou escaneie o código de barras">
                                <button type="button" id="btn-camera-boleto" class="btn btn-secundario btn-sm" title="Escanear código de barras">
                                    <i class="fa-solid fa-camera"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="observacoes-boleto">Observações:</label>
                            <textarea id="observacoes-boleto" class="form-input" rows="2" placeholder="Observações sobre o boleto..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Campo para valor total (calculado automaticamente) -->
                    <div class="form-group">
                        <label for="valor-total-pagamento">Valor Total do Pagamento:</label>
                        <input type="text" id="valor-total-pagamento" class="form-input" readonly style="background-color: #f5f5f5;">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-destaque" style="width:100%; margin-top: 1rem;">Confirmar Recebimento e Pagamento</button>
            </form>
        </div>
    </div>

    <?php include '_partials/menu_admin.php'; ?>

    <!-- Modal para Cadastro de Cartão -->
    <div id="modal-cartao" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Cadastrar Cartão</h3>
                <span class="close" id="close-modal-cartao">&times;</span>
            </div>
            <div class="modal-body">
                <form id="form-cartao">
                    <div class="form-group">
                        <label for="tipo-cartao">Tipo de Cartão:</label>
                        <select id="tipo-cartao" class="form-input" required>
                            <option value="">Selecione o tipo</option>
                            <option value="credito">Crédito</option>
                            <option value="debito">Débito</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ultimos-digitos">Últimos 4 Dígitos:</label>
                        <input type="text" id="ultimos-digitos" class="form-input" maxlength="4" pattern="[0-9]{4}" placeholder="0000" required>
                    </div>
                    <div class="form-group">
                        <label for="apelido-cartao">Apelido do Cartão:</label>
                        <input type="text" id="apelido-cartao" class="form-input" placeholder="Ex: Cartão Principal" required>
                    </div>
                    <div id="campos-credito-cartao" style="display: none;">
                        <div class="form-group">
                            <label for="data-fechamento">Data de Fechamento:</label>
                            <input type="number" id="data-fechamento" class="form-input" min="1" max="31" placeholder="Dia do mês (1-31)">
                        </div>
                        <div class="form-group">
                            <label for="data-vencimento-fatura">Data de Vencimento da Fatura:</label>
                            <input type="number" id="data-vencimento-fatura" class="form-input" min="1" max="31" placeholder="Dia do mês (1-31)">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" id="btn-cancelar-cartao" class="btn btn-secundario">Cancelar</button>
                        <button type="submit" class="btn btn-primario">Salvar Cartão</button>
                    </div>
                </form>
                
                <hr>
                
                <div id="lista-cartoes">
                    <h4>Cartões Cadastrados</h4>
                    <div id="cartoes-existentes">
                        <!-- Lista de cartões será carregada aqui -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Função para calcular valor total do pagamento (escopo global)
        const calcularValorTotal = () => {
            let total = 0;
            document.querySelectorAll('#receber-pedido-tbody tr').forEach(row => {
                const quantidade = parseFloat(row.querySelector('[name="quantidade_recebida"]').value) || 0;
                const preco = parseFloat(row.querySelector('[name="preco_custo"]').value) || 0;
                total += quantidade * preco;
            });
            document.getElementById('valor-total-pagamento').value = `R$ ${total.toFixed(2).replace('.', ',')}`;
        };

        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('pedidos-container');
            const realizarPedidoModal = document.getElementById('realizar-pedido-modal');
            const receberPedidoModal = document.getElementById('receber-pedido-modal');
            let pedidosData = [];
            let pedidoAtual = null;

            const carregarPedidos = async () => {
                container.innerHTML = '<p>Carregando pedidos...</p>';
                try {
                    const response = await fetch('api/api_compras.php?acao=listar_pedidos');
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    pedidosData = resultado.dados;
                    renderizarPedidos();
                } catch (e) {
                    container.innerHTML = `<p class="error-message">Erro ao carregar pedidos: ${e.message}</p>`;
                }
            };

            const renderizarPedidos = () => {
                container.innerHTML = '';
                if (pedidosData.length === 0) {
                    container.innerHTML = '<p>Nenhum pedido encontrado.</p>';
                    return;
                }
                const pedidosAgrupados = pedidosData.reduce((acc, pedido) => {
                    const fornecedor = pedido.nome_empresa || 'Fornecedor Indefinido';
                    if (!acc[fornecedor]) {
                        acc[fornecedor] = [];
                    }
                    acc[fornecedor].push(pedido);
                    return acc;
                }, {});

                for (const fornecedor in pedidosAgrupados) {
                    const fornecedorWrapper = document.createElement('div');

                    const header = document.createElement('div');
                    header.className = 'fornecedor-header';
                    header.innerHTML = `<i class="fa-solid fa-truck"></i> ${fornecedor}`;
                    fornecedorWrapper.appendChild(header);

                    const pedidosAtivosContainer = document.createElement('div');
                    const pedidosOcultosContainer = document.createElement('div');
                    pedidosOcultosContainer.className = 'pedidos-ocultos';

                    pedidosAgrupados[fornecedor].forEach(p => {
                        const card = document.createElement('div');
                        card.className = 'pedido-card';
                        const dataPedido = new Date(p.data_pedido).toLocaleDateString('pt-BR');
                        const valorTotal = p.valor_total_pedido ? parseFloat(p.valor_total_pedido).toFixed(2).replace('.', ',') : '0,00';

                        let actionsHtml = '';
                        if (p.status === 'PENDENTE') {
                            actionsHtml = `<button class="btn btn-sm btn-primario btn-realizar-pedido" data-id="${p.id}"><i class="fa-solid fa-check"></i> Realizar Pedido</button>`;
                        } else if (p.status === 'REALIZADO') {
                            actionsHtml = `<button class="btn btn-sm btn-sucesso btn-receber-mercadoria" data-id="${p.id}"><i class="fa-solid fa-box-open"></i> Receber Mercadoria</button>`;
                        }

                        card.innerHTML = `
                            <div class="pedido-header"><h3>Pedido #${p.id}</h3><span class="status status-${p.status}">${p.status.replace('_', ' ')}</span></div>
                            <div class="pedido-info"><div class="info-item"><strong>Data do Pedido:</strong> ${dataPedido}</div><div class="info-item"><strong>Valor Total (Custo):</strong> R$ ${valorTotal}</div></div>
                            <div class="pedido-actions">
                                <a href="imprimir_pedido.php?id=${p.id}" target="_blank" class="btn btn-sm btn-secundario" title="Imprimir Pedido"><i class="fa-solid fa-print"></i> Imprimir</a>
                                ${actionsHtml}
                            </div>
                        `;

                        if (p.status === 'PENDENTE' || p.status === 'REALIZADO') {
                            pedidosAtivosContainer.appendChild(card);
                        } else {
                            pedidosOcultosContainer.appendChild(card);
                        }
                    });

                    fornecedorWrapper.appendChild(pedidosAtivosContainer);

                    if (pedidosOcultosContainer.children.length > 0) {
                        fornecedorWrapper.appendChild(pedidosOcultosContainer);
                        const toggleBtn = document.createElement('button');
                        toggleBtn.className = 'toggle-historico-btn';
                        toggleBtn.innerHTML = 'Exibir Histórico <i class="fa-solid fa-chevron-down"></i>';
                        toggleBtn.onclick = () => {
                            pedidosOcultosContainer.style.display = pedidosOcultosContainer.style.display === 'block' ? 'none' : 'block';
                            toggleBtn.classList.toggle('expanded');
                            const icon = toggleBtn.querySelector('i');
                            icon.className = toggleBtn.classList.contains('expanded') ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
                            toggleBtn.firstChild.textContent = toggleBtn.classList.contains('expanded') ? 'Ocultar Histórico ' : 'Exibir Histórico ';
                        };
                        fornecedorWrapper.appendChild(toggleBtn);
                    }

                    container.appendChild(fornecedorWrapper);
                }
            };

            const abrirModalRealizarPedido = async (id) => {
                pedidoAtual = pedidosData.find(p => p.id == id);
                document.getElementById('realizar-pedido-title').textContent = `Resumo do Pedido #${id}`;
                document.getElementById('resumo-fornecedor').textContent = pedidoAtual.nome_empresa;
                document.getElementById('resumo-valor').textContent = `R$ ${parseFloat(pedidoAtual.valor_total_pedido || 0).toFixed(2).replace('.', ',')}`;

                const listaItens = document.getElementById('resumo-itens-lista');
                listaItens.innerHTML = '<li>Carregando itens...</li>';
                realizarPedidoModal.style.display = 'flex';

                const response = await fetch(`api/api_compras.php?acao=get_detalhes_pedido&pedido_id=${id}`);
                const resultado = await response.json();
                listaItens.innerHTML = '';
                if (resultado.sucesso) {
                    resultado.dados.forEach(item => {
                        let infoTamanho = item.tamanho ? ` (Tamanho: ${item.tamanho})` : '';
                        listaItens.innerHTML += `<li><span>${item.quantidade_solicitada}x ${item.produto_nome}${infoTamanho}</span></li>`;
                    });
                }
            };

            const abrirModalRecebimento = async (id) => {
                receberPedidoModal.style.display = 'flex';
                document.getElementById('receber-pedido-title').textContent = `Receber Pedido #${id}`;
                document.getElementById('receber-pedido-id').value = id;
                
                // Limpar campos de pagamento
                document.getElementById('metodo-pagamento').value = '';
                document.getElementById('campos-prazo').style.display = 'none';
                document.getElementById('campos-boleto').style.display = 'none';
                document.getElementById('valor-total-pagamento').value = '';
                
                const tbody = document.getElementById('receber-pedido-tbody');
                tbody.innerHTML = '<tr><td colspan="4">Carregando itens...</td></tr>';
                const response = await fetch(`api/api_compras.php?acao=get_detalhes_pedido&pedido_id=${id}`);
                const resultado = await response.json();
                tbody.innerHTML = '';
                if (resultado.sucesso) {
                    resultado.dados.forEach(item => {
                        tbody.innerHTML += `
                            <tr data-item-id="${item.id}" data-produto-id="${item.produto_id}">
                                <td>${item.produto_nome} ${item.tamanho ? `(${item.tamanho})` : ''}</td>
                                <td>${item.quantidade_solicitada}</td>
                                <td><input type="number" class="form-input" name="quantidade_recebida" value="${item.quantidade_solicitada}" min="0" onchange="calcularValorTotal()"></td>
                                <td><input type="number" step="0.01" class="form-input" name="preco_custo" value="${item.preco_custo_atual || '0.00'}" required onchange="calcularValorTotal()"></td>
                            </tr>`;
                    });
                    calcularValorTotal();
                }
            };
            
            // Event listener para método de pagamento
            document.getElementById('metodo-pagamento').addEventListener('change', function() {
                const metodo = this.value;
                const camposPrazo = document.getElementById('campos-prazo');
                const camposBoleto = document.getElementById('campos-boleto');
                const camposCartaoCredito = document.getElementById('campos-cartao-credito');
                const camposCartaoDebito = document.getElementById('campos-cartao-debito');
                const camposCheque = document.getElementById('campos-cheque');
                
                // Ocultar todos os campos condicionais
                camposPrazo.style.display = 'none';
                camposBoleto.style.display = 'none';
                camposCartaoCredito.style.display = 'none';
                camposCartaoDebito.style.display = 'none';
                camposCheque.style.display = 'none';
                
                // Mostrar campos específicos baseado no método
                if (metodo === 'A_PRAZO') {
                    camposPrazo.style.display = 'block';
                    document.getElementById('data-vencimento').required = true;
                } else if (metodo === 'BOLETO') {
                    camposBoleto.style.display = 'block';
                    document.getElementById('data-vencimento-boleto').required = true;
                    document.getElementById('codigo-barras').required = true;
                } else if (metodo === 'CREDITO') {
                    camposCartaoCredito.style.display = 'block';
                } else if (metodo === 'DEBITO') {
                    camposCartaoDebito.style.display = 'block';
                } else if (metodo === 'CHEQUE') {
                    camposCheque.style.display = 'block';
                    document.getElementById('data-deposito-cheque').required = true;
                } else {
                    // Para DINHEIRO e PIX, limpar campos obrigatórios
                    document.getElementById('data-vencimento').required = false;
                    document.getElementById('data-vencimento-boleto').required = false;
                    document.getElementById('codigo-barras').required = false;
                    document.getElementById('data-deposito-cheque').required = false;
                }
            });
            
            // Event listener para botão da câmera - funcionalidade de leitura de código de barras
             document.getElementById('btn-camera-boleto').addEventListener('click', function() {
                 startBarcodeScanner();
             });

             // Event listeners para botões de cadastro de cartão
             document.getElementById('btn-cadastrar-cartao').addEventListener('click', function() {
                 document.getElementById('tipo-cartao').value = 'credito';
                 document.getElementById('campos-credito-cartao').style.display = 'block';
                 document.getElementById('modal-cartao').style.display = 'flex';
                 carregarCartoes('credito');
             });

             document.getElementById('btn-cadastrar-cartao-debito').addEventListener('click', function() {
                 document.getElementById('tipo-cartao').value = 'debito';
                 document.getElementById('campos-credito-cartao').style.display = 'none';
                 document.getElementById('modal-cartao').style.display = 'flex';
                 carregarCartoes('debito');
             });

             // Event listener para mudança no tipo de cartão no modal
             document.getElementById('tipo-cartao').addEventListener('change', function() {
                 const tipo = this.value;
                 const camposCredito = document.getElementById('campos-credito-cartao');
                 
                 if (tipo === 'credito') {
                     camposCredito.style.display = 'block';
                     document.getElementById('data-fechamento').setAttribute('required', 'required');
                     document.getElementById('data-vencimento-fatura').setAttribute('required', 'required');
                 } else {
                     camposCredito.style.display = 'none';
                     document.getElementById('data-fechamento').removeAttribute('required');
                     document.getElementById('data-vencimento-fatura').removeAttribute('required');
                 }
             });

             // Event listeners para fechar modal de cartão
             document.getElementById('close-modal-cartao').addEventListener('click', function() {
                 document.getElementById('modal-cartao').style.display = 'none';
                 limparFormularioCartao();
             });

             document.getElementById('btn-cancelar-cartao').addEventListener('click', function() {
                 document.getElementById('modal-cartao').style.display = 'none';
                 limparFormularioCartao();
             });

             // Event listener para submissão do formulário de cartão
             document.getElementById('form-cartao').addEventListener('submit', function(e) {
                 e.preventDefault();
                 salvarCartao();
             });

             // Validação para aceitar apenas números nos últimos dígitos
             document.getElementById('ultimos-digitos').addEventListener('input', function() {
                 this.value = this.value.replace(/[^0-9]/g, '');
             });
            
            function startBarcodeScanner() {
                // Verificar se o navegador suporta getUserMedia
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Seu navegador não suporta acesso à câmera');
                    return;
                }

                // Criar modal para scanner
                const scannerModal = document.createElement('div');
                scannerModal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.8);
                    z-index: 10000;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                `;

                const videoElement = document.createElement('video');
                videoElement.style.cssText = `
                    width: 80%;
                    max-width: 500px;
                    height: auto;
                    border: 2px solid #fff;
                    border-radius: 8px;
                `;
                videoElement.autoplay = true;
                videoElement.playsInline = true;

                const closeBtn = document.createElement('button');
                closeBtn.textContent = 'Fechar Scanner';
                closeBtn.style.cssText = `
                    margin-top: 20px;
                    padding: 10px 20px;
                    background: #dc3545;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                `;

                const instructionText = document.createElement('p');
                instructionText.textContent = 'Posicione o código de barras do boleto na frente da câmera';
                instructionText.style.cssText = `
                    color: white;
                    text-align: center;
                    margin-bottom: 20px;
                    font-size: 18px;
                `;

                scannerModal.appendChild(instructionText);
                scannerModal.appendChild(videoElement);
                scannerModal.appendChild(closeBtn);
                document.body.appendChild(scannerModal);

                let stream = null;
                let isScanning = true;

                // Iniciar câmera
                navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    } 
                })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    videoElement.srcObject = stream;
                    
                    // Iniciar detecção de código de barras
                    videoElement.addEventListener('loadedmetadata', function() {
                        scanForBarcode();
                    });
                })
                .catch(function(error) {
                    console.error('Erro ao acessar câmera:', error);
                    alert('Erro ao acessar a câmera. Verifique as permissões.');
                    document.body.removeChild(scannerModal);
                });

                // Função para detectar código de barras
                function scanForBarcode() {
                    if (!isScanning) return;

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    
                    canvas.width = videoElement.videoWidth;
                    canvas.height = videoElement.videoHeight;
                    
                    context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
                    
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    
                    // Simular detecção de código de barras (implementação básica)
                    // Em produção, seria necessário usar uma biblioteca como QuaggaJS ou ZXing
                    detectBarcodePattern(imageData);
                    
                    if (isScanning) {
                        requestAnimationFrame(scanForBarcode);
                    }
                }

                // Função simplificada para detectar padrão de código de barras
                function detectBarcodePattern(imageData) {
                    // Esta é uma implementação simplificada
                    // Para produção, recomenda-se usar bibliotecas especializadas
                    
                    // Simular detecção após alguns segundos (para demonstração)
                    setTimeout(() => {
                        if (isScanning) {
                            const mockBarcode = prompt('Scanner simulado - Digite o código de barras do boleto:');
                            if (mockBarcode && mockBarcode.trim()) {
                                document.getElementById('codigo-barras').value = mockBarcode.trim();
                                closeScannerModal();
                            }
                        }
                    }, 3000);
                }

                // Fechar modal
                function closeScannerModal() {
                    isScanning = false;
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                    }
                    if (scannerModal.parentNode) {
                        document.body.removeChild(scannerModal);
                    }
                }

                closeBtn.addEventListener('click', closeScannerModal);
                
                // Fechar ao clicar fora do vídeo
                scannerModal.addEventListener('click', function(e) {
                    if (e.target === scannerModal) {
                        closeScannerModal();
                    }
                });
             }

             // Funções para gerenciamento de cartões
             function limparFormularioCartao() {
                 document.getElementById('form-cartao').reset();
                 document.getElementById('campos-credito-cartao').style.display = 'none';
             }

             async function carregarCartoes(tipo) {
                 try {
                     const response = await fetch(`api/api_compras.php?acao=listar_cartoes&tipo=${tipo}`);
                     const result = await response.json();
                     
                     const container = document.getElementById('cartoes-existentes');
                     container.innerHTML = '';
                     
                     if (result.sucesso && result.dados.length > 0) {
                         result.dados.forEach(cartao => {
                             const cartaoDiv = document.createElement('div');
                             cartaoDiv.className = 'cartao-item';
                             cartaoDiv.style.cssText = `
                                 border: 1px solid #ddd;
                                 padding: 10px;
                                 margin: 5px 0;
                                 border-radius: 5px;
                                 cursor: pointer;
                                 background: #f9f9f9;
                             `;
                             
                             cartaoDiv.innerHTML = `
                                 <strong>${cartao.apelido}</strong><br>
                                 <small>**** **** **** ${cartao.ultimos_digitos}</small>
                                 ${cartao.tipo === 'credito' ? `<br><small>Venc: ${cartao.data_vencimento_fatura}</small>` : ''}
                             `;
                             
                             cartaoDiv.addEventListener('click', () => selecionarCartao(cartao, tipo));
                             container.appendChild(cartaoDiv);
                         });
                     } else {
                         container.innerHTML = '<p>Nenhum cartão cadastrado</p>';
                     }
                 } catch (error) {
                     console.error('Erro ao carregar cartões:', error);
                     alert('Erro ao carregar cartões');
                 }
             }

             function selecionarCartao(cartao, tipo) {
                 if (tipo === 'credito') {
                     document.getElementById('cartao-selecionado').value = `${cartao.apelido} (**** ${cartao.ultimos_digitos})`;
                     document.getElementById('cartao-id').value = cartao.id;
                 } else {
                     document.getElementById('cartao-debito-selecionado').value = `${cartao.apelido} (**** ${cartao.ultimos_digitos})`;
                     document.getElementById('cartao-debito-id').value = cartao.id;
                 }
                 
                 document.getElementById('modal-cartao').style.display = 'none';
                 limparFormularioCartao();
             }

             async function salvarCartao() {
                 const formData = new FormData();
                 formData.append('acao', 'salvar_cartao');
                 formData.append('tipo', document.getElementById('tipo-cartao').value);
                 formData.append('ultimos_digitos', document.getElementById('ultimos-digitos').value);
                 formData.append('apelido', document.getElementById('apelido-cartao').value);
                 
                 if (document.getElementById('tipo-cartao').value === 'credito') {
                     formData.append('data_fechamento', document.getElementById('data-fechamento').value);
                     formData.append('data_vencimento_fatura', document.getElementById('data-vencimento-fatura').value);
                 }
                 
                 try {
                     const response = await fetch('api/api_compras.php', {
                         method: 'POST',
                         body: formData
                     });
                     
                     const result = await response.json();
                     
                     if (result.sucesso) {
                         alert('Cartão cadastrado com sucesso!');
                         carregarCartoes(document.getElementById('tipo-cartao').value);
                         limparFormularioCartao();
                     } else {
                         alert('Erro ao cadastrar cartão: ' + result.mensagem);
                     }
                 } catch (error) {
                     console.error('Erro ao salvar cartão:', error);
                     alert('Erro ao salvar cartão');
                 }
             }

             container.addEventListener('click', async e => {
                const btnRealizar = e.target.closest('.btn-realizar-pedido');
                const btnReceber = e.target.closest('.btn-receber-mercadoria');
                if (btnRealizar) {
                    abrirModalRealizarPedido(btnRealizar.dataset.id);
                }
                if (btnReceber) {
                    abrirModalRecebimento(btnReceber.dataset.id);
                }
            });

            realizarPedidoModal.querySelector('.modal-close').onclick = () => realizarPedidoModal.style.display = 'none';
            document.getElementById('btn-enviar-whatsapp').addEventListener('click', async () => {
                if (!pedidoAtual.fornecedor_telefone) {
                    alert('Este fornecedor não possui um telefone cadastrado.');
                    return;
                }
                const response = await fetch(`api/api_compras.php?acao=get_detalhes_pedido&pedido_id=${pedidoAtual.id}`);
                const resultado = await response.json();
                if (!resultado.sucesso) {
                    alert('Erro ao buscar itens do pedido.');
                    return;
                }

                let mensagem = `Olá, ${pedidoAtual.nome_empresa}!\n\nSegue nosso pedido de compra *#${pedidoAtual.id}*:\n\n`;
                resultado.dados.forEach(item => {
                    let infoTamanho = item.tamanho ? ` (Tamanho: ${item.tamanho})` : '';
                    mensagem += `*Produto:* ${item.produto_nome}${infoTamanho}\n`;
                    mensagem += `*Quantidade:* ${item.quantidade_solicitada}\n------------------------\n`;
                });
                mensagem += `\nQualquer dúvida, estamos à disposição.\nAtenciosamente,\n${window.lojaConfig?.config?.nome_loja || 'BikeOS'}`;

                const telefoneFormatado = '55' + pedidoAtual.fornecedor_telefone.replace(/\D/g, '');
                window.open(`https://wa.me/${telefoneFormatado}?text=${encodeURIComponent(mensagem)}`, '_blank');

                await fetch('api/api_compras.php?acao=marcar_pedido_realizado', {
                    method: 'POST',
                    body: JSON.stringify({
                        pedido_id: pedidoAtual.id
                    })
                });
                realizarPedidoModal.style.display = 'none';
                await carregarPedidos();
            });

            receberPedidoModal.querySelector('.modal-close').onclick = () => receberPedidoModal.style.display = 'none';
            document.getElementById('receber-pedido-form').addEventListener('submit', async e => {
                e.preventDefault();
                
                const pedidoId = document.getElementById('receber-pedido-id').value;
                const metodoPagamento = document.getElementById('metodo-pagamento').value;
                
                if (!metodoPagamento) {
                    alert('Por favor, selecione um método de pagamento.');
                    return;
                }
                
                const itens = [];
                let valorTotal = 0;
                document.querySelectorAll('#receber-pedido-tbody tr').forEach(row => {
                    const quantidade = parseFloat(row.querySelector('[name="quantidade_recebida"]').value) || 0;
                    const preco = parseFloat(row.querySelector('[name="preco_custo"]').value) || 0;
                    valorTotal += quantidade * preco;
                    
                    itens.push({
                        id: row.dataset.itemId,
                        produto_id: row.dataset.produtoId,
                        quantidade_recebida: quantidade,
                        preco_custo: preco
                    });
                });
                
                // Dados de pagamento
                const dadosPagamento = {
                    metodo_pagamento: metodoPagamento,
                    valor_total: valorTotal
                };
                
                // Adicionar campos específicos baseado no método
                if (metodoPagamento === 'CREDITO') {
                    const parcelas = document.getElementById('parcelas-credito').value;
                    const cartaoId = document.getElementById('cartao-id').value;
                    
                    if (!cartaoId) {
                        alert('Por favor, selecione um cartão de crédito.');
                        return;
                    }
                    
                    dadosPagamento.parcelas = parcelas;
                    dadosPagamento.cartao_id = cartaoId;
                } else if (metodoPagamento === 'DEBITO') {
                    const cartaoId = document.getElementById('cartao-debito-id').value;
                    
                    if (!cartaoId) {
                        alert('Por favor, selecione um cartão de débito.');
                        return;
                    }
                    
                    dadosPagamento.cartao_id = cartaoId;
                } else if (metodoPagamento === 'CHEQUE') {
                    const dataDeposito = document.getElementById('data-deposito-cheque').value;
                    const observacoes = document.getElementById('observacoes-cheque').value;
                    
                    if (!dataDeposito) {
                        alert('Por favor, informe a data para depósito do cheque.');
                        return;
                    }
                    
                    dadosPagamento.data_deposito = dataDeposito;
                    dadosPagamento.observacoes = observacoes;
                } else if (metodoPagamento === 'A_PRAZO') {
                    const dataVencimento = document.getElementById('data-vencimento').value;
                    if (!dataVencimento) {
                        alert('Por favor, informe a data de vencimento para pagamento a prazo.');
                        return;
                    }
                    dadosPagamento.data_vencimento = dataVencimento;
                    dadosPagamento.observacoes = document.getElementById('observacoes-prazo').value;
                } else if (metodoPagamento === 'BOLETO') {
                    const dataVencimento = document.getElementById('data-vencimento-boleto').value;
                    const codigoBarras = document.getElementById('codigo-barras').value;
                    if (!dataVencimento || !codigoBarras) {
                        alert('Por favor, informe a data de vencimento e o código de barras do boleto.');
                        return;
                    }
                    dadosPagamento.data_vencimento = dataVencimento;
                    dadosPagamento.codigo_barras = codigoBarras;
                    dadosPagamento.observacoes = document.getElementById('observacoes-boleto').value;
                }
                
                const response = await fetch('api/api_compras.php?acao=receber_pedido', {
                    method: 'POST',
                    body: JSON.stringify({
                        pedido_id: pedidoId,
                        itens: itens,
                        pagamento: dadosPagamento
                    })
                });
                
                const resultado = await response.json();
                if (resultado.sucesso) {
                    alert(resultado.mensagem);
                    receberPedidoModal.style.display = 'none';
                    await carregarPedidos();
                } else {
                    alert('Erro: ' + resultado.mensagem);
                }
            });

            carregarPedidos();
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>