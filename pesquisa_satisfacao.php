<?php
// ARQUIVO: pesquisa_satisfacao.php (PÁGINA PÚBLICA PARA O CLIENTE)
require_once 'api/db_connection.php'; // Inclui a conexão com o banco de dados

$token = $_GET['token'] ?? '';
$pesquisa_info = null;
$error_message = '';
$success_message = '';

if (!empty($token)) {
    try {
        $stmt = $pdo->prepare("SELECT id, os_id, status, data_resposta FROM pesquisas_satisfacao WHERE link_acesso LIKE ?");
        $stmt->execute(["%token=" . $token]);
        $pesquisa_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pesquisa_info) {
            $error_message = "Link de pesquisa inválido ou expirado.";
        } elseif ($pesquisa_info['status'] === 'RESPONDIDA') {
            $success_message = "Obrigado! Você já respondeu a esta pesquisa em " . date('d/m/Y H:i', strtotime($pesquisa_info['data_resposta'])) . ".";
        } elseif ($pesquisa_info['status'] === 'EXPIRADA') {
            $error_message = "Esta pesquisa expirou.";
        }

    } catch (PDOException $e) {
        error_log("Erro ao buscar pesquisa de satisfação: " . $e->getMessage());
        $error_message = "Ocorreu um erro ao carregar a pesquisa. Tente novamente mais tarde.";
    }
} else {
    $error_message = "Token da pesquisa não fornecido.";
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php require_once 'helpers/empresa_helper.php'; echo getTituloComEmpresa('Pesquisa de Satisfação'); ?></title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .navbar {
            display: none; /* Esconde a navbar nesta página pública */
        }

        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 1.5rem;
        }

        .card {
            background-color: var(--cor-branco);
            border-radius: var(--raio-borda);
            box-shadow: var(--sombra-card);
            padding: 2rem;
            text-align: center;
        }

        .card h2 {
            font-size: 1.8rem;
            color: var(--cor-primaria);
            margin-bottom: 1.5rem;
        }

        .rating-stars {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
            direction: rtl; /* Para alinhar as estrelas da direita para a esquerda */
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            font-size: 2.8rem; /* Estrelas maiores */
            color: #ccc; /* Cor das estrelas vazias */
            cursor: pointer;
            transition: color 0.2s;
            padding: 0 5px; /* Espaçamento entre as estrelas */
        }

        .rating-stars input[type="radio"]:checked ~ label,
        .rating-stars label:hover,
        .rating-stars label:hover ~ label {
            color: #ffc107; /* Cor das estrelas preenchidas */
        }

        .rating-stars input[type="radio"]:checked + label {
            color: #ffc107;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--cor-texto-primario);
        }

        .form-input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            font-size: 1rem;
            color: var(--cor-texto-primario);
            background-color: var(--cor-branco);
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--cor-primaria);
        }

        .btn-destaque {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            margin-top: 1rem;
        }

        .message-box {
            padding: 1rem;
            border-radius: var(--raio-borda);
            margin-top: 1.5rem;
            font-weight: bold;
        }

        .message-box.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c2c7;
        }

        .message-box.success {
            background-color: #d1e7dd;
            color: #0a3622;
            border: 1px solid #badbcc;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <?php if ($error_message): ?>
                <div class="message-box error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_message; ?>
                </div>
            <?php elseif ($success_message): ?>
                <div class="message-box success">
                    <i class="fa-solid fa-circle-check"></i> <?php echo $success_message; ?>
                </div>
            <?php elseif ($pesquisa_info && $pesquisa_info['status'] === 'ENVIADA'): ?>
                <h2><i class="fa-solid fa-star"></i> Sua Opinião é Muito Importante!</h2>
                <p>Por favor, avalie sua experiência com a <?php require_once 'helpers/empresa_helper.php'; require_once 'api/db_connection.php'; echo getNomeEmpresa($pdo); ?>.</p>
                <form id="form-pesquisa-satisfacao">
                    <input type="hidden" id="token-pesquisa" value="<?php echo htmlspecialchars($token); ?>">
                    <div class="rating-stars">
                        <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="5 estrelas"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 estrelas"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 estrelas"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 estrelas"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 estrela"><i class="fa-solid fa-star"></i></label>
                    </div>
                    <div class="form-group">
                        <label for="comentario" class="form-label">Deixe um comentário (opcional):</label>
                        <textarea id="comentario" class="form-input" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-destaque">Enviar Avaliação</button>
                </form>
            <?php else: ?>
                 <div class="message-box error">
                    <i class="fa-solid fa-circle-exclamation"></i> Ocorreu um erro inesperado ao carregar a pesquisa.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const formPesquisa = document.getElementById('form-pesquisa-satisfacao');
            if (formPesquisa) {
                formPesquisa.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const token = document.getElementById('token-pesquisa').value;
                    const nota = formPesquisa.querySelector('input[name="rating"]:checked');
                    const comentario = document.getElementById('comentario').value;

                    if (!nota) {
                        alert('Por favor, selecione uma nota de 1 a 5 estrelas.');
                        return;
                    }

                    const payload = {
                        token: token,
                        nota: parseInt(nota.value),
                        comentario: comentario
                    };

                    const btn = formPesquisa.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enviando...';

                    try {
                        const response = await fetch('api/api_gestao_avancada.php?acao=salvar_resposta_pesquisa', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });
                        const resultado = await response.json();

                        if (resultado.sucesso) {
                            document.querySelector('.card').innerHTML = `
                                <div class="message-box success">
                                    <i class="fa-solid fa-circle-check"></i> ${resultado.mensagem}
                                </div>
                            `;
                        } else {
                            throw new Error(resultado.mensagem);
                        }
                    } catch (error) {
                        alert('Erro ao enviar avaliação: ' + error.message);
                        btn.disabled = false;
                        btn.innerHTML = 'Enviar Avaliação';
                    }
                });
            }
        });
    </script>
    
    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>
