<?php
require_once 'config.php';
require_once 'email_config.php';
require_once 'helpers/security_headers.php';

// Aplica headers de segurança específicos para APIs
SecurityHeaders::setAPIHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    // Receber dados do formulário
    $nome_empresa = trim($_POST['nome_empresa'] ?? '');
    $responsavel = trim($_POST['responsavel'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    
    // Validar campos obrigatórios
    if (empty($nome_empresa) || empty($responsavel) || empty($email) || empty($telefone) || empty($cidade) || empty($senha)) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
        exit;
    }
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email inválido']);
        exit;
    }
    
    // Verificar se email já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Este email já está cadastrado']);
        exit;
    }
    
    // Iniciar transação
    $pdo->beginTransaction();
    
    // Criar usuário
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, status) VALUES (?, ?, ?, 'ADMIN', 'ATIVO')");
    $stmt->execute([$responsavel, $email, $senha_hash]);
    $usuario_id = $pdo->lastInsertId();
    
    // Criar configuração da empresa
    $stmt = $pdo->prepare("INSERT INTO configuracoes_loja (nome_loja, razao_social, email, telefone, cidade, empresa_id, configuracao_inicial_pendente) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->execute([$nome_empresa, $responsavel, $email, $telefone, $cidade, $usuario_id]);
    
    // Registrar no log de cadastros
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
    $stmt = $pdo->prepare("INSERT INTO log_cadastros (usuario_id, nome_empresa, email, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $nome_empresa, $email, $ip_address, $user_agent]);
    
    // Confirmar transação
    $pdo->commit();
    
    // Preparar dados para envio de email
    $dados_cliente = [
        'nome_empresa' => $nome_empresa,
        'nome_responsavel' => $responsavel,
        'email' => $email,
        'telefone' => $telefone,
        'cidade' => $cidade
    ];
    
    // Enviar email de boas-vindas para o cliente
    $email_cliente_enviado = enviarEmailBoasVindas($dados_cliente);
    
    // Enviar email de notificação para o administrador
    $email_admin_enviado = enviarEmailNotificacaoAdmin($dados_cliente);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Cadastro realizado com sucesso! Verifique seu email para os dados de acesso.',
        'email_cliente_enviado' => $email_cliente_enviado,
        'email_admin_enviado' => $email_admin_enviado
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro no cadastro: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro geral no cadastro: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro inesperado']);
}

// Função para enviar email de notificação
function enviarEmailNovoCliente($nome_empresa, $responsavel, $email, $telefone, $cidade) {
    try {
        $to = 'julianoqm@gmail.com';
        $subject = 'Novo Cliente Cadastrado - Sistema de Gestão';
        
        $message = "
        <html>
        <head>
            <title>Novo Cliente Cadastrado</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .info-item { margin-bottom: 10px; }
                .label { font-weight: bold; color: #555; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Novo Cliente Cadastrado!</h1>
                </div>
                <div class='content'>
                    <p>Um novo cliente se cadastrou no sistema de gestão:</p>
                    
                    <div class='info-item'>
                        <span class='label'>Nome da Empresa:</span> {$nome_empresa}
                    </div>
                    <div class='info-item'>
                        <span class='label'>Responsável:</span> {$responsavel}
                    </div>
                    <div class='info-item'>
                        <span class='label'>Email:</span> {$email}
                    </div>
                    <div class='info-item'>
                        <span class='label'>Telefone:</span> {$telefone}
                    </div>
                    <div class='info-item'>
                        <span class='label'>Cidade:</span> {$cidade}
                    </div>
                    <div class='info-item'>
                        <span class='label'>Data do Cadastro:</span> " . date('d/m/Y H:i:s') . "
                    </div>
                    
                    <p style='margin-top: 20px;'>
                        <strong>Próximos passos:</strong><br>
                        O cliente precisará fazer a configuração inicial obrigatória antes de usar o sistema.
                    </p>
                </div>
                <div class='footer'>
                    Sistema de Gestão - Notificação Automática
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Sistema de Gestão <noreply@sistema.com>" . "\r\n";
        $headers .= "Reply-To: noreply@sistema.com" . "\r\n";
        
        return mail($to, $subject, $message, $headers);
        
    } catch (Exception $e) {
        error_log("Erro ao enviar email: " . $e->getMessage());
        return false;
    }
}
?>