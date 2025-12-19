<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Novo Pedido de Compra'); ?></title>
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
            background-color: #fff3cd;
        }
        .estoque-zerado {
            background-color: #f8d7da;
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
            <h1><i class="fa-solid fa-clipboard-list"></i> Novo Pedido de Compra</h1>
        </div>
        <div class="card">
            <h2>Selecione os Produtos para Adicionar ao Pedido</h2>
            <div style="overflow-x: auto;">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Foto</th><th>Produto</th><th>Marca</th><th>Tamanhos</th><th>Est. Mínimo</th><th>Est. Atual</th><th>Fornecedor</th><th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="produtos-table-body">
                        <tr><td colspan="8" style="text-align:center;">Carregando produtos...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include '_partials/menu_admin.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.getElementById('produtos-table-body');

            const carregarProdutos = async () => {
                tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">Carregando produtos...</td></tr>';
                try {
                    const response = await fetch('api/api_compras.php?acao=listar_produtos_completo');
                    const resultado = await response.json();
                    tableBody.innerHTML = '';
                    if (resultado.sucesso) {
                        resultado.dados.forEach(p => {
                            let classeEstoque = '';
                            if (p.estoque_atual <= 0) classeEstoque = 'estoque-zerado';
                            else if (p.estoque_atual <= p.estoque_minimo) classeEstoque = 'estoque-baixo';
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
                                <td><button class="btn btn-sm btn-primario btn-pedir" data-produto-id="${p.id}" data-fornecedor-id="${p.fornecedor_id || ''}"><i class="fa-solid fa-cart-plus"></i> Pedir</button></td>
                            `;
                            tableBody.appendChild(tr);
                        });
                    }
                } catch(e) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="error-message">Erro ao carregar produtos.</td></tr>';
                }
            };
            
            tableBody.addEventListener('click', async e => {
                if(e.target.closest('.btn-pedir')) {
                    const btn = e.target.closest('.btn-pedir');
                    const produtoId = btn.dataset.produtoId;
                    const fornecedorId = btn.dataset.fornecedorId;
                    if(!fornecedorId) {
                        alert('Este produto não tem um fornecedor associado. Por favor, edite o produto e defina um fornecedor.');
                        return;
                    }
                    const qtd = prompt("Qual a quantidade que deseja adicionar ao pedido?", "1");
                    if (qtd && !isNaN(qtd) && qtd > 0) {
                        try {
                            const response = await fetch('api/api_compras.php?acao=adicionar_item_pedido', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ produto_id: produtoId, fornecedor_id: fornecedorId, quantidade: qtd })
                            });
                            const resultado = await response.json();
                            if (resultado.sucesso) {
                                alert(resultado.mensagem);
                            } else {
                                alert('Erro: ' + resultado.mensagem);
                            }
                        } catch (error) {
                            alert('Ocorreu um erro ao tentar adicionar o item ao pedido. Verifique a conexão e tente novamente.');
                        }
                    }
                }
            });

            carregarProdutos();
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>
</html>