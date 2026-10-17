<?php
/**
 * =====================================================================
 *  URRI GROUP — Contact form processor (Phase 1)
 * ---------------------------------------------------------------------
 *    GET  ?action=token  -> issue a CSRF token (JSON)
 *    POST                -> validate, store in MySQL, email a notification
 *
 *  Protections: CSRF token (+ same-origin fallback for no-JS visitors),
 *  honeypot, minimum fill time, per-session and per-IP rate limits,
 *  server-side validation, prepared statements, header-injection guards,
 *  and no internal errors shown to visitors.
 *
 *  No admin panel, login or user accounts — by design (Phase 1).
 * =====================================================================
 */

declare(strict_types=1);

const SUCCESS_MESSAGE = 'Thank you for contacting Urri Group. Your message has been received and our team will get back to you shortly.';

// ---- Load configuration ---------------------------------------------
$configPath = __DIR__ . '/../config/config.php';
if (!is_file($configPath)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'The site is not fully configured yet. Please email admin@urrigroup.com directly.']);
    exit;
}
require $configPath;
require __DIR__ . '/lib/mailer.php';

/** Read an optional config constant, falling back to a default. */
function cfg(string $name, $default) {
    return defined($name) ? constant($name) : $default;
}

if (cfg('DEBUG_MODE', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ---- Session (hardened cookie) --------------------------------------
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (cfg('TRUST_PROXY_HEADERS', false) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_name('urri_form');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ---- Helpers --------------------------------------------------------

function is_ajax(): bool {
    return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
}

/** Current CSRF token (created on first use). */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Respond with JSON (AJAX) or a simple HTML page (no-JS fallback), then stop. */
function finish(bool $success, string $message, array $errors = [], int $code = 200): void {
    if (is_ajax()) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        $payload = ['success' => $success, 'message' => $message, 'token' => csrf_token()];
        if ($errors) {
            $payload['errors'] = $errors;
        }
        echo json_encode($payload);
        exit;
    }
    http_response_code($success ? 200 : ($code >= 400 ? $code : 400));
    header('Content-Type: text/html; charset=utf-8');
    $title = $success ? 'Message sent' : 'Something went wrong';
    $color = $success ? '#12674f' : '#a5281b';
    $msg   = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    if ($errors) {
        $msg .= '<ul style="text-align:left">';
        foreach ($errors as $err) {
            $msg .= '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $msg .= '</ul>';
    }
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">'
       . '<title>' . $title . ' — Urri Group</title>'
       . '<style>body{font-family:Poppins,Arial,sans-serif;background:#F8F7FC;color:#191426;'
       . 'display:grid;place-items:center;min-height:100vh;margin:0;padding:24px}'
       . '.box{background:#fff;border:1px solid #e7e3f1;border-radius:16px;padding:40px;max-width:520px;'
       . 'box-shadow:0 12px 34px rgba(18,11,36,.1);text-align:center}'
       . 'h1{color:' . $color . ';font-size:1.4rem}a{display:inline-block;margin-top:18px;color:#fff;'
       . 'background:#4902BC;padding:12px 22px;border-radius:999px;text-decoration:none}</style></head>'
       . '<body><div class="box"><h1>' . $title . '</h1><p>' . $msg . '</p>'
       . '<a href="../contact.html">Back to Contact</a></div></body></html>';
    exit;
}

/**
 * The visitor's IP. Uses the address of the actual TCP connection, which
 * cannot be forged. Forwarded headers are only trusted when the site sits
 * behind a proxy/CDN you control (set TRUST_PROXY_HEADERS in config.php).
 */
function client_ip(): string {
    if (cfg('TRUST_PROXY_HEADERS', false)) {
        $fwd = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '')[0] ?? '');
        $fwd = trim($fwd);
        if (filter_var($fwd, FILTER_VALIDATE_IP)) {
            return $fwd;
        }
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/** Trim and strip control characters (keeps newlines and tabs). */
function clean_str($v): string {
    if (!is_string($v)) {
        return '';
    }
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', trim($v));
    return $v ?? '';   // preg_replace returns null on invalid UTF-8
}

/** Character length that works with or without the mbstring extension. */
function ulen(string $s): int {
    return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : (int) preg_match_all('/./us', $s);
}

// =====================================================================
//  ROUTE 1 — Issue a CSRF token
// =====================================================================
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && ($_GET['action'] ?? '') === 'token') {
    $_SESSION['form_loaded_at'] = time();   // for the minimum-fill-time check
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['token' => csrf_token()]);
    exit;
}

// =====================================================================
//  ROUTE 2 — Handle a submission (POST only)
// =====================================================================
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    finish(false, 'Invalid request method.', [], 405);
}

