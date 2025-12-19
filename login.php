<?php
session_start();
// Se o usuário já estiver logado, redireciona para o dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#fbcd00">
    <title>Login | BikeOS</title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .pwa-banner {
            background: linear-gradient(135deg, #fbcd00, #6f42c1);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: none;
            animation: slideDown 0.5s ease-out;
        }
        .pwa-banner.show {
            display: block;
        }
        .pwa-banner h3 {
            margin: 0 0 10px 0;
            font-size: 1.2em;
            font-weight: 600;
        }
        .pwa-banner p {
            margin: 0 0 15px 0;
            font-size: 0.9em;
            opacity: 0.9;
        }
        .pwa-install-btn {
            background: rgba(255,255,255,0.2);
            border: 2px solid white;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 10px;
        }
        .pwa-install-btn:hover {
            background: white;
            color: #6f42c1;
        }
        .pwa-dismiss-btn {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.5);
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.8em;
            transition: all 0.3s ease;
        }
        .pwa-dismiss-btn:hover {
            background: rgba(255,255,255,0.1);
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    </head>
<body class="login-page">
    <!-- Banner de Instalação PWA -->
    <div id="pwa-banner" class="pwa-banner">
        <h3><i class="fa-solid fa-mobile-screen-button"></i> Instale o BikeOS no seu dispositivo!</h3>
        <p>Tenha acesso rápido ao sistema direto da tela inicial do seu celular ou computador. Funciona offline e é mais rápido!</p>
        <button id="pwa-install-btn" class="pwa-install-btn">
            <i class="fa-solid fa-download"></i> Instalar App
        </button>
        <button id="pwa-dismiss-btn" class="pwa-dismiss-btn">
            <i class="fa-solid fa-times"></i> Agora não
        </button>
    </div>
    
    <div class="card login-card">
        <div class="login-header">
            <img src="assets/img/logo.png" alt="Logo">
            <h2>Acesso ao Sistema</h2>
        </div>
        
        <form id="login-form">
            <div id="error-message" class="error-message" style="display: none;"></div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-input" placeholder="seuemail@exemplo.com" required>
                </div>
            </div>

            <div class="form-group">
                <label for="senha" class="form-label">Senha</label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="senha" name="senha" class="form-input" placeholder="Sua senha" required>
                </div>
            </div>

            <button type="submit" class="btn btn-destaque" style="width: 100%;">
                <i class="fa-solid fa-right-to-bracket"></i> Entrar
            </button>
        </form>
    </div>

<script>
// PWA Installation Management
let deferredPrompt;
const pwaInstallBanner = document.getElementById('pwa-banner');
const pwaInstallBtn = document.getElementById('pwa-install-btn');
const pwaDismissBtn = document.getElementById('pwa-dismiss-btn');

// Função para mostrar o banner PWA
function showPWABanner() {
    // Verificar se já está instalado como PWA
    if (window.matchMedia('(display-mode: standalone)').matches) {
        return;
    }
    
    // Verificar se o usuário já dispensou o banner recentemente (24 horas)
    const dismissedTime = localStorage.getItem('pwa-banner-dismissed-time');
    const now = new Date().getTime();
    const oneDayInMs = 24 * 60 * 60 * 1000;
    
    if (!dismissedTime || (now - parseInt(dismissedTime)) > oneDayInMs) {
        pwaInstallBanner.classList.add('show');
    }
}

// Detectar se o PWA pode ser instalado
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    showPWABanner();
});

// Mostrar banner sempre que carregar a página de login
window.addEventListener('load', () => {
    // Aguardar um pouco para garantir que todos os elementos estejam carregados
    setTimeout(showPWABanner, 1000);
});

// Instalar PWA
pwaInstallBtn.addEventListener('click', async () => {
    if (!deferredPrompt) return;
    
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    
    if (outcome === 'accepted') {
        console.log('PWA instalado com sucesso!');
    }
    
    deferredPrompt = null;
    pwaInstallBanner.classList.remove('show');
});

// Dispensar banner
pwaDismissBtn.addEventListener('click', () => {
    pwaInstallBanner.classList.remove('show');
    const now = new Date().getTime();
    localStorage.setItem('pwa-banner-dismissed-time', now.toString());
});

// Adicionar botão para mostrar banner manualmente (para teste)
function showPWABannerManually() {
    localStorage.removeItem('pwa-banner-dismissed-time');
    showPWABanner();
}



document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const errorMessage = document.getElementById('error-message');
    const submitButton = loginForm.querySelector('button[type="submit"]');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        errorMessage.style.display = 'none';
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Entrando...';
        const dadosLogin = { email: document.getElementById('email').value, senha: document.getElementById('senha').value };

        try {
            const loginResponse = await fetch('api/api_login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dadosLogin)
            });
            const loginResultado = await loginResponse.json();

            if (!loginResultado.sucesso) { throw new Error(loginResultado.mensagem); }

            const caixaResponse = await fetch('api/api_financeiro.php?acao=get_status_caixa');
            const caixaResultado = await caixaResponse.json();

            if (caixaResultado.sucesso) {
                if (caixaResultado.dados) {
                    alert("Lembrete: O caixa está ABERTO. Não se esqueça de fechá-lo ao final do expediente!");
                } else {
                    alert("Lembrete: O caixa está FECHADO. Não se esqueça de abri-lo para registrar as vendas!");
                }
            }
            
            window.location.href = 'dashboard.php';
        } catch (error) {
            errorMessage.textContent = error.message;
            errorMessage.style.display = 'block';
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> Entrar';
        }
    });
});
</script>

<!-- Sistema de Configurações Dinâmicas da Loja -->
<script src="assets/js/dynamic-config.js"></script>
</body>
</html>