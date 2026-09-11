<?php
require_once 'config.php';

$erro = '';
$sucesso = false;
$emailPreenchido = $_GET['email'] ?? $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $novaSenha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    if ($email === '' || $codigo === '') {
        $erro = 'Preencha o email e o código recebido.';
    } elseif (strlen($novaSenha) < 6) {
        $erro = 'A senha precisa ter pelo menos 6 caracteres.';
    } elseif ($novaSenha !== $confirmar) {
        $erro = 'As senhas não coincidem.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            $erro = 'Email ou código inválido.';
        } else {
            $stmt = $pdo->prepare("SELECT id, expira_em, usado FROM password_resets WHERE usuario_id = :uid AND token = :codigo ORDER BY id DESC LIMIT 1");
            $stmt->execute(['uid' => $usuario['id'], 'codigo' => $codigo]);
            $reset = $stmt->fetch();

            if (!$reset) {
                $erro = 'Email ou código inválido.';
            } elseif ($reset['usado']) {
                $erro = 'Esse código já foi usado. Solicite um novo.';
            } elseif (strtotime($reset['expira_em']) < time()) {
                $erro = 'Esse código expirou. Solicite um novo.';
            } else {
                $hash = password_hash($novaSenha, PASSWORD_DEFAULT);

                $pdo->beginTransaction();
                $stmt = $pdo->prepare('UPDATE usuarios SET senha = :senha WHERE id = :id');
                $stmt->execute(['senha' => $hash, 'id' => $usuario['id']]);

                $stmt = $pdo->prepare('UPDATE password_resets SET usado = 1 WHERE id = :id');
                $stmt->execute(['id' => $reset['id']]);
                $pdo->commit();

                $sucesso = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organiza - Nova senha</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="login-card">
    <h1><span class="brand-icon" style="font-size:1.3rem;margin-right:4px;vertical-align:middle;"><i class="bi bi-bar-chart-fill"></i></span>Organiza</h1>
    <p class="subtitle">Digite o código e sua nova senha</p>

    <?php if ($sucesso): ?>
        <div class="alert alert-success alert-custom">Senha alterada com sucesso!</div>
        <a href="pages/login.php" class="btn btn-gradient d-block text-center text-decoration-none">Ir para o login</a>
    <?php else: ?>
        <?php if ($erro): ?>
            <div class="alert alert-danger alert-custom"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST" action="redefinir_senha.php" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control field-custom" id="email" name="email"
                       value="<?= htmlspecialchars($emailPreenchido) ?>" placeholder="seu@email.com" required>
            </div>
            <div class="mb-3">
                <label for="codigo" class="form-label">Código recebido por email</label>
                <input type="text" class="form-control field-custom" id="codigo" name="codigo"
                       placeholder="000000" maxlength="6" required style="letter-spacing:6px; text-align:center; font-weight:700;">
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Nova senha</label>
                <input type="password" class="form-control field-custom" id="senha" name="senha"
                       placeholder="Mínimo 6 caracteres" required minlength="6">
            </div>
            <div class="mb-4">
                <label for="confirmar_senha" class="form-label">Confirmar nova senha</label>
                <input type="password" class="form-control field-custom" id="confirmar_senha" name="confirmar_senha"
                       placeholder="Repita a senha" required minlength="6">
            </div>
            <button type="submit" class="btn btn-gradient">Salvar nova senha</button>
        </form>
        <a href="esqueci_senha.php" class="forgot-link">Reenviar código</a>
    <?php endif; ?>
</div>

</body>
</html>
