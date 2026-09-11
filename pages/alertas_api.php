<?php

session_start();

require_once("../conn.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {

    http_response_code(401);

    echo json_encode(array(
        'sucesso' => false,
        'mensagem' => 'Usuário não autenticado.'
    ));

    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];


$stmt = $conn->prepare("
    SELECT COUNT(*) AS quantidade
    FROM alertas
    WHERE usuario_id = ?
      AND status_visto = 'nao_lido'
");

$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado = $stmt->get_result();
$dados = $resultado->fetch_assoc();

$stmt->close();

$stmt = $conn->prepare("
    SELECT
        id,
        mensagem,
        status_visto,
        data_alerta
    FROM alertas
    WHERE usuario_id = ?
    ORDER BY data_alerta DESC
    LIMIT 10
");

$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado = $stmt->get_result();

$alertas = array();

while ($alerta = $resultado->fetch_assoc()) {
    $alertas[] = $alerta;
}

$stmt->close();


echo json_encode(array(
    'sucesso' => true,
    'nao_lidos' => (int) $dados['quantidade'],
    'alertas' => $alertas
));
