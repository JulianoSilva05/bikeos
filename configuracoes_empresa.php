<?php 
require_once 'auth_check.php';
require_once 'check_initial_config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Configurações da Empresa'); ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .logo-preview {
            margin-top: 1rem;
            border: 2px dashed var(--cor-borda);
            border-radius: 8px;
            padding: 1rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .required {
            color: red;
        }
    </style>
</head>

<body>
    <?php include '_partials/navigation_header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1><i class="fa-solid fa-building"></i> Configurações da Empresa</h1>
            <p>Configure os dados da sua empresa para emissão de NFe/NFCe e outras funcionalidades.</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><i class="fa-solid fa-store"></i> Dados da Empresa</h2>
            </div>
            <div class="card-body">
                <form id="form-configuracoes-empresa">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nome_loja">Nome da Empresa <span class="required">*</span></label>
                            <input type="text" id="nome_loja" name="nome_loja" class="form-input" placeholder="Digite o nome da empresa" required>
                        </div>
                        <div class="form-group">
                            <label for="razao_social">Razão Social <span class="required">*</span></label>
                            <input type="text" id="razao_social" name="razao_social" class="form-input" placeholder="Digite a razão social" required>
                        </div>
                        <div class="form-group">
                            <label for="cnpj">CNPJ <span class="required">*</span></label>
                            <input type="text" id="cnpj" name="cnpj" class="form-input" placeholder="00.000.000/0000-00" required>
                        </div>
                        <div class="form-group">
                            <label for="inscricao_estadual">Inscrição Estadual</label>
                            <input type="text" id="inscricao_estadual" name="inscricao_estadual" class="form-input" placeholder="Digite a inscrição estadual">
                        </div>
                        <div class="form-group">
                            <label for="telefone">Telefone <span class="required">*</span></label>
                            <input type="text" id="telefone" name="telefone" class="form-input" placeholder="(00) 00000-0000" required>
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="Digite o e-mail da empresa">
                        </div>
                        <div class="form-group">
                            <label for="inscricao_estadual">Inscrição Estadual</label>
                            <input type="text" id="inscricao_estadual" name="inscricao_estadual" class="form-input" placeholder="Digite a inscrição estadual">
                        </div>
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" name="cep" class="form-input" placeholder="00000-000" data-cep="true">
                        </div>
                        <div class="form-group">
                            <label for="logradouro">Logradouro</label>
                            <input type="text" id="logradouro" name="logradouro" class="form-input" placeholder="Rua, Avenida, etc." data-cep-logradouro="true">
                        </div>
                        <div class="form-group">
                            <label for="numero">Número</label>
                            <input type="text" id="numero" name="numero" class="form-input" placeholder="Número">
                        </div>
                        <div class="form-group">
                            <label for="complemento">Complemento</label>
                            <input type="text" id="complemento" name="complemento" class="form-input" placeholder="Apartamento, bloco, etc.">
                        </div>
                        <div class="form-group">
                            <label for="bairro">Bairro</label>
                            <input type="text" id="bairro" name="bairro" class="form-input" placeholder="Bairro" data-cep-bairro="true">
                        </div>
                        <div class="form-group">
                            <label for="cidade">Cidade <span class="required">*</span></label>
                            <input type="text" id="cidade" name="cidade" class="form-input" placeholder="Digite a cidade" required data-cep-localidade="true">
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado" class="form-input" data-cep-uf="true">
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
                            <label for="chave_pix">Chave PIX</label>
                            <input type="text" id="chave_pix" name="chave_pix" class="form-input" placeholder="Digite a chave PIX">
                        </div>
                        <div class="form-group">
                            <label for="logo_loja">Logo da Empresa</label>
                            <input type="file" id="logo_loja" name="logo_loja" class="form-input" accept="image/*">
                            <img id="logo_preview" class="logo-preview" src="/assets/img/logo.png" alt="Logo" style="max-width: 200px; max-height: 100px; object-fit: contain; display: block;">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primario">
                            <i class="fa-solid fa-save"></i> Salvar Configurações
                        </button>
                        <a href="emissao_nfe.php" class="btn btn-secundario">
                            <i class="fa-solid fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Carregar dados existentes
        document.addEventListener('DOMContentLoaded', function() {
            carregarConfiguracoes();
        });

        // Função para carregar configurações existentes
        async function carregarConfiguracoes() {
            try {
                const response = await fetch('api/api_funcionalidades.php?action=get_configuracoes');
                const data = await response.json();
                
                if (data.success && data.configuracoes) {
                    const config = data.configuracoes;
                    
                    // Preencher campos
                    document.getElementById('nome_loja').value = config.nome_loja || '';
                    document.getElementById('razao_social').value = config.razao_social || '';
                    document.getElementById('cnpj').value = config.cnpj || '';
                    document.getElementById('inscricao_estadual').value = config.inscricao_estadual || '';
                    document.getElementById('telefone').value = config.telefone || '';
                    document.getElementById('email').value = config.email || '';
                    document.getElementById('cidade').value = config.cidade || '';
                    document.getElementById('estado').value = config.estado || '';
                    document.getElementById('cep').value = config.cep || '';
                    document.getElementById('chave_pix').value = config.chave_pix || '';
                    
                    // Preencher campos de endereço se existirem no formato separado
                    if (config.endereco) {
                        // Se o endereço estiver em formato único, tentar extrair informações
                        const enderecoCompleto = config.endereco;
                        // Por enquanto, colocar o endereço completo no campo logradouro
                        if (document.getElementById('logradouro')) {
                            document.getElementById('logradouro').value = enderecoCompleto;
                        }
                    }
                    
                    // Preencher campos individuais se existirem
                    if (config.logradouro) document.getElementById('logradouro').value = config.logradouro;
                    if (config.numero) document.getElementById('numero').value = config.numero;
                    if (config.complemento) document.getElementById('complemento').value = config.complemento;
                    if (config.bairro) document.getElementById('bairro').value = config.bairro;
                    
                    // Atualizar preview do logo se existir
                    if (config.logo_loja) {
                        document.getElementById('logo_preview').src = config.logo_loja;
                    }
                }
            } catch (error) {
                console.error('Erro ao carregar configurações:', error);
            }
        }

        // Submissão do formulário
        document.getElementById('form-configuracoes-empresa').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'salvar_configuracoes');
            
            try {
                const response = await fetch('api/api_funcionalidades.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Mostrar mensagem de sucesso
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success';
                    alert.innerHTML = '<i class="fa-solid fa-check-circle"></i> Configurações salvas com sucesso!';
                    
                    const cardBody = document.querySelector('.card-body');
                    cardBody.insertBefore(alert, cardBody.firstChild);
                    
                    // Remover alerta após 3 segundos
                    setTimeout(() => {
                        alert.remove();
                    }, 3000);
                    
                    // Redirecionar para emissão de NFe se veio de lá
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('redirect') === 'nfe') {
                        setTimeout(() => {
                            window.location.href = 'emissao_nfe.php';
                        }, 1500);
                    }
                } else {
                    throw new Error(data.message || 'Erro ao salvar configurações');
                }
            } catch (error) {
                // Mostrar mensagem de erro
                const alert = document.createElement('div');
                alert.className = 'alert alert-warning';
                alert.innerHTML = '<i class="fa-solid fa-exclamation-triangle"></i> ' + error.message;
                
                const cardBody = document.querySelector('.card-body');
                cardBody.insertBefore(alert, cardBody.firstChild);
                
                // Remover alerta após 5 segundos
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }
        });

        // Máscara para CNPJ
        document.getElementById('cnpj').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = value;
        });

        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });

        // Máscara para CEP
        document.getElementById('cep').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });

        // Preview do logo
        document.getElementById('logo_loja').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo_preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.mask.min.js"></script>
    <script src="assets/js/cep-consulta.js"></script>
</body>

</html>