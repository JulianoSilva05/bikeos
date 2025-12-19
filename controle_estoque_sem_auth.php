<?php
// Simular sessão autenticada para teste
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['empresa_id'] = 1;
$_SESSION['nome'] = 'Teste';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Estoque - Teste</title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Controle de Estoque - Teste</h1>
        
        <div id="loading" style="text-align: center; padding: 20px;">
            <i class="fas fa-spinner fa-spin"></i> Carregando...
        </div>
        
        <div id="stats-container" style="display: none;">
            <h2>Estatísticas</h2>
            <div id="stats-cards"></div>
        </div>
        
        <div id="produtos-container" style="display: none;">
            <h2>Produtos</h2>
            <div id="produtos-lista"></div>
        </div>
        
        <div id="error-container" style="display: none; color: red;">
            <h2>Erro</h2>
            <div id="error-message"></div>
        </div>
    </div>
    
    <script>
        // Testar APIs
        async function testarAPIs() {
            try {
                console.log('Testando API de estatísticas...');
                
                // Testar estatísticas
                const statsResponse = await fetch('/api/api_estoque.php?acao=estatisticas_estoque');
                const statsData = await statsResponse.json();
                
                console.log('Resposta das estatísticas:', statsData);
                
                if (statsData.sucesso) {
                    document.getElementById('stats-cards').innerHTML = `
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <h3>Total</h3>
                                <p style="font-size: 2rem; font-weight: bold;">${statsData.total || 0}</p>
                            </div>
                            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <h3>Normal</h3>
                                <p style="font-size: 2rem; font-weight: bold; color: green;">${statsData.normal || 0}</p>
                            </div>
                            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <h3>Baixo</h3>
                                <p style="font-size: 2rem; font-weight: bold; color: orange;">${statsData.baixo || 0}</p>
                            </div>
                            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <h3>Crítico</h3>
                                <p style="font-size: 2rem; font-weight: bold; color: red;">${statsData.critico || 0}</p>
                            </div>
                        </div>
                    `;
                    document.getElementById('stats-container').style.display = 'block';
                } else {
                    throw new Error('Erro nas estatísticas: ' + (statsData.erro || statsData.mensagem));
                }
                
                // Testar listagem de produtos
                console.log('Testando API de produtos...');
                const produtosResponse = await fetch('/api/api_estoque.php?acao=listar_produtos_estoque');
                const produtosData = await produtosResponse.json();
                
                console.log('Resposta dos produtos:', produtosData);
                
                if (produtosData.sucesso) {
                    const produtos = produtosData.produtos || [];
                    document.getElementById('produtos-lista').innerHTML = `
                        <p>Total de produtos encontrados: ${produtos.length}</p>
                        <div style="max-height: 400px; overflow-y: auto;">
                            ${produtos.slice(0, 10).map(produto => `
                                <div style="background: white; padding: 15px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <h4>${produto.nome}</h4>
                                    <p>SKU: ${produto.sku || 'N/A'} | Estoque: ${produto.estoque_atual} | Mínimo: ${produto.estoque_minimo}</p>
                                    <p>Fornecedor: ${produto.fornecedor_nome || 'N/A'}</p>
                                </div>
                            `).join('')}
                            ${produtos.length > 10 ? `<p>... e mais ${produtos.length - 10} produtos</p>` : ''}
                        </div>
                    `;
                    document.getElementById('produtos-container').style.display = 'block';
                } else {
                    throw new Error('Erro nos produtos: ' + (produtosData.erro || produtosData.mensagem));
                }
                
                document.getElementById('loading').style.display = 'none';
                
            } catch (error) {
                console.error('Erro ao testar APIs:', error);
                document.getElementById('error-message').textContent = error.message;
                document.getElementById('error-container').style.display = 'block';
                document.getElementById('loading').style.display = 'none';
            }
        }
        
        // Executar teste quando a página carregar
        document.addEventListener('DOMContentLoaded', testarAPIs);
    </script>
</body>
</html>