<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <title>Alterar Senha | BikeOS</title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php"><img src="assets/img/images.jpeg" alt="Logo" class="navbar-logo"></a>
        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php" class="btn btn-destaque">Sair</a>
    </nav>

    <main class="container" style="max-width: 600px;">
        <div class="page-header-with-nav">
            <div class="page-title">
                <h1><i class="fa-solid fa-key"></i> Alterar Senha de Acesso</h1>
            </div>
            <div class="page-navigation">
                <a href="dashboard.php" class="btn btn-secundario" title="Voltar ao Painel Principal"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
            </div>
        </div>

        <div class="card">
            <form id="form-alterar-senha">
                <div class="form-group">
                    <label for="email" class="form-label">Seu Email (Login)</label>
                    <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" readonly>
                    <small>Este campo não pode ser alterado.</small>
                </div>
                <div class="form-group">
                    <label for="senha_atual" class="form-label">Senha Atual</label>
                    <input type="password" id="senha_atual" name="senha_atual" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="nova_senha" class="form-label">Nova Senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="confirmar_nova_senha" class="form-label">Confirme a Nova Senha</label>
                    <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" class="form-input" required>
                </div>
                <button type="submit" class="btn btn-destaque" style="width: 100%;">Salvar Nova Senha</button>
            </form>
        </div>
    </main>

    <?php include '_partials/menu_admin.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('form-alterar-senha');
            
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = e.target.querySelector('button[type="submit"]');
                const novaSenha = document.getElementById('nova_senha').value;
                const confirmarSenha = document.getElementById('confirmar_nova_senha').value;

                if (novaSenha.length < 6) {
                    alert("A nova senha deve ter pelo menos 6 caracteres.");
                    return;
                }

                if (novaSenha !== confirmarSenha) {
                    alert("As novas senhas não coincidem. Por favor, digite novamente.");
                    document.getElementById('confirmar_nova_senha').focus();
                    return;
                }

                const formData = new FormData(form);
                const dados = Object.fromEntries(formData.entries());

                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';

                try {
                    const response = await fetch('api/api_auth.php?acao=alterar_senha', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(dados)
                    });

                    const resultado = await response.json();
                    
                    if (resultado.sucesso) {
                        alert(resultado.mensagem);
                        window.location.href = 'dashboard.php'; // Redireciona para o dashboard
                    } else {
                        throw new Error(resultado.mensagem);
                    }
                } catch (error) {
                    alert('Erro: ' + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Salvar Nova Senha';
                }
            });
        });
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>
</html>