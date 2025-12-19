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
            alert('O caixa deve estar aberto para realizar vendas. Redirecionando para o financeiro...');
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
        echo getTituloComEmpresa('Nova Venda');
    ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .venda-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
        }

        .produtos-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .carrinho-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .search-container {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        #search-results-cliente {
            position: absolute !important;
            top: calc(100% + 5px) !important;
            left: 1rem !important;
            right: 1rem !important;
            z-index: 2000 !important;
            margin-top: 0 !important;
        }

        .search-results li {
            padding: 12px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .search-results li:hover {
            background-color: #f8f9fa;
        }

        .search-result-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            background: #f8f9fa;
        }

        .search-result-info {
            flex: 1;
        }

        .search-result-info strong {
            color: #333;
            font-size: 14px;
        }

        .search-result-info small {
            color: #666;
            font-size: 12px;
        }

        .produto-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .produto-item:hover {
            border-color: var(--cor-primaria);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .produto-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            background: #f8f9fa;
        }

        .produto-info {
            flex: 1;
        }

        .produto-nome {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .produto-detalhes {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .produto-preco {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--cor-primaria);
        }

        .carrinho-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .carrinho-item:last-child {
            border-bottom: none;
        }

        .item-info {
            flex: 1;
        }

        .item-nome {
            font-weight: 500;
            font-size: 0.875rem;
        }

        .item-detalhes {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        .qty-btn:hover {
            background: #f8f9fa;
        }

        .qty-input {
            width: 40px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.25rem;
            font-size: 0.875rem;
        }

        .total-venda {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--cor-primaria);
            text-align: center;
            margin: 1.5rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .cliente-section {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .cliente-selecionado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem;
            background: white;
            border-radius: 6px;
            margin-top: 0.5rem;
        }

        .alerta-crediario {
            background-color: var(--cor-erro);
            color: white;
            padding: 1rem;
            border-radius: var(--raio-borda);
            text-align: center;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .pagamento-section {
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .venda-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }
            
            .carrinho-section {
                position: static;
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
            <button onclick="history.back()" class="btn btn-secundario" title="Voltar à página anterior"><i class="fa-solid fa-arrow-left"></i> Voltar</button>
            <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Ir para o Painel Principal"><i class="fa-solid fa-house"></i></a>
        </div>
        
        <div class="page-header">
            <h1><i class="fa-solid fa-cart-plus"></i> Nova Venda</h1>
        </div>
        
        <div class="venda-container">
            <!-- Seção de Produtos -->
            <div class="produtos-section">
                <h2><i class="fa-solid fa-search"></i> Buscar Produtos</h2>
                <div class="search-container">
                    <input type="text" id="search-produto" class="form-input" placeholder="Digite o nome do produto ou código...">
                    <ul id="search-results" class="search-results"></ul>
                </div>
                
                <div id="produtos-lista">
                    <!-- Produtos serão carregados aqui -->
                </div>
            </div>
            
            <!-- Seção do Carrinho -->
            <div class="carrinho-section">
                <!-- Cliente -->
                <div class="cliente-section">
                    <h3><i class="fa-solid fa-user"></i> Cliente</h3>
                    <input type="text" id="search-cliente" class="form-input" placeholder="Buscar cliente...">
                    <ul id="search-results-cliente" class="search-results"></ul>
                    <div id="cliente-selecionado"></div>
                </div>
                
                <!-- Método de Pagamento -->
                <div class="pagamento-section">
                    <h3><i class="fa-solid fa-credit-card"></i> Pagamento</h3>
                    <select id="metodo-pagamento" class="form-input">
                        <option value="">Selecione...</option>
                        <option value="DINHEIRO">Dinheiro</option>
                        <option value="DEBITO">Cartão de Débito</option>
                        <option value="CREDITO">Cartão de Crédito</option>
                        <option value="PIX">PIX</option>
                        <option value="CREDIARIO">Crediário</option>
                    </select>
                </div>
                
                <!-- Informações Fiscais -->
                <div class="fiscal-section">
                    <h3 class="fiscal-header" onclick="toggleFiscalSection()" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between;">
                        <span><i class="fa-solid fa-file-invoice"></i> Informações Fiscais (Opcional)</span>
                        <i id="fiscal-arrow" class="fa-solid fa-chevron-down" style="transition: transform 0.3s ease;"></i>
                    </h3>
                    <div id="fiscal-content" style="display: none; margin-top: 1rem;">
                        <div class="form-group">
                            <label class="form-label">CFOP (Opcional)</label>
                            <select id="cfop-venda" class="form-input">
                                <option value="">Selecione o CFOP...</option>
                                <option value="5102">5.102 - Venda de mercadoria adquirida (dentro do estado)</option>
                                <option value="6102">6.102 - Venda de mercadoria adquirida (fora do estado)</option>
                                <option value="5101">5.101 - Venda de produção própria (dentro do estado)</option>
                                <option value="6101">6.101 - Venda de produção própria (fora do estado)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Natureza da Operação (Opcional)</label>
                            <input type="text" id="natureza-operacao" class="form-input" placeholder="Ex: Venda de mercadoria" value="Venda de mercadoria">
                        </div>
                    </div>
                </div>
                
                <!-- Carrinho -->
                <div>
                    <h3><i class="fa-solid fa-shopping-cart"></i> Carrinho</h3>
                    <div id="carrinho-itens">
                        <p style="text-align: center; color: #6c757d; padding: 2rem;">Carrinho vazio</p>
                    </div>
                    
                    <div class="total-venda">
                        Total: <span id="valor-total">R$ 0,00</span>
                    </div>
                    
                    <button id="btn-finalizar" class="btn btn-destaque" style="width: 100%;">
                        <i class="fa-solid fa-check"></i> Finalizar Venda
                    </button>
                </div>
            </div>
        </div>
    </main>
    <!-- Modal simples para adicionar produto -->
    <div id="produto-modal" class="modal-backdrop" style="display: none;">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="modal-produto-nome">Adicionar Produto</h2>
            <form id="produto-form">
                <input type="hidden" id="produto-id">
                <div class="produto-imagem" style="text-align: center; margin-bottom: 1rem;">
                    <img id="produto-imagem" src="" alt="Imagem do produto" style="max-width: 200px; max-height: 200px; border-radius: 8px; display: none;">
                </div>
                <div class="form-group">
                    <label class="form-label">Quantidade</label>
                    <input type="number" id="produto-qty" class="form-input" value="1" min="1" required>
                </div>
                <div class="form-group" id="tamanho-group" style="display: none;">
                    <label class="form-label">Tamanho</label>
                    <select id="produto-tamanho" class="form-input"></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Preço Unitário</label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="number" id="produto-preco" class="form-input" step="0.01" readonly style="flex: 1;">
                        <button type="button" id="btn-editar-preco" class="btn btn-secundario" style="padding: 0.5rem; white-space: nowrap;">
                            <i class="fa-solid fa-edit"></i> Editar
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-destaque" style="width: 100%;">Adicionar ao Carrinho</button>
            </form>
        </div>
    </div>
    
    <!-- Modal para novo cliente -->
    <div id="cliente-modal" class="modal-backdrop" style="display: none;">
        <div class="modal-content modal-content-large">
            <span class="modal-close">&times;</span>
            <h2>Novo Cliente</h2>
            <form id="cliente-form">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nome Completo *</label>
                        <input type="text" id="cliente-nome" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telefone</label>
                        <input type="tel" id="cliente-telefone" class="form-input" placeholder="(00) 00000-0000">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="cliente-email" class="form-input" placeholder="cliente@email.com">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tipo de Pessoa</label>
                        <select id="cliente-tipo-pessoa" class="form-input">
                            <option value="fisica" selected>Pessoa Física</option>
                            <option value="juridica">Pessoa Jurídica</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">CPF/CNPJ</label>
                        <input type="text" id="cliente-documento" class="form-input" placeholder="000.000.000-00">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Data de Nascimento</label>
                    <input type="date" id="cliente-nascimento" class="form-input">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">CEP</label>
                        <input type="text" id="cliente-cep" class="form-input" placeholder="00000-000" data-cep="true">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cidade</label>
                        <input type="text" id="cliente-cidade" class="form-input" data-cep-cidade="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select id="cliente-estado" class="form-input" data-cep-estado="true">
                            <option value="">Selecione o Estado</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bairro</label>
                        <input type="text" id="cliente-bairro" class="form-input" data-cep-bairro="true">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Endereço (Rua/Avenida)</label>
                    <input type="text" id="cliente-logradouro" class="form-input" placeholder="Rua, Avenida, etc." data-cep-logradouro="true">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Número</label>
                        <input type="text" id="cliente-numero" class="form-input" placeholder="Número da residência">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Complemento</label>
                        <input type="text" id="cliente-complemento" class="form-input" placeholder="Apartamento, bloco, etc." data-cep-complemento="true">
                    </div>
                </div>
                <div id="campos-pessoa-juridica" style="display: none;">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Inscrição Estadual</label>
                            <input type="text" id="cliente-inscricao-estadual" class="form-input" placeholder="000.000.000.000">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Inscrição Municipal</label>
                            <input type="text" id="cliente-inscricao-municipal" class="form-input" placeholder="000000000">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-destaque" style="width: 100%;">Salvar Cliente</button>
            </form>
        </div>
    </div>

    <!-- Modal para senha de administrador -->
    <div id="senha-admin-modal" class="modal-backdrop" style="display: none;">
        <div class="modal-content" style="max-width: 400px;">
            <span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-lock"></i> Autorização Necessária</h2>
            <p>Digite a senha de administrador para editar o preço:</p>
            <form id="senha-admin-form">
                <div class="form-group">
                    <label class="form-label">Senha de Administrador</label>
                    <input type="password" id="senha-admin" class="form-input" required placeholder="Digite a senha...">
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="btn btn-secundario" style="flex: 1;" onclick="document.getElementById('senha-admin-modal').style.display = 'none'">Cancelar</button>
                    <button type="submit" class="btn btn-destaque" style="flex: 1;">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para confirmação de dados do cliente no crediário -->
    <div id="crediario-modal" class="modal-backdrop" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-credit-card"></i> Confirmação de Dados - Crediário</h2>
            <p>Para vendas no crediário, é obrigatório confirmar os dados do cliente:</p>
            <form id="crediario-form">
                <div class="form-group">
                    <label class="form-label">Nome Completo *</label>
                    <input type="text" id="crediario-nome" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">CPF *</label>
                    <input type="text" id="crediario-cpf" class="form-input" required placeholder="000.000.000-00" maxlength="14">
                    <small style="color: #6c757d;">Campo obrigatório para crediário</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="tel" id="crediario-telefone" class="form-input" placeholder="(00) 00000-0000">
                </div>
                <div class="form-group">
                    <label class="form-label">CEP</label>
                    <input type="text" id="crediario-cep" class="form-input" placeholder="00000-000" data-cep="true">
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label">Cidade</label>
                        <input type="text" id="crediario-cidade" class="form-input" placeholder="Cidade" data-cep-localidade="true">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Estado</label>
                        <select id="crediario-estado" class="form-input" data-cep-uf="true">
                            <option value="">UF</option>
                            <option value="AC">AC</option>
                            <option value="AL">AL</option>
                            <option value="AP">AP</option>
                            <option value="AM">AM</option>
                            <option value="BA">BA</option>
                            <option value="CE">CE</option>
                            <option value="DF">DF</option>
                            <option value="ES">ES</option>
                            <option value="GO">GO</option>
                            <option value="MA">MA</option>
                            <option value="MT">MT</option>
                            <option value="MS">MS</option>
                            <option value="MG">MG</option>
                            <option value="PA">PA</option>
                            <option value="PB">PB</option>
                            <option value="PR">PR</option>
                            <option value="PE">PE</option>
                            <option value="PI">PI</option>
                            <option value="RJ">RJ</option>
                            <option value="RN">RN</option>
                            <option value="RS">RS</option>
                            <option value="RO">RO</option>
                            <option value="RR">RR</option>
                            <option value="SC">SC</option>
                            <option value="SP">SP</option>
                            <option value="SE">SE</option>
                            <option value="TO">TO</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Endereço</label>
                    <input type="text" id="crediario-endereco" class="form-input" placeholder="Rua, Avenida" data-cep-logradouro="true">
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Número</label>
                        <input type="text" id="crediario-numero" class="form-input" placeholder="Nº">
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label">Complemento</label>
                        <input type="text" id="crediario-complemento" class="form-input" placeholder="Apartamento, bloco, etc.">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Bairro</label>
                    <input type="text" id="crediario-bairro" class="form-input" placeholder="Bairro" data-cep-bairro="true">
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="btn btn-secundario" style="flex: 1;" onclick="document.getElementById('crediario-modal').style.display = 'none'">Cancelar</button>
                    <button type="submit" class="btn btn-destaque" style="flex: 1;">Confirmar e Finalizar</button>
                </div>
            </form>
        </div>
    </div>
    <?php include '_partials/menu_admin.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let carrinho = [];
            let clienteSelecionado = null;
            let debounceTimer;
            let produtoSelecionado = null;

            // Elementos DOM
            const searchProduto = document.getElementById('search-produto');
            const searchResults = document.getElementById('search-results');
            const searchCliente = document.getElementById('search-cliente');
            const searchResultsCliente = document.getElementById('search-results-cliente');
            const clienteSelecionadoDiv = document.getElementById('cliente-selecionado');
            const carrinhoItens = document.getElementById('carrinho-itens');
            const valorTotal = document.getElementById('valor-total');
            const metodoPagamento = document.getElementById('metodo-pagamento');
            const btnFinalizar = document.getElementById('btn-finalizar');
            const produtoModal = document.getElementById('produto-modal');
            const clienteModal = document.getElementById('cliente-modal');
            const senhaAdminModal = document.getElementById('senha-admin-modal');
        const btnEditarPreco = document.getElementById('btn-editar-preco');
        const produtoPreco = document.getElementById('produto-preco');
        let precoOriginal = 0;
        
        // Variáveis para modal de crediário
        const crediarioModal = document.getElementById('crediario-modal');
        const crediarioForm = document.getElementById('crediario-form');
        const crediarioNome = document.getElementById('crediario-nome');
        const crediarioCpf = document.getElementById('crediario-cpf');
        const crediarioTelefone = document.getElementById('crediario-telefone');
        const crediarioEndereco = document.getElementById('crediario-endereco');
        const crediarioCep = document.getElementById('crediario-cep');
        const crediarioCidade = document.getElementById('crediario-cidade');
        const crediarioEstado = document.getElementById('crediario-estado');
        const crediarioNumero = document.getElementById('crediario-numero');
        const crediarioComplemento = document.getElementById('crediario-complemento');
        const crediarioBairro = document.getElementById('crediario-bairro');
        
        // Função para validar CPF
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
        
        // Função para formatar CPF
        function formatarCPF(cpf) {
            cpf = cpf.replace(/\D/g, '');
            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            return cpf;
        }

            // Busca de clientes
            searchCliente.addEventListener('input', e => {
                clearTimeout(debounceTimer);
                const termo = e.target.value;
                if (termo.length < 2) {
                    searchResultsCliente.style.display = 'none';
                    return;
                }
                debounceTimer = setTimeout(() => {
                    fetch(`api/api_clientes.php?acao=listar_clientes&termo=${termo}`)
                        .then(res => res.json())
                        .then(resultado => {
                            searchResultsCliente.innerHTML = '';
                            if (resultado.sucesso && resultado.dados.length > 0) {
                                resultado.dados.forEach(cliente => {
                                    const li = document.createElement('li');
                                    li.textContent = cliente.nome;
                                    li.onclick = () => selecionarCliente(cliente);
                                    searchResultsCliente.appendChild(li);
                                });
                            }
                            // Opção para adicionar novo cliente
                            const liNovo = document.createElement('li');
                            liNovo.innerHTML = `<i class="fa-solid fa-plus"></i> Novo cliente: "${termo}"`;
                            liNovo.style.color = 'var(--primary-color)';
                            liNovo.onclick = () => abrirModalCliente(termo);
                            searchResultsCliente.appendChild(liNovo);
                            searchResultsCliente.style.display = 'block';
                        });
                }, 300);
            });

            async function selecionarCliente(cliente) {
                clienteSelecionado = cliente;
                
                let alertaCrediario = '';
                
                // Verificar se o cliente tem crediário em aberto
                try {
                    const response = await fetch(`api/api_clientes.php?acao=get_historico_completo&id=${cliente.id}`);
                    const resultado = await response.json();
                    
                    if (resultado.sucesso) {
                        const historico = resultado.dados;
                        const crediarioAberto = historico.compras.find(v => v.metodo_pagamento === 'CREDIARIO' && v.pago == 0);
                        if (crediarioAberto) {
                            alertaCrediario = `<div class="alerta-crediario"><i class="fa-solid fa-triangle-exclamation"></i> <a href="historico_cliente.php?id=${cliente.id}&aba=crediario" target="_blank" style="color:white; text-decoration:underline;">Cliente possui crediário em aberto!</a></div>`;
                        }
                    }
                } catch (error) {
                    console.error('Erro ao verificar crediário:', error);
                }
                
                clienteSelecionadoDiv.innerHTML = `
                    ${alertaCrediario}
                    <div class="cliente-selecionado">
                        <span><strong>${cliente.nome}</strong></span>
                        <button type="button" onclick="removerCliente()" class="btn btn-sm btn-erro">&times;</button>
                    </div>
                `;
                searchCliente.value = '';
                searchResultsCliente.style.display = 'none';
            }

            window.removerCliente = function() {
                clienteSelecionado = null;
                clienteSelecionadoDiv.innerHTML = '';
            }

            // Busca de produtos
            searchProduto.addEventListener('input', e => {
                clearTimeout(debounceTimer);
                const termo = e.target.value;
                if (termo.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }
                debounceTimer = setTimeout(() => {
                    fetch(`api/api_vendas.php?acao=buscar_produtos&termo=${termo}`)
                        .then(res => res.json())
                        .then(produtos => {
                            searchResults.innerHTML = '';
                            if (produtos && produtos.length > 0) {
                                produtos.forEach(produto => {
                                    const li = document.createElement('li');
                                    const img = produto.imagem_url ? `<img src="${produto.imagem_url}" class="search-result-img">` : '<i class="fa-solid fa-camera fa-2x" style="color:#ccc; width:40px; text-align:center;"></i>';
                                    li.innerHTML = `
                                        ${img}
                                        <div class="search-result-info">
                                            <strong>${produto.nome}</strong><br>
                                            <small>Marca: ${produto.marca || 'N/A'} | Estoque: ${produto.estoque_atual} | R$ ${parseFloat(produto.preco_venda).toFixed(2).replace('.', ',')}</small>
                                        </div>`;
                                    li.onclick = () => abrirModalProduto(produto);
                                    searchResults.appendChild(li);
                                });
                            }
                            searchResults.style.display = 'block';
                        });
                }, 300);
            });

            // Fechar resultados de busca ao clicar fora
            document.addEventListener('click', e => {
                if (!e.target.closest('.search-container')) {
                    searchResultsCliente.style.display = 'none';
                    searchResults.style.display = 'none';
                }
            });

            async function abrirModalProduto(produto) {
                try {
                    // Buscar detalhes completos do produto
                    const response = await fetch(`api/api_produtos.php?acao=buscar_produto_por_id&id=${produto.id}`);
                    const resultado = await response.json();
                    
                    if (!resultado.sucesso) {
                        alert('Erro ao carregar detalhes do produto.');
                        return;
                    }
                    
                    const produtoCompleto = resultado.dados;
                    produtoSelecionado = produtoCompleto;
                    
                    document.getElementById('modal-produto-nome').textContent = produtoCompleto.nome;
                    document.getElementById('produto-id').value = produtoCompleto.id;
                    document.getElementById('produto-preco').value = parseFloat(produtoCompleto.preco_venda).toFixed(2);
                    document.getElementById('produto-qty').value = 1;
                    document.getElementById('produto-qty').max = produtoCompleto.estoque_atual;
                    
                    // Armazenar preço original e resetar estado do botão editar
                    precoOriginal = parseFloat(produtoCompleto.preco_venda);
                    produtoPreco.readOnly = true;
                    btnEditarPreco.innerHTML = '<i class="fa-solid fa-edit"></i> Editar';
                    btnEditarPreco.style.backgroundColor = '';
                    btnEditarPreco.onclick = () => {
                        precoOriginal = parseFloat(produtoPreco.value);
                        senhaAdminModal.style.display = 'flex';
                        document.getElementById('senha-admin').value = '';
                        document.getElementById('senha-admin').focus();
                    };
                    
                    // Exibir imagem do produto se disponível
                    const imagemProduto = document.getElementById('produto-imagem');
                    if (produtoCompleto.imagem_url) {
                        imagemProduto.src = produtoCompleto.imagem_url;
                        imagemProduto.style.display = 'block';
                    } else {
                        imagemProduto.style.display = 'none';
                    }
                    
                    // Verificar se tem variações de tamanho
                    const tamanhoGroup = document.getElementById('tamanho-group');
                    const tamanhoSelect = document.getElementById('produto-tamanho');
                    
                    if ((produtoCompleto.tipo === 'ROUPA' || produtoCompleto.tipo === 'CALCADO') && produtoCompleto.variacoes && produtoCompleto.variacoes.length > 0) {
                        tamanhoGroup.style.display = 'block';
                        tamanhoSelect.innerHTML = '<option value="">Selecione...</option>';
                        produtoCompleto.variacoes.forEach(v => {
                            if (v.quantidade > 0) {
                                const option = new Option(`${v.tamanho} (${v.quantidade} em estoque)`, v.tamanho);
                                tamanhoSelect.appendChild(option);
                            }
                        });
                    } else {
                        tamanhoGroup.style.display = 'none';
                    }
                    
                    produtoModal.style.display = 'flex';
                    searchProduto.value = '';
                    searchResults.style.display = 'none';
                } catch (error) {
                    alert('Erro ao carregar produto: ' + error.message);
                }
            }

            function adicionarItemAoCarrinho(item) {
                const itemExistente = carrinho.find(i => i.produto_id == item.produto_id && i.tamanho == item.tamanho);
                if (itemExistente) {
                    itemExistente.quantidade += item.quantidade;
                } else {
                    carrinho.push(item);
                }
                renderizarCarrinho();
            }

            function removerItemDoCarrinho(index) {
                carrinho.splice(index, 1);
                renderizarCarrinho();
            }

            function renderizarCarrinho() {
                if (carrinho.length === 0) {
                    carrinhoItens.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 2rem;">Carrinho vazio</p>';
                    valorTotal.textContent = 'R$ 0,00';
                    return;
                }
                
                let total = 0;
                carrinhoItens.innerHTML = '';
                
                carrinho.forEach((item, index) => {
                    const subtotal = item.quantidade * item.preco_venda;
                    total += subtotal;
                    
                    const div = document.createElement('div');
                    div.className = 'carrinho-item';
                    div.innerHTML = `
                        <div class="item-info">
                            <div class="item-nome">${item.nome}</div>
                            <div class="item-detalhes">${item.tamanho || ''} - R$ ${item.preco_venda.toFixed(2).replace('.', ',')}</div>
                        </div>
                        <div class="qty-controls">
                            <button class="qty-btn" onclick="alterarQuantidade(${index}, -1)">-</button>
                            <input type="number" class="qty-input" value="${item.quantidade}" min="1" onchange="alterarQuantidadeInput(${index}, this.value)">
                            <button class="qty-btn" onclick="alterarQuantidade(${index}, 1)">+</button>
                            <button class="qty-btn" onclick="removerItemDoCarrinho(${index})" style="margin-left: 0.5rem; color: red;">&times;</button>
                        </div>
                    `;
                    carrinhoItens.appendChild(div);
                });
                
                valorTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            }
            
            window.alterarQuantidade = function(index, delta) {
                carrinho[index].quantidade += delta;
                if (carrinho[index].quantidade <= 0) {
                    removerItemDoCarrinho(index);
                } else {
                    renderizarCarrinho();
                }
            }
            
            window.alterarQuantidadeInput = function(index, novaQuantidade) {
                const qty = parseInt(novaQuantidade);
                if (qty > 0) {
                    carrinho[index].quantidade = qty;
                    renderizarCarrinho();
                }
            }



            async function executarFinalizacaoVenda() {
                // Se for crediário, verificar se cliente está selecionado
                if (metodoPagamento.value === 'CREDIARIO') {
                    if (!clienteSelecionado) {
                        alert('Para vendas no crediário é obrigatório selecionar um cliente.');
                        document.getElementById('search-cliente').focus();
                        return;
                    }
                    abrirModalCrediario();
                    return;
                }
                
                btnFinalizar.disabled = true;
                btnFinalizar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Finalizando...';
                
                // Obter campos fiscais (opcionais)
                const cfop = document.getElementById('cfop-venda').value;
                const naturezaOperacao = document.getElementById('natureza-operacao').value;
                
                const venda = {
                    cliente_id: clienteSelecionado ? clienteSelecionado.id : null,
                    metodo_pagamento: metodoPagamento.value,
                    cfop: cfop,
                    natureza_operacao: naturezaOperacao,
                    itens: carrinho
                };
                
                try {
                    const response = await fetch('api/api_vendas.php?acao=finalizar_venda', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(venda)
                    });
                    const resultado = await response.json();
                    
                    if (resultado.sucesso) {
                        alert('Venda finalizada com sucesso!');
                        window.open(`imprimir_venda.php?id=${resultado.venda_id}`, '_blank');
                        window.location.href = 'dashboard.php';
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    alert('Erro ao finalizar venda: ' + error.message);
                } finally {
                    btnFinalizar.disabled = false;
                    btnFinalizar.innerHTML = '<i class="fa-solid fa-check"></i> Finalizar Venda';
                }
            }
            
            function abrirModalCliente(nomeInicial = '') {
                // Limpar todos os campos
                document.getElementById('cliente-nome').value = nomeInicial;
                document.getElementById('cliente-telefone').value = '';
                document.getElementById('cliente-email').value = '';
                document.getElementById('cliente-tipo-pessoa').value = 'fisica';
                document.getElementById('cliente-documento').value = '';
                document.getElementById('cliente-nascimento').value = '';
                document.getElementById('cliente-cep').value = '';
                document.getElementById('cliente-logradouro').value = '';
                document.getElementById('cliente-numero').value = '';
                document.getElementById('cliente-complemento').value = '';
                document.getElementById('cliente-bairro').value = '';
                document.getElementById('cliente-cidade').value = '';
                document.getElementById('cliente-estado').value = '';
                document.getElementById('cliente-inscricao-estadual').value = '';
                document.getElementById('cliente-inscricao-municipal').value = '';
                
                // Ocultar campos de pessoa jurídica por padrão
                document.getElementById('campos-pessoa-juridica').style.display = 'none';
                
                clienteModal.style.display = 'flex';
                searchResultsCliente.style.display = 'none';
                searchCliente.value = '';
            }
            
            function abrirModalCrediario() {
                // Preencher dados do cliente se já selecionado
                if (clienteSelecionado) {
                    crediarioNome.value = clienteSelecionado.nome || '';
                    crediarioTelefone.value = clienteSelecionado.telefone || '';
                    crediarioCpf.value = clienteSelecionado.documento || '';
                    // Construir endereço completo a partir dos campos padronizados
                    let enderecoCompleto = '';
                    if (clienteSelecionado.logradouro) {
                        enderecoCompleto += clienteSelecionado.logradouro;
                        if (clienteSelecionado.numero) enderecoCompleto += ', ' + clienteSelecionado.numero;
                        if (clienteSelecionado.complemento) enderecoCompleto += ', ' + clienteSelecionado.complemento;
                        if (clienteSelecionado.bairro) enderecoCompleto += ' - ' + clienteSelecionado.bairro;
                        if (clienteSelecionado.cidade) enderecoCompleto += ', ' + clienteSelecionado.cidade;
                        if (clienteSelecionado.estado) enderecoCompleto += '/' + clienteSelecionado.estado;
                    }
                    crediarioEndereco.value = enderecoCompleto;
                    crediarioCep.value = clienteSelecionado.cep || '';
                    crediarioCidade.value = clienteSelecionado.cidade || '';
                    crediarioEstado.value = clienteSelecionado.estado || '';
                    crediarioNumero.value = clienteSelecionado.numero || '';
                    crediarioComplemento.value = clienteSelecionado.complemento || '';
                    crediarioBairro.value = clienteSelecionado.bairro || '';
                } else {
                    crediarioNome.value = '';
                    crediarioTelefone.value = '';
                    crediarioCpf.value = '';
                    crediarioEndereco.value = '';
                    crediarioCep.value = '';
                    crediarioCidade.value = '';
                    crediarioEstado.value = '';
                    crediarioNumero.value = '';
                    crediarioComplemento.value = '';
                    crediarioBairro.value = '';
                }
                
                crediarioModal.style.display = 'flex';
                crediarioNome.focus();
            }
            
            async function finalizarVendaCrediario(dadosCliente) {
                const btnSubmit = crediarioForm.querySelector('button[type="submit"]');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Finalizando...';
                
                // Obter campos fiscais (opcionais)
                const cfop = document.getElementById('cfop-venda').value;
                const naturezaOperacao = document.getElementById('natureza-operacao').value;
                
                const venda = {
                    cliente_id: clienteSelecionado ? clienteSelecionado.id : null,
                    metodo_pagamento: 'CREDIARIO',
                    cfop: cfop,
                    natureza_operacao: naturezaOperacao,
                    itens: carrinho,
                    dados_cliente: dadosCliente
                };
                
                try {
                    const response = await fetch('api/api_vendas.php?acao=finalizar_venda', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(venda)
                    });
                    const resultado = await response.json();
                    
                    if (resultado.sucesso) {
                        alert('Venda no crediário finalizada com sucesso!');
                        window.open(`imprimir_venda_crediario.php?id=${resultado.venda_id}`, '_blank');
                        window.location.href = 'dashboard.php';
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    alert('Erro ao finalizar venda: ' + error.message);
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = 'Confirmar e Finalizar';
                }
            }

            // Form do produto
            document.getElementById('produto-form').addEventListener('submit', e => {
                e.preventDefault();
                
                const tamanhoGroup = document.getElementById('tamanho-group');
                const tamanhoSelect = document.getElementById('produto-tamanho');
                
                if (tamanhoGroup.style.display !== 'none' && !tamanhoSelect.value) {
                    alert('Por favor, selecione um tamanho.');
                    return;
                }
                
                const item = {
                    produto_id: document.getElementById('produto-id').value,
                    nome: produtoSelecionado.nome,
                    preco_venda: parseFloat(document.getElementById('produto-preco').value),
                    tamanho: tamanhoSelect.value || null,
                    quantidade: parseInt(document.getElementById('produto-qty').value)
                };
                
                adicionarItemAoCarrinho(item);
                produtoModal.style.display = 'none';
            });

            // Form do cliente
            document.getElementById('cliente-form').addEventListener('submit', async e => {
                e.preventDefault();
                const btn = e.target.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
                
                const dadosCliente = {
                    nome: document.getElementById('cliente-nome').value,
                    telefone: document.getElementById('cliente-telefone').value
                };
                
                try {
                    const response = await fetch('api/api_clientes.php?acao=salvar_cliente', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(dadosCliente)
                    });
                    const resultado = await response.json();
                    
                    if (resultado.sucesso) {
                        selecionarCliente(resultado.dados);
                        clienteModal.style.display = 'none';
                        alert('Cliente salvo com sucesso!');
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    alert('Erro ao salvar cliente: ' + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Salvar Cliente';
                }
            });

            btnFinalizar.addEventListener('click', () => {
                if (carrinho.length === 0) {
                    alert("O carrinho está vazio.");
                    return;
                }
                if (metodoPagamento.value === "") {
                    alert("Por favor, selecione um método de pagamento.");
                    metodoPagamento.focus();
                    return;
                }
                executarFinalizacaoVenda();
            });

            // Botão editar preço
            btnEditarPreco.addEventListener('click', () => {
                precoOriginal = parseFloat(produtoPreco.value);
                senhaAdminModal.style.display = 'flex';
                document.getElementById('senha-admin').value = '';
                document.getElementById('senha-admin').focus();
            });
            
            // Formatação automática do CPF
            crediarioCpf.addEventListener('input', function() {
                this.value = formatarCPF(this.value);
            });
            
            // Form do crediário
            crediarioForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const nome = crediarioNome.value.trim();
                const cpf = crediarioCpf.value.trim();
                const telefone = crediarioTelefone.value.trim();
                const endereco = crediarioEndereco.value.trim();
                
                // Validações
                if (!nome) {
                    alert('Nome é obrigatório para vendas no crediário.');
                    crediarioNome.focus();
                    return;
                }
                
                if (!cpf) {
                    alert('CPF é obrigatório para vendas no crediário.');
                    crediarioCpf.focus();
                    return;
                }
                
                if (!validarCPF(cpf)) {
                    alert('CPF inválido. Por favor, digite um CPF válido.');
                    crediarioCpf.focus();
                    return;
                }
                
                const dadosCliente = {
                    nome: nome,
                    cpf: cpf,
                    telefone: telefone,
                    endereco: endereco
                };
                
                crediarioModal.style.display = 'none';
                finalizarVendaCrediario(dadosCliente);
            });

            // Form de senha de administrador
            document.getElementById('senha-admin-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const senha = document.getElementById('senha-admin').value;
                const btn = e.target.querySelector('button[type="submit"]');
                
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verificando...';
                
                try {
                    const response = await fetch('api/api_auth.php?acao=verificar_senha_admin', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ senha: senha })
                    });
                    const resultado = await response.json();
                    
                    if (resultado.sucesso) {
                        // Senha correta - habilitar edição
                        produtoPreco.readOnly = false;
                        produtoPreco.focus();
                        produtoPreco.select();
                        btnEditarPreco.innerHTML = '<i class="fa-solid fa-save"></i> Salvar';
                        btnEditarPreco.style.backgroundColor = '#28a745';
                        btnEditarPreco.onclick = salvarPrecoEditado;
                        senhaAdminModal.style.display = 'none';
                    } else {
                        alert('Senha incorreta!');
                        document.getElementById('senha-admin').focus();
                    }
                } catch (error) {
                    alert('Erro ao verificar senha: ' + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Confirmar';
                }
            });

            function salvarPrecoEditado() {
                const novoPreco = parseFloat(produtoPreco.value);
                if (isNaN(novoPreco) || novoPreco <= 0) {
                    alert('Por favor, digite um preço válido.');
                    produtoPreco.focus();
                    return;
                }
                
                // Confirmar alteração
                const precoFormatado = novoPreco.toFixed(2).replace('.', ',');
                const precoOriginalFormatado = precoOriginal.toFixed(2).replace('.', ',');
                
                if (confirm(`Confirma a alteração do preço de R$ ${precoOriginalFormatado} para R$ ${precoFormatado}?`)) {
                    // Salvar alteração
                    produtoPreco.readOnly = true;
                    btnEditarPreco.innerHTML = '<i class="fa-solid fa-edit"></i> Editar';
                    btnEditarPreco.style.backgroundColor = '';
                    btnEditarPreco.onclick = () => {
                        precoOriginal = parseFloat(produtoPreco.value);
                        senhaAdminModal.style.display = 'flex';
                        document.getElementById('senha-admin').value = '';
                        document.getElementById('senha-admin').focus();
                    };
                    alert('Preço alterado com sucesso!');
                } else {
                    // Cancelar - voltar ao preço original
                    produtoPreco.value = precoOriginal.toFixed(2);
                    produtoPreco.readOnly = true;
                    btnEditarPreco.innerHTML = '<i class="fa-solid fa-edit"></i> Editar';
                    btnEditarPreco.style.backgroundColor = '';
                    btnEditarPreco.onclick = () => {
                        precoOriginal = parseFloat(produtoPreco.value);
                        senhaAdminModal.style.display = 'flex';
                        document.getElementById('senha-admin').value = '';
                        document.getElementById('senha-admin').focus();
                    };
                }
            }

            // Evento para mostrar/ocultar campos de pessoa jurídica
            document.getElementById('cliente-tipo-pessoa').addEventListener('change', function() {
                const camposPJ = document.getElementById('campos-pessoa-juridica');
                if (this.value === 'juridica') {
                    camposPJ.style.display = 'block';
                } else {
                    camposPJ.style.display = 'none';
                }
            });
            
            // Form de cadastro de cliente
            document.getElementById('cliente-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
                
                const dadosCliente = {
                    nome: document.getElementById('cliente-nome').value.trim(),
                    telefone: document.getElementById('cliente-telefone').value.trim(),
                    email: document.getElementById('cliente-email').value.trim(),
                    tipo_pessoa: document.getElementById('cliente-tipo-pessoa').value,
                    documento: document.getElementById('cliente-documento').value.trim(),
                    data_nascimento: document.getElementById('cliente-nascimento').value,
                    cep: document.getElementById('cliente-cep').value.trim(),
                    logradouro: document.getElementById('cliente-logradouro').value.trim(),
                    numero: document.getElementById('cliente-numero').value.trim(),
                    complemento: document.getElementById('cliente-complemento').value.trim(),
                    bairro: document.getElementById('cliente-bairro').value.trim(),
                    cidade: document.getElementById('cliente-cidade').value.trim(),
                    estado: document.getElementById('cliente-estado').value,
                    inscricao_estadual: document.getElementById('cliente-inscricao-estadual').value.trim(),
                    inscricao_municipal: document.getElementById('cliente-inscricao-municipal').value.trim()
                };
                
                // Validações básicas
                if (!dadosCliente.nome) {
                    alert('Nome é obrigatório.');
                    document.getElementById('cliente-nome').focus();
                    btn.disabled = false;
                    btn.innerHTML = 'Salvar Cliente';
                    return;
                }
                
                try {
                    const response = await fetch('api/api_clientes.php?acao=salvar_cliente', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(dadosCliente)
                    });
                    const resultado = await response.json();
                    
                    if (resultado.sucesso) {
                        alert('Cliente cadastrado com sucesso!');
                        clienteModal.style.display = 'none';
                        
                        // Selecionar o cliente recém-cadastrado
                        const novoCliente = resultado.dados;
                        await selecionarCliente(novoCliente);
                    } else {
                        alert('Erro ao cadastrar cliente: ' + (resultado.erro || 'Erro desconhecido'));
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao cadastrar cliente. Tente novamente.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Salvar Cliente';
                }
            });
            
            // Fechar modais
            document.querySelectorAll('.modal-close').forEach(closeBtn => {
                closeBtn.onclick = () => {
                    const modal = closeBtn.closest('.modal-backdrop');
                    modal.style.display = 'none';
                    
                    // Se estiver fechando o modal de produto e o preço estiver sendo editado, cancelar edição
                    if (modal.id === 'produto-modal' && !produtoPreco.readOnly) {
                        produtoPreco.value = precoOriginal.toFixed(2);
                        produtoPreco.readOnly = true;
                        btnEditarPreco.innerHTML = '<i class="fa-solid fa-edit"></i> Editar';
                        btnEditarPreco.style.backgroundColor = '';
                        btnEditarPreco.onclick = () => {
                            precoOriginal = parseFloat(produtoPreco.value);
                            senhaAdminModal.style.display = 'flex';
                            document.getElementById('senha-admin').value = '';
                            document.getElementById('senha-admin').focus();
                        };
                    }
                    
                    // Limpar campos do modal de crediário ao fechar
                    if (modal.id === 'crediario-modal') {
                        crediarioForm.reset();
                    }
                };
            });
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
    
    <!-- Sistema de Consulta de CEP -->
    <script src="assets/js/cep-consulta.js"></script>
    
    <script>
        // Função para expandir/colapsar seção fiscal
        function toggleFiscalSection() {
            const content = document.getElementById('fiscal-content');
            const arrow = document.getElementById('fiscal-arrow');
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                content.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</body>

</html>