$now = time();

// ---- Honeypot: real visitors never fill this hidden field -----------
// Pretend success so bots don't learn they were caught.
if (!empty($_POST['website'])) {
    finish(true, SUCCESS_MESSAGE);
}

// ---- CSRF: session token, or same-origin request (no-JS fallback) ---
$postedToken = $_POST['csrf_token'] ?? '';
$viaToken = !empty($_SESSION['csrf_token']) && is_string($postedToken) && $postedToken !== ''
    && hash_equals($_SESSION['csrf_token'], $postedToken);
$sameOrigin = false;
if (!$viaToken) {
    $siteHost   = parse_url((string) cfg('SITE_URL', ''), PHP_URL_HOST);
    $origin     = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
    $originHost = $origin ? parse_url($origin, PHP_URL_HOST) : '';
    // Treat www.example.com and example.com as the same site.
    $norm = static function ($h) { return preg_replace('/^www\./i', '', strtolower((string) $h)); };
    $sameOrigin = $siteHost && $originHost && $norm($siteHost) === $norm($originHost);
}
if (!$viaToken && !$sameOrigin) {
    finish(false, 'Your session has expired. Please refresh the page and try again.', [], 419);
}

// ---- Minimum fill time: nobody completes 5 required fields in 3s ----
$minFill = (int) cfg('MIN_FILL_SECONDS', 3);
if ($viaToken && isset($_SESSION['form_loaded_at']) && ($now - (int) $_SESSION['form_loaded_at']) < $minFill) {
    finish(true, SUCCESS_MESSAGE);
}

// ---- Per-session throttle (cheap first check) -----------------------
$minGap = (int) cfg('RATE_LIMIT_SECONDS', 30);
if (isset($_SESSION['last_submit_at']) && ($now - (int) $_SESSION['last_submit_at']) < $minGap) {
    finish(false, 'You are sending messages too quickly. Please wait a moment and try again.', [], 429);
}

// ---- Collect + validate ---------------------------------------------
$name         = clean_str($_POST['name'] ?? '');
$company      = clean_str($_POST['company'] ?? '');
$email        = clean_str($_POST['email'] ?? '');
$phone        = clean_str($_POST['phone'] ?? '');
$businessArea = clean_str($_POST['business_area'] ?? '');
$subject      = clean_str($_POST['subject'] ?? '');
$message      = clean_str($_POST['message'] ?? '');

$errors = [];
if ($name === '')         { $errors['name'] = 'Please enter your name.'; }
if ($email === '')        { $errors['email'] = 'Please enter your email address.'; }
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Please enter a valid email address.'; }
if ($businessArea === '') { $errors['business_area'] = 'Please select a business area.'; }
if ($subject === '')      { $errors['subject'] = 'Please enter a subject.'; }
if ($message === '')      { $errors['message'] = 'Please enter a message.'; }

if (ulen($name) > 120)    { $errors['name'] = 'Name is too long.'; }
if (ulen($company) > 150) { $errors['company'] = 'Company name is too long.'; }
if (ulen($email) > 150)   { $errors['email'] = 'Email is too long.'; }
if (ulen($phone) > 40)    { $errors['phone'] = 'Phone number is too long.'; }
if (ulen($subject) > 160) { $errors['subject'] = 'Subject is too long.'; }
if (ulen($message) > 4000){ $errors['message'] = 'Message is too long (max 4000 characters).'; }

