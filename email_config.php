<?php
/**
 * Configurações de Email para o Sistema DengoBike
 * 
 * Este arquivo contém as configurações necessárias para envio de emails
 * usando SMTP ao invés da função mail() nativa do PHP
 */

// Carregar variáveis de ambiente
require_once __DIR__ . '/helpers/env_loader.php';

// Carregar o arquivo .env
EnvLoader::load();

// Configurações SMTP - Titan Email
define('SMTP_HOST', EnvLoader::get('SMTP_HOST', 'smtp.titan.email'));
define('SMTP_PORT', EnvLoader::get('SMTP_PORT', 465));
define('SMTP_USERNAME', EnvLoader::get('SMTP_USERNAME', 'contato@bikeos.com.br'));
define('SMTP_PASSWORD', EnvLoader::get('SMTP_PASSWORD', 'Jjmgmz56@'));
define('SMTP_ENCRYPTION', EnvLoader::get('SMTP_ENCRYPTION', 'ssl'));
define('FROM_EMAIL', EnvLoader::get('FROM_EMAIL', 'contato@bikeos.com.br'));
define('FROM_NAME', EnvLoader::get('FROM_NAME', 'BikeOS'));

/**
 * Função para enviar email usando SMTP
 * 
 * @param string $to Email do destinatário
 * @param string $subject Assunto do email
 * @param string $message Corpo do email (HTML)
 * @param string $from_email Email do remetente (opcional)
 * @param string $from_name Nome do remetente (opcional)
 * @return bool True se enviado com sucesso, False caso contrário
 */
function enviarEmailSMTP($to, $subject, $message, $from_email = null, $from_name = null) {
    // Usar configurações padrão se não fornecidas
    $from_email = $from_email ?: FROM_EMAIL;
    $from_name = $from_name ?: FROM_NAME;
    
    try {
        // Conectar ao servidor SMTP
        $smtp_host = SMTP_HOST;
        $smtp_port = SMTP_PORT;
        $smtp_user = SMTP_USERNAME;
        $smtp_pass = SMTP_PASSWORD;
        $smtp_encryption = SMTP_ENCRYPTION;
        
        // Criar contexto SSL se necessário
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        // Conectar ao servidor
        if ($smtp_encryption === 'ssl') {
            $connection = stream_socket_client("ssl://$smtp_host:$smtp_port", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        } else {
            $connection = stream_socket_client("$smtp_host:$smtp_port", $errno, $errstr, 30);
        }
        
        if (!$connection) {
            error_log("Erro ao conectar ao SMTP: $errstr ($errno)");
            return false;
        }
        
        // Função para ler resposta do servidor
        $read_response = function() use ($connection) {
            return fgets($connection, 512);
        };
        
        // Função para enviar comando
        $send_command = function($command) use ($connection) {
            fwrite($connection, $command . "\r\n");
        };
        
        // Ler resposta inicial
        $response = $read_response();
        if (substr($response, 0, 3) !== '220') {
            error_log("Erro na resposta inicial SMTP: $response");
            fclose($connection);
            return false;
        }
        
        // EHLO
        $send_command("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
        $response = $read_response();
        
        // Ler todas as linhas de resposta do EHLO
        while (substr($response, 3, 1) === '-') {
            $response = $read_response();
        }
        
        // AUTH LOGIN
        $send_command("AUTH LOGIN");
        $response = $read_response();
        if (substr($response, 0, 3) !== '334') {
            error_log("Erro no comando AUTH LOGIN: $response");
            fclose($connection);
            return false;
        }
        
        // Enviar usuário
        $send_command(base64_encode($smtp_user));
        $response = $read_response();
        if (substr($response, 0, 3) !== '334') {
            error_log("Erro ao enviar usuário: $response");
            fclose($connection);
            return false;
        }
        
        // Enviar senha
        $send_command(base64_encode($smtp_pass));
        $response = $read_response();
        if (substr($response, 0, 3) !== '235') {
            error_log("Erro na autenticação SMTP: $response");
            fclose($connection);
            return false;
        }
        
        // MAIL FROM
        $send_command("MAIL FROM: <$from_email>");
        $response = $read_response();
        
        // RCPT TO
        $send_command("RCPT TO: <$to>");
        $response = $read_response();
        
        // DATA
        $send_command("DATA");
        $response = $read_response();
        
        // Cabeçalhos e corpo do email
        $email_content = "From: $from_name <$from_email>\r\n";
        $email_content .= "To: $to\r\n";
        $email_content .= "Subject: $subject\r\n";
        $email_content .= "MIME-Version: 1.0\r\n";
        $email_content .= "Content-Type: text/html; charset=UTF-8\r\n";
        $email_content .= "\r\n";
        $email_content .= $message;
        $email_content .= "\r\n.\r\n";
        
        fwrite($connection, $email_content);
        $response = $read_response();
        
        // QUIT
        $send_command("QUIT");
        $response = $read_response();
        
        fclose($connection);
        
        error_log("Email enviado com sucesso para: $to");
        return true;
        
    } catch (Exception $e) {
        error_log("Erro ao enviar email: " . $e->getMessage());
        return false;
    }
}

/**
 * Função melhorada para enviar email de boas-vindas
 * 
 * @param array $dados Dados do cliente cadastrado
 * @return bool True se enviado com sucesso
 */
function enviarEmailBoasVindas($dados) {
    $to = $dados['email'];
    $subject = 'Bem-vindo ao BikeOS - Seus Dados de Acesso';
    
    // Link de acesso ao sistema
    $link_acesso = 'https://bikeos.com.br/login.php';
    
    $message = "
    <html>
    <head>
        <title>Bem-vindo ao BikeOS</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #fbcd00; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
            .credentials { background: #e8f4f8; padding: 20px; border-left: 4px solid #fbcd00; margin: 20px 0; border-radius: 4px; }
            .access-button { background: #fbcd00; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; font-weight: bold; }
            .info-item { margin-bottom: 10px; }
            .label { font-weight: bold; color: #fbcd00; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🚴‍♂️ Bem-vindo ao BikeOS!</h2>
                <p>Seu sistema de gestão para oficina de bicicletas</p>
            </div>
            <div class='content'>
                <p>Olá <strong>{$dados['nome_responsavel']}</strong>,</p>
                
                <p>Parabéns! Seu cadastro foi realizado com sucesso no <strong>BikeOS</strong>. Agora você tem acesso completo ao nosso sistema de gestão para oficinas de bicicletas.</p>
                
                <div class='credentials'>
                    <h3>📋 Seus Dados de Acesso:</h3>
                    <div class='info-item'>
                        <span class='label'>Email/Usuário:</span> {$dados['email']}
                    </div>
                    <div class='info-item'>
                        <span class='label'>Senha:</span> A senha que você definiu no cadastro
                    </div>
                    <div class='info-item'>
                        <span class='label'>Empresa:</span> {$dados['nome_empresa']}
                    </div>
                </div>
                
                <div style='text-align: center;'>
                    <a href='$link_acesso' class='access-button'>🔐 ACESSAR O SISTEMA</a>
                </div>
                
                <h3>🎯 Próximos Passos:</h3>
                <ol>
                    <li><strong>Faça login</strong> usando o link acima</li>
                    <li><strong>Complete a configuração inicial</strong> da sua empresa</li>
                    <li><strong>Explore as funcionalidades</strong> do sistema</li>
                    <li><strong>Cadastre seus clientes e serviços</strong></li>
                </ol>
                
                <h3>🚀 Funcionalidades Disponíveis:</h3>
                <ul>
                    <li>✅ Gestão de Clientes</li>
                    <li>✅ Controle de Ordens de Serviço</li>
                    <li>✅ Gestão de Estoque</li>
                    <li>✅ Controle Financeiro</li>
                    <li>✅ Relatórios Detalhados</li>
                    <li>✅ Sistema de Impressão</li>
                </ul>
                
                <p><strong>Importante:</strong> Mantenha seus dados de acesso em local seguro. Se precisar de ajuda, entre em contato conosco.</p>
                
                <p>Seja bem-vindo à família DengoBike! 🚴‍♂️</p>
            </div>
            <div class='footer'>
                <p>BikeOS - Sistema de Gestão para Oficinas de Bicicletas</p>
                <p>Este é um email automático, não responda.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return enviarEmailSMTP($to, $subject, $message);
}

/**
 * Função para enviar email de notificação ao administrador
 * 
 * @param array $dados Dados do cliente cadastrado
 * @return bool True se enviado com sucesso
 */
function enviarEmailNotificacaoAdmin($dados) {
    $to = 'julianoqm@gmail.com';
    $subject = 'Novo Cliente Cadastrado - BikeOS';
    
    $message = "
    <html>
    <head>
        <title>Novo Cliente Cadastrado</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #fbcd00; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .info-item { margin-bottom: 10px; }
            .label { font-weight: bold; color: #fbcd00; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🎉 Novo Cliente Cadastrado!</h2>
            </div>
            <div class='content'>
                <p>Um novo cliente se cadastrou no sistema BikeOS:</p>
                
                <div class='info-item'>
                    <span class='label'>Empresa:</span> {$dados['nome_empresa']}
                </div>
                <div class='info-item'>
                    <span class='label'>Responsável:</span> {$dados['nome_responsavel']}
                </div>
                <div class='info-item'>
                    <span class='label'>Email:</span> {$dados['email']}
                </div>
                <div class='info-item'>
                    <span class='label'>Telefone:</span> {$dados['telefone']}
                </div>
                <div class='info-item'>
                    <span class='label'>Cidade:</span> {$dados['cidade']}
                </div>
                <div class='info-item'>
                    <span class='label'>Data do Cadastro:</span> " . date('d/m/Y H:i:s') . "
                </div>
                
                <p style='margin-top: 20px;'>
                    <strong>Status:</strong> Email de boas-vindas enviado para o cliente.
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return enviarEmailSMTP($to, $subject, $message);
}
?>