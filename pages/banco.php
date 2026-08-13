<?php
include("../conn.php");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banco</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/navbar/navbar_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./CSS/stle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script type="text/javascript" src="app.js" defer></script>
</head>

<body>

    <?php include("../estrutura/sidebar_banco.php"); ?>

    <main>
        <div class="container py-4">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold">Integração Bancária</h4>
                    <small class="text-muted">Conecte suas contas bancárias</small>
                </div>

                <button class="btn btn-gradient">
                    <i class="bi bi-plus-lg"></i> Adicionar Banco
                </button>
            </div>

            <div class="row g-4">


                <div class="col-md-6">
                    <div class="card bank-card">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-3">
                                <div class="icon green">
                                    <i class="bi bi-bank"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Banco do Brasil</h6>
                                    <small class="text-success">
                                        <i class="bi bi-check"></i> Conectado
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Saldo</small>
                                <div class="fw-semibold">R$ 12.500,00</div>
                            </div>
                        </div>

                        <div class="mt-3 small text-muted">
                            <i class="bi bi-clock"></i> Última sincronização: 2 horas atrás
                        </div>

                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <button class="btn btn-outline-secondary btn-sm px-4">Sincronizar</button>
                            <a href="#" class="text-dark text-decoration-none small">Desconectar</a>
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card bank-card">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-3">
                                <div class="icon green">
                                    <i class="bi bi-bank"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Nubank</h6>
                                    <small class="text-success">
                                        <i class="bi bi-check"></i> Conectado
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Saldo</small>
                                <div class="fw-semibold">R$ 3.420,50</div>
                            </div>
                        </div>

                        <div class="mt-3 small text-muted">
                            <i class="bi bi-clock"></i> Última sincronização: 1 hora atrás
                        </div>

                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <button class="btn btn-outline-secondary btn-sm px-4">Sincronizar</button>
                            <a href="#" class="text-dark text-decoration-none small">Desconectar</a>
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card bank-card disabled-card text-center">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon gray">
                                <i class="bi bi-bank"></i>
                            </div>
                            <h6 class="mb-0">Inter</h6>
                        </div>
                        <button class="btn btn-gradient w-100">Conectar Conta</button>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card bank-card disabled-card text-center">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon gray">
                                <i class="bi bi-bank"></i>
                            </div>
                            <h6 class="mb-0">Bradesco</h6>
                        </div>
                        <button class="btn btn-gradient w-100">Conectar Conta</button>
                    </div>
                </div>

            </div>

            <div class="card mt-5 p-3">
                <h6 class="fw-bold mb-3">Transações Sincronizadas Recentes</h6>

                <div class="transaction">
                    <div>
                        <div>Compra - Supermercado Extra</div>
                        <small class="text-muted">Nubank • 10/04/2026</small>
                    </div>
                    <span class="text-danger">R$ 234,50</span>
                </div>

                <div class="transaction">
                    <div>
                        <div>Transferência recebida</div>
                        <small class="text-muted">Banco do Brasil • 09/04/2026</small>
                    </div>
                    <span class="text-success">+ R$ 1.500,00</span>
                </div>

                <div class="transaction">
                    <div>
                        <div>Pagamento - Netflix</div>
                        <small class="text-muted">Nubank • 08/04/2026</small>
                    </div>
                    <span class="text-danger">R$ 45,90</span>
                </div>

            </div>

        </div>
    </main>
</body>

</html>