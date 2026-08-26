  <nav id="sidebar">
    <ul>
      <span class="logo">Organiza</span>

      <li>
        <a href="index.php">
          <i class="fa-regular fa-house"></i>
          <span>Início</span>
        </a>
      </li>

      <li>
        <a href="./novo_registro.php">
          <i class="fa-solid fa-plus" style="color: rgb(0, 0, 0);"></i>
          <span>Cadastro</span>
        </a>
      </li>

      <li class="active">
        <a href="./listagem.php">
          <i class="fa-solid fa-list-ul" style="color: rgb(0, 0, 0);"></i>
          <span>Listagem</span>
        </a>
      </li>

      <li>
        <a href="./operacoes.php">
          <i class="fa-solid fa-left-right" style="color: rgb(0, 0, 0);"></i>
          <span>Operações</span>
        </a>
      </li>

      <li>
        <a href="./banco.php">
          <i class="fa-solid fa-landmark" style="color: rgb(0, 0, 0);"></i>
          <span>Banco</span>
        </a>
      </li>

      <li>
        <a href="./perfil.php">
          <i class="fa-regular fa-user"></i>
          <span>Perfil</span>
        </a>
      </li>

      <li>
        <?php if (isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "admin"): ?>
          <a href="./adm.php">
            <i class="fa-solid fa-users-gear" style="color: rgb(0, 0, 0);"></i>
            <span>Admin</span>
          </a>
        <?php endif; ?>
      </li>
    </ul>
  </nav>