<nav id="sidebar">

    <div class="sidebar-logo">
        <div class="logo-icon">
            <i class="fa-solid fa-chart-simple"></i>
        </div>

        <span>Organiza</span>
    </div>

    <ul>

        <li class="active">
            <a href="index.php">
                <i class="fa-regular fa-house"></i>
                <span>Início</span>
            </a>
        </li>

        <li>
            <a href="./novo_registro.php">
                <i class="fa-solid fa-plus"></i>
                <span>Cadastro</span>
            </a>
        </li>

        <li>
            <a href="./listagem.php">
                <i class="fa-solid fa-list-ul"></i>
                <span>Listagem</span>
            </a>
        </li>

        <li>
            <a href="./operacoes.php">
                <i class="fa-solid fa-left-right"></i>
                <span>Operações</span>
            </a>
        </li>

        <li>
            <a href="./banco.php">
                <i class="fa-solid fa-landmark"></i>
                <span>Banco</span>
            </a>
        </li>

        <li>
            <a href="./perfil.php">
                <i class="fa-regular fa-user"></i>
                <span>Perfil</span>
            </a>
        </li>

        <?php if (isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "admin"): ?>

            <li>
                <a href="./adm.php">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Admin</span>
                </a>
            </li>

        <?php endif; ?>
        
    </ul>

    <div class="sidebar-footer">
        <span>Controle hoje</span>
        <strong>Conquiste amanhã</strong>
    </div>

</nav>