<?php require_once __DIR__ . '/promocao.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>BikeOS - Sistema de Gestão Completo para Oficinas de Bicicletas | Software ERP</title>
    <meta name="description" content="🚴‍♂️ BikeOS: Sistema ERP completo para oficinas de bicicletas. Gestão de vendas, estoque, ordens de serviço, financeiro e clientes. Teste grátis por 30 dias! Aumente sua produtividade em 300%.">
    <meta name="keywords" content="sistema gestão oficina bicicleta, software bicicletaria, ERP bike shop, controle vendas bicicleta, gestão estoque bike, ordem serviço bicicleta, software oficina bike, sistema bicicletaria, gestão financeira bike shop">
    <meta name="author" content="BikeOS">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot" content="index, follow">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://bikeos.com.br/">
    <meta property="og:title" content="BikeOS - Sistema de Gestão Completo para Oficinas de Bicicletas">
    <meta property="og:description" content="Sistema ERP completo para oficinas de bicicletas. Gestão de vendas, estoque, ordens de serviço e financeiro. Teste grátis!">
    <meta property="og:image" content="https://bikeos.com.br/assets/img/logo_grande.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="BikeOS">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://bikeos.com.br/">
    <meta property="twitter:title" content="BikeOS - Sistema de Gestão para Oficinas de Bicicletas">
    <meta property="twitter:description" content="Sistema ERP completo para oficinas de bicicletas. Gestão de vendas, estoque, ordens de serviço e financeiro.">
    <meta property="twitter:image" content="https://bikeos.com.br/assets/img/logo_grande.png">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://bikeos.com.br/">
    
    <!-- Favicon and Icons -->
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/logo.png">
    <meta name="theme-color" content="#fbcd00">
    <meta name="msapplication-TileColor" content="#fbcd00">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "BikeOS",
      "description": "Sistema ERP completo para gestão de oficinas de bicicletas",
      "url": "https://bikeos.com.br",
      "applicationCategory": "BusinessApplication",
      "operatingSystem": "Web Browser",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "BRL",
        "description": "Teste grátis por 30 dias"
      },
      "provider": {
        "@type": "Organization",
        "name": "BikeOS",
        "url": "https://bikeos.com.br"
      },
      "featureList": [
        "Gestão de Vendas",
        "Controle de Estoque",
        "Ordens de Serviço",
        "Gestão Financeira",
        "Cadastro de Clientes",
        "Relatórios Gerenciais"
      ]
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #fbcd00;
            --secondary-color: #6f42c1;
            --dark-bg: #0a0a0a;
            --card-bg: #1a1a1a;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --success-color: #28a745;
            --gradient-primary: linear-gradient(135deg, #fbcd00 0%, #ffd633 100%);
            --gradient-secondary: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);
            --shadow-light: 0 4px 20px rgba(251, 205, 0, 0.1);
            --shadow-heavy: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: var(--gradient-primary);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-align: center;
        }

        .form-section .btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #17a2b8 100%);
            padding: 1.2rem 3rem;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-section .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .form-section .btn:hover {
            background: linear-gradient(135deg, #218838 0%, #1e7e34 50%, #155724 100%);
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 20px 40px rgba(40, 167, 69, 0.4);
        }

        .form-section .btn:hover::before {
            left: 100%;
        }

        .form-section .btn:active {
            transform: translateY(-2px) scale(1.01);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-light);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: radial-gradient(ellipse at center, rgba(251, 205, 0, 0.1) 0%, transparent 70%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23333" stroke-width="0.5" opacity="0.3"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.1;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .hero-text {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .hero-text h1 {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .hero-text p {
            text-align: center;
            margin-bottom: 2rem;
        }

        .slogan {
            text-align: center !important;
            margin: 1.5rem 0;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .main-logo {
            max-width: 300px;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 10px 30px rgba(251, 205, 0, 0.3));
            animation: logoFloat 3s ease-in-out infinite;
        }

        .slogan {
            margin: 1.5rem 0;
            text-align: center;
        }

        .slogan p {
            font-size: 1.2rem;
            color: var(--secondary-color);
            font-weight: 500;
            letter-spacing: 0.5px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .slogan em {
            font-style: italic;
            background: linear-gradient(135deg, var(--secondary-color), #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .hero-text h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            text-align: center;
        }

        .hero-text .highlight {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-text p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            max-width: 500px;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .hero-image {
            position: relative;
        }

        .dashboard-preview {
            width: 100%;
            border-radius: 20px;
            box-shadow: var(--shadow-heavy);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Features Section */
        .features {
            padding: 8rem 0;
            background: linear-gradient(180deg, transparent 0%, rgba(251, 205, 0, 0.05) 50%, transparent 100%);
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-light);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-primary);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.6;
        }



        /* Pricing Section */
        .pricing {
            padding: 8rem 0;
            background: var(--card-bg);
        }

        .pricing-card {
            max-width: 500px;
            margin: 0 auto;
            background: var(--dark-bg);
            border-radius: 30px;
            padding: 3rem;
            text-align: center;
            border: 2px solid var(--primary-color);
            position: relative;
            overflow: hidden;
            color: white;
        }

        .pricing-card h3 {
            color: white;
        }

        .pricing-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, var(--primary-color), transparent, var(--primary-color));
            animation: rotate 4s linear infinite;
            z-index: -1;
        }

        .pricing-card::after {
            content: '';
            position: absolute;
            inset: 2px;
            background: var(--dark-bg);
            border-radius: 28px;
            z-index: -1;
        }

        @keyframes rotate {
            to { transform: rotate(360deg); }
        }

        .price {
            font-size: 4rem;
            font-weight: 900;
            color: var(--primary-color);
            margin: 1rem 0;
        }

        .price-period {
            color: var(--text-secondary);
            font-size: 1.2rem;
        }

        .pricing-features {
            list-style: none;
            margin: 2rem 0;
            text-align: left;
        }

        .pricing-features li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
        }

        .pricing-features li strong {
            color: white;
        }

        .pricing-features li mark {
            background: transparent;
            color: white;
            padding: 0;
            border-radius: 0;
        }

        .pricing-features i {
            color: var(--success-color);
        }

        /* CTA Section */
        .cta {
            padding: 8rem 0;
            background: var(--gradient-primary);
            text-align: center;
        }

        .cta h2 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: white;
        }

        .cta p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .cta .btn {
            background: white;
            color: var(--primary-color);
            font-size: 1.1rem;
            padding: 1rem 3rem;
        }

        /* Footer */
        .footer {
            background: var(--card-bg);
            padding: 3rem 0;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Accessibility and SEO Styles */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        mark {
            background: transparent;
            color: white;
            font-weight: 700;
            padding: 0;
        }

        /* Focus styles for accessibility */
        a:focus,
        button:focus,
        input:focus,
        select:focus,
        textarea:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Skip to main content link */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: var(--primary-color);
            color: white;
            padding: 8px;
            text-decoration: none;
            z-index: 1000;
            border-radius: 4px;
        }

        .skip-link:focus {
            top: 6px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2rem;
            }

            .hero-logo {
                order: -1;
                margin-bottom: 1rem;
            }

            .hero-text {
                align-items: center;
                text-align: center;
            }

            .hero-text h1,
            .hero-text p {
                text-align: center;
            }

            .slogan {
                text-align: center !important;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }



            .hero-buttons {
                justify-content: center;
            }
        }

        /* Form Styles */
        .form-section {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 249, 250, 0.95) 100%);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 25px;
            margin-top: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #fbcd00, #ff6b35, #f7931e, #fbcd00);
            background-size: 200% 100%;
            animation: gradientShift 3s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            position: relative;
            animation: slideInLeft 0.6s ease-out;
            animation-fill-mode: both;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }
        .form-group.full-width { animation-delay: 0.7s; }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            margin-bottom: 0.8rem;
            font-weight: 700;
            color: #2c3e50;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }

        .form-group label::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 30px;
            height: 2px;
            background: linear-gradient(90deg, #fbcd00, #ff6b35);
            border-radius: 1px;
        }

        .form-input {
            padding: 1.2rem;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            border: 2px solid rgba(251, 205, 0, 0.2);
            border-radius: 15px;
            color: #2c3e50;
            font-size: 1rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .form-input:focus {
            outline: none;
            border-color: #fbcd00;
            box-shadow: 0 0 0 4px rgba(251, 205, 0, 0.1), inset 0 2px 4px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
            background: linear-gradient(145deg, #ffffff, #fefefe);
        }

        .form-input::placeholder {
            color: var(--text-secondary);
        }

        .success-message {
            background: var(--success-color);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
            display: none;
        }

        .error-message {
            background: #dc3545;
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
            display: none;
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>
    
    <header class="header" role="banner">
        <nav class="nav container" role="navigation" aria-label="Navegação principal">
            <div class="logo" aria-label="BikeOS - Sistema de Gestão">BikeOS</div>
            <ul class="nav-links" role="menubar">
                <li role="none"><a href="#inicio" role="menuitem" aria-label="Ir para seção inicial">Início</a></li>
                <li role="none"><a href="#funcionalidades" role="menuitem" aria-label="Ver funcionalidades do sistema">Funcionalidades</a></li>
                <li role="none"><a href="#preco" role="menuitem" aria-label="Ver preços e planos">Preço</a></li>
                <li role="none"><a href="#cadastro" role="menuitem" aria-label="Fazer cadastro gratuito">Cadastro</a></li>
                <?php promocao_render_nav(); ?>
            </ul>
            <a href="login.php" class="btn btn-outline" aria-label="Acessar área de login">Fazer Login</a>
        </nav>
    </header>

    <main id="main-content" role="main">
        <section id="inicio" class="hero" role="banner">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-logo">
                        <img src="assets/img/logo_grande.png" alt="BikeOS - Sistema de Gestão para Oficinas de Bicicletas" class="main-logo">
                    </div>
                    <div class="hero-text">
                        <h1>O <span class="highlight">Sistema Completo</span> para sua <mark>Oficina de Bicicletas</mark></h1>
                        <div class="slogan">
                            <p><em><span style="color: yellow;">A <strong>engrenagem perfeita</strong> para o seu negócio. <strong>Potência</strong> para crescer, <strong>simplicidade</strong> para girar.</span></em></p>
                        </div>
                        <p>Gerencie <strong>vendas</strong>, <strong>estoque</strong>, <strong>ordens de serviço</strong>, <strong>financeiro</strong> e muito mais com o <strong>BikeOS</strong> - o <mark>sistema mais completo do mercado</mark>. <strong>Automatize</strong> sua oficina e <strong>aumente seus lucros</strong>!</p>
                        <div class="hero-buttons">
                            <a href="#cadastro" class="btn" aria-label="Começar teste grátis do BikeOS">Começar Grátis</a>
                            <a href="#funcionalidades" class="btn btn-outline" aria-label="Ver todas as funcionalidades do sistema">Ver Funcionalidades</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php promocao_render_secao(); ?>

        <section id="funcionalidades" class="features" role="main">
            <div class="container">
                <header class="section-title">
                    <h2>Funcionalidades <span class="highlight">Completas</span></h2>
                    <p>Tudo que você precisa para <strong>gerenciar sua oficina</strong> de forma <strong>profissional</strong> e <strong>eficiente</strong></p>
                </header>
                <div class="features-grid" role="list">
                    <article class="feature-card" role="listitem">
                        <div class="feature-icon" aria-hidden="true">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <h3><strong>Controle de Vendas</strong></h3>
                        <p>Sistema completo de <strong>PDV</strong> com <mark>controle de estoque automático</mark>, <strong>múltiplas formas de pagamento</strong> e emissão de <strong>recibos profissionais</strong>.</p>
                    </article>
                    <article class="feature-card" role="listitem">
                        <div class="feature-icon" aria-hidden="true">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h3><strong>Ordens de Serviço</strong></h3>
                        <p>Gerencie todos os <strong>serviços da sua oficina</strong> com <mark>controle de status</mark>, <strong>fotos de avarias</strong>, <strong>orçamentos</strong> e <strong>histórico completo</strong>.</p>
                    </article>
                    <article class="feature-card" role="listitem">
                        <div class="feature-icon" aria-hidden="true">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h3><strong>Gestão de Estoque</strong></h3>
                        <p><mark>Controle completo do seu estoque</mark> com <strong>alertas de produtos em falta</strong>, <strong>entrada de mercadorias</strong> e <strong>relatórios detalhados</strong>.</p>
                    </article>
                    <article class="feature-card" role="listitem">
                        <div class="feature-icon" aria-hidden="true">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3><strong>Relatórios Financeiros</strong></h3>
                        <p><mark>Dashboard completo</mark> com <strong>gráficos</strong>, <strong>controle de caixa</strong>, <strong>contas a pagar/receber</strong> e <strong>análise de performance</strong>.</p>
                    </article>
                    <article class="feature-card" role="listitem">
                        <div class="feature-icon" aria-hidden="true">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3><strong>Gestão de Clientes</strong></h3>
                        <p><mark>Cadastro completo de clientes</mark> com <strong>histórico de compras</strong>, <strong>serviços realizados</strong> e <strong>sistema de fidelidade</strong>.</p>
                    </article>
                    <article class="feature-card" role="listitem">
                        <div class="feature-icon" aria-hidden="true">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3><strong>Acesso Mobile</strong></h3>
                        <p><mark>Sistema responsivo</mark> que funciona perfeitamente em qualquer dispositivo - <strong>computador</strong>, <strong>tablet</strong> ou <strong>smartphone</strong>.</p>
                    </article>
                    <article class="feature-card" role="listitem">
                        <div class="feature-icon" aria-hidden="true">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3><strong>Automações Inteligentes</strong></h3>
                        <p><mark>Lembretes automáticos</mark> de vencimentos, <strong>backup automático</strong> dos dados e <strong>notificações por WhatsApp</strong>.</p>
                    </article>
                    <article class="feature-card" role="listitem">
                        <div class="feature-icon" aria-hidden="true">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3><strong>Suporte Avançado</strong></h3>
                        <p><mark>Suporte técnico especializado</mark> <strong>7 dias por semana</strong> via <strong>WhatsApp</strong>, <strong>email</strong> e <strong>telefone</strong> para resolver qualquer dúvida.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="preco" class="pricing" role="complementary">
            <div class="container">
                <header class="section-title">
                    <h2>Preço <span class="highlight">Justo</span></h2>
                    <p><strong>Sem custos adicionais</strong>, <strong>sem surpresas</strong>. Tudo incluído por um <mark>preço fixo mensal</mark>.</p>
                </header>
                <article class="pricing-card">
                    <h3><strong>Plano Completo</strong></h3>
                    <div class="price">R$ <strong>69</strong><span class="price-period">/mês</span></div>
                    <ul class="pricing-features" role="list">
                        <li role="listitem"><i class="fas fa-check" aria-hidden="true"></i> <strong>Todas as funcionalidades incluídas</strong></li>
                        <li role="listitem"><i class="fas fa-check" aria-hidden="true"></i> <mark>Usuários ilimitados</mark></li>
                        <li role="listitem"><i class="fas fa-check" aria-hidden="true"></i> <strong>Backup automático diário</strong></li>
                        <li role="listitem"><i class="fas fa-check" aria-hidden="true"></i> <mark>Suporte 7 dias por semana</mark></li>
                        <li role="listitem"><i class="fas fa-check" aria-hidden="true"></i> <strong>Atualizações automáticas</strong></li>
                        <li role="listitem"><i class="fas fa-check" aria-hidden="true"></i> <mark>Primeiro mês GRÁTIS</mark></li>
                        <li role="listitem"><i class="fas fa-check" aria-hidden="true"></i> <strong>Sem compromisso de permanência</strong></li>
                        <li role="listitem"><i class="fas fa-check" aria-hidden="true"></i> <strong>Sem custos de instalação</strong></li>
                    </ul>
                    <a href="#cadastro" class="btn" aria-label="Começar plano completo agora">Começar Agora</a>
                </article>
            </div>
        </section>

        <section id="cadastro" class="cta" role="form">
            <div class="container">
                <header>
                    <h2 style="color: #1A1A1A;"><strong>Comece Hoje Mesmo!</strong></h2>
                    <p style="color: #1A1A1A;"><strong>Cadastre-se agora</strong> e tenha <mark>acesso completo ao sistema</mark> por <strong>30 dias totalmente grátis</strong></p>
                </header>
                <div class="form-section">
                    <form id="cadastro-form" role="form" aria-label="Formulário de cadastro gratuito">
                        <fieldset class="form-grid">
                            <legend class="sr-only">Dados para cadastro no BikeOS</legend>
                            <div class="form-group">
                                <label for="nome_empresa"><strong>Nome da Empresa</strong> *</label>
                                <input type="text" id="nome_empresa" name="nome_empresa" class="form-input" required aria-required="true" aria-describedby="empresa-help">
                                <small id="empresa-help" class="sr-only">Digite o nome da sua oficina ou empresa</small>
                            </div>
                            <div class="form-group">
                                <label for="nome_responsavel"><strong>Nome do Responsável</strong> *</label>
                                <input type="text" id="nome_responsavel" name="nome_responsavel" class="form-input" required aria-required="true">
                            </div>
                            <div class="form-group">
                                <label for="email"><strong>Email</strong> *</label>
                                <input type="email" id="email" name="email" class="form-input" required aria-required="true" aria-describedby="email-help">
                                <small id="email-help" class="sr-only">Email para acesso ao sistema e comunicações</small>
                            </div>
                            <div class="form-group">
                                <label for="telefone"><strong>Telefone/WhatsApp</strong> *</label>
                                <input type="tel" id="telefone" name="telefone" class="form-input" required aria-required="true">
                            </div>
                            <div class="form-group">
                                <label for="cidade"><strong>Cidade</strong> *</label>
                                <input type="text" id="cidade" name="cidade" class="form-input" required aria-required="true">
                            </div>
                            <div class="form-group">
                                <label for="senha"><strong>Senha</strong> *</label>
                                <input type="password" id="senha" name="senha" class="form-input" required minlength="6" aria-required="true" aria-describedby="senha-help">
                                <small id="senha-help" class="sr-only">Mínimo de 6 caracteres</small>
                            </div>
                            <div class="form-group full-width">
                                <button type="submit" class="btn" aria-label="Criar conta gratuita no BikeOS"><strong>Criar Conta Grátis</strong></button>
                            </div>
                        </fieldset>
                        <div class="success-message" id="success-message" role="alert" aria-live="polite">
                            <strong>Cadastro realizado com sucesso!</strong> Você receberá um email com as instruções de acesso.
                        </div>
                        <div class="error-message" id="error-message" role="alert" aria-live="assertive">
                            <strong>Erro ao realizar cadastro.</strong> Tente novamente.
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer" role="contentinfo">
        <div class="container">
            <p>&copy; 2025 <strong><a href="https://bikeos.com.br" target="_blank" rel="noopener noreferrer" style="color: var(--primary-color); text-decoration: none;">BikeOS</a></strong>. Todos os direitos reservados.</p>
            <p><strong>Contato:</strong> <a href="mailto:contato@bikeos.com.br" style="color: var(--primary-color);" aria-label="Enviar email para contato">contato@bikeos.com.br</a></p>
            <p><strong>WhatsApp:</strong> <a href="https://wa.me/5531984204877" target="_blank" style="color: var(--primary-color); text-decoration: none;" aria-label="Entrar em contato via WhatsApp">
                <i class="fab fa-whatsapp" style="margin-right: 5px;"></i>
                (31) 98420-4877
            </a></p>
            <address class="sr-only">
                BikeOS - Sistema de Gestão para Oficinas de Bicicletas<br>
                Email: contato@bikeos.com.br<br>
                WhatsApp: (31) 98420-4877
            </address>
        </div>
    </footer>

    <script>
        // Smooth scrolling para links internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(10, 10, 10, 0.98)';
            } else {
                header.style.background = 'rgba(10, 10, 10, 0.95)';
            }
        });

        // Form submission
        document.getElementById('cadastro-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            
            // Hide previous messages
            successMessage.style.display = 'none';
            errorMessage.style.display = 'none';
            
            try {
                const response = await fetch('api/api_cadastro.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.sucesso) {
                    successMessage.style.display = 'block';
                    this.reset();
                } else {
                    errorMessage.textContent = result.mensagem || 'Erro ao realizar cadastro. Tente novamente.';
                    errorMessage.style.display = 'block';
                }
            } catch (error) {
                errorMessage.textContent = 'Erro de conexão. Tente novamente.';
                errorMessage.style.display = 'block';
            }
        });
    </script>
</body>
</html>