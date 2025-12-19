<?php
// ARQUIVO: produtos.php
require_once 'api/db_connection.php';
try {
    // Busca TODOS os produtos que têm estoque
    $stmt_produtos = $pdo->query("
        SELECT id, nome, descricao, preco_venda, imagem_url
        FROM produtos
        WHERE estoque_atual > 0
        ORDER BY nome ASC
    ");
    $produtos_em_estoque = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $produtos_em_estoque = [];
    error_log("Erro ao buscar produtos para o catálogo: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Nossos Produtos'); ?></title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root { 
            --cor-primaria: #fbcd00; 
            --cor-fundo-escuro: #1a1a1a; 
            --cor-fundo-card: #2c2c2c; 
            --cor-texto: #f0f0f0; 
            --cor-texto-secundario: #a0a0a0; /* Adicionado para texto secundário */
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--cor-fundo-escuro); color: var(--cor-texto); }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        h1 { font-size: 2.5rem; text-align: center; margin-bottom: 3rem; text-transform: uppercase; }
        h1 span { color: var(--cor-primaria); }
        .produtos-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; }
        .produto-card { background-color: var(--cor-fundo-card); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; }
        .produto-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        .produto-card img { width: 100%; height: 200px; object-fit: cover; }
        .produto-card-content { padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column; }
        .produto-card-content h3 { font-size: 1.4rem; margin-bottom: 0.5rem; color: var(--cor-texto); }
        .produto-card-content p { font-size: 0.9rem; margin-bottom: 1rem; flex-grow: 1; color: var(--cor-texto-secundario); } /* Usando a variável */
        .produto-card-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem; margin-top: 1rem; }
        .produto-card .price { font-size: 1.8rem; font-weight: 700; color: var(--cor-primaria); }
        .produto-card .whatsapp-icon { font-size: 1.5rem; color: #25D366; }
        .btn { display: inline-block; padding: 1rem 2.5rem; background-color: var(--cor-primaria); color: white; font-weight: 700; text-decoration: none; border-radius: 50px; transition: all 0.3s ease; border: 2px solid var(--cor-primaria); cursor: pointer; }
        .btn-secundario { background-color: transparent; color: var(--cor-primaria); }
        .btn-secundario:hover { background-color: var(--cor-primaria); color: white; }
        
        /* Estilos para o Modal de Contato de Produto */
        .modal-backdrop {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.8); /* Fundo semi-transparente mais escuro */
            align-items: center; /* Centraliza verticalmente */
            justify-content: center; /* Centraliza horizontalmente */
        }

        .modal-content {
            background-color: #1a1a1a; /* Fundo do card do modal mais escuro */
            margin: auto; /* Remove o margin fixo para centralizar melhor com flex */
            padding: 2.5rem; /* Aumenta um pouco o padding */
            border: 1px solid #444; /* Borda um pouco mais visível */
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5); /* Adiciona sombra */
            color: var(--cor-texto); /* Garante que o texto dentro do modal seja claro */
        }

        .modal-close {
            color: var(--cor-texto-secundario); /* Cor do 'x' */
            float: right;
            font-size: 32px; /* Tamanho maior */
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            transition: color 0.3s ease;
        }

        .modal-close:hover,
        .modal-close:focus {
            color: var(--cor-primaria); /* Cor ao passar o mouse */
            text-decoration: none;
            cursor: pointer;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--cor-texto); /* Cor da label para contraste */
        }

        .form-input {
            width: 100%;
            padding: 0.8rem;
            border-radius: 8px;
            border: 1px solid #555;
            background: #2c2c2c; /* Fundo do input mais escuro */
            color: var(--cor-texto); /* Texto do input claro */
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--cor-primaria);
        }
        .btn-destaque { /* Estilo para o botão de enviar no modal */
            background-color: var(--cor-primaria);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-destaque:hover {
            background-color: #e65c00; /* Um tom ligeiramente mais escuro ao hover */
        }
    </style>
</head>
<body>
    <?php include '_partials/navigation_header.php'; ?>
    
    <main class="container">

        <h1>Nossos <span>Produtos</span></h1>
        <div class="produtos-grid">
            <?php if (empty($produtos_em_estoque)): ?>
                <p style="text-align:center; grid-column: 1 / -1;">Nenhum produto em estoque no momento.</p>
            <?php else: ?>
                <?php foreach ($produtos_em_estoque as $produto): ?>
                    <div class="produto-card" 
                         data-nome="<?php echo htmlspecialchars($produto['nome']); ?>"
                         data-descricao="<?php echo htmlspecialchars($produto['descricao']); ?>"
                         data-preco="<?php echo number_format($produto['preco_venda'], 2, ',', '.'); ?>">
                        <img src="<?php echo htmlspecialchars($produto['imagem_url'] ?? 'assets/img/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                        <div class="produto-card-content">
                            <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($produto['descricao'], 0, 100)) . (strlen($produto['descricao']) > 100 ? '...' : ''); ?></p>
                            <div class="produto-card-footer">
                                <span class="price">R$ <?php echo number_format($produto['preco_venda'], 2, ',', '.'); ?></span>
                                <i class="fa-brands fa-whatsapp whatsapp-icon"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <div id="contato-produto-modal" class="modal-backdrop">
        <div class="modal-content">
            <span class="modal-close" id="close-modal-btn">&times;</span>
            <h3 style="color:var(--cor-primaria); margin-bottom:1rem;">Tenho interesse no produto!</h3>
            <p style="color:var(--cor-texto-secundario);">Preencha seus dados para que possamos enviar uma mensagem personalizada sobre o produto <strong id="modal-produto-nome" style="color:var(--cor-primaria);"></strong>.</p>
            <form id="form-contato-produto">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="cliente-nome" class="form-label">Seu Nome</label>
                    <input type="text" id="cliente-nome" class="form-input" required>
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="cliente-whatsapp" class="form-label">Seu WhatsApp (com DDD)</label>
                    <input type="tel" id="cliente-whatsapp" class="form-input" required>
                </div>
                <button type="submit" class="btn btn-destaque" style="width:100%;">Enviar Mensagem</button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const produtoCards = document.querySelectorAll('.produto-card');
        const modal = document.getElementById('contato-produto-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const formContatoProduto = document.getElementById('form-contato-produto');
        const modalProdutoNome = document.getElementById('modal-produto-nome');
        let produtoSelecionado = {};

        produtoCards.forEach(card => {
            card.addEventListener('click', () => {
                produtoSelecionado = {
                    nome: card.dataset.nome,
                    descricao: card.dataset.descricao,
                    preco: card.dataset.preco
                };
                modalProdutoNome.textContent = produtoSelecionado.nome;
                modal.style.display = 'flex'; // Usar flex para centralizar
            });
        });

        if(closeModalBtn) {
            closeModalBtn.addEventListener('click', () => { modal.style.display = 'none'; });
        }
        
        window.addEventListener('click', (event) => {
            if (event.target == modal) { modal.style.display = 'none'; }
        });

        if(formContatoProduto) {
            formContatoProduto.addEventListener('submit', (e) => {
                e.preventDefault();
                const clienteNome = document.getElementById('cliente-nome').value;
                const clienteWhatsapp = document.getElementById('cliente-whatsapp').value.replace(/\D/g, '');
                const telefoneDestino = '5532999628568'; // SEU NÚMERO DE WHATSAPP
                let mensagem = `Olá! Meu nome é ${clienteNome} e tenho interesse no produto:\n\n*Produto:* ${produtoSelecionado.nome}\n*Descrição:* ${produtoSelecionado.descricao}\n*Valor:* R$ ${produtoSelecionado.preco}\n\nMeu contato é ${clienteWhatsapp}. Aguardo retorno, obrigado!`;
                const linkWhatsApp = `https://wa.me/${telefoneDestino}?text=${encodeURIComponent(mensagem)}`;
                window.open(linkWhatsApp, '_blank');
                modal.style.display = 'none';
                formContatoProduto.reset();
            });
        }
    });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>
</html>