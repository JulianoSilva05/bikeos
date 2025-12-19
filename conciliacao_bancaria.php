<?php
session_start();
require_once 'auth_check.php';
require_once 'api/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conciliação Bancária - BikeOS</title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .conciliacao-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .conciliacao-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .filtros-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #28a745;
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #28a745;
            color: white;
        }
        
        .btn-primary:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .conciliacao-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .extrato-container, .movimentacoes-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .extrato-container h3, .movimentacoes-container h3 {
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .upload-area {
            border: 2px dashed #28a745;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .upload-area:hover {
            background: #f8f9fa;
        }
        
        .upload-area.dragover {
            background: #e8f5e8;
            border-color: #20c997;
        }
        
        .lista-movimentacoes {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .movimentacao-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        
        .movimentacao-item:hover {
            background: #f8f9fa;
        }
        
        .movimentacao-item.conciliado {
            background: #d4edda;
            border-color: #c3e6cb;
        }
        
        .movimentacao-item.pendente {
            background: #fff3cd;
            border-color: #ffeaa7;
        }
        
        .movimentacao-info {
            flex: 1;
        }
        
        .movimentacao-data {
            font-weight: 600;
            color: #495057;
        }
        
        .movimentacao-descricao {
            color: #6c757d;
            font-size: 14px;
        }
        
        .movimentacao-valor {
            font-weight: 600;
            font-size: 16px;
        }
        
        .valor-positivo {
            color: #28a745;
        }
        
        .valor-negativo {
            color: #dc3545;
        }
        
        .movimentacao-acoes {
            display: flex;
            gap: 5px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-conciliado {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pendente {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-divergente {
            background: #f8d7da;
            color: #721c24;
        }
        
        .resumo-conciliacao {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .resumo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .resumo-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }
        
        .resumo-card h4 {
            margin: 0 0 10px 0;
            color: #495057;
        }
        
        .resumo-valor {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .loading.show {
            display: block;
        }
        
        @media (max-width: 768px) {
            .conciliacao-grid {
                grid-template-columns: 1fr;
            }
            
            .filtros-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '_partials/navigation_header.php'; ?>
    
    <?php include '_partials/menu_admin.php'; ?>
    
    <div class="conciliacao-container">
        <div class="conciliacao-header">
            <h1><i class="fas fa-university"></i> Conciliação Bancária</h1>
            <p>Concilie automaticamente seus extratos bancários com as movimentações financeiras</p>
        </div>
        
        <!-- Filtros -->
        <div class="filtros-container">
            <h3><i class="fas fa-filter"></i> Filtros</h3>
            <div class="filtros-grid">
                <div class="form-group">
                    <label for="conta_bancaria">Conta Bancária:</label>
                    <select id="conta_bancaria" name="conta_bancaria">
                        <option value="">Selecione uma conta</option>
                        <option value="001">Banco do Brasil - CC 12345-6</option>
                        <option value="237">Bradesco - CC 98765-4</option>
                        <option value="104">Caixa Econômica - CC 54321-0</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="data_inicio">Data Início:</label>
                    <input type="date" id="data_inicio" name="data_inicio">
                </div>
                
                <div class="form-group">
                    <label for="data_fim">Data Fim:</label>
                    <input type="date" id="data_fim" name="data_fim">
                </div>
                
                <div class="form-group">
                    <label for="status_conciliacao">Status:</label>
                    <select id="status_conciliacao" name="status_conciliacao">
                        <option value="">Todos</option>
                        <option value="conciliado">Conciliado</option>
                        <option value="pendente">Pendente</option>
                        <option value="divergente">Divergente</option>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="button" class="btn btn-primary" onclick="filtrarMovimentacoes()">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <button type="button" class="btn btn-info" onclick="conciliarAutomatico()">
                    <i class="fas fa-magic"></i> Conciliação Automática
                </button>
                <button type="button" class="btn btn-secondary" onclick="exportarConciliacao()">
                    <i class="fas fa-file-excel"></i> Exportar
                </button>
            </div>
        </div>
        
        <!-- Resumo da Conciliação -->
        <div class="resumo-conciliacao">
            <h3><i class="fas fa-chart-pie"></i> Resumo da Conciliação</h3>
            <div class="resumo-grid">
                <div class="resumo-card">
                    <h4>Saldo Banco</h4>
                    <p class="resumo-valor valor-positivo" id="saldoBanco">R$ 0,00</p>
                </div>
                <div class="resumo-card">
                    <h4>Saldo Sistema</h4>
                    <p class="resumo-valor valor-positivo" id="saldoSistema">R$ 0,00</p>
                </div>
                <div class="resumo-card">
                    <h4>Diferença</h4>
                    <p class="resumo-valor" id="diferenca">R$ 0,00</p>
                </div>
                <div class="resumo-card">
                    <h4>Conciliados</h4>
                    <p class="resumo-valor" id="totalConciliados">0</p>
                </div>
                <div class="resumo-card">
                    <h4>Pendentes</h4>
                    <p class="resumo-valor" id="totalPendentes">0</p>
                </div>
            </div>
        </div>
        
        <!-- Grid Principal -->
        <div class="conciliacao-grid">
            <!-- Extrato Bancário -->
            <div class="extrato-container">
                <h3><i class="fas fa-file-upload"></i> Extrato Bancário</h3>
                
                <div class="upload-area" id="uploadArea">
                    <i class="fas fa-cloud-upload-alt fa-3x" style="color: #28a745; margin-bottom: 15px;"></i>
                    <p><strong>Arraste o arquivo do extrato aqui</strong></p>
                    <p>ou clique para selecionar</p>
                    <p style="font-size: 12px; color: #6c757d;">Formatos aceitos: OFX, CSV, TXT</p>
                    <input type="file" id="arquivoExtrato" accept=".ofx,.csv,.txt" style="display: none;">
                </div>
                
                <div id="extratoProcessado" style="display: none;">
                    <h4>Extrato Processado</h4>
                    <div id="listaExtrato" class="lista-movimentacoes">
                        <!-- Movimentações do extrato serão carregadas aqui -->
                    </div>
                </div>
            </div>
            
            <!-- Movimentações do Sistema -->
            <div class="movimentacoes-container">
                <h3><i class="fas fa-list"></i> Movimentações do Sistema</h3>
                
                <div id="listaMovimentacoes" class="lista-movimentacoes">
                    <!-- Movimentações do sistema serão carregadas aqui -->
                </div>
            </div>
        </div>
        
        <!-- Área de Loading -->
        <div class="loading" id="loadingArea">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p>Processando conciliação...</p>
        </div>
    </div>
    
    <script>
        // Definir datas padrão (últimos 30 dias)
        document.addEventListener('DOMContentLoaded', function() {
            const hoje = new Date();
            const trintaDiasAtras = new Date(hoje.getTime() - (30 * 24 * 60 * 60 * 1000));
            
            const formatoData = (data) => data.toISOString().split('T')[0];
            
            document.getElementById('data_inicio').value = formatoData(trintaDiasAtras);
            document.getElementById('data_fim').value = formatoData(hoje);
            
            carregarMovimentacoes();
            atualizarResumo();
            
            // Configurar upload de arquivo
            configurarUpload();
        });
        
        // Configurar área de upload
        function configurarUpload() {
            const uploadArea = document.getElementById('uploadArea');
            const arquivoInput = document.getElementById('arquivoExtrato');
            
            uploadArea.addEventListener('click', () => arquivoInput.click());
            
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    processarExtrato(files[0]);
                }
            });
            
            arquivoInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    processarExtrato(e.target.files[0]);
                }
            });
        }
        
        // Processar arquivo de extrato
        function processarExtrato(arquivo) {
            const formData = new FormData();
            formData.append('arquivo_extrato', arquivo);
            formData.append('acao', 'processar_extrato');
            
            mostrarLoading(true);
            
            fetch('api/api_conciliacao.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.sucesso) {
                    document.getElementById('extratoProcessado').style.display = 'block';
                    exibirExtrato(data.movimentacoes);
                    atualizarResumo();
                    alert('Extrato processado com sucesso!');
                } else {
                    alert('Erro ao processar extrato: ' + data.erro);
                }
            })
            .catch(error => {
                mostrarLoading(false);
                alert('Erro de conexão: ' + error.message);
            });
        }
        
        // Exibir movimentações do extrato
        function exibirExtrato(movimentacoes) {
            const lista = document.getElementById('listaExtrato');
            lista.innerHTML = '';
            
            movimentacoes.forEach(mov => {
                const item = document.createElement('div');
                item.className = 'movimentacao-item';
                item.innerHTML = `
                    <div class="movimentacao-info">
                        <div class="movimentacao-data">${mov.data}</div>
                        <div class="movimentacao-descricao">${mov.descricao}</div>
                    </div>
                    <div class="movimentacao-valor ${mov.valor >= 0 ? 'valor-positivo' : 'valor-negativo'}">
                        R$ ${Math.abs(mov.valor).toFixed(2).replace('.', ',')}
                    </div>
                    <div class="movimentacao-acoes">
                        <button class="btn btn-info" style="font-size: 12px; padding: 5px 10px;" onclick="conciliarManual('${mov.id}')">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                `;
                lista.appendChild(item);
            });
        }
        
        // Carregar movimentações do sistema
        function carregarMovimentacoes() {
            const params = new URLSearchParams({
                acao: 'listar_movimentacoes',
                data_inicio: document.getElementById('data_inicio').value,
                data_fim: document.getElementById('data_fim').value,
                conta_bancaria: document.getElementById('conta_bancaria').value,
                status: document.getElementById('status_conciliacao').value
            });
            
            fetch('api/api_conciliacao.php?' + params)
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    exibirMovimentacoes(data.movimentacoes);
                } else {
                    alert('Erro ao carregar movimentações: ' + data.erro);
                }
            })
            .catch(error => {
                alert('Erro de conexão: ' + error.message);
            });
        }
        
        // Exibir movimentações do sistema
        function exibirMovimentacoes(movimentacoes) {
            const lista = document.getElementById('listaMovimentacoes');
            lista.innerHTML = '';
            
            movimentacoes.forEach(mov => {
                const item = document.createElement('div');
                item.className = `movimentacao-item ${mov.status_conciliacao}`;
                item.innerHTML = `
                    <div class="movimentacao-info">
                        <div class="movimentacao-data">${mov.data_movimento}</div>
                        <div class="movimentacao-descricao">${mov.descricao}</div>
                        <span class="status-badge status-${mov.status_conciliacao}">${mov.status_conciliacao}</span>
                    </div>
                    <div class="movimentacao-valor ${mov.valor >= 0 ? 'valor-positivo' : 'valor-negativo'}">
                        R$ ${Math.abs(mov.valor).toFixed(2).replace('.', ',')}
                    </div>
                    <div class="movimentacao-acoes">
                        ${mov.status_conciliacao === 'pendente' ? `
                            <button class="btn btn-warning" style="font-size: 12px; padding: 5px 10px;" onclick="marcarConciliado(${mov.id})">
                                <i class="fas fa-check"></i>
                            </button>
                        ` : ''}
                        <button class="btn btn-info" style="font-size: 12px; padding: 5px 10px;" onclick="verDetalhes(${mov.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                `;
                lista.appendChild(item);
            });
        }
        
        // Filtrar movimentações
        function filtrarMovimentacoes() {
            carregarMovimentacoes();
            atualizarResumo();
        }
        
        // Conciliação automática
        function conciliarAutomatico() {
            if (!confirm('Deseja executar a conciliação automática? Esta ação irá tentar conciliar automaticamente as movimentações com base em data, valor e descrição.')) {
                return;
            }
            
            mostrarLoading(true);
            
            const formData = new FormData();
            formData.append('acao', 'conciliar_automatico');
            formData.append('data_inicio', document.getElementById('data_inicio').value);
            formData.append('data_fim', document.getElementById('data_fim').value);
            formData.append('conta_bancaria', document.getElementById('conta_bancaria').value);
            
            fetch('api/api_conciliacao.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.sucesso) {
                    alert(`Conciliação automática concluída!\nConciliados: ${data.conciliados}\nPendentes: ${data.pendentes}`);
                    carregarMovimentacoes();
                    atualizarResumo();
                } else {
                    alert('Erro na conciliação automática: ' + data.erro);
                }
            })
            .catch(error => {
                mostrarLoading(false);
                alert('Erro de conexão: ' + error.message);
            });
        }
        
        // Marcar como conciliado
        function marcarConciliado(movimentacaoId) {
            const formData = new FormData();
            formData.append('acao', 'marcar_conciliado');
            formData.append('movimentacao_id', movimentacaoId);
            
            fetch('api/api_conciliacao.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    carregarMovimentacoes();
                    atualizarResumo();
                } else {
                    alert('Erro ao marcar como conciliado: ' + data.erro);
                }
            })
            .catch(error => {
                alert('Erro de conexão: ' + error.message);
            });
        }
        
        // Atualizar resumo
        function atualizarResumo() {
            const params = new URLSearchParams({
                acao: 'resumo_conciliacao',
                data_inicio: document.getElementById('data_inicio').value,
                data_fim: document.getElementById('data_fim').value,
                conta_bancaria: document.getElementById('conta_bancaria').value
            });
            
            fetch('api/api_conciliacao.php?' + params)
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    document.getElementById('saldoBanco').textContent = 'R$ ' + data.saldo_banco.toFixed(2).replace('.', ',');
                    document.getElementById('saldoSistema').textContent = 'R$ ' + data.saldo_sistema.toFixed(2).replace('.', ',');
                    
                    const diferenca = data.saldo_banco - data.saldo_sistema;
                    const diferencaElement = document.getElementById('diferenca');
                    diferencaElement.textContent = 'R$ ' + Math.abs(diferenca).toFixed(2).replace('.', ',');
                    diferencaElement.className = 'resumo-valor ' + (diferenca >= 0 ? 'valor-positivo' : 'valor-negativo');
                    
                    document.getElementById('totalConciliados').textContent = data.total_conciliados;
                    document.getElementById('totalPendentes').textContent = data.total_pendentes;
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar resumo:', error);
            });
        }
        
        // Exportar conciliação
        function exportarConciliacao() {
            const params = new URLSearchParams({
                acao: 'exportar_conciliacao',
                data_inicio: document.getElementById('data_inicio').value,
                data_fim: document.getElementById('data_fim').value,
                conta_bancaria: document.getElementById('conta_bancaria').value
            });
            
            window.open('api/api_conciliacao.php?' + params, '_blank');
        }
        
        // Ver detalhes
        function verDetalhes(movimentacaoId) {
            alert('Funcionalidade de detalhes em desenvolvimento.');
        }
        
        // Conciliar manual
        function conciliarManual(extratoId) {
            alert('Funcionalidade de conciliação manual em desenvolvimento.');
        }
        
        // Mostrar/ocultar loading
        function mostrarLoading(mostrar) {
            const loading = document.getElementById('loadingArea');
            if (mostrar) {
                loading.classList.add('show');
            } else {
                loading.classList.remove('show');
            }
        }
    </script>
</body>
</html>