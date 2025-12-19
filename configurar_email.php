<?php
/**
 * Script de Configuração Automática de Email
 * DengoBike Manager
 */

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha_app = $_POST['senha_app'] ?? '';
    $provedor = $_POST['provedor'] ?? 'gmail';
    
    if (empty($email) || empty($senha_app)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        // Configurações por provedor
        $configuracoes = [
            'gmail' => [
                'smtp_server' => 'smtp.gmail.com',
                'smtp_port' => '587',
                'smtp_ssl' => 'tls'
            ],
            'outlook' => [
                'smtp_server' => 'smtp-mail.outlook.com',
                'smtp_port' => '587',
                'smtp_ssl' => 'tls'
            ],
            'yahoo' => [
                'smtp_server' => 'smtp.mail.yahoo.com',
                'smtp_port' => '587',
                'smtp_ssl' => 'tls'
            ],
            'titan' => [
                'smtp_server' => 'smtp.titan.email',
                'smtp_port' => '465',
                'smtp_ssl' => 'ssl'
            ]
        ];
        
        $config = $configuracoes[$provedor];
        
        // Gerar conteúdo do sendmail.ini
        $sendmail_ini_content = "[sendmail]\n\n";
        $sendmail_ini_content .= "; Configurações SMTP\n";
        $sendmail_ini_content .= "smtp_server={$config['smtp_server']}\n";
        $sendmail_ini_content .= "smtp_port={$config['smtp_port']}\n";
        $sendmail_ini_content .= "smtp_ssl={$config['smtp_ssl']}\n\n";
        $sendmail_ini_content .= "; Autenticação\n";
        $sendmail_ini_content .= "auth_username={$email}\n";
        $sendmail_ini_content .= "auth_password={$senha_app}\n\n";
        $sendmail_ini_content .= "; Email padrão\n";
        $sendmail_ini_content .= "default_domain=" . substr($email, strpos($email, '@') + 1) . "\n";
        $sendmail_ini_content .= "force_sender={$email}\n\n";
        $sendmail_ini_content .= "; Configurações adicionais\n";
        $sendmail_ini_content .= "error_logfile=error.log\n";
        $sendmail_ini_content .= "debug_logfile=debug.log\n";
        
        // Gerar conteúdo do php.ini (seção mail)
        $php_ini_content = "[mail function]\n";
        $php_ini_content .= "; Para Win32 apenas.\n";
        $php_ini_content .= "SMTP = {$config['smtp_server']}\n";
        $php_ini_content .= "smtp_port = {$config['smtp_port']}\n\n";
        $php_ini_content .= "; Para Win32 apenas.\n";
        $php_ini_content .= "sendmail_from = {$email}\n\n";
        $php_ini_content .= "; Para Unix apenas.\n";
        $php_ini_content .= 'sendmail_path = "C:\\xampp\\sendmail\\sendmail.exe" -t' . "\n";
        
        $sucesso = "Configuração gerada com sucesso! Copie os conteúdos abaixo para os respectivos arquivos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Email - BikeOS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            color: #fbcd00;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background: #fbcd00;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #e55a00;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .config-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .config-box h4 {
            margin-top: 0;
            color: #495057;
        }
        .config-content {
            background: #ffffff;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 10px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            white-space: pre-wrap;
            overflow-x: auto;
        }
        .instructions {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚴‍♂️ Configurar Email</h1>
            <p>BikeOS - Sistema de Gestão</p>
        </div>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-error">
                <strong>Erro:</strong> <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($sucesso)): ?>
            <div class="alert alert-success">
                <strong>Sucesso:</strong> <?php echo htmlspecialchars($sucesso); ?>
            </div>
        <?php endif; ?>
        
        <div class="warning">
            <h4>⚠️ Importante - Configuração de Senhas</h4>
            <p><strong>Para Gmail:</strong> Você precisa criar uma "Senha de App" em vez de usar sua senha normal.</p>
            <ol>
                <li>Acesse: <a href="https://myaccount.google.com/" target="_blank">https://myaccount.google.com/</a></li>
                <li>Vá em "Segurança" → "Verificação em duas etapas" (ative se necessário)</li>
                <li>Vá em "Senhas de app"</li>
                <li>Crie uma nova senha para "DengoBike XAMPP"</li>
                <li>Use essa senha de 16 caracteres no campo abaixo</li>
            </ol>
            <p><strong>Para Titan Email:</strong> Use a senha normal da sua conta (não é necessário senha de app).</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="provedor">Provedor de Email:</label>
                <select id="provedor" name="provedor" required>
                    <option value="gmail" <?php echo (isset($_POST['provedor']) && $_POST['provedor'] === 'gmail') ? 'selected' : ''; ?>>Gmail</option>
                    <option value="outlook" <?php echo (isset($_POST['provedor']) && $_POST['provedor'] === 'outlook') ? 'selected' : ''; ?>>Outlook/Hotmail</option>
                    <option value="yahoo" <?php echo (isset($_POST['provedor']) && $_POST['provedor'] === 'yahoo') ? 'selected' : ''; ?>>Yahoo</option>
                    <option value="titan" <?php echo (isset($_POST['provedor']) && $_POST['provedor'] === 'titan') ? 'selected' : ''; ?>>Titan Email</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? 'contato@bikeos.com.br'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="senha_app">Senha:</label>
                <input type="password" id="senha_app" name="senha_app" placeholder="Digite sua senha" required>
                <small style="color: #666;">Para Gmail, use a Senha de App gerada. Para Titan Email, use a senha normal da conta.</small>
            </div>
            
            <button type="submit">Gerar Configuração</button>
        </form>
        
        <?php if (isset($sendmail_ini_content) && isset($php_ini_content)): ?>
            <div class="config-box">
                <h4>📁 Arquivo: C:\xampp\sendmail\sendmail.ini</h4>
                <p>Substitua o conteúdo do arquivo sendmail.ini por:</p>
                <div class="config-content"><?php echo htmlspecialchars($sendmail_ini_content); ?></div>
            </div>
            
            <div class="config-box">
                <h4>📁 Arquivo: C:\xampp\php\php.ini</h4>
                <p>Localize a seção [mail function] e substitua por:</p>
                <div class="config-content"><?php echo htmlspecialchars($php_ini_content); ?></div>
            </div>
            
            <div class="instructions">
                <h4>📋 Próximos Passos:</h4>
                <ol>
                    <li><strong>Copie</strong> o conteúdo do primeiro box para <code>C:\xampp\sendmail\sendmail.ini</code></li>
                    <li><strong>Localize</strong> a seção [mail function] em <code>C:\xampp\php\php.ini</code> e substitua pelas configurações do segundo box</li>
                    <li><strong>Reinicie</strong> o Apache no XAMPP Control Panel</li>
                    <li><strong>Teste</strong> acessando: <a href="test_email_novo.php" target="_blank">test_email_novo.php</a></li>
                </ol>
            </div>
        <?php endif; ?>
        
        <div class="instructions">
            <h4>🔗 Links Úteis:</h4>
            <ul>
                <li><a href="test_email_novo.php" target="_blank">Testar Envio de Email</a></li>
                <li><a href="CONFIGURACAO_EMAIL.md" target="_blank">Guia Completo de Configuração</a></li>
                <li><a href="index.php" target="_blank">Voltar ao Sistema</a></li>
            </ul>
        </div>
    </div>
</body>
</html>