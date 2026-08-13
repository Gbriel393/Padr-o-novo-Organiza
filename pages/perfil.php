<?php
include("../conn.php");
?>

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

    <script src="app.js" defer></script>
</head>

<body>

    <?php include("../estrutura/sidebar_perfil.php") ?>

    <main>
        <div class="container py-5">


            <div class="card perfil-card mx-auto shadow-sm">
                <div class="card-body">

                    <h5 class="fw-bold">Perfil do Usuário</h5>
                    <p class="text-muted small">Gerencie suas informações pessoais</p>


                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Usuário Demo</h6>
                            <small class="text-muted">s@gmail.com</small>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome completo</label>
                            <input type="text" class="form-control" value="Usuário Demo">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="s@gmail.com">
                        </div>
                    </div>


                    <div class="mt-4">
                        <h6 class="fw-semibold">
                            <i class="bi bi-bell me-2"></i> Preferências de Notificação
                        </h6>

                        <div class="notif-item">
                            <span>Notificações push</span>
                            <input class="form-check-input" type="checkbox" checked>
                        </div>

                        <div class="notif-item">
                            <span>Alertas por email</span>
                            <input class="form-check-input" type="checkbox">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-gradient w-100">Salvar Alterações</button>
                    </div>

                </div>
            </div>

            <div class="card perfil-card mx-auto shadow-sm mt-4 mb-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-lock me-2"></i> Segurança
                    </h6>

                    <button class="btn btn-outline-secondary w-100 mb-3">
                        Alterar Senha
                    </button>

                    <button class="btn btn-outline-secondary w-100">
                        Autenticação de Dois Fatores
                    </button>
                </div>
            </div>
            <div class="card perfil-card mx-auto shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="fw-bold mb-0">Perfil do Usuário</h5>
                            <p class="text-muted small">Gerencie suas informações pessoais</p>
                        </div>

                        <a href="./login.php" class="btn btn-logout">
                            Sair
                        </a>

                    </div>
                </div>

    </main>
</body>

</html>