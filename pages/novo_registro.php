<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../conn.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];

$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

$erro = '';

function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // VALIDAÇÃO DO TOKEN CSRF
    if (
        !hash_equals(
            $_SESSION['csrf_token'],
            (string) ($_POST['csrf_token'] ?? '')
        )
    ) {
        http_response_code(403);
        exit('Requisição inválida.');
    }

    // TIPO
    $tipo = (string) ($_POST['tipo'] ?? 'entrada');

    // NATUREZA
    $natureza = $tipo === 'saida'
        ? (string) ($_POST['natureza'] ?? 'variavel')
        : null;

    // CATEGORIA
    $categoria = trim(
        (string) ($_POST['categoria'] ?? '')
    );

    // VALOR
    $valorInformado = trim(
        (string) ($_POST['valor'] ?? '')
    );

    // ACEITA VALORES COMO:
    // 500
    // 500,50
    // 1.500,50
    // 1500.50
    if (str_contains($valorInformado, ',')) {

        $valorInformado = str_replace(
            '.',
            '',
            $valorInformado
        );

        $valorInformado = str_replace(
            ',',
            '.',
            $valorInformado
        );
    }

    $valor = filter_var(
        $valorInformado,
        FILTER_VALIDATE_FLOAT
    );

    // DATA
    $data = (string) (
        $_POST['data_transacao'] ?? ''
    );

    // STATUS
    $status = (string) (
        $_POST['status'] ?? 'pendente'
    );

    // DESCRIÇÃO
    $descricao = trim(
        (string) ($_POST['descricao'] ?? '')
    );

    // VALIDA DATA
    $dataValida = DateTime::createFromFormat(
        'Y-m-d',
        $data
    );

    // STATUS PERMITIDOS
    $statusPermitido = $tipo === 'entrada'
        ? ['recebido', 'pendente']
        : ['pago', 'pendente'];

    // VALIDAÇÃO DOS CAMPOS
    if (
        !in_array(
            $tipo,
            ['entrada', 'saida'],
            true
        )

        ||

        (
            $tipo === 'saida'
            &&
            !in_array(
                $natureza,
                ['fixo', 'variavel'],
                true
            )
        )

        ||

        $categoria === ''

        ||

        mb_strlen($categoria) > 80

        ||

        $valor === false

        ||

        $valor <= 0

        ||

        !$dataValida

        ||

        $dataValida->format('Y-m-d') !== $data

        ||

        !in_array(
            $status,
            $statusPermitido,
            true
        )

        ||

        mb_strlen($descricao) > 500
    ) {

        $erro = 'Confira os campos informados e tente novamente.';

    } else {

        // INSERT NO BANCO
        $sql = "
            INSERT INTO transacoes
            (
                usuario_id,
                tipo,
                natureza,
                categoria,
                valor,
                data_transacao,
                status,
                descricao
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            $erro = 'Erro no prepare: ' . $conn->error;

        } else {

            $stmt->bind_param(
                'isssdsss',
                $usuarioId,
                $tipo,
                $natureza,
                $categoria,
                $valor,
                $data,
                $status,
                $descricao
            );

            if ($stmt->execute()) {

                $registroId = $stmt->insert_id;

                header(
                    'Location: listagem.php?status=criado&registro='
                    . $registroId
                );

                exit;

            } else {

                // DEIXEI O ERRO DO MYSQL VISÍVEL
                // PARA FACILITAR CASO AINDA TENHA ALGUM PROBLEMA
                $erro = 'Erro MySQL: ' . $stmt->error;
            }

            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Novo Registro</title>

    <link
        rel="stylesheet"
        href="../CSS/navbar/navbar_index.css"
    >

    <link
        rel="stylesheet"
        href="../CSS/novo.css"
    >

    <link
        rel="stylesheet"
        href="../CSS/style.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    >

</head>

<body>

<?php
include __DIR__ . '/../estrutura/sidebar_novo.php';
?>

<main class="content">

    <div class="container container_pg py-4">

        <div class="card">

            <h1>
                Novo Registro
            </h1>

            <p>
                Adicione uma nova transação
            </p>

            <?php if ($erro): ?>

                <div class="alerta-erro">

                    <?= e($erro) ?>

                </div>

            <?php endif; ?>

            <!-- BOTÕES ENTRADA E SAÍDA -->

            <div class="tipo">

                <button
                    type="button"
                    class="btn tipo-btn active"
                    data-tipo="entrada"
                >
                    Entrada
                </button>

                <button
                    type="button"
                    class="btn tipo-btn"
                    data-tipo="saida"
                >
                    Saída
                </button>

            </div>

            <!-- FORMULÁRIO -->

            <form
                id="registroForm"
                name="registroForm"
                method="POST"
            >

                <!-- TOKEN CSRF -->

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>"
                    form="registroForm"
                >

                <!-- TIPO -->

                <input
                    type="hidden"
                    id="tipo"
                    name="tipo"
                    value="entrada"
                    form="registroForm"
                >

                <!-- VALOR -->

                <label for="valor">
                    Valor
                </label>

                <input
                    id="valor"
                    name="valor"
                    type="text"
                    inputmode="decimal"
                    placeholder="0,00"
                    required
                    form="registroForm"
                >

                <!-- CATEGORIA -->

                <label for="categoria">
                    Categoria
                </label>

                <input
                    id="categoria"
                    name="categoria"
                    list="categorias"
                    maxlength="80"
                    placeholder="Ex.: Salário, Moradia, Alimentação"
                    required
                    form="registroForm"
                >

                <datalist id="categorias">

                    <option value="Salário">
                    <option value="Freelance">
                    <option value="Moradia">
                    <option value="Alimentação">
                    <option value="Transporte">
                    <option value="Saúde">
                    <option value="Lazer">

                </datalist>

                <!-- NATUREZA -->

                <div
                    id="natureza-grupo"
                    hidden
                >

                    <label for="natureza">
                        Tipo do gasto
                    </label>

                    <select
                        id="natureza"
                        name="natureza"
                        form="registroForm"
                    >

                        <option value="variavel">
                            Variável
                        </option>

                        <option value="fixo">
                            Fixo
                        </option>

                    </select>

                </div>

                <!-- DATA -->

                <label for="data_transacao">
                    Data
                </label>

                <input
                    id="data_transacao"
                    name="data_transacao"
                    type="date"
                    value="<?= date('Y-m-d') ?>"
                    required
                    form="registroForm"
                >

                <!-- STATUS -->

                <label for="status">
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    required
                    form="registroForm"
                >

                    <option value="recebido">
                        Recebido
                    </option>

                    <option value="pendente">
                        Pendente
                    </option>

                </select>

                <!-- DESCRIÇÃO -->

                <label for="descricao">
                    Descrição (opcional)
                </label>

                <textarea
                    id="descricao"
                    name="descricao"
                    maxlength="500"
                    placeholder="Detalhes da transação"
                    form="registroForm"
                ></textarea>

                <!-- BOTÃO SALVAR -->

                <button
                    type="submit"
                    class="salvar"
                    form="registroForm"
                >
                    Salvar Transação
                </button>

            </form>

        </div>

    </div>

</main>

<script src="../js/script.js"></script>

</body>

</html>