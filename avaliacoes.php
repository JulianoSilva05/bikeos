<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Avaliações dos Clientes'); ?></title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .avaliacao-card {
            background-color: var(--cor-branco);
            padding: 1.5rem;
            border-radius: var(--raio-borda);
            box-shadow: var(--sombra-card);
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .avaliacao-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--cor-borda);
            padding-bottom: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .avaliacao-header .cliente-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .avaliacao-header .cliente-info i {
            font-size: 1.8rem;
            color: var(--cor-primaria);
        }

        .avaliacao-header .cliente-info h3 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--cor-texto-primario);
        }

        .avaliacao-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .avaliacao-details p {
            margin: 0;
            color: var(--cor-texto-secundario);
        }

        .avaliacao-stars {
            font-size: 1.2rem;
            color: #ffc107;
        }

        .avaliacao-stars .fa-star-o {
            color: #ccc;
        }

        .avaliacao-comment {
            background-color: var(--cor-fundo-claro);
            padding: 1rem;
            border-radius: var(--raio-borda);
            border: 1px solid var(--cor-borda);
            font-style: italic;
            color: var(--cor-texto-primario);
        }

        .avaliacao-os-link {
            text-align: right;
            margin-top: 1rem;
        }

        .avaliacao-os-link .btn {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php"><img src="assets/img/images.jpeg" alt="Logo" class="navbar-logo"></a>
        <div class="navbar-user-info"><i class="fa-solid fa-user-circle"></i><span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span></div>
        <a href="logout.php" class="btn btn-destaque"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
    </nav>
    <main class="container">
        <div class="page-header-with-nav">
            <div class="page-title">
                <h1><i class="fa-solid fa-star"></i> Avaliações dos Clientes</h1>
            </div>
            <div class="page-navigation">
                <button onclick="history.back()" class="btn btn-secundario" title="Voltar à página anterior"><i class="fa-solid fa-arrow-left"></i> Voltar</button>
                <a href="dashboard.php" class="btn btn-secundario btn-icon-only" title="Ir para o Painel Principal"><i class="fa-solid fa-house"></i></a>
            </div>
        </div>

        <div id="avaliacoes-list">
            <p style="text-align:center;">Carregando avaliações...</p>
        </div>
    </main>

    <footer class="page-footer">
        Desenvolvido por <a href="https://bikeos.com.br" target="_blank" rel="noopener noreferrer">BikeOS</a> - Todos os direitos reservados 2025 | <a href="mailto:contato@bikeos.com.br">contato@bikeos.com.br</a>
    </footer>

    <?php include '_partials/menu_admin.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const avaliacoesListContainer = document.getElementById('avaliacoes-list');

            const renderStars = (nota) => {
                let starsHtml = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= nota) {
                        starsHtml += '<i class="fa-solid fa-star"></i>';
                    } else {
                        starsHtml += '<i class="fa-regular fa-star fa-star-o"></i>';
                    }
                }
                return `<span class="avaliacao-stars">${starsHtml}</span>`;
            };

            try {
                const response = await fetch('api/api_dashboard.php?acao=listar_todas_avaliacoes');
                const resultado = await response.json();

                avaliacoesListContainer.innerHTML = ''; // Limpa o "Carregando..."

                if (resultado.sucesso && resultado.dados.length > 0) {
                    resultado.dados.forEach(avaliacao => {
                        const avaliacaoCard = `
                            <div class="avaliacao-card">
                                <div class="avaliacao-header">
                                    <div class="cliente-info">
                                        <i class="fa-solid fa-user-circle"></i>
                                        <h3>${avaliacao.cliente_nome}</h3>
                                    </div>
                                    ${renderStars(avaliacao.nota)}
                                </div>
                                <div class="avaliacao-details">
                                    <p><strong>OS:</strong> <a href="editar_os.php?id=${avaliacao.os_id}" class="btn-link" target="_blank">#${avaliacao.os_id}</a></p>
                                    <p><strong>Bicicleta:</strong> ${avaliacao.bicicleta_modelo}</p>
                                    <p><strong>Avaliado em:</strong> ${avaliacao.data_avaliacao_formatada}</p>
                                </div>
                                ${avaliacao.comentario ? `<div class="avaliacao-comment">${avaliacao.comentario}</div>` : ''}
                                <div class="avaliacao-os-link">
                                    <a href="editar_os.php?id=${avaliacao.os_id}" class="btn btn-primario" target="_blank">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver OS
                                    </a>
                                </div>
                            </div>
                        `;
                        avaliacoesListContainer.innerHTML += avaliacaoCard;
                    });
                } else {
                    avaliacoesListContainer.innerHTML = '<div class="card"><p style="text-align:center;">Nenhuma avaliação encontrada.</p></div>';
                }

            } catch (error) {
                console.error("Erro ao carregar avaliações:", error);
                avaliacoesListContainer.innerHTML = '<div class="card"><p class="error-message" style="text-align:center;">Erro ao carregar as avaliações. Tente novamente mais tarde.</p></div>';
            }
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>
