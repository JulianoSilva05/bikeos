<?php
require_once 'auth_check.php';
require_once 'check_initial_config.php';

// Lógica para controlar a exibição do modal de aniversário
$showBirthdayModal = false;
if (!isset($_SESSION['birthday_modal_shown'])) {
    $_SESSION['birthday_modal_shown'] = true;
    $showBirthdayModal = true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php 
        require_once 'helpers/empresa_helper.php';
        echo getTituloComEmpresa('Dashboard');
    ?></title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-header-with-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .page-header-with-nav {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }
            
            .header-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                justify-content: center;
            }
            
            .header-actions .btn {
                flex: 1;
                min-width: 120px;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .header-actions {
                flex-direction: column;
            }
            
            .header-actions .btn {
                width: 100%;
            }
        }

        /* Ajuste para zoom 80% (telas maiores) */
        @media (min-width: 1200px) {
            .page-header-with-nav {
                margin-bottom: 5rem;
            }
        }

        .kpi-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 2.5rem;
            width: 100%;
            box-sizing: border-box;
            padding: 0 0.5rem;
        }

        /* Media queries para responsividade */
        @media (max-width: 1200px) {
            .kpi-cards {
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
            }
        }

        @media (max-width: 768px) {
            .kpi-cards {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.75rem;
                width: 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .kpi-cards {
                grid-template-columns: 1fr;
                gap: 0.75rem;
                width: 100%;
                max-width: 100%;
            }
        }

        .card-kpi {
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            padding: 1.25rem 1.5rem;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 123, 255, 0.1);
            border-left: 4px solid transparent;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            min-height: 85px;
            position: relative;
            overflow: hidden;
        }
        
        .card-kpi::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
            opacity: 0.6;
        }

        @media (max-width: 1200px) {
            .card-kpi {
                padding: 1rem 1.25rem;
                gap: 1.25rem;
                min-height: 75px;
            }
        }

        @media (max-width: 768px) {
            .card-kpi {
                padding: 0.875rem 1rem;
                gap: 1rem;
                min-height: 70px;
                flex-direction: row;
            }
        }

        @media (max-width: 480px) {
            .card-kpi {
                padding: 0.875rem;
                gap: 0.875rem;
                min-height: 70px;
            }
        }

        /* Media query específica para orientação vertical em dispositivos móveis */
        @media (max-width: 480px) and (orientation: portrait) {
            .card-kpi {
                padding: 0.75rem;
                gap: 0.75rem;
                min-height: 55px;
            }
            
            .card-kpi .icon {
                width: 45px;
                height: 45px;
                font-size: 1.8rem;
            }
        }

        .card-kpi .icon {
            font-size: 2.4rem;
            color: var(--cor-primaria);
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 123, 255, 0.05));
            border-radius: 14px;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }
        
        /* Cores específicas para cada card */
        #card-os-ativas {
            border-left-color: #28a745;
        }
        
        #card-os-ativas .icon {
            color: #28a745;
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
        }
        
        #card-faturamento-link {
            border-left-color: #007bff;
        }
        
        #card-faturamento-link .icon {
            color: #007bff;
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 123, 255, 0.05));
        }
        
        #card-satisfacao {
            border-left-color: #ffc107;
        }
        
        #card-satisfacao .icon {
            color: #ffc107;
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
        }
        
        #card-os-ativas:hover .icon {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.2), rgba(40, 167, 69, 0.1));
        }
        
        #card-faturamento-link:hover .icon {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.2), rgba(0, 123, 255, 0.1));
        }
        
        #card-satisfacao:hover .icon {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.2), rgba(255, 193, 7, 0.1));
        }

        .card-kpi .info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0.25rem;
        }

        .card-kpi .info h3 {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
            word-wrap: break-word;
            color: #2c3e50;
            line-height: 1.2;
        }

        .card-kpi .info p {
            color: #6c757d;
            margin: 0;
            word-wrap: break-word;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        @media (max-width: 1200px) {
            .card-kpi .icon {
                font-size: 1.6rem;
            }
            
            .card-kpi .info h3 {
                font-size: 1.4rem;
            }
            
            .card-kpi .info p {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 768px) {
            .card-kpi .icon {
                font-size: 1.4rem;
            }
            
            .card-kpi .info h3 {
                font-size: 1.2rem;
            }
            
            .card-kpi .info p {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .card-kpi .icon {
                font-size: 1.2rem;
            }
            
            .card-kpi .info h3 {
                font-size: 1rem;
            }
            
            .card-kpi .info p {
                font-size: 0.75rem;
            }
        }

        .faturamento-detalhado {
            font-size: 0.9rem;
            width: 100%;
        }

        .faturamento-detalhado .total-dia {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            display: block;
            color: #2c3e50;
            line-height: 1.2;
        }

        .faturamento-detalhado .detalhe-item {
            display: flex;
            justify-content: space-between;
            line-height: 1.4;
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 0.125rem;
        }
        
        .faturamento-detalhado .detalhe-item:last-child {
            margin-bottom: 0;
        }

        .faturamento-detalhado .detalhe-item strong {
            font-weight: 600;
        }

        .faturamento-detalhado hr {
            border: 0;
            border-top: 1px solid #eee;
            margin: 0.5rem 0;
        }

        /* Estilo para as estrelas de satisfação */
        .satisfaction-stars {
            font-size: 1.5rem;
            color: #ffc107; /* Cor das estrelas preenchidas */
            margin-top: 5px;
        }

        /* Estilos para o calendário */
        .calendar-container {
            background-color: var(--cor-branco);
            padding: 1.5rem;
            border-radius: var(--raio-borda);
            box-shadow: var(--sombra-card);
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .calendar-container {
                padding: 1rem;
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 480px) {
            .calendar-container {
                padding: 0.75rem;
            }
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .calendar-header h2 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--cor-primaria);
        }

        .calendar-nav {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--cor-primaria);
            padding: 0.5rem;
        }

        .calendar-nav:hover {
            background-color: var(--cor-fundo-claro);
            border-radius: 50%;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.25rem;
        }

        @media (max-width: 768px) {
            .calendar-grid {
                gap: 0.125rem;
            }
        }

        @media (max-width: 480px) {
            .calendar-grid {
                gap: 0.0625rem;
            }
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--cor-texto-secundario);
            padding: 0.5rem 0;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--cor-borda);
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9rem;
            position: relative;
            min-height: 35px;
        }

        @media (max-width: 768px) {
            .calendar-day {
                font-size: 0.8rem;
                min-height: 30px;
            }
        }

        @media (max-width: 480px) {
            .calendar-day {
                font-size: 0.7rem;
                min-height: 25px;
            }
        }

        .calendar-day:hover {
            background-color: var(--cor-fundo-claro);
        }

        .calendar-day.today {
            background-color: var(--cor-primaria);
            color: white;
        }

        .calendar-day.other-month {
            color: var(--cor-texto-secundario);
            opacity: 0.5;
        }

        .calendar-day.has-os {
            background-color: #e3f2fd;
            border-color: var(--cor-primaria);
        }

        .calendar-day.has-os.today {
            background-color: var(--cor-primaria);
        }

        .os-count {
            font-size: 0.7rem;
            background-color: var(--cor-destaque);
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 2px;
            right: 2px;
        }

        .calendar-day.today .os-count {
            background-color: rgba(255, 255, 255, 0.9);
            color: var(--cor-primaria);
        }

        .satisfaction-stars .fa-star-o {
            color: #ccc; /* Cor das estrelas vazias */
        }

        .dashboard-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            grid-template-areas: 
                "agenda calendar";
            gap: 2rem;
        }

        @media (max-width: 1200px) {
            .dashboard-layout {
                grid-template-columns: 2fr 1fr;
                grid-template-areas: 
                    "agenda calendar";
                gap: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .dashboard-layout {
                grid-template-columns: 1fr;
                grid-template-areas: 
                    "agenda"
                    "calendar";
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .dashboard-layout {
                gap: 0.75rem;
            }
        }
        
        .calendar-alertas-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            max-width: 100%;
        }
        
        .agenda-section {
            grid-area: agenda;
        }
        
        .calendar-section {
            grid-area: calendar;
        }

        .lista-itens {
            list-style: none;
            padding: 0;
        }

        .lista-itens li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--cor-borda);
            transition: background-color 0.2s;
        }

        .lista-itens li:last-child {
            border-bottom: none;
        }

        .lista-itens .item-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .icon-item {
            font-size: 1.5rem;
            width: 40px;
            text-align: center;
        }

        .icon-agenda {
            color: var(--cor-primaria);
        }

        .icon-alerta {
            color: var(--cor-alerta);
        }

        .icon-birthday {
            color: #e83e8c;
        }

        .icon-problema {
            color: #ff6b35;
        }

        .problema-agenda {
            border-left: 4px solid #ff6b35;
        }

        .problema-agenda.prioridade-alta {
            border-left-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }

        .problema-agenda.prioridade-alta .icon-problema {
            color: #dc3545;
        }
        
        /* Estilos para problema relatado na agenda */
        .problema-relatado {
            font-size: 0.8rem;
            color: #666;
            margin-top: 4px;
            padding: 4px 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #ffa502;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .problema-relatado i {
            color: #ffa502;
            font-size: 0.75rem;
        }

        .problema-agenda.prioridade-media {
            border-left-color: #ffc107;
            background-color: rgba(255, 193, 7, 0.05);
        }

        .problema-agenda.prioridade-media .icon-problema {
            color: #ffc107;
        }

        .problema-agenda.prioridade-baixa {
            border-left-color: #28a745;
            background-color: rgba(40, 167, 69, 0.05);
        }

        .problema-agenda.prioridade-baixa .icon-problema {
            color: #28a745;
        }

        .status-problema {
            font-weight: 600;
        }

        .info-item .titulo {
            font-weight: 600;
            margin: 0;
        }

        .info-item .subtitulo {
            font-size: 0.9rem;
            color: var(--cor-texto-secundario);
            margin: 0;
        }

        .status {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--cor-branco);
            text-transform: uppercase;
        }

        .status-EM_SERVICO,
        .status-ORCAMENTO,
        .status-ATIVA {
            background-color: var(--cor-primaria);
        }

        .status-PRONTA {
            background-color: #ffc107;
            color: #333;
        }

        .status-AGUARDANDO_PECA {
            background-color: var(--cor-alerta);
            color: #333;
        }

        .status-CANCELADA {
            background-color: var(--cor-texto-secundario);
        }

        .status-FINALIZADA {
            background-color: #28a745;
        }

        .card-link {
            text-decoration: none;
            color: inherit;
        }

        #card-os-ativas,
        #card-faturamento-link,
        .card-kpi, /* Adicionado para todos os KPI cards */
        .lista-itens .clickable {
            cursor: pointer;
        }

        #card-os-ativas:hover,
        #card-faturamento-link:hover,
        .card-kpi:hover, /* Adicionado para todos os KPI cards */
        .lista-itens .clickable:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 123, 255, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08);
            border-color: rgba(0, 123, 255, 0.3);
        }
        
        #card-os-ativas:hover {
            box-shadow: 0 15px 35px rgba(40, 167, 69, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        #card-satisfacao:hover {
            box-shadow: 0 15px 35px rgba(255, 193, 7, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .card-kpi:hover .icon {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.2), rgba(0, 123, 255, 0.1));
            transform: scale(1.05);
        }

        .fab-container-history {
            position: fixed;
            bottom: 95px;
            right: 25px;
            z-index: 1050;
        }

        .fab-container-birthday {
            position: fixed;
            bottom: 160px;
            right: 25px;
            z-index: 1050;
        }

        .caixa-status {
            padding: 0.3rem;
            border-radius: var(--raio-borda);
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .caixa-fechado {
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            color: #721c24;
        }

        .caixa-aberto {
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            color: #0f5132;
        }

        .agenda-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .agenda-header h2 {
            margin: 0;
            font-size: 1.2em;
            flex-grow: 1;
            text-align: center;
        }

        .agenda-nav-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--cor-primaria);
            padding: 0 10px;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid var(--cor-borda);
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .tab-link {
            padding: 0.8rem 1.5rem;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 1.1rem;
        }

        .tab-link.active {
            border-bottom: 3px solid var(--cor-primaria);
            font-weight: 600;
            color: var(--cor-primaria);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .text-muted {
            text-decoration: line-through;
            color: #6c757d;
        }

        /* ESTILO PARA O NOVO FOOTER */
        .page-footer {
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: var(--cor-texto-secundario);
            border-top: 1px solid var(--cor-borda);
        }

        .page-footer a {
            color: var(--cor-primaria);
            text-decoration: none;
            font-weight: 600;
        }

        .page-footer a:hover {
            text-decoration: underline;
        }

        .page-footer .footer-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .page-footer .help-button {
            background: #25D366;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.3s ease;
        }

        .page-footer .help-button:hover {
            background: #128C7E;
            text-decoration: none;
        }

        /* Estilos do Modal de Dica de Zoom */
        .zoom-tip-content {
            max-width: 500px;
            text-align: center;
        }

        .zoom-tip-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            color: var(--cor-primaria);
        }

        .zoom-tip-header i {
            font-size: 1.5rem;
            animation: pulse 2s infinite;
        }

        .zoom-animation-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
            margin: 1.5rem 0;
            border: 2px dashed #dee2e6;
        }

        .keyboard-demo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .key-ctrl {
            background: #343a40;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-weight: bold;
            box-shadow: 0 3px 0 #212529;
            animation: keyPress 3s infinite;
        }

        .plus-sign {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--cor-primaria);
        }

        .mouse-demo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .mouse-demo i {
            font-size: 2rem;
            color: #6c757d;
            animation: mouseMove 3s infinite;
        }

        .scroll-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2rem;
        }

        .scroll-arrow {
            font-size: 1.2rem;
            color: var(--cor-primaria);
            animation: scrollDown 2s infinite;
        }

        .scroll-indicator span {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .zoom-result {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--cor-sucesso);
            font-weight: bold;
        }

        .zoom-result i {
            font-size: 1.5rem;
            animation: zoomOut 3s infinite;
        }

        .zoom-instructions {
            text-align: left;
            margin: 1.5rem 0;
        }

        .instruction-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: white;
            border-radius: 8px;
            border-left: 4px solid var(--cor-primaria);
        }

        .instruction-item i {
            color: var(--cor-primaria);
            width: 20px;
        }

        .zoom-tip-footer {
             display: flex;
             justify-content: center;
             align-items: center;
             margin-top: 2rem;
             padding-top: 1rem;
             border-top: 1px solid #dee2e6;
         }

        /* Animações do Modal de Zoom */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        @keyframes keyPress {
            0%, 90%, 100% { transform: translateY(0); }
            5%, 15% { transform: translateY(3px); }
        }

        @keyframes mouseMove {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @keyframes scrollDown {
            0%, 100% { transform: translateY(0); opacity: 1; }
            50% { transform: translateY(10px); opacity: 0.5; }
        }

        @keyframes zoomOut {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(0.8); }
        }

        @media (max-width: 768px) {
            .zoom-tip-content {
                max-width: 90vw;
                margin: 1rem;
            }
            
            .keyboard-demo {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .zoom-tip-footer {
                flex-direction: column;
                gap: 1rem;
            }
        }
        
        /* Estilos do Calendário */
        .calendar-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 10px;
            height: fit-content;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
        }

        .calendar-header h2 {
            margin: 0;
            color: #333;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .calendar-nav {
            background: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
        }

        .calendar-nav:hover {
            background: #0056b3;
            transform: scale(1.1);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: bold;
            color: #666;
            padding: 6px 2px;
            font-size: 0.7rem;
            border-bottom: 1px solid #eee;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #eee;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            min-height: 32px;
            background: white;
            font-size: 0.75rem;
        }

        .calendar-day:hover {
            background: #f8f9fa;
            border-color: #007bff;
            transform: translateY(-1px);
        }

        .calendar-day.today {
            background: #007bff;
            color: white;
            font-weight: bold;
        }

        .calendar-day.other-month {
            color: #ccc;
            background: #f9f9f9;
            cursor: default;
        }

        .calendar-day.other-month:hover {
            transform: none;
            border-color: #eee;
            background: #f9f9f9;
        }

        .calendar-day.has-os {
            background: #e8f5e8;
            border-color: #28a745;
        }

        .calendar-day.has-os:hover {
            background: #d4edda;
        }

        .day-number {
            font-size: 0.8rem;
            font-weight: 500;
        }

        .os-count {
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 14px;
            height: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: bold;
            margin-top: 1px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php"><img src="assets/img/logo.png" alt="Logo" class="navbar-logo"></a>
        <div class="navbar-user-info"><i class="fa-solid fa-user-circle"></i><span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?>!</span></div>

        <a href="logout.php" class="btn btn-destaque"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
    </nav>
    <main class="container">
        <style>
            .container {
                max-width: 100%;
                padding: 0 1rem;
                width: 100%;
                box-sizing: border-box;
                overflow-x: hidden;
            }
            
            @media (min-width: 576px) {
                .container {
                    max-width: 540px;
                    margin: 0 auto;
                }
            }
            
            @media (min-width: 768px) {
                .container {
                    max-width: 720px;
                    padding: 0 1.5rem;
                }
            }
            
            @media (min-width: 992px) {
                .container {
                    max-width: 960px;
                }
            }
            
            @media (min-width: 1200px) {
                .container {
                    max-width: 1140px;
                }
            }
            
            @media (min-width: 1400px) {
                .container {
                    max-width: 1320px;
                }
            }
            
            /* Otimizações específicas para telas com altura maior que largura */
            @media screen and (max-aspect-ratio: 4/5) {
                .container {
                    padding: 0 0.75rem;
                    max-width: 100vw;
                }
                
                .page-header-with-nav {
                    margin-bottom: 1rem;
                }
                
                .kpi-cards {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 0.5rem;
                }
                
                .dashboard-layout {
                    grid-template-columns: 2fr 1fr;
                    grid-template-areas: "agenda calendar";
                    gap: 0.75rem;
                    width: 100%;
                }
                
                .card {
                    width: 100%;
                    max-width: 100%;
                    box-sizing: border-box;
                }
            }
            
            @media screen and (max-aspect-ratio: 4/5) and (max-width: 600px) {
                .kpi-cards {
                    grid-template-columns: 1fr;
                }
                
                .dashboard-layout {
                    grid-template-columns: 1fr;
                    grid-template-areas: 
                        "agenda"
                        "calendar";
                }
            }
            
            /* Media queries específicas para orientação portrait e telas verticais */
            @media screen and (orientation: portrait) {
                .kpi-cards {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 0.75rem;
                }
                
                .card-kpi {
                    min-height: 80px;
                    padding: 0.875rem;
                    flex-direction: column;
                    text-align: center;
                }
                
                .card-kpi .info h3 {
                    font-size: 1.1rem;
                }
                
                .card-kpi .info p {
                    font-size: 0.7rem;
                }
                
                .dashboard-layout {
                    grid-template-columns: 2fr 1fr;
                    grid-template-areas: "agenda calendar";
                    gap: 1rem;
                }
            }
            
            @media screen and (orientation: portrait) and (max-width: 600px) {
                .kpi-cards {
                    grid-template-columns: 1fr;
                    gap: 0.5rem;
                }
                
                .card-kpi {
                    flex-direction: row;
                    text-align: left;
                }
                
                .dashboard-layout {
                    grid-template-columns: 1fr;
                    grid-template-areas: 
                        "agenda"
                        "calendar";
                }
            }

            /* Para telas com altura maior que largura (verticais) */
            @media screen and (max-aspect-ratio: 1/1) {
                .kpi-cards {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 0.5rem;
                }
                
                .card-kpi {
                    padding: 0.75rem;
                    min-height: 70px;
                    gap: 0.75rem;
                    flex-direction: column;
                    text-align: center;
                }
                
                .card-kpi .icon {
                    font-size: 1.1rem;
                    flex-shrink: 0;
                }
                
                .card-kpi .info h3 {
                    font-size: 0.95rem;
                }
                
                .card-kpi .info p {
                    font-size: 0.65rem;
                }
            }
            
            @media screen and (max-aspect-ratio: 1/1) and (max-width: 600px) {
                .kpi-cards {
                    grid-template-columns: 1fr;
                }
                
                .card-kpi {
                    flex-direction: row;
                    text-align: left;
                }
            }

            /* Para dispositivos móveis em portrait com zoom 100% */
            @media screen and (orientation: portrait) and (max-width: 768px) {
                .page-header,
                .page-header-with-nav {
                    margin-bottom: 1rem;
                }
                
                .kpi-cards {
                    margin-bottom: 1rem;
                    gap: 0.5rem;
                    grid-template-columns: 1fr;
                }
                
                .card-kpi {
                    padding: 0.75rem;
                    min-height: 60px;
                    gap: 0.75rem;
                    flex-direction: row;
                    text-align: left;
                }
                
                .calendar-container {
                    padding: 0.75rem;
                    margin-bottom: 1rem;
                }
                
                .dashboard-layout {
                    grid-template-columns: 1fr;
                    grid-template-areas: 
                        "agenda"
                        "calendar";
                }
            }

            /* Para telas muito estreitas em portrait */
            @media screen and (orientation: portrait) and (max-width: 480px) {
                .container {
                    padding: 0 0.5rem;
                }
                
                .kpi-cards {
                    gap: 0.375rem;
                }
                
                .card-kpi {
                    padding: 0.625rem;
                    min-height: 55px;
                    gap: 0.625rem;
                }
                
                .card-kpi .icon {
                    font-size: 1.1rem;
                }
                
                .card-kpi .info h3 {
                    font-size: 0.95rem;
                }
                
                .card-kpi .info p {
                    font-size: 0.7rem;
                }
            }
            
            /* Ajustes específicos para evitar estouro em telas verticais */
            @media screen and (orientation: portrait) and (min-height: 600px) {
                .dashboard-layout {
                    gap: 0.75rem;
                }
                
                .calendar-alertas-container {
                    gap: 1rem;
                }
            }
        </style>
        <div class="page-header-with-nav">
            <div class="page-title">
                <h1>Painel Principal</h1>
            </div>
            <div class="header-actions">
                <a href="gestao_avancada.php" class="btn btn-secundario">
                    <i class="fa-solid fa-chart-line"></i> Gestão Avançada
                </a>
                <a href="nova_venda.php" class="btn btn-primario">
                    <i class="fa-solid fa-plus"></i> Nova Venda
                </a>
                <a href="nova_os.php" class="btn btn-destaque">
                    <i class="fa-solid fa-bicycle"></i> Nova OS
                </a>
            </div>
        </div>

        <div id="caixa-status-container"></div>

        <section class="kpi-cards">
            <div id="card-os-ativas" class="card-kpi">
                <div class="icon"><i class="fa-solid fa-gears"></i></div>
                <div class="info">
                    <h3 id="kpi-os-ativas">0</h3>
                    <p>OS Ativas</p>
                </div>
            </div>

            <a id="card-faturamento-link" href="financeiro.php" class="card-link">
                <div class="card-kpi">
                    <div class="icon"><i class="fa-solid fa-dollar-sign"></i></div>
                    <div class="info faturamento-detalhado" id="kpi-faturamento-detalhado">
                        <div class="total-dia">R$ 0,00</div>
                        <p>Vendas do Dia</p>
                        <div class="detalhes-caixa">
                            <div class="entrada-item">
                                <span class="label">Vendas Diretas:</span>
                                <span class="valor" id="vendas-diretas">R$ 0,00</span>
                            </div>
                            <div class="entrada-item">
                                <span class="label">Crediário:</span>
                                <span class="valor" id="vendas-crediario">R$ 0,00</span>
                            </div>
                            <div class="entrada-item">
                                <span class="label">Serviços/OS:</span>
                                <span class="valor" id="vendas-servicos">R$ 0,00</span>
                            </div>
                            <div class="saida-item">
                                <span class="label">Saídas:</span>
                                <span class="valor" id="saidas-caixa">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- NOVO CARD: Nível de Satisfação dos Clientes Hoje -->
            <div id="card-satisfacao" class="card-kpi">
                <div class="icon"><i class="fa-solid fa-star"></i></div>
                <div class="info">
                    <h3 id="kpi-satisfacao-media">
                        <span id="stars-satisfacao">
                            <i class="fa-regular fa-star fa-star-o"></i>
                            <i class="fa-regular fa-star fa-star-o"></i>
                            <i class="fa-regular fa-star fa-star-o"></i>
                            <i class="fa-regular fa-star fa-star-o"></i>
                            <i class="fa-regular fa-star fa-star-o"></i>
                        </span>
                    </h3>
                    <p>Satisfação Hoje (<span id="total-avaliacoes-hoje">0</span> avaliações)</p>
                </div>
            </div>
            <!-- FIM DO NOVO CARD -->

        </section>

        <div class="dashboard-layout">
            <section class="agenda-section">
                <div class="card">
                    <div class="agenda-header">
                        <button id="btn-agenda-prev" class="agenda-nav-btn" title="Dia Anterior"><i class="fa-solid fa-chevron-left"></i></button>
                        <h2><i class="fa-solid fa-calendar-day"></i> <span id="agenda-titulo">Agenda de Entregas</span></h2>
                        <button id="btn-agenda-next" class="agenda-nav-btn" title="Próximo Dia"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                    <ul class="lista-itens" id="lista-agenda-dia"></ul>
                </div>
            </section>
            
            <!-- Calendário e Alertas -->
            <section class="calendar-section">
                <div class="calendar-alertas-container">
                    <div class="card calendar-container">
                        <div class="calendar-header">
                            <button id="calendar-prev" class="calendar-nav" title="Mês Anterior">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <h2 id="calendar-title">
                                <i class="fa-solid fa-calendar"></i> <span id="calendar-month-year">Janeiro 2025</span>
                            </h2>
                            <button id="calendar-next" class="calendar-nav" title="Próximo Mês">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="calendar-grid" id="calendar-grid">
                            <!-- Cabeçalhos dos dias da semana -->
                            <div class="calendar-day-header">Dom</div>
                            <div class="calendar-day-header">Seg</div>
                            <div class="calendar-day-header">Ter</div>
                            <div class="calendar-day-header">Qua</div>
                            <div class="calendar-day-header">Qui</div>
                            <div class="calendar-day-header">Sex</div>
                            <div class="calendar-day-header">Sáb</div>
                            <!-- Os dias serão gerados dinamicamente pelo JavaScript -->
                        </div>
                    </div>
                    
                    <div class="card">
                        <h2><i class="fa-solid fa-triangle-exclamation"></i> Lista de Alertas</h2>
                        <ul class="lista-itens" id="lista-alertas"></ul>
                    </div>
                </div>
             </section>
        </div>
    </main>

    <footer class="page-footer">
        <div class="footer-content">
            <span>Desenvolvido por <a href="https://bikeos.com.br" target="_blank" rel="noopener noreferrer">BikeOS</a> - Todos os direitos reservados 2025 | <a href="mailto:contato@bikeos.com.br">contato@bikeos.com.br</a></span>
            <a href="https://wa.me/5531984204877?text=Preciso%20de%20ajuda%20no%20sistema%20BikeOS" target="_blank" rel="noopener noreferrer" class="help-button">
                <i class="fab fa-whatsapp"></i> AJUDA
            </a>
        </div>
    </footer>

    <div id="os-ativas-modal" class="modal-backdrop">
        <div class="modal-content" style="max-width: 900px;">
            <div class="modal-header">
                <h2><i class="fa-solid fa-list-check"></i> Ordens de Serviço Ativas</h2><a href="nova_os.php" class="btn btn-destaque"><i class="fa-solid fa-plus"></i> Nova OS</a>
            </div>
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>OS Nº</th>
                            <th>Cliente</th>
                            <th>Bicicleta</th>
                            <th>Data Entrada</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="os-ativas-table-body"></tbody>
                </table>
            </div><button class="modal-close" style="position:absolute; top:5px; right:15px;">&times;</button>
        </div>
    </div>

    <div id="birthday-modal" class="modal-backdrop">
        <div class="modal-content"><span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-cake-candles"></i> Aniversariantes de Hoje</h2>
            <ul class="lista-itens" id="lista-aniversariantes"></ul>
        </div>
    </div>

    <!-- Modal de Dica de Zoom -->
    <div id="zoom-tip-modal" class="modal-backdrop">
        <div class="modal-content zoom-tip-content">
            <span class="modal-close">&times;</span>
            <div class="zoom-tip-header">
                <i class="fa-solid fa-lightbulb"></i>
                <h2>Dica para Telas Pequenas</h2>
            </div>
            <div class="zoom-tip-body">
                <p>Para uma melhor visualização em telas pequenas, você pode ajustar o zoom da página:</p>
                
                <div class="zoom-animation-container">
                    <div class="keyboard-demo">
                        <div class="key-ctrl">Ctrl</div>
                        <div class="plus-sign">+</div>
                        <div class="mouse-demo">
                            <i class="fa-solid fa-computer-mouse"></i>
                            <div class="scroll-indicator">
                                <div class="scroll-arrow">↓</div>
                                <span>Scroll</span>
                            </div>
                        </div>
                    </div>
                    <div class="zoom-result">
                        <i class="fa-solid fa-search-minus"></i>
                        <span>Diminuir Zoom</span>
                    </div>
                </div>
                
                <div class="zoom-instructions">
                    <div class="instruction-item">
                        <i class="fa-solid fa-hand-pointer"></i>
                        <span>Segure a tecla <strong>Ctrl</strong></span>
                    </div>
                    <div class="instruction-item">
                        <i class="fa-solid fa-computer-mouse"></i>
                        <span>Role o <strong>scroll do mouse</strong> para baixo</span>
                    </div>
                    <div class="instruction-item">
                        <i class="fa-solid fa-eye"></i>
                        <span>Ajuste até encontrar o zoom ideal</span>
                    </div>
                </div>
                
                <div class="zoom-tip-footer">
                     <button class="btn btn-primario" id="close-zoom-tip">Entendi!</button>
                 </div>
            </div>
        </div>
    </div>

    <div id="history-modal" class="modal-backdrop">
        <div class="modal-content" style="max-width: 900px;"><span class="modal-close">&times;</span>
            <h2><i class="fa-solid fa-history"></i> Histórico Geral</h2>
            
            <!-- Filtros de Histórico -->
            <div class="filters-container" style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3 style="margin: 0 0 1rem 0; color: #333;"><i class="fa-solid fa-filter"></i> Filtros de Histórico</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Filtrar por Histórico</label>
                        <input type="text" id="search-historico" class="form-input" placeholder="OS, venda, cliente, data, pagamento, valor...">
                    </div>
                    <div class="form-group" style="display: flex; align-items: end;">
                        <button id="btn-limpar-busca-historico" class="btn btn-secundario" style="width: 100%;">
                            <i class="fa-solid fa-eraser"></i> Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="tabs"><button class="tab-link active" data-tab="history-os">Ordens de Serviço</button><button class="tab-link" data-tab="history-vendas">Vendas</button></div>
            <div id="history-os" class="tab-content active" style="max-height: 60vh; overflow-y: auto;">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>OS</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Valor</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="history-os-tbody"></tbody>
                </table>

            </div>
            <div id="history-vendas" class="tab-content" style="max-height: 60vh; overflow-y: auto;">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Venda</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Pagamento</th>
                            <th>Status</th>
                            <th>Valor</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="history-vendas-tbody"></tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="fab-container-birthday">
        <button id="fab-birthday" class="fab-button" title="Aniversariantes do Dia" style="background-color: #e83e8c; display: none;"><i class="fa-solid fa-cake-candles"></i></button>
    </div>
    <div class="fab-container-history">
        <button id="fab-history-menu" class="fab-button" title="Histórico de Vendas e OS"><i class="fa-solid fa-history"></i></button>
    </div>

    <?php include '_partials/menu_admin.php'; ?>

    <script>
        const showBirthdayModalOnLoad = <?php echo json_encode($showBirthdayModal); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            const cardOsAtivas = document.getElementById('card-os-ativas');
            const osAtivasModal = document.getElementById('os-ativas-modal');
            const osAtivasTableBody = document.getElementById('os-ativas-table-body');
            const listaAlertas = document.getElementById('lista-alertas');
            const listaAgenda = document.getElementById('lista-agenda-dia');
            const caixaStatusContainer = document.getElementById('caixa-status-container');
            const birthdayFab = document.getElementById('fab-birthday');
            const birthdayModal = document.getElementById('birthday-modal');
            const listaAniversariantes = document.getElementById('lista-aniversariantes');
            const agendaTitulo = document.getElementById('agenda-titulo');
            const btnAgendaPrev = document.getElementById('btn-agenda-prev');
            const btnAgendaNext = document.getElementById('btn-agenda-next');
            const historyFab = document.getElementById('fab-history-menu');
            const historyModal = document.getElementById('history-modal');
            const historyVendasTbody = document.getElementById('history-vendas-tbody');
            const cardSatisfacao = document.getElementById('card-satisfacao'); // NOVO: Elemento do card de satisfação
            let agendaDataAtual = new Date();
            let osHistoryLoaded = false;
            let vendasHistoryLoaded = false;
            
            // Dados originais do histórico para busca
            let dadosOriginaisOS = [];
            let dadosOriginaisVendas = [];
            let searchHistoricoInput;
            
            // Variáveis para paginação infinita
            let osOffset = 0;
            let vendasOffset = 0;
            let osTemMais = true;
            let vendasTemMais = true;
            let carregandoOS = false;
            let carregandoVendas = false;
            const LIMITE_POR_PAGINA = 20;
            
            // Variáveis do calendário
            const calendarGrid = document.getElementById('calendar-grid');
            const calendarMonthYear = document.getElementById('calendar-month-year');
            const calendarPrev = document.getElementById('calendar-prev');
            const calendarNext = document.getElementById('calendar-next');
            let currentCalendarDate = new Date();
            let osCalendarData = {}; // Armazena dados de OS por data

            const fecharModalOsAtivas = () => osAtivasModal.style.display = 'none';
            const abrirModalOsAtivas = async () => {
                osAtivasTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Carregando...</td></tr>';
                osAtivasModal.style.display = 'flex';
                try {
                    const response = await fetch(`api/api_dashboard.php?acao=listar_os_ativas&_=${new Date().getTime()}`);
                    const resultado = await response.json();
                    osAtivasTableBody.innerHTML = '';
                    if (resultado.sucesso && resultado.dados.length > 0) {
                        resultado.dados.forEach(os => {
                            osAtivasTableBody.innerHTML += `<tr><td>${os.id}</td><td>${os.cliente_nome}</td><td>${os.bicicleta_modelo}</td><td>${os.data_entrada_formatada}</td><td><span class="status status-${os.status}">${os.status.replace('_', ' ')}</span></td><td><a href="editar_os.php?id=${os.id}" class="btn btn-secundario btn-sm"><i class="fa-solid fa-pencil"></i> Editar</a></td></tr>`;
                        });
                    } else {
                        osAtivasTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Nenhuma OS ativa no momento.</td></tr>';
                    }
                } catch (error) {
                    osAtivasTableBody.innerHTML = `<tr><td colspan="6" class="error-message">Erro ao carregar dados.</td></tr>`;
                }
            };

            function formatCurrency(value) {
                return `R$ ${parseFloat(value || 0).toFixed(2).replace('.', ',')}`;
            }

            function atualizarKPIs(kpis) {
                document.getElementById('kpi-os-ativas').textContent = kpis.os_ativas || 0;
                
                // Atualizar total do dia
                const totalDia = (parseFloat(kpis.faturamento_caixa) || 0) + (parseFloat(kpis.faturamento_crediario) || 0);
                document.querySelector('.total-dia').textContent = formatCurrency(totalDia);
                
                // Atualizar valores detalhados
                document.getElementById('vendas-diretas').textContent = formatCurrency(kpis.faturamento_caixa);
                document.getElementById('vendas-crediario').textContent = formatCurrency(kpis.faturamento_crediario);
                document.getElementById('vendas-servicos').textContent = formatCurrency(kpis.faturamento_servicos);
                document.getElementById('saidas-caixa').textContent = formatCurrency(kpis.saidas_caixa || 0);

                // NOVO: Lógica para o card de Nível de Satisfação
                const mediaSatisfacao = kpis.media_satisfacao || 0;
                const totalAvaliacoes = kpis.total_avaliacoes_hoje || 0;
                const starsContainer = document.getElementById('stars-satisfacao');
                let starsHtml = '';

                if (totalAvaliacoes === 0) {
                    // Se não há avaliações, todas as estrelas ficam brancas (contorno)
                    for (let i = 0; i < 5; i++) {
                        starsHtml += '<i class="fa-regular fa-star fa-star-o"></i>';
                    }
                } else {
                    // Preenche as estrelas com base na média
                    for (let i = 1; i <= 5; i++) {
                        if (i <= mediaSatisfacao) {
                            starsHtml += '<i class="fa-solid fa-star"></i>'; // Estrela preenchida
                        } else if (i - 0.5 <= mediaSatisfacao) {
                            starsHtml += '<i class="fa-solid fa-star-half-stroke"></i>'; // Meia estrela
                        } else {
                            starsHtml += '<i class="fa-regular fa-star fa-star-o"></i>'; // Estrela vazia (contorno)
                        }
                    }
                }
                starsContainer.innerHTML = starsHtml;
                starsContainer.classList.add('satisfaction-stars'); // Adiciona a classe CSS para o estilo das estrelas
                document.getElementById('total-avaliacoes-hoje').textContent = totalAvaliacoes;
                // FIM DO NOVO: Lógica para o card de Nível de Satisfação
            }

            function atualizarAgenda(agenda) {
                listaAgenda.innerHTML = '';
                if (!agenda || agenda.length === 0) {
                    listaAgenda.innerHTML = '<li><div class="item-content"><div class="icon-item icon-agenda"><i class="fa-solid fa-check-circle"></i></div><div class="info-item"><p class="titulo">Nenhuma entrega agendada para esta data.</p></div></div></li>';
                    return;
                }
                agenda.forEach(item => {
                    const statusTexto = item.status === 'FINALIZADA' ? 'ENTREGUE' : item.status.replace(/_/g, ' ');
                    const problemaRelatado = item.problema_relatado ? item.problema_relatado : 'Nenhum problema específico relatado';
                    const problemaTexto = problemaRelatado.length > 60 ? problemaRelatado.substring(0, 60) + '...' : problemaRelatado;
                    
                    listaAgenda.innerHTML += `<li class="clickable" data-os-id="${item.id}" title="${problemaRelatado}"><div class="item-content"><div class="icon-item icon-agenda"><i class="fa-solid fa-bicycle"></i></div><div class="info-item"><p class="titulo">${item.cliente_nome}</p><p class="subtitulo">${item.bicicleta_modelo} (OS: #${item.id})</p><p class="problema-relatado"><i class="fa-solid fa-exclamation-triangle"></i> ${problemaTexto}</p></div></div><span class="status status-${item.status}">${statusTexto}</span></li>`;
                });
            }

            function atualizarAlertas(alertasEstoque, alertasCrediario, alertasContasPagar) {
                listaAlertas.innerHTML = '';
                let hasAlerts = false;
                
                // Alertas de Contas a Pagar (prioridade alta)
                if (alertasContasPagar && alertasContasPagar.length > 0) {
                    hasAlerts = true;
                    alertasContasPagar.forEach(conta => {
                        const diasTexto = conta.dias_restantes === 0 ? 'Vence hoje!' : 
                                         conta.dias_restantes === 1 ? 'Vence amanhã!' : 
                                         `Vence em ${conta.dias_restantes} dias`;
                        const valorFormatado = parseFloat(conta.valor).toFixed(2).replace('.', ',');
                        listaAlertas.innerHTML += `<li class="alerta-conta-pagar clickable" title="Clique para ir ao financeiro - contas a pagar"><div class="item-content"><div class="icon-item icon-alerta"><i class="fa-solid fa-exclamation-triangle"></i></div><div class="info-item"><p class="titulo">${conta.descricao}</p><p class="subtitulo">${conta.fornecedor_nome || 'Fornecedor não informado'} - R$ ${valorFormatado}</p><p class="subtitulo">${diasTexto} (${conta.data_vencimento_formatada})</p></div></div></li>`;
                    });
                }
                
                if (alertasCrediario && alertasCrediario.length > 0) {
                    hasAlerts = true;
                    alertasCrediario.forEach(venda => {
                        listaAlertas.innerHTML += `<li class="alerta-crediario clickable" data-cliente-nome="${venda.cliente_nome}" data-cliente-telefone="${venda.cliente_telefone}" data-valor-total="${venda.valor_total}" data-data-venda="${venda.data_venda}" title="Clique para enviar lembrete de cobrança"><div class="item-content"><div class="icon-item icon-alerta"><i class="fa-solid fa-credit-card"></i></div><div class="info-item"><p class="titulo">${venda.cliente_nome}</p><p class="subtitulo">Crediário em atraso: R$ ${parseFloat(venda.valor_total).toFixed(2).replace('.', ',')}</p></div></div></li>`;
                    });
                }
                if (alertasEstoque && alertasEstoque.length > 0) {
                    hasAlerts = true;
                    alertasEstoque.forEach(produto => {
                        listaAlertas.innerHTML += `<li class="alerta-estoque clickable" data-produto-id="${produto.id}" title="Clique para ir à entrada de estoque"><div class="item-content"><div class="icon-item icon-alerta"><i class="fa-solid fa-box-open"></i></div><div class="info-item"><p class="titulo">${produto.nome}</p><p class="subtitulo">Apenas ${produto.estoque_atual} em estoque.</p></div></div></li>`;
                    });
                }
                if (!hasAlerts) {
                    listaAlertas.innerHTML = '<li><div class="item-content"><div class="icon-item icon-alerta"><i class="fa-solid fa-thumbs-up"></i></div><div class="info-item"><p class="titulo">Nenhum alerta no momento.</p></div></div></li>';
                }
            }

            function atualizarAniversariantes(aniversariantes) {
                if (!aniversariantes || aniversariantes.length === 0) {
                    birthdayFab.style.display = 'none';
                    listaAniversariantes.innerHTML = '<li><div class="item-content"><div class="info-item"><p class="titulo">Nenhum aniversariante hoje.</p></div></div></li>';
                    return;
                }
                birthdayFab.style.display = 'block';
                listaAniversariantes.innerHTML = '';
                aniversariantes.forEach(aniversariante => {
                    listaAniversariantes.innerHTML += `<li class="clickable" data-cliente-telefone="${aniversariante.telefone}" title="Clique para enviar parabéns"><div class="item-content"><div class="icon-item icon-birthday"><i class="fa-solid fa-gift"></i></div><div class="info-item"><p class="titulo">${aniversariante.nome}</p></div></div><button class="btn btn-sm btn-sucesso"><i class="fa-brands fa-whatsapp"></i> Enviar</button></li>`;
                });
            }

            function renderizarStatusCaixa(caixa) {
                if (caixa && caixa.status === 'ABERTO') {
                    caixaStatusContainer.innerHTML = `<div class="caixa-status caixa-aberto">Caixa Aberto por <strong>${caixa.nome_usuario}</strong> com ${formatCurrency(caixa.valor_inicial)}</div>`;
                } else {
                    caixaStatusContainer.innerHTML = `<div class="caixa-status caixa-fechado"><strong>Atenção:</strong> O caixa está fechado. <a href="financeiro.php"><strong>Clique aqui para abrir.</strong></a></div>`;
                }
            }

            async function carregarDadosDashboard(data = null) {
                const dataFormatada = data ? data.toISOString().split('T')[0] : '';
                try {
                    const response = await fetch(`api/api_dashboard.php?acao=carregar_dashboard&data=${dataFormatada}`);
                    if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        if (resultado.dados.kpis) {
                            atualizarKPIs(resultado.dados.kpis);
                            atualizarAlertas(resultado.dados.alertas_estoque, resultado.dados.alertas_crediario, resultado.dados.alertas_contas_pagar);
                            renderizarStatusCaixa(resultado.dados.caixa_status);
                            atualizarAniversariantes(resultado.dados.aniversariantes_do_dia);
                        }
                        atualizarAgenda(resultado.dados.agenda_do_dia);
                        if (showBirthdayModalOnLoad && resultado.dados.aniversariantes_do_dia && resultado.dados.aniversariantes_do_dia.length > 0) {
                            birthdayModal.style.display = 'flex';
                        }
                    } else {
                        console.error("API retornou um erro:", resultado.mensagem);
                    }
                } catch (error) {
                    console.error("Falha ao buscar dados do dashboard:", error);
                }
            }

            // Funções do calendário
            async function carregarDadosCalendario(ano, mes) {
                try {
                    const response = await fetch(`api/api_dashboard.php?acao=carregar_calendario&ano=${ano}&mes=${mes}`);
                    const resultado = await response.json();
                    if (resultado.sucesso) {
                        osCalendarData = resultado.dados || {};
                        renderizarCalendario();
                    }
                } catch (error) {
                    console.error('Erro ao carregar dados do calendário:', error);
                }
            }

            function renderizarCalendario() {
                if (!calendarGrid || !calendarMonthYear) {
                    console.error('Elementos do calendário não encontrados!');
                    return;
                }
                
                const ano = currentCalendarDate.getFullYear();
                const mes = currentCalendarDate.getMonth();
                
                // Atualizar título
                const meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                              'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
                calendarMonthYear.textContent = `${meses[mes]} ${ano}`;
                
                // Limpar dias existentes (mantendo cabeçalhos)
                const existingDays = calendarGrid.querySelectorAll('.calendar-day');
                existingDays.forEach(day => day.remove());
                
                // Primeiro dia do mês e último dia do mês anterior
                const primeiroDia = new Date(ano, mes, 1);
                const ultimoDia = new Date(ano, mes + 1, 0);
                const primeiroDiaSemana = primeiroDia.getDay();
                
                // Dias do mês anterior
                const ultimoDiaMesAnterior = new Date(ano, mes, 0).getDate();
                for (let i = primeiroDiaSemana - 1; i >= 0; i--) {
                    const dia = ultimoDiaMesAnterior - i;
                    const dayElement = criarElementoDia(dia, true);
                    calendarGrid.appendChild(dayElement);
                }
                
                // Dias do mês atual
                const hoje = new Date();
                for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
                    const dataAtual = new Date(ano, mes, dia);
                    const isToday = dataAtual.toDateString() === hoje.toDateString();
                    const dayElement = criarElementoDia(dia, false, isToday, dataAtual);
                    calendarGrid.appendChild(dayElement);
                }
                
                // Dias do próximo mês para completar a grade
                const diasRestantes = 42 - (primeiroDiaSemana + ultimoDia.getDate());
                for (let dia = 1; dia <= diasRestantes; dia++) {
                    const dayElement = criarElementoDia(dia, true);
                    calendarGrid.appendChild(dayElement);
                }
            }

            function criarElementoDia(dia, isOtherMonth, isToday = false, dataCompleta = null) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                
                // Criar elemento para o número do dia
                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = dia;
                dayElement.appendChild(dayNumber);
                
                if (isOtherMonth) {
                    dayElement.classList.add('other-month');
                } else {
                    if (isToday) {
                        dayElement.classList.add('today');
                    }
                    
                    // Verificar se há OS para este dia
                    if (dataCompleta) {
                        const dataKey = dataCompleta.toISOString().split('T')[0];
                        const osData = osCalendarData[dataKey];
                        
                        if (osData && osData.quantidade > 0) {
                            dayElement.classList.add('has-os');
                            const countElement = document.createElement('div');
                            countElement.className = 'os-count';
                            countElement.textContent = osData.quantidade;
                            dayElement.appendChild(countElement);
                        }
                        
                        // Adicionar evento de clique
                        dayElement.addEventListener('click', () => {
                            agendaDataAtual = new Date(dataCompleta);
                            agendaTitulo.textContent = formatarTituloAgenda(agendaDataAtual);
                            carregarDadosDashboard(agendaDataAtual);
                        });
                    }
                }
                
                return dayElement;
            }

            function navegarCalendario(direcao) {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() + direcao);
                const ano = currentCalendarDate.getFullYear();
                const mes = currentCalendarDate.getMonth() + 1;
                carregarDadosCalendario(ano, mes);
            }

            function selecionarDiaCalendario(data) {
                const osDodia = calendarData[data] || { os_lista: [] };
                
                // Atualizar título da agenda
                const dataObj = new Date(data + 'T00:00:00');
                agendaTitulo.textContent = formatarTituloAgenda(dataObj);
                
                // Limpar agenda atual
                listaAgenda.innerHTML = '';
                
                if (osDodia.os_lista.length === 0) {
                    listaAgenda.innerHTML = '<li>Nenhuma OS para entrega nesta data</li>';
                    return;
                }
                
                // Renderizar OS do dia selecionado
                osDodia.os_lista.forEach(os => {
                    const agendaItem = document.createElement('li');
                    agendaItem.setAttribute('data-os-id', os.id);
                    
                    const statusClass = {
                        'AGUARDANDO': 'status-aguardando',
                        'EM_ANDAMENTO': 'status-andamento',
                        'AGUARDANDO_PECA': 'status-aguardando-peca',
                        'PRONTA': 'status-pronta',
                        'ENTREGUE': 'status-entregue'
                    }[os.status] || 'status-aguardando';
                    
                    agendaItem.innerHTML = `
                        <span class="agenda-cliente">${os.cliente_nome}</span>
                        <span class="agenda-bicicleta">${os.bicicleta_modelo}</span>
                        <span class="agenda-status ${statusClass}">${os.status.replace('_', ' ')}</span>
                    `;
                    
                    listaAgenda.appendChild(agendaItem);
                });
            }

            function formatarTituloAgenda(data) {
                const hoje = new Date();
                const amanha = new Date();
                amanha.setDate(hoje.getDate() + 1);
                hoje.setHours(0, 0, 0, 0);
                amanha.setHours(0, 0, 0, 0);
                data.setHours(0, 0, 0, 0);
                if (data.getTime() === hoje.getTime()) return 'Agenda de Hoje';
                if (data.getTime() === amanha.getTime()) return 'Agenda de Amanhã';
                return `Agenda de ${data.toLocaleDateString('pt-BR')}`;
            }

            async function navegarAgenda(dias) {
                agendaDataAtual.setDate(agendaDataAtual.getDate() + dias);
                agendaTitulo.textContent = formatarTituloAgenda(agendaDataAtual);
                listaAgenda.innerHTML = '<li>Carregando...</li>';
                await carregarDadosDashboard(agendaDataAtual);
            }

            const openHistoryModal = () => {
                historyModal.style.display = 'flex';
                // Carregar histórico da aba ativa
                const tabAtiva = document.querySelector('.tab-link.active').dataset.tab;
                if (tabAtiva === 'history-os' && !osHistoryLoaded) {
                    loadOsHistory(true);
                } else if (tabAtiva === 'history-vendas' && !vendasHistoryLoaded) {
                    loadVendasHistory(true);
                }
                // Se nenhuma aba está carregada, carregar OS por padrão
                if (!osHistoryLoaded && !vendasHistoryLoaded) {
                    loadOsHistory(true);
                }
            };
            const closeHistoryModal = () => historyModal.style.display = 'none';

            const loadOsHistory = async (reset = false) => {
                if (carregandoOS || (!reset && !osTemMais)) return;
                
                carregandoOS = true;
                const tbody = document.getElementById('history-os-tbody');
                
                if (reset) {
                    osOffset = 0;
                    osTemMais = true;
                    dadosOriginaisOS = [];
                    tbody.innerHTML = '<tr><td colspan="6">Carregando...</td></tr>';
                }
                
                try {
                    const params = new URLSearchParams({
                        acao: 'listar_historico_os',
                        limite: LIMITE_POR_PAGINA,
                        offset: osOffset
                    });
                    
                    const response = await fetch(`api/api_historico.php?${params}`);
                    const resultado = await response.json();
                    
                    if (resultado.sucesso && resultado.dados.length > 0) {
                        if (reset) {
                            dadosOriginaisOS = resultado.dados;
                            renderOsHistory(dadosOriginaisOS, false);
                        } else {
                            dadosOriginaisOS = [...dadosOriginaisOS, ...resultado.dados];
                            renderOsHistory(resultado.dados, true);
                        }
                        osOffset += resultado.dados.length;
                        osTemMais = resultado.tem_mais;
                        osHistoryLoaded = true;
                    } else if (reset) {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Nenhuma OS encontrada.</td></tr>';

                    }
                } catch (e) {
                    if (reset) {
                        tbody.innerHTML = '<tr><td colspan="6" class="error-message">Erro ao carregar histórico.</td></tr>';
                    }
                } finally {
                    carregandoOS = false;
                }
            };
            
            const renderOsHistory = (dados, append = false) => {
                 const tbody = document.getElementById('history-os-tbody');
                 if (!append) {
                     tbody.innerHTML = '';
                 }
                 dados.forEach(os => {
                     tbody.innerHTML += `<tr><td>#${os.id}</td><td>${os.cliente_nome}</td><td>${new Date(os.data_entrada).toLocaleDateString('pt-BR')}</td><td>${os.status}</td><td>${formatCurrency(os.valor_total)}</td><td><a href="imprimir_os.php?id=${os.id}" target="_blank" class="btn btn-sm btn-secundario"><i class="fa-solid fa-print"></i></a></td></tr>`;
                 });
             };
            const loadVendasHistory = async (reset = false) => {
                if (carregandoVendas || (!reset && !vendasTemMais)) return;
                
                carregandoVendas = true;
                const tbody = document.getElementById('history-vendas-tbody');
                
                if (reset) {
                    vendasOffset = 0;
                    vendasTemMais = true;
                    dadosOriginaisVendas = [];
                    tbody.innerHTML = '<tr><td colspan="7">Carregando...</td></tr>';
                }
                
                try {
                    const params = new URLSearchParams({
                        acao: 'listar_historico_vendas',
                        limite: LIMITE_POR_PAGINA,
                        offset: vendasOffset
                    });
                    
                    const response = await fetch(`api/api_historico.php?${params}`);
                    const resultado = await response.json();
                    
                    if (resultado.sucesso && resultado.dados.length > 0) {
                        if (reset) {
                            dadosOriginaisVendas = resultado.dados;
                            renderVendasHistory(dadosOriginaisVendas, false);
                        } else {
                            dadosOriginaisVendas = [...dadosOriginaisVendas, ...resultado.dados];
                            renderVendasHistory(resultado.dados, true);
                        }
                        vendasOffset += resultado.dados.length;
                        vendasTemMais = resultado.tem_mais;
                        vendasHistoryLoaded = true;
                    } else if (reset) {
                        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Nenhuma venda encontrada.</td></tr>';

                    }
                } catch (e) {
                    if (reset) {
                        tbody.innerHTML = '<tr><td colspan="7" class="error-message">Erro ao carregar histórico.</td></tr>';
                    }
                } finally {
                    carregandoVendas = false;
                }
            };
            
            const renderVendasHistory = (dados, append = false) => {
                 const tbody = document.getElementById('history-vendas-tbody');
                 if (!append) {
                     tbody.innerHTML = '';
                 }
                 dados.forEach(venda => {
                     const isCancelada = venda.status === 'CANCELADA';
                     const trClass = isCancelada ? 'text-muted' : '';
                     const statusBadge = `<span class="status status-${venda.status}">${venda.status}</span>`;
                     let acoes = `<a href="imprimir_venda.php?id=${venda.id}" target="_blank" class="btn btn-sm btn-secundario" title="Imprimir"><i class="fa-solid fa-print"></i></a>`;
                     tbody.innerHTML += `<tr class="${trClass}"><td>#${venda.id}</td><td>${venda.cliente_nome || 'Consumidor Final'}</td><td>${new Date(venda.data_venda).toLocaleDateString('pt-BR')}</td><td>${statusBadge}</td><td>${formatCurrency(venda.valor_total)}</td><td>${acoes}</td></tr>`;
                 });
             };
             
             // Função de busca no histórico
             const filtrarHistorico = (termo) => {
                 const termoLower = termo.toLowerCase();
                 const tabAtiva = document.querySelector('.tab-link.active').dataset.tab;
                 
                 if (tabAtiva === 'history-os') {
                     const dadosFiltrados = dadosOriginaisOS.filter(os => {
                         return (
                             os.id.toString().includes(termoLower) ||
                             os.cliente_nome.toLowerCase().includes(termoLower) ||
                             os.status.toLowerCase().includes(termoLower) ||
                             new Date(os.data_entrada).toLocaleDateString('pt-BR').includes(termoLower) ||
                             os.valor_total.toString().includes(termoLower)
                         );
                     });
                     renderOsHistory(dadosFiltrados);
                 } else if (tabAtiva === 'history-vendas') {
                     const dadosFiltrados = dadosOriginaisVendas.filter(venda => {
                         return (
                             venda.id.toString().includes(termoLower) ||
                             (venda.cliente_nome && venda.cliente_nome.toLowerCase().includes(termoLower)) ||
                             venda.metodo_pagamento.toLowerCase().includes(termoLower) ||
                             venda.status.toLowerCase().includes(termoLower) ||
                             new Date(venda.data_venda).toLocaleDateString('pt-BR').includes(termoLower) ||
                             venda.valor_total.toString().includes(termoLower)
                         );
                     });
                     renderVendasHistory(dadosFiltrados);
                 }
             };

            // Event Listeners
            cardOsAtivas.addEventListener('click', abrirModalOsAtivas);
            osAtivasModal.querySelector('.modal-close').onclick = fecharModalOsAtivas;
            btnAgendaPrev.addEventListener('click', () => navegarAgenda(-1));
            btnAgendaNext.addEventListener('click', () => navegarAgenda(1));
            listaAgenda.addEventListener('click', (e) => {
                const target = e.target.closest('li[data-os-id]');
                if (target) {
                    window.location.href = `editar_os.php?id=${target.dataset.osId}`;
                }
            });
            listaAlertas.addEventListener('click', (e) => {
                const target = e.target.closest('li.alerta-crediario');
                if (target) {
                    const nome = target.dataset.clienteNome;
                    const telefone = target.dataset.clienteTelefone;
                    const valor = parseFloat(target.dataset.valorTotal).toFixed(2).replace('.', ',');
                    const dataVenda = new Date(target.dataset.dataVenda).toLocaleDateString('pt-BR');
                    const mensagem = `Olá, ${nome}! Passando para lembrar sobre o débito referente à compra do dia ${dataVenda}, no valor de R$ ${valor}, que ainda está em aberto. Por favor, entre em contato para regularizarmos. Agradecemos a compreensão! ${window.lojaConfig?.config?.nome_loja || 'BikeOS'}.`;
                    const telefoneFormatado = '55' + telefone.replace(/\D/g, '');
                    window.open(`https://wa.me/${telefoneFormatado}?text=${encodeURIComponent(mensagem)}`, '_blank');
                }
                
                // Event listener para alertas de contas a pagar
                const targetContaPagar = e.target.closest('li.alerta-conta-pagar');
                if (targetContaPagar) {
                    window.location.href = 'financeiro.php#contas-pagar';
                }
                
                // Event listener para alertas de estoque baixo
                const targetEstoque = e.target.closest('li.alerta-estoque');
                if (targetEstoque) {
                    const produtoId = targetEstoque.dataset.produtoId;
                    window.location.href = `entrada_estoque.php?produto_id=${produtoId}`;
                }
            });
            birthdayFab.addEventListener('click', () => {
                birthdayModal.style.display = 'flex';
            });
            birthdayModal.querySelector('.modal-close').onclick = () => birthdayModal.style.display = 'none';
            listaAniversariantes.addEventListener('click', (e) => {
                const target = e.target.closest('li[data-cliente-telefone]');
                if (target) {
                    const telefone = target.dataset.clienteTelefone;
                    const telefoneFormatado = '55' + telefone.replace(/\D/g, '');
                    const mensagem = `Olá! A equipe da ${window.lojaConfig?.config?.nome_loja || 'BikeOS'} te deseja um feliz aniversário, com muitas pedaladas e alegrias! 🎉🚴‍♂️`;
                    window.open(`https://wa.me/${telefoneFormatado}?text=${encodeURIComponent(mensagem)}`, '_blank');
                }
            });
            historyFab.addEventListener('click', openHistoryModal);
            historyModal.querySelector('.modal-close').onclick = closeHistoryModal;
            historyModal.querySelectorAll('.tab-link').forEach(tab => {
                tab.addEventListener('click', () => {
                    historyModal.querySelectorAll('.tab-link, .tab-content').forEach(el => el.classList.remove('active'));
                    tab.classList.add('active');
                    document.getElementById(tab.dataset.tab).classList.add('active');
                    
                    // Limpar campo de busca ao trocar de aba
                    if (searchHistoricoInput) {
                        searchHistoricoInput.value = '';
                    }
                    
                    // Carregar histórico da aba selecionada se ainda não foi carregado
                    if (tab.dataset.tab === 'history-os' && !osHistoryLoaded) {
                        loadOsHistory(true);
                    } else if (tab.dataset.tab === 'history-vendas' && !vendasHistoryLoaded) {
                        loadVendasHistory(true);
                    }
                });
            });
            
            // Event listener para o campo de busca do histórico
             searchHistoricoInput = document.getElementById('search-historico');
             if (searchHistoricoInput) {
                 searchHistoricoInput.addEventListener('input', function() {
                     const termo = this.value.trim();
                     if (termo === '') {
                         // Se campo vazio, mostrar todos os dados
                         const tabAtiva = document.querySelector('.tab-link.active').dataset.tab;
                         if (tabAtiva === 'history-os') {
                             renderOsHistory(dadosOriginaisOS);
                         } else if (tabAtiva === 'history-vendas') {
                             renderVendasHistory(dadosOriginaisVendas);
                         }
                     } else {
                         filtrarHistorico(termo);
                     }
                 });
             }
             
             // Event listener para o botão de limpar busca do histórico
             const btnLimparBuscaHistorico = document.getElementById('btn-limpar-busca-historico');
             if (btnLimparBuscaHistorico) {
                 btnLimparBuscaHistorico.addEventListener('click', function() {
                     if (searchHistoricoInput) {
                         searchHistoricoInput.value = '';
                         // Mostrar todos os dados
                         const tabAtiva = document.querySelector('.tab-link.active').dataset.tab;
                         if (tabAtiva === 'history-os') {
                             renderOsHistory(dadosOriginaisOS);
                         } else if (tabAtiva === 'history-vendas') {
                             renderVendasHistory(dadosOriginaisVendas);
                         }
                     }
                 });
             }
             
             // Event listeners para scroll infinito no histórico
             const historyOsDiv = document.getElementById('history-os');
             const historyVendasDiv = document.getElementById('history-vendas');
             
             if (historyOsDiv) {
                 historyOsDiv.addEventListener('scroll', function() {
                     if (this.scrollTop + this.clientHeight >= this.scrollHeight - 5) {
                         const tabAtiva = document.querySelector('.tab-link.active').dataset.tab;
                         if (tabAtiva === 'history-os' && osTemMais && !carregandoOS) {
                             loadOsHistory(false);
                         }
                     }
                 });
             }
             
             if (historyVendasDiv) {
                 historyVendasDiv.addEventListener('scroll', function() {
                     if (this.scrollTop + this.clientHeight >= this.scrollHeight - 5) {
                         const tabAtiva = document.querySelector('.tab-link.active').dataset.tab;
                         if (tabAtiva === 'history-vendas' && vendasTemMais && !carregandoVendas) {
                             loadVendasHistory(false);
                         }
                     }
                 });
             }

            // NOVO: Event listener para o card de satisfação
            cardSatisfacao.addEventListener('click', () => {
                window.location.href = 'avaliacoes.php';
            });

            // Event listeners do calendário
            calendarPrev.addEventListener('click', () => navegarCalendario(-1));
            calendarNext.addEventListener('click', () => navegarCalendario(1));

            // Inicialização
            agendaTitulo.textContent = formatarTituloAgenda(agendaDataAtual);
            carregarDadosDashboard();
            
            // Inicializar calendário
            const hoje = new Date();
            carregarDadosCalendario(hoje.getFullYear(), hoje.getMonth() + 1);
            
            // Controle do Modal de Dica de Zoom
             const zoomTipModal = document.getElementById('zoom-tip-modal');
             const closeZoomTipBtn = document.getElementById('close-zoom-tip');
            
            // Verificar se deve mostrar a dica de zoom (apenas uma vez por sessão)
             const zoomTipShown = sessionStorage.getItem('bikeos_zoom_tip_shown');
             
             if (!zoomTipShown) {
                 setTimeout(() => {
                     zoomTipModal.style.display = 'flex';
                     // Marcar como exibido nesta sessão
                     sessionStorage.setItem('bikeos_zoom_tip_shown', 'true');
                 }, 2000); // Aguardar 2 segundos após carregar a página
             }
            
            // Event listener para fechar o modal
             closeZoomTipBtn.addEventListener('click', () => {
                 // Apenas fechar o modal (não salvar preferência)
                 zoomTipModal.style.display = 'none';
             });
            
            // Event listener para fechar modal clicando no X
            zoomTipModal.querySelector('.modal-close').addEventListener('click', () => {
                zoomTipModal.style.display = 'none';
            });
            
            // Event listener para fechar modal clicando fora dele
            zoomTipModal.addEventListener('click', (e) => {
                if (e.target === zoomTipModal) {
                    zoomTipModal.style.display = 'none';
                }
            });
        });
    </script>
    
    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
    
    <!-- Sistema de Alertas para Fechamento do Caixa -->
    <script src="assets/js/alerta-fechamento-caixa.js"></script>

</body>

</html>
