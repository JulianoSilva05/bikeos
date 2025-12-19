<?php
// Inicia a sessão em todas as páginas que o incluírem
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se não existir uma sessão com 'user_id', redireciona para o login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Garante que o script pare de ser executado
}
?>