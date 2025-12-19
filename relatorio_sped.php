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
    <title>Relatório SPED - BikeOS</title>
    <link rel="stylesheet" href="assets/css/estilo-global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sped-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .sped-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .sped-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .sped-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #667eea;
        }
        
        .sped-card h3 {
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
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
            border-color: #667eea;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-info:hover {
            background: #138496;
            transform: translateY(-2px);
        }
        
        .status-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .status-info h4 {
            color: #495057;
            margin-bottom: 10px;
        }
        
        .status-list {
            list-style: none;
            padding: 0;
        }
        
        .status-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
        }
        
        .status-list li:last-child {
            border-bottom: none;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pendente {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-processando {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-concluido {
            background: #d4edda;
            color: #155724;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .loading.show {
            display: block;
        }
    </style>
</head>
<body>
    <?php include '_partials/navigation_header.php'; ?>
    
    <?php include '_partials/menu_admin.php'; ?>
    
    <div class="sped-container">
        <div class="sped-header">
            <h1><i class="fas fa-file-alt"></i> Relatórios SPED</h1>
            <p>Sistema Público de Escrituração Digital - Fiscal e Contábil</p>
        </div>
        
        <div class="sped-cards">
            <!-- SPED Fiscal -->
            <div class="sped-card">
                <h3><i class="fas fa-receipt"></i> SPED Fiscal</h3>
                <p>Geração de arquivo SPED Fiscal com registros de movimentação fiscal da empresa.</p>
                
                <form id="formSpedFiscal">
                    <div class="form-group">
                        <label for="data_inicio_fiscal">Data Início:</label>
                        <input type="date" id="data_inicio_fiscal" name="data_inicio" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_fim_fiscal">Data Fim:</label>
                        <input type="date" id="data_fim_fiscal" name="data_fim" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="perfil_fiscal">Perfil de Apresentação:</label>
                        <select id="perfil_fiscal" name="perfil" required>
                            <option value="A">A - Inventário e Balancete</option>
                            <option value="B">B - Livros Fiscais</option>
                            <option value="C">C - Livros Fiscais e Inventário</option>
                        </select>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-download"></i> Gerar SPED Fiscal
                        </button>
                        <button type="button" class="btn btn-info" onclick="validarSpedFiscal()">
                            <i class="fas fa-check-circle"></i> Validar Dados
                        </button>
                    </div>
                </form>
                
                <div class="status-info">
                    <h4>Status da Geração</h4>
                    <ul class="status-list" id="statusSpedFiscal">
                        <li>Último arquivo: <span class="status-badge status-pendente">Pendente</span></li>
                    </ul>
                </div>
            </div>
            
            <!-- SPED Contábil -->
            <div class="sped-card">
                <h3><i class="fas fa-calculator"></i> SPED Contábil (ECD)</h3>
                <p>Geração de arquivo SPED Contábil (Escrituração Contábil Digital).</p>
                
                <form id="formSpedContabil">
                    <div class="form-group">
                        <label for="data_inicio_contabil">Data Início:</label>
                        <input type="date" id="data_inicio_contabil" name="data_inicio" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_fim_contabil">Data Fim:</label>
                        <input type="date" id="data_fim_contabil" name="data_fim" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_escrituracao">Tipo de Escrituração:</label>
                        <select id="tipo_escrituracao" name="tipo_escrituracao" required>
                            <option value="G">G - Livro Diário com escrituração completa</option>
                            <option value="R">R - Livro Diário com escrituração resumida</option>
                            <option value="A">A - Livro Diário auxiliar</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="situacao_especial">Situação Especial:</label>
                        <select id="situacao_especial" name="situacao_especial">
                            <option value="0">0 - Normal</option>
                            <option value="1">1 - Extinção</option>
                            <option value="2">2 - Fusão</option>
                            <option value="3">3 - Incorporação</option>
                            <option value="4">4 - Cisão</option>
                        </select>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-file-download"></i> Gerar SPED Contábil
                        </button>
                        <button type="button" class="btn btn-info" onclick="validarSpedContabil()">
                            <i class="fas fa-check-circle"></i> Validar Dados
                        </button>
                    </div>
                </form>
                
                <div class="status-info">
                    <h4>Status da Geração</h4>
                    <ul class="status-list" id="statusSpedContabil">
                        <li>Último arquivo: <span class="status-badge status-pendente">Pendente</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Área de Loading -->
        <div class="loading" id="loadingArea">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p>Processando arquivo SPED...</p>
        </div>
        
        <!-- Histórico de Arquivos -->
        <div class="sped-card">
            <h3><i class="fas fa-history"></i> Histórico de Arquivos SPED</h3>
            <div id="historicoSped">
                <p>Carregando histórico...</p>
            </div>
        </div>
    </div>
    
    <script>
        // Definir datas padrão (mês atual)
        document.addEventListener('DOMContentLoaded', function() {
            const hoje = new Date();
            const primeiroDiaMes = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
            const ultimoDiaMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);
            
            const formatoData = (data) => data.toISOString().split('T')[0];
            
            document.getElementById('data_inicio_fiscal').value = formatoData(primeiroDiaMes);
            document.getElementById('data_fim_fiscal').value = formatoData(ultimoDiaMes);
            document.getElementById('data_inicio_contabil').value = formatoData(primeiroDiaMes);
            document.getElementById('data_fim_contabil').value = formatoData(ultimoDiaMes);
            
            carregarHistorico();
        });
        
        // Gerar SPED Fiscal
        document.getElementById('formSpedFiscal').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('acao', 'gerar_sped_fiscal');
            
            mostrarLoading(true);
            atualizarStatus('statusSpedFiscal', 'Processando...', 'processando');
            
            fetch('api/api_sped.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.sucesso) {
                    atualizarStatus('statusSpedFiscal', 'Concluído', 'concluido');
                    
                    // Download do arquivo
                    const link = document.createElement('a');
                    link.href = data.arquivo_url;
                    link.download = data.nome_arquivo;
                    link.click();
                    
                    alert('SPED Fiscal gerado com sucesso!');
                    carregarHistorico();
                } else {
                    atualizarStatus('statusSpedFiscal', 'Erro: ' + data.erro, 'pendente');
                    alert('Erro ao gerar SPED Fiscal: ' + data.erro);
                }
            })
            .catch(error => {
                mostrarLoading(false);
                atualizarStatus('statusSpedFiscal', 'Erro de conexão', 'pendente');
                alert('Erro de conexão: ' + error.message);
            });
        });
        
        // Gerar SPED Contábil
        document.getElementById('formSpedContabil').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('acao', 'gerar_sped_contabil');
            
            mostrarLoading(true);
            atualizarStatus('statusSpedContabil', 'Processando...', 'processando');
            
            fetch('api/api_sped.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.sucesso) {
                    atualizarStatus('statusSpedContabil', 'Concluído', 'concluido');
                    
                    // Download do arquivo
                    const link = document.createElement('a');
                    link.href = data.arquivo_url;
                    link.download = data.nome_arquivo;
                    link.click();
                    
                    alert('SPED Contábil gerado com sucesso!');
                    carregarHistorico();
                } else {
                    atualizarStatus('statusSpedContabil', 'Erro: ' + data.erro, 'pendente');
                    alert('Erro ao gerar SPED Contábil: ' + data.erro);
                }
            })
            .catch(error => {
                mostrarLoading(false);
                atualizarStatus('statusSpedContabil', 'Erro de conexão', 'pendente');
                alert('Erro de conexão: ' + error.message);
            });
        });
        
        // Validar SPED Fiscal
        function validarSpedFiscal() {
            const formData = new FormData(document.getElementById('formSpedFiscal'));
            formData.append('acao', 'validar_sped_fiscal');
            
            fetch('api/api_sped.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert('Validação concluída:\n' + data.relatorio);
                } else {
                    alert('Erro na validação: ' + data.erro);
                }
            })
            .catch(error => {
                alert('Erro de conexão: ' + error.message);
            });
        }
        
        // Validar SPED Contábil
        function validarSpedContabil() {
            const formData = new FormData(document.getElementById('formSpedContabil'));
            formData.append('acao', 'validar_sped_contabil');
            
            fetch('api/api_sped.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert('Validação concluída:\n' + data.relatorio);
                } else {
                    alert('Erro na validação: ' + data.erro);
                }
            })
            .catch(error => {
                alert('Erro de conexão: ' + error.message);
            });
        }
        
        // Carregar histórico
        function carregarHistorico() {
            fetch('api/api_sped.php?acao=historico')
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    const historicoDiv = document.getElementById('historicoSped');
                    
                    if (data.historico.length === 0) {
                        historicoDiv.innerHTML = '<p>Nenhum arquivo SPED gerado ainda.</p>';
                        return;
                    }
                    
                    let html = '<table style="width: 100%; border-collapse: collapse;">';
                    html += '<thead><tr style="background: #f8f9fa;"><th style="padding: 10px; border: 1px solid #dee2e6;">Tipo</th><th style="padding: 10px; border: 1px solid #dee2e6;">Período</th><th style="padding: 10px; border: 1px solid #dee2e6;">Data Geração</th><th style="padding: 10px; border: 1px solid #dee2e6;">Ações</th></tr></thead><tbody>';
                    
                    data.historico.forEach(arquivo => {
                        html += `<tr>
                            <td style="padding: 10px; border: 1px solid #dee2e6;">${arquivo.tipo}</td>
                            <td style="padding: 10px; border: 1px solid #dee2e6;">${arquivo.periodo}</td>
                            <td style="padding: 10px; border: 1px solid #dee2e6;">${arquivo.data_geracao}</td>
                            <td style="padding: 10px; border: 1px solid #dee2e6;">
                                <a href="${arquivo.arquivo_url}" class="btn btn-info" style="font-size: 12px; padding: 5px 10px;" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </td>
                        </tr>`;
                    });
                    
                    html += '</tbody></table>';
                    historicoDiv.innerHTML = html;
                } else {
                    document.getElementById('historicoSped').innerHTML = '<p>Erro ao carregar histórico.</p>';
                }
            })
            .catch(error => {
                document.getElementById('historicoSped').innerHTML = '<p>Erro de conexão ao carregar histórico.</p>';
            });
        }
        
        // Funções auxiliares
        function mostrarLoading(mostrar) {
            const loading = document.getElementById('loadingArea');
            if (mostrar) {
                loading.classList.add('show');
            } else {
                loading.classList.remove('show');
            }
        }
        
        function atualizarStatus(elementId, texto, tipo) {
            const elemento = document.getElementById(elementId);
            const statusClass = 'status-' + tipo;
            elemento.innerHTML = `<li>Último arquivo: <span class="status-badge ${statusClass}">${texto}</span></li>`;
        }
    </script>
</body>
</html>