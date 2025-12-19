<?php 
require_once 'auth_check.php';
require_once 'check_initial_config.php';

// Verificar se é configuração inicial
$config_inicial = isset($_GET['config_inicial']) && $_GET['config_inicial'] == '1';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php 
        require_once 'helpers/empresa_helper.php';
        echo getTituloComEmpresa('Funcionalidades');
    ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .tabs-container {
            margin-bottom: 2rem;
        }

        .tabs-nav {
            display: flex;
            border-bottom: 2px solid var(--cor-borda);
            margin-bottom: 2rem;
        }

        .tab-button {
            padding: 1rem 2rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            color: var(--cor-texto-secundario);
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            color: var(--cor-primaria);
            border-bottom-color: var(--cor-primaria);
            font-weight: bold;
        }

        .tab-button:hover {
            color: var(--cor-primaria);
            background-color: var(--cor-fundo-claro);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-grid-full {
            grid-column: 1 / -1;
        }



        .logo-preview {
            max-width: 200px;
            max-height: 100px;
            border: 2px solid var(--cor-borda);
            border-radius: 5px;
            margin-top: 0.5rem;
        }

        .funcionario-card {
            background: var(--cor-fundo-claro);
            border: 1px solid var(--cor-borda);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .funcionario-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .status-ativo {
            background-color: var(--cor-sucesso);
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8em;
        }

        .status-inativo {
            background-color: var(--cor-erro);
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8em;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .tabs-nav {
                flex-direction: column;
            }

            .tab-button {
                text-align: left;
                border-bottom: 1px solid var(--cor-borda);
                border-right: none;
            }

            .tab-button.active {
                border-bottom-color: var(--cor-primaria);
                border-right: none;
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
        <?php if (!$config_inicial): ?>
        <div class="page-navigation">
            <button onclick="history.back()" class="btn btn-secundario" title="Voltar"><i class="fa-solid fa-arrow-left"></i> Voltar</button>
            <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Dashboard"><i class="fa-solid fa-house"></i></a>
        </div>
        <?php endif; ?>
        
        <div class="page-header">
            <h1><i class="fa-solid fa-cogs"></i> <?php echo $config_inicial ? 'Configuração Inicial Obrigatória' : 'Funcionalidades do Sistema'; ?></h1>
        </div>
        
        <?php if ($config_inicial): ?>
        <div class="alert alert-warning">
            <i class="fa-solid fa-exclamation-triangle"></i>
            <strong>Configuração Inicial Obrigatória:</strong> Para utilizar o sistema, você deve configurar os dados da sua empresa abaixo.
        </div>
        <?php endif; ?>

        <div class="tabs-container">
            <div class="tabs-nav">
                <button class="tab-button active" data-tab="configuracoes">
                    <i class="fa-solid fa-store"></i> <?php echo $config_inicial ? 'Dados da Empresa' : 'Configurações da Loja'; ?>
                </button>
                <?php if (!$config_inicial): ?>
                <button class="tab-button" data-tab="funcionarios">
                    <i class="fa-solid fa-users"></i> Funcionários
                </button>
                <button class="tab-button" data-tab="impressao">
                    <i class="fa-solid fa-print"></i> Gerar Impressão/Link
                </button>
                <?php endif; ?>
            </div>

            <!-- Aba Configurações da Loja -->
            <div id="configuracoes" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fa-solid fa-store"></i> <?php echo $config_inicial ? 'Dados da Empresa (Obrigatório)' : 'Configurações da Loja'; ?></h2>
                    </div>
                    <div class="card-body">
                        <form id="form-configuracoes">
                            <?php if ($config_inicial): ?>
                            <input type="hidden" name="config_inicial" value="1">
                            <?php endif; ?>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nome_loja">Nome da Empresa <?php echo $config_inicial ? '<span style="color: red;">*</span>' : ''; ?></label>
                                    <input type="text" id="nome_loja" name="nome_loja" class="form-input" placeholder="Digite o nome da empresa" <?php echo $config_inicial ? 'required' : ''; ?>>
                                </div>
                                <div class="form-group">
                                    <label for="razao_social">Responsável <?php echo $config_inicial ? '<span style="color: red;">*</span>' : ''; ?></label>
                                    <input type="text" id="razao_social" name="razao_social" class="form-input" placeholder="Digite o nome do responsável" <?php echo $config_inicial ? 'required' : ''; ?>>
                                </div>
                                <div class="form-group">
                                    <label for="cnpj">CNPJ</label>
                                    <input type="text" id="cnpj" name="cnpj" class="form-input" placeholder="00.000.000/0000-00">
                                </div>
                                <div class="form-group">
                                    <label for="inscricao_estadual">Inscrição Estadual</label>
                                    <input type="text" id="inscricao_estadual" name="inscricao_estadual" class="form-input" placeholder="Digite a inscrição estadual">
                                </div>
                                <div class="form-group">
                                    <label for="telefone">Telefone <?php echo $config_inicial ? '<span style="color: red;">*</span>' : ''; ?></label>
                                    <input type="text" id="telefone" name="telefone" class="form-input" placeholder="(00) 00000-0000" <?php echo $config_inicial ? 'required' : ''; ?>>
                                </div>
                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="email" id="email" name="email" class="form-input" placeholder="Digite o e-mail da empresa">
                                </div>
                                <div class="form-group">
                                    <label for="cep">CEP</label>
                                    <input type="text" id="cep" name="cep" class="form-input" placeholder="00000-000" data-cep="true">
                                </div>
                                <div class="form-group">
                                    <label for="cidade">Cidade <?php echo $config_inicial ? '<span style="color: red;">*</span>' : ''; ?></label>
                                    <input type="text" id="cidade" name="cidade" class="form-input" placeholder="Digite a cidade" <?php echo $config_inicial ? 'required' : ''; ?> data-cep-localidade="true">
                                </div>
                                <div class="form-group">
                                    <label for="estado">Estado</label>
                                    <select id="estado" name="estado" class="form-input" data-cep-uf="true">
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
                                    <label for="logradouro">Logradouro</label>
                                    <input type="text" id="logradouro" name="logradouro" class="form-input" placeholder="Digite o logradouro" data-cep-logradouro="true">
                                </div>
                                <div class="form-group">
                                    <label for="numero">Número</label>
                                    <input type="text" id="numero" name="numero" class="form-input" placeholder="Digite o número">
                                </div>
                                <div class="form-group">
                                    <label for="complemento">Complemento</label>
                                    <input type="text" id="complemento" name="complemento" class="form-input" placeholder="Digite o complemento (opcional)">
                                </div>
                                <div class="form-group">
                                    <label for="bairro">Bairro</label>
                                    <input type="text" id="bairro" name="bairro" class="form-input" placeholder="Digite o bairro" data-cep-bairro="true">
                                </div>
                                <div class="form-group">
                                    <label for="endereco">Endereço Completo</label>
                                    <input type="text" id="endereco" name="endereco" class="form-input" placeholder="Endereço será preenchido automaticamente" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="chave_pix">Chave PIX</label>
                                    <input type="text" id="chave_pix" name="chave_pix" class="form-input" placeholder="Digite a chave PIX">
                                </div>
                                <div class="form-group">
                                    <label for="logo_loja">Logo da Loja</label>
                                    <input type="file" id="logo_loja" name="logo_loja" class="form-input" accept="image/*">
                                    <img id="logo_preview" class="logo-preview" src="/assets/img/logo.png" alt="Logo" style="max-width: 200px; max-height: 100px; object-fit: contain; display: block;">
                                </div>
                            </div>



                            <div class="form-actions">
                                <button type="submit" class="btn btn-primario">
                                    <i class="fa-solid fa-save"></i> <?php echo $config_inicial ? 'Concluir Configuração Inicial' : 'Salvar Configurações'; ?>
                                </button>
                                <?php if ($config_inicial): ?>
                                <p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">
                                    <i class="fa-solid fa-info-circle"></i> Após concluir a configuração inicial, você terá acesso a todas as funcionalidades do sistema.
                                </p>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Aba Funcionários -->
            <div id="funcionarios" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fa-solid fa-users"></i> Gestão de Funcionários</h2>
                        <button class="btn btn-primario" onclick="abrirModalFuncionario()">
                            <i class="fa-solid fa-plus"></i> Novo Funcionário
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="lista-funcionarios">
                            <!-- Lista de funcionários será carregada aqui -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba Gerar Impressão/Link -->
            <div id="impressao" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fa-solid fa-print"></i> Gerar Impressão/Link</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tipo_documento">Tipo de Documento</label>
                                <select id="tipo_documento" class="form-input" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="os">Ordem de Serviço</option>
                                    <option value="venda">Venda</option>
                                    <option value="divida">Dívida</option>
                                    <option value="pedido">Pedido</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="numero_documento">Número do Documento</label>
                                <input type="number" id="numero_documento" class="form-input" placeholder="Digite o número" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-primario" onclick="buscarDocumento()">
                                <i class="fa-solid fa-search"></i> Buscar Documento
                            </button>
                        </div>
                        
                        <!-- Área de resultado -->
                        <div id="resultado-documento" style="display: none; margin-top: 2rem;">
                            <div class="card">
                                <div class="card-header">
                                    <h3 id="titulo-documento"></h3>
                                </div>
                                <div class="card-body">
                                    <div id="detalhes-documento"></div>
                                    <div class="form-actions" style="margin-top: 1.5rem;">
                                        <button type="button" class="btn btn-sucesso" onclick="enviarWhatsApp()">
                                            <i class="fa-brands fa-whatsapp"></i> Enviar via WhatsApp
                                        </button>
                                        <button type="button" class="btn btn-secundario" onclick="imprimirDocumento()">
                                            <i class="fa-solid fa-print"></i> Imprimir
                                        </button>
                                        <button type="button" class="btn btn-info" onclick="gerarLink()">
                                            <i class="fa-solid fa-link"></i> Gerar Link
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Funcionário -->
    <div id="modal-funcionario" class="modal-backdrop" style="display: none;">
        <div class="modal-content">
            <span class="modal-close" onclick="fecharModalFuncionario()">&times;</span>
            <h2 id="modal-titulo"><i class="fa-solid fa-user-plus"></i> Novo Funcionário</h2>
            <form id="form-funcionario">
                <input type="hidden" id="funcionario_id" name="funcionario_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome_funcionario">Nome Completo</label>
                        <input type="text" id="nome_funcionario" name="nome_funcionario" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="email_funcionario">E-mail</label>
                        <input type="email" id="email_funcionario" name="email_funcionario" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="telefone_funcionario">Telefone</label>
                        <input type="text" id="telefone_funcionario" name="telefone_funcionario" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="cep_funcionario">CEP</label>
                        <input type="text" id="cep_funcionario" name="cep_funcionario" class="form-input" placeholder="00000-000" data-cep="true">
                    </div>
                    <div class="form-group">
                        <label for="logradouro_funcionario">Logradouro</label>
                        <input type="text" id="logradouro_funcionario" name="logradouro_funcionario" class="form-input" placeholder="Rua, Avenida, etc." data-cep-logradouro="true">
                    </div>
                    <div class="form-group">
                        <label for="numero_funcionario">Número</label>
                        <input type="text" id="numero_funcionario" name="numero_funcionario" class="form-input" placeholder="123">
                    </div>
                    <div class="form-group">
                        <label for="complemento_funcionario">Complemento</label>
                        <input type="text" id="complemento_funcionario" name="complemento_funcionario" class="form-input" placeholder="Apto, Bloco, etc.">
                    </div>
                    <div class="form-group">
                        <label for="bairro_funcionario">Bairro</label>
                        <input type="text" id="bairro_funcionario" name="bairro_funcionario" class="form-input" placeholder="Nome do bairro" data-cep-bairro="true">
                    </div>
                    <div class="form-group">
                        <label for="cidade_funcionario">Cidade</label>
                        <input type="text" id="cidade_funcionario" name="cidade_funcionario" class="form-input" placeholder="Nome da cidade" data-cep-cidade="true">
                    </div>
                    <div class="form-group">
                        <label for="estado_funcionario">Estado</label>
                        <select id="estado_funcionario" name="estado_funcionario" class="form-input" data-cep-estado="true">
                            <option value="">Selecione o estado</option>
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
                        <label for="cargo_funcionario">Cargo</label>
                        <select id="cargo_funcionario" name="cargo_funcionario" class="form-input" required>
                            <option value="">Selecione o cargo</option>
                            <option value="GERENTE">Gerente</option>
                            <option value="VENDEDOR">Vendedor</option>
                            <option value="MECANICO">Mecânico</option>
                            <option value="ATENDENTE">Atendente</option>
                            <option value="ADMINISTRATIVO">Administrativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usuario_funcionario">Usuário de Login</label>
                        <input type="text" id="usuario_funcionario" name="usuario_funcionario" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="senha_funcionario">Senha</label>
                        <input type="password" id="senha_funcionario" name="senha_funcionario" class="form-input">
                        <small class="form-help">Deixe em branco para manter a senha atual (apenas edição)</small>
                    </div>
                    <div class="form-group">
                        <label for="status_funcionario">Status</label>
                        <select id="status_funcionario" name="status_funcionario" class="form-input" required>
                            <option value="ATIVO">Ativo</option>
                            <option value="INATIVO">Inativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="permissoes_funcionario">Permissões</label>
                        <select id="permissoes_funcionario" name="permissoes_funcionario" class="form-input" required>
                            <option value="BASICO">Básico (Vendas e OS)</option>
                            <option value="AVANCADO">Avançado (+ Estoque e Relatórios)</option>
                            <option value="ADMIN">Administrador (Acesso Total)</option>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secundario" onclick="fecharModalFuncionario()">Cancelar</button>
                    <button type="submit" class="btn btn-primario">
                        <i class="fa-solid fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include '_partials/menu_admin.php'; ?>

    <script>
        // Gerenciamento de Abas
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DEBUG: DOMContentLoaded executado!');
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetTab = button.getAttribute('data-tab');

                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    // Add active class to clicked button and corresponding content
                    button.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });

            // Carregar dados iniciais
            carregarConfiguracoes();
            carregarFuncionarios();



            // Preview de logo
            setupLogoPreview();
            
            // Salvar configurações
            document.getElementById('form-configuracoes').addEventListener('submit', function(e) {
                console.log('DEBUG: Evento de submit foi disparado!');
                e.preventDefault();
                
                const formData = new FormData(this);
                const isConfigInicial = formData.get('config_inicial') === '1';
                
                console.log('DEBUG: config_inicial value:', formData.get('config_inicial'));
                console.log('DEBUG: isConfigInicial:', isConfigInicial);
                
                // Escolher API baseada no tipo de configuração
                const apiUrl = isConfigInicial ? 'api_config_inicial.php' : 'api/api_funcionalidades.php';
                console.log('DEBUG: API URL escolhida:', apiUrl);
                
                if (!isConfigInicial) {
                    formData.append('action', 'salvar_configuracoes');
                    console.log('DEBUG: Adicionado action=salvar_configuracoes');
                }

                console.log('DEBUG: Enviando requisição para:', apiUrl);
                console.log('DEBUG: FormData entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }
                
                fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('DEBUG: Response status:', response.status);
                    console.log('DEBUG: Response headers:', response.headers);
                    return response.text();
                })
                .then(text => {
                    console.log('DEBUG: Response text:', text);
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            alert(data.message || 'Configurações salvas com sucesso!');
                            
                            if (isConfigInicial && data.redirect) {
                                // Redirecionar para dashboard após configuração inicial
                                window.location.href = data.redirect;
                            } else {
                                carregarConfiguracoes();
                                
                                // Recarregar configurações dinâmicas em todo o sistema
                                if (window.lojaConfig) {
                                    window.lojaConfig.recarregar();
                                }
                                
                                // Recarregar CSS dinâmico
                                const dynamicCSS = document.querySelector('link[href*="dynamic-theme.php"]');
                                if (dynamicCSS) {
                                    dynamicCSS.href = dynamicCSS.href.split('?')[0] + '?v=' + Date.now();
                                }
                            }
                        } else {
                            alert('Erro: ' + (data.message || 'Erro ao salvar configurações'));
                        }
                    } catch (parseError) {
                        console.error('Erro ao fazer parse do JSON:', parseError);
                        console.error('Texto da resposta:', text);
                        alert('Erro: Resposta inválida do servidor');
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição:', error);
                    alert('Erro ao salvar configurações: ' + error.message);
                });
            });
        });



        // Configuração de preview de logo
        function setupLogoPreview() {
            const logoInput = document.getElementById('logo_loja');
            const logoPreview = document.getElementById('logo_preview');

            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        logoPreview.src = e.target.result;
                        logoPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Carregar configurações da loja
        function carregarConfiguracoes() {
            fetch('api/api_funcionalidades.php?action=get_configuracoes')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.configuracoes) {
                        const config = data.configuracoes;
                        
                        // Verificar se os elementos existem antes de tentar acessá-los
                        const setValueIfExists = (id, value) => {
                            const element = document.getElementById(id);
                            if (element) element.value = value || '';
                        };
                        
                        setValueIfExists('nome_loja', config.nome_loja);
                        setValueIfExists('razao_social', config.razao_social);
                        setValueIfExists('cnpj', config.cnpj);
                        setValueIfExists('inscricao_estadual', config.inscricao_estadual);
                        setValueIfExists('telefone', config.telefone);
                        setValueIfExists('email', config.email);
                        setValueIfExists('cep', config.cep);
                        setValueIfExists('cidade', config.cidade);
                        setValueIfExists('estado', config.estado);
                        setValueIfExists('logradouro', config.logradouro);
                        setValueIfExists('numero', config.numero);
                        setValueIfExists('complemento', config.complemento);
                        setValueIfExists('bairro', config.bairro);
                        setValueIfExists('endereco', config.endereco);
                        setValueIfExists('chave_pix', config.chave_pix);

                        
                        // Sempre exibir a logo padrão da pasta assets/img
                        const logoPreview = document.getElementById('logo_preview');
                        if (logoPreview) {
                            logoPreview.src = '/assets/img/logo.png';
                            logoPreview.style.display = 'block';
                        }
                    }
                })
                .catch(error => console.error('Erro ao carregar configurações:', error));
        }



        // Carregar funcionários
        function carregarFuncionarios() {
            fetch('api/api_funcionalidades.php?action=get_funcionarios')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderizarFuncionarios(data.funcionarios);
                    }
                })
                .catch(error => console.error('Erro ao carregar funcionários:', error));
        }

        // Renderizar lista de funcionários
        function renderizarFuncionarios(funcionarios) {
            const container = document.getElementById('lista-funcionarios');
            
            if (funcionarios.length === 0) {
                container.innerHTML = '<p class="text-center">Nenhum funcionário cadastrado.</p>';
                return;
            }

            container.innerHTML = funcionarios.map(funcionario => `
                <div class="funcionario-card">
                    <div class="funcionario-header">
                        <h4>${funcionario.nome}</h4>
                        <span class="status-${funcionario.status.toLowerCase()}">${funcionario.status}</span>
                    </div>
                    <p><strong>Cargo:</strong> ${funcionario.cargo}</p>
                    <p><strong>E-mail:</strong> ${funcionario.email}</p>
                    <p><strong>Telefone:</strong> ${funcionario.telefone || 'Não informado'}</p>
                    <p><strong>Usuário:</strong> ${funcionario.usuario}</p>
                    <p><strong>Permissões:</strong> ${funcionario.permissoes}</p>
                    <div class="form-actions">
                        <button class="btn btn-secundario" onclick="editarFuncionario(${funcionario.id})">
                            <i class="fa-solid fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-erro" onclick="excluirFuncionario(${funcionario.id})">
                            <i class="fa-solid fa-trash"></i> Excluir
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Modal de funcionário
        function abrirModalFuncionario(funcionario = null) {
            const modal = document.getElementById('modal-funcionario');
            const titulo = document.getElementById('modal-titulo');
            const form = document.getElementById('form-funcionario');
            
            if (funcionario) {
                titulo.innerHTML = '<i class="fa-solid fa-user-edit"></i> Editar Funcionário';
                preencherFormFuncionario(funcionario);
            } else {
                titulo.innerHTML = '<i class="fa-solid fa-user-plus"></i> Novo Funcionário';
                form.reset();
                document.getElementById('funcionario_id').value = '';
            }
            
            modal.style.display = 'flex';
        }

        function fecharModalFuncionario() {
            document.getElementById('modal-funcionario').style.display = 'none';
        }

        function preencherFormFuncionario(funcionario) {
            document.getElementById('funcionario_id').value = funcionario.id;
            document.getElementById('nome_funcionario').value = funcionario.nome;
            document.getElementById('email_funcionario').value = funcionario.email;
            document.getElementById('telefone_funcionario').value = funcionario.telefone || '';
            document.getElementById('cargo_funcionario').value = funcionario.cargo;
            document.getElementById('usuario_funcionario').value = funcionario.usuario;
            document.getElementById('status_funcionario').value = funcionario.status;
            document.getElementById('permissoes_funcionario').value = funcionario.permissoes;
            document.getElementById('senha_funcionario').value = '';
        }

        // Salvar funcionário
        document.getElementById('form-funcionario').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const funcionarioId = document.getElementById('funcionario_id').value;
            formData.append('action', funcionarioId ? 'editar_funcionario' : 'criar_funcionario');

            fetch('api/api_funcionalidades.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Funcionário salvo com sucesso!');
                    fecharModalFuncionario();
                    carregarFuncionarios();
                } else {
                    alert('Erro ao salvar funcionário: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar funcionário.');
            });
        });

        // Editar funcionário
        function editarFuncionario(id) {
            fetch(`api/api_funcionalidades.php?action=get_funcionario&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        abrirModalFuncionario(data.funcionario);
                    } else {
                        alert('Erro ao carregar dados do funcionário.');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao carregar funcionário.');
                });
        }

        // Excluir funcionário
        function excluirFuncionario(id) {
            if (confirm('Tem certeza que deseja excluir este funcionário?')) {
                fetch('api/api_funcionalidades.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=excluir_funcionario&id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Funcionário excluído com sucesso!');
                        carregarFuncionarios();
                    } else {
                        alert('Erro ao excluir funcionário: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao excluir funcionário.');
                });
            }
        }

        // Funcionalidades de Impressão/Link
        let documentoAtual = null;

        function buscarDocumento() {
            const tipo = document.getElementById('tipo_documento').value;
            const numero = document.getElementById('numero_documento').value;
            
            if (!tipo || !numero) {
                alert('Por favor, selecione o tipo de documento e digite o número.');
                return;
            }
            
            // Mostrar loading
            const resultadoDiv = document.getElementById('resultado-documento');
            resultadoDiv.style.display = 'block';
            document.getElementById('detalhes-documento').innerHTML = '<p><i class="fa-solid fa-spinner fa-spin"></i> Buscando documento...</p>';
            
            fetch(`api/api_funcionalidades.php?action=buscar_documento&tipo=${tipo}&numero=${numero}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        documentoAtual = data.documento;
                        exibirDocumento(data.documento, tipo);
                    } else {
                        document.getElementById('detalhes-documento').innerHTML = `<p class="text-error">Documento não encontrado: ${data.message}</p>`;
                        documentoAtual = null;
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('detalhes-documento').innerHTML = '<p class="text-error">Erro ao buscar documento.</p>';
                    documentoAtual = null;
                });
        }

        function exibirDocumento(documento, tipo) {
            const tituloElement = document.getElementById('titulo-documento');
            const detalhesElement = document.getElementById('detalhes-documento');
            
            let titulo = '';
            let detalhes = '';
            
            switch(tipo) {
                case 'os':
                    titulo = `Ordem de Serviço #${documento.id}`;
                    detalhes = `
                        <div class="documento-info">
                            <p><strong>Cliente:</strong> ${documento.cliente_nome}</p>
                            <p><strong>Data:</strong> ${new Date(documento.data_criacao).toLocaleDateString('pt-BR')}</p>
                            <p><strong>Status:</strong> ${documento.status}</p>
                            <p><strong>Valor Total:</strong> R$ ${parseFloat(documento.valor_total || 0).toFixed(2)}</p>
                            <p><strong>Descrição:</strong> ${documento.descricao_problema}</p>
                            ${documento.servicos ? `<p><strong>Serviços:</strong> ${documento.servicos}</p>` : ''}
                        </div>
                    `;
                    break;
                case 'venda':
                    titulo = `Venda #${documento.id}`;
                    detalhes = `
                        <div class="documento-info">
                            <p><strong>Cliente:</strong> ${documento.cliente_nome}</p>
                            <p><strong>Data:</strong> ${new Date(documento.data_venda).toLocaleDateString('pt-BR')}</p>
                            <p><strong>Valor Total:</strong> R$ ${parseFloat(documento.valor_total).toFixed(2)}</p>
                            <p><strong>Método de Pagamento:</strong> ${documento.metodo_pagamento}</p>
                            ${documento.produtos ? `<div><strong>Produtos:</strong><br>${documento.produtos}</div>` : ''}
                        </div>
                    `;
                    break;
                case 'divida':
                    titulo = `Dívida #${documento.id}`;
                    detalhes = `
                        <div class="documento-info">
                            <p><strong>Cliente:</strong> ${documento.cliente_nome}</p>
                            <p><strong>Data:</strong> ${new Date(documento.data_venda).toLocaleDateString('pt-BR')}</p>
                            <p><strong>Valor:</strong> R$ ${parseFloat(documento.valor_total).toFixed(2)}</p>
                            <p><strong>Status:</strong> ${documento.pago == 1 ? 'PAGO' : 'ATIVO'}</p>
                            <p><strong>Descrição:</strong> Venda a crediário</p>
                        </div>
                    `;
                    break;
                case 'pedido':
                    titulo = `Pedido #${documento.id}`;
                    detalhes = `
                        <div class="documento-info">
                            <p><strong>Cliente:</strong> ${documento.cliente_nome}</p>
                            <p><strong>Data:</strong> ${new Date(documento.data_pedido).toLocaleDateString('pt-BR')}</p>
                            <p><strong>Valor Total:</strong> R$ ${parseFloat(documento.valor_total).toFixed(2)}</p>
                            <p><strong>Status:</strong> ${documento.status}</p>
                            ${documento.produtos ? `<div><strong>Produtos:</strong><br>${documento.produtos}</div>` : ''}
                        </div>
                    `;
                    break;
            }
            
            tituloElement.textContent = titulo;
            detalhesElement.innerHTML = detalhes;
        }

        function enviarWhatsApp() {
            if (!documentoAtual) {
                alert('Nenhum documento selecionado.');
                return;
            }
            
            const tipo = document.getElementById('tipo_documento').value;
            const numero = document.getElementById('numero_documento').value;
            
            // Gerar mensagem para WhatsApp
            let mensagem = '';
            switch(tipo) {
                case 'os':
                    mensagem = `Olá ${documentoAtual.cliente_nome}!\n\nSegue os detalhes da sua Ordem de Serviço #${documentoAtual.id}:\n\nDescrição: ${documentoAtual.descricao_problema}\nValor: R$ ${parseFloat(documentoAtual.valor_total || 0).toFixed(2)}\nStatus: ${documentoAtual.status}\n\nQualquer dúvida, estamos à disposição!`;
                    break;
                case 'venda':
                    mensagem = `Olá ${documentoAtual.cliente_nome}!\n\nSegue os detalhes da sua compra #${documentoAtual.id}:\n\nValor Total: R$ ${parseFloat(documentoAtual.valor_total).toFixed(2)}\nPagamento: ${documentoAtual.metodo_pagamento}\n\nObrigado pela preferência!`;
                    break;
                case 'divida':
                    mensagem = `Olá ${documentoAtual.cliente_nome}!\n\nLembramos que você possui uma pendência #${documentoAtual.id}:\n\nValor: R$ ${parseFloat(documentoAtual.valor).toFixed(2)}\nDescrição: ${documentoAtual.descricao}\n\nPor favor, entre em contato para regularizar.`;
                    break;
                case 'pedido':
                    mensagem = `Olá ${documentoAtual.cliente_nome}!\n\nSeu pedido #${documentoAtual.id} está sendo processado:\n\nValor Total: R$ ${parseFloat(documentoAtual.valor_total).toFixed(2)}\nStatus: ${documentoAtual.status}\n\nEm breve entraremos em contato!`;
                    break;
            }
            
            const telefone = documentoAtual.cliente_telefone || '';
            const whatsappUrl = `https://wa.me/55${telefone.replace(/\D/g, '')}?text=${encodeURIComponent(mensagem)}`;
            window.open(whatsappUrl, '_blank');
        }

        function imprimirDocumento() {
            if (!documentoAtual) {
                alert('Nenhum documento selecionado.');
                return;
            }
            
            const tipo = document.getElementById('tipo_documento').value;
            const numero = document.getElementById('numero_documento').value;
            
            let printUrl = '';
            switch(tipo) {
                case 'os':
                    printUrl = `imprimir_os.php?id=${numero}`;
                    break;
                case 'venda':
                    printUrl = `imprimir_venda.php?id=${numero}`;
                    break;
                case 'divida':
                    printUrl = `imprimir_divida.php?id=${numero}`;
                    break;
                case 'pedido':
                    printUrl = `imprimir_pedido.php?id=${numero}`;
                    break;
            }
            
            window.open(printUrl, '_blank');
        }

        function gerarLink() {
            if (!documentoAtual) {
                alert('Nenhum documento selecionado.');
                return;
            }
            
            const tipo = document.getElementById('tipo_documento').value;
            const numero = document.getElementById('numero_documento').value;
            
            let linkUrl = '';
            switch(tipo) {
                case 'os':
                    linkUrl = `${window.location.origin}/imprimir_os.php?id=${numero}`;
                    break;
                case 'venda':
                    linkUrl = `${window.location.origin}/imprimir_venda.php?id=${numero}`;
                    break;
                case 'divida':
                    linkUrl = `${window.location.origin}/imprimir_divida.php?id=${numero}`;
                    break;
                case 'pedido':
                    linkUrl = `${window.location.origin}/imprimir_pedido.php?id=${numero}`;
                    break;
            }
            
            // Copiar link para área de transferência
            navigator.clipboard.writeText(linkUrl).then(() => {
                alert('Link copiado para a área de transferência!');
            }).catch(() => {
                // Fallback para navegadores mais antigos
                const textArea = document.createElement('textarea');
                textArea.value = linkUrl;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Link copiado para a área de transferência!');
            });
        }
    </script>
    
    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
    
    <!-- Sistema de Consulta de CEP -->
    <script src="assets/js/cep-consulta.js"></script>
    </main>
</body>
</html>