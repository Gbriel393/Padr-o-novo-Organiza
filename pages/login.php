<?php
session_start();
include("../conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"] ?? null;
    $senha = $_POST["senha"] ?? null;

    $smtm = $conn->prepare("SELECT * FROM usuarios WHERE usuario=?");
    $smtm->bind_param("s", $usuario);
    $smtm->execute();
    $result  = $smtm->get_result();
    $linha = $result->fetch_assoc();
    if (password_verify($senha, $linha["senha"])) {
        $_SESSION["usuario_id"] = $linha["id"];
        $_SESSION["usuario"] = $linha["usuario"];
        header("location:index.php");
    } else {
        echo ('Nada Valido');
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    </head>

    <body>
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="login-box text-center">
                <img src="/img/logo.png" alt="Logo.png" class="logo">

                <h3 class="title text-center">Organiza</h3>
                <p class="subtitle text-center">Entre na sua conta</p>

                <form id="loginForm">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control custom-input" id="email"
                            placeholder="seuemail@gmail.com" required>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">Senha</label>
                        <input type="password" class="form-control custom-input" id="senha" placeholder="......."
                            required>
                        <span class="toggle-password" onclick="togglePassword()"><i class=" bi bi-eye"></i></span>
                    </div>

                    <a href="./index.php" class="btn btn-gradient w-100">
                        Entrar
                    </a>

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
        <script src="script.js"></script>




    </body>

</html>