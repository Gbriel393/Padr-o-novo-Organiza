<?php
session_start();
 
include("../conn.php");
 
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
 
$usuario_id = (int) $_SESSION["usuario_id"];
 
$mes_atual = date("Y-m");
 
$alertas_gerados = array();
 
$sql_saldo = "
    SELECT
        COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE 0 END), 0) AS entradas,
        COALESCE(SUM(CASE WHEN tipo = 'saida' THEN valor ELSE 0 END), 0) AS saidas
    FROM transacoes
    WHERE usuario_id = ?
";
 
$stmt = $conn->prepare($sql_saldo);
 
if ($stmt) {
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
 
    $resultado = $stmt->get_result();
    $dados_saldo = $resultado->fetch_assoc();
 
    $entradas = (float) $dados_saldo["entradas"];
    $saidas = (float) $dados_saldo["saidas"];
 
    if ($saidas > $entradas) {
        $alertas_gerados[] = "Suas despesas estão maiores que suas receitas.";
    }
 
    $stmt->close();
}
 
$sql_despesas = "
    SELECT COALESCE(SUM(valor), 0) AS total
    FROM transacoes
    WHERE usuario_id = ?
    AND tipo = 'saida'
    AND DATE_FORMAT(data_transacao, '%Y-%m') = ?
";
 
$stmt = $conn->prepare($sql_despesas);
 
if ($stmt) {
    $stmt->bind_param("is", $usuario_id, $mes_atual);
    $stmt->execute();
 
    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_assoc();
 
    $total_despesas = (float) $dados["total"];
 
    if ($total_despesas >= 5000) {
        $alertas_gerados[] = "Suas despesas deste mês já ultrapassaram R$ 5.000,00.";
    }
 
    $stmt->close();
}
 
$sql_quantidade = "
    SELECT COUNT(*) AS quantidade
    FROM transacoes
    WHERE usuario_id = ?
    AND tipo = 'saida'
    AND DATE_FORMAT(data_transacao, '%Y-%m') = ?
";
 
$stmt = $conn->prepare($sql_quantidade);
 
if ($stmt) {
    $stmt->bind_param("is", $usuario_id, $mes_atual);
    $stmt->execute();
 
    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_assoc();
 
    $quantidade_despesas = (int) $dados["quantidade"];
 
    if ($quantidade_despesas >= 20) {
        $alertas_gerados[] = "Você já registrou muitas despesas neste mês. Vale a pena revisar seus gastos.";
    }
 
    $stmt->close();
}
 
$sql_pendentes = "
    SELECT COUNT(*) AS quantidade
    FROM transacoes
    WHERE usuario_id = ?
    AND status = 'pendente'
";
 
$stmt = $conn->prepare($sql_pendentes);
 
if ($stmt) {
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
 
    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_assoc();
 
    $quantidade_pendentes = (int) $dados["quantidade"];
 
    if ($quantidade_pendentes > 0) {
        $alertas_gerados[] = "Você possui " . $quantidade_pendentes . " transação(ões) pendente(s).";
    }
 
    $stmt->close();
}
 
foreach ($alertas_gerados as $mensagem) {
 
    $sql_verifica = "
        SELECT id
        FROM alertas
        WHERE usuario_id = ?
        AND mensagem = ?
        LIMIT 1
    ";
 
    $stmt = $conn->prepare($sql_verifica);
 
    if ($stmt) {
 
        $stmt->bind_param("is", $usuario_id, $mensagem);
        $stmt->execute();
 
        $resultado = $stmt->get_result();
 
        if ($resultado->num_rows == 0) {
 
            $sql_insert = "
                INSERT INTO alertas
                (usuario_id, mensagem, status_visto)
                VALUES (?, ?, 'nao_lido')
            ";
 
            $stmt_insert = $conn->prepare($sql_insert);
 
            if ($stmt_insert) {
                $stmt_insert->bind_param("is", $usuario_id, $mensagem);
                $stmt_insert->execute();
                $stmt_insert->close();
            }
        }
 
        $stmt->close();
    }
}
 
if (isset($_GET["ler"])) {
 
    $alerta_id = (int) $_GET["ler"];
 
    $sql = "
        UPDATE alertas
        SET status_visto = 'lido'
        WHERE id = ?
        AND usuario_id = ?
    ";
 
    $stmt = $conn->prepare($sql);
 
    if ($stmt) {
        $stmt->bind_param("ii", $alerta_id, $usuario_id);
        $stmt->execute();
        $stmt->close();
    }
 
    header("Location: alertas_financeiros.php");
    exit;
}
 
if (isset($_GET["ler_todos"])) {
 
    $sql = "
        UPDATE alertas
        SET status_visto = 'lido'
        WHERE usuario_id = ?
        AND status_visto = 'nao_lido'
    ";
 
    $stmt = $conn->prepare($sql);
 
    if ($stmt) {
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();
    }
 
    header("Location: alertas_financeiros.php");
    exit;
}
 
$sql_alertas = "
    SELECT id, mensagem, status_visto, data_alerta
    FROM alertas
    WHERE usuario_id = ?
    ORDER BY
        status_visto = 'nao_lido' DESC,
        data_alerta DESC
";
 
$stmt = $conn->prepare($sql_alertas);
 
$alertas = array();
 
if ($stmt) {
 
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
 
    $resultado = $stmt->get_result();
 
    while ($linha = $resultado->fetch_assoc()) {
        $alertas[] = $linha;
    }
 
    $stmt->close();
}
 
$total_alertas = count($alertas);
$nao_lidos = 0;
$lidos = 0;
 
foreach ($alertas as $alerta) {
 
    if ($alerta["status_visto"] == "nao_lido") {
        $nao_lidos++;
    } else {
        $lidos++;
    }
}
 
?>
 
<!DOCTYPE html>
<html lang="pt-BR">
 
<head>
 
    <meta charset="UTF-8">
 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
    <title>Alertas Financeiros - Organiza</title>
 
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/navbar/navbar_index.css">
 
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >
 
    <style>
 
        .alertas-page {
            padding: 32px 40px 40px;
            min-height: 100vh;
            background: #f7f8fc;
        }
 
        .alertas-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }
 
        .alertas-titulo h1 {
            margin: 0 0 7px;
            font-size: 30px;
            font-weight: 700;
            color: #252b36;
        }
 
        .alertas-titulo p {
            margin: 0;
            color: #7b8190;
            font-size: 15px;
        }
 
        .btn-ler-todos {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 17px;
            border-radius: 9px;
            background: linear-gradient(135deg, #5b5ee7, #7657e8);
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 5px 14px rgba(91, 94, 231, 0.22);
            transition: 0.2s;
        }
 
        .btn-ler-todos:hover {
            transform: translateY(-1px);
            box-shadow: 0 7px 17px rgba(91, 94, 231, 0.28);
        }
 
        .alertas-resumo {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }
 
        .resumo-card {
            position: relative;
            overflow: hidden;
            background: #ffffff;
            border-radius: 14px;
            padding: 20px;
            border: 1px solid #edf0f4;
            box-shadow: 0 4px 14px rgba(30, 41, 59, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }
 
        .resumo-card::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #5b5ee7, #7657e8);
        }
 
        .resumo-card:nth-child(2)::after {
            background: linear-gradient(90deg, #f0a43c, #f6c453);
        }
 
        .resumo-card:nth-child(3)::after {
            background: linear-gradient(90deg, #31a66f, #54c78c);
        }
 
        .resumo-icone {
            width: 47px;
            height: 47px;
            min-width: 47px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #eef0ff, #e7e8ff);
            color: #5b5ee7;
            font-size: 18px;
        }
 
        .resumo-card:nth-child(2) .resumo-icone {
            background: #fff4df;
            color: #e79a27;
        }
 
        .resumo-card:nth-child(3) .resumo-icone {
            background: #eaf8f1;
            color: #35a471;
        }
 
        .resumo-info span {
            display: block;
            color: #8a909d;
            font-size: 13px;
            margin-bottom: 4px;
        }
 
        .resumo-info strong {
            display: block;
            color: #252b36;
            font-size: 21px;
        }
 
        .alertas-box {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #edf0f4;
            box-shadow: 0 4px 14px rgba(30, 41, 59, 0.05);
            overflow: hidden;
        }
 
        .alertas-box-header {
            padding: 21px 24px;
            border-bottom: 1px solid #edf0f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(90deg, #ffffff, #fafaff);
        }
 
        .alertas-box-header h2 {
            margin: 0;
            font-size: 18px;
            color: #292f38;
        }
 
        .alertas-box-header span {
            color: #665fe2;
            font-size: 13px;
            font-weight: 600;
            background: #f0efff;
            padding: 6px 10px;
            border-radius: 20px;
        }
 
        .alerta-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 24px;
            border-bottom: 1px solid #f0f1f4;
            transition: 0.2s;
        }
 
        .alerta-item:last-child {
            border-bottom: none;
        }
 
        .alerta-item:hover {
            background: #fafbff;
        }
 
        .alerta-item.nao-lido {
            background: #f9f9ff;
            border-left: 4px solid #6466e8;
            padding-left: 20px;
        }
 
        .alerta-icone {
            min-width: 44px;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff4df;
            color: #e79a27;
            font-size: 17px;
        }
 
        .alerta-item.nao-lido .alerta-icone {
            background: linear-gradient(135deg, #eef0ff, #e4e5ff);
            color: #5b5ee7;
        }
 
        .alerta-conteudo {
            flex: 1;
        }
 
        .alerta-conteudo strong {
            display: block;
            font-size: 14px;
            color: #303640;
            margin-bottom: 5px;
        }
 
        .alerta-conteudo small {
            color: #969ca7;
            font-size: 12px;
        }
 
        .alerta-status {
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 20px;
            background: #edf8f2;
            color: #35956a;
            font-weight: 600;
        }
 
        .alerta-item.nao-lido .alerta-status {
            background: #eeedff;
            color: #5b5ee7;
        }
 
        .btn-ler {
            text-decoration: none;
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            background: linear-gradient(135deg, #5b5ee7, #7657e8);
            transition: 0.2s;
        }
 
        .btn-ler:hover {
            transform: scale(1.05);
        }
 
        .sem-alertas {
            padding: 70px 20px;
            text-align: center;
        }
 
        .sem-alertas-icone {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #eaf8f1, #ddf4e9);
            color: #35a471;
            font-size: 24px;
        }
 
        .sem-alertas h3 {
            margin: 0 0 7px;
            color: #343a43;
            font-size: 17px;
        }
 
        .sem-alertas p {
            margin: 0;
            color: #969ca7;
            font-size: 13px;
        }
 
        @media (max-width: 900px) {
 
            .alertas-resumo {
                grid-template-columns: 1fr;
            }
 
            .alertas-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 18px;
            }
 
            .alertas-page {
                padding: 25px 20px;
            }
        }
 
        @media (max-width: 600px) {
 
            .alerta-item {
                padding: 15px;
                gap: 11px;
            }
 
            .alerta-item.nao-lido {
                padding-left: 11px;
            }
 
            .alerta-status {
                display: none;
            }
 
            .alertas-box-header {
                padding: 18px;
            }
 
            .alertas-titulo h1 {
                font-size: 25px;
            }
 
            .btn-ler-todos {
                width: 100%;
                justify-content: center;
            }
        }
 
    </style>
 
</head>
 
<body>
 
    <?php include("../estrutura/sidebar_index.php"); ?>
 
    <main class="alertas-page">
 
        <div class="alertas-header">
 
            <div class="alertas-titulo">
 
                <h1>Alertas Financeiros</h1>
 
                <p>
                    Acompanhe avisos importantes sobre suas finanças.
                </p>
 
            </div>
 
            <?php if (count($alertas) > 0): ?>
 
                <a
                    href="alertas_financeiros.php?ler_todos=1"
                    class="btn-ler-todos"
                >
                    <i class="fa-solid fa-check-double"></i>
                    Marcar todos como lidos
                </a>
 
            <?php endif; ?>
 
        </div>
 
        <div class="alertas-resumo">
 
            <div class="resumo-card">
 
                <div class="resumo-icone">
                    <i class="fa-solid fa-bell"></i>
                </div>
 
                <div class="resumo-info">
 
                    <span>Total de alertas</span>
 
                    <strong>
                        <?php echo $total_alertas; ?>
                    </strong>
 
                </div>
 
            </div>
 
            <div class="resumo-card">
 
                <div class="resumo-icone">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
 
                <div class="resumo-info">
 
                    <span>Não lidos</span>
 
                    <strong>
                        <?php echo $nao_lidos; ?>
                    </strong>
 
                </div>
 
            </div>
 
            <div class="resumo-card">
 
                <div class="resumo-icone">
                    <i class="fa-solid fa-check"></i>
                </div>
 
                <div class="resumo-info">
 
                    <span>Já lidos</span>
 
                    <strong>
                        <?php echo $lidos; ?>
                    </strong>
 
                </div>
 
            </div>
 
        </div>
 
        <section class="alertas-box">
 
            <div class="alertas-box-header">
 
                <h2>Seus alertas</h2>
 
                <span>
                    <?php echo $nao_lidos; ?> não lido(s)
                </span>
 
            </div>
 
            <?php if (count($alertas) == 0): ?>
 
                <div class="sem-alertas">
 
                    <div class="sem-alertas-icone">
 
                        <i class="fa-solid fa-check"></i>
 
                    </div>
 
                    <h3>Nenhum alerta financeiro</h3>
 
                    <p>
                        No momento, não há avisos importantes para você.
                    </p>
 
                </div>
 
            <?php else: ?>
 
                <?php foreach ($alertas as $alerta): ?>
 
                    <div
                        class="alerta-item
                        <?php
                        if ($alerta["status_visto"] == "nao_lido") {
                            echo "nao-lido";
                        }
                        ?>"
                    >
 
                        <div class="alerta-icone">
 
                            <?php if ($alerta["status_visto"] == "nao_lido"): ?>
 
                                <i class="fa-solid fa-bell"></i>
 
                            <?php else: ?>
 
                                <i class="fa-solid fa-check"></i>
 
                            <?php endif; ?>
 
                        </div>
 
                        <div class="alerta-conteudo">
 
                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $alerta["mensagem"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );
                                ?>
                            </strong>
 
                            <small>
 
                                <?php
                                echo date(
                                    "d/m/Y H:i",
                                    strtotime($alerta["data_alerta"])
                                );
                                ?>
 
                            </small>
 
                        </div>
 
                        <span class="alerta-status">
 
                            <?php
                            if ($alerta["status_visto"] == "nao_lido") {
                                echo "Não lido";
                            } else {
                                echo "Lido";
                            }
                            ?>
 
                        </span>
 
                        <?php if ($alerta["status_visto"] == "nao_lido"): ?>
 
                            <a
                                href="alertas_financeiros.php?ler=<?php echo (int) $alerta["id"]; ?>"
                                class="btn-ler"
                                title="Marcar como lido"
                            >
                                <i class="fa-solid fa-check"></i>
                            </a>
 
                        <?php endif; ?>
 
                    </div>
 
                <?php endforeach; ?>
 
            <?php endif; ?>
 
        </section>
 
    </main>
 
</body>
 
</html>
 