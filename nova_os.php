<?php 
require_once 'auth_check.php';
require_once 'config.php';

// Verificar se o caixa está aberto
try {
    $stmt = $pdo->prepare("SELECT id FROM caixa_sessoes WHERE status = 'ABERTO' ORDER BY data_abertura DESC LIMIT 1");
    $stmt->execute();
    $caixa_aberto = $stmt->fetch();
    
    if (!$caixa_aberto) {
        echo "<script>
            alert('O caixa deve estar aberto para criar ordens de serviço. Redirecionando para o financeiro...');
            window.location.href = 'financeiro.php';
        </script>";
        exit;
    }
} catch (Exception $e) {
    echo "<script>
        alert('Erro ao verificar status do caixa. Redirecionando para o financeiro...');
        window.location.href = 'financeiro.php';
    </script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php 
        require_once 'helpers/empresa_helper.php';
        echo getTituloComEmpresa('Nova Ordem de Serviço');
    ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .wizard-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .step {
            text-align: center;
            flex-grow: 1;
        }

        .step-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: var(--cor-texto-secundario);
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            border: 3px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .step.active .step-icon {
            background-color: var(--cor-primaria);
            color: white;
            border-color: var(--cor-primaria);
        }

        .step p {
            margin-top: 0.5rem;
            font-weight: 500;
        }

        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        .search-container {
            position: relative;
        }

        .search-results-list {
            list-style: none;
            padding: 0;
            margin-top: 5px;
            position: absolute;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background: var(--cor-branco);
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            z-index: 1000;
        }

        .search-results-list li {
            padding: 10px;
            cursor: pointer;
        }

        .search-results-list li:hover {
            background-color: var(--cor-fundo-claro);
        }

        .add-new-item {
            color: var(--cor-primaria);
            font-weight: bold;
        }

        .bike-selected {
            background-color: #d1e7dd !important;
            border-color: var(--cor-sucesso) !important;
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

        #bike-image-preview {
            max-width: 200px;
            max-height: 200px;
            margin: 1rem auto;
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            display: none;
            object-fit: cover;
        }

        #camera-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        #camera-view {
            max-width: 100%;
            max-height: 80%;
            border-radius: 10px;
        }

        #camera-controls {
            position: absolute;
            bottom: 20px;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        #snap-photo-btn {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 5px solid white;
            background-color: rgba(251, 205, 0, 0.8);
            cursor: pointer;
        }

        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-with-icon .form-input {
            padding-right: 2.5rem;
        }

        .input-with-icon .input-icon {
            position: absolute;
            right: 10px;
            color: var(--cor-primaria);
            cursor: pointer;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
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
            <h1><i class="fa-solid fa-bicycle"></i> Nova Ordem de Serviço</h1>
        </div>

        <div class="card">
            <div class="wizard-steps">
                <div class="step active" data-step="1">
                    <div class="step-icon">1</div>
                    <p>Cliente</p>
                </div>
                <div class="step" data-step="2">
                    <div class="step-icon">2</div>
                    <p>Bicicleta</p>
                </div>
                <div class="step" data-step="3">
                    <div class="step-icon">3</div>
                    <p>Detalhes</p>
                </div>
            </div>

            <form id="nova-os-form">
                <div id="step-1" class="step-content active">
                    <h2><i class="fa-solid fa-user"></i> Selecione o Cliente</h2>
                    <div class="form-group search-container">
                        <label for="search-cliente" class="form-label">Buscar por nome ou telefone</label>
                        <input type="search" id="search-cliente" class="form-input" placeholder="Digite para buscar...">
                        <ul id="search-results-cliente" class="search-results-list" style="display:none;"></ul>
                    </div>
                    <div id="cliente-selecionado" style="display:none;" class="card"></div>
                </div>

                <div id="step-2" class="step-content">
                    <h2><i class="fa-solid fa-bicycle"></i> Selecione a Bicicleta</h2>
                    <div id="bicicletas-cliente-container" style="overflow-x:auto;"></div>
                    <button type="button" id="btn-cadastrar-bicicleta" class="btn btn-secundario" style="margin-top: 1rem;"><i class="fa-solid fa-plus"></i> Cadastrar Nova Bicicleta</button>
                </div>

                <div id="step-3" class="step-content">
                    <h2><i class="fa-solid fa-file-signature"></i> Detalhes do Serviço</h2>
                    <div class="form-group"><label for="problema-relatado" class="form-label">Problema Relatado pelo Cliente</label><textarea id="problema-relatado" class="form-input" rows="5" required></textarea></div>
                    <div class="form-group"><label for="mecanico-select" class="form-label">Mecânico Responsável</label><select id="mecanico-select" class="form-input"></select></div>
                    <div class="form-group">
                        <label for="previsao-entrega" class="form-label">Previsão de Entrega</label>
                        <div class="input-with-icon">
                            <input type="date" id="previsao-entrega" class="form-input" required>
                            <i id="previsao-entrega-icon" class="fa-solid fa-calendar-days input-icon" title="Selecionar data"></i>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                    <button type="button" id="btn-anterior" class="btn btn-secundario" disabled>Anterior</button>
                    <button type="button" id="btn-proximo" class="btn btn-destaque" disabled>Próximo</button>
                    <button type="submit" id="btn-finalizar-os" class="btn btn-sucesso" style="display: none;">Criar Ordem de Serviço</button>
                </div>
            </form>
        </div>
    </main>

    <div id="modal-novo-cliente" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-user-plus"></i> Novo Cliente</h2>
            <form id="form-novo-cliente">
                <div class="form-group"><label for="novo-cliente-nome">Nome</label><input type="text" id="novo-cliente-nome" class="form-input" required></div>
                <div class="form-group"><label for="novo-cliente-telefone">Telefone</label><input type="tel" id="novo-cliente-telefone" class="form-input"></div>
                <button type="submit" class="btn btn-destaque" style="width:100%;">Salvar Cliente</button>
            </form>
        </div>
    </div>

    <div id="modal-nova-bicicleta" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="bicicleta-modal-title">Cadastrar Nova Bicicleta</h2>
            <form id="form-nova-bicicleta" enctype="multipart/form-data">
                <input type="hidden" id="bicicleta-id" name="id">
                <input type="hidden" name="imagem_url_existente" id="bicicleta-imagem-url-existente">

                <img id="bike-image-preview" src="#" alt="Prévia da imagem da bicicleta">
                <div class="form-group">
                    <label class="form-label">Foto da Bicicleta</label>
                    <input type="file" id="bicicleta-imagem-input" name="imagem" accept="image/*" style="display: none;">
                    <div class="btn-group">
                        <button type="button" id="btn-galeria-principal" class="btn btn-secundario"><i class="fa-solid fa-upload"></i> Selecionar da Galeria</button>
                        <button type="button" class="btn btn-secundario btn-abrir-camera-principal"><i class="fa-solid fa-camera"></i> Usar Câmera</button>
                    </div>
                </div>

                <div class="form-group"><label for="nova-bike-marca">Marca</label><input type="text" id="nova-bike-marca" name="marca" class="form-input" required></div>
                <div class="form-group"><label for="nova-bike-modelo">Modelo</label><input type="text" id="nova-bike-modelo" name="modelo" class="form-input"></div>
                <div class="form-group"><label for="nova-bike-cor">Cor</label><input type="text" id="nova-bike-cor" name="cor" class="form-input"></div>
                <div class="form-group"><label for="nova-bike-serie">Nº de Série</label><input type="text" id="nova-bike-serie" name="numero_serie" class="form-input"></div>

                <hr style="margin: 1.5rem 0;">
                <h4><i class="fa-solid fa-shield-halved"></i> Avarias (Opcional)</h4>
                <div id="avarias-container"></div>
                <button type="button" id="btn-add-avaria" class="btn btn-secundario btn-sm" style="margin-top: 0.5rem;"><i class="fa-solid fa-plus"></i> Adicionar Avaria</button>

                <button type="submit" class="btn btn-destaque" style="width: 100%; margin-top: 1.5rem;">Salvar Bicicleta</button>
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

    <?php include '_partials/menu_admin.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let currentStep = 1;
            const osData = {
                cliente_id: null,
                bicicleta_id: null,
                cliente_info: null,
                bicicleta_info: null
            };
            let debounceTimer;
            let currentFileInput = null;

            const steps = document.querySelectorAll('.step');
            const stepContents = document.querySelectorAll('.step-content');
            const btnAnterior = document.getElementById('btn-anterior');
            const btnProximo = document.getElementById('btn-proximo');
            const btnFinalizar = document.getElementById('btn-finalizar-os');
            const searchClienteInput = document.getElementById('search-cliente');
            const searchClienteResults = document.getElementById('search-results-cliente');
            const clienteSelecionadoDiv = document.getElementById('cliente-selecionado');
            const bicicletasContainer = document.getElementById('bicicletas-cliente-container');
            const modalNovoCliente = document.getElementById('modal-novo-cliente');
            const formNovoCliente = document.getElementById('form-novo-cliente');
            const modalNovaBicicleta = document.getElementById('modal-nova-bicicleta');
            const formNovaBicicleta = document.getElementById('form-nova-bicicleta');
            const btnCadastrarBicicleta = document.getElementById('btn-cadastrar-bicicleta');
            const cameraOverlay = document.getElementById('camera-overlay');
            const cameraView = document.getElementById('camera-view');
            const snapPhotoButton = document.getElementById('snap-photo-btn');
            const closeCameraButton = document.getElementById('close-camera-btn');
            const previsaoEntregaInput = document.getElementById('previsao-entrega');
            const previsaoEntregaIcon = document.getElementById('previsao-entrega-icon');

            const updateWizard = () => {
                steps.forEach(step => step.classList.remove('active'));
                stepContents.forEach(content => content.classList.remove('active'));
                document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
                document.getElementById(`step-${currentStep}`).classList.add('active');
                btnAnterior.disabled = currentStep === 1;
                btnProximo.style.display = currentStep === 3 ? 'none' : 'block';
                btnFinalizar.style.display = currentStep === 3 ? 'block' : 'none';
                if (currentStep === 1) btnProximo.disabled = !osData.cliente_id;
                if (currentStep === 2) btnProximo.disabled = !osData.bicicleta_id;
            };

            const selecionarCliente = (cliente) => {
                osData.cliente_id = cliente.id;
                osData.cliente_info = cliente;
                clienteSelecionadoDiv.innerHTML = `<h4>Cliente Selecionado:</h4><p><strong>${cliente.nome}</strong><br>${cliente.telefone || ''}</p>`;
                clienteSelecionadoDiv.style.display = 'block';
                searchClienteInput.value = '';
                searchClienteResults.style.display = 'none';
                btnProximo.disabled = false;
            };

            const carregarBicicletas = async () => {
                const response = await fetch(`api/api_os.php?acao=buscar_bicicletas_cliente&cliente_id=${osData.cliente_id}`);
                const resultado = await response.json();
                bicicletasContainer.innerHTML = '';
                if (resultado.sucesso && resultado.dados.length > 0) {
                    const table = document.createElement('table');
                    table.className = 'tabela';
                    table.innerHTML = `<thead><tr><th>Marca</th><th>Modelo</th><th>Cor</th><th>Nº Série</th><th>Ações</th></tr></thead><tbody></tbody>`;
                    const tbody = table.querySelector('tbody');
                    resultado.dados.forEach(bike => {
                        tbody.innerHTML += `<tr data-bike-id-row="${bike.id}"><td>${bike.marca || '-'}</td><td>${bike.modelo || '-'}</td><td>${bike.cor || '-'}</td><td>${bike.numero_serie || '-'}</td><td><button type="button" class="btn btn-sm btn-secundario btn-ver-bike" data-bike-id="${bike.id}"><i class="fa-solid fa-eye"></i> Ver/Editar</button><button type="button" class="btn btn-sm btn-primario btn-selecionar-bike" data-bike-id="${bike.id}">Selecionar</button></td></tr>`;
                    });
                    bicicletasContainer.appendChild(table);
                } else {
                    bicicletasContainer.innerHTML = '<p>Nenhuma bicicleta cadastrada para este cliente.</p>';
                }
            };

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
                        console.error("Erro detalhado da câmera:", err);
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
                if (cameraView.srcObject) {
                    cameraView.srcObject.getTracks().forEach(track => track.stop());
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

            const adicionarCampoAvaria = (texto = '', imagemUrl = '') => {
                const avariasContainer = document.getElementById('avarias-container');
                const div = document.createElement('div');
                div.className = 'avaria-item';
                let previewSrc = imagemUrl || 'assets/img/placeholder.png';
                let previewOpacity = imagemUrl ? '1' : '0.5';
                div.innerHTML = `
                <div class="form-group"><input type="text" name="avarias_texto[]" class="form-input" placeholder="Descreva a avaria..." value="${texto}"></div>
                <img src="${previewSrc}" class="avaria-image-preview" style="opacity:${previewOpacity};">
                <input type="file" name="avarias_fotos[]" class="avaria-file-input" accept="image/*" style="display:none;">
                <input type="hidden" name="avarias_imagem_existente[]" value="${imagemUrl || ''}">
                <button type="button" class="btn btn-sm btn-secundario btn-tirar-foto" title="Tirar Foto"><i class="fa-solid fa-camera"></i></button>
                <button type="button" class="btn btn-sm btn-secundario btn-galeria" title="Selecionar da Galeria"><i class="fa-solid fa-upload"></i></button>
                <button type="button" class="btn btn-sm btn-erro btn-remover-avaria" title="Remover Avaria"><i class="fa-solid fa-times"></i></button>`;
                avariasContainer.appendChild(div);
            };

            const abrirModalBicicleta = (bikeData = null) => {
                formNovaBicicleta.reset();
                document.getElementById('bicicleta-id').value = '';
                document.getElementById('bicicleta-imagem-url-existente').value = '';
                const bikeImagePreview = document.getElementById('bike-image-preview');
                bikeImagePreview.style.display = 'none';
                bikeImagePreview.src = '#';
                document.getElementById('avarias-container').innerHTML = '';

                if (bikeData) {
                    document.getElementById('bicicleta-modal-title').textContent = 'Editar Bicicleta';
                    document.getElementById('bicicleta-id').value = bikeData.id;
                    document.getElementById('nova-bike-marca').value = bikeData.marca;
                    document.getElementById('nova-bike-modelo').value = bikeData.modelo;
                    document.getElementById('nova-bike-cor').value = bikeData.cor;
                    document.getElementById('nova-bike-serie').value = bikeData.numero_serie;
                    if (bikeData.imagem_url) {
                        bikeImagePreview.src = bikeData.imagem_url;
                        bikeImagePreview.style.display = 'block';
                        document.getElementById('bicicleta-imagem-url-existente').value = bikeData.imagem_url;
                    }
                    if (bikeData.avarias && bikeData.avarias.length > 0) {
                        bikeData.avarias.forEach(av => adicionarCampoAvaria(av.texto, av.imagem_url));
                    }
                } else {
                    document.getElementById('bicicleta-modal-title').textContent = 'Cadastrar Nova Bicicleta';
                    adicionarCampoAvaria();
                }

                const btnAddAvaria = document.getElementById('btn-add-avaria');
                const btnAbrirCameraPrincipal = modalNovaBicicleta.querySelector('.btn-abrir-camera-principal');
                const btnGaleriaPrincipal = document.getElementById('btn-galeria-principal');
                const bikeImageInput = document.getElementById('bicicleta-imagem-input');

                btnAddAvaria.onclick = () => adicionarCampoAvaria();
                btnAbrirCameraPrincipal.onclick = () => openCamera(bikeImageInput);
                btnGaleriaPrincipal.onclick = () => bikeImageInput.click();

                bikeImageInput.onchange = (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        bikeImagePreview.src = URL.createObjectURL(file);
                        bikeImagePreview.style.display = 'block';
                    }
                };

                modalNovaBicicleta.style.display = 'flex';
            };

            // --- Event Listeners ---
            btnProximo.addEventListener('click', () => {
                if (currentStep < 3) currentStep++;
                updateWizard();
            });
            btnAnterior.addEventListener('click', () => {
                if (currentStep > 1) currentStep--;
                updateWizard();
            });

            searchClienteInput.addEventListener('input', e => {
                clearTimeout(debounceTimer);
                const termo = e.target.value;
                if (termo.length < 2) {
                    searchClienteResults.style.display = 'none';
                    return;
                }
                debounceTimer = setTimeout(() => {
                    fetch(`api/api_clientes.php?acao=listar_clientes&termo=${termo}`).then(res => res.json()).then(res => {
                        searchClienteResults.innerHTML = '';
                        if (res.sucesso) {
                            res.dados.forEach(c => {
                                const li = document.createElement('li');
                                li.textContent = `${c.nome} - ${c.telefone || 'Sem telefone'}`;
                                li.onclick = () => {
                                    selecionarCliente(c);
                                    carregarBicicletas();
                                };
                                searchClienteResults.appendChild(li);
                            });
                            const liNovo = document.createElement('li');
                            liNovo.className = 'add-new-item';
                            liNovo.innerHTML = `<i class="fa-solid fa-plus"></i> Adicionar novo cliente: "<b>${termo}</b>"`;
                            liNovo.onclick = () => {
                                document.getElementById('novo-cliente-nome').value = termo;
                                modalNovoCliente.style.display = 'flex';
                                searchClienteResults.style.display = 'none';
                            };
                            searchClienteResults.appendChild(liNovo);
                        }
                        searchClienteResults.style.display = 'block';
                    });
                }, 300);
            });

            formNovoCliente.addEventListener('submit', async e => {
                e.preventDefault();
                const response = await fetch('api/api_os.php?acao=salvar_cliente', {
                    method: 'POST',
                    body: JSON.stringify({
                        nome: document.getElementById('novo-cliente-nome').value,
                        telefone: document.getElementById('novo-cliente-telefone').value
                    })
                });
                const resultado = await response.json();
                if (resultado.sucesso) {
                    selecionarCliente(resultado.dados);
                    carregarBicicletas();
                    modalNovoCliente.style.display = 'none';
                } else {
                    alert("Erro ao salvar cliente.");
                }
            });

            bicicletasContainer.addEventListener('click', async e => {
                const btnSelecionar = e.target.closest('.btn-selecionar-bike');
                if (btnSelecionar) {
                    osData.bicicleta_id = btnSelecionar.dataset.bikeId;
                    const linhaSelecionada = btnSelecionar.closest('tr');
                    osData.bicicleta_info = {
                        modelo: linhaSelecionada.cells[1].textContent,
                        marca: linhaSelecionada.cells[0].textContent
                    };
                    document.querySelectorAll('#bicicletas-cliente-container tr').forEach(row => row.classList.remove('bike-selected'));
                    linhaSelecionada.classList.add('bike-selected');
                    btnProximo.disabled = false;
                }
                const btnVer = e.target.closest('.btn-ver-bike');
                if (btnVer) {
                    const bikeId = btnVer.dataset.bikeId;
                    const response = await fetch(`api/api_os.php?acao=buscar_bicicleta_por_id&id=${bikeId}`);
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        abrirModalBicicleta(resultado.dados);
                    } else {
                        alert('Erro ao carregar dados da bicicleta.');
                    }
                }
            });

            formNovaBicicleta.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(formNovaBicicleta);
                formData.append('cliente_id', osData.cliente_id);
                const response = await fetch('api/api_os.php?acao=salvar_bicicleta', {
                    method: 'POST',
                    body: formData
                });
                const resultado = await response.json();
                if (resultado.sucesso) {
                    alert(resultado.mensagem);
                    await carregarBicicletas();
                    osData.bicicleta_id = resultado.dados.id;
                    osData.bicicleta_info = resultado.dados;
                    btnProximo.disabled = false;
                    modalNovaBicicleta.style.display = 'none';
                    const novaLinha = document.querySelector(`tr[data-bike-id-row="${osData.bicicleta_id}"]`);
                    if (novaLinha) novaLinha.classList.add('bike-selected');
                } else {
                    alert("Erro ao salvar bicicleta: " + resultado.mensagem);
                }
            });

            document.getElementById('nova-os-form').addEventListener('submit', async e => {
                e.preventDefault();
                const payload = {
                    ...osData,
                    problema_relatado: document.getElementById('problema-relatado').value,
                    mecanico_id: document.getElementById('mecanico-select').value,
                    previsao_entrega: document.getElementById('previsao-entrega').value
                };
                const response = await fetch('api/api_os.php?acao=criar_os', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                const resultado = await response.json();
                if (resultado.sucesso) {
                    alert(resultado.mensagem);
                    if (confirm('Deseja notificar o cliente via WhatsApp?')) {
                        const nomeCliente = osData.cliente_info.nome.split(' ')[0];
                        const telefone = osData.cliente_info.telefone.replace(/\D/g, '');
                        const bikeInfo = `${osData.bicicleta_info.marca || ''} ${osData.bicicleta_info.modelo || ''}`.trim();
                        const linkAcompanhamento = `${window.location.origin}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'))}/acompanhar_os.php?id=${resultado.os_id}&codigo=${resultado.codigo_retirada}`;
                        let mensagem = `Olá, ${nomeCliente}! Sua bicicleta (${bikeInfo}) foi recebida em nossa oficina.\n\n*Ordem de Serviço:* #${resultado.os_id}\n*Código de Retirada:* ${resultado.codigo_retirada}\n\nVocê pode acompanhar o andamento do serviço em tempo real através do link:\n${linkAcompanhamento}\n\nObrigado por confiar na BikeOS!`;
                        window.open(`https://wa.me/55${telefone}?text=${encodeURIComponent(mensagem)}`, '_blank');
                    }
                    window.location.href = `editar_os.php?id=${resultado.os_id}`;
                } else {
                    alert("Erro ao criar OS: " + resultado.mensagem);
                }
            });

            closeCameraButton.addEventListener('click', closeCamera);
            btnCadastrarBicicleta.addEventListener('click', () => abrirModalBicicleta());
            modalNovoCliente.querySelector('.modal-close').onclick = () => modalNovoCliente.style.display = 'none';
            modalNovaBicicleta.querySelector('.modal-close').onclick = () => modalNovaBicicleta.style.display = 'none';

            previsaoEntregaIcon.addEventListener('click', () => {
                try {
                    previsaoEntregaInput.showPicker();
                } catch (e) {
                    previsaoEntregaInput.focus();
                }
            });

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

            (async () => {
                const response = await fetch(`api/api_os.php?acao=listar_mecanicos`);
                const resultado = await response.json();
                if (resultado.sucesso) {
                    const select = document.getElementById('mecanico-select');
                    resultado.dados.forEach(m => select.add(new Option(m.nome, m.id)));
                }
            })();

            updateWizard();
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>