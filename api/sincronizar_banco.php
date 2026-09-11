<?php

session_start();

require_once("../conn.php");
require_once("../servicos/IntegracaoBancaria.php");

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

$dados = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($dados)) {
    $dados = $_POST;
}

$banco = trim($dados['banco'] ?? '');
$agencia = trim($dados['agencia'] ?? '');
$numero_conta = trim($dados['numero_conta'] ?? '');
$saldo = (float) ($dados['saldo'] ?? 0);

if ($banco === '' || $numero_conta === '') {

    http_response_code(400);

    echo json_encode(array(
        'sucesso' => false,
        'mensagem' => 'Banco e número da conta são obrigatórios.'
    ));

    exit;
}

$integracao = new IntegracaoBancaria($conn);

$conta_id = $integracao->salvarConta(
    $usuario_id,
    $banco,
    $agencia,
    $numero_conta,
    $saldo
);

if ($conta_id === false) {

    http_response_code(500);

    echo json_encode(array(
        'sucesso' => false,
        'mensagem' => 'Erro ao salvar a conta.'
    ));

    exit;
}

echo json_encode(array(
    'sucesso' => true,
    'conta_id' => $conta_id,
    'mensagem' => 'Conta bancária sincronizada.'
));