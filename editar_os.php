<?php
require_once 'auth_check.php';
$os_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($os_id <= 0) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar OS #<?php echo $os_id; ?> | <?php require_once 'helpers/empresa_helper.php'; require_once 'api/db_connection.php'; echo getNomeEmpresa($pdo); ?></title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .os-layout { display: grid; grid-template-columns: 1fr; gap: 2rem; }
        @media (min-width: 1024px) { .os-layout { grid-template-columns: 1fr 2fr; } }
        

        .info-header { margin-bottom: 1rem; }
        .info-header strong { display: block; color: var(--cor-texto-secundario); font-size: 0.9rem; }
        .os-status-select { font-weight: bold; padding: 0.5rem; border-radius: var(--raio-borda); border: 2px solid var(--cor-primaria); }
        .resumo-financeiro { text-align: right; }
        .resumo-financeiro p { margin: 0.5rem 0; font-size: 1.1rem; }
        .resumo-financeiro .total-geral { font-size: 1.8rem; font-weight: 700; color: var(--cor-primaria); border-top: 1px solid var(--cor-borda); padding-top: 1rem; margin-top: 1rem; }
        .search-container { position: relative; }
        .search-results-list { list-style: none; padding: 0; margin-top: 5px; position: absolute; width: 100%; max-height: 250px; overflow-y: auto; background: var(--cor-branco); border: 1px solid var(--cor-borda); border-radius: var(--raio-borda); z-index: 1000; }
        .search-results-list li { display: flex; align-items: center; padding: 10px; cursor: pointer; gap: 10px; }
        .search-results-list li:hover { background-color: var(--cor-fundo-claro); }
        .search-result-img { width: 40px; height: 40px; object-fit: cover; border-radius: 5px; }
        .search-result-info { flex-grow: 1; }
        .editable-field .fa-pencil { margin-left: 10px; cursor: pointer; color: var(--cor-primaria); }
        .item-actions { text-align: center; }
        .item-actions button { background: none; border: none; cursor: pointer; padding: 5px; font-size: 1rem; }
        .item-actions .fa-trash-can { color: var(--cor-erro); }
        #form-cliente-crediario { display: none; margin-top: 1.5rem; border-top: 1px solid var(--cor-borda); padding-top: 1.5rem; animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-product-image { max-height: 150px; max-width: 100%; border-radius: var(--raio-borda); border: 1px solid var(--cor-borda); object-fit: cover; margin: 0 auto 1rem; display: none; }
        .card-actions { display: flex; flex-direction: column; gap: 1rem; margin-top: 1.5rem; border-top: 1px solid var(--cor-borda); padding-top: 1.5rem; }
        .card-actions .btn-group { display: flex; gap: 1rem; }
        
        /* Responsividade para botões */
        @media (max-width: 768px) {
            .card-actions .btn-group {
                flex-direction: column;
                gap: 0.5rem;
            }
            .card-actions .btn-group .btn {
                flex: none;
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .card-actions .btn-group .btn {
                font-size: 0.9rem;
                padding: 0.6rem 0.8rem;
            }
        }
        .observacoes-container { display: flex; flex-direction: column; gap: 1rem; max-height: 400px; overflow-y: auto; margin-bottom: 1rem; }
        .observacao-item { background: #f8f9fa; padding: 1rem; border-radius: var(--raio-borda); }
        .observacao-meta { font-size: 0.8rem; color: #6c757d; border-bottom: 1px solid #dee2e6; padding-bottom: 0.5rem; margin-bottom: 0.5rem; }
        .observacao-imagem { max-width: 150px; border-radius: var(--raio-borda); margin-top: 0.5rem; cursor: pointer; transition: transform 0.2s; }
        .observacao-imagem:hover { transform: scale(1.05); }
        .progress-bar-container { display: none; margin-top: 1rem; }
        .progress-bar { width: 100%; background-color: #e9ecef; border-radius: var(--raio-borda); overflow: hidden; }
        .progress-bar-fill { height: 20px; width: 0%; background-color: var(--cor-sucesso); text-align: center; line-height: 20px; color: white; transition: width 0.25s; }
        .fab-whatsapp { position: fixed; bottom: 160px; right: 25px; z-index: 1050; }
        .fab-whatsapp .fab-button { background-color: #25D366; }
        .fab-whatsapp .fab-button:hover { background-color: #128C7E; }
        #camera-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.9); z-index: 1060; display: none; justify-content: center; align-items: center; flex-direction: column; }
        #camera-view { max-width: 100%; max-height: 85%; border-radius: 10px; }
        #camera-controls { position: absolute; bottom: 20px; display: flex; gap: 1rem; align-items: center; }
        #btn-snap-photo { width: 70px; height: 70px; border-radius: 50%; border: 5px solid white; background-color: rgba(251, 205, 0, 0.8); cursor: pointer; }
        .quick-add-grid { display: grid; grid-template-columns: 80px 1fr; gap: 1rem; align-items: flex-end; }
        .quick-add-grid .form-group { margin-bottom: 0; }
        #toast-container { position: fixed; bottom: 20px; right: 20px; z-index: 1070; }
        .toast { padding: 10px 20px; margin-top: 10px; border-radius: 5px; color: white; background-color: var(--cor-sucesso); opacity: 0; transform: translateY(20px); transition: all 0.3s ease; }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.error { background-color: var(--cor-erro); }
        .historico-card { margin-top: 2rem; }
        .historico-lista { list-style: none; padding: 0; margin-top: 1rem; max-height: 200px; overflow-y: auto; }
        .historico-item { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .historico-item:last-child { border-bottom: none; }
        .alerta-crediario { background-color: var(--cor-erro); color: white; padding: 1rem; border-radius: var(--raio-borda); text-align: center; font-weight: bold; margin-bottom: 1rem; }
        
        /* Estilo específico para o botão de gerar código */
        #btn-gerar-codigo {
            background-color: #e0e0e0 !important;
            color: #333 !important;
            border: 1px solid #ccc !important;
        }
        
        #btn-gerar-codigo:hover {
            background-color: #e0e0e0 !important;
            color: #333 !important;
            border: 1px solid #ccc !important;
            transform: none !important;
            box-shadow: none !important;
        }
        
        #btn-gerar-codigo:disabled {
            background-color: #f5f5f5 !important;
            color: #999 !important;
            border: 1px solid #ddd !important;
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
                <h1><i class="fa-solid fa-pencil"></i> Ordem de Serviço #<span id="os-number-title"><?php echo $os_id; ?></span></h1>
            </div>
            <div class="page-navigation">
                <button onclick="history.back()" class="btn btn-secundario" title="Voltar à página anterior"><i class="fa-solid fa-arrow-left"></i> Voltar</button>
                <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Ir para o Painel Principal"><i class="fa-solid fa-house"></i></a>
            </div>
        </div>

        <div class="os-layout">
            <aside class="os-info">
                <div class="card">
                    <h2><i class="fa-solid fa-circle-info"></i> Detalhes da OS</h2>
                    <div class="info-header"><strong>Cliente:</strong> <span id="info-cliente">Carregando...</span></div>
                    <div class="info-header"><strong>Bicicleta:</strong> <span id="info-bike">Carregando...</span></div>
                    <div class="info-header editable-field"><strong>Problema Relatado: <i class="fa-solid fa-pencil" id="btn-edit-problema" title="Editar"></i></strong>
                        <div id="problema-container">
                            <p id="info-problema">Carregando...</p>
                        </div>
                    </div>
                    <div class="form-group"><label for="previsao-entrega" class="form-label"><strong>Previsão de Entrega:</strong></label><input type="date" id="previsao-entrega" class="form-input"></div>
                    <div class="form-group"><label for="os-status-select" class="form-label"><strong>Status Atual:</strong></label><select id="os-status-select" class="form-input os-status-select">
                            <option value="ORCAMENTO">Orçamento</option>
                            <option value="AGUARDANDO_PECA">Aguardando Peça</option>
                            <option value="EM_SERVICO">Em Serviço</option>
                            <option value="PRONTA">Pronta para Retirada</option>
                            <option value="FINALIZADA">Finalizada</option>
                            <option value="CANCELADA">Cancelada</option>
                        </select></div>
                    <button id="btn-gerar-codigo" class="btn btn-secundario" style="width: 100%; margin-top: 1rem;"><i class="fa-solid fa-key"></i> Gerar Código de Retirada</button>

                    <div class="card-actions">
                        <div class="btn-group" style="width: 100%;">
                            <a href="imprimir_os.php?id=<?php echo $os_id; ?>" target="_blank" class="btn btn-secundario" style="flex: 1;"><i class="fa-solid fa-print"></i> Imprimir</a>
                            <button id="btn-atualizar-os" class="btn btn-secundario" style="flex: 1;"><i class="fa-solid fa-save"></i> Atualizar OS</button>
                        </div>
                    </div>
                </div>
                <div class="card historico-card">
                    <h2><i class="fa-solid fa-user-clock"></i> Histórico do Cliente</h2>
                    <div id="historico-cliente-container">
                        <p>Carregando histórico...</p>
                    </div>
                </div>
            </aside>
            <div class="os-items">
                <div class="card">
                    <h2><i class="fa-solid fa-wrench"></i> Adicionar Peças e Serviços</h2>
                    <div class="quick-add-grid">
                        <div class="form-group"><label class="form-label">Qtd.</label><input type="number" id="qty-pecas" class="form-input" value="1" min="1"></div>
                        <div class="form-group search-container">
                            <label class="form-label">Adicionar Peça</label>
                            <input type="search" id="search-pecas" class="form-input" placeholder="Buscar peça por nome ou SKU...">
                            <ul id="search-results-pecas" class="search-results-list" style="display:none;"></ul>
                        </div>
                    </div>
                    <hr style="margin: 1rem 0;">
                    <div class="quick-add-grid">
                        <div class="form-group"><label class="form-label">Qtd.</label><input type="number" id="qty-servicos" class="form-input" value="1" min="1" readonly></div>
                        <div class="form-group search-container">
                             <label class="form-label">Adicionar Serviço (Mão de Obra)</label>
                             <input type="search" id="search-servicos" class="form-input" placeholder="Buscar serviço...">
                             <ul id="search-results-servicos" class="search-results-list" style="display:none;"></ul>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-top: 2rem;">
                    <h2><i class="fa-solid fa-comment-dots"></i> Observações do Mecânico</h2>
                    <div id="lista-observacoes" class="observacoes-container"></div>
                    <hr style="margin: 1.5rem 0;">
                    <h4>Adicionar Nova Observação</h4>
                    <form id="form-nova-observacao">
                        <div class="form-group"><label for="texto-observacao" class="form-label">Descrição</label><textarea id="texto-observacao" class="form-input" rows="4"></textarea></div>
                        <div class="form-group">
                            <label class="form-label">Anexar Fotos</label>
                            <input type="file" id="fotos-input" multiple accept="image/*" style="display: none;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-secundario" onclick="document.getElementById('fotos-input').click();"><i class="fa-solid fa-upload"></i> Selecionar da Galeria</button>
                                <button type="button" id="btn-abrir-camera" class="btn btn-secundario"><i class="fa-solid fa-camera"></i> Usar Câmera</button>
                            </div>
                        </div>
                        <div id="upload-progress-container" class="progress-bar-container">
                            <p><strong>Aguarde o carregamento das imagens...</strong></p>
                            <div class="progress-bar">
                                <div id="upload-progress-fill" class="progress-bar-fill">0%</div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-destaque" style="width: 100%;"><i class="fa-solid fa-plus"></i> Adicionar Observação</button>
                    </form>
                </div>

                <div class="card" style="margin-top: 2rem;">
                    <h2><i class="fa-solid fa-dollar-sign"></i> Resumo Financeiro</h2>
                    <h3>Peças</h3>
                    <div style="overflow-x:auto;">
                        <table class="tabela" style="width:100%">
                            <thead><tr><th>Item</th><th>Valor</th><th>Ações</th></tr></thead>
                            <tbody id="lista-pecas"></tbody>
                        </table>
                    </div><br>
                    <h3>Serviços</h3>
                    <div style="overflow-x:auto;">
                        <table class="tabela" style="width:100%">
                            <thead><tr><th>Item</th><th>Valor</th><th>Ações</th></tr></thead>
                            <tbody id="lista-servicos"></tbody>
                        </table>
                    </div>
                    <div class="resumo-financeiro">
                        <p>Total Peças: <strong id="total-pecas">R$ 0,00</strong></p>
                        <p>Total Serviços: <strong id="total-servicos">R$ 0,00</strong></p>
                        <p class="total-geral">Total Geral: <strong id="total-geral">R$ 0,00</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="add-peca-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="modal-peca-name">Adicionar Peça</h2>
            <img id="modal-peca-imagem-preview" src="#" alt="Imagem do Produto" class="modal-product-image">
            <form id="modal-peca-form">
                <input type="hidden" id="modal-peca-id">
                <div class="form-group"><label for="modal-peca-qty" class="form-label">Quantidade</label><input type="number" id="modal-peca-qty" class="form-input" value="1" min="1" required></div>
                <div class="form-group"><label for="modal-peca-price" class="form-label">Preço Cobrado (unitário)</label><input type="number" id="modal-peca-price" class="form-input" step="0.01" min="0" required></div>
                <button type="submit" class="btn btn-destaque" style="width: 100%;">Adicionar Peça à OS</button>
            </form>
        </div>
    </div>

    <div id="finalizar-os-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-flag-checkered"></i> Finalizar e Pagar OS</h2>
            <form id="finalizar-os-form">
                <p>Confirme o método de pagamento e o código de retirada.</p>
                <div class="resumo-financeiro" style="text-align:center; margin: 1rem 0;">
                    <p class="total-geral">Valor Total a Pagar: <strong id="modal-total-pagar">R$ 0,00</strong></p>
                </div>
                <div class="form-group"><label for="metodo-pagamento-select" class="form-label">Método de Pagamento</label><select id="metodo-pagamento-select" class="form-input" required>
                        <option value="PIX">PIX</option>
                        <option value="CREDITO">Cartão de Crédito</option>
                        <option value="DEBITO">Cartão de Débito</option>
                        <option value="DINHEIRO">Dinheiro</option>
                        <option value="CREDIARIO">Crediário</option>
                    </select></div>
                <div id="form-cliente-crediario">
                    <h4><i class="fa-solid fa-user-check"></i> Confirme os Dados para Crediário</h4>
                    <div class="form-group"><label for="cliente-nome" class="form-label">Nome Completo</label><input type="text" id="cliente-nome" class="form-input"></div>
                    <div class="form-group"><label for="cliente-cpf" class="form-label">CPF (obrigatório)</label><input type="text" id="cliente-cpf" class="form-input" placeholder="000.000.000-00"></div>
                    <div class="form-group"><label for="cliente-telefone" class="form-label">Telefone</label><input type="text" id="cliente-telefone" class="form-input"></div>
                    <div class="form-group"><label for="cliente-email" class="form-label">Email</label><input type="email" id="cliente-email" class="form-input"></div>
                    <div class="form-group"><label for="cliente-cep" class="form-label">CEP</label><input type="text" id="cliente-cep" class="form-input" placeholder="00000-000" data-cep="true"></div>
                    <div class="form-group"><label for="cliente-logradouro" class="form-label">Logradouro</label><input type="text" id="cliente-logradouro" class="form-input" placeholder="Rua, Avenida, etc." data-cep-logradouro="true"></div>
                    <div class="form-group"><label for="cliente-numero" class="form-label">Número</label><input type="text" id="cliente-numero" class="form-input" placeholder="123"></div>
                    <div class="form-group"><label for="cliente-complemento" class="form-label">Complemento</label><input type="text" id="cliente-complemento" class="form-input" placeholder="Apto, Bloco, etc."></div>
                    <div class="form-group"><label for="cliente-bairro" class="form-label">Bairro</label><input type="text" id="cliente-bairro" class="form-input" placeholder="Nome do bairro" data-cep-bairro="true"></div>
                    <div class="form-group"><label for="cliente-cidade" class="form-label">Cidade</label><input type="text" id="cliente-cidade" class="form-input" placeholder="Nome da cidade" data-cep-cidade="true"></div>
                    <div class="form-group"><label for="cliente-estado" class="form-label">Estado</label><select id="cliente-estado" class="form-input" data-cep-estado="true"><option value="">Selecione o estado</option><option value="AC">Acre</option><option value="AL">Alagoas</option><option value="AP">Amapá</option><option value="AM">Amazonas</option><option value="BA">Bahia</option><option value="CE">Ceará</option><option value="DF">Distrito Federal</option><option value="ES">Espírito Santo</option><option value="GO">Goiás</option><option value="MA">Maranhão</option><option value="MT">Mato Grosso</option><option value="MS">Mato Grosso do Sul</option><option value="MG">Minas Gerais</option><option value="PA">Pará</option><option value="PB">Paraíba</option><option value="PR">Paraná</option><option value="PE">Pernambuco</option><option value="PI">Piauí</option><option value="RJ">Rio de Janeiro</option><option value="RN">Rio Grande do Norte</option><option value="RS">Rio Grande do Sul</option><option value="RO">Rondônia</option><option value="RR">Roraima</option><option value="SC">Santa Catarina</option><option value="SP">São Paulo</option><option value="SE">Sergipe</option><option value="TO">Tocantins</option></select></div>
                </div>
                <div class="form-group"><label for="codigo-retirada-input" class="form-label">Código de Retirada</label><input type="text" id="codigo-retirada-input" class="form-input" required></div>
                <button type="submit" class="btn btn-destaque" style="width: 100%;">Confirmar Pagamento e Finalizar OS</button>
            </form>
        </div>
    </div>
    
    <div id="camera-overlay">
        <video id="camera-view" autoplay playsinline></video>
        <div id="camera-controls">
            <button type="button" id="btn-snap-photo"></button>
            <button type="button" id="btn-close-camera" class="btn btn-erro"><i class="fa-solid fa-times"></i></button>
        </div>
    </div>

    <div class="fab-whatsapp">
        <button id="btn-contato-whatsapp" class="fab-button" title="Entrar em contato com o cliente">
            <i class="fa-brands fa-whatsapp"></i>
        </button>
    </div>

    <div id="toast-container"></div>

    <?php include '_partials/menu_admin.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const osId = <?php echo $os_id; ?>;
            let osData = {};
            let debounceTimer;
            let isEditingProblema = false;
            
            const btnAtualizarOS = document.getElementById('btn-atualizar-os');
            const statusSelect = document.getElementById('os-status-select');
            const entregaInput = document.getElementById('previsao-entrega');
            const btnGerarCodigo = document.getElementById('btn-gerar-codigo');
            const problemaContainer = document.getElementById('problema-container');
            const btnEditProblema = document.getElementById('btn-edit-problema');
            const osItemsContainer = document.querySelector('.os-items');
            const searchPecasInput = document.getElementById('search-pecas');
            const searchResultsPecas = document.getElementById('search-results-pecas');
            const searchServicosInput = document.getElementById('search-servicos');
            const searchResultsServicos = document.getElementById('search-results-servicos');
            const pecaModal = document.getElementById('add-peca-modal');
            const pecaModalForm = document.getElementById('modal-peca-form');
            const finalizarModal = document.getElementById('finalizar-os-modal');
            const finalizarForm = document.getElementById('finalizar-os-form');
            const metodoPagamentoSelect = document.getElementById('metodo-pagamento-select');
            const formNovaObservacao = document.getElementById('form-nova-observacao');
            const listaObservacoes = document.getElementById('lista-observacoes');
            const progressContainer = document.getElementById('upload-progress-container');
            const progressBarFill = document.getElementById('upload-progress-fill');
            const btnContatoWhatsApp = document.getElementById('btn-contato-whatsapp');
            const btnAbrirCamera = document.getElementById('btn-abrir-camera');
            const cameraOverlay = document.getElementById('camera-overlay');
            const cameraView = document.getElementById('camera-view');
            const btnSnapPhoto = document.getElementById('btn-snap-photo');
            const btnCloseCamera = document.getElementById('btn-close-camera');
            const fotosInput = document.getElementById('fotos-input');
            const historicoClienteContainer = document.getElementById('historico-cliente-container');
            let currentStream;

            function showToast(message, type = 'success') {
                const toastContainer = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = `toast ${type === 'error' ? 'error' : 'success'}`;
                toast.textContent = message;
                toastContainer.appendChild(toast);
                setTimeout(() => { toast.classList.add('show'); }, 100);
                setTimeout(() => { toast.classList.remove('show'); setTimeout(() => { toast.remove(); }, 300); }, 2000);
            }

            function validarCPF(cpf) {
                cpf = cpf.replace(/[^\d]+/g, '');
                if (cpf == '' || cpf.length != 11 || /^(\d)\1+$/.test(cpf)) return false;
                let add = 0;
                for (let i = 0; i < 9; i++) add += parseInt(cpf.charAt(i)) * (10 - i);
                let rev = 11 - (add % 11);
                if (rev == 10 || rev == 11) rev = 0;
                if (rev != parseInt(cpf.charAt(9))) return false;
                add = 0;
                for (let i = 0; i < 10; i++) add += parseInt(cpf.charAt(i)) * (11 - i);
                rev = 11 - (add % 11);
                if (rev == 10 || rev == 11) rev = 0;
                if (rev != parseInt(cpf.charAt(10))) return false;
                return true;
            }

            const enviarNotificacaoWhatsApp = () => {
                if (!osData?.os_info?.cliente_telefone) { alert("Cliente sem telefone para notificação."); return; }
                const telefoneFormatado = '55' + osData.os_info.cliente_telefone.replace(/\D/g, '');
                const nomeCliente = osData.os_info.cliente_nome.split(' ')[0];
                const idOS = osData.os_info.id;
                const statusNovo = statusSelect.options[statusSelect.selectedIndex].text;
                const bike = `${osData.os_info.bicicleta_marca || ''} ${osData.os_info.bicicleta_modelo}`;
                const valorTotal = document.getElementById('total-geral').textContent;
                const linkAcompanhamento = `${window.location.origin}/acompanhar_os.php?id=${idOS}&codigo=${osData.os_info.codigo_retirada}`;
                let mensagem = `Olá, ${nomeCliente}! Temos uma atualização sobre sua Ordem de Serviço #${idOS} (${bike}).\n\n`;
                mensagem += `Novo Status: *${statusNovo}*\n\n`;
                if (statusSelect.value === 'PRONTA' && osData.os_info.codigo_retirada) {
                    mensagem += `Sua bicicleta está pronta para retirada!\n*Valor Total:* ${valorTotal}\n*Código para Retirada:* ${osData.os_info.codigo_retirada}\nÉ indispensável a apresentação deste código.\n\n`;
                }
                mensagem += `Acompanhe os detalhes aqui: ${linkAcompanhamento}\n\nAtenciosamente,\n${window.lojaConfig?.config?.nome_loja || 'BikeOS'}`;
                window.open(`https://wa.me/${telefoneFormatado}?text=${encodeURIComponent(mensagem)}`, '_blank');
            };
            
            const abrirModalFinalizacao = () => {
                document.getElementById('modal-total-pagar').textContent = document.getElementById('total-geral').textContent;
                const cliente = osData.os_info;
                document.getElementById('cliente-nome').value = cliente.cliente_nome || '';
                document.getElementById('cliente-telefone').value = cliente.cliente_telefone || '';
                document.getElementById('cliente-email').value = cliente.cliente_email || '';
                document.getElementById('cliente-cpf').value = cliente.cliente_cpf || '';
                document.getElementById('cliente-cep').value = cliente.cliente_cep || '';
                document.getElementById('cliente-logradouro').value = cliente.cliente_logradouro || '';
                document.getElementById('cliente-numero').value = cliente.cliente_numero || '';
                document.getElementById('cliente-complemento').value = cliente.cliente_complemento || '';
                document.getElementById('cliente-bairro').value = cliente.cliente_bairro || '';
                document.getElementById('cliente-cidade').value = cliente.cliente_cidade || '';
                document.getElementById('cliente-estado').value = cliente.cliente_estado || '';
                metodoPagamentoSelect.value = 'PIX';
                metodoPagamentoSelect.dispatchEvent(new Event('change'));
                finalizarModal.style.display = 'flex';
            };

            const fecharModalFinalizacao = () => {
                finalizarModal.style.display = 'none';
                statusSelect.value = osData.os_info.status;
            };
            
            const carregarHistoricoCliente = async (clienteId) => {
                try {
                    const response = await fetch(`api/api_clientes.php?acao=get_historico_completo&id=${clienteId}`);
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    
                    const historico = resultado.dados;
                    let html = '';
                    
                    const crediarioAberto = historico.compras.find(v => v.metodo_pagamento === 'CREDIARIO' && v.pago == 0);
                    if (crediarioAberto) {
                        html += `<div class="alerta-crediario"><i class="fa-solid fa-triangle-exclamation"></i> <a href="historico_cliente.php?id=${clienteId}" target="_blank" style="color:white; text-decoration:underline;">Cliente possui crediário em aberto!</a></div>`;
                    }

                    html += '<h4>Últimas Ordens de Serviço</h4>';
                    if (historico.ordens_servico.length > 0) {
                        html += '<ul class="historico-lista">';
                        historico.ordens_servico.slice(0, 5).forEach(os => {
                            const problemaRelatado = os.problema_relatado ? `<div class="problema-relatado" style="font-size: 0.9em; color: #666; margin-top: 0.25rem; font-style: italic;">${os.problema_relatado}</div>` : '';
                            html += `<li class="historico-item">
                                <div style="flex: 1;">
                                    <span>OS #${os.id} - ${new Date(os.data_entrada).toLocaleDateString('pt-BR')}</span>
                                    ${problemaRelatado}
                                </div>
                                <a href="imprimir_os.php?id=${os.id}" target="_blank" class="btn btn-sm btn-secundario"><i class="fa-solid fa-print"></i></a>
                            </li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<p>Nenhuma OS anterior.</p>';
                    }

                    html += '<h4 style="margin-top:1rem;">Últimas Compras</h4>';
                    if (historico.compras.length > 0) {
                        html += '<ul class="historico-lista">';
                        historico.compras.slice(0, 5).forEach(venda => {
                            html += `<li class="historico-item"><span>Venda #${venda.id} - ${new Date(venda.data_venda).toLocaleDateString('pt-BR')}</span> <a href="imprimir_venda.php?id=${venda.id}" target="_blank" class="btn btn-sm btn-secundario"><i class="fa-solid fa-print"></i></a></li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<p>Nenhuma compra anterior.</p>';
                    }
                    
                    historicoClienteContainer.innerHTML = html;

                } catch(e) {
                    historicoClienteContainer.innerHTML = '<p class="error-message">Erro ao carregar histórico.</p>';
                }
            };
            
            const renderizarPagina = async (dados) => {
                osData = dados;
                document.getElementById('info-cliente').textContent = `${dados.os_info.cliente_nome} (${dados.os_info.cliente_telefone||'N/D'})`;
                document.getElementById('info-bike').textContent = `${dados.os_info.bicicleta_marca || ''} ${dados.os_info.bicicleta_modelo} (${dados.os_info.bicicleta_cor || 'N/D'})`;
                document.getElementById('problema-container').innerHTML = `<p id="info-problema">${(dados.os_info.problema_relatado || '').replace(/\n/g, '<br>')}</p>`;
                entregaInput.value = dados.os_info.previsao_entrega;
                statusSelect.value = dados.os_info.status;
                if (osData.os_info.codigo_retirada) {
                    btnGerarCodigo.disabled = true;
                    btnGerarCodigo.innerHTML = `<i class="fa-solid fa-check-circle"></i> Código: ${osData.os_info.codigo_retirada}`;
                } else {
                    btnGerarCodigo.disabled = false;
                    btnGerarCodigo.innerHTML = `<i class="fa-solid fa-key"></i> Gerar Novo Código`;
                }
                renderizarItensEtotal();
                renderizarObservacoes(dados.os_observacoes);
                await carregarHistoricoCliente(dados.os_info.cliente_id);
            };

            const renderizarItensEtotal = () => {
                const listaPecas = document.getElementById('lista-pecas');
                const listaServicos = document.getElementById('lista-servicos');
                let totalPecas = 0, totalServicos = 0;
                listaPecas.innerHTML = '';
                if (osData.os_produtos && osData.os_produtos.length > 0) {
                    osData.os_produtos.forEach(p => {
                        const subtotal = p.quantidade * p.preco_unitario_cobrado;
                        totalPecas += subtotal;
                        listaPecas.innerHTML += `<tr><td>${p.quantidade}x ${p.nome}</td><td>R$ ${subtotal.toFixed(2).replace('.',',')}</td><td class="item-actions"><button class="btn-excluir-item" data-id="${p.id}" data-tipo="produto" title="Excluir"><i class="fa-solid fa-trash-can"></i></button></td></tr>`;
                    });
                } else {
                    listaPecas.innerHTML = '<tr><td colspan="3" style="text-align:center;">Nenhuma peça adicionada.</td></tr>';
                }
                listaServicos.innerHTML = '';
                if (osData.os_servicos && osData.os_servicos.length > 0) {
                    osData.os_servicos.forEach(s => {
                        const subtotal = parseFloat(s.preco_cobrado);
                        totalServicos += subtotal;
                        listaServicos.innerHTML += `<tr><td>${s.nome}</td><td>R$ ${subtotal.toFixed(2).replace('.',',')}</td><td class="item-actions"><button class="btn-excluir-item" data-id="${s.id}" data-tipo="servico" title="Excluir"><i class="fa-solid fa-trash-can"></i></button></td></tr>`;
                    });
                } else {
                    listaServicos.innerHTML = '<tr><td colspan="3" style="text-align:center;">Nenhum serviço adicionado.</td></tr>';
                }
                document.getElementById('total-pecas').textContent = `R$ ${totalPecas.toFixed(2).replace('.',',')}`;
                document.getElementById('total-servicos').textContent = `R$ ${totalServicos.toFixed(2).replace('.',',')}`;
                document.getElementById('total-geral').textContent = `R$ ${(totalPecas + totalServicos).toFixed(2).replace('.',',')}`;
            };
            
            const renderizarObservacoes = (observacoes) => {
                listaObservacoes.innerHTML = '';
                if (observacoes && observacoes.length > 0) {
                    observacoes.forEach(obs => {
                        const obsDiv = document.createElement('div');
                        obsDiv.className = 'observacao-item';
                        obsDiv.dataset.observacaoId = obs.id;
                        
                        // Cabeçalho da observação
                        let conteudo = `<div class="observacao-meta">Por: <strong>${obs.usuario_nome}</strong> em ${new Date(obs.data_criacao).toLocaleString('pt-BR')}`;
                        if (obs.texto) {
                            conteudo += ` <button class="btn-edit-observacao" data-obs-id="${obs.id}" style="background: none; border: none; color: #007bff; cursor: pointer; font-size: 12px; margin-left: 10px;"><i class="fa-solid fa-edit"></i> Editar</button>`;
                        }
                        conteudo += `</div>`;
                        
                        // Conteúdo da observação (texto e/ou imagem no mesmo container)
                        if (obs.texto || obs.imagem_url) {
                            conteudo += `<div class="observacao-conteudo" style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.75rem;">`;
                            
                            if (obs.texto) {
                                conteudo += `<div class="observacao-texto" data-obs-id="${obs.id}">${obs.texto.replace(/\n/g, '<br>')}</div>`;
                            }
                            
                            if (obs.imagem_url) {
                                conteudo += `<div class="observacao-imagem-container" style="position: relative; display: inline-block;">
                                    <a href="${obs.imagem_url}" target="_blank"><img src="${obs.imagem_url}" class="observacao-imagem" alt="Foto da observação"></a>
                                    <button class="btn-excluir-foto" data-obs-id="${obs.id}" data-imagem-url="${obs.imagem_url}" style="position: absolute; top: 5px; right: 5px; background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; font-size: 12px; display: flex; align-items: center; justify-content: center;" title="Excluir foto">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>`;
                            }
                            
                            conteudo += `</div>`;
                        }
                        
                        // Seção de comentário do cliente
                        if (obs.comentario_cliente) {
                            conteudo += `
                                <div class="comentario-cliente-section" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #dee2e6;">
                                    <h5 style="color: #495057; margin-bottom: 0.5rem;"><i class="fa-solid fa-comment"></i> Comentário do Cliente</h5>
                                    <div class="comentario-existente" style="background-color: #e3f2fd; padding: 0.75rem; border-radius: 0.375rem; border-left: 4px solid #2196f3;">
                                        <p style="margin: 0; color: #1565c0;">${obs.comentario_cliente}</p>
                                    </div>
                                </div>
                            `;
                        }
                        
                        obsDiv.innerHTML = conteudo;
                        listaObservacoes.appendChild(obsDiv);
                    });
                } else {
                    listaObservacoes.innerHTML = '<p>Nenhuma observação ainda.</p>';
                }
            };

            const carregarOS = async () => {
                try {
                    const response = await fetch(`api/api_os.php?acao=buscar_os_completa&os_id=${osId}&_=${new Date().getTime()}`);
                    const resultado = await response.json();
                    if (resultado.sucesso) { 
                        await renderizarPagina(resultado.dados); 
                    } else { 
                        throw new Error(resultado.mensagem); 
                    }
                } catch (error) {
                    alert("Erro ao carregar OS: " + error.message);
                    document.querySelector('.os-layout').innerHTML = '<p class="error-message">Não foi possível carregar os dados.</p>';
                }
            };
            
            const abrirCamera = async () => {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) { return alert('Seu navegador não suporta acesso à câmera.'); }
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                    currentStream = stream;
                    cameraView.srcObject = stream;
                    cameraOverlay.style.display = 'flex';
                } catch (err) { console.error("Erro na câmera: ", err); alert('Não foi possível acessar a câmera. Verifique as permissões.'); }
            };

            const fecharCamera = () => { if (currentStream) { currentStream.getTracks().forEach(track => track.stop()); } cameraOverlay.style.display = 'none'; };

            const tirarFoto = () => {
                const canvas = document.createElement('canvas');
                canvas.width = cameraView.videoWidth;
                canvas.height = cameraView.videoHeight;
                canvas.getContext('2d').drawImage(cameraView, 0, 0);
                canvas.toBlob(blob => {
                    const fileName = `foto_os_${osId}_${new Date().getTime()}.jpg`;
                    const file = new File([blob], fileName, { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    for(const oldFile of fotosInput.files) { dataTransfer.items.add(oldFile); }
                    dataTransfer.items.add(file);
                    fotosInput.files = dataTransfer.files;
                    alert(`Foto adicionada à lista de uploads! (${fileName})`);
                    fecharCamera();
                }, 'image/jpeg');
            };

            const adicionarItem = async (endpoint, payload) => {
                const response = await fetch(endpoint, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                const resultado = await response.json();
                if (!resultado.sucesso) throw new Error(resultado.mensagem);
                return resultado;
            };

            const adicionarPecaRapido = async (peca) => {
                const quantidade = document.getElementById('qty-pecas').value;
                try {
                    await adicionarItem('api/api_os.php?acao=adicionar_peca_os', { os_id: osId, produto_id: peca.id, quantidade: quantidade, preco_cobrado: peca.preco_venda });
                    await carregarOS();
                    showToast('Peça inserida com sucesso!');
                } catch (error) { alert('Erro: ' + error.message); }
                searchPecasInput.value = '';
                searchResultsPecas.style.display = 'none';
            };

            const adicionarServicoRapido = async (servico) => {
                try {
                    await adicionarItem('api/api_os.php?acao=adicionar_servico_os', { os_id: osId, servico_id: servico.id, preco_cobrado: servico.preco_padrao });
                    await carregarOS();
                    showToast('Serviço inserido com sucesso!');
                } catch (error) { alert('Erro: ' + error.message); }
                searchServicosInput.value = '';
                searchResultsServicos.style.display = 'none';
            };

            const buscarPecas = async (termo) => {
                if (termo.length < 2) { searchResultsPecas.style.display = 'none'; return; }
                const response = await fetch(`api/api_vendas.php?acao=buscar_produtos&termo=${termo}`);
                const produtos = await response.json();
                searchResultsPecas.innerHTML = '';
                if (produtos.length > 0) {
                    produtos.forEach(p => {
                        const li = document.createElement('li');
                        const img = p.imagem_url ? `<img src="${p.imagem_url}" class="search-result-img">` : '<i class="fa-solid fa-camera fa-2x" style="color:#ccc; width:40px; text-align:center;"></i>';
                        li.innerHTML = `
                            ${img}
                            <div class="search-result-info">
                                <span>${p.nome}</span><br>
                                <small>Estoque: ${p.estoque_atual} | R$ ${parseFloat(p.preco_venda).toFixed(2).replace('.',',')}</small>
                            </div> 
                            <button class="btn btn-sm btn-secundario"><i class="fa-solid fa-cog"></i></button>`;
                        li.querySelector('.search-result-info').onclick = () => adicionarPecaRapido(p);
                        li.querySelector('button').onclick = () => abrirModalPeca(p);
                        searchResultsPecas.appendChild(li);
                    });
                    searchResultsPecas.style.display = 'block';
                } else { searchResultsPecas.style.display = 'none'; }
            };

            const buscarServicos = async (termo) => {
                if (termo.length < 2) { searchResultsServicos.style.display = 'none'; return; }
                const response = await fetch(`api/api_os.php?acao=listar_servicos`);
                const resultado = await response.json();
                searchResultsServicos.innerHTML = '';
                if(resultado.sucesso) {
                    const servicosFiltrados = resultado.dados.filter(s => s.nome.toLowerCase().includes(termo.toLowerCase()));
                    servicosFiltrados.forEach(s => {
                        const li = document.createElement('li');
                        li.textContent = `${s.nome} - R$ ${parseFloat(s.preco_padrao).toFixed(2)}`;
                        li.onclick = () => adicionarServicoRapido(s);
                        searchResultsServicos.appendChild(li);
                    });
                    searchResultsServicos.style.display = 'block';
                }
            };
            
            const abrirModalPeca = (peca) => {
                const imagemPreview = document.getElementById('modal-peca-imagem-preview');
                if(peca.imagem_url) { imagemPreview.src = peca.imagem_url; imagemPreview.style.display = 'block'; }
                else { imagemPreview.style.display = 'none'; }
                document.getElementById('modal-peca-name').textContent = peca.nome;
                document.getElementById('modal-peca-id').value = peca.id;
                document.getElementById('modal-peca-price').value = parseFloat(peca.preco_venda).toFixed(2);
                document.getElementById('modal-peca-qty').value = document.getElementById('qty-pecas').value;
                document.getElementById('modal-peca-qty').max = peca.estoque_atual;
                pecaModal.style.display = 'flex';
            };
            const fecharModalPeca = () => { pecaModal.style.display = 'none'; searchPecasInput.value = ''; searchResultsPecas.style.display = 'none'; }

            pecaModalForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const payload = { os_id: osId, produto_id: document.getElementById('modal-peca-id').value, quantidade: document.getElementById('modal-peca-qty').value, preco_cobrado: document.getElementById('modal-peca-price').value };
                try { await adicionarItem('api/api_os.php?acao=adicionar_peca_os', payload); fecharModalPeca(); await carregarOS(); showToast('Peça inserida com sucesso!'); } 
                catch (error) { alert('Erro ao adicionar peça: ' + error.message); }
            });
            
            const ativarBotaoSalvar = () => {
                btnAtualizarOS.classList.remove('btn-secundario');
                btnAtualizarOS.classList.add('btn-sucesso');
            };

            btnAtualizarOS.addEventListener('click', async () => {
                const novoStatus = statusSelect.value;
                if (novoStatus === 'FINALIZADA' && osData.os_info.status !== 'FINALIZADA') {
                    abrirModalFinalizacao();
                    return;
                }
                const btn = btnAtualizarOS;
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
                try {
                    let algumaMudanca = false;
                    if (entregaInput.value !== osData.os_info.previsao_entrega) {
                        await adicionarItem(`api/api_os.php?acao=atualizar_dados_os`, { os_id: osId, previsao_entrega: entregaInput.value });
                        algumaMudanca = true;
                    }
                    if (novoStatus !== osData.os_info.status) {
                        await adicionarItem(`api/api_os.php?acao=atualizar_status_os`, { os_id: osId, novo_status: novoStatus });
                        if (confirm('OS Atualizada com sucesso!\n\nDeseja notificar o cliente via WhatsApp?')) {
                            enviarNotificacaoWhatsApp();
                        }
                        algumaMudanca = true;
                    }
                    if (algumaMudanca) { 
                        await carregarOS(); 
                        showToast('OS atualizada com sucesso!');
                    } else {
                        alert("Nenhuma alteração para salvar.");
                    }
                } catch (error) {
                    alert("Falha ao atualizar OS: " + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-save"></i> Atualizar OS';
                    btn.classList.remove('btn-sucesso');
                    btn.classList.add('btn-secundario');
                }
            });
            
            finalizarForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const metodoPagamento = metodoPagamentoSelect.value;
                const payload = {
                    os_id: osId,
                    metodo_pagamento: metodoPagamento,
                    codigo_retirada: document.getElementById('codigo-retirada-input').value
                };

                if (metodoPagamento === 'CREDIARIO') {
                    const cpf = document.getElementById('cliente-cpf').value;
                    if (!validarCPF(cpf)) {
                        alert("CPF inválido! Por favor, verifique o número.");
                        document.getElementById('cliente-cpf').focus();
                        return;
                    }
                    payload.dados_cliente = {
                        nome: document.getElementById('cliente-nome').value,
                        telefone: document.getElementById('cliente-telefone').value,
                        email: document.getElementById('cliente-email').value,
                        cpf: cpf,
                        cep: document.getElementById('cliente-cep').value,
                        logradouro: document.getElementById('cliente-logradouro').value,
                        numero: document.getElementById('cliente-numero').value,
                        complemento: document.getElementById('cliente-complemento').value,
                        bairro: document.getElementById('cliente-bairro').value,
                        cidade: document.getElementById('cliente-cidade').value,
                        estado: document.getElementById('cliente-estado').value
                    };
                }

                const btn = e.target.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Finalizando...';

                try {
                    const response = await fetch('api/api_os.php?acao=finalizar_e_pagar_os', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);
                    alert(resultado.mensagem);
                    
                    if (metodoPagamento === 'CREDIARIO') {
                        window.open(`imprimir_divida.php?id=${resultado.venda_id}`, '_blank');
                    }
                    window.open(`imprimir_os.php?id=${osId}`, '_blank');
                    
                    window.location.href = 'dashboard.php';
                } catch (error) {
                    alert("Falha na finalização: " + error.message);
                    btn.disabled = false;
                    btn.innerHTML = 'Confirmar Pagamento e Finalizar OS';
                }
            });
            
            metodoPagamentoSelect.addEventListener('change', (e) => {
                const formCrediario = document.getElementById('form-cliente-crediario');
                const isCrediario = e.target.value === 'CREDIARIO';
                formCrediario.style.display = isCrediario ? 'block' : 'none';
                document.getElementById('cliente-cpf').required = isCrediario;
            });

            statusSelect.addEventListener('change', ativarBotaoSalvar);
            entregaInput.addEventListener('change', ativarBotaoSalvar);
            btnAbrirCamera.addEventListener('click', abrirCamera);
            btnCloseCamera.addEventListener('click', fecharCamera);
            btnSnapPhoto.addEventListener('click', tirarFoto);
            searchPecasInput.addEventListener('input', e => { clearTimeout(debounceTimer); debounceTimer = setTimeout(() => buscarPecas(e.target.value), 300); });
            searchServicosInput.addEventListener('input', e => { clearTimeout(debounceTimer); debounceTimer = setTimeout(() => buscarServicos(e.target.value), 300); });
            pecaModal.querySelector('.modal-close').onclick = fecharModalPeca;
            finalizarModal.querySelector('.modal-close').onclick = fecharModalFinalizacao;
            osItemsContainer.addEventListener('click', async (e) => {
                const target = e.target.closest('.btn-excluir-item');
                if (target && confirm('Tem certeza que deseja remover este item?')) {
                    await adicionarItem('api/api_os.php?acao=excluir_item_os', { item_id: target.dataset.id, tipo: target.dataset.tipo });
                    await carregarOS();
                    showToast('Item removido.', 'error');
                }
            });
            
            formNovaObservacao.addEventListener('submit', (e) => {
                e.preventDefault();
                const texto = document.getElementById('texto-observacao').value;
                if (!texto.trim() && fotosInput.files.length === 0) { alert("Escreva um texto ou selecione ao menos uma foto."); return; }
                const btn = e.target.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
                const formData = new FormData();
                formData.append('os_id', osId);
                formData.append('texto', texto);
                for (let i = 0; i < fotosInput.files.length; i++) { formData.append('fotos[]', fotosInput.files[i]); }
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'api/api_os.php?acao=salvar_observacao', true);
                xhr.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        const percentComplete = Math.round((event.loaded / event.total) * 100);
                        progressContainer.style.display = 'block';
                        progressBarFill.style.width = percentComplete + '%';
                        progressBarFill.textContent = percentComplete + '%';
                    }
                };
                xhr.onload = async () => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-plus"></i> Adicionar Observação';
                    progressContainer.style.display = 'none';
                    if (xhr.status === 200) {
                        try {
                            const resultado = JSON.parse(xhr.responseText);
                            if (resultado.sucesso) {
                                alert(resultado.mensagem);
                                formNovaObservacao.reset();
                                await carregarOS();
                            } else {
                                throw new Error(resultado.mensagem);
                            }
                        } catch (jsonError) {
                            alert("Erro ao processar a resposta do servidor.");
                        }
                    } else {
                        alert(`Erro no upload: ${xhr.statusText}`);
                    }
                };
                xhr.onerror = () => {
                    alert("Erro de rede.");
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-plus"></i> Adicionar Observação';
                    progressContainer.style.display = 'none';
                };
                xhr.send(formData);
            });
            
            // Event listener para editar problema relatado
            btnEditProblema.addEventListener('click', () => {
                if (isEditingProblema) return;
                
                isEditingProblema = true;
                const problemaText = document.getElementById('info-problema');
                const currentText = problemaText.textContent;
                
                // Criar textarea para edição
                const textarea = document.createElement('textarea');
                textarea.className = 'form-input';
                textarea.style.minHeight = '80px';
                textarea.value = currentText;
                
                // Criar botões de ação
                const actionButtons = document.createElement('div');
                actionButtons.style.marginTop = '10px';
                actionButtons.innerHTML = `
                    <button type="button" class="btn btn-sucesso btn-sm" id="btn-salvar-problema">
                        <i class="fa-solid fa-check"></i> Salvar
                    </button>
                    <button type="button" class="btn btn-secundario btn-sm" id="btn-cancelar-problema" style="margin-left: 10px;">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </button>
                `;
                
                // Substituir o conteúdo
                problemaContainer.innerHTML = '';
                problemaContainer.appendChild(textarea);
                problemaContainer.appendChild(actionButtons);
                
                // Event listeners para os botões
                document.getElementById('btn-salvar-problema').addEventListener('click', async () => {
                    const novoTexto = textarea.value.trim();
                    
                    try {
                        const response = await fetch('api/api_os.php?acao=atualizar_problema_relatado', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ os_id: osId, texto: novoTexto })
                        });
                        
                        const resultado = await response.json();
                        if (!resultado.sucesso) throw new Error(resultado.mensagem);
                        
                        // Restaurar visualização normal
                        problemaContainer.innerHTML = `<p id="info-problema">${novoTexto || 'Nenhum problema relatado'}</p>`;
                        isEditingProblema = false;
                        showToast('Problema atualizado com sucesso!');
                        
                    } catch (error) {
                        alert('Erro ao salvar: ' + error.message);
                    }
                });
                
                document.getElementById('btn-cancelar-problema').addEventListener('click', () => {
                    problemaContainer.innerHTML = `<p id="info-problema">${currentText}</p>`;
                    isEditingProblema = false;
                });
                
                textarea.focus();
            });
            
            // Event listener para edição de observações
            listaObservacoes.addEventListener('click', async (e) => {
                // Handler para excluir foto
                if (e.target.closest('.btn-excluir-foto')) {
                    const btnExcluir = e.target.closest('.btn-excluir-foto');
                    const observacaoId = btnExcluir.dataset.obsId;
                    const imagemUrl = btnExcluir.dataset.imagemUrl;
                    
                    if (!confirm('Tem certeza que deseja excluir esta foto? Esta ação não pode ser desfeita.')) {
                        return;
                    }
                    
                    try {
                        btnExcluir.disabled = true;
                        btnExcluir.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                        
                        const response = await fetch('api/api_os.php?acao=excluir_foto_observacao', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                observacao_id: observacaoId,
                                imagem_url: imagemUrl
                            })
                        });
                        
                        const resultado = await response.json();
                        
                        if (resultado.sucesso) {
                            showToast('Foto excluída com sucesso!', 'success');
                            // Recarregar as observações para refletir a mudança
                            await carregarOS();
                        } else {
                            throw new Error(resultado.mensagem || 'Erro ao excluir foto');
                        }
                    } catch (error) {
                        alert('Erro ao excluir foto: ' + error.message);
                        btnExcluir.disabled = false;
                        btnExcluir.innerHTML = '<i class="fa-solid fa-trash"></i>';
                    }
                    return;
                }
                
                // Handler para editar observação
                if (e.target.closest('.btn-edit-observacao')) {
                    const btnEdit = e.target.closest('.btn-edit-observacao');
                    const observacaoId = btnEdit.dataset.obsId;
                    const observacaoTexto = document.querySelector(`.observacao-texto[data-obs-id="${observacaoId}"]`);
                    
                    if (!observacaoTexto || btnEdit.dataset.editing === 'true') return;
                    
                    const textoAtual = observacaoTexto.innerHTML.replace(/<br>/g, '\n');
                    
                    // Marcar como editando
                    btnEdit.dataset.editing = 'true';
                    
                    // Criar textarea para edição
                    const textarea = document.createElement('textarea');
                    textarea.className = 'form-input';
                    textarea.style.minHeight = '80px';
                    textarea.style.width = '100%';
                    textarea.value = textoAtual;
                    
                    // Criar botões de ação
                    const actionButtons = document.createElement('div');
                    actionButtons.style.marginTop = '10px';
                    actionButtons.innerHTML = `
                        <button type="button" class="btn btn-sucesso btn-sm" data-action="salvar">
                            <i class="fa-solid fa-check"></i> Salvar
                        </button>
                        <button type="button" class="btn btn-secundario btn-sm" data-action="cancelar" style="margin-left: 10px;">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </button>
                    `;
                    
                    // Substituir o texto pelo textarea
                    const container = observacaoTexto.parentNode;
                    const originalContent = observacaoTexto.outerHTML;
                    container.replaceChild(textarea, observacaoTexto);
                    container.appendChild(actionButtons);
                    
                    // Ocultar o botão de editar
                    btnEdit.style.display = 'none';
                    
                    // Focar no textarea
                    textarea.focus();
                    
                    // Função para restaurar o estado original
                    const restaurarEstado = () => {
                        container.removeChild(actionButtons);
                        container.innerHTML = container.innerHTML.replace(textarea.outerHTML, originalContent);
                        btnEdit.style.display = 'inline';
                        btnEdit.dataset.editing = 'false';
                    };
                    
                    // Event listeners para os botões
                    actionButtons.addEventListener('click', async function(event) {
                        const action = event.target.closest('button')?.dataset.action;
                        
                        if (action === 'cancelar') {
                            restaurarEstado();
                        } else if (action === 'salvar') {
                            const novoTexto = textarea.value.trim();
                            
                            if (novoTexto === textoAtual) {
                                restaurarEstado();
                                return;
                            }
                            
                            if (!novoTexto) {
                                alert('O texto da observação não pode estar vazio.');
                                return;
                            }
                            
                            const btnSalvar = event.target.closest('button');
                            try {
                                btnSalvar.disabled = true;
                                btnSalvar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
                                
                                const response = await fetch('api/api_os.php?acao=atualizar_observacao', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        observacao_id: observacaoId,
                                        texto: novoTexto
                                    })
                                });
                                
                                const resultado = await response.json();
                                
                                if (resultado.sucesso) {
                                    // Atualizar o conteúdo da observação
                                    const novoTextoFormatado = novoTexto.replace(/\n/g, '<br>');
                                    container.removeChild(actionButtons);
                                    container.innerHTML = container.innerHTML.replace(textarea.outerHTML, `<div class="observacao-texto" data-obs-id="${observacaoId}">${novoTextoFormatado}</div>`);
                                    btnEdit.style.display = 'inline';
                                    btnEdit.dataset.editing = 'false';
                                    showToast('Observação atualizada com sucesso!');
                                } else {
                                    throw new Error(resultado.mensagem || 'Erro ao atualizar observação');
                                }
                            } catch (error) {
                                alert('Erro ao salvar: ' + error.message);
                                btnSalvar.disabled = false;
                                btnSalvar.innerHTML = '<i class="fa-solid fa-check"></i> Salvar';
                            }
                        }
                    });
                }
            });
            
            carregarOS();
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
    
    <!-- Script de Consulta de CEP -->
    <script src="assets/js/cep-consulta.js"></script>
</body>
</html>