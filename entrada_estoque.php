<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Estoque e Compras'); ?></title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .product-image-cell {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: var(--raio-borda);
        }

        .estoque-baixo {
            background-color: #fff3cd !important;
        }

        .estoque-zerado {
            background-color: #f8d7da !important;
        }

        .tamanho-badge {
            background-color: #e2e3e5;
            color: #343a40;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8em;
            margin-right: 5px;
            white-space: nowrap;
            display: inline-block;
            margin-top: 2px;
        }

        .fornecedor-group-header {
            background-color: var(--cor-fundo-claro);
            font-weight: bold;
            font-size: 1.1em;
            padding: 0.8rem;
        }

        .fornecedor-group-header .fa-truck {
            color: var(--cor-primaria);
            margin-right: 10px;
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
            opacity: 0;
            transform: translateY(100px);
            transition: opacity 0.3s, transform 0.3s;
        }

        .toast.success {
            background-color: #28a745;
        }

        .toast.error {
            background-color: #dc3545;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .page-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .search-container {
            position: relative;
            display: inline-block;
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
            <button onclick="history.back()" class="btn btn-secundario" title="Voltar"><i class="fa-solid fa-arrow-left"></i> Voltar</button>
            <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Dashboard"><i class="fa-solid fa-house"></i></a>
        </div>
        
        <div class="page-header">
            <h1><i class="fa-solid fa-boxes-stacked"></i> Estoque e Compras</h1>
            <div class="search-container">
                <input type="text" id="search-estoque" placeholder="Buscar produtos...">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>

        <div class="page-actions">
            <a href="pedidos.php" class="btn btn-primario"><i class="fa-solid fa-clipboard-list"></i> Ver/Realizar Pedidos</a>
            <button id="btn-gerar-sugestao" class="btn btn-sucesso"><i class="fa-solid fa-lightbulb"></i> Gerar Sugestão de Pedidos</button>
        </div>

        <div class="card">
            <div style="overflow-x: auto;">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Produto</th>
                            <th>Marca</th>
                            <th>Tamanhos</th>
                            <th>Est. Mínimo</th>
                            <th>Est. Atual</th>
                            <th>Fornecedor</th>
                            <th>Preço Custo</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="produtos-table-body"></tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="toast-container"></div>
    <?php include '_partials/menu_admin.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.getElementById('produtos-table-body');
            const btnGerarSugestao = document.getElementById('btn-gerar-sugestao');
            const searchInput = document.getElementById('search-estoque');
            let produtosData = [];

            function showToast(message, type = 'success') {
                const toastContainer = document.getElementById('toast-container') || document.body;
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                toast.textContent = message;
                toastContainer.appendChild(toast);
                setTimeout(() => {
                    toast.classList.add('show');
                }, 100);
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            }

            const carregarProdutos = async () => {
                tableBody.innerHTML = '<tr><td colspan="9" style="text-align:center;">Carregando produtos...</td></tr>';
                try {
                    const response = await fetch('api/api_compras.php?acao=listar_produtos_completo');
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);

                    produtosData = resultado.dados;
                    renderizarProdutos(produtosData);
                } catch (error) {
                    tableBody.innerHTML = `<tr><td colspan="9" style="text-align:center;color:red;">Erro: ${error.message}</td></tr>`;
                }
            };

            const renderizarProdutos = (produtos) => {
                tableBody.innerHTML = '';
                let fornecedorAtual = null;

                produtos.forEach(p => {
                        const nomeFornecedor = p.fornecedor_nome || 'Sem Fornecedor Definido';
                        if (nomeFornecedor !== fornecedorAtual) {
                            fornecedorAtual = nomeFornecedor;
                            tableBody.innerHTML += `<tr class="fornecedor-group-header"><td colspan="9"><i class="fa-solid fa-truck"></i> ${fornecedorAtual}</td></tr>`;
                        }

                        let classeEstoque = '';
                        if (parseInt(p.estoque_atual) <= 0) classeEstoque = 'estoque-zerado';
                        else if (parseInt(p.estoque_atual) <= parseInt(p.estoque_minimo)) classeEstoque = 'estoque-baixo';

                        let tamanhosHtml = '-';
                        if (p.variacoes && p.variacoes.length > 0) {
                            tamanhosHtml = p.variacoes.map(v => `<span class="tamanho-badge">${v.tamanho}: ${v.quantidade}</span>`).join(' ');
                        }

                        const tr = document.createElement('tr');
                        tr.className = classeEstoque;
                        tr.innerHTML = `
                            <td><img src="${p.imagem_url || 'assets/img/placeholder.png'}" class="product-image-cell"></td>
                            <td>${p.nome}</td>
                            <td>${p.marca || '-'}</td>
                            <td>${tamanhosHtml}</td>
                            <td>${p.estoque_minimo}</td>
                            <td><strong>${p.estoque_atual}</strong></td>
                            <td>${p.fornecedor_nome || '-'}</td>
                            <td>R$ ${parseFloat(p.preco_custo).toFixed(2).replace('.',',')}</td>
                            <td><button class="btn btn-sm btn-primario btn-pedir" data-produto-id="${p.id}"><i class="fa-solid fa-cart-plus"></i> Incluir no Pedido</button></td>
                        `;
                        tableBody.appendChild(tr);
                    });
            };

            const filtrarProdutos = (termo) => {
                const termoLower = termo.toLowerCase();
                const produtosFiltrados = produtosData.filter(produto => 
                    produto.nome.toLowerCase().includes(termoLower) ||
                    (produto.marca && produto.marca.toLowerCase().includes(termoLower)) ||
                    (produto.sku && produto.sku.toLowerCase().includes(termoLower))
                );
                renderizarProdutos(produtosFiltrados);
            };

            tableBody.addEventListener('click', async e => {
                const btnPedir = e.target.closest('.btn-pedir');
                if (btnPedir) {
                    const produtoId = btnPedir.dataset.produtoId;
                    const produto = produtosData.find(p => p.id == produtoId);

                    if (!produto || !produto.fornecedor_id) {
                        showToast('Este produto não tem um fornecedor associado.', 'error');
                        return;
                    }

                    let quantidade = 1;
                    let tamanho = null;

                    if (produto.tipo === 'ROUPA' || produto.tipo === 'CALCADO') {
                        const tamanhosDisponiveis = produto.variacoes.map(v => v.tamanho).join(', ');
                        tamanho = prompt(`Digite o TAMANHO desejado para "${produto.nome}":\n(Opções: ${tamanhosDisponiveis})`);
                        if (!tamanho) return;
                    }

                    const qtdInput = prompt(`Digite a QUANTIDADE para "${produto.nome}"${tamanho ? ` (Tamanho: ${tamanho})` : ''}:`, "1");
                    if (!qtdInput || isNaN(qtdInput) || parseInt(qtdInput) <= 0) {
                        return;
                    }
                    quantidade = parseInt(qtdInput);

                    try {
                        const response = await fetch('api/api_compras.php?acao=adicionar_item_pedido', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                produto_id: produtoId,
                                fornecedor_id: produto.fornecedor_id,
                                quantidade: quantidade,
                                tamanho: tamanho
                            })
                        });
                        const resultado = await response.json();
                        if (resultado.sucesso) {
                            showToast(resultado.mensagem);
                            btnPedir.disabled = true;
                            btnPedir.innerHTML = '<i class="fa-solid fa-check"></i> Na Lista';
                            btnPedir.classList.remove('btn-primario');
                            btnPedir.classList.add('btn-sucesso');
                        } else {
                            throw new Error(resultado.mensagem);
                        }
                    } catch (error) {
                        showToast('Erro: ' + error.message, 'error');
                    }
                }
            });

            btnGerarSugestao.addEventListener('click', async () => {
                if (!confirm('Deseja que o sistema crie pedidos automaticamente para todos os produtos com estoque baixo?')) {
                    return;
                }
                const originalText = btnGerarSugestao.innerHTML;
                btnGerarSugestao.disabled = true;
                btnGerarSugestao.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Gerando...';
                try {
                    const response = await fetch('api/api_compras.php?acao=gerar_sugestao_pedido', {
                        method: 'POST'
                    });
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        showToast(resultado.mensagem);
                        if (confirm(resultado.mensagem + '\n\nDeseja ir para a página de pedidos agora?')) {
                            window.location.href = 'pedidos.php';
                        }
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    showToast('Erro: ' + error.message, 'error');
                } finally {
                    btnGerarSugestao.disabled = false;
                    btnGerarSugestao.innerHTML = originalText;
                }
            });

            // Função para fazer scroll até um produto específico
            const scrollToProduto = (produtoId) => {
                const produtoRow = document.querySelector(`[data-produto-id="${produtoId}"]`);
                if (produtoRow) {
                    produtoRow.closest('tr').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    produtoRow.closest('tr').style.backgroundColor = '#fff3cd';
                    setTimeout(() => {
                        produtoRow.closest('tr').style.backgroundColor = '';
                    }, 3000);
                }
            };

            // Event listener para o campo de busca
            searchInput.addEventListener('input', (e) => {
                const termo = e.target.value.trim();
                if (termo === '') {
                    renderizarProdutos(produtosData);
                } else {
                    filtrarProdutos(termo);
                }
            });

            // Verificar se há um produto_id na URL
            const urlParams = new URLSearchParams(window.location.search);
            const produtoIdFromUrl = urlParams.get('produto_id');

            carregarProdutos().then(() => {
                // Se há um produto_id na URL, fazer scroll até ele
                if (produtoIdFromUrl) {
                    setTimeout(() => {
                        scrollToProduto(produtoIdFromUrl);
                    }, 500); // Aguardar um pouco para garantir que a tabela foi renderizada
                }
            });
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
    

</body>

</html>