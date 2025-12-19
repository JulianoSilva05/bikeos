<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php 
        require_once 'helpers/empresa_helper.php';
        echo getTituloComEmpresa('Gestão de Produtos');
    ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .estoque-ok {
            color: var(--cor-sucesso);
            font-weight: bold;
        }

        .estoque-baixo {
            color: var(--cor-alerta);
            font-weight: bold;
        }

        .estoque-zerado {
            color: var(--cor-erro);
            font-weight: bold;
        }

        #image-preview {
            max-width: 150px;
            max-height: 150px;
            margin-top: 10px;
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            display: none;
            object-fit: cover;
        }

        .product-image-cell {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: var(--raio-borda);
        }

        #scanner-container {
            width: 100%;
            max-width: 400px;
            margin: 1rem auto;
            border: 2px solid var(--cor-primaria);
            border-radius: var(--raio-borda);
            overflow: hidden;
            display: none;
        }

        .image-input-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        #camera-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: black;
            z-index: 1060;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        #camera-stream {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #camera-controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 1rem;
        }

        #btn-snap-photo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 5px solid white;
            background-color: rgba(255, 255, 255, 0.5);
        }
        /* NOVO: Estilo para o botão flutuante de sugestões */
        .fab-container-sugestoes {
            position: fixed;
            bottom: 95px; /* Posição acima do menu admin */
            right: 25px;
            z-index: 1050;
        }
        
        .search-container {
            position: relative;
            display: inline-block;
            margin-right: 15px;
        }
        
        .search-container input {
            padding: 8px 35px 8px 12px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
            width: 200px;
            transition: all 0.3s ease;
        }
        
        .search-container input:focus {
            outline: none;
            border-color: var(--cor-primaria);
            box-shadow: 0 0 5px rgba(var(--cor-primaria-rgb), 0.3);
        }
        
        .search-container i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            pointer-events: none;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
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
            <h1><i class="fa-solid fa-boxes-stacked"></i> Gestão de Peças e Produtos</h1>
            <div class="header-actions">
                <div class="search-container">
                    <input type="text" id="search-produtos" placeholder="Buscar produtos...">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <button id="btn-novo-produto" class="btn btn-destaque"><i class="fa-solid fa-plus"></i> Novo Produto</button>
            </div>
        </div>

        <!-- Cards de Resumo do Estoque -->
        <div class="cards-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; font-size: 2rem; font-weight: bold;" id="total-produtos">0</h3>
                        <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Total de Produtos</p>
                    </div>
                    <i class="fa-solid fa-boxes-stacked" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; font-size: 2rem; font-weight: bold;" id="estoque-baixo">0</h3>
                        <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Estoque Baixo</p>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: #333; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; font-size: 2rem; font-weight: bold;" id="estoque-zerado">0</h3>
                        <p style="margin: 0.5rem 0 0 0; opacity: 0.8;">Estoque Zerado</p>
                    </div>
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; font-size: 2rem; font-weight: bold;" id="valor-total-estoque">R$ 0,00</h3>
                        <p style="margin: 0.5rem 0 0 0; opacity: 0.8;">Valor Total Estoque</p>
                    </div>
                    <i class="fa-solid fa-dollar-sign" style="font-size: 2.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>

        <!-- Filtros de Estoque -->
        <div class="filters-container" style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h3 style="margin: 0 0 1rem 0; color: #333;"><i class="fa-solid fa-filter"></i> Filtros de Estoque</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Filtrar por Produto</label>
                    <input type="text" id="filtro-produto" class="form-input" placeholder="Nome ou SKU do produto">
                </div>
                <div class="form-group">
                    <label class="form-label">Categoria</label>
                    <select id="filtro-categoria" class="form-input">
                        <option value="">Todas as categorias</option>
                        <option value="PECA">Peça</option>
                        <option value="ACESSORIO">Acessório</option>
                        <option value="ROUPA">Roupa</option>
                        <option value="CALCADO">Calçado</option>
                        <option value="BIKE_NOVA">Bicicleta Nova</option>
                        <option value="BIKE_USADA">Bicicleta Usada</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status do Estoque</label>
                    <select id="filtro-estoque" class="form-input">
                        <option value="">Todos os status</option>
                        <option value="normal">Estoque Normal</option>
                        <option value="baixo">Estoque Baixo</option>
                        <option value="zerado">Estoque Zerado</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; align-items: end;">
                    <button id="btn-limpar-filtros" class="btn btn-secundario" style="width: 100%;">
                        <i class="fa-solid fa-eraser"></i> Limpar Filtros
                    </button>
                </div>
            </div>
        </div>
        <div class="card">
            <div style="overflow-x: auto;">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Img</th>
                            <th>ID</th>
                            <th>SKU</th>
                            <th>Nome</th>
                            <th>Estoque</th>
                            <th>Preço Venda</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="produtos-table-body"></tbody>
                </table>
            </div>
        </div>

        <!-- Seção de Movimentações Recentes -->
        <div class="card" style="margin-top: 2rem;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #eee;">
                <h3 style="margin: 0; color: #333;"><i class="fa-solid fa-clock-rotate-left"></i> Movimentações Recentes</h3>
                <button id="btn-gerar-relatorio" class="btn btn-secundario">
                    <i class="fa-solid fa-file-pdf"></i> Gerar Relatório
                </button>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <div id="movimentacoes-container">
                    <p style="text-align: center; color: #666; margin: 2rem 0;">Carregando movimentações...</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Movimentação de Estoque -->
    <div id="movimentacao-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-arrows-up-down"></i> Nova Movimentação de Estoque</h2>
            <form id="movimentacao-form">
                <input type="hidden" id="movimentacao-produto-id" name="produto_id">
                <div class="form-group">
                    <label class="form-label">Produto</label>
                    <input type="text" id="movimentacao-produto-nome" class="form-input" readonly>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="movimentacao-tipo" class="form-label">Tipo de Movimentação</label>
                        <select id="movimentacao-tipo" name="tipo" class="form-input" required>
                            <option value="">Selecione o tipo</option>
                            <option value="ENTRADA">Entrada</option>
                            <option value="SAIDA">Saída</option>
                            <option value="AJUSTE">Ajuste</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="movimentacao-quantidade" class="form-label">Quantidade</label>
                        <input type="number" id="movimentacao-quantidade" name="quantidade" class="form-input" min="1" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="movimentacao-observacao" class="form-label">Observação</label>
                    <textarea id="movimentacao-observacao" name="observacao" class="form-input" rows="3" placeholder="Descreva o motivo da movimentação..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secundario" id="btn-cancelar-movimentacao">Cancelar</button>
                    <button type="submit" class="btn btn-primario">Salvar Movimentação</button>
                </div>
            </form>
        </div>
    </div>

    <div id="produto-modal" class="modal-backdrop">
        <div class="modal-content modal-content-large">
            <span class="modal-close">&times;</span>
            <h2 id="modal-title">Novo Produto</h2>
            <div id="scanner-container"></div>
            <form id="produto-form" enctype="multipart/form-data">
                <input type="hidden" id="produto_id" name="id">
                <div class="form-grid">
                    <div class="form-group"><label for="produto_nome" class="form-label">Nome do Produto</label><input type="text" id="produto_nome" name="nome" class="form-input" required></div>
                    <div class="form-group"><label for="produto_sku" class="form-label">SKU (Cód. de Barras)</label>
                        <div style="display: flex; gap: 0.5rem;"><input type="text" id="produto_sku" name="sku" class="form-input"><button type="button" id="btn-scan-barcode" class="btn btn-secundario btn-icon-only" title="Ler código de barras"><i class="fa-solid fa-barcode"></i></button></div>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Imagem do Produto</label>
                    <div class="image-input-group"><button type="button" id="btn-select-image" class="btn btn-secundario"><i class="fa-solid fa-upload"></i> Selecionar Arquivo</button><button type="button" id="btn-take-photo" class="btn btn-secundario"><i class="fa-solid fa-camera"></i> Tirar Foto</button></div><input type="file" id="produto_imagem_select" name="imagem" class="form-input" accept="image/*" style="display: none;"><img id="image-preview" src="#" alt="Prévia da imagem">
                </div>
                <div class="form-group"><label for="produto_descricao" class="form-label">Descrição</label><textarea id="produto_descricao" name="descricao" class="form-input" rows="3"></textarea></div>
                <div class="form-grid">
                    <div class="form-group"><label for="produto_tipo" class="form-label">Tipo</label><select id="produto_tipo" name="tipo" class="form-input">
                            <option value="PECA">Peça</option>
                            <option value="ACESSORIO">Acessório</option>
                            <option value="ROUPA">Roupa</option>
                            <option value="CALCADO">Calçado</option>
                            <option value="BIKE_NOVA">Bicicleta Nova</option>
                            <option value="BIKE_USADA">Bicicleta Usada</option>
                        </select></div>
                    <div class="form-group"><label for="produto_marca" class="form-label">Marca</label><input type="text" id="produto_marca" name="marca" class="form-input"></div>
                </div>
                <div id="variacoes-container" style="display: none; margin-top: 1rem; border-top: 1px solid var(--cor-borda); padding-top: 1rem;">
                    <h4>Gerenciar Estoque por Tamanho</h4>
                    <p><small>O "Estoque Atual" será calculado automaticamente. Adicione os tamanhos e quantidades abaixo.</small></p>
                    <div id="lista-variacoes" style="margin-bottom: 1rem;"></div>
                    <div class="form-grid" style="align-items: flex-end; gap: 0.5rem;">
                        <div class="form-group"><label class="form-label">Tamanho</label><input type="text" id="novo-tamanho" class="form-input" placeholder="Ex: M ou 42"></div>
                        <div class="form-group"><label class="form-label">Quantidade</label><input type="number" id="nova-quantidade" class="form-input" placeholder="Ex: 10" min="0"></div><button type="button" id="btn-add-variacao" class="btn btn-secundario">Adicionar</button>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label for="produto_estoque_atual" class="form-label">Estoque Atual</label><input type="number" id="produto_estoque_atual" name="estoque_atual" class="form-input" required></div>
                    <div class="form-group"><label for="produto_estoque_minimo" class="form-label">Estoque Mínimo</label><input type="number" id="produto_estoque_minimo" name="estoque_minimo" class="form-input" required></div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label for="produto_preco_custo" class="form-label">Preço de Custo (R$)</label><input type="number" step="0.01" id="produto_preco_custo" name="preco_custo" class="form-input" required></div>
                    <div class="form-group"><label for="produto_preco_venda" class="form-label">Preço de Venda (R$)</label><input type="number" step="0.01" id="produto_preco_venda" name="preco_venda" class="form-input" required></div>
                </div>
                <div class="form-group">
                    <label for="produto_fornecedor_input" class="form-label">Fornecedor</label>
                    <div class="autocomplete-container" style="position: relative;">
                        <input type="text" id="produto_fornecedor_input" name="fornecedor_nome" class="form-input" placeholder="Digite o nome do fornecedor..." autocomplete="off">
                        <input type="hidden" id="produto_fornecedor_id" name="fornecedor_id" value="">
                        <div id="fornecedor-suggestions" class="autocomplete-suggestions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; max-height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-destaque" style="width: 100%;">Salvar Produto</button>
            </form>
            <div id="camera-container"><video id="camera-stream" autoplay playsinline></video><canvas id="camera-canvas" style="display:none;"></canvas>
                <div id="camera-controls"><button type="button" id="btn-snap-photo" title="Capturar Foto"></button><button type="button" id="btn-close-camera" class="btn btn-erro" title="Fechar Câmera"><i class="fa-solid fa-times"></i></button></div>
            </div>
        </div>
    </div>

    <div id="estoque-variacao-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="estoque-modal-title">Ajustar Estoque</h2>
            <form id="estoque-variacao-form">
                <input type="hidden" id="estoque-produto-id">
                <p>Altere as quantidades abaixo para corrigir o estoque de cada tamanho.</p>
                <div id="lista-estoque-variacoes"></div>
                <div class="form-group" style="margin-top: 1rem;"><label for="estoque-observacao" class="form-label">Motivo do Ajuste (Opcional)</label><input type="text" id="estoque-observacao" class="form-input" placeholder="Ex: Contagem de balanço"></div>
                <button type="submit" class="btn btn-destaque" style="width: 100%;">Salvar Ajuste de Estoque</button>
            </form>
        </div>
    </div>
<div class="fab-container-sugestoes">
        <a href="sugestoes_config.php" class="fab-button" title="Configurar Sugestões de Produtos" style="background-color: #8b5cf6;"target="_blank">
            <i class="fa-solid fa-lightbulb"></i>
        </a>
    </div>
    <?php include '_partials/menu_admin.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.getElementById('produtos-table-body');
            const modal = document.getElementById('produto-modal');
            const modalTitle = document.getElementById('modal-title');
            const produtoForm = document.getElementById('produto-form');
            const btnNovoProduto = document.getElementById('btn-novo-produto');
            const fornecedorInput = document.getElementById('produto_fornecedor_input');
            const fornecedorIdInput = document.getElementById('produto_fornecedor_id');
            const fornecedorSuggestions = document.getElementById('fornecedor-suggestions');
            const searchInput = document.getElementById('search-produtos');
            let fornecedoresData = [];
            let produtosData = [];
            const imagePreview = document.getElementById('image-preview');
            const btnScanBarcode = document.getElementById('btn-scan-barcode');
            const scannerContainer = document.getElementById('scanner-container');
            let html5QrcodeScanner = null;
            const btnSelectImage = document.getElementById('btn-select-image');
            const imageSelectInput = document.getElementById('produto_imagem_select');
            const btnTakePhoto = document.getElementById('btn-take-photo');
            const cameraContainer = document.getElementById('camera-container');
            const cameraStream = document.getElementById('camera-stream');
            const cameraCanvas = document.getElementById('camera-canvas');
            const btnSnapPhoto = document.getElementById('btn-snap-photo');
            const btnCloseCamera = document.getElementById('btn-close-camera');
            let currentStream = null;
            let capturedImageBlob = null;
            const produtoTipoSelect = document.getElementById('produto_tipo');
            const variacoesContainer = document.getElementById('variacoes-container');
            const estoqueAtualInput = document.getElementById('produto_estoque_atual');
            const btnAddVariacao = document.getElementById('btn-add-variacao');
            const listaVariacoesDiv = document.getElementById('lista-variacoes');
            const estoqueModal = document.getElementById('estoque-variacao-modal');
            const estoqueForm = document.getElementById('estoque-variacao-form');

            const carregarProdutos = async () => {
                try {
                    const response = await fetch('api/api_produtos.php?acao=listar_produtos');
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        produtosData = resultado.dados;
                        renderizarProdutos(produtosData);
                        atualizarCardsResumo(produtosData);
                    }
                } catch (error) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="error-message">Erro ao carregar produtos.</td></tr>`;
                }
            };

            const atualizarCardsResumo = (produtos) => {
                const totalProdutos = produtos.length;
                let estoqueBaixo = 0;
                let estoqueZerado = 0;
                let valorTotalEstoque = 0;

                produtos.forEach(p => {
                    const estoque = parseInt(p.estoque_atual);
                    const estoqueMinimo = parseInt(p.estoque_minimo);
                    const precoVenda = parseFloat(p.preco_venda);

                    if (estoque <= 0) {
                        estoqueZerado++;
                    } else if (estoque <= estoqueMinimo) {
                        estoqueBaixo++;
                    }

                    valorTotalEstoque += estoque * precoVenda;
                });

                document.getElementById('total-produtos').textContent = totalProdutos;
                document.getElementById('estoque-baixo').textContent = estoqueBaixo;
                document.getElementById('estoque-zerado').textContent = estoqueZerado;
                document.getElementById('valor-total-estoque').textContent = 
                    'R$ ' + valorTotalEstoque.toFixed(2).replace('.', ',');
            };

            const renderizarProdutos = (produtos) => {
                tableBody.innerHTML = '';
                produtos.forEach(p => {
                    let estoqueClasse = 'estoque-ok';
                    if (parseInt(p.estoque_atual) <= 0) {
                        estoqueClasse = 'estoque-zerado';
                    } else if (parseInt(p.estoque_atual) <= parseInt(p.estoque_minimo)) {
                        estoqueClasse = 'estoque-baixo';
                    }
                    const tr = document.createElement('tr');
                    let btnEstoque = '';
                    if (p.tipo === 'ROUPA' || p.tipo === 'CALCADO') {
                        btnEstoque = `<button class="btn btn-sm btn-secundario btn-estoque" data-id="${p.id}" title="Ajustar Estoque por Tamanho"><i class="fa-solid fa-boxes-stacked"></i></button>`;
                    }
                    tr.innerHTML = `<td><img src="${p.imagem_url || 'assets/img/placeholder.png'}" alt="${p.nome}" class="product-image-cell"></td><td>${p.id}</td><td>${p.sku || '-'}</td><td>${p.nome}</td><td class="${estoqueClasse}">${p.estoque_atual}</td><td>R$ ${parseFloat(p.preco_venda).toFixed(2).replace('.',',')}</td><td><button class="btn btn-sm btn-primario btn-editar" data-id="${p.id}" title="Editar"><i class="fa-solid fa-pencil"></i></button> ${btnEstoque} <button class="btn btn-sm btn-sucesso btn-movimentacao" data-id="${p.id}" title="Nova Movimentação"><i class="fa-solid fa-arrows-up-down"></i></button> <button class="btn btn-sm btn-secundario btn-historico" data-id="${p.id}" title="Histórico"><i class="fa-solid fa-clock-rotate-left"></i></button> <button class="btn btn-sm btn-erro btn-excluir" data-id="${p.id}" title="Excluir"><i class="fa-solid fa-trash-can"></i></button></td>`;
                    tableBody.appendChild(tr);
                });
            };

            const filtrarProdutos = (termo) => {
                const produtosFiltrados = produtosData.filter(p => 
                    p.nome.toLowerCase().includes(termo.toLowerCase()) ||
                    (p.sku && p.sku.toLowerCase().includes(termo.toLowerCase())) ||
                    (p.marca && p.marca.toLowerCase().includes(termo.toLowerCase()))
                );
                renderizarProdutos(produtosFiltrados);
            };

            const aplicarFiltros = () => {
                const filtroProduto = document.getElementById('filtro-produto').value.toLowerCase();
                const filtroCategoria = document.getElementById('filtro-categoria').value;
                const filtroEstoque = document.getElementById('filtro-estoque').value;

                let produtosFiltrados = produtosData.filter(p => {
                    // Filtro por nome/SKU
                    const matchProduto = !filtroProduto || 
                        p.nome.toLowerCase().includes(filtroProduto) ||
                        (p.sku && p.sku.toLowerCase().includes(filtroProduto));

                    // Filtro por categoria
                    const matchCategoria = !filtroCategoria || p.tipo === filtroCategoria;

                    // Filtro por status do estoque
                    let matchEstoque = true;
                    if (filtroEstoque) {
                        const estoque = parseInt(p.estoque_atual);
                        const estoqueMinimo = parseInt(p.estoque_minimo);
                        
                        if (filtroEstoque === 'zerado') {
                            matchEstoque = estoque <= 0;
                        } else if (filtroEstoque === 'baixo') {
                            matchEstoque = estoque > 0 && estoque <= estoqueMinimo;
                        } else if (filtroEstoque === 'normal') {
                            matchEstoque = estoque > estoqueMinimo;
                        }
                    }

                    return matchProduto && matchCategoria && matchEstoque;
                });

                renderizarProdutos(produtosFiltrados);
            };

            const carregarFornecedores = async () => {
                try {
                    const response = await fetch('api/api_produtos.php?acao=listar_fornecedores');
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        fornecedoresData = resultado.dados;
                    }
                } catch (error) {
                    console.error('Erro ao carregar fornecedores:', error);
                }
            };

            const filtrarFornecedores = (termo) => {
                if (!termo.trim()) return [];
                return fornecedoresData.filter(f => 
                    f.nome_empresa.toLowerCase().includes(termo.toLowerCase())
                ).slice(0, 5); // Limita a 5 resultados
            };

            const mostrarSugestoesFornecedor = (fornecedores, termo) => {
                fornecedorSuggestions.innerHTML = '';
                
                if (fornecedores.length === 0 && termo.trim()) {
                    // Mostra opção para cadastrar novo fornecedor
                    const novoItem = document.createElement('div');
                    novoItem.className = 'suggestion-item';
                    novoItem.style.cssText = 'padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; color: #007bff;';
                    novoItem.innerHTML = `<i class="fa-solid fa-plus"></i> Cadastrar Fornecedor: "${termo}"`;
                    novoItem.onclick = () => cadastrarNovoFornecedor(termo);
                    fornecedorSuggestions.appendChild(novoItem);
                } else {
                    fornecedores.forEach(f => {
                        const item = document.createElement('div');
                        item.className = 'suggestion-item';
                        item.style.cssText = 'padding: 10px; cursor: pointer; border-bottom: 1px solid #eee;';
                        item.innerHTML = `<strong>${f.nome_empresa}</strong>`;
                        if (f.contato_nome) item.innerHTML += `<br><small>${f.contato_nome}</small>`;
                        
                        item.onmouseover = () => item.style.backgroundColor = '#f5f5f5';
                        item.onmouseout = () => item.style.backgroundColor = 'white';
                        
                        item.onclick = () => {
                            fornecedorInput.value = f.nome_empresa;
                            fornecedorIdInput.value = f.id;
                            fornecedorSuggestions.style.display = 'none';
                        };
                        
                        fornecedorSuggestions.appendChild(item);
                    });
                }
                
                fornecedorSuggestions.style.display = fornecedorSuggestions.children.length > 0 ? 'block' : 'none';
            };

            const cadastrarNovoFornecedor = (nome) => {
                if (confirm(`Deseja cadastrar o fornecedor "${nome}"?`)) {
                    // Redireciona para a página de gestão de clientes na aba fornecedores
                    window.open('gestao_clientes.php#fornecedores', '_blank');
                }
            };

            // Event listeners para o autocomplete de fornecedor
            fornecedorInput.addEventListener('input', (e) => {
                const termo = e.target.value;
                const fornecedoresFiltrados = filtrarFornecedores(termo);
                mostrarSugestoesFornecedor(fornecedoresFiltrados, termo);
            });

            fornecedorInput.addEventListener('focus', (e) => {
                const termo = e.target.value;
                if (termo) {
                    const fornecedoresFiltrados = filtrarFornecedores(termo);
                    mostrarSugestoesFornecedor(fornecedoresFiltrados, termo);
                }
            });

            fornecedorInput.addEventListener('blur', (e) => {
                // Delay para permitir clique nas sugestões
                setTimeout(() => {
                    fornecedorSuggestions.style.display = 'none';
                }, 200);
            });

            // Limpa o ID quando o usuário digita manualmente
            fornecedorInput.addEventListener('keydown', () => {
                fornecedorIdInput.value = '';
            });

            const abrirModal = async (id = null) => {
                produtoForm.reset();
                capturedImageBlob = null;
                scannerContainer.style.display = 'none';
                imagePreview.style.display = 'none';
                imagePreview.src = '#';
                document.getElementById('produto_id').value = '';
                fornecedorInput.value = '';
                fornecedorIdInput.value = '';
                fornecedorSuggestions.style.display = 'none';
                listaVariacoesDiv.innerHTML = '';
                variacoesContainer.style.display = 'none';
                estoqueAtualInput.readOnly = false;
                if (id) {
                    modalTitle.innerHTML = `<i class="fa-solid fa-pencil"></i> Editar Produto #${id}`;
                    try {
                        const response = await fetch(`api/api_produtos.php?acao=buscar_produto_por_id&id=${id}`);
                        const resultado = await response.json();
                        if (resultado.sucesso) {
                            const p = resultado.dados;
                            document.getElementById('produto_id').value = p.id;
                            document.getElementById('produto_nome').value = p.nome;
                            document.getElementById('produto_sku').value = p.sku;
                            document.getElementById('produto_descricao').value = p.descricao;
                            document.getElementById('produto_tipo').value = p.tipo;
                            document.getElementById('produto_marca').value = p.marca;
                            document.getElementById('produto_estoque_atual').value = p.estoque_atual;
                            document.getElementById('produto_estoque_minimo').value = p.estoque_minimo;
                            document.getElementById('produto_preco_custo').value = p.preco_custo;
                            document.getElementById('produto_preco_venda').value = p.preco_venda;
                            fornecedorIdInput.value = p.fornecedor_id;
                            // Busca o nome do fornecedor pelo ID
                            const fornecedor = fornecedoresData.find(f => f.id == p.fornecedor_id);
                            if (fornecedor) {
                                fornecedorInput.value = fornecedor.nome_empresa;
                            }

                            if (p.imagem_url) {
                                imagePreview.src = p.imagem_url;
                                imagePreview.style.display = 'block';
                            }
                            if ((p.tipo === 'ROUPA' || p.tipo === 'CALCADO') && p.variacoes && p.variacoes.length > 0) {
                                p.variacoes.forEach(v => adicionarLinhaVariacao(v.tamanho, v.quantidade));
                            }
                            produtoTipoSelect.dispatchEvent(new Event('change'));
                        }
                    } catch (error) {
                        alert("Erro ao buscar dados do produto.");
                    }
                } else {
                    modalTitle.innerHTML = `<i class="fa-solid fa-plus"></i> Novo Produto`;
                }
                modal.style.display = 'flex';
            };

            const fecharModal = () => {
                stopCamera();
                if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                    html5QrcodeScanner.stop().catch(err => console.error("Erro ao parar scanner:", err));
                }
                modal.style.display = 'none';
            };
            btnNovoProduto.addEventListener('click', () => abrirModal());
            modal.querySelector('.modal-close').onclick = fecharModal;
            const startCamera = async () => {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'environment'
                            }
                        });
                        currentStream = stream;
                        cameraStream.srcObject = stream;
                        cameraContainer.style.display = 'flex';
                    } catch (error) {
                        console.error("Erro ao acessar a câmera: ", error);
                        alert("Não foi possível acessar a câmera.");
                    }
                } else {
                    alert("Seu navegador não suporta o acesso à câmera.");
                }
            };
            const stopCamera = () => {
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                    currentStream = null;
                }
                cameraContainer.style.display = 'none';
            };
            const takePicture = () => {
                const context = cameraCanvas.getContext('2d');
                cameraCanvas.width = cameraStream.videoWidth;
                cameraCanvas.height = cameraStream.videoHeight;
                context.drawImage(cameraStream, 0, 0, cameraCanvas.width, cameraCanvas.height);
                const dataUrl = cameraCanvas.toDataURL('image/jpeg');
                imagePreview.src = dataUrl;
                imagePreview.style.display = 'block';
                cameraCanvas.toBlob(blob => {
                    capturedImageBlob = blob;
                }, 'image/jpeg', 0.9);
                stopCamera();
            };
            btnTakePhoto.addEventListener('click', startCamera);
            btnCloseCamera.addEventListener('click', stopCamera);
            btnSnapPhoto.addEventListener('click', takePicture);

            produtoForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(produtoForm);
                if (capturedImageBlob) {
                    formData.delete('imagem');
                    formData.append('imagem', capturedImageBlob, 'foto-capturada.jpg');
                }
                const btn = e.target.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
                try {
                    const response = await fetch('api/api_produtos.php?acao=salvar_produto', {
                        method: 'POST',
                        body: formData
                    });
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        alert(resultado.mensagem);
                        fecharModal();
                        carregarProdutos();
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    if (error.message.includes('Erro sku-01')) {
                        alert(error.message);
                        const skuInput = document.getElementById('produto_sku');
                        skuInput.focus();
                        skuInput.select();
                    } else {
                        alert("Erro ao salvar: " + error.message);
                    }
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Salvar Produto';
                }
            });

            imageSelectInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (file) {
                    capturedImageBlob = null;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
            btnSelectImage.addEventListener('click', () => {
                imageSelectInput.click();
            });
            const iniciarScanner = () => {
                if (scannerContainer.style.display === 'block') {
                    if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                        html5QrcodeScanner.stop();
                    }
                    scannerContainer.style.display = 'none';
                    return;
                }
                scannerContainer.style.display = 'block';
                html5QrcodeScanner = new Html5Qrcode("scanner-container");
                const onScanSuccess = (decodedText, decodedResult) => {
                    document.getElementById('produto_sku').value = decodedText;
                    if (html5QrcodeScanner.isScanning) {
                        html5QrcodeScanner.stop();
                    }
                    scannerContainer.style.display = 'none';
                };
                html5QrcodeScanner.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 150
                    }
                }, onScanSuccess, (errorMessage) => {}).catch(err => {
                    alert("Erro ao iniciar a câmera: " + err);
                    scannerContainer.style.display = 'none';
                });
            };
            btnScanBarcode.addEventListener('click', iniciarScanner);
            produtoTipoSelect.addEventListener('change', (e) => {
                const tipo = e.target.value;
                if (tipo === 'ROUPA' || tipo === 'CALCADO') {
                    variacoesContainer.style.display = 'block';
                    estoqueAtualInput.readOnly = true;
                    atualizarEstoqueTotal();
                } else {
                    variacoesContainer.style.display = 'none';
                    estoqueAtualInput.readOnly = false;
                }
            });
            btnAddVariacao.addEventListener('click', () => {
                const tamanhoInput = document.getElementById('novo-tamanho');
                const quantidadeInput = document.getElementById('nova-quantidade');
                adicionarLinhaVariacao(tamanhoInput.value, quantidadeInput.value);
                tamanhoInput.value = '';
                quantidadeInput.value = '';
                tamanhoInput.focus();
            });

            function adicionarLinhaVariacao(tamanho, quantidade) {
                const tamanhoValido = String(tamanho).trim();
                const quantidadeValida = parseInt(quantidade);
                if (tamanhoValido && quantidadeValida >= 0) {
                    const variacaoId = `variacao_${Date.now()}`;
                    const novaLinhaHTML = `<div class="form-grid" id="${variacaoId}" style="margin-bottom: 0.5rem; gap: 0.5rem;"><input type="text" class="form-input" name="variacoes[tamanho][]" value="${tamanhoValido}" readonly><input type="number" class="form-input" name="variacoes[quantidade][]" value="${quantidadeValida}" readonly><button type="button" class="btn btn-erro" onclick="removerVariacao(this)">Remover</button></div>`;
                    listaVariacoesDiv.insertAdjacentHTML('beforeend', novaLinhaHTML);
                    atualizarEstoqueTotal();
                } else {
                    alert('Por favor, informe um tamanho e uma quantidade válida.');
                }
            }
            window.removerVariacao = (btn) => {
                btn.closest('.form-grid').remove();
                atualizarEstoqueTotal();
            }

            function atualizarEstoqueTotal() {
                let total = 0;
                const quantidades = document.querySelectorAll('#lista-variacoes input[name="variacoes[quantidade][]"]');
                quantidades.forEach(input => {
                    total += parseInt(input.value) || 0;
                });
                estoqueAtualInput.value = total;
            }
            tableBody.addEventListener('click', async (e) => {
                const target = e.target.closest('button');
                if (!target) return;
                const id = target.dataset.id;
                if (target.classList.contains('btn-editar')) {
                    abrirModal(id);
                } else if (target.classList.contains('btn-excluir')) {
                    if (confirm(`Tem certeza que deseja excluir o produto #${id}?`)) {
                        const response = await fetch('api/api_produtos.php?acao=excluir_produto', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id
                            })
                        });
                        const resultado = await response.json();
                        alert(resultado.mensagem);
                        if (resultado.sucesso) carregarProdutos();
                    }
                } else if (target.classList.contains('btn-estoque')) {
                    abrirModalAjusteEstoque(id);
                } else if (target.classList.contains('btn-movimentacao')) {
                    abrirModalMovimentacao(id);
                } else if (target.classList.contains('btn-historico')) {
                    visualizarHistoricoEstoque(id);
                }
            });
            async function abrirModalAjusteEstoque(id) {
                estoqueForm.reset();
                document.getElementById('estoque-produto-id').value = id;
                const listaDiv = document.getElementById('lista-estoque-variacoes');
                listaDiv.innerHTML = 'Carregando...';
                estoqueModal.style.display = 'flex';
                const response = await fetch(`api/api_produtos.php?acao=buscar_produto_por_id&id=${id}`);
                const resultado = await response.json();
                if (resultado.sucesso && resultado.dados.variacoes) {
                    document.getElementById('estoque-modal-title').textContent = `Ajustar Estoque de "${resultado.dados.nome}"`;
                    listaDiv.innerHTML = '';
                    resultado.dados.variacoes.forEach(v => {
                        const div = document.createElement('div');
                        div.className = 'form-grid';
                        div.style.marginBottom = '0.5rem';
                        div.innerHTML = `<div class="form-group"><label class="form-label">Tamanho</label><input type="text" class="form-input" value="${v.tamanho}" readonly></div><div class="form-group"><label class="form-label">Quantidade Atual</label><input type="number" class="form-input" value="${v.quantidade}" min="0" required></div>`;
                        listaDiv.appendChild(div);
                    });
                } else {
                    listaDiv.innerHTML = '<p class="error-message">Não foi possível carregar as variações.</p>';
                }
            }
            estoqueModal.querySelector('.modal-close').onclick = () => estoqueModal.style.display = 'none';
            estoqueForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const produtoId = document.getElementById('estoque-produto-id').value;
                const variacoes = [];
                document.querySelectorAll('#lista-estoque-variacoes .form-grid').forEach(row => {
                    variacoes.push({
                        tamanho: row.querySelector('input[readonly]').value,
                        quantidade: row.querySelector('input[type="number"]').value
                    });
                });
                const payload = {
                    produto_id: produtoId,
                    observacao: document.getElementById('estoque-observacao').value,
                    variacoes: variacoes
                };
                const btn = e.target.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
                try {
                    const response = await fetch('api/api_produtos.php?acao=ajustar_estoque_variacao', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        alert(resultado.mensagem);
                        estoqueModal.style.display = 'none';
                        carregarProdutos();
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    alert('Erro: ' + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Salvar Ajuste de Estoque';
                }
            });

            // Event listeners para busca e filtros
            searchInput.addEventListener('input', (e) => {
                const termo = e.target.value.trim();
                if (termo === '') {
                    renderizarProdutos(produtosData);
                } else {
                    filtrarProdutos(termo);
                }
            });

            // Event listeners para os filtros
            document.getElementById('filtro-produto').addEventListener('input', aplicarFiltros);
            document.getElementById('filtro-categoria').addEventListener('change', aplicarFiltros);
            document.getElementById('filtro-estoque').addEventListener('change', aplicarFiltros);

            // Botão limpar filtros
            document.getElementById('btn-limpar-filtros').addEventListener('click', () => {
                document.getElementById('filtro-produto').value = '';
                document.getElementById('filtro-categoria').value = '';
                document.getElementById('filtro-estoque').value = '';
                renderizarProdutos(produtosData);
            });

            // Funções para movimentação de estoque
            const carregarMovimentacoesRecentes = async () => {
                try {
                    const response = await fetch('api/api_produtos.php?acao=listar_movimentacoes_recentes');
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        renderizarMovimentacoes(resultado.dados);
                    }
                } catch (error) {
                    document.getElementById('movimentacoes-container').innerHTML = 
                        '<p style="text-align: center; color: #e74c3c; margin: 2rem 0;">Erro ao carregar movimentações.</p>';
                }
            };

            const renderizarMovimentacoes = (movimentacoes) => {
                const container = document.getElementById('movimentacoes-container');
                if (movimentacoes.length === 0) {
                    container.innerHTML = '<p style="text-align: center; color: #666; margin: 2rem 0;">Nenhuma movimentação encontrada.</p>';
                    return;
                }

                let html = '<div style="display: grid; gap: 1rem;">';
                movimentacoes.forEach(m => {
                    const tipoClass = m.tipo === 'ENTRADA' ? 'success' : m.tipo === 'SAIDA' ? 'danger' : 'warning';
                    const tipoIcon = m.tipo === 'ENTRADA' ? 'fa-arrow-up' : m.tipo === 'SAIDA' ? 'fa-arrow-down' : 'fa-arrows-rotate';
                    
                    html += `
                        <div style="background: white; border: 1px solid #eee; border-radius: 8px; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--cor-${tipoClass}); color: white; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid ${tipoIcon}"></i>
                                </div>
                                <div>
                                    <h4 style="margin: 0; color: #333;">${m.produto_nome}</h4>
                                    <p style="margin: 0.25rem 0 0 0; color: #666; font-size: 0.9rem;">${m.observacao || 'Sem observação'}</p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: bold; color: var(--cor-${tipoClass}); margin-bottom: 0.25rem;">
                                    ${m.tipo === 'ENTRADA' ? '+' : m.tipo === 'SAIDA' ? '-' : '±'}${m.quantidade}
                                </div>
                                <div style="font-size: 0.8rem; color: #666;">
                                    ${new Date(m.data_movimentacao).toLocaleDateString('pt-BR')} às ${new Date(m.data_movimentacao).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            };

            const abrirModalMovimentacao = async (produtoId) => {
                const movimentacaoModal = document.getElementById('movimentacao-modal');
                const movimentacaoForm = document.getElementById('movimentacao-form');
                
                movimentacaoForm.reset();
                document.getElementById('movimentacao-produto-id').value = produtoId;
                
                // Buscar dados do produto
                try {
                    const response = await fetch(`api/api_produtos.php?acao=buscar_produto_por_id&id=${produtoId}`);
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        document.getElementById('movimentacao-produto-nome').value = resultado.dados.nome;
                    }
                } catch (error) {
                    console.error('Erro ao buscar produto:', error);
                }
                
                movimentacaoModal.style.display = 'flex';
            };

            const visualizarHistoricoEstoque = (produtoId) => {
                // Abrir em nova aba o histórico do produto
                window.open(`historico_estoque.php?produto_id=${produtoId}`, '_blank');
            };

            // Event listeners para movimentação
            document.getElementById('movimentacao-modal').querySelector('.modal-close').onclick = () => {
                document.getElementById('movimentacao-modal').style.display = 'none';
            };
            
            document.getElementById('btn-cancelar-movimentacao').onclick = () => {
                document.getElementById('movimentacao-modal').style.display = 'none';
            };

            document.getElementById('movimentacao-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                const dados = {
                    produto_id: formData.get('produto_id'),
                    tipo: formData.get('tipo'),
                    quantidade: formData.get('quantidade'),
                    observacao: formData.get('observacao')
                };
                
                const btn = e.target.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
                
                try {
                    const response = await fetch('api/api_produtos.php?acao=salvar_movimentacao', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(dados)
                    });
                    
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        alert(resultado.mensagem);
                        document.getElementById('movimentacao-modal').style.display = 'none';
                        carregarProdutos();
                        carregarMovimentacoesRecentes();
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    alert('Erro: ' + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Salvar Movimentação';
                }
            });

            // Event listener para gerar relatório
            document.getElementById('btn-gerar-relatorio').addEventListener('click', () => {
                window.open('relatorio_estoque.php', '_blank');
            });

            carregarProdutos();
            carregarFornecedores();
            carregarMovimentacoesRecentes();
        });
    </script>
    

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>