<?php

require_once("../conn.php");

header('Content-Type: application/json; charset=utf-8');


$payload = file_get_contents('php://input');

if (!$payload) {

    http_response_code(400);

    echo json_encode(array(
        'sucesso' => false,
        'mensagem' => 'Payload vazio.'
    ));

    exit;
}


$dados = json_decode($payload, true);

if (!is_array($dados)) {

    http_response_code(400);

    echo json_encode(array(
        'sucesso' => false,
        'mensagem' => 'JSON inválido.'
    ));

    exit;
}

$evento = $dados['event'] ?? '';
$usuario_id = isset($dados['usuario_id'])
    ? (int) $dados['usuario_id']
    : 0;


if ($evento === '') {

    http_response_code(400);

    echo json_encode(array(
        'sucesso' => false,
        'mensagem' => 'Evento não informado.'
    ));

    exit;
}

$stmt = $conn->prepare("
    INSERT INTO logs_sistema
    (
        nivel,
        mensagem
    )
    VALUES
    (
        'INFO',
        ?
    )
");

$mensagem = 'Webhook bancário recebido: ' . $evento;

$stmt->bind_param("s", $mensagem);
$stmt->execute();
$stmt->close();


/*
 * Aqui o evento pode disparar a sincronização
 * das contas/transações do usuário.
 */

echo json_encode(array(
    'sucesso' => true,
    'mensagem' => 'Webhook recebido.'
));