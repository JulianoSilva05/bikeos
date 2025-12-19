<?php $token = htmlspecialchars($_GET['token'] ?? ''); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Estoque | BikeOS Oficina de Bike</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { background-color: #f4f7f9; }
        .header-publico { text-align: center; margin-bottom: 2rem; }
        .tamanho-badge {
            background-color: #e2e3e5; color: #343a40; padding: 3px 8px;
            border-radius: 5px; font-size: 0.8em; margin-right: 5px;
            white-space: nowrap; display: inline-block; margin-top: 2px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="#"><img src="assets/img/images.jpeg" alt="Logo" class="navbar-logo"></a>
    </nav>
    <main class="container">
        <div class="card">
            <div id="conteudo-estoque">
                <p style="text-align:center;">Carregando dados...</p>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = '<?php echo $token; ?>';
            const container = document.getElementById('conteudo-estoque');

            if (!token) {
                container.innerHTML = '<p class="error-message">Erro: Token de acesso ausente.</p>';
                return;
            }

            try {
                const response = await fetch(`api/public_api.php?acao=get_fornecedor_estoque&token=${token}`);
                const resultado = await response.json();

                if (!resultado.sucesso) {
                    throw new Error(resultado.mensagem);
                }

                const { fornecedor, produtos } = resultado.dados;
                document.title = `Estoque de ${fornecedor.nome_empresa} | Dengo Bike`;

                let produtosHtml = '';
                if (produtos.length > 0) {
                    produtos.forEach(p => {
                        let estoqueDetalhe = `<strong>${p.estoque_atual}</strong>`;
                        if (p.variacoes && p.variacoes.length > 0) {
                            estoqueDetalhe = p.variacoes.map(v => `<span class="tamanho-badge">${v.tamanho}: ${v.quantidade}</span>`).join(' ');
                        }
                        produtosHtml += `
                            <tr>
                                <td>${p.nome}</td>
                                <td>${p.sku || '-'}</td>
                                <td>${estoqueDetalhe}</td>
                            </tr>
                        `;
                    });
                } else {
                    produtosHtml = '<tr><td colspan="3" style="text-align:center;">Nenhum produto encontrado.</td></tr>';
                }

                container.innerHTML = `
                    <div class="header-publico">
                        <h1><i class="fa-solid fa-boxes-stacked"></i> Consulta de Estoque</h1>
                        <h2>Fornecedor: ${fornecedor.nome_empresa}</h2>
                        <p>Última atualização: ${new Date().toLocaleString('pt-BR')}</p>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="tabela">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>SKU</th>
                                    <th>Estoque Atual</th>
                                </tr>
                            </thead>
                            <tbody>${produtosHtml}</tbody>
                        </table>
                    </div>
                `;

            } catch (error) {
                container.innerHTML = `<p class="error-message">Não foi possível carregar os dados. Motivo: ${error.message}</p>`;
            }
        });
    </script>
    
    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>
</html>