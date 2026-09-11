<?php
require_once 'config.php';
require_once 'mailer.php';

// if (!empty($_SESSION['usuario_id'])) {
//     header('Location: index.php');
//     exit;
// }

$mensagem = '';
$erro = '';
$emailEnviado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $erro = 'Digite seu email.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nome FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            // Código de 6 dígitos, mais simples de digitar que um link
            $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiraEm = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            $stmt = $pdo->prepare('INSERT INTO password_resets (usuario_id, token, expira_em) VALUES (:uid, :token, :exp)');
            $stmt->execute(['uid' => $usuario['id'], 'token' => $codigo, 'exp' => $expiraEm]);

            $corpo = "
                <div style='font-family: Poppins, Arial, sans-serif; max-width:480px; margin:auto;'>
                    <h2 style='color:#7c3aed;'>Organiza</h2>
                    <p>Olá, " . htmlspecialchars($usuario['nome']) . "!</p>
                    <p>Use o código abaixo para redefinir sua senha (válido por 15 minutos):</p>
                    <p style='text-align:center; margin: 28px 0;'>
                        <span style='font-size:32px; font-weight:700; letter-spacing:8px; color:#7c3aed;'>$codigo</span>
                    </p>
                    <p style='color:#6b7280; font-size:0.85rem;'>Se você não pediu isso, pode ignorar este email.</p>
                </div>
            ";

            enviarEmail($email, 'Seu código de redefinição - Organiza', $corpo);
        }

        // Mesma mensagem sempre, exista ou não o email (segurança)
        $mensagem = 'Se esse email existir na nossa base, enviamos um código de 6 dígitos para ele. Confira sua caixa de entrada (e o spam).';
        $emailEnviado = $email;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organiza - Esqueci a senha</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="login-card">
    <h1><span class="brand-icon" style="font-size:1.3rem;margin-right:4px;vertical-align:middle;"><i class="bi bi-bar-chart-fill"></i></span>Organiza</h1>
    <p class="subtitle">Esqueceu sua senha?</p>

    <?php if ($erro): ?>
        <div class="alert alert-danger alert-custom" role="alert"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <?php if ($mensagem): ?>
        <div class="alert alert-success alert-custom" role="alert"><?= htmlspecialchars($mensagem) ?></div>
        <a href="redefinir_senha.php?email=<?= urlencode($emailEnviado) ?>" class="btn btn-gradient d-block text-center text-decoration-none">Já tenho meu código</a>
    <?php else: ?>
    <form method="POST" action="esqueci_senha.php" novalidate>
        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control field-custom" id="email" name="email"
                   placeholder="seu@email.com" required>
        </div>
        <button type="submit" class="btn btn-gradient">Enviar código</button>
    </form>
    <?php endif; ?>

    <a href="login.php" class="forgot-link">Voltar para o login</a>
</div>

</body>
</html>
