<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    $usuario_id = $_SESSION['user_id'];
    
    // Receber dados do formulário ou JSON
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input) {
        // Dados via JSON
        $nome_empresa = trim($input['nome_empresa'] ?? $input['nome_loja'] ?? '');
        $responsavel = trim($input['responsavel'] ?? $input['razao_social'] ?? '');
        $email_empresa = trim($input['email_empresa'] ?? $input['email'] ?? '');
        $telefone = trim($input['telefone'] ?? '');
        $cidade = trim($input['cidade'] ?? '');
        $endereco = trim($input['endereco'] ?? '');
        $cnpj = trim($input['cnpj'] ?? '');
        $chave_pix = trim($input['chave_pix'] ?? '');
        $logo_loja = trim($input['logo_loja'] ?? '');
        $config_inicial = isset($input['config_inicial']) ? $input['config_inicial'] == '1' : true;
    } else {
        // Dados via POST
        $nome_empresa = trim($_POST['nome_empresa'] ?? $_POST['nome_loja'] ?? '');
        $responsavel = trim($_POST['responsavel'] ?? $_POST['razao_social'] ?? '');
        $email_empresa = trim($_POST['email_empresa'] ?? $_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $cnpj = trim($_POST['cnpj'] ?? '');
        $chave_pix = trim($_POST['chave_pix'] ?? '');
        $logo_loja = trim($_POST['logo_loja'] ?? '');
        $config_inicial = isset($_POST['config_inicial']) ? $_POST['config_inicial'] == '1' : true;
    }
    
    // Validar campos obrigatórios para configuração inicial
    if ($config_inicial) {
        if (empty($nome_empresa) || empty($responsavel) || empty($telefone) || empty($cidade)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Por favor, preencha todos os campos obrigatórios (Nome da Empresa, Responsável, Telefone e Cidade)'
            ]);
            exit;
        }
    }
    
    // Buscar o ID e email do usuário na tabela funcionarios
    $stmt = $pdo->prepare("SELECT id, email FROM funcionarios WHERE email = ?");
    $stmt->execute([$_SESSION['email']]);
    $usuario_funcionarios = $stmt->fetch();
    
    if (!$usuario_funcionarios) {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado na tabela funcionarios']);
        exit;
    }
    
    $usuario_funcionarios_id = $usuario_funcionarios['id'];
    
    // Se email_empresa não foi fornecido, usar o email do usuário logado
    if (empty($email_empresa)) {
        $email_empresa = $usuario_funcionarios['email'];
    }
    
    // Verificar se já existe configuração para este usuário
    $stmt = $pdo->prepare("SELECT id FROM configuracoes_loja WHERE empresa_id = ?");
    $stmt->execute([$usuario_funcionarios_id]);
    $config_existente = $stmt->fetch();
    
    if ($config_existente) {
        // Atualizar configuração existente
        $sql = "UPDATE configuracoes_loja SET 
                nome_loja = ?, 
                razao_social = ?, 
                email = ?, 
                telefone = ?, 
                cidade = ?, 
                endereco = ?, 
                cnpj = ?, 
                configuracao_inicial_pendente = ?, 
                data_atualizacao = CURRENT_TIMESTAMP 
                WHERE empresa_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $nome_empresa,
            $responsavel,
            $email_empresa, 
            $telefone,
            $cidade,
            $endereco,
            $cnpj,
            $config_inicial ? 0 : 1, // Se é config inicial, marca como concluída
            $usuario_funcionarios_id
        ]);
    } else {
        // Criar nova configuração
        $sql = "INSERT INTO configuracoes_loja 
                (nome_loja, razao_social, email, telefone, cidade, endereco, cnpj, empresa_id, configuracao_inicial_pendente) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $nome_empresa,
            $responsavel,
            $email_empresa,
            $telefone,
            $cidade,
            $endereco,
            $cnpj,
            $usuario_funcionarios_id,
            $config_inicial ? 0 : 1 // Se é config inicial, marca como concluída
        ]);
    }
    
    if ($result) {
        // Configuração já foi salva na tabela configuracoes_loja acima
        
        $message = $config_inicial ? 'Configuração inicial concluída com sucesso!' : 'Configurações salvas com sucesso!';
        $redirect = $config_inicial ? 'dashboard.php' : null;
        
        echo json_encode([
            'success' => true, 
            'message' => $message,
            'redirect' => $redirect
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar configurações']);
    }
    
} catch (PDOException $e) {
    error_log("Erro na configuração inicial: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro PDO: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Erro geral na configuração inicial: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>