<?php
include("../conn.php");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Início</title>
  <link rel="stylesheet" href="../CSS/style.css">
  <link rel="stylesheet" href="../CSS/navbar/navbar_index.css">
  <link rel="stylesheet" href="../CSS/style_index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script type="text/javascript" src="app.js" defer></script>
</head>

<body>
  <?php include("../estrutura/sidebar_index.php") ?>


  <main>
    <div class="topbar">
      <div class="topbar-left">
        <h1>Dashboard</h1>
        <p>Bem-vindo de volta! Aqui está seu resumo financeiro</p>
      </div>

      <div class="topbar-right">
        <span>Última atualização</span>
        <strong>24 de Março, 2026 - 14:32</strong>
      </div>
    </div>

    <body>

      <!-- TOPO -->
      <header class="topbar">
        <div class="moon">☾</div>
      </header>


      <!-- CONTEÚDO PRINCIPAL -->
      <main class="container">

        <!-- TÍTULO -->
        <section class="page-title">
          <h1>Dashboard</h1>
          <p>Visão geral das suas finanças</p>
        </section>


        <!-- CARDS -->
        <section class="cards">

          <div class="card saldo">
            <div>
              <span>Saldo Total</span>
              <strong>R$ 15.420,50</strong>
            </div>

            <div class="card-icon">$</div>
          </div>


          <div class="card receitas">
            <div>
              <span>Receitas</span>
              <strong>R$ 8.500,00</strong>
            </div>

            <div class="card-icon">↗</div>
          </div>


          <div class="card despesas">
            <div>
              <span>Despesas</span>
              <strong>R$ 6.920,30</strong>
            </div>

            <div class="card-icon">⌁</div>
          </div>

        </section>


        <!-- GRÁFICOS -->
        <section class="charts">

          <!-- GRÁFICO 1 -->
          <div class="chart-box">

            <h2>Receitas vs Despesas</h2>

            <div class="chart">

              <div class="y-axis">
                <span>6000</span>
                <span>4500</span>
                <span>3000</span>
                <span>1500</span>
                <span>0</span>
              </div>

              <div class="graph">

                <div class="horizontal-line line1"></div>
                <div class="horizontal-line line2"></div>
                <div class="horizontal-line line3"></div>
                <div class="horizontal-line line4"></div>
                <div class="horizontal-line line5"></div>

                <svg
                  class="line-chart"
                  viewBox="0 0 600 250"
                  preserveAspectRatio="none">

                  <path
                    d="
                                M 0 70
                                C 80 90, 100 105, 150 110
                                C 200 115, 230 150, 270 145
                                C 320 140, 340 105, 390 120
                                C 450 140, 470 160, 510 150
                                C 550 140, 570 130, 600 135
                                " />

                  <circle cx="0" cy="70" r="3"></circle>
                  <circle cx="120" cy="105" r="3"></circle>
                  <circle cx="270" cy="145" r="3"></circle>
                  <circle cx="390" cy="120" r="3"></circle>
                  <circle cx="510" cy="150" r="3"></circle>
                  <circle cx="600" cy="135" r="3"></circle>

                </svg>

                <div class="months">
                  <span>Jan</span>
                  <span>Fev</span>
                  <span>Mar</span>
                  <span>Abr</span>
                  <span>Mai</span>
                  <span>Jun</span>
                </div>

              </div>

            </div>

          </div>


          <!-- GRÁFICO 2 -->
          <div class="chart-box">

            <h2>Comparativo Mensal</h2>

            <div class="chart">

              <div class="y-axis">
                <span>6000</span>
                <span>4500</span>
                <span>3000</span>
                <span>1500</span>
                <span>0</span>
              </div>

              <div class="graph bar-graph">

                <div class="horizontal-line line1"></div>
                <div class="horizontal-line line2"></div>
                <div class="horizontal-line line3"></div>
                <div class="horizontal-line line4"></div>
                <div class="horizontal-line line5"></div>


                <div class="bars">

                  <div class="bar-item">
                    <div class="bar" style="height: 145px;"></div>
                    <span>Jan</span>
                  </div>

                  <div class="bar-item">
                    <div class="bar" style="height: 110px;"></div>
                    <span>Fev</span>
                  </div>

                  <div class="bar-item">
                    <div class="bar" style="height: 73px;"></div>
                    <span>Mar</span>
                  </div>

                  <div class="bar-item">
                    <div class="bar" style="height: 101px;"></div>
                    <span>Abr</span>
                  </div>

                  <div class="bar-item">
                    <div class="bar" style="height: 68px;"></div>
                    <span>Mai</span>
                  </div>

                  <div class="bar-item">
                    <div class="bar" style="height: 85px;"></div>
                    <span>Jun</span>
                  </div>

                </div>

              </div>

            </div>

          </div>

        </section>


        <!-- ALERTAS -->
        <section class="alerts">

          <h2>Alertas Financeiros</h2>

          <div class="alert alert-warning">
            <span class="alert-icon">!</span>
            <span>Limite de gastos atingido em Alimentação</span>
          </div>

          <div class="alert alert-info">
            <span class="alert-icon">!</span>
            <span>Fatura do cartão vence em 3 dias</span>
          </div>

        </section>

      </main>

    </body>

</html>
</main>
</body>

</html>