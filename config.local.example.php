<?php
// ARQUIVO: config.local.example.php - Modelo de configuração para o PC local
//
// Como usar:
//   1. Copie este arquivo e renomeie a cópia para config.local.php
//   2. Coloque a senha do root do seu MySQL local em DB_PASS
//
// Quando config.local.php existe, o config.php usa estes dados no lugar do .env.
// O config.local.php NÃO vai para o GitHub (.gitignore) e NÃO deve ser enviado
// para a HostGator: lá o sistema continua usando o .env.

return [
    'DB_HOST'    => 'localhost',
    'DB_NAME'    => 'jul33718_bikeos',
    'DB_USER'    => 'root',
    'DB_PASS'    => 'sua_senha_do_root',
    'DB_CHARSET' => 'utf8mb4',
];
