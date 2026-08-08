<?php
include("conn.php");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Novo Registro</title>
  <link rel="stylesheet" href="./CSS/novo.css">
  <link rel="stylesheet" href="./CSS/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<?php include("./estrutura/sidebar_novo.php") ?>

  <main>
    <div class="container">

      <!-- Conteúdo -->
      <main class="content">
        <div class="card">
          <h1>Novo Registro</h1>
          <p>Adicione uma nova transação</p>

          <!-- Tipo -->
          <div class="tipo">
            <button type="button" class="btn tipo-btn active">Entrada</button>
            <button type="button" class="btn tipo-btn">Saída</button>
          </div>

          <!-- Form -->
          <form id="form">
            <input type="number" placeholder="Valor" required />

            <select required>
              <option value="">Selecione uma categoria</option>
              <option>Alimentação</option>
              <option>Transporte</option>
              <option>Lazer</option>
            </select>

            <input type="date" required />

            <textarea placeholder="Descrição (opcional)"></textarea>

            <button type="submit" class="salvar">Salvar Transação</button>
          </form>
        </div>
      </main>

    </div>
  </main>


  <script src="./js/script.js"></script>
</body>

</html>