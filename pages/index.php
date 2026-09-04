<?php
session_start();
include("../conn.php");

if (!isset($_SESSION["usuario_id"])) {
  header("location:login.php");
  exit();
}

$usuario_id = $_SESSION["usuario_id"];

$sql = "SELECT COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE 0 END), 0) AS receitas,COALESCE(SUM(CASE WHEN tipo = 'saida' THEN valor ELSE 0 END), 0) AS despesas FROM transacoes WHERE usuario_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado = $stmt->get_result()->fetch_assoc();

$receitas = (float)$resultado["receitas"];
$despesas = (float)$resultado["despesas"];

$saldo = $receitas - $despesas;

$stmt->close();

$dados_grafico = [];

$sql = "SELECT 
          MONTH(data_transacao) AS mes,

          COALESCE(
            SUM(
              CASE 
                WHEN tipo = 'entrada' AND status = 'recebido'
                THEN valor 
                ELSE 0 
              END
            ), 0
          ) AS receitas,

          COALESCE(
            SUM(
              CASE 
                WHEN tipo = 'saida' AND status = 'pago'
                THEN valor 
                ELSE 0 
              END
            ), 0
          ) AS despesas

        FROM transacoes

        WHERE usuario_id = ?

        GROUP BY MONTH(data_transacao)

        ORDER BY MONTH(data_transacao)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado = $stmt->get_result();
while ($linha = $resultado->fetch_assoc()) {
  $dados_grafico[(int) $linha["mes"]] = [
    "receitas" => (float)$linha["receitas"],
    "despesas" => (float)$linha["despesas"]
  ];
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Organiza</title>
  <link rel="stylesheet" href="../CSS/style.css">
  <link rel="stylesheet" href="../CSS/navbar/navbar_index.css">
  <link rel="stylesheet" href="../CSS/style_index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script>
    const dados_grafico = <?= json_encode($dados_grafico) ?>;
  </script>
  <script type="text/javascript" src="../js/app.js" defer></script>
</head>

<body>

  <?php include("../estrutura/sidebar_index.php") ?>

  <!-- CONTEÚDO PRINCIPAL -->
  <main>

    <div class="container container_pg  py-0">

      <!-- TÍTULO -->
      <section class="page-title">
        <h1>Olá, <?= htmlspecialchars($_SESSION["nome"]) ?>, seja bem vindo!</h1>
        <p>Visão geral das suas finanças</p>
      </section>


      <!-- CARDS -->
      <section class="cards">

        <div class="card saldo">
          <div>
            <span>Saldo Total</span>
            <strong>R$ <?= number_format($saldo, 2, ',', '.') ?></strong>
          </div>

          <div class="card-icon">$</div>
        </div>


        <div class="card receitas">
          <div>
            <span>Receitas</span>
            <strong>R$ <?= number_format($receitas, 2, ',', '.') ?></strong>
          </div>

          <div class="card-icon">↗</div>
        </div>


        <div class="card despesas">
          <div>
            <span>Despesas</span>
            <strong>R$ <?= number_format($despesas, 2, ',', '.') ?></strong>
          </div>

          <div class="card-icon">⌁</div>
        </div>

      </section>


      <!-- GRÁFICOS -->
      <section class="charts">

       <!-- GRÁFICO 1 -->

        <div class="chart-box">

          <h2>Receitas vs Despesas</h2>

          <div class="chart-carousel">

            <!-- SETA ESQUERDA -->
            <button
              type="button"
              class="chart-nav chart-prev"
              aria-label="Meses anteriores">
              ‹
            </button>


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
                  id="lineChart"
                  class="line-chart"
                  viewBox="0 0 600 220"
                  preserveAspectRatio="none">
                </svg>


                <div
                  id="lineTooltip"
                  class="tooltip">
                </div>


                <div class="months"></div>

              </div>

            </div>


            <!-- SETA DIREITA -->
            <button
              type="button"
              class="chart-nav chart-next"
              aria-label="Próximos meses">
              ›
            </button>

          </div>

        </div>

       <!-- GRÁFICO 2 -->

        <div class="chart-box">

          <h2>Comparativo Mensal</h2>

          <div class="chart-carousel">

            <!-- SETA ESQUERDA -->
            <button
              type="button"
              class="chart-nav chart-prev"
              aria-label="Meses anteriores">
              ‹
            </button>


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


                <!--
              O JAVASCRIPT VAI CRIAR
              AS BARRAS AQUI
          -->
                <div class="bars"></div>

              </div>

            </div>


            <!-- SETA DIREITA -->
            <button
              type="button"
              class="chart-nav chart-next"
              aria-label="Próximos meses">
              ›
            </button>

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

    </div>



  </main>

</body>

</html>

</html>