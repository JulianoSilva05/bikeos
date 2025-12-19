<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Sugestões | BikeOS</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .page-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 1200px) {
            .page-layout {
                grid-template-columns: 1fr 1fr;
            }
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr auto;
                align-items: flex-end;
            }
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

        .regra-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
        }

        .regra-item:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <main class="container">
        <div class="page-header">
            <h1><i class="fa-solid fa-lightbulb"></i> Configurar Sugestões de Produtos</h1>
        </div>

        <div class="page-layout">
            <div class="card">
                <h2>Criar Nova Regra</h2>
                <p>Defina uma regra: "Se o cliente comprar o <strong>Produto A</strong>, então sugira o <strong>Produto B</strong>."</p>
                <form id="form-nova-regra" class="form-grid">
                    <div class="form-group search-container">
                        <label class="form-label">Se comprar este (Gatilho)</label>
                        <input type="search" id="search-gatilho" class="form-input" placeholder="Buscar produto..." autocomplete="off">
                        <input type="hidden" id="produto-gatilho-id">
                        <ul id="results-gatilho" class="search-results-list" style="display:none;"></ul>
                    </div>
                    <div class="form-group search-container">
                        <label class="form-label">Sugerir este</label>
                        <input type="search" id="search-sugerido" class="form-input" placeholder="Buscar produto..." autocomplete="off">
                        <input type="hidden" id="produto-sugerido-id">
                        <ul id="results-sugerido" class="search-results-list" style="display:none;"></ul>
                    </div>
                    <button type="submit" class="btn btn-destaque"><i class="fa-solid fa-plus"></i> Criar Regra</button>
                </form>
            </div>

            <div class="card">
                <h2><i class="fa-solid fa-chart-pie"></i> Produtos Mais Vendidos em Conjunto</h2>
                <p>Use estes dados para criar novas regras de sugestão.</p>
                <div style="overflow-x:auto;">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>Par de Produtos</th>
                                <th>Vezes</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody id="pares-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top:2rem;">
            <h2>Regras Atuais</h2>
            <div id="lista-regras"></div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const formNovaRegra = document.getElementById('form-nova-regra');
            const listaRegrasDiv = document.getElementById('lista-regras');
            const paresTbody = document.getElementById('pares-tbody');
            let debounceTimer;

            const setupSearch = (inputId, resultsId, hiddenId) => {
                const searchInput = document.getElementById(inputId);
                const resultsList = document.getElementById(resultsId);
                const hiddenInput = document.getElementById(hiddenId);

                searchInput.addEventListener('input', e => {
                    clearTimeout(debounceTimer);
                    const termo = e.target.value;
                    if (termo.length < 2) {
                        resultsList.style.display = 'none';
                        return;
                    }
                    debounceTimer = setTimeout(() => {
                        fetch(`api/api_vendas.php?acao=buscar_produtos&termo=${termo}`)
                            .then(res => res.json())
                            .then(produtos => {
                                resultsList.innerHTML = '';
                                if (produtos.length > 0) {
                                    produtos.forEach(p => {
                                        const li = document.createElement('li');
                                        li.textContent = p.nome;
                                        li.onclick = () => {
                                            searchInput.value = p.nome;
                                            hiddenInput.value = p.id;
                                            resultsList.style.display = 'none';
                                        };
                                        resultsList.appendChild(li);
                                    });
                                    resultsList.style.display = 'block';
                                }
                            });
                    }, 300);
                });
            };

            setupSearch('search-gatilho', 'results-gatilho', 'produto-gatilho-id');
            setupSearch('search-sugerido', 'results-sugerido', 'produto-sugerido-id');

            const carregarRegras = async () => {
                try {
                    const response = await fetch('api/api_sugestoes.php?acao=listar_regras');
                    const resultado = await response.json();
                    listaRegrasDiv.innerHTML = '';
                    if (resultado.sucesso && resultado.dados.length > 0) {
                        resultado.dados.forEach(regra => {
                            listaRegrasDiv.innerHTML += `
                                <div class="regra-item">
                                    <span><strong>Se comprar:</strong> ${regra.nome_gatilho} <i class="fa-solid fa-arrow-right"></i> <strong>Sugerir:</strong> ${regra.nome_sugerido}</span>
                                    <button class="btn btn-sm btn-erro btn-excluir-regra" data-id="${regra.id}"><i class="fa-solid fa-trash-can"></i></button>
                                </div>
                            `;
                        });
                    } else {
                        listaRegrasDiv.innerHTML = '<p>Nenhuma regra de sugestão criada ainda.</p>';
                    }
                } catch (e) {
                    listaRegrasDiv.innerHTML = '<p class="error-message">Erro ao carregar regras.</p>';
                }
            };

            const carregarParesVendidos = async () => {
                paresTbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Analisando dados...</td></tr>';
                try {
                    const response = await fetch('api/api_sugestoes.php?acao=listar_pares_vendidos');
                    const resultado = await response.json();
                    paresTbody.innerHTML = '';
                    if (resultado.sucesso && resultado.dados.length > 0) {
                        resultado.dados.forEach(par => {
                            const btnDisabled = par.regra_existe ? 'disabled' : '';
                            const btnClass = par.regra_existe ? 'btn-secundario' : 'btn-sucesso';
                            const btnIcon = par.regra_existe ? 'fa-check' : 'fa-plus';
                            const btnText = par.regra_existe ? 'Regra Criada' : 'Criar Regra';

                            paresTbody.innerHTML += `
                                <tr>
                                    <td>${par.nome1} + ${par.nome2}</td>
                                    <td>${par.vezes_vendidos_juntos}x</td>
                                    <td>
                                        <button class="btn btn-sm ${btnClass} btn-criar-regra-par" 
                                            data-id1="${par.id1}" data-nome1="${par.nome1}" 
                                            data-id2="${par.id2}" data-nome2="${par.nome2}" 
                                            title="Criar regra a partir deste par" ${btnDisabled}>
                                            <i class="fa-solid ${btnIcon}"></i> ${btnText}
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        paresTbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Nenhum par de produtos frequentemente vendido junto foi encontrado.</td></tr>';
                    }
                } catch (e) {
                    paresTbody.innerHTML = '<tr><td colspan="3" class="error-message">Erro ao analisar os pares.</td></tr>';
                }
            };

            formNovaRegra.addEventListener('submit', async (e) => {
                e.preventDefault();
                const payload = {
                    produto_gatilho_id: document.getElementById('produto-gatilho-id').value,
                    produto_sugerido_id: document.getElementById('produto-sugerido-id').value
                };
                try {
                    const response = await fetch('api/api_sugestoes.php?acao=salvar_regra', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        alert(resultado.mensagem);
                        formNovaRegra.reset();
                        carregarRegras();
                        carregarParesVendidos();
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    alert('Erro: ' + error.message);
                }
            });

            listaRegrasDiv.addEventListener('click', async (e) => {
                const target = e.target.closest('.btn-excluir-regra');
                if (target) {
                    const id = target.dataset.id;
                    if (confirm('Tem certeza que deseja excluir esta regra?')) {
                        try {
                            const response = await fetch('api/api_sugestoes.php?acao=excluir_regra', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    id
                                })
                            });
                            const resultado = await response.json();
                            if (resultado.sucesso) {
                                alert(resultado.mensagem);
                                carregarRegras();
                                carregarParesVendidos();
                            } else {
                                throw new Error(resultado.mensagem);
                            }
                        } catch (error) {
                            alert('Erro: ' + error.message);
                        }
                    }
                }
            });

            paresTbody.addEventListener('click', (e) => {
                const target = e.target.closest('.btn-criar-regra-par');
                if (target) {
                    const id1 = target.dataset.id1;
                    const nome1 = target.dataset.nome1;
                    const id2 = target.dataset.id2;
                    const nome2 = target.dataset.nome2;

                    if (confirm(`Qual regra deseja criar?\n\nOK: Se comprar "${nome1}", sugerir "${nome2}".\nCANCELAR: Se comprar "${nome2}", sugerir "${nome1}".`)) {
                        document.getElementById('search-gatilho').value = nome1;
                        document.getElementById('produto-gatilho-id').value = id1;
                        document.getElementById('search-sugerido').value = nome2;
                        document.getElementById('produto-sugerido-id').value = id2;
                    } else {
                        document.getElementById('search-gatilho').value = nome2;
                        document.getElementById('produto-gatilho-id').value = id2;
                        document.getElementById('search-sugerido').value = nome1;
                        document.getElementById('produto-sugerido-id').value = id1;
                    }
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });

            carregarRegras();
            carregarParesVendidos();
        });
    </script>
    
    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>