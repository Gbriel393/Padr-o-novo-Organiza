<?php
session_start();
include("../conn.php");

// if ($_SERVER["REQUEST_METHOD"] == "POST") {

//     $email = trim($_POST["email"]);
//     $senha = trim($_POST["senha"]);

//     $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email=?");
//     $stmt->bind_param("s", $email);
//     $stmt->execute();

//     $resultado = $stmt->get_result();

//     if ($resultado->num_rows > 0) {
//         echo "Este email já está cadastrado.";
//     } else {
//         $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

//         $stmt = $conn->prepare("INSERT INTO usuarios (email, senha, dt_criacao) VALUES (?, ?, NOW())");
//         $stmt->bind_param("ss", $email, $senha_hash);

//         if ($stmt->execute()) {
//             header("location:login.php");
//             exit();
//         } else {
//             echo "Erro ao criar a conta.";
//         }
//     }
// }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"] ?? null);
    $email = trim($_POST["email"] ?? null);
    $senha = trim($_POST["senha"] ?? null);
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $verifica = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $verifica->bind_param("s", $email);
    $verifica->execute();

    $resultado = $verifica->get_result();

    if ($resultado->num_rows > 0) {
        echo "<script>
                alert('email já cadastrado!');
                window.location.href = 'nova_conta.php';
              </script>";
              exit;
    }

    $stmt = $conn->prepare("INSERT INTO usuarios(nome,email,senha) VALUE(?,?,?)");
    $stmt->bind_param("sss", $nome, $email, $senha_hash);
    if ($stmt->execute()) {
        header("location:login.php?cadastro=ok");
        exit;
    } else {
        header("location:login.php?cadastro=erro");
        exit;
    }
}


?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Cadastro</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../CSS/esqueciasenha_style.css" />
</head>

<body>
    </head>

    <body>
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="login-box text-center">
                <img src="../img/logo.png" alt="Logo.png" class="logo">

                <h3 class="title text-center">Crie sua conta</h3>

                <form id="loginForm" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control custom-input" id="nome" name="nome"
                            placeholder="Seu nome" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control custom-input" id="email" name="email"
                            placeholder="E-mail@gmail.com" required>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">Senha</label>
                        <input type="password" class="form-control custom-input" id="senha" name="senha" placeholder="......."
                            required>
                        <span class="toggle-password" onclick="togglePassword()"><i class=" bi bi-eye"></i></span>
                    </div>

                    <button type="submit" class="btn btn-gradient w-100">
                        Criar
                    </button>

                    <div class="back">
                        <a href="./login.php">Voltar para login</a>
                    </div>

                </form>

            </div>
        </div>

        <!-- JS -->
        <script src="script.js"></script>




    </body>

</html>