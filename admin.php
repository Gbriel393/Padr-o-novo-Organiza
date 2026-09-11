<?php
require_once 'config.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: pages/login.php');
    exit;
}
if (($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: pages/index.php');
    exit;
}
$usuarioNome = $_SESSION['nome'];
$stmt = $pdo->prepare('SELECT email FROM usuarios WHERE id = :id');
$stmt->execute(['id' => $_SESSION['usuario_id']]);
$usuarioEmail = $stmt->fetch()['email'] ?? '';

// ---------- Métricas dos cards ----------
$totalUsuarios = (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();

$usuariosEsteMes = (int) $pdo->query("SELECT COUNT(*) FROM usuarios WHERE MONTH(data_cadastro)=MONTH(CURDATE()) AND YEAR(data_cadastro)=YEAR(CURDATE())")->fetchColumn();
$usuariosMesPassado = (int) $pdo->query("SELECT COUNT(*) FROM usuarios WHERE MONTH(data_cadastro)=MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(data_cadastro)=YEAR(CURDATE() - INTERVAL 1 MONTH)")->fetchColumn();
$cresceUsuarios = $usuariosMesPassado > 0 ? round((($usuariosEsteMes - $usuariosMesPassado) / $usuariosMesPassado) * 100) : ($usuariosEsteMes > 0 ? 100 : 0);

$totalTransacoes = (int) $pdo->query('SELECT COUNT(*) FROM transacoes')->fetchColumn();
$transacoesEstaSemana = (int) $pdo->query("SELECT COUNT(*) FROM transacoes WHERE YEARWEEK(data_transacao, 1) = YEARWEEK(CURDATE(), 1)")->fetchColumn();
$transacoesSemanaPassada = (int) $pdo->query("SELECT COUNT(*) FROM transacoes WHERE YEARWEEK(data_transacao, 1) = YEARWEEK(CURDATE() - INTERVAL 7 DAY, 1)")->fetchColumn();
$cresceTransacoes = $transacoesSemanaPassada > 0 ? round((($transacoesEstaSemana - $transacoesSemanaPassada) / $transacoesSemanaPassada) * 100) : ($transacoesEstaSemana > 0 ? 100 : 0);

$volumeTotal = (float) $pdo->query('SELECT COALESCE(SUM(valor),0) FROM transacoes')->fetchColumn();
$volumeEsteMes = (float) $pdo->query("SELECT COALESCE(SUM(valor),0) FROM transacoes WHERE MONTH(data_transacao)=MONTH(CURDATE()) AND YEAR(data_transacao)=YEAR(CURDATE())")->fetchColumn();
$volumeMesPassado = (float) $pdo->query("SELECT COALESCE(SUM(valor),0) FROM transacoes WHERE MONTH(data_transacao)=MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(data_transacao)=YEAR(CURDATE() - INTERVAL 1 MONTH)")->fetchColumn();
$cresceVolume = $volumeMesPassado > 0 ? round((($volumeEsteMes - $volumeMesPassado) / $volumeMesPassado) * 100) : ($volumeEsteMes > 0 ? 100 : 0);

function formatarCompacto($valor) {
    if ($valor >= 1000000) return 'R$ ' . number_format($valor / 1000000, 1, ',', '.') . 'M';
    if ($valor >= 1000) return 'R$ ' . number_format($valor / 1000, 1, ',', '.') . 'K';
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

$usuariosComTransacao = (int) $pdo->query('SELECT COUNT(DISTINCT usuario_id) FROM transacoes')->fetchColumn();
$atividade = $totalUsuarios > 0 ? round(($usuariosComTransacao / $totalUsuarios) * 100) : 0;

// ---------- Gráfico: Crescimento de Usuários (acumulado, últimos 6 meses) ----------
$mesesLabels = [];
$crescimentoDados = [];
$mesesPt = ['01'=>'Jan','02'=>'Fev','03'=>'Mar','04'=>'Abr','05'=>'Mai','06'=>'Jun','07'=>'Jul','08'=>'Ago','09'=>'Set','10'=>'Out','11'=>'Nov','12'=>'Dez'];

for ($i = 5; $i >= 0; $i--) {
    $referencia = date('Y-m-t', strtotime("-$i months"));
    $mesesLabels[] = $mesesPt[date('m', strtotime($referencia))];
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE data_cadastro <= :ref');
    $stmt->execute(['ref' => $referencia . ' 23:59:59']);
    $crescimentoDados[] = (int) $stmt->fetchColumn();
}

// ---------- Gráfico: Transações por Dia (dia da semana) ----------
$diasLabels = ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'];
$transacoesPorDia = array_fill(0, 7, 0);
$stmt = $pdo->query("SELECT WEEKDAY(data_transacao) AS dia, COUNT(*) AS total FROM transacoes GROUP BY dia");
foreach ($stmt->fetchAll() as $linha) {
    $transacoesPorDia[(int) $linha['dia']] = (int) $linha['total'];
}

// ---------- Usuários recentes ----------
$usuariosRecentes = $pdo->query('SELECT nome, email, tipo, data_cadastro FROM usuarios ORDER BY data_cadastro DESC LIMIT 6')->fetchAll();

// ---------- Logs do sistema ----------
$logs = $pdo->query('SELECT nivel, mensagem, criado_em FROM logs_sistema ORDER BY criado_em DESC LIMIT 8')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organiza - Painel Administrativo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body class="dashboard-body">

<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-icon"><i class="bi bi-bar-chart-fill"></i></span>
            <span class="brand-a">Organiza</span>
        </div>

        <nav class="nav-menu">
            <a href="pages/index.php" class="nav-item"><span class="nav-icon"><i class="bi bi-house-door-fill"></i></span> Início</a>
            <a href="nova_conta.php" class="nav-item"><span class="nav-icon"><i class="bi bi-plus-circle"></i></span> Novo</a>
            <a href="lista.php" class="nav-item"><span class="nav-icon"><i class="bi bi-clipboard-check"></i></span> Lista</a>
            <a href="operacoes.php" class="nav-item"><span class="nav-icon"><i class="bi bi-arrow-left-right"></i></span> Operações</a>
            <a href="banco.php" class="nav-item"><span class="nav-icon"><i class="bi bi-bank2"></i></span> Banco</a>
            <a href="perfil.php" class="nav-item"><span class="nav-icon"><i class="bi bi-person-fill"></i></span> Perfil</a>
            <a href="admin.php" class="nav-item active"><span class="nav-icon"><i class="bi bi-shield-lock-fill"></i></span> Admin</a>
        </nav>

        <div class="sidebar-bottom">
            <a href="logout.php" class="nav-item"><span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span> Sair</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar-dash">
            <div></div>
        </div>

        <h2 class="page-title">Painel Administrativo</h2>
        <p class="page-subtitle">Métricas e gerenciamento do sistema</p>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <button type="button" class="stat-card stat-purple border-0 w-100 text-start" data-bs-toggle="modal" data-bs-target="#modalDetalhe" data-titulo="Usuários Ativos" data-texto="Total de contas cadastradas no sistema: <?= $totalUsuarios ?>. Crescimento de <?= $cresceUsuarios ?>% em relação ao mês passado (<?= $usuariosMesPassado ?> → <?= $usuariosEsteMes ?> cadastros este mês).">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="stat-label">Usuários Ativos</div>
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-value"><?= number_format($totalUsuarios, 0, ',', '.') ?></div>
                    <div class="stat-delta"><?= $cresceUsuarios >= 0 ? '+' : '' ?><?= $cresceUsuarios ?>% este mês</div>
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button type="button" class="stat-card stat-cyan border-0 w-100 text-start" data-bs-toggle="modal" data-bs-target="#modalDetalhe" data-titulo="Transações" data-texto="Total de transações registradas: <?= $totalTransacoes ?>. Essa semana: <?= $transacoesEstaSemana ?> (semana passada: <?= $transacoesSemanaPassada ?>).">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="stat-label">Transações</div>
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stat-value"><?= number_format($totalTransacoes, 0, ',', '.') ?></div>
                    <div class="stat-delta"><?= $cresceTransacoes >= 0 ? '+' : '' ?><?= $cresceTransacoes ?>% esta semana</div>
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button type="button" class="stat-card stat-green border-0 w-100 text-start" data-bs-toggle="modal" data-bs-target="#modalDetalhe" data-titulo="Volume Total" data-texto="Soma de todas as transações: <?= formatarCompacto($volumeTotal) ?>. Volume deste mês: <?= formatarCompacto($volumeEsteMes) ?>.">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="stat-label">Volume Total</div>
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="stat-value"><?= formatarCompacto($volumeTotal) ?></div>
                    <div class="stat-delta"><?= $cresceVolume >= 0 ? '+' : '' ?><?= $cresceVolume ?>% este mês</div>
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button type="button" class="stat-card stat-pink border-0 w-100 text-start" data-bs-toggle="modal" data-bs-target="#modalDetalhe" data-titulo="Atividade" data-texto="<?= $usuariosComTransacao ?> de <?= $totalUsuarios ?> usuários já registraram pelo menos uma transação (taxa de engajamento de <?= $atividade ?>%).">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="stat-label">Atividade</div>
                        <i class="bi bi-activity"></i>
                    </div>
                    <div class="stat-value"><?= $atividade ?>%</div>
                    <div class="stat-delta">Taxa de engajamento</div>
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="chart-card">
                    <h5>Crescimento de Usuários</h5>
                    <canvas id="chartCrescimento" height="220"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-card">
                    <h5>Transações por Dia</h5>
                    <canvas id="chartTransacoesDia" height="220"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-card mb-4">
            <h5>Usuários Recentes</h5>
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr><th>Nome</th><th>Email</th><th>Data de Cadastro</th><th>Ações</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($usuariosRecentes as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['nome']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= date('d/m/Y', strtotime($u['data_cadastro'])) ?></td>
                            <td>
                                <a href="#" class="ver-detalhes-link" data-bs-toggle="modal" data-bs-target="#modalDetalhe"
                                   data-titulo="<?= htmlspecialchars($u['nome']) ?>"
                                   data-texto="Email: <?= htmlspecialchars($u['email']) ?><br>Tipo de conta: <?= htmlspecialchars($u['tipo']) ?><br>Cadastrado em: <?= date('d/m/Y \à\s H:i', strtotime($u['data_cadastro'])) ?>">Ver detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($usuariosRecentes)): ?>
                        <tr><td colspan="4" class="text-muted">Nenhum usuário cadastrado ainda.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="chart-card">
            <h5>Logs do Sistema</h5>
            <?php if (empty($logs)): ?>
                <p class="text-muted mb-0">Nenhum log registrado ainda.</p>
            <?php else: foreach ($logs as $log): ?>
                <div class="log-line">
                    <span class="log-nivel log-<?= strtolower($log['nivel']) ?>">[<?= htmlspecialchars($log['nivel']) ?>]</span>
                    <?= date('d/m/Y H:i', strtotime($log['criado_em'])) ?> - <?= htmlspecialchars($log['mensagem']) ?>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </main>
</div>

<!-- Modal de detalhes (usado pelos cards e pelo "Ver detalhes") -->
<div class="modal fade" id="modalDetalhe" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetalheTitulo">Detalhes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalDetalheTexto">...</div>
    </div>
  </div>
</div>

<script>
    document.getElementById('modalDetalhe').addEventListener('show.bs.modal', function (event) {
        const gatilho = event.relatedTarget;
        document.getElementById('modalDetalheTitulo').innerText = gatilho.getAttribute('data-titulo');
        document.getElementById('modalDetalheTexto').innerHTML = gatilho.getAttribute('data-texto');
    });

    new Chart(document.getElementById('chartCrescimento'), {
        type: 'line',
        data: {
            labels: <?= json_encode($mesesLabels) ?>,
            datasets: [{
                label: 'Usuários',
                data: <?= json_encode($crescimentoDados) ?>,
                borderColor: '#8b3ce0',
                backgroundColor: 'rgba(139,60,224,0.12)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#8b3ce0',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('chartTransacoesDia'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($diasLabels) ?>,
            datasets: [{
                label: 'Transações',
                data: <?= json_encode(array_values($transacoesPorDia)) ?>,
                backgroundColor: '#22b8e6',
                borderRadius: 6
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
</script>

</body>
</html>
