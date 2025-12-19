<?php
session_start();
require_once 'auth_check.php';
require_once 'api/db_connection.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Contabilidade - Dengo Bike</title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .contabilidade-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .contabilidade-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .contabilidade-header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .contabilidade-header p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .contabilidade-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .contabilidade-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .contabilidade-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-decoration: none;
            color: white;
        }
        
        .contabilidade-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .contabilidade-card:hover::before {
            left: 100%;
        }
        
        .contabilidade-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .contabilidade-card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .contabilidade-card p {
            font-size: 0.9rem;
            opacity: 0.9;
            line-height: 1.4;
        }
        
        .back-button {
            position: fixed;
            top: 2rem;
            left: 2rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 1000;
        }
        
        .back-button:hover {
            background: #2980b9;
            transform: translateX(-5px);
            text-decoration: none;
            color: white;
        }
        
        .card-impostos {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }
        
        .card-nfe {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
        }
        
        .card-sped {
            background: linear-gradient(135deg, #45b7d1 0%, #96c93d 100%);
        }
        
        .card-conciliacao {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        @media (max-width: 768px) {
            .contabilidade-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .contabilidade-header h1 {
                font-size: 2rem;
            }
            
            .contabilidade-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .back-button {
                top: 1rem;
                left: 1rem;
                padding: 0.8rem 1.2rem;
            }
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Voltar ao Dashboard
    </a>
    
    <div class="contabilidade-container">
        <div class="contabilidade-header">
            <h1><i class="fas fa-calculator"></i> Menu Contabilidade</h1>
            <p>Gerencie todos os aspectos contábeis e fiscais do seu negócio</p>
        </div>
        
        <div class="contabilidade-grid">
            <a href="config_impostos.php" class="contabilidade-card card-impostos">
                <i class="fas fa-percentage"></i>
                <h3>Configuração de Impostos</h3>
                <p>Configure alíquotas, regimes tributários e parâmetros fiscais</p>
            </a>
            
            <a href="emissao_nfe.php" class="contabilidade-card card-nfe">
                <i class="fas fa-file-invoice"></i>
                <h3>Emissão de NFe</h3>
                <p>Emita notas fiscais eletrônicas de forma rápida e segura</p>
            </a>
            
            <a href="relatorio_impostos.php" class="contabilidade-card card-impostos">
                <i class="fas fa-chart-bar"></i>
                <h3>Relatório de Impostos</h3>
                <p>Visualize relatórios detalhados de impostos e tributos</p>
            </a>
            
            <a href="relatorio_sped.php" class="contabilidade-card card-sped">
                <i class="fas fa-database"></i>
                <h3>Relatório SPED</h3>
                <p>Gere arquivos SPED para cumprimento das obrigações fiscais</p>
            </a>
            
            <a href="conciliacao_bancaria.php" class="contabilidade-card card-conciliacao">
                <i class="fas fa-university"></i>
                <h3>Conciliação Bancária</h3>
                <p>Concilie movimentações bancárias com registros contábeis</p>
            </a>
        </div>
    </div>
    
    <script>
        // Adicionar efeito de entrada suave
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.contabilidade-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>