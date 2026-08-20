<?php
$conn = new mysqli('localhost', 'root', '', 'organiza_banco');
if ($conn->connect_error) {
    error_log('Falha na conexão com o banco: ' . $conn->connect_error);
    http_response_code(500);
    exit('Não foi possível conectar ao banco de dados.');
}
$conn->set_charset('utf8mb4');