if ($phone !== '' && !preg_match('/^[0-9+()\-.\s]{5,40}$/', $phone)) {
    $errors['phone'] = 'Please enter a valid phone number.';
}

$allowedAreas = require __DIR__ . '/lib/business_areas.php';
if ($businessArea !== '' && !in_array($businessArea, $allowedAreas, true)) {
    $errors['business_area'] = 'Please select a valid business area.';
}

// Single-line fields must never contain line breaks (header injection).
foreach (['name' => $name, 'company' => $company, 'email' => $email, 'phone' => $phone, 'subject' => $subject] as $key => $val) {
    if (preg_match('/[\r\n]/', $val)) {
        $errors[$key] = 'Please remove line breaks from this field.';
    }
}

if ($errors) {
    finish(false, 'Please correct the highlighted fields and try again.', $errors, 422);
}

// ---- Database: per-IP rate limit, then store ------------------------
$ip = client_ip();
$ua = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
$ua = $ua === '' ? null : (clean_str($ua) ?: null);

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . cfg('DB_CHARSET', 'utf8mb4'),
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    // Per-IP limits survive cookie-less bots that start a new session each time.
    $q = $pdo->prepare(
        'SELECT COUNT(*) AS n, TIMESTAMPDIFF(SECOND, MAX(created_at), NOW()) AS since_last
           FROM contact_submissions
          WHERE ip_address = :ip AND created_at > (NOW() - INTERVAL 1 HOUR)'
    );
    $q->execute([':ip' => $ip]);
    $recent = $q->fetch();
    $perHour = (int) cfg('RATE_LIMIT_PER_HOUR', 5);
    if ((int) $recent['n'] >= $perHour
        || ($recent['since_last'] !== null && (int) $recent['since_last'] < $minGap)) {
        finish(false, 'We have received several messages from your connection recently. Please try again later or email admin@urrigroup.com.', [], 429);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO contact_submissions
            (name, company, email, phone, business_area, subject, message, ip_address, user_agent, status, created_at)
         VALUES
            (:name, :company, :email, :phone, :business_area, :subject, :message, :ip, :ua, :status, NOW())'
    );
    $stmt->execute([
        ':name'          => $name,
        ':company'       => $company !== '' ? $company : null,
        ':email'         => $email,
        ':phone'         => $phone !== '' ? $phone : null,
        ':business_area' => $businessArea,
        ':subject'       => $subject,
        ':message'       => $message,
        ':ip'            => $ip,
        ':ua'            => $ua,
        ':status'        => 'New',
    ]);
} catch (Throwable $e) {
    error_log('[urrigroup contact] DB error: ' . $e->getMessage());
    finish(false, 'Sorry, we could not save your message right now. Please try again shortly or email admin@urrigroup.com.', [], 500);
}

$_SESSION['last_submit_at'] = $now;

// ---- Email notification (never fails the request: the message is saved) ----
$body = implode("\r\n", [
    'A new message was submitted on ' . (parse_url((string) cfg('SITE_URL', ''), PHP_URL_HOST) ?: 'the website'),
    '----------------------------------------------',
    'Name:          ' . $name,
    'Company:       ' . ($company !== '' ? $company : '-'),
    'Email:         ' . $email,
    'Phone:         ' . ($phone !== '' ? $phone : '-'),
    'Business Area: ' . $businessArea,
    'Subject:       ' . $subject,
    '',
    'Message:',
    $message,
    '',
    '----------------------------------------------',
    'IP: ' . $ip,
    'Time (UTC): ' . gmdate('Y-m-d H:i:s'),
]);

try {
    send_notification(
        (string) cfg('ADMIN_EMAIL', 'admin@urrigroup.com'),
        'New contact form submission: ' . $subject,
        $body,
        $email,
        $name
    );
} catch (Throwable $e) {
    error_log('[urrigroup contact] Mail error: ' . $e->getMessage());
}

// Rotate the token so a captured one cannot be replayed.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

finish(true, SUCCESS_MESSAGE);
