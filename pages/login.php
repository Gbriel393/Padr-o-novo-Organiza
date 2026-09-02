<?php
session_start();
include("../conn.php");

$erro = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? null;
    $senha = $_POST["senha"] ?? null;

    $smtm = $conn->prepare("SELECT * FROM usuarios WHERE email=?");
    $smtm->bind_param("s", $email);
    $smtm->execute();

    $result  = $smtm->get_result();
    $linha = $result->fetch_assoc();

    if ($linha && password_verify($senha, $linha["senha"])) {

        $_SESSION["usuario_id"] = $linha["id"];
        $_SESSION["nome"] = $linha["nome"];
        $_SESSION["email"] = $linha["email"];
        $_SESSION["tipo"] = $linha["tipo"];

        header("location:index.php");
        exit();
    } else {
        $erro = true;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login Organiza</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../css/login.css">
</head>


<body>

    <?php if ($erro): ?>

        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert">
            <strong>Erro!</strong> Usuário ou senha inválidos.

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>

    <?php endif; ?>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="login-box text-center">
            <img src="../img/logo.png" alt="Logo.png" class="logo">

            <h3 class="title text-center">Organiza</h3>
            <p class="subtitle text-center">Entre na sua conta</p>

            <form id="loginForm" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control custom-input" id="email" name="email"
                        placeholder="seuemail@gmail.com" required>
                </div>

                <div class="mb-3 position-relative">
                    <label class="form-label">Senha</label>
                    <input type="password" class="form-control custom-input" id="senha" name="senha" placeholder="......."
                        required>

                    <span class="toggle-password" onclick="togglePassword()"><i class=" bi bi-eye"></i></span>
                </div>

                <div class="btn p-0">
                    <button type="submit" name="btn" class="btn btn-gradient w-100">Entrar</button>
                </div>

                <!-- <a href="./index.php" class="btn btn-gradient w-100">
                        Entrar
                    </a> -->

                <div class="text-center mt-3">
                    <a href="../pages/esqueciasenha_index.php" class="forgot">Esqueceu a senha?</a>
                </div>

                <div class="text-center mt-3">
                    <a href="./nova_conta.php" class="forgot">Não tem uma conta? Criar uma</a>
                </div>

            </form>

        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>



</body>

</html>