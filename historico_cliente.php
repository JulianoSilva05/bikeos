<?php
require_once 'auth_check.php';
$cliente_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$aba_inicial = isset($_GET['aba']) ? $_GET['aba'] : 'bicicletas';
// Se nenhum ID for fornecido, redireciona para a gestão de clientes
if ($cliente_id <= 0) {
    header('Location: gestao_clientes.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Histórico do Cliente'); ?></title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .cliente-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .cliente-info-card {
            flex-grow: 1;
        }

        .cliente-info-card .info-item {
            margin-bottom: 0.5rem;
        }

        .cliente-info-card strong {
            color: var(--cor-texto-secundario);
        }

        .search-container {
            position: relative;
            min-width: 300px;
        }

        .search-results-list {
            list-style: none;
            padding: 0;
            margin: 5px 0 0;
            position: absolute;
            width: 100%;
            max-height: 250px;
            overflow-y: auto;
            background: var(--cor-branco);
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            z-index: 1000;
        }

        .search-results-list li {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid var(--cor-borda);
        }

        .search-results-list li:hover {
            background-color: var(--cor-fundo-claro);
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid var(--cor-borda);
            margin: 2rem 0 1rem;
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
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        #bike-image-preview {
            max-width: 200px;
            max-height: 200px;
            margin: 1rem auto;
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            display: none;
            object-fit: cover;
        }

        .form-grid-half {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-grid-third {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .resumo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .resumo-card h3 {
            margin-top: 0;
            border-bottom: 2px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .resumo-card ul {
            padding-left: 20px;
            margin: 0;
        }

        .resumo-card li {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .avaria-item {
            display: grid;
            grid-template-columns: 1fr auto auto auto auto;
            gap: 0.5rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .avaria-item .form-group {
            margin-bottom: 0;
        }

        .avaria-image-preview {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
        }

        #bicicleta-modal,
        #vender-bike-modal {
            z-index: 1050;
        }

        #camera-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 1060;
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        #camera-view {
            max-width: 100%;
            max-height: 80%;
        }

        #camera-controls {
            margin-top: 1rem;
            display: flex;
            gap: 1rem;
        }

        #snap-photo-btn {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 5px solid white;
            background-color: rgba(255, 255, 255, 0.3);
        }

        .valor-restante-negativo {
            color: var(--cor-erro);
            font-weight: bold;
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
        <div class="page-header-with-nav">
            <div class="page-title">
                <h1><i class="fa-solid fa-user-clock"></i> Histórico do Cliente</h1>
            </div>
            <div class="page-navigation">
                <a href="gestao_clientes.php" class="btn btn-secundario" title="Voltar para Gestão de Clientes"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
                <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Ir para o Painel Principal"><i class="fa-solid fa-house"></i></a>
            </div>
        </div>
        <div class="card cliente-header">
            <div class="cliente-info-card">
                <h2 id="cliente-nome">Carregando...</h2>
                <div class="info-item"><strong>ID:</strong> <span id="cliente-id"><?php echo $cliente_id; ?></span></div>
                <div class="info-item"><strong>Telefone:</strong> <span id="cliente-telefone"></span></div>
                <div class="info-item"><strong>Email:</strong> <span id="cliente-email"></span></div>
                <div class="info-item"><strong>Endereço:</strong> <span id="cliente-endereco"></span></div>
            </div>
            <div class="search-container">
                <div class="form-group">
                    <label for="search-cliente" class="form-label">Buscar Outro Cliente</label>
                    <input type="search" id="search-cliente" class="form-input" placeholder="Nome, CPF ou Telefone...">
                    <ul id="search-results-cliente" class="search-results-list" style="display:none;"></ul>
                </div>
            </div>
        </div>
        <div class="resumo-grid">
            <div class="card resumo-card">
                <h3><i class="fa-solid fa-chart-line"></i> Resumo Financeiro</h3>
                <div id="resumo-financeiro">
                    <p>Carregando...</p>
                </div>
            </div>
            <div class="card resumo-card">
                <h3><i class="fa-solid fa-star"></i> Itens Frequentes</h3>
                <div id="itens-frequentes">
                    <p>Carregando...</p>
                </div>
            </div>
            <div class="card resumo-card">
                <h3><i class="fa-solid fa-lightbulb"></i> Sugestões para o Cliente</h3>
                <div id="sugestoes-produtos">
                    <p>Carregando...</p>
                </div>
            </div>
        </div>
        <div class="tabs">
            <button class="tab-link active" data-tab="bicicletas"><i class="fa-solid fa-bicycle"></i> Bicicletas</button>
            <button class="tab-link" data-tab="ordens_servico"><i class="fa-solid fa-wrench"></i> Ordens de Serviço</button>
            <button class="tab-link" data-tab="compras"><i class="fa-solid fa-shopping-cart"></i> Compras</button>
            <button class="tab-link" data-tab="crediarios"><i class="fa-solid fa-file-invoice-dollar"></i> Crediários</button>
            <button class="tab-link" data-tab="bikefit"><i class="fa-solid fa-ruler-combined"></i> BikeFit</button>
        </div>
        <div id="bicicletas" class="tab-content active">
            <div class="card">
                <button id="btn-nova-bicicleta" class="btn btn-destaque" style="margin-bottom:1rem;"><i class="fa-solid fa-plus"></i> Adicionar Bicicleta</button>
                <div style="overflow-x:auto;">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Cor</th>
                                <th>Nº Série</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="bicicletas-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="ordens_servico" class="tab-content">
            <div class="card">
                <div style="overflow-x:auto;">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>OS</th>
                                <th>Data</th>
                                <th>Bicicleta</th>
                                <th>Status</th>
                                <th>Valor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="os-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="compras" class="tab-content">
            <div class="card">
                <div style="overflow-x:auto;">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>Venda</th>
                                <th>Data</th>
                                <th>Pagamento</th>
                                <th>Status</th>
                                <th>Valor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="compras-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="crediarios" class="tab-content">
            <div class="card">
                <h3><i class="fa-solid fa-file-invoice-dollar"></i> Contas em Aberto</h3>
                <div style="overflow-x:auto;">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>Venda ID</th>
                                <th>Data</th>
                                <th>Valor Total</th>
                                <th>Valor Pago</th>
                                <th>Restante</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="crediarios-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="bikefit" class="tab-content">
            <div class="card">
                <form id="bikefit-form">
                    <h3><i class="fa-solid fa-ruler"></i> Medidas do Ciclista e da Bike</h3>
                    <input type="hidden" name="id" id="bikefit-id">
                    <input type="hidden" name="cliente_id" value="<?php echo $cliente_id; ?>">
                    <div class="form-grid-third">
                        <div class="form-group"><label for="data_fit" class="form-label">Data do Fit</label><input type="date" id="data_fit" name="data_fit" class="form-input" required></div>
                        <div class="form-group"><label for="altura_ciclista" class="form-label">Altura (cm)</label><input type="number" step="0.01" id="altura_ciclista" name="altura_ciclista" class="form-input"></div>
                        <div class="form-group"><label for="altura_cavalo" class="form-label">Cavalo (cm)</label><input type="number" step="0.01" id="altura_cavalo" name="altura_cavalo" class="form-input"></div>
                    </div>
                    <div class="form-grid-third">
                        <div class="form-group"><label for="altura_selim" class="form-label">Altura Selim (cm)</label><input type="number" step="0.01" id="altura_selim" name="altura_selim" class="form-input"></div>
                        <div class="form-group"><label for="recuo_selim" class="form-label">Recuo Selim (cm)</label><input type="number" step="0.01" id="recuo_selim" name="recuo_selim" class="form-input"></div>
                        <div class="form-group"><label for="altura_guidao" class="form-label">Altura Guidão (cm)</label><input type="number" step="0.01" id="altura_guidao" name="altura_guidao" class="form-input"></div>
                    </div>
                    <div class="form-group"><label for="distancia_guidao_selim" class="form-label">Distância Guidão-Selim (cm)</label><input type="number" step="0.01" id="distancia_guidao_selim" name="distancia_guidao_selim" class="form-input"></div>
                    <div class="form-group"><label for="bikefit-observacoes" class="form-label">Observações</label><textarea id="bikefit-observacoes" name="observacoes" class="form-input" rows="4"></textarea></div>
                    <button type="submit" class="btn btn-destaque" style="width: 100%;">Salvar BikeFit</button>
                </form>
            </div>
        </div>
    </main>
    <div id="bicicleta-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="bicicleta-modal-title">Nova Bicicleta</h2>
            <form id="bicicleta-form" enctype="multipart/form-data">
                <input type="hidden" name="id" id="bicicleta-id">
                <input type="hidden" name="cliente_id" id="bicicleta-cliente-id" value="<?php echo $cliente_id; ?>">
                <input type="hidden" name="imagem_url_existente" id="bicicleta-imagem-url-existente">
                <img id="bike-image-preview" src="#" alt="Prévia da imagem">
                <div class="form-group">
                    <label class="form-label">Foto da Bicicleta</label>
                    <input type="file" id="bicicleta-imagem" name="imagem" class="form-input" accept="image/*" style="display:none;">
                    <div class="btn-group">
                        <button type="button" id="btn-galeria-principal" class="btn btn-secundario"><i class="fa-solid fa-upload"></i> Galeria</button>
                        <button type="button" id="btn-camera-principal" class="btn btn-secundario"><i class="fa-solid fa-camera"></i> Câmera</button>
                    </div>
                </div>
                <div class="form-grid-half">
                    <div class="form-group"><label for="bicicleta-marca" class="form-label">Marca</label><input type="text" id="bicicleta-marca" name="marca" class="form-input" required></div>
                    <div class="form-group"><label for="bicicleta-modelo" class="form-label">Modelo</label><input type="text" id="bicicleta-modelo" name="modelo" class="form-input"></div>
                </div>
                <div class="form-grid-half">
                    <div class="form-group"><label for="bicicleta-cor" class="form-label">Cor</label><input type="text" id="bicicleta-cor" name="cor" class="form-input"></div>
                    <div class="form-group"><label for="bicicleta-aro" class="form-label">Aro</label><input type="text" id="bicicleta-aro" name="aro" class="form-input"></div>
                </div>
                <div class="form-group"><label for="bicicleta-serie" class="form-label">Nº de Série</label><input type="text" id="bicicleta-serie" name="numero_serie" class="form-input"></div>
                <div class="form-group">
                    <label class="form-label">Avarias (riscos, etc)</label>
                    <div id="avarias-container"></div>
                    <button type="button" id="btn-add-avaria" class="btn btn-secundario btn-sm" style="margin-top: 0.5rem;"><i class="fa-solid fa-plus"></i> Adicionar Avaria</button>
                </div>
                <div class="form-group"><label for="bicicleta-observacoes" class="form-label">Observações Gerais</label><textarea id="bicicleta-observacoes" name="observacoes" class="form-input" rows="3"></textarea></div>
                <button type="submit" class="btn btn-destaque" style="width: 100%;">Salvar Bicicleta</button>
            </form>
        </div>
    </div>
    <div id="camera-overlay">
        <video id="camera-view" autoplay playsinline></video>
        <div id="camera-controls">
            <button type="button" id="snap-photo-btn"></button>
            <button type="button" id="close-camera-btn" class="btn btn-erro"><i class="fa-solid fa-times"></i></button>
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
                <div class="form-group"><label for="valor-pago" class="form-label">Valor a Pagar (R$)</label><input type="number" step="0.01" id="valor-pago" class="form-input" required></div>
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
    <div id="vender-bike-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-arrow-right-arrow-left"></i> Vender / Transferir Bicicleta</h2>
            <p>Para quem você deseja transferir a bicicleta <strong id="vender-bike-nome"></strong>?</p>
            <form id="form-vender-bike">
                <input type="hidden" id="vender-bike-id">
                <div class="form-group search-container">
                    <label for="search-novo-proprietario" class="form-label">Buscar Novo Proprietário</label>
                    <input type="search" id="search-novo-proprietario" class="form-input" placeholder="Digite o nome ou telefone...">
                    <ul id="search-results-novo-proprietario" class="search-results-list" style="display:none;"></ul>
                </div>
                <div id="novo-proprietario-selecionado" style="display:none;" class="card"></div>
                <div id="form-novo-cliente-container" style="display:none; margin-top:1rem;">
                    <h4>Cadastrar Novo Cliente</h4>
                    <div class="form-group"><label for="novo-cliente-nome">Nome Completo</label><input type="text" id="novo-cliente-nome" class="form-input"></div>
                    <div class="form-group"><label for="novo-cliente-telefone">Telefone</label><input type="tel" id="novo-cliente-telefone" class="form-input"></div>
                </div>
                <button type="submit" class="btn btn-destaque" style="width:100%; margin-top:1rem;">Confirmar Transferência</button>
            </form>
        </div>
    </div>
    <?php include '_partials/menu_admin.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let debounceTimer;
            const clienteIdAtual = <?php echo $cliente_id; ?>;
            let historicoData = {};
            const bicicletaModal = document.getElementById('bicicleta-modal');
            const bicicletaForm = document.getElementById('bicicleta-form');
            const btnNovaBicicleta = document.getElementById('btn-nova-bicicleta');
            const cameraOverlay = document.getElementById('camera-overlay');
            const cameraView = document.getElementById('camera-view');
            const snapPhotoButton = document.getElementById('snap-photo-btn');
            const closeCameraButton = document.getElementById('close-camera-btn');
            const pagamentoModal = document.getElementById('pagamento-crediario-modal');
            const pagamentoForm = document.getElementById('form-pagamento-crediario');
            const searchClienteInput = document.getElementById('search-cliente');
            const searchClienteResults = document.getElementById('search-results-cliente');
            const venderBikeModal = document.getElementById('vender-bike-modal');
            const venderBikeForm = document.getElementById('form-vender-bike');
            let currentFileInput = null;
            let novoProprietarioId = null;

            const openCamera = (fileInput) => {
                currentFileInput = fileInput;
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Seu navegador não suporta a funcionalidade de câmera.');
                    return;
                }
                navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: "environment"
                        }
                    })
                    .then(stream => {
                        cameraView.srcObject = stream;
                        cameraOverlay.style.display = 'flex';
                    })
                    .catch(err => {
                        console.error("Erro ao acessar a câmera: ", err);
                        let msg = "Não foi possível acessar a câmera.\n\nVerifique se você permitiu o acesso no seu navegador.";
                        if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                            msg += "\n\nAVISO: O acesso à câmera geralmente requer uma conexão segura (HTTPS).";
                        }
                        if (err.name === "NotAllowedError") {
                            msg = "Você bloqueou o acesso à câmera. Por favor, libere a permissão nas configurações do seu navegador para este site.";
                        } else if (err.name === "NotFoundError") {
                            msg = "Nenhuma câmera foi encontrada no seu dispositivo.";
                        }
                        alert(msg);
                    });
            };

            const closeCamera = () => {
                const stream = cameraView.srcObject;
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                cameraOverlay.style.display = 'none';
            };

            snapPhotoButton.addEventListener('click', () => {
                const canvas = document.createElement('canvas');
                canvas.width = cameraView.videoWidth;
                canvas.height = cameraView.videoHeight;
                canvas.getContext('2d').drawImage(cameraView, 0, 0);
                canvas.toBlob(blob => {
                    const file = new File([blob], `foto_${new Date().getTime()}.jpg`, {
                        type: 'image/jpeg'
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    currentFileInput.files = dataTransfer.files;
                    currentFileInput.dispatchEvent(new Event('change'));
                }, 'image/jpeg');
                closeCamera();
            });

            const carregarHistorico = async (clienteId) => {
                try {
                    const response = await fetch(`api/api_clientes.php?acao=get_historico_completo&id=${clienteId}`);
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    historicoData = resultado.dados;
                    document.getElementById('cliente-nome').textContent = historicoData.cliente.nome;
                    document.getElementById('cliente-telefone').textContent = historicoData.cliente.telefone || 'Não informado';
                    document.getElementById('cliente-email').textContent = historicoData.cliente.email || 'Não informado';
                    // Construir endereço completo a partir dos campos granulares
                    const enderecoCompleto = [
                        historicoData.cliente.logradouro,
                        historicoData.cliente.numero,
                        historicoData.cliente.complemento,
                        historicoData.cliente.bairro,
                        historicoData.cliente.cidade,
                        historicoData.cliente.estado,
                        historicoData.cliente.cep
                    ].filter(campo => campo && campo.trim() !== '').join(', ');
                    document.getElementById('cliente-endereco').textContent = enderecoCompleto || 'Não informado';
                    renderizarResumos(historicoData.totais, historicoData.frequentes);
                    carregarSugestoes(clienteId);
                    renderizarTabelaBicicletas();
                    renderizarTabelaOS();
                    renderizarTabelaCompras();
                    renderizarTabelaCrediarios();
                    preencherFormBikeFit(historicoData.bikefit);
                } catch (error) {
                    alert('Erro ao carregar histórico: ' + error.message);
                }
            };

            const carregarSugestoes = async (clienteId) => {
                const sugestoesDiv = document.getElementById('sugestoes-produtos');
                sugestoesDiv.innerHTML = '<p>Carregando sugestões...</p>';
                try {
                    const response = await fetch(`api/api_sugestoes.php?acao=gerar_sugestoes_cliente&cliente_id=${clienteId}`);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    if (resultado.dados.length > 0) {
                        let html = '<ul style="list-style: none; padding: 0;">';
                        resultado.dados.forEach(prod => {
                            const preco = parseFloat(prod.preco_venda).toFixed(2).replace('.',',');
                            html += `
                                <li style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: background-color 0.3s;" 
                                    class="sugestao-item" 
                                    data-produto="${prod.produto_sugerido}" 
                                    data-preco="${preco}"
                                    onmouseover="this.style.backgroundColor='#f0f0f0'" 
                                    onmouseout="this.style.backgroundColor='white'">
                                    <strong>${prod.produto_sugerido}</strong><br>
                                    <span style="color: #28a745; font-weight: bold;">R$ ${preco}</span><br>
                                    <small style="color: #666;">📱 Clique para enviar oferta via WhatsApp</small>
                                </li>
                            `;
                        });
                        html += '</ul>';
                        sugestoesDiv.innerHTML = html;
                        
                        // Adicionar event listeners para os itens de sugestão
                        document.querySelectorAll('.sugestao-item').forEach(item => {
                            item.addEventListener('click', () => {
                                const produto = item.dataset.produto;
                                const preco = item.dataset.preco;
                                enviarOfertaWhatsApp(produto, preco);
                            });
                        });
                    } else {
                        sugestoesDiv.innerHTML = '<p>Nenhuma sugestão nova para este cliente no momento.</p>';
                    }
                } catch (e) {
                    console.error("Erro ao buscar sugestões:", e);
                    sugestoesDiv.innerHTML = '<p class="error-message">Erro ao buscar sugestões.</p>';
                }
            };

            const enviarOfertaWhatsApp = async (produto, preco) => {
                const clienteNome = historicoData.cliente.nome;
                const clienteTelefone = historicoData.cliente.telefone;
                
                if (!clienteTelefone) {
                    alert('Cliente não possui telefone cadastrado.');
                    return;
                }
                
                // Limpar o telefone (remover caracteres especiais)
                const telefoneNumeros = clienteTelefone.replace(/\D/g, '');
                
                // Verificar se o telefone tem o formato correto
                if (telefoneNumeros.length < 10) {
                    alert('Telefone do cliente inválido.');
                    return;
                }
                
                // Adicionar código do país se necessário
                const telefoneCompleto = telefoneNumeros.startsWith('55') ? telefoneNumeros : '55' + telefoneNumeros;
                
                // Buscar nome da loja das configurações
                let nomeLoja = 'Sua bike shop de confiança';
                try {
                    const response = await fetch('api/api_funcionalidades.php?action=get_configuracoes');
                    const resultado = await response.json();
                    if (resultado.success && resultado.configuracoes && resultado.configuracoes.nome_loja) {
                        nomeLoja = resultado.configuracoes.nome_loja;
                    }
                } catch (error) {
                    console.error('Erro ao buscar configurações da loja:', error);
                }
                
                // Criar mensagem personalizada
                const mensagem = `Olá ${clienteNome}! 😊\n\nTenho uma oferta especial para você!\n\n🚴‍♂️ *${produto}*\n💰 Por apenas *R$ ${preco}*\n\nEste produto é perfeito para você, baseado no seu histórico de compras.\n\nTem interesse? Posso separar para você! 🎯\n\n${nomeLoja} - Sua bike shop de confiança! 🚲`;
                
                // Codificar a mensagem para URL
                const mensagemCodificada = encodeURIComponent(mensagem);
                
                // Criar URL do WhatsApp
                const urlWhatsApp = `https://wa.me/${telefoneCompleto}?text=${mensagemCodificada}`;
                
                // Abrir WhatsApp em nova aba
                window.open(urlWhatsApp, '_blank');
            };

            const renderizarResumos = (totais, frequentes) => {
                document.getElementById('resumo-financeiro').innerHTML = `<p><strong>Total em Vendas:</strong> R$ ${parseFloat(totais.vendas).toFixed(2).replace('.',',')}</p><p><strong>Total em Serviços:</strong> R$ ${parseFloat(totais.servicos).toFixed(2).replace('.',',')}</p><p><strong>Total Geral:</strong> R$ ${(parseFloat(totais.vendas) + parseFloat(totais.servicos)).toFixed(2).replace('.',',')}</p>`;
                let frequentesHtml = '<h4>Produtos Mais Comprados</h4>';
                frequentesHtml += (frequentes.produtos.length > 0) ? `<ul>${frequentes.produtos.map(item => `<li>${item.nome} (${item.qtd}x)</li>`).join('')}</ul>` : '<p>Nenhum produto.</p>';
                frequentesHtml += '<h4 style="margin-top:1rem;">Serviços Mais Realizados</h4>';
                frequentesHtml += (frequentes.servicos.length > 0) ? `<ul>${frequentes.servicos.map(item => `<li>${item.nome} (${item.qtd}x)</li>`).join('')}</ul>` : '<p>Nenhum serviço.</p>';
                document.getElementById('itens-frequentes').innerHTML = frequentesHtml;
            };

            const renderizarTabelaBicicletas = () => {
                const tbody = document.getElementById('bicicletas-tbody');
                tbody.innerHTML = '';
                if (!historicoData.bicicletas || historicoData.bicicletas.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Nenhuma bicicleta cadastrada.</td></tr>`;
                    return;
                }
                historicoData.bicicletas.forEach(bike => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                    <td>${bike.id}</td><td>${bike.marca || '-'}</td><td>${bike.modelo || '-'}</td><td>${bike.cor || '-'}</td><td>${bike.numero_serie || '-'}</td>
                    <td class="btn-group">
                        <button class="btn btn-sm btn-secundario btn-ver-bike" data-bike-id="${bike.id}"><i class="fa-solid fa-eye"></i> Ver/Editar</button>
                        <button class="btn btn-sm btn-sucesso btn-vender-bike" data-bike-id="${bike.id}"><i class="fa-solid fa-dollar-sign"></i> Vender</button>
                    </td>`;
                    tbody.appendChild(tr);
                });
                tbody.querySelectorAll('.btn-ver-bike').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const bikeId = parseInt(e.currentTarget.dataset.bikeId);
                        const bike = historicoData.bicicletas.find(b => b.id === bikeId);
                        abrirModalBicicleta(bike);
                    });
                });
                tbody.querySelectorAll('.btn-vender-bike').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const bikeId = parseInt(e.currentTarget.dataset.bikeId);
                        const bike = historicoData.bicicletas.find(b => b.id === bikeId);
                        abrirModalVenderBike(bike);
                    });
                });
            };

            const renderizarTabelaOS = () => {
                const tbody = document.getElementById('os-tbody');
                tbody.innerHTML = '';
                if (!historicoData.ordens_servico || historicoData.ordens_servico.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Nenhum registro encontrado.</td></tr>`;
                    return;
                }
                historicoData.ordens_servico.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>#${item.id}</td><td>${new Date(item.data_entrada).toLocaleDateString('pt-BR')}</td><td>${item.bicicleta_info || '-'}</td><td>${item.status.replace('_', ' ')}</td><td>R$ ${parseFloat(item.valor_total).toFixed(2).replace('.',',')}</td><td><a href="editar_os.php?id=${item.id}" class="btn btn-sm btn-secundario"><i class="fa-solid fa-eye"></i> Ver</a></td>`;
                    tbody.appendChild(tr);
                });
            };

            const renderizarTabelaCompras = () => {
                const tbody = document.getElementById('compras-tbody');
                tbody.innerHTML = '';
                if (!historicoData.compras || historicoData.compras.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Nenhum registro encontrado.</td></tr>`;
                    return;
                }
                historicoData.compras.forEach(item => {
                    const tr = document.createElement('tr');
                    const status = (item.status_pagamento === 'Pendente') ? `<span class="status-pendente">${item.status_pagamento}</span>` : `<span class="status-pago">${item.status_pagamento}</span>`;
                    tr.innerHTML = `<td>#${item.id}</td><td>${new Date(item.data_venda).toLocaleDateString('pt-BR')}</td><td>${item.metodo_pagamento || '-'}</td><td>${status}</td><td>R$ ${parseFloat(item.valor_total).toFixed(2).replace('.',',')}</td><td><a href="imprimir_venda.php?id=${item.id}" target="_blank" class="btn btn-sm btn-secundario"><i class="fa-solid fa-print"></i> Imprimir</a></td>`;
                    tbody.appendChild(tr);
                });
            };

            const renderizarTabelaCrediarios = () => {
                const tbody = document.getElementById('crediarios-tbody');
                const dados = historicoData.crediarios_abertos;
                tbody.innerHTML = '';
                if (!dados || dados.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Nenhum crediário em aberto.</td></tr>`;
                    return;
                }
                dados.forEach(item => {
                    const valorTotal = parseFloat(item.valor_total);
                    const valorPago = parseFloat(item.valor_pago);
                    const restante = valorTotal - valorPago;
                    const tr = document.createElement('tr');
                    const vendaJson = JSON.stringify({
                        id: item.id,
                        cliente_nome: historicoData.cliente.nome,
                        valor_total: item.valor_total,
                        valor_pago: item.valor_pago
                    });
                    tr.innerHTML = `<td><button onclick="window.open('imprimir_venda.php?id=${item.id}', '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes')" class="btn btn-sm btn-secundario" style="margin-right: 8px;"><i class="fa-solid fa-eye"></i> Ver</button>#${item.id}</td><td>${new Date(item.data_venda).toLocaleDateString('pt-BR')}</td><td>R$ ${valorTotal.toFixed(2).replace('.', ',')}</td><td>R$ ${valorPago.toFixed(2).replace('.', ',')}</td><td class="valor-restante-negativo">R$ ${restante.toFixed(2).replace('.', ',')}</td><td><button class="btn btn-sm btn-sucesso btn-registrar-pagamento" data-venda-json='${vendaJson}'><i class="fa-solid fa-hand-holding-dollar"></i> Pagar</button></td>`;
                    tbody.appendChild(tr);
                });
            };

            const preencherFormBikeFit = (bikefitData) => {
                const form = document.getElementById('bikefit-form');
                form.reset();
                if (bikefitData) {
                    Object.keys(bikefitData).forEach(key => {
                        const el = document.getElementById(key) || document.getElementById(`bikefit-${key}`);
                        if (el) el.value = bikefitData[key];
                    });
                } else {
                    document.getElementById('data_fit').value = new Date().toISOString().split('T')[0];
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

            const adicionarCampoAvaria = (texto = '', imagemUrl = '') => {
                const avariasContainer = document.getElementById('avarias-container');
                const div = document.createElement('div');
                div.className = 'avaria-item';
                let previewSrc = imagemUrl || 'assets/img/placeholder.png';
                let previewOpacity = imagemUrl ? '1' : '0.5';
                div.innerHTML = `<div class="form-group"><input type="text" name="avarias_texto[]" class="form-input" placeholder="Descreva a avaria..." value="${texto}"></div><img src="${previewSrc}" class="avaria-image-preview" style="opacity:${previewOpacity};"><input type="file" name="avarias_fotos[]" class="avaria-file-input" accept="image/*" style="display:none;"><input type="hidden" name="avarias_imagem_existente[]" value="${imagemUrl || ''}"><button type="button" class="btn btn-sm btn-secundario btn-tirar-foto" title="Tirar Foto"><i class="fa-solid fa-camera"></i></button><button type="button" class="btn btn-sm btn-secundario btn-galeria" title="Selecionar da Galeria"><i class="fa-solid fa-upload"></i></button><button type="button" class="btn btn-sm btn-erro btn-remover-avaria" title="Remover Avaria"><i class="fa-solid fa-times"></i></button>`;
                avariasContainer.appendChild(div);
            };

            const abrirModalBicicleta = (bikeData = null) => {
                bicicletaForm.reset();
                const bikeImagePreview = document.getElementById('bike-image-preview');
                document.getElementById('bicicleta-id').value = '';
                document.getElementById('bicicleta-imagem-url-existente').value = '';
                bikeImagePreview.style.display = 'none';
                bikeImagePreview.src = '#';
                const avariasContainer = document.getElementById('avarias-container');
                avariasContainer.innerHTML = '';
                if (bikeData) {
                    document.getElementById('bicicleta-modal-title').textContent = 'Editar Bicicleta';
                    document.getElementById('bicicleta-id').value = bikeData.id;
                    document.getElementById('bicicleta-marca').value = bikeData.marca;
                    document.getElementById('bicicleta-modelo').value = bikeData.modelo;
                    document.getElementById('bicicleta-cor').value = bikeData.cor;
                    document.getElementById('bicicleta-aro').value = bikeData.aro;
                    document.getElementById('bicicleta-serie').value = bikeData.numero_serie;
                    document.getElementById('bicicleta-observacoes').value = bikeData.observacoes;
                    if (bikeData.imagem_url) {
                        bikeImagePreview.src = bikeData.imagem_url;
                        bikeImagePreview.style.display = 'block';
                        document.getElementById('bicicleta-imagem-url-existente').value = bikeData.imagem_url;
                    }
                    if (bikeData.avarias && bikeData.avarias.length > 0) {
                        bikeData.avarias.forEach(av => adicionarCampoAvaria(av.texto, av.imagem_url));
                    } else {
                        adicionarCampoAvaria(); // Adiciona um campo vazio se não houver avarias
                    }
                } else {
                    document.getElementById('bicicleta-modal-title').textContent = 'Adicionar Nova Bicicleta';
                    adicionarCampoAvaria();
                }
                document.getElementById('btn-add-avaria').onclick = () => adicionarCampoAvaria();
                document.getElementById('btn-camera-principal').onclick = () => openCamera(document.getElementById('bicicleta-imagem'));
                document.getElementById('btn-galeria-principal').onclick = () => document.getElementById('bicicleta-imagem').click();
                document.getElementById('bicicleta-imagem').onchange = (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        bikeImagePreview.src = URL.createObjectURL(file);
                        bikeImagePreview.style.display = 'block';
                    }
                };
                bicicletaModal.style.display = 'flex';
            };

            const abrirModalVenderBike = (bike) => {
                venderBikeForm.reset();
                novoProprietarioId = null;
                document.getElementById('vender-bike-id').value = bike.id;
                document.getElementById('vender-bike-nome').textContent = `${bike.marca} ${bike.modelo}`;
                document.getElementById('novo-proprietario-selecionado').style.display = 'none';
                document.getElementById('form-novo-cliente-container').style.display = 'none';
                venderBikeModal.style.display = 'flex';
            };

            // --- EVENT LISTENERS ---
            btnNovaBicicleta.addEventListener('click', () => abrirModalBicicleta(null));
            closeCameraButton.addEventListener('click', closeCamera);
            pagamentoModal.querySelector('.modal-close').onclick = () => pagamentoModal.style.display = 'none';
            bicicletaModal.querySelector('.modal-close').onclick = () => bicicletaModal.style.display = 'none';
            venderBikeModal.querySelector('.modal-close').onclick = () => venderBikeModal.style.display = 'none';

            document.getElementById('avarias-container').addEventListener('click', (e) => {
                if (e.target.closest('.btn-remover-avaria')) e.target.closest('.avaria-item').remove();
                if (e.target.closest('.btn-tirar-foto')) openCamera(e.target.closest('.avaria-item').querySelector('.avaria-file-input'));
                if (e.target.closest('.btn-galeria')) e.target.closest('.avaria-item').querySelector('.avaria-file-input').click();
            });

            document.getElementById('avarias-container').addEventListener('change', e => {
                if (e.target.classList.contains('avaria-file-input')) {
                    const fileInput = e.target;
                    const previewImg = fileInput.closest('.avaria-item').querySelector('.avaria-image-preview');
                    if (fileInput.files && fileInput.files[0]) {
                        previewImg.src = URL.createObjectURL(fileInput.files[0]);
                        previewImg.style.opacity = 1;
                        fileInput.closest('.avaria-item').querySelector('input[name="avarias_imagem_existente[]"]').value = '';
                    }
                }
            });

            pagamentoForm.addEventListener('submit', async e => {
                e.preventDefault();
                const btn = e.target.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processando...';
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
                    await carregarHistorico(clienteIdAtual);
                } catch (error) {
                    alert('Erro: ' + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Confirmar Pagamento';
                }
            });

            searchClienteInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                const termo = e.target.value;
                if (termo.length < 2) {
                    searchClienteResults.style.display = 'none';
                    return;
                }
                debounceTimer = setTimeout(() => {
                    fetch(`api/api_clientes.php?acao=listar_clientes&termo=${termo}`).then(res => res.json()).then(resultado => {
                        searchClienteResults.innerHTML = '';
                        if (resultado.sucesso && resultado.dados.length > 0) {
                            resultado.dados.forEach(c => {
                                const li = document.createElement('li');
                                li.textContent = `${c.nome} (${c.cpf || 'CPF não informado'})`;
                                li.onclick = () => {
                                    window.location.href = `historico_cliente.php?id=${c.id}`;
                                };
                                searchClienteResults.appendChild(li);
                            });
                        }
                        searchClienteResults.style.display = 'block';
                    });
                }, 300);
            });

            document.addEventListener('click', function(event) {
                if (!searchClienteInput.contains(event.target)) {
                    searchClienteResults.style.display = 'none';
                }
            });

            document.getElementById('crediarios-tbody').addEventListener('click', e => {
                const btnPagar = e.target.closest('.btn-registrar-pagamento');
                if (btnPagar) {
                    abrirModalPagamento(JSON.parse(btnPagar.dataset.vendaJson));
                }
            });

            const searchNovoProprietarioInput = document.getElementById('search-novo-proprietario');
            const searchNovoProprietarioResults = document.getElementById('search-results-novo-proprietario');
            searchNovoProprietarioInput.addEventListener('input', e => {
                clearTimeout(debounceTimer);
                const termo = e.target.value;
                if (termo.length < 2) {
                    searchNovoProprietarioResults.style.display = 'none';
                    return;
                }
                debounceTimer = setTimeout(() => {
                    fetch(`api/api_clientes.php?acao=listar_clientes&termo=${termo}`).then(res => res.json()).then(resultado => {
                        searchNovoProprietarioResults.innerHTML = '';
                        if (resultado.sucesso) {
                            resultado.dados.forEach(c => {
                                const li = document.createElement('li');
                                li.textContent = c.nome;
                                li.onclick = () => {
                                    novoProprietarioId = c.id;
                                    document.getElementById('novo-proprietario-selecionado').innerHTML = `<p>Novo proprietário: <strong>${c.nome}</strong></p>`;
                                    document.getElementById('novo-proprietario-selecionado').style.display = 'block';
                                    document.getElementById('form-novo-cliente-container').style.display = 'none';
                                    searchNovoProprietarioInput.value = '';
                                    searchNovoProprietarioResults.style.display = 'none';
                                };
                                searchNovoProprietarioResults.appendChild(li);
                            });
                            const liNovo = document.createElement('li');
                            liNovo.innerHTML = `+ Adicionar novo cliente: "${termo}"`;
                            liNovo.onclick = () => {
                                novoProprietarioId = 'novo';
                                document.getElementById('form-novo-cliente-container').style.display = 'block';
                                document.getElementById('novo-cliente-nome').value = termo;
                                document.getElementById('novo-proprietario-selecionado').style.display = 'none';
                                searchNovoProprietarioResults.style.display = 'none';
                            };
                            searchNovoProprietarioResults.appendChild(liNovo);
                        }
                        searchNovoProprietarioResults.style.display = 'block';
                    });
                }, 300);
            });

            venderBikeForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (!novoProprietarioId) {
                    alert('Por favor, selecione ou cadastre um novo proprietário.');
                    return;
                }
                const payload = {
                    bicicleta_id: document.getElementById('vender-bike-id').value,
                    novo_cliente_id: novoProprietarioId === 'novo' ? null : novoProprietarioId,
                    novo_cliente_nome: document.getElementById('novo-cliente-nome').value,
                    novo_cliente_telefone: document.getElementById('novo-cliente-telefone').value
                };
                try {
                    const response = await fetch('api/api_clientes.php?acao=transferir_bicicleta', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    alert(resultado.mensagem);
                    venderBikeModal.style.display = 'none';
                    await carregarHistorico(clienteIdAtual);
                } catch (error) {
                    alert('Erro ao transferir bicicleta: ' + error.message);
                }
            });

            bicicletaForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(bicicletaForm);
                try {
                    const response = await fetch('api/api_clientes.php?acao=salvar_bicicleta', {
                        method: 'POST',
                        body: formData
                    });
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        alert(resultado.mensagem);
                        bicicletaModal.style.display = 'none';
                        carregarHistorico(clienteIdAtual);
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    alert('Erro ao salvar bicicleta: ' + error.message);
                }
            });

            document.getElementById('bikefit-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const form = e.target;
                const dados = Object.fromEntries(new FormData(form).entries());
                for (const key in dados) {
                    if (['altura_ciclista', 'altura_cavalo', 'altura_selim', 'recuo_selim', 'altura_guidao', 'distancia_guidao_selim'].includes(key) && dados[key] === '') {
                        dados[key] = null;
                    }
                }
                try {
                    const response = await fetch('api/api_clientes.php?acao=salvar_bikefit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(dados)
                    });
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        alert(resultado.mensagem);
                        carregarHistorico(clienteIdAtual);
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    alert('Erro ao salvar BikeFit: ' + error.message);
                }
            });

            document.querySelectorAll('.tab-link').forEach(tab => {
                tab.addEventListener('click', () => {
                    document.querySelectorAll('.tab-link, .tab-content').forEach(el => el.classList.remove('active'));
                    tab.classList.add('active');
                    document.getElementById(tab.dataset.tab).classList.add('active');
                });
            });

            // Abrir aba específica se especificada na URL
            const abaInicial = '<?php echo $aba_inicial; ?>';
            if (abaInicial && abaInicial !== 'bicicletas') {
                const tabToActivate = document.querySelector(`[data-tab="${abaInicial}"]`) || document.querySelector(`[data-tab="${abaInicial}s"]`);
                if (tabToActivate) {
                    document.querySelectorAll('.tab-link, .tab-content').forEach(el => el.classList.remove('active'));
                    tabToActivate.classList.add('active');
                    document.getElementById(tabToActivate.dataset.tab).classList.add('active');
                }
            }

            carregarHistorico(clienteIdAtual);
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>