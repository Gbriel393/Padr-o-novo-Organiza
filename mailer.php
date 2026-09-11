<?php
/**
 * mailer.php
 * Envio de email via SMTP (Gmail) usando apenas PHP puro — sem Composer,
 * sem PHPMailer. Ideal pra projeto de faculdade: só incluir e chamar
 * enviarEmail(...).
 *
 * Depois de chamar enviarEmail(), a variável global $ultimoErroMailer
 * guarda o motivo exato de uma falha (pra mostrar na tela, sem precisar
 * abrir arquivo de log nenhum).
 */

$GLOBALS['ultimoErroMailer'] = '';

function enviarEmail(string $destinatario, string $assunto, string $corpoHtml): bool
{
    $host = MAIL_HOST;
    $port = MAIL_PORT;
    $user = MAIL_USER;
    $pass = MAIL_PASS;
    $fromName = MAIL_FROM_NAME;

    $timeout = 15;
    $socket = @stream_socket_client("tcp://$host:$port", $errno, $errstr, $timeout);
    if (!$socket) {
        $GLOBALS['ultimoErroMailer'] = "Não conseguiu conectar em $host:$port ($errno - $errstr). Pode ser firewall/rede bloqueando a porta $port.";
        return false;
    }

    $ler = function () use ($socket) {
        $resposta = '';
        while ($linha = fgets($socket, 515)) {
            $resposta .= $linha;
            if (isset($linha[3]) && $linha[3] === ' ') break;
        }
        return $resposta;
    };

    $enviar = function (string $comando) use ($socket, $ler) {
        fwrite($socket, $comando . "\r\n");
        return $ler();
    };

    $ler(); // Banner inicial do servidor
    $enviar("EHLO localhost");
    $enviar("STARTTLS");

    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        $GLOBALS['ultimoErroMailer'] = "Conectou, mas falhou ao iniciar a criptografia TLS com $host.";
        fclose($socket);
        return false;
    }

    $enviar("EHLO localhost");
    $enviar("AUTH LOGIN");
    $enviar(base64_encode($user));
    $respostaSenha = $enviar(base64_encode($pass));

    if (strpos($respostaSenha, '235') === false) {
        $GLOBALS['ultimoErroMailer'] = "Falha na autenticação com o Gmail. Resposta do servidor: " . trim($respostaSenha) . ". Confira se MAIL_USER e MAIL_PASS (senha de app) estão certos no config.php.";
        fclose($socket);
        return false;
    }

    $enviar("MAIL FROM:<$user>");
    $enviar("RCPT TO:<$destinatario>");
    $enviar("DATA");

    $headers = "From: $fromName <$user>\r\n";
    $headers .= "To: <$destinatario>\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode($assunto) . "?=\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $mensagem = $headers . "\r\n" . $corpoHtml . "\r\n.";
    $respostaEnvio = $enviar($mensagem);

    $enviar("QUIT");
    fclose($socket);

    $sucesso = strpos($respostaEnvio, '250') !== false;
    if (!$sucesso) {
        $GLOBALS['ultimoErroMailer'] = "O servidor Gmail recusou o envio. Resposta: " . trim($respostaEnvio);
    }

    return $sucesso;
}
