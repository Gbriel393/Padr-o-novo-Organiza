<?php
/**
 * config.php
 * Conexão com o banco de dados (MySQL / MariaDB) usando PDO.
 *
 * IMPORTANTE: ajuste DB_HOST, DB_USER e DB_PASS de acordo com o seu
 * ambiente (XAMPP/WAMP geralmente é usuário "root" e senha vazia "").
 */

// ATENÇÃO: se o banco for remoto (ex: dbaas.com.br), troque DB_HOST,
// DB_USER e DB_PASS pelos dados de conexão do servidor remoto — os
// mesmos usados para entrar no phpMyAdmin do provedor.
define('DB_HOST', 'organiza_db.mysql.dbaas.com.br');
define('DB_NAME', 'organiza_db');
define('DB_USER', 'organiza_db');
define('DB_PASS', 'Organiza123@');

// ---- Configuração de envio de email (Gmail SMTP) ----
// Usado pela tela "Esqueci a senha" (esqueci_senha.php)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'rhaquel.projects@gmail.com');
define('MAIL_PASS', 'gxyzolprskoqytyl'); // senha de app do Gmail (16 letras, sem espaços)
define('MAIL_FROM_NAME', 'Planeje e Poupe');

// Endereço base do site, usado para montar o link de redefinição de senha.
// Ajuste se o projeto estiver em outra pasta/domínio.
define('SITE_URL', 'http://localhost/Padr-o-novo-Organiza/');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}

// Inicia a sessão em todas as páginas que incluírem este arquivo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
