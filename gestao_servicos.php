<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Gestão de Serviços'); ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Estilos de modal e botões podem ser reutilizados do estilo-global.css */
        .search-container {
            position: relative;
            display: inline-block;
            margin-left: 15px;
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
            <button onclick="history.back()" class="btn btn-secundario" title="Voltar à página anterior">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </button>
            <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Ir para o Painel Principal">
                <i class="fa-solid fa-house"></i>
            </a>
        </div>
        
        <div class="page-header">
            <h1><i class="fa-solid fa-wrench"></i> Gestão de Serviços (Mão de Obra)</h1>
            <div class="header-actions">
                <div class="search-container" style="margin-right: 1rem;">
                    <div class="form-group" style="margin: 0; position: relative; width: 200px;">
                        <input type="text" id="search-servicos" class="form-input" placeholder="Buscar serviços..." style="padding-left: 2.5rem; font-size: 0.9rem;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 0.8rem; top: 50%; transform: translateY(-50%); color: var(--cor-texto-secundario); font-size: 0.9rem;"></i>
                    </div>
                </div>
                <button id="btn-novo-servico" class="btn btn-destaque"><i class="fa-solid fa-plus"></i> Novo Serviço</button>
            </div>
        </div>

        <div class="card">
            <div style="overflow-x: auto;">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Preço Padrão</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="servicos-table-body">
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="servico-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="modal-title">Novo Serviço</h2>
            <form id="servico-form">
                <input type="hidden" id="servico-id" name="id">
                <div class="form-group">
                    <label for="servico-nome" class="form-label">Nome do Serviço</label>
                    <input type="text" id="servico-nome" name="nome" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="servico-descricao" class="form-label">Descrição</label>
                    <textarea id="servico-descricao" name="descricao" class="form-input" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="servico-preco" class="form-label">Preço Padrão (R$)</label>
                    <input type="number" step="0.01" id="servico-preco" name="preco_padrao" class="form-input" required>
                </div>
                <button type="submit" class="btn btn-destaque" style="width: 100%;">Salvar Serviço</button>
            </form>
        </div>
    </div>

    <?php include '_partials/menu_admin.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Elementos do DOM ---
            const tableBody = document.getElementById('servicos-table-body');
            const modal = document.getElementById('servico-modal');
            const modalTitle = document.getElementById('modal-title');
            const servicoForm = document.getElementById('servico-form');
            const btnNovoServico = document.getElementById('btn-novo-servico');
            const searchInput = document.getElementById('search-servicos');
            let servicosData = [];

            // --- Funções ---
            const carregarServicos = async () => {
                try {
                    const response = await fetch('api/api_servicos.php?acao=listar_servicos');
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        servicosData = resultado.dados;
                        renderizarServicos(servicosData);
                    }
                } catch (error) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-danger">Erro ao carregar serviços.</td></tr>'
                }
            };

            const renderizarServicos = (servicos) => {
                tableBody.innerHTML = '';
                servicos.forEach(s => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${s.id}</td>
                        <td>${s.nome}</td>
                        <td>${s.descricao || '-'}</td>
                        <td>R$ ${parseFloat(s.preco_padrao).toFixed(2)}</td>
                        <td>
                            <button class="btn btn-sm btn-primario btn-editar" data-id="${s.id}" title="Editar"><i class="fa-solid fa-pencil"></i></button>
                            <button class="btn btn-sm btn-erro btn-excluir" data-id="${s.id}" title="Excluir"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });
            };

            const filtrarServicos = (termo) => {
                const servicosFiltrados = servicosData.filter(s => 
                    s.nome.toLowerCase().includes(termo.toLowerCase()) ||
                    (s.descricao && s.descricao.toLowerCase().includes(termo.toLowerCase()))
                );
                renderizarServicos(servicosFiltrados);
            };

            const abrirModal = async (id = null) => {
                servicoForm.reset();
                document.getElementById('servico-id').value = '';
                if (id) { // Modo Edição
                    modalTitle.textContent = `Editar Serviço #${id}`;
                    const response = await fetch(`api/api_servicos.php?acao=buscar_servico_por_id&id=${id}`);
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        const s = resultado.dados;
                        document.getElementById('servico-id').value = s.id;
                        document.getElementById('servico-nome').value = s.nome;
                        document.getElementById('servico-descricao').value = s.descricao;
                        document.getElementById('servico-preco').value = s.preco_padrao;
                    }
                } else { // Modo Criação
                    modalTitle.textContent = 'Novo Serviço';
                }
                modal.style.display = 'flex';
            };

            const fecharModal = () => modal.style.display = 'none';

            // --- Event Listeners ---
            btnNovoServico.addEventListener('click', () => abrirModal());
            modal.querySelector('.modal-close').onclick = fecharModal;
            modal.onclick = (e) => {
                if (e.target === modal) fecharModal();
            };

            servicoForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(servicoForm);
                const dados = Object.fromEntries(formData.entries());

                try {
                    const response = await fetch('api/api_servicos.php?acao=salvar_servico', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(dados)
                    });
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        alert(resultado.mensagem);
                        fecharModal();
                        carregarServicos();
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    alert("Erro: " + error.message);
                }
            });

            tableBody.addEventListener('click', async (e) => {
                const target = e.target.closest('button');
                if (!target) return;

                const id = target.dataset.id;
                if (target.classList.contains('btn-editar')) {
                    abrirModal(id);
                } else if (target.classList.contains('btn-excluir')) {
                    if (confirm(`Tem certeza que deseja excluir o serviço #${id}? Esta ação não pode ser desfeita.`)) {
                        try {
                            const response = await fetch('api/api_servicos.php?acao=excluir_servico', {
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
                            if (resultado.sucesso) carregarServicos();
                        } catch (error) {
                            alert("Erro: " + error.message);
                        }
                    }
                }
            });

            // --- Event Listeners ---
            searchInput.addEventListener('input', (e) => {
                const termo = e.target.value.trim();
                if (termo === '') {
                    renderizarServicos(servicosData);
                } else {
                    filtrarServicos(termo);
                }
            });

            // --- Inicialização ---
            carregarServicos();
        });
    </script>
    

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>