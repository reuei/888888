<?php
function smtp_send(string $to, string $subject, string $body, string $from = '', string $fromName = ''): bool {
    $host = setting('smtp_host');
    $port = intval(setting('smtp_port', '25'));
    $user = setting('smtp_user');
    $pass = setting('smtp_pass');
    if (!$host || !$user || !$pass) {
        return false;
    }
    $secure = strtolower(setting('smtp_secure', ''));
    $from = $from ?: $user;
    $fromName = $fromName ?: $from;

    $address = ($secure === 'ssl' ? 'ssl://' : '') . $host;
    $sock = @fsockopen($address, $port, $errno, $errstr, 10);
    if (!$sock) {
        return false;
    }
    stream_set_timeout($sock, 10);

    $read = function () use ($sock) {
        $res = '';
        while (substr($res, 3, 1) !== ' ') {
            $line = fgets($sock, 512);
            if ($line === false) break;
            $res .= $line;
        }
        return $res;
    };

    $cmd = function ($c) use ($sock) {
        fwrite($sock, $c . "\r\n");
    };

    $read();
    $cmd('EHLO yuyun');
    $read();

    if ($secure === 'tls') {
        $cmd('STARTTLS');
        $read();
        stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $cmd('EHLO yuyun');
        $read();
    }

    $cmd('AUTH LOGIN');
    $read();
    $cmd(base64_encode($user));
    $read();
    $cmd(base64_encode($pass));
    $res = $read();
    if (substr($res, 0, 3) !== '235') {
        fclose($sock);
        return false;
    }

    $cmd('MAIL FROM:<' . $from . '>');
    $read();
    $cmd('RCPT TO:<' . $to . '>');
    $read();
    $cmd('DATA');
    $read();

    $nameHeader = $fromName ? '=?UTF-8?B?' . base64_encode($fromName) . '?=' : $from;
    $subjectHeader = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $data = "From: {$nameHeader} <{$from}>\r\n";
    $data .= "To: {$to}\r\n";
    $data .= "Subject: {$subjectHeader}\r\n";
    $data .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $data .= "Content-Transfer-Encoding: base64\r\n";
    $data .= "\r\n" . base64_encode($body) . "\r\n.";

    $cmd($data);
    $res = $read();
    $cmd('QUIT');
    fclose($sock);

    return substr($res, 0, 3) === '250';
}
