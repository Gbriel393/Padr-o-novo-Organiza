<?php
include("../conn.php");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operações</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/operacoes_style.css">
    <link rel="stylesheet" href="../CSS/navbar/navbar_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script type="text/javascript" src="app.js" defer></script>
    <script src="https://unpkg.com/lucide@0.378.0/dist/umd/lucide.min.js"></script>
</head>

<body>

    <?php include("../estrutura/sidebar_operacoes.php") ?>

    <main>

        <div class="container py-4">
            
            <h1>Operações</h1>
            <p class="subtitle">Ações rápidas e alertas financeiros</p>

            <div class="grid">
                <div class="card">
                    <div class="icon blue"><i data-lucide="zap"></i></div>
                    <h3>Pagamento Rápido</h3>
                    <p>Registre um pagamento instantâneo</p>
                </div>

                <div class="card">
                    <div class="icon purple"><i data-lucide="arrow-right-left"></i></div>
                    <h3>Transferência</h3>
                    <p>Transfira entre contas</p>
                </div>

                <div class="card">
                    <div class="icon green"><i data-lucide="target"></i></div>
                    <h3>Definir Meta</h3>
                    <p>Crie uma nova meta financeira</p>
                </div>

                <div class="card">
                    <div class="icon orange"><i data-lucide="credit-card"></i></div>
                    <h3>Pagar Fatura</h3>
                    <p>Efetue pagamento de cartão</p>
                </div>
            </div>

            <div class="main">

                <div class="alerts">
                    <h2>Alertas Financeiros</h2>

                    <div class="alert">
                        <div class="alert-left red">
                            <i data-lucide="alert-circle"></i>
                        </div>
                        <div class="alert-content">
                            <div class="alert-title">Fatura do Cartão Vencendo</div>
                            <p>R$ 2.130 vence em 3 dias</p>
                            <span class="tag alta">Alta</span>
                        </div>
                    </div>

                    <div class="alert">
                        <div class="alert-left dark-red">
                            <i data-lucide="alert-triangle"></i>
                        </div>
                        <div class="alert-content">
                            <div class="alert-title">Orçamento Excedido</div>
                            <p>Você ultrapassou 90% do orçamento</p>
                            <span class="tag urgente">Urgente</span>
                        </div>
                    </div>

                    <div class="alert">
                        <div class="alert-left yellow">
                            <i data-lucide="clock"></i>
                        </div>
                        <div class="alert-content">
                            <div class="alert-title">Conta de Luz</div>
                            <p>R$ 180 vence em 5 dias</p>
                            <span class="tag media">Média</span>
                        </div>
                    </div>

                    <div class="alert">
                        <div class="alert-left green">
                            <i data-lucide="check-circle"></i>
                        </div>
                        <div class="alert-content">
                            <div class="alert-title">Meta Atingida</div>
                            <p>75% da meta alcançada</p>
                            <span class="tag baixa">Baixa</span>
                        </div>
                    </div>

                </div>

                <div class="insights">

                    <div class="insight-card">
                        <h3>Economia Potencial</h3>
                        <div class="value">R$ 450,00</div>
                        <div class="small">Possível economia no mês</div>
                    </div>

                    <div class="insight-card">
                        <h3>Média de Gastos</h3>
                        <div class="value">R$ 143/dia</div>
                        <div class="small">Baseado no último mês</div>
                    </div>

                    <div class="insight-card">
                        <h3>Próximo Objetivo</h3>
                        <div class="value">R$ 1.200</div>
                        <div class="small">75% concluído</div>
                    </div>

                </div>

            </div>

            <script>
                lucide.createIcons();
            </script>

        </div>

    </main>
</body>

</html>