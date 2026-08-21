<?php

declare(strict_types=1);
session_start();
require_once __DIR__ . '/../conn.php';
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}
$usuarioId = (int) $_SESSION['usuario_id'];
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
function e(string $valor): string
{
  return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
function moeda(float $valor): string
{
  return 'R$ ' . number_format($valor, 2, ',', '.');
}
function redirecionar(string $status): never
{
  header('Location: listagem.php?status=' . urlencode($status));
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir') {
  if (!hash_equals($_SESSION['csrf_token'], (string) ($_POST['csrf_token'] ?? ''))) {
    http_response_code(403);
    exit('Requisição inválida.');
  }
  $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
  if (!$id) redirecionar('erro');
  $stmt = $conn->prepare('DELETE FROM transacoes WHERE id = ? AND usuario_id = ?');
  $stmt->bind_param('ii', $id, $usuarioId);
  $stmt->execute();
  redirecionar($stmt->affected_rows ? 'excluido' : 'erro');
}

$stmt = $conn->prepare("SELECT COALESCE(SUM(CASE WHEN tipo='entrada' AND status='recebido' THEN valor ELSE 0 END), 0) receitas, COALESCE(SUM(CASE WHEN tipo='saida' AND status='pago' THEN valor ELSE 0 END), 0) despesas FROM transacoes WHERE usuario_id = ?");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$totais = $stmt->get_result()->fetch_assoc();
$stmt->close();
$receitas = (float) $totais['receitas'];
$despesas = (float) $totais['despesas'];
$saldo = $receitas - $despesas;

$busca = trim((string) ($_GET['busca'] ?? ''));
$tipoFiltro = (string) ($_GET['tipo'] ?? 'todos');
$categoriaFiltro = trim((string) ($_GET['categoria'] ?? ''));
$permitidos = ['todos', 'entrada', 'saida', 'fixo', 'variavel', 'pendente', 'pago'];
if (!in_array($tipoFiltro, $permitidos, true)) $tipoFiltro = 'todos';
$sql = 'SELECT id, tipo, natureza, categoria, valor, data_transacao, status, descricao FROM transacoes WHERE usuario_id = ?';
$tipos = 'i';
$parametros = [$usuarioId];
if ($busca !== '') {
  $sql .= ' AND (categoria LIKE ? OR descricao LIKE ? OR status LIKE ?)';
  $termo = '%' . $busca . '%';
  $tipos .= 'sss';
  array_push($parametros, $termo, $termo, $termo);
}
if ($tipoFiltro === 'entrada') {
  $sql .= " AND tipo='entrada'";
} elseif ($tipoFiltro === 'saida') {
  $sql .= " AND tipo='saida'";
} elseif (in_array($tipoFiltro, ['fixo', 'variavel'], true)) {
  $sql .= ' AND natureza = ?';
  $tipos .= 's';
  $parametros[] = $tipoFiltro;
} elseif ($tipoFiltro === 'pendente') {
  $sql .= " AND status='pendente'";
} elseif ($tipoFiltro === 'pago') {
  $sql .= " AND status IN ('pago','recebido')";
}
if ($categoriaFiltro !== '') {
  $sql .= ' AND categoria = ?';
  $tipos .= 's';
  $parametros[] = $categoriaFiltro;
}
$sql .= ' ORDER BY data_transacao DESC, id DESC';
$stmt = $conn->prepare($sql);
$stmt->bind_param($tipos, ...$parametros);
$stmt->execute();
$transacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$stmt = $conn->prepare('SELECT DISTINCT categoria FROM transacoes WHERE usuario_id = ? ORDER BY categoria');
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$categorias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$mensagens = ['criado' => ['Transação salva com sucesso.', 'success'], 'excluido' => ['Transação excluída.', 'success'], 'erro' => ['Não foi possível concluir a operação.', 'danger']];
$aviso = $mensagens[(string) ($_GET['status'] ?? '')] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listagem</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../CSS/style.css">
  <link rel="stylesheet" href="../CSS/listagem.css">
  <link rel="stylesheet" href="../CSS/navbar/navbar_index.css">
</head>

<body><?php include __DIR__ . '/../estrutura/sidebar_listagem.php'; ?><main>
    <div class="container container_pg py-4">
      <?php if ($aviso): ?><div class="alert alert-<?= e($aviso[1]) ?> mt-4"><?= e($aviso[0]) ?></div><?php endif; ?>
      <div class="fx_1 d-flex justify-content-between container p-0 m-0 mt-4 gap-3">
        <div class="botao_rendas d-flex flex-column justify-content-center p-3">
          <div class="d-flex"><span>Lucro (recebido)</span>
            <div class="card-icon ps-2">$</div>
          </div><strong><?= moeda($receitas) ?></strong>
          <p class="mb-0 porcentagem">Total de entradas recebidas</p>
        </div>
        <div class="botao_despesas d-flex flex-column justify-content-center p-3">
          <div class="d-flex"><span>Despesas</span>
            <div class="card-icon ps-2">↗</div>
          </div><strong><?= moeda($despesas) ?></strong>
          <p class="mb-0 porcentagem">Total de saídas pagas</p>
        </div>
        <div class="botao_saldo d-flex flex-column justify-content-center p-3"><span>Saldo Total</span><strong><?= moeda($saldo) ?></strong>
          <p class="mb-0 porcentagem">Entradas recebidas menos despesas pagas</p>
        </div>
      </div>
      <div class="container container_pg_2 p-0">
        <form method="get" class="barra_pesquisa d-flex justify-content-between p-3 my-3">
          <div class="lupa d-flex"><input name="busca" type="search" class="pesquisa" value="<?= e($busca) ?>" placeholder="Pesquisar categoria, descrição ou status..."></div>
          <select class="form-select filtro-select" name="tipo" aria-label="Tipo">
            <option value="todos">Todos os tipos</option>
            <option value="entrada" <?= $tipoFiltro === 'entrada' ? 'selected' : '' ?>>Recebidos</option>
            <option value="saida" <?= $tipoFiltro === 'saida' ? 'selected' : '' ?>>Gastos</option>
            <option value="variavel" <?= $tipoFiltro === 'variavel' ? 'selected' : '' ?>>Gastos variáveis</option>
            <option value="fixo" <?= $tipoFiltro === 'fixo' ? 'selected' : '' ?>>Gastos fixos</option>
            <option value="pago" <?= $tipoFiltro === 'pago' ? 'selected' : '' ?>>Pagos/recebidos</option>
            <option value="pendente" <?= $tipoFiltro === 'pendente' ? 'selected' : '' ?>>Pendentes</option>
          </select>
          <select class="form-select filtro-select" name="categoria">
            <option value="">Todas as categorias</option><?php foreach ($categorias as $item): ?><option value="<?= e($item['categoria']) ?>" <?= $categoriaFiltro === $item['categoria'] ? 'selected' : '' ?>><?= e($item['categoria']) ?></option><?php endforeach; ?>
          </select>
          <button class="btn btn-primary" type="submit">Filtrar</button><?php if ($busca !== '' || $tipoFiltro !== 'todos' || $categoriaFiltro !== ''): ?><a class="btn btn-outline-secondary" href="listagem.php">Limpar</a><?php endif; ?>
        </form>
      </div>
      <div class="estrato">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Tipo</th>
              <th>Categoria</th>
              <th>Valor</th>
              <th>Data</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$transacoes): ?><tr>
                <td colspan="6" class="text-center py-5 text-muted">Nenhuma transação encontrada. <a href="novo_registro.php">Adicionar registro</a></td>
              </tr><?php endif; ?>
            <?php foreach ($transacoes as $t): $entrada = $t['tipo'] === 'entrada';
              $tipoTexto = $entrada ? 'Entrada' : 'Gasto (' . ($t['natureza'] === 'fixo' ? 'Fixo' : 'Variável') . ')';
              $concluido = in_array($t['status'], ['pago', 'recebido'], true); ?>
              <tr title="<?= e((string) $t['descricao']) ?>">
                <th><span class="btn <?= $entrada ? 'btn-outline-primary' : 'btn-outline-danger' ?>"><?= e($tipoTexto) ?></span></th>
                <td><?= e($t['categoria']) ?></td>
                <td class="<?= $entrada ? 'td_valor' : 'td_valor_gasto' ?>"><?= moeda((float) $t['valor']) ?></td>
                <td><?= date('d/m/Y', strtotime($t['data_transacao'])) ?></td>
                <td><span class="btn <?= $concluido ? 'btn-outline-success' : 'btn-outline-secondary' ?>"><?= e(ucfirst($t['status'])) ?></span></td>
                <td>
                  <form method="post" class="form-excluir" onsubmit="return confirm('Excluir esta transação?');"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>"><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int) $t['id'] ?>"><button type="submit" class="acao-excluir" title="Excluir"><i class="fa-regular fa-trash-can"></i></button></form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>

</html>