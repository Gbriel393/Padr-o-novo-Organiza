<?php

declare(strict_types=1);
session_start();
require_once __DIR__ . '/../conn.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$usuarioId = (int) $_SESSION['usuario_id'];
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

function redirecionar(string $status): never
{
    header('Location: perfil.php?status=' . urlencode($status));
    exit;
}
function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], (string) ($_POST['csrf_token'] ?? ''))) {
        http_response_code(403);
        exit('Requisição inválida. Atualize a página e tente novamente.');
    }
    $acao = (string) ($_POST['acao'] ?? '');
    if ($acao === 'atualizar_perfil') {
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $push = isset($_POST['notificacoes_push']) ? 1 : 0;
        $alertas = isset($_POST['alertas_email']) ? 1 : 0;
        if ($nome === '' || mb_strlen($nome) > 100) redirecionar('nome_invalido');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) redirecionar('email_invalido');
        $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? AND id <> ? LIMIT 1');
        $stmt->bind_param('si', $email, $usuarioId);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) redirecionar('email_existente');
        $stmt->close();
        $stmt = $conn->prepare('UPDATE usuarios SET nome = ?, email = ?, notificacoes_push = ?, alertas_email = ? WHERE id = ?');
        $stmt->bind_param('ssiii', $nome, $email, $push, $alertas, $usuarioId);
        if (!$stmt->execute()) redirecionar('erro');
        $stmt->close();
        $_SESSION['email'] = $email;
        redirecionar('perfil_ok');
    }
    if ($acao === 'alterar_senha') {
        $atual = (string) ($_POST['senha_atual'] ?? '');
        $nova = (string) ($_POST['nova_senha'] ?? '');
        $confirmacao = (string) ($_POST['confirmar_senha'] ?? '');
        $stmt = $conn->prepare('SELECT senha FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $usuarioId);
        $stmt->execute();
        $credencial = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$credencial || !password_verify($atual, $credencial['senha'])) redirecionar('senha_atual_invalida');
        if (strlen($nova) < 8) redirecionar('senha_curta');
        if ($nova !== $confirmacao) redirecionar('senhas_diferentes');
        $hash = password_hash($nova, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
        $stmt->bind_param('si', $hash, $usuarioId);
        if (!$stmt->execute()) redirecionar('erro');
        $stmt->close();
        session_regenerate_id(true);
        redirecionar('senha_ok');
    }
}

$stmt = $conn->prepare('SELECT nome, email, notificacoes_push, alertas_email FROM usuarios WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$usuario) {
    header('Location: logout.php');
    exit;
}
$mensagens = [
    'perfil_ok' => ['Dados do perfil atualizados.', 'success'],
    'senha_ok' => ['Senha alterada com sucesso.', 'success'],
    'nome_invalido' => ['Informe um nome válido com até 100 caracteres.', 'danger'],
    'email_invalido' => ['Informe um e-mail válido.', 'danger'],
    'email_existente' => ['Este e-mail já está em uso.', 'danger'],
    'senha_atual_invalida' => ['A senha atual está incorreta.', 'danger'],
    'senha_curta' => ['A nova senha deve ter pelo menos 8 caracteres.', 'danger'],
    'senhas_diferentes' => ['A confirmação não corresponde à nova senha.', 'danger'],
    'erro' => ['Não foi possível salvar. Tente novamente.', 'danger']
];
$aviso = $mensagens[(string) ($_GET['status'] ?? '')] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/perfil.css">
    <link rel="stylesheet" href="../CSS/navbar/navbar_index.css">
</head>

<body>
    <?php include __DIR__ . '/../estrutura/sidebar_perfil.php'; ?>
    <main>
        <div class="container container_pg py-5">
            <?php if ($aviso): ?><div class="alert alert-<?= e($aviso[1]) ?> perfil-card mx-auto" role="alert"><?= e($aviso[0]) ?></div><?php endif; ?>
            <div class="card perfil-card mx-auto shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold">Perfil do Usuário</h5>
                    <p class="text-muted small">Gerencie suas informações pessoais</p>
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar"><i class="bi bi-person"></i></div>
                        <div class="ms-3">
                            <h6 class="mb-0"><?= e($usuario['nome']) ?></h6><small class="text-muted"><?= e($usuario['email']) ?></small>
                        </div>
                    </div>
                    <form method="post"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>"><input type="hidden" name="acao" value="atualizar_perfil">
                        <div class="row g-3">
                            <div class="col-md-6"><label for="nome" class="form-label">Nome completo</label><input id="nome" name="nome" type="text" class="form-control" maxlength="100" value="<?= e($usuario['nome']) ?>" required></div>
                            <div class="col-md-6"><label for="email" class="form-label">E-mail</label><input id="email" name="email" type="email" class="form-control" maxlength="150" value="<?= e($usuario['email']) ?>" required></div>
                        </div>
                        <div class="mt-4">
                            <h6 class="fw-semibold"><i class="bi bi-bell me-2"></i>Preferências de notificação</h6>
                            <label class="notif-item" for="notificacoes_push"><span>Notificações push</span><input id="notificacoes_push" name="notificacoes_push" class="form-check-input" type="checkbox" <?= $usuario['notificacoes_push'] ? 'checked' : '' ?>></label>
                            <label class="notif-item" for="alertas_email"><span>Alertas por e-mail</span><input id="alertas_email" name="alertas_email" class="form-check-input" type="checkbox" <?= $usuario['alertas_email'] ? 'checked' : '' ?>></label>
                        </div>
                        <button type="submit" class="btn btn-gradient w-100 mt-4">Salvar alterações</button>
                    </form>
                </div>
            </div>
            <div class="card perfil-card mx-auto shadow-sm mt-4 mb-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-lock me-2"></i>Segurança</h6>
                    <form method="post"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>"><input type="hidden" name="acao" value="alterar_senha">
                        <div class="mb-3"><label for="senha_atual" class="form-label">Senha atual</label><input id="senha_atual" name="senha_atual" type="password" class="form-control" autocomplete="current-password" required></div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6"><label for="nova_senha" class="form-label">Nova senha</label><input id="nova_senha" name="nova_senha" type="password" class="form-control" minlength="8" autocomplete="new-password" required></div>
                            <div class="col-md-6"><label for="confirmar_senha" class="form-label">Confirmar nova senha</label><input id="confirmar_senha" name="confirmar_senha" type="password" class="form-control" minlength="8" autocomplete="new-password" required></div>
                        </div>
                        <button type="submit" class="btn btn-outline-secondary w-100">Alterar senha</button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-3" disabled>Autenticação de dois fatores (em breve)</button>
                </div>
            </div>
            <div class="card perfil-card mx-auto shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">Encerrar sessão</h5>
                        <p class="text-muted small mb-0">Saia com segurança da sua conta</p>
                    </div><a href="logout.php" class="btn btn-logout">Sair</a>
                </div>
            </div>
        </div>
    </main>
</body>

</html>