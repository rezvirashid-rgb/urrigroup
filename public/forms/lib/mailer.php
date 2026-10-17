<?php
/**
 * URRI GROUP — notification mailer (no dependencies).
 *
 * Two transports, chosen with MAIL_TRANSPORT in config.php:
 *   'mail' — PHP's built-in mail() (default; works on most shared hosts)
 *   'smtp' — log in to a real mailbox (e.g. no-reply@urrigroup.com) and
 *            send through it. Much better inbox delivery.
 *
 * Header values are encoded for UTF-8, so Bangla (or any non-English)
 * names and subjects arrive intact; the body is base64-encoded so long
 * lines are never mangled.
 */

declare(strict_types=1);

/**
 * Encode a header value as RFC 2047 UTF-8 when it isn't plain ASCII.
 * The WHOLE value is encoded (not just the non-ASCII words), so nothing
 * typed by a visitor can appear as raw header syntax. Encoded words are
 * kept under 75 chars and split on character boundaries, then folded.
 */
function mail_encode_header(string $text): string {
    if (!preg_match('/[^\x20-\x7E]/', $text)) {
        return $text;
    }
    $words = [];
    $chunk = '';
    foreach (preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $ch) {
        if (strlen($chunk . $ch) > 45) {     // 45 bytes -> 60 base64 chars per word
            $words[] = $chunk;
            $chunk = '';
        }
        $chunk .= $ch;
    }
    $words[] = $chunk;
    return implode("\r\n ", array_map(static function ($w) {
        return '=?UTF-8?B?' . base64_encode($w) . '?=';
    }, $words));
}

/** A safe "Display Name <address>" pair (display name stripped of header syntax). */
function mail_address(string $email, string $name = ''): string {
    $name = trim(preg_replace('/[\r\n"<>,;:\\\\]/', ' ', $name) ?? '');
    $name = preg_replace('/\s+/', ' ', $name) ?? '';
    if ($name === '') {
        return '<' . $email . '>';
    }
    $display = preg_match('/[^\x20-\x7E]/', $name) ? mail_encode_header($name) : '"' . $name . '"';
    return $display . ' <' . $email . '>';
}

/**
 * Send the admin notification. Throws on SMTP failure; returns mail()'s
 * result for the built-in transport.
 */
function send_notification(string $to, string $subject, string $body, string $replyEmail, string $replyName): bool {
    $siteHost = parse_url((string) (defined('SITE_URL') ? SITE_URL : ''), PHP_URL_HOST) ?: 'localhost';
    $fromEml  = defined('MAIL_FROM') ? MAIL_FROM : 'no-reply@' . $siteHost;
    $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Website';

    $headers = [
        'From: ' . mail_address($fromEml, $fromName),
        'Reply-To: ' . mail_address($replyEmail, $replyName),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: base64',
        'X-Mailer: UrriGroup-Website',
    ];
    $encodedSubject = mail_encode_header($subject);
    $encodedBody    = rtrim(chunk_split(base64_encode($body), 76, "\r\n"));

    $transport = defined('MAIL_TRANSPORT') ? MAIL_TRANSPORT : 'mail';
    if ($transport === 'smtp') {
        $message = implode("\r\n", array_merge([
            'Date: ' . date('r'),
            'To: <' . $to . '>',
            'Subject: ' . $encodedSubject,
            'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $siteHost . '>',
        ], $headers)) . "\r\n\r\n" . $encodedBody . "\r\n";
        smtp_send($fromEml, $to, $message, $siteHost);
        return true;
    }

    return @mail($to, $encodedSubject, $encodedBody, implode("\r\n", $headers), '-f' . $fromEml);
}

// ---------------------------------------------------------------------
// Minimal SMTP client: implicit TLS (465), STARTTLS (587) or plain.
// ---------------------------------------------------------------------

function smtp_send(string $from, string $to, string $message, string $heloHost): void {
    $host   = defined('SMTP_HOST') ? SMTP_HOST : '';
    $port   = defined('SMTP_PORT') ? (int) SMTP_PORT : 465;
    $secure = defined('SMTP_SECURE') ? strtolower((string) SMTP_SECURE) : 'ssl';
    $user   = defined('SMTP_USER') ? (string) SMTP_USER : '';
    $pass   = defined('SMTP_PASS') ? (string) SMTP_PASS : '';
    if ($host === '') {
        throw new RuntimeException('SMTP_HOST is not set');
    }

    $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'peer_name' => $host]]);
    $remote = ($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
    $fp = @stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) {
        throw new RuntimeException("SMTP connect failed: $errstr ($errno)");
    }
    stream_set_timeout($fp, 15);

    try {
        smtp_expect($fp, [220]);
        smtp_cmd($fp, "EHLO $heloHost", [250]);
        if ($secure === 'tls') {
            smtp_cmd($fp, 'STARTTLS', [220]);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('STARTTLS negotiation failed');
            }
            smtp_cmd($fp, "EHLO $heloHost", [250]);
        }
        if ($user !== '') {
            smtp_cmd($fp, 'AUTH LOGIN', [334]);
            smtp_cmd($fp, base64_encode($user), [334]);
            smtp_cmd($fp, base64_encode($pass), [235], true);
        }
        smtp_cmd($fp, "MAIL FROM:<$from>", [250]);
        smtp_cmd($fp, "RCPT TO:<$to>", [250, 251]);
        smtp_cmd($fp, 'DATA', [354]);
        // Dot-stuffing: a line that starts with "." gets an extra ".".
        $data = preg_replace('/^\./m', '..', rtrim($message, "\r\n"));
        smtp_cmd($fp, $data . "\r\n.", [250]);
        smtp_cmd($fp, 'QUIT', [221]);
    } finally {
        fclose($fp);
    }
}

/** Send a command and check the reply code. $secret hides it from error messages. */
function smtp_cmd($fp, string $cmd, array $ok, bool $secret = false): void {
    fwrite($fp, $cmd . "\r\n");
    smtp_expect($fp, $ok, $secret ? '[credentials]' : strtok($cmd, "\r\n"));
}

function smtp_expect($fp, array $ok, string $after = 'connect'): void {
    $reply = '';
    while (($line = fgets($fp, 1024)) !== false) {
        $reply .= $line;
        if (strlen($line) < 4 || $line[3] === ' ') {
            break;          // last line of a (possibly multi-line) reply
        }
    }
    $code = (int) substr($reply, 0, 3);
    if (!in_array($code, $ok, true)) {
        throw new RuntimeException('SMTP error after ' . substr($after, 0, 40) . ': ' . trim($reply));
    }
}
