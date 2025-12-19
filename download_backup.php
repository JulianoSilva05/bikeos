<?php
session_start();

// Verificar se o arquivo foi especificado
if (!isset($_GET['arquivo']) || empty($_GET['arquivo'])) {
    die('Arquivo não especificado.');
}

$arquivo = $_GET['arquivo'];
$backupPath = __DIR__ . "/backups/{$arquivo}";

// Verificar se o arquivo existe e é seguro
if (!file_exists($backupPath) || !is_file($backupPath)) {
    die('Arquivo não encontrado.');
}

// Verificar se o arquivo está no diretório correto (segurança)
if (strpos(realpath($backupPath), realpath(__DIR__ . '/backups/')) !== 0) {
    die('Acesso negado.');
}

// Verificar se é um arquivo .sql
if (pathinfo($arquivo, PATHINFO_EXTENSION) !== 'sql') {
    die('Tipo de arquivo não permitido.');
}

// Configurar headers para download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($arquivo) . '"');
header('Content-Length: ' . filesize($backupPath));
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Limpar buffer de saída
ob_clean();
flush();

// Enviar arquivo
readfile($backupPath);
exit;
?>