<?php
// ARQUIVO: acompanhar_os.php (VERSÃO ATUALIZADA)
require_once 'api/db_connection.php';
$os_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : '';

if ($os_id <= 0 || empty($codigo)) {
    $show_form = true;
} else {
    $show_form = false;
    $stmt = $pdo->prepare("SELECT id FROM ordens_servico WHERE id = ? AND codigo_retirada = ?");
    $stmt->execute([$os_id, $codigo]);
    if ($stmt->fetch() === false) {
        $error_message = "Ordem de Serviço não encontrada ou código inválido.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhar OS #<?php echo $os_id; ?> | <?php require_once 'helpers/empresa_helper.php'; require_once 'api/db_connection.php'; echo getNomeEmpresa($pdo); ?></title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
        }

        .tracking-card {
            background-color: var(--cor-branco);
            border-radius: var(--raio-borda);
            box-shadow: var(--sombra-card);
            padding: 2rem;
        }

        .tracking-header {
            text-align: center;
            border-bottom: 1px solid var(--cor-borda);
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .tracking-header h1 {
            font-size: 1.8rem;
            color: var(--cor-primaria);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-item strong {
            display: block;
            color: var(--cor-texto-secundario);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .info-item span {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .section-title {
            font-size: 1.3rem;
            margin-top: 2.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--cor-primaria);
            display: inline-block;
        }

        .status-timeline {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: linear-gradient(to right, #dee2e6 0%, #dee2e6 100%);
            height: 4px;
            border-radius: 2px;
            margin-top: 3rem;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 4px;
            background: linear-gradient(to right, var(--cor-sucesso) 0%, var(--cor-primaria) 100%);
            border-radius: 2px;
            transition: width 0.8s ease;
            z-index: 1;
            width: var(--timeline-progress, 0%);
        }

        .status-step {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            z-index: 2;
            padding: 0;
        }

        .status-step:last-child {
            padding-bottom: 0;
        }

        .status-step::before {
            display: none;
        }

        .status-step:last-child::before {
            display: none;
        }

        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #dee2e6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            z-index: 3;
            flex-shrink: 0;
            margin-right: 0;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
            margin-top: -18px;
        }

        .status-content {
            text-align: center;
            min-height: 60px;
            max-width: 120px;
        }

        .status-content h4 {
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.2;
        }

        .status-content p {
            font-size: 0.8rem;
            color: #6c757d;
            margin: 0;
        }

        .status-step.completed .status-icon {
            background-color: var(--cor-sucesso);
            transform: scale(1.1);
        }

        .status-step.active .status-icon {
            background-color: var(--cor-primaria);
            animation: pulse 2s infinite;
            transform: scale(1.2);
        }

        .status-step.active::before {
            background-color: var(--cor-sucesso);
        }

        .status-step.active h4 {
            color: var(--cor-primaria);
            font-weight: bold;
        }

        .bike-progress {
            position: absolute;
            top: -35px;
            left: 0;
            font-size: 2.5rem;
            color: var(--cor-primaria);
            transition: left 0.8s ease;
            z-index: 4;
            animation: bike-bounce 1.5s ease-in-out infinite alternate;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        @keyframes bike-bounce {
            0% { transform: translateY(0px) rotate(-2deg); }
            100% { transform: translateY(-4px) rotate(2deg); }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(var(--cor-primaria-rgb), 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(var(--cor-primaria-rgb), 0); }
            100% { box-shadow: 0 0 0 0 rgba(var(--cor-primaria-rgb), 0); }
        }

        .whatsapp-button {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            margin-top: 2rem;
            width: 100%;
            justify-content: center;
            font-size: 1.1rem;
            padding: 1rem;
            background-color: #25D366;
            color: white;
            text-decoration: none;
        }

        .whatsapp-button:hover {
            background-color: #128C7E;
        }

        .observacao-item {
            background: #f8f9fa;
            padding: 1rem;
            border-left: 5px solid var(--cor-primaria);
            border-radius: var(--raio-borda);
            margin-bottom: 1rem;
        }

        .observacao-meta {
            font-size: 0.8rem;
            color: #6c757d;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .observacao-imagem {
            max-width: 200px;
            border-radius: var(--raio-borda);
            margin-top: 1rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .observacao-imagem:hover {
            transform: scale(1.05);
        }

        .avaliacao-container {
            background-color: #fff3cd;
            padding: 1.5rem;
            border-radius: var(--raio-borda);
            margin-top: 2rem;
            border: 1px solid #ffeeba;
        }

        .rating-stars {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
            direction: rtl;
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            font-size: 2.5rem;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating-stars input[type="radio"]:checked~label,
        .rating-stars label:hover,
        .rating-stars label:hover~label {
            color: #ffc107;
        }

        .rating-stars input[type="radio"]:checked+label {
            color: #ffc107;
        }

        .permissao-checkbox {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1rem;
        }

        .permissao-checkbox input {
            margin-right: 10px;
        }

        .avaria-item {
            background: #f8f9fa;
            padding: 1rem;
            border-left: 5px solid #dc3545;
            border-radius: var(--raio-borda);
            margin-bottom: 1rem;
        }

        .avaria-item p {
            margin-bottom: 0.5rem;
        }

        .avaria-foto {
            margin-top: 0.5rem;
        }

        .avaria-imagem {
            max-width: 300px;
            max-height: 200px;
            border-radius: var(--raio-borda);
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .avaria-imagem:hover {
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .avaria-imagem {
                max-width: 100%;
                max-height: 150px;
            }
        }
    </style>
</head>

<body>
    <main class="container">
        <?php if (isset($error_message)): ?>
            <div class="card" style="text-align: center;">
                <h2><i class="fa-solid fa-circle-exclamation"></i> Erro</h2>
                <p class="error-message"><?php echo $error_message; ?></p>
                <a href="acompanhar_os.php" class="btn btn-primario">Tentar Novamente</a>
            </div>
        <?php elseif ($show_form): ?>
            <div class="card" style="max-width: 500px; margin: 2rem auto;">
                <h2><i class="fa-solid fa-magnifying-glass"></i> Acompanhar Ordem de Serviço</h2>
                <form method="GET" action="acompanhar_os.php">
                    <div class="form-group"><label for="id" class="form-label">Número da OS</label><input type="number" name="id" class="form-input" placeholder="Ex: 123" required></div>
                    <div class="form-group"><label for="codigo" class="form-label">Código de Retirada</label><input type="text" name="codigo" class="form-input" placeholder="Ex: 4567" required></div>
                    <button type="submit" class="btn btn-destaque" style="width:100%;">Acompanhar</button>
                </form>
            </div>
        <?php else: ?>
            <div class="tracking-card">
                <div id="os-details">
                    <p style="text-align:center;">Carregando informações...</p>
                </div>
            </div>
        <?php endif; ?>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const osId = <?php echo $os_id; ?>;
            const codigo = '<?php echo $codigo; ?>';
            const detailsContainer = document.getElementById('os-details');

            const carregarDadosOS = async () => {
                if (!osId || !codigo) return;
                try {
                    const response = await fetch(`api/api_os.php?acao=buscar_os_publica&id=${osId}&codigo=${codigo}`);
                    const resultado = await response.json();
                    if (!resultado.sucesso) throw new Error(resultado.mensagem);

                    const os = resultado.dados;
                    const numeroWhatsapp = "5532999628568";
                    const nomeCliente = os.os_info.cliente_nome;
                    const mensagemWhatsapp = encodeURIComponent(`Olá! Tenho uma dúvida sobre a minha Ordem de Serviço de número #${os.os_info.id}, em nome de ${nomeCliente}.`);
                    const linkWhatsapp = `https://wa.me/${numeroWhatsapp}?text=${mensagemWhatsapp}`;
                    const statusOrder = ['ORCAMENTO', 'AGUARDANDO_PECA', 'EM_SERVICO', 'PRONTA', 'FINALIZADA'];
                    const statusLabels = {
                        'ORCAMENTO': {
                            title: 'Orçamento',
                            icon: 'fa-file-invoice-dollar'
                        },
                        'AGUARDANDO_PECA': {
                            title: 'Aguardando Peças',
                            icon: 'fa-box'
                        },
                        'EM_SERVICO': {
                            title: 'Em Serviço',
                            icon: 'fa-gears'
                        },
                        'PRONTA': {
                            title: 'Pronta para Retirada',
                            icon: 'fa-flag-checkered'
                        },
                        'FINALIZADA': {
                            title: 'Serviço Concluído',
                            icon: 'fa-check-circle'
                        }
                    };
                    const currentStatusIndex = statusOrder.indexOf(os.os_info.status);

                    let timelineHtml = '';
                    statusOrder.forEach((status, index) => {
                        let stepClass = '';
                        if (index < currentStatusIndex) stepClass = 'completed';
                        else if (index === currentStatusIndex) stepClass = 'active';
                        timelineHtml += `<li class="status-step ${stepClass}"><div class="status-icon"><i class="fa-solid ${statusLabels[status].icon}"></i></div><div class="status-content"><h4>${statusLabels[status].title}</h4></div></li>`;
                    });

                    // Calcular posição da bicicleta baseada no progresso
                    const progressPercentage = currentStatusIndex >= 0 ? (currentStatusIndex / (statusOrder.length - 1)) * 100 : 0;
                    const bikePosition = Math.min(progressPercentage, 100);
                    
                    // Bicicleta removida do timeline

                    let servicosHtml = '';
                    if (os.os_servicos && os.os_servicos.length > 0) os.os_servicos.forEach(s => servicosHtml += `<tr><td>${s.nome}</td><td class="text-right">R$ ${parseFloat(s.preco_cobrado).toFixed(2).replace('.',',')}</td></tr>`);
                    let produtosHtml = '';
                    if (os.os_produtos && os.os_produtos.length > 0) os.os_produtos.forEach(p => produtosHtml += `<tr><td>${p.quantidade}x ${p.nome}</td><td class="text-right">R$ ${(p.quantidade * p.preco_unitario_cobrado).toFixed(2).replace('.',',')}</td></tr>`);
                    let avariasHtml = '';
                    if (os.os_info.bicicleta_avarias) {
                        const avarias = JSON.parse(os.os_info.bicicleta_avarias);
                        if (avarias.length > 0) {
                            avariasHtml += '<h3 class="section-title"><i class="fa-solid fa-shield-halved"></i> Avarias Registradas na Entrada</h3>';
                            avarias.forEach(avaria => {
                                if (avaria.texto) {
                                    avariasHtml += '<div class="avaria-item">';
                                    avariasHtml += `<p><strong>Descrição:</strong> ${avaria.texto}</p>`;
                                    if (avaria.imagem_url) {
                                        avariasHtml += `<div class="avaria-foto"><a href="${avaria.imagem_url}" target="_blank"><img src="${avaria.imagem_url}" class="avaria-imagem" alt="Foto da avaria"></a></div>`;
                                    }
                                    avariasHtml += '</div>';
                                }
                            });
                        }
                    }
                    let observacoesHtml = '';
                    if (os.os_observacoes && os.os_observacoes.length > 0) {
                        os.os_observacoes.forEach((obs, index) => {
                            observacoesHtml += `<div class="observacao-item"><div class="observacao-meta">Por: <strong>${obs.usuario_nome}</strong> em ${new Date(obs.data_criacao).toLocaleString('pt-BR')}</div>`;
                            if (obs.texto) observacoesHtml += `<p>${obs.texto.replace(/\n/g, '<br>')}</p>`;
                            if (obs.imagem_url) observacoesHtml += `<a href="${obs.imagem_url}" target="_blank"><img src="${obs.imagem_url}" class="observacao-imagem" alt="Foto da observação"></a>`;
                            
                            // Adicionar seção de comentário do cliente
                            observacoesHtml += `
                                <div class="comentario-cliente-section" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #dee2e6;">`;
                            
                            if (obs.comentario_cliente) {
                                // Exibir comentário existente
                                observacoesHtml += `
                                    <div class="comentario-existente" style="background-color: #e3f2fd; padding: 0.75rem; border-radius: 0.375rem; border-left: 4px solid #2196f3;">
                                        <div style="font-size: 0.8rem; color: #666; margin-bottom: 0.25rem;">Seu comentário:</div>
                                        <p style="margin: 0; color: #1565c0;">${obs.comentario_cliente}</p>
                                    </div>`;
                            } else {
                                // Exibir formulário para adicionar comentário
                                observacoesHtml += `
                                    <div class="form-comentario" id="form-comentario-${obs.id}">
                                        <label style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; display: block;">Adicione seu comentário sobre esta observação:</label>
                                        <textarea class="form-input" id="textarea-comentario-${obs.id}" rows="2" placeholder="Digite seu comentário..." style="margin-bottom: 0.5rem;"></textarea>
                                        <button type="button" class="btn btn-primario btn-sm" onclick="salvarComentario(${obs.id}, ${osId}, '${codigo}')" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">Enviar Comentário</button>
                                    </div>`;
                            }
                            
                            observacoesHtml += `</div>`;
                            observacoesHtml += `</div>`;
                        });
                    } else {
                        observacoesHtml += '<p>Nenhuma observação técnica adicionada ainda.</p>';
                    }

                    let avaliacaoHtml = '';
                    if (os.os_info.status === 'FINALIZADA' && !os.os_info.ja_avaliado) {
                        avaliacaoHtml = `
                            <div id="avaliacao-container" class="avaliacao-container">
                                <h3 style="text-align:center; margin-bottom: 1rem;">Serviço Concluído! O que você achou?</h3>
                                <form id="form-avaliacao">
                                    <div class="rating-stars">
                                        <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="5 estrelas"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 estrelas"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 estrelas"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 estrelas"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 estrela"><i class="fa-solid fa-star"></i></label>
                                    </div>
                                    <div class="form-group">
                                        <label for="comentario" class="form-label">Deixe uma sugestão ou elogio (opcional):</label>
                                        <textarea id="comentario" class="form-input" rows="3"></textarea>
                                    </div>
                                    <div class="permissao-checkbox">
                                        <input type="checkbox" id="permissao-exibir" name="permissao_exibir" value="1" checked>
                                        <label for="permissao-exibir">Autorizo a exibição da minha avaliação no site da loja.</label>
                                    </div>
                                    <button type="submit" class="btn btn-destaque" style="width:100%; margin-top: 1rem;">Enviar Avaliação</button>
                                </form>
                            </div>`;
                    } else if (os.os_info.ja_avaliado) {
                        avaliacaoHtml = `<div class="avaliacao-container" style="background-color: #d1e7dd; border-color: #badbcc;"><p style="text-align:center; margin:0;"><strong>Obrigado!</strong> Já recebemos sua avaliação para este serviço.</p></div>`;
                    }

                    detailsContainer.innerHTML = `
                        <div class="tracking-header"><h1>Acompanhamento da OS #${osId}</h1><p>Veja em tempo real o status do serviço da sua bicicleta.</p></div>
                        <div class="info-grid">
                            <div class="info-item"><strong>Cliente:</strong> <span>${os.os_info.cliente_nome}</span></div>
                            <div class="info-item"><strong>Bicicleta:</strong> <span>${os.os_info.bicicleta_marca || ''} ${os.os_info.bicicleta_modelo}</span></div>
                            <div class="info-item"><strong>Previsão de Entrega:</strong> <span>${new Date(os.os_info.previsao_entrega + 'T12:00:00').toLocaleDateString('pt-BR')}</span></div>
                            ${os.os_info.status === 'FINALIZADA' ? `<div class="info-item"><strong>Serviço Concluído em:</strong> <span>${new Date(os.os_info.data_finalizacao).toLocaleDateString('pt-BR')}</span></div>` : ''}
                        </div>
                        ${os.os_info.problema_relatado ? `<h3 class="section-title"><i class="fa-solid fa-exclamation-triangle"></i> Problema Relatado</h3><div class="observacao-item"><p>${os.os_info.problema_relatado.replace(/\n/g, '<br>')}</p></div>` : ''}
                        <h3 class="section-title"><i class="fa-solid fa-tasks"></i> Andamento do Serviço</h3>
                        <ul class="status-timeline" id="status-timeline">${timelineHtml}</ul>
                        ${avariasHtml}
                        <h3 class="section-title"><i class="fa-solid fa-list-check"></i> Itens e Serviços</h3>
                        <table class="tabela"><tbody>${servicosHtml}${produtosHtml}<tr style="font-weight: bold; border-top: 1px solid #ccc;"><td>Valor Total:</td><td class="text-right">R$ ${parseFloat(os.os_info.valor_total).toFixed(2).replace('.',',')}</td></tr></tbody></table>
                        <h3 class="section-title"><i class="fa-solid fa-comment-dots"></i> Histórico e Observações</h3>
                        <div class="observacoes-container">${observacoesHtml}</div>
                        ${avaliacaoHtml}
                        <a href="${linkWhatsapp}" class="btn whatsapp-button" target="_blank"><i class="fa-brands fa-whatsapp"></i> Tem alguma dúvida? Fale conosco!</a>
                    `;

                    // Atualizar barra de progresso após renderizar
                    setTimeout(() => {
                        const timeline = document.getElementById('status-timeline');
                        if (timeline && timeline.querySelector('::before')) {
                            timeline.style.setProperty('--progress-width', `${bikePosition}%`);
                        }
                        // Aplicar largura da barra de progresso via JavaScript
                        const timelineElement = document.querySelector('.status-timeline::before');
                        if (timelineElement) {
                            timelineElement.style.width = `${bikePosition}%`;
                        }
                        // Alternativa: aplicar via CSS custom property
                        document.documentElement.style.setProperty('--timeline-progress', `${bikePosition}%`);
                    }, 100);

                    const formAvaliacao = document.getElementById('form-avaliacao');
                    if (formAvaliacao) {
                        formAvaliacao.addEventListener('submit', async (e) => {
                            e.preventDefault();
                            const formData = new FormData(formAvaliacao);
                            const payload = {
                                os_id: osId,
                                codigo: codigo,
                                nota: formData.get('rating'),
                                comentario: document.getElementById('comentario').value,
                                permissao_exibir: document.getElementById('permissao-exibir').checked
                            };

                            try {
                                const response = await fetch('api/api_os.php?acao=salvar_avaliacao_os', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify(payload)
                                });
                                const resultado = await response.json();
                                if (!resultado.sucesso) throw new Error(resultado.mensagem);

                                document.getElementById('avaliacao-container').innerHTML = `<p style="text-align:center; font-weight:bold; color: #0a3622;">Obrigado pelo seu feedback!</p>`;
                                document.getElementById('avaliacao-container').style.backgroundColor = '#d1e7dd';
                                document.getElementById('avaliacao-container').style.borderColor = '#badbcc';

                            } catch (error) {
                                alert('Erro ao enviar avaliação: ' + error.message);
                            }
                        });
                    }

                } catch (error) {
                    detailsContainer.innerHTML = `<p class="error-message">Erro ao carregar os dados da OS: ${error.message}</p>`;
                }
            };

            if (!<?php echo json_encode($show_form); ?>) {
                carregarDadosOS();
            }
        });

        // Função para salvar comentário do cliente
        async function salvarComentario(observacaoId, osId, codigo) {
            const textarea = document.getElementById(`textarea-comentario-${observacaoId}`);
            const comentario = textarea.value.trim();
            
            if (!comentario) {
                alert('Por favor, digite um comentário antes de enviar.');
                return;
            }
            
            try {
                const response = await fetch('api/api_os.php?acao=salvar_comentario_cliente', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        observacao_id: observacaoId,
                        os_id: osId,
                        codigo: codigo,
                        comentario: comentario
                    })
                });
                
                const resultado = await response.json();
                
                if (!resultado.sucesso) {
                    throw new Error(resultado.mensagem);
                }
                
                alert('Comentário enviado com sucesso!');
                
                // Recarregar os dados da OS para mostrar o comentário salvo
                carregarDadosOS();
                
            } catch (error) {
                alert('Erro ao enviar comentário: ' + error.message);
            }
        }
    </script>

    <!-- Sistema de Configurações Dinâmicas da Loja -->
    <script src="assets/js/dynamic-config.js"></script>
</body>

</html>