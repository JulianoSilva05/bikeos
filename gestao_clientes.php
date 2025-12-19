<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php 
        require_once 'helpers/empresa_helper.php';
        echo getTituloComEmpresa('Gestão de Clientes e Fornecedores');
    ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .form-grid-half {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .status-ATIVA,
        .status-ATIVO {
            background-color: var(--cor-sucesso);
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8em;
        }

        .status-INATIVO {
            background-color: var(--cor-erro);
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8em;
        }

        .link-fornecedor-container {
            display: none;
            margin-top: 1rem;
        }

        .input-with-copy-btn {
            display: flex;
            gap: 0.5rem;
        }

        .input-with-copy-btn .form-input {
            flex-grow: 1;
        }

        /* Garante que o input ocupe o espaço */

        .alphabet-navigation {
            margin: 1rem 0;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: var(--raio-borda);
            border: 1px solid #dee2e6;
        }

        .alphabet-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
        }

        .alphabet-btn {
            padding: 0.5rem 0.8rem;
            border: 1px solid #dee2e6;
            background-color: white;
            color: #495057;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            min-width: 40px;
        }

        .alphabet-btn:hover {
            background-color: var(--cor-primaria);
            color: white;
            border-color: var(--cor-primaria);
            transform: translateY(-1px);
        }

        .alphabet-btn.active {
            background-color: var(--cor-primaria);
            color: white;
            border-color: var(--cor-primaria);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .alphabet-btn.disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .alphabet-btn.disabled:hover {
            background-color: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
            transform: none;
        }

        @media (max-width: 768px) {
            .alphabet-buttons {
                gap: 0.3rem;
            }
            
            .alphabet-btn {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
                min-width: 35px;
            }
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
            <button onclick="history.back()" class="btn btn-secundario" title="Voltar à página anterior"><i class="fa-solid fa-arrow-left"></i> Voltar</button>
            <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Ir para o Painel Principal"><i class="fa-solid fa-house"></i></a>
        </div>
        
        <div class="page-header">
            <h1><i class="fa-solid fa-users"></i> Gestão de Clientes</h1>
            <div class="header-actions">
                <button id="btn-fornecedores" class="btn btn-secundario"><i class="fa-solid fa-truck"></i> Fornecedores</button>
                <button id="btn-novo-cliente" class="btn btn-destaque"><i class="fa-solid fa-plus"></i> Novo Cliente</button>
            </div>
        </div>

        <div class="card">
            <div class="form-group">
                <input type="search" id="search-input" class="form-input" placeholder="Buscar cliente por nome, telefone ou email...">
            </div>
            
            <!-- Navegação Alfabética -->
            <div class="alphabet-navigation">
                <div class="alphabet-buttons">
                    <button class="alphabet-btn active" data-letter="all">Todos</button>
                    <button class="alphabet-btn" data-letter="A">A</button>
                    <button class="alphabet-btn" data-letter="B">B</button>
                    <button class="alphabet-btn" data-letter="C">C</button>
                    <button class="alphabet-btn" data-letter="D">D</button>
                    <button class="alphabet-btn" data-letter="E">E</button>
                    <button class="alphabet-btn" data-letter="F">F</button>
                    <button class="alphabet-btn" data-letter="G">G</button>
                    <button class="alphabet-btn" data-letter="H">H</button>
                    <button class="alphabet-btn" data-letter="I">I</button>
                    <button class="alphabet-btn" data-letter="J">J</button>
                    <button class="alphabet-btn" data-letter="K">K</button>
                    <button class="alphabet-btn" data-letter="L">L</button>
                    <button class="alphabet-btn" data-letter="M">M</button>
                    <button class="alphabet-btn" data-letter="N">N</button>
                    <button class="alphabet-btn" data-letter="O">O</button>
                    <button class="alphabet-btn" data-letter="P">P</button>
                    <button class="alphabet-btn" data-letter="Q">Q</button>
                    <button class="alphabet-btn" data-letter="R">R</button>
                    <button class="alphabet-btn" data-letter="S">S</button>
                    <button class="alphabet-btn" data-letter="T">T</button>
                    <button class="alphabet-btn" data-letter="U">U</button>
                    <button class="alphabet-btn" data-letter="V">V</button>
                    <button class="alphabet-btn" data-letter="W">W</button>
                    <button class="alphabet-btn" data-letter="X">X</button>
                    <button class="alphabet-btn" data-letter="Y">Y</button>
                    <button class="alphabet-btn" data-letter="Z">Z</button>
                </div>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF/CNPJ</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="clientes-table-body"></tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="cliente-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="modal-title">Novo Cliente</h2>
            <form id="cliente-form">
                <input type="hidden" id="cliente-id" name="id">
                <div class="form-group"><label for="cliente-nome" class="form-label">Nome Completo</label><input type="text" id="cliente-nome" name="nome" class="form-input" required></div>
                
                <!-- Campos Fiscais -->
                <div class="form-grid-half">
                    <div class="form-group">
                        <label for="cliente-tipo-pessoa" class="form-label">Tipo de Pessoa</label>
                        <select id="cliente-tipo-pessoa" name="tipo_pessoa" class="form-input" required>
                            <option value="">Selecione...</option>
                            <option value="fisica">Pessoa Física</option>
                            <option value="juridica">Pessoa Jurídica</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cliente-documento" class="form-label">CPF/CNPJ</label>
                        <input type="text" id="cliente-documento" name="documento" class="form-input" required>
                        <small class="form-help" id="documento-help">Digite apenas números</small>
                    </div>
                </div>
                
                <div class="form-grid-half">
                    <div class="form-group"><label for="cliente-telefone" class="form-label">Telefone</label><input type="tel" id="cliente-telefone" name="telefone" class="form-input"></div>
                    <div class="form-group"><label for="cliente-nascimento" class="form-label">Data de Nascimento</label><input type="date" id="cliente-nascimento" name="data_nascimento" class="form-input"></div>
                </div>
                
                <div class="form-group"><label for="cliente-email" class="form-label">Email</label><input type="email" id="cliente-email" name="email" class="form-input"></div>
                
                <!-- Campos adicionais para Pessoa Jurídica -->
                <div id="campos-pj" style="display: none;">
                    <div class="form-grid-half">
                        <div class="form-group">
                            <label for="cliente-inscricao-estadual" class="form-label">Inscrição Estadual</label>
                            <input type="text" id="cliente-inscricao-estadual" name="inscricao_estadual" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="cliente-inscricao-municipal" class="form-label">Inscrição Municipal</label>
                            <input type="text" id="cliente-inscricao-municipal" name="inscricao_municipal" class="form-input">
                        </div>
                    </div>
                </div>
                <!-- Campos de Endereço -->
                <div class="form-grid-half">
                    <div class="form-group">
                        <label for="cliente-cep" class="form-label">CEP</label>
                        <input type="text" id="cliente-cep" name="cep" class="form-input" placeholder="00000-000" data-cep="true">
                    </div>
                    <div class="form-group">
                        <label for="cliente-cidade" class="form-label">Cidade</label>
                        <input type="text" id="cliente-cidade" name="cidade" class="form-input" data-cep-localidade="true">
                    </div>
                </div>
                <div class="form-grid-half">
                    <div class="form-group">
                        <label for="cliente-estado" class="form-label">Estado</label>
                        <select id="cliente-estado" name="estado" class="form-input" data-cep-uf="true">
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
                        <label for="cliente-bairro" class="form-label">Bairro</label>
                        <input type="text" id="cliente-bairro" name="bairro" class="form-input" data-cep-bairro="true">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cliente-logradouro" class="form-label">Endereço (Rua/Avenida)</label>
                    <input type="text" id="cliente-logradouro" name="logradouro" class="form-input" placeholder="Rua, Avenida, etc." data-cep-logradouro="true">
                </div>
                <div class="form-group">
                    <label for="cliente-numero" class="form-label">Número</label>
                    <input type="text" id="cliente-numero" name="numero" class="form-input" placeholder="Número da residência">
                </div>
                <div class="form-group">
                    <label for="cliente-complemento" class="form-label">Complemento</label>
                    <input type="text" id="cliente-complemento" name="complemento" class="form-input" placeholder="Apartamento, bloco, etc." data-cep-complemento="true">
                </div>
                <button type="submit" class="btn btn-destaque" style="width: 100%;">Salvar Cliente</button>
            </form>
        </div>
    </div>

    <div id="fornecedores-modal" class="modal-backdrop">
        <div class="modal-content modal-content-large">
            <span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-truck"></i> Fornecedores</h2>
            <button id="btn-novo-fornecedor" class="btn btn-destaque" style="margin-bottom: 1rem;"><i class="fa-solid fa-plus"></i> Novo Fornecedor</button>
            <div style="max-height: 50vh; overflow-y: auto;">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Contato</th>
                            <th>Telefone</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="fornecedores-table-body"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="fornecedor-edit-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="fornecedor-modal-title"><i class="fa-solid fa-truck"></i> Novo Fornecedor</h2>
            <form id="fornecedor-form">
                <input type="hidden" id="fornecedor-id" name="id">
                <div class="form-grid-half">
                    <div class="form-group"><label for="fornecedor-nome" class="form-label">Nome da Empresa</label><input type="text" id="fornecedor-nome" name="nome_empresa" class="form-input" required></div>
                    <div class="form-group"><label for="fornecedor-cnpj" class="form-label">CNPJ</label><input type="text" id="fornecedor-cnpj" name="cnpj" class="form-input"></div>
                </div>
                <div class="form-grid-half">
                    <div class="form-group"><label for="fornecedor-contato" class="form-label">Nome do Contato</label><input type="text" id="fornecedor-contato" name="contato_nome" class="form-input"></div>
                    <div class="form-group"><label for="fornecedor-telefone" class="form-label">Telefone</label><input type="tel" id="fornecedor-telefone" name="telefone" class="form-input"></div>
                </div>
                <div class="form-group"><label for="fornecedor-email" class="form-label">Email</label><input type="email" id="fornecedor-email" name="email" class="form-input"></div>
                <div class="form-group"><label for="fornecedor-cep" class="form-label">CEP</label><input type="text" id="fornecedor-cep" name="cep" class="form-input" placeholder="00000-000" data-cep="true"></div>
                <div class="form-group"><label for="fornecedor-logradouro" class="form-label">Logradouro</label><input type="text" id="fornecedor-logradouro" name="logradouro" class="form-input" placeholder="Rua, Avenida, etc." data-cep-logradouro="true"></div>
                <div class="form-group"><label for="fornecedor-numero" class="form-label">Número</label><input type="text" id="fornecedor-numero" name="numero" class="form-input" placeholder="Número"></div>
                <div class="form-group"><label for="fornecedor-complemento" class="form-label">Complemento</label><input type="text" id="fornecedor-complemento" name="complemento" class="form-input" placeholder="Apartamento, bloco, etc."></div>
                <div class="form-group"><label for="fornecedor-bairro" class="form-label">Bairro</label><input type="text" id="fornecedor-bairro" name="bairro" class="form-input" placeholder="Bairro" data-cep-bairro="true"></div>
                <div class="form-group"><label for="fornecedor-cidade" class="form-label">Cidade</label><input type="text" id="fornecedor-cidade" name="cidade" class="form-input" placeholder="Cidade" data-cep-localidade="true"></div>
                <div class="form-group"><label for="fornecedor-estado" class="form-label">Estado</label><input type="text" id="fornecedor-estado" name="estado" class="form-input" placeholder="Estado" data-cep-uf="true"></div>
                <div class="form-group"><label for="fornecedor-observacoes" class="form-label">Observações</label><textarea id="fornecedor-observacoes" name="observacoes" class="form-input" rows="2"></textarea></div>
                <div class="form-group"><label for="fornecedor-status" class="form-label">Status</label><select id="fornecedor-status" name="status" class="form-input">
                        <option value="ATIVO">Ativo</option>
                        <option value="INATIVO">Inativo</option>
                    </select></div>

                <div id="link-fornecedor-container" class="link-fornecedor-container">
                    <label class="form-label">Link Exclusivo de Acesso ao Estoque</label>
                    <div class="input-with-copy-btn">
                        <input type="text" id="link-fornecedor" class="form-input" readonly placeholder="Gere um token para criar o link...">
                        <button type="button" id="btn-gerar-token" class="btn btn-secundario btn-icon-only" title="Gerar Novo Token (invalida o link antigo)"><i class="fa-solid fa-key"></i></button>
                        <button type="button" id="btn-copiar-link" class="btn btn-secundario btn-icon-only" title="Copiar Link"><i class="fa-solid fa-copy"></i></button>
                    </div>
                </div>

                <button type="submit" class="btn btn-destaque" style="width: 100%; margin-top: 1rem;">Salvar</button>
            </form>
        </div>
    </div>

    <?php include '_partials/menu_admin.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.getElementById('clientes-table-body');
            const searchInput = document.getElementById('search-input');
            const modal = document.getElementById('cliente-modal');
            const modalTitle = document.getElementById('modal-title');
            const clienteForm = document.getElementById('cliente-form');
            const btnNovoCliente = document.getElementById('btn-novo-cliente');
            let debounceTimer;

            const btnFornecedores = document.getElementById('btn-fornecedores');
            const fornecedoresModal = document.getElementById('fornecedores-modal');
            const fornecedorEditModal = document.getElementById('fornecedor-edit-modal');
            const fornecedorForm = document.getElementById('fornecedor-form');
            const fornecedorModalTitle = document.getElementById('fornecedor-modal-title');
            let fornecedoresData = [];

            let todosClientes = [];
            let letraAtiva = 'all';

            const carregarClientes = async (termo = '') => {
                try {
                    const response = await fetch(`api/api_clientes.php?acao=listar_clientes&termo=${termo}`);
                    const resultado = await response.json();
                    tableBody.innerHTML = '';
                    if (resultado.sucesso) {
                        if (resultado.dados.length === 0) {
                            tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Nenhum cliente encontrado.</td></tr>';
                            return;
                        }
                        
                        // Ordena os clientes alfabeticamente por nome
                        todosClientes = resultado.dados.sort((a, b) => {
                            return a.nome.localeCompare(b.nome, 'pt-BR', { sensitivity: 'base' });
                        });
                        
                        // Atualiza a navegação alfabética
                        await atualizarNavegacaoAlfabetica();
                        
                        // Exibe os clientes
                        exibirClientes(todosClientes);
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="5" class="error-message">Erro: ' + (resultado.mensagem || 'Erro desconhecido') + '</td></tr>';
                    }
                } catch (error) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="error-message">Erro ao carregar clientes.</td></tr>';
                }
            };

            const exibirClientes = (clientes) => {
                tableBody.innerHTML = '';
                clientes.forEach(c => {
                    const tr = document.createElement('tr');
                    const tipoDoc = c.tipo_pessoa === 'juridica' ? 'CNPJ' : 'CPF';
                    const documento = c.documento ? formatarDocumento(c.documento, c.tipo_pessoa) : '-';
                    
                    tr.innerHTML = `
                        <td>${c.id}</td>
                        <td>${c.nome}</td>
                        <td>${tipoDoc}: ${documento}</td>
                        <td>${c.telefone || '-'}</td>
                        <td>${c.email || '-'}</td>
                        <td>
                            <a href="historico_cliente.php?id=${c.id}" class="btn btn-sm btn-secundario" title="Histórico"><i class="fa-solid fa-history"></i></a>
                            <button class="btn btn-sm btn-primario btn-editar" data-id="${c.id}" title="Editar"><i class="fa-solid fa-pencil"></i></button>
                            <button class="btn btn-sm btn-erro btn-excluir" data-id="${c.id}" title="Excluir"><i class="fa-solid fa-trash-can"></i></button>
                        </td>`;
                    tableBody.appendChild(tr);
                });
            };
            
            // Função para formatar documento (CPF/CNPJ)
            const formatarDocumento = (documento, tipo) => {
                if (!documento) return '';
                
                if (tipo === 'fisica') {
                    // Formato CPF: 000.000.000-00
                    return documento.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                } else {
                    // Formato CNPJ: 00.000.000/0000-00
                    return documento.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
                }
            };

            const filtrarPorLetra = (letra) => {
                letraAtiva = letra;
                
                // Remove classe active de todos os botões
                document.querySelectorAll('.alphabet-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Adiciona classe active ao botão clicado
                document.querySelector(`[data-letter="${letra}"]`).classList.add('active');
                
                // Se for "all", limpa o campo de busca e recarrega todos os clientes
                if (letra === 'all') {
                    searchInput.value = '';
                    carregarClientes('');
                    return;
                }
                
                // Para letras específicas, filtra os clientes já carregados
                let clientesFiltrados = todosClientes.filter(cliente => {
                    return cliente.nome.charAt(0).toUpperCase() === letra;
                });
                
                exibirClientes(clientesFiltrados);
            };

            const atualizarNavegacaoAlfabetica = async () => {
                try {
                    // Busca todos os clientes do banco para determinar quais letras têm clientes
                    const response = await fetch('api/api_clientes.php?acao=listar_clientes&termo=');
                    const resultado = await response.json();
                    
                    const letrasComClientes = new Set();
                    
                    if (resultado.sucesso && resultado.dados) {
                        resultado.dados.forEach(cliente => {
                            const primeiraLetra = cliente.nome.charAt(0).toUpperCase();
                            letrasComClientes.add(primeiraLetra);
                        });
                    }
                    
                    // Atualiza os botões - habilita/desabilita baseado na disponibilidade
                    document.querySelectorAll('.alphabet-btn').forEach(btn => {
                        const letra = btn.getAttribute('data-letter');
                        
                        if (letra === 'all') {
                            btn.classList.remove('disabled');
                        } else if (letrasComClientes.has(letra)) {
                            btn.classList.remove('disabled');
                        } else {
                            btn.classList.add('disabled');
                        }
                    });
                } catch (error) {
                    console.error('Erro ao atualizar navegação alfabética:', error);
                    // Em caso de erro, habilita todos os botões
                    document.querySelectorAll('.alphabet-btn').forEach(btn => {
                        btn.classList.remove('disabled');
                    });
                }
            };

            const abrirModalCliente = async (id = null) => {
                clienteForm.reset();
                document.getElementById('cliente-id').value = '';
                
                // Reset campos fiscais
                document.getElementById('cliente-tipo-pessoa').value = 'fisica';
                document.getElementById('cliente-documento').value = '';
                document.getElementById('cliente-inscricao-estadual').value = '';
                document.getElementById('cliente-inscricao-municipal').value = '';
                document.getElementById('campos-pj').style.display = 'none';
                
                if (id) {
                    modalTitle.textContent = `Editar Cliente #${id}`;
                    const response = await fetch(`api/api_clientes.php?acao=buscar_cliente_por_id&id=${id}`);
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        const c = resultado.dados;
                        document.getElementById('cliente-id').value = c.id;
                        document.getElementById('cliente-nome').value = c.nome;
                        document.getElementById('cliente-telefone').value = c.telefone;
                        document.getElementById('cliente-email').value = c.email;
                        document.getElementById('cliente-nascimento').value = c.data_nascimento;
                        
                        // Carregar campos de endereço padronizados
                        document.getElementById('cliente-cep').value = c.cep || '';
                        document.getElementById('cliente-logradouro').value = c.logradouro || '';
                        document.getElementById('cliente-numero').value = c.numero || '';
                        document.getElementById('cliente-complemento').value = c.complemento || '';
                        document.getElementById('cliente-bairro').value = c.bairro || '';
                        document.getElementById('cliente-cidade').value = c.cidade || '';
                        document.getElementById('cliente-estado').value = c.estado || '';
                        
                        // Carregar campos fiscais
                        if (c.tipo_pessoa) {
                            document.getElementById('cliente-tipo-pessoa').value = c.tipo_pessoa;
                            if (c.tipo_pessoa === 'juridica') {
                                document.getElementById('campos-pj').style.display = 'block';
                            }
                        }
                        if (c.documento) {
                            document.getElementById('cliente-documento').value = c.documento;
                        }
                        if (c.inscricao_estadual) {
                            document.getElementById('cliente-inscricao-estadual').value = c.inscricao_estadual;
                        }
                        if (c.inscricao_municipal) {
                            document.getElementById('cliente-inscricao-municipal').value = c.inscricao_municipal;
                        }
                    }
                } else {
                    modalTitle.textContent = 'Novo Cliente';
                }
                modal.style.display = 'flex';
            };

            const fecharModalCliente = () => modal.style.display = 'none';

            const abrirModalFornecedores = async () => {
                // ... (código inalterado)
                const tbody = document.getElementById('fornecedores-table-body');
                tbody.innerHTML = '<tr><td colspan="5">Carregando...</td></tr>';
                fornecedoresModal.style.display = 'flex';
                try {
                    const response = await fetch('api/api_compras.php?acao=listar_fornecedores');
                    const resultado = await response.json();
                    tbody.innerHTML = '';
                    if (resultado.sucesso) {
                        fornecedoresData = resultado.dados;
                        fornecedoresData.forEach(f => {
                            tbody.innerHTML += `<tr><td>${f.nome_empresa}</td><td>${f.contato_nome || '-'}</td><td>${f.telefone || '-'}</td><td><span class="status ${f.status === 'ATIVO' ? 'status-ATIVA' : 'status-INATIVO'}">${f.status}</span></td><td><button class="btn btn-sm btn-primario btn-editar-fornecedor" data-id="${f.id}"><i class="fa-solid fa-pencil"></i></button></td></tr>`;
                        });
                    }
                } catch (e) {
                    tbody.innerHTML = '<tr><td colspan="5" class="error-message">Erro ao carregar fornecedores.</td></tr>';
                }
            };

            const abrirModalEditFornecedor = (id = null) => {
                fornecedorForm.reset();
                document.getElementById('fornecedor-id').value = '';
                const linkContainer = document.getElementById('link-fornecedor-container');
                const linkInput = document.getElementById('link-fornecedor');
                linkInput.value = '';

                if (id) { // Editando um fornecedor existente
                    const fornecedor = fornecedoresData.find(f => f.id == id);
                    fornecedorModalTitle.innerHTML = `<i class="fa-solid fa-truck"></i> Editar Fornecedor`;
                    document.getElementById('fornecedor-id').value = fornecedor.id;
                    document.getElementById('fornecedor-nome').value = fornecedor.nome_empresa;
                    document.getElementById('fornecedor-cnpj').value = fornecedor.cnpj;
                    document.getElementById('fornecedor-contato').value = fornecedor.contato_nome;
                    document.getElementById('fornecedor-telefone').value = fornecedor.telefone;
                    document.getElementById('fornecedor-email').value = fornecedor.email;
                    document.getElementById('fornecedor-endereco').value = fornecedor.endereco;
                    document.getElementById('fornecedor-observacoes').value = fornecedor.observacoes;
                    document.getElementById('fornecedor-status').value = fornecedor.status;

                    linkContainer.style.display = 'block'; // Mostra sempre o container do link ao editar
                    if (fornecedor.token_acesso) {
                        const fullUrl = `${window.location.origin}/fornecedor_estoque.php?token=${fornecedor.token_acesso}`;
                        linkInput.value = fullUrl;
                    } else {
                        linkInput.placeholder = "Gere um token para criar o link...";
                    }

                } else { // Criando um novo fornecedor
                    fornecedorModalTitle.innerHTML = `<i class="fa-solid fa-truck"></i> Novo Fornecedor`;
                    linkContainer.style.display = 'none'; // Esconde o container para novos fornecedores
                }
                fornecedorEditModal.style.display = 'flex';
            };

            // Event Listeners
            searchInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => carregarClientes(e.target.value), 300);
            });
            btnNovoCliente.addEventListener('click', () => abrirModalCliente());
            modal.querySelector('.modal-close').onclick = fecharModalCliente;

            clienteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const dados = Object.fromEntries(new FormData(clienteForm).entries());
                const response = await fetch('api/api_clientes.php?acao=salvar_cliente', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados)
                });
                const resultado = await response.json();
                alert(resultado.mensagem);
                if (resultado.sucesso) {
                    fecharModalCliente();
                    carregarClientes(searchInput.value);
                }
            });

            tableBody.addEventListener('click', async (e) => {
                // ... (código inalterado)
                const target = e.target.closest('button');
                if (!target) return;
                const id = target.dataset.id;
                if (target.classList.contains('btn-editar')) {
                    abrirModalCliente(id);
                } else if (target.classList.contains('btn-excluir')) {
                    if (confirm(`Tem certeza que deseja excluir o cliente #${id}?`)) {
                        const response = await fetch('api/api_clientes.php?acao=excluir_cliente', {
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
                        if (resultado.sucesso) carregarClientes(searchInput.value);
                    }
                }
            });

            btnFornecedores.addEventListener('click', abrirModalFornecedores);
            fornecedoresModal.querySelector('.modal-close').onclick = () => fornecedoresModal.style.display = 'none';
            document.getElementById('btn-novo-fornecedor').addEventListener('click', () => abrirModalEditFornecedor());
            document.getElementById('fornecedores-table-body').addEventListener('click', e => {
                if (e.target.closest('.btn-editar-fornecedor')) {
                    abrirModalEditFornecedor(e.target.closest('.btn-editar-fornecedor').dataset.id);
                }
            });
            fornecedorEditModal.querySelector('.modal-close').onclick = () => fornecedorEditModal.style.display = 'none';

            document.getElementById('btn-copiar-link').addEventListener('click', () => {
                const linkInput = document.getElementById('link-fornecedor');
                if (!linkInput.value) {
                    alert('Nenhum link para copiar. Por favor, gere um token primeiro.');
                    return;
                }
                linkInput.select();
                document.execCommand('copy');
                alert('Link copiado para a área de transferência!');
            });

            // **NOVA LÓGICA PARA O BOTÃO DE GERAR TOKEN**
            document.getElementById('btn-gerar-token').addEventListener('click', async () => {
                const fornecedorId = document.getElementById('fornecedor-id').value;
                if (!fornecedorId) {
                    alert('Você precisa salvar o fornecedor antes de gerar um token.');
                    return;
                }

                if (!confirm('Gerar um novo token irá invalidar qualquer link de acesso anterior. Deseja continuar?')) {
                    return;
                }

                try {
                    const response = await fetch('api/api_compras.php?acao=gerar_token_fornecedor', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            fornecedor_id: fornecedorId
                        })
                    });
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);

                    const novoToken = resultado.novo_token;
                    const linkInput = document.getElementById('link-fornecedor');
                    linkInput.value = `${window.location.origin}/fornecedor_estoque.php?token=${novoToken}`;

                    // Atualiza o token nos dados locais para evitar recarregar a lista
                    const fornecedorIndex = fornecedoresData.findIndex(f => f.id == fornecedorId);
                    if (fornecedorIndex > -1) {
                        fornecedoresData[fornecedorIndex].token_acesso = novoToken;
                    }

                    alert(resultado.mensagem);

                } catch (error) {
                    alert('Erro ao gerar token: ' + error.message);
                }
            });

            fornecedorForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const dados = Object.fromEntries(new FormData(fornecedorForm).entries());
                const response = await fetch('api/api_compras.php?acao=salvar_fornecedor', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados)
                });
                const resultado = await response.json();
                alert(resultado.mensagem);
                if (resultado.sucesso) {
                    fornecedorEditModal.style.display = 'none';
                    abrirModalFornecedores(); // Recarrega a lista para obter o novo token, se for o caso
                }
            });

            // Event listeners para navegação alfabética
            document.querySelectorAll('.alphabet-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (!btn.classList.contains('disabled')) {
                        filtrarPorLetra(btn.getAttribute('data-letter'));
                    }
                });
            });

            // Validação de CPF/CNPJ e controle de campos
            const tipoSelect = document.getElementById('cliente-tipo-pessoa');
            const documentoInput = document.getElementById('cliente-documento');
            const camposPJ = document.getElementById('campos-pj');
            const documentoHelp = document.getElementById('documento-help');

            // Função para validar CPF
            function validarCPF(cpf) {
                cpf = cpf.replace(/[^\d]/g, '');
                if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
                
                let soma = 0;
                for (let i = 0; i < 9; i++) {
                    soma += parseInt(cpf.charAt(i)) * (10 - i);
                }
                let resto = 11 - (soma % 11);
                let digito1 = resto < 2 ? 0 : resto;
                
                soma = 0;
                for (let i = 0; i < 10; i++) {
                    soma += parseInt(cpf.charAt(i)) * (11 - i);
                }
                resto = 11 - (soma % 11);
                let digito2 = resto < 2 ? 0 : resto;
                
                return digito1 === parseInt(cpf.charAt(9)) && digito2 === parseInt(cpf.charAt(10));
            }

            // Função para validar CNPJ
            function validarCNPJ(cnpj) {
                cnpj = cnpj.replace(/[^\d]/g, '');
                if (cnpj.length !== 14) return false;
                
                const pesos1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
                const pesos2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
                
                let soma = 0;
                for (let i = 0; i < 12; i++) {
                    soma += parseInt(cnpj.charAt(i)) * pesos1[i];
                }
                let resto = soma % 11;
                let digito1 = resto < 2 ? 0 : 11 - resto;
                
                soma = 0;
                for (let i = 0; i < 13; i++) {
                    soma += parseInt(cnpj.charAt(i)) * pesos2[i];
                }
                resto = soma % 11;
                let digito2 = resto < 2 ? 0 : 11 - resto;
                
                return digito1 === parseInt(cnpj.charAt(12)) && digito2 === parseInt(cnpj.charAt(13));
            }

            // Controle de exibição de campos baseado no tipo de pessoa
            tipoSelect.addEventListener('change', function() {
                const tipo = this.value;
                documentoInput.value = '';
                
                if (tipo === 'fisica') {
                    documentoHelp.textContent = 'Digite o CPF (apenas números)';
                    documentoInput.placeholder = 'CPF';
                    camposPJ.style.display = 'none';
                    // Remove required dos campos PJ
                    document.getElementById('cliente-inscricao-estadual').required = false;
                    document.getElementById('cliente-inscricao-municipal').required = false;
                } else if (tipo === 'juridica') {
                    documentoHelp.textContent = 'Digite o CNPJ (apenas números)';
                    documentoInput.placeholder = 'CNPJ';
                    camposPJ.style.display = 'block';
                } else {
                    documentoHelp.textContent = 'Digite apenas números';
                    documentoInput.placeholder = 'CPF/CNPJ';
                    camposPJ.style.display = 'none';
                }
            });

            // Validação em tempo real do documento
            documentoInput.addEventListener('blur', function() {
                const tipo = tipoSelect.value;
                const documento = this.value.replace(/[^\d]/g, '');
                
                if (!documento) return;
                
                let valido = false;
                if (tipo === 'fisica') {
                    valido = validarCPF(documento);
                } else if (tipo === 'juridica') {
                    valido = validarCNPJ(documento);
                }
                
                if (!valido && documento.length > 0) {
                    this.style.borderColor = '#dc3545';
                    documentoHelp.style.color = '#dc3545';
                    documentoHelp.textContent = tipo === 'fisica' ? 'CPF inválido' : 'CNPJ inválido';
                } else {
                    this.style.borderColor = '';
                    documentoHelp.style.color = '';
                    documentoHelp.textContent = tipo === 'fisica' ? 'Digite o CPF (apenas números)' : 'Digite o CNPJ (apenas números)';
                }
            });

            // Formatação automática do documento
            documentoInput.addEventListener('input', function() {
                let value = this.value.replace(/[^\d]/g, '');
                const tipo = tipoSelect.value;
                
                if (tipo === 'fisica' && value.length <= 11) {
                    // Formato CPF: 000.000.000-00
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                } else if (tipo === 'juridica' && value.length <= 14) {
                    // Formato CNPJ: 00.000.000/0000-00
                    value = value.replace(/(\d{2})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1/$2');
                    value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
                }
                
                this.value = value;
            });

            carregarClientes();
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
    
    <!-- Sistema de Consulta de CEP -->
    <script src="assets/js/cep-consulta.js"></script>
</body>

</html>