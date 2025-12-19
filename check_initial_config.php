<?php
require_once 'config.php';

// Função para verificar se a configuração inicial foi feita
function verificarConfiguracaoInicial($usuario_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT configuracao_inicial_pendente FROM configuracoes_loja WHERE empresa_id = ?");
        $stmt->execute([$usuario_id]);
        $config = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Se não existe registro ou está pendente, retorna true (precisa configurar)
        if (!$config || $config['configuracao_inicial_pendente'] == 1) {
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Erro ao verificar configuração inicial: " . $e->getMessage());
        return true; // Em caso de erro, força a configuração
    }
}

// Função para marcar configuração inicial como concluída
function marcarConfiguracaoInicial($usuario_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE configuracoes_loja SET configuracao_inicial_pendente = 0 WHERE empresa_id = ?");
        return $stmt->execute([$usuario_id]);
    } catch (PDOException $e) {
        error_log("Erro ao marcar configuração inicial: " . $e->getMessage());
        return false;
    }
}

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar se precisa fazer configuração inicial
if (verificarConfiguracaoInicial($_SESSION['user_id'])) {
    // Se estiver na página de funcionalidades, permitir acesso
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page !== 'funcionalidades.php') {
        // Redirecionar para funcionalidades com parâmetro de configuração inicial
        header('Location: funcionalidades.php?config_inicial=1');
        exit;
    }
}
?>