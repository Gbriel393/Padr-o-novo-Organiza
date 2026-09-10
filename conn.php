<?php
$conn = new mysqli('organiza_db.mysql.dbaas.com.br', 'organiza_db', 'Organiza123@', 'organiza_db');
 if ($conn->connect_error) {
   error_log('Falha na conexão com o banco: ' . $conn->connect_error);
   http_response_code(500);
   exit('Não foi possível conectar ao banco de dados.');
}
$conn->set_charset('utf8mb4');

define('DB_HOST', 'organiza_db.mysql.dbaas.com.br');
define('DB_NAME', 'organiza_db');
define('DB_USER', 'organiza_db');
define('DB_PASS', 'Organiza123@');
