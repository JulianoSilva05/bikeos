<?php
// ARQUIVO: config.php - Configuração de conexão com banco de dados

// Carrega o loader de variáveis de ambiente
require_once __DIR__ . '/helpers/env_loader.php';

// Carrega e aplica headers de segurança
require_once __DIR__ . '/helpers/security_headers.php';
SecurityHeaders::setSecurityHeaders();

try {
    // Carrega as variáveis de ambiente
    EnvLoader::load();
    
    // Configurações do Banco de Dados a partir de variáveis de ambiente
    $host = EnvLoader::get('DB_HOST', 'localhost');
    $dbname = EnvLoader::get('DB_NAME', 'jul33718_dengo_bike');
    $user = EnvLoader::get('DB_USER', 'root');
    $pass = EnvLoader::get('DB_PASS', '');
    $charset = EnvLoader::get('DB_CHARSET', 'utf8mb4');
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    // Cria a conexão PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
    
} catch (Exception $e) {
    // Log seguro do erro sem expor detalhes sensíveis
    error_log("Erro de configuração do sistema: " . date('Y-m-d H:i:s'));
    
    // Resposta genérica para o usuário
    if (php_sapi_name() === 'cli') {
        die("Erro de configuração do sistema. Verifique as configurações.\n");
    } else {
        http_response_code(500);
        die("Sistema temporariamente indisponível. Tente novamente em alguns minutos.");
    }
} catch (\PDOException $e) {
    // Log seguro do erro de banco sem expor credenciais
    error_log("Erro de conexão com banco de dados: " . date('Y-m-d H:i:s'));
    
    // Resposta genérica para o usuário
    if (php_sapi_name() === 'cli') {
        die("Erro de conexão com o banco de dados. Verifique as configurações.\n");
    } else {
        http_response_code(500);
        die("Sistema temporariamente indisponível. Tente novamente em alguns minutos.");
    }
}
?>