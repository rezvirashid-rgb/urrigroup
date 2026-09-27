<?php
/**
 * =====================================================================
 *  URRI GROUP — Contact form processor (Phase 1)
 * ---------------------------------------------------------------------
 *  Responsibilities:
 *    - Issue a CSRF token   (GET  ?action=token)
 *    - Receive a submission (POST)
 *        • validate + sanitise every field
 *        • block spam (honeypot, rate limit, CSRF / origin check)
 *        • store the message in MySQL via a prepared statement
 *        • email a notification to ADMIN_EMAIL
 *        • return JSON (AJAX) or a simple HTML page (no-JS fallback)
 *
 *  No admin panel, login or user accounts — by design (Phase 1).
 * =====================================================================
 */

declare(strict_types=1);

session_start();

// ---- Load configuration ---------------------------------------------
$configPath = __DIR__ . '/../config/config.php';
if (!file_exists($configPath)) {
    // config.php has not been created yet from config.example.php
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'The site is not fully configured yet. Please email admin@urrigroup.com directly.',
    ]);
    exit;
}
require $configPath;

// Never leak PHP warnings/errors to visitors in production.
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ---- Helpers --------------------------------------------------------

/** Is this an AJAX (fetch/XHR) request? */
function is_ajax(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/** Send a JSON response and stop. */
function respond_json(bool $success, string $message, array $errors = [], int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    $payload = ['success' => $success, 'message' => $message];
    if ($errors) {
        $payload['errors'] = $errors;
    }
    echo json_encode($payload);
    exit;
}

/** Fallback HTML response for visitors without JavaScript. */
function respond_html(bool $success, string $message): void {
    http_response_code($success ? 200 : 400);
    header('Content-Type: text/html; charset=utf-8');
    $title = $success ? 'Message sent' : 'Something went wrong';
    $color = $success ? '#12674f' : '#a5281b';
    $safeMsg = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<title>' . $title . ' — Urri Group</title>'
       . '<style>body{font-family:Poppins,Arial,sans-serif;background:#F8F7FC;color:#191426;'
       . 'display:grid;place-items:center;min-height:100vh;margin:0;padding:24px}'
       . '.box{background:#fff;border:1px solid #e7e3f1;border-radius:16px;padding:40px;max-width:520px;'
       . 'box-shadow:0 12px 34px rgba(18,11,36,.1);text-align:center}'
       . 'h1{color:' . $color . ';font-size:1.4rem}a{display:inline-block;margin-top:18px;color:#fff;'
       . 'background:#4902BC;padding:12px 22px;border-radius:999px;text-decoration:none}</style></head>'
       . '<body><div class="box"><h1>' . $title . '</h1><p>' . $safeMsg . '</p>'
       . '<a href="../contact.html">Back to Contact</a></div></body></html>';
    exit;
}

/** Respond appropriately depending on request type. */
function finish(bool $success, string $message, array $errors = [], int $code = 200): void {
    if (is_ajax()) {
        respond_json($success, $message, $errors, $code);
    }
    respond_html($success, $message);
}

/** Best-effort client IP (handles common proxy headers, safely). */
function client_ip(): string {
    $candidates = [];
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $candidates[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $part) {
            $candidates[] = trim($part);
        }
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $candidates[] = $_SERVER['REMOTE_ADDR'];
    }
    foreach ($candidates as $ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }
    return '0.0.0.0';
}

// =====================================================================
//  ROUTE 1 — Issue a CSRF token
// =====================================================================
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && ($_GET['action'] ?? '') === 'token') {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['token' => $_SESSION['csrf_token']]);
    exit;
}

// =====================================================================
//  ROUTE 2 — Handle a submission (POST only)
// =====================================================================
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    finish(false, 'Invalid request method.', [], 405);
}

// ---- 2a. Honeypot: real users never fill this hidden field ----------
if (!empty($_POST['website'])) {
    // Silently pretend success so bots don't learn they were caught.
    finish(true, 'Thank you for contacting Urri Group. Your message has been received and our team will get back to you shortly.');
}

// ---- 2b. CSRF protection --------------------------------------------
// Primary: session token match. Fallback: same-origin request check
// (protects no-JS submissions where the token could not be fetched).
$tokenOk = false;
$postedToken = $_POST['csrf_token'] ?? '';
if (!empty($_SESSION['csrf_token']) && is_string($postedToken) && $postedToken !== ''
    && hash_equals($_SESSION['csrf_token'], $postedToken)) {
    $tokenOk = true;
}
if (!$tokenOk) {
    $siteHost = parse_url(defined('SITE_URL') ? SITE_URL : '', PHP_URL_HOST);
    $originHeader = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
    $originHost = $originHeader ? parse_url($originHeader, PHP_URL_HOST) : '';
    if ($siteHost && $originHost && strcasecmp($siteHost, $originHost) === 0) {
        $tokenOk = true; // same-origin fallback
    }
}
if (!$tokenOk) {
    finish(false, 'Your session has expired. Please refresh the page and try again.', [], 419);
}

// ---- 2c. Rate limiting (throttle rapid resubmissions) ---------------
$now   = time();
$limit = defined('RATE_LIMIT_SECONDS') ? (int) RATE_LIMIT_SECONDS : 30;
if (isset($_SESSION['last_submit_at']) && ($now - (int) $_SESSION['last_submit_at']) < $limit) {
    finish(false, 'You are sending messages too quickly. Please wait a moment and try again.', [], 429);
}

// ---- 2d. Collect + validate input -----------------------------------
$errors = [];

/** Trim + strip control chars from a scalar input. */
function clean_str($v): string {
    if (!is_string($v)) {
        return '';
    }
    $v = trim($v);
    // Remove null bytes and non-printable control chars (keep newlines/tabs).
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $v);
    return $v ?? '';
}

$name         = clean_str($_POST['name'] ?? '');
$company      = clean_str($_POST['company'] ?? '');
$email        = clean_str($_POST['email'] ?? '');
$phone        = clean_str($_POST['phone'] ?? '');
$businessArea = clean_str($_POST['business_area'] ?? '');
$subject      = clean_str($_POST['subject'] ?? '');
$message      = clean_str($_POST['message'] ?? '');

// Required fields
if ($name === '')          { $errors['name'] = 'Please enter your name.'; }
if ($email === '')         { $errors['email'] = 'Please enter your email address.'; }
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Please enter a valid email address.'; }
if ($businessArea === '')  { $errors['business_area'] = 'Please select a business area.'; }
if ($subject === '')       { $errors['subject'] = 'Please enter a subject.'; }
if ($message === '')       { $errors['message'] = 'Please enter a message.'; }

// Length guards (defensive; DB columns are also bounded)
if (mb_strlen($name) > 120)        { $errors['name'] = 'Name is too long.'; }
if (mb_strlen($company) > 150)     { $errors['company'] = 'Company name is too long.'; }
if (mb_strlen($email) > 150)       { $errors['email'] = 'Email is too long.'; }
if (mb_strlen($phone) > 40)        { $errors['phone'] = 'Phone number is too long.'; }
if (mb_strlen($subject) > 160)     { $errors['subject'] = 'Subject is too long.'; }
if (mb_strlen($message) > 4000)    { $errors['message'] = 'Message is too long (max 4000 characters).'; }

// Whitelist the business area to expected values.
$allowedAreas = [
    'Travel & Mobility', 'International Recruitment', 'Education', 'Sourcing', 'Trade',
    'Real Estate', 'Construction', 'Energy', 'Logistics', 'Hospitality',
    'Consultancy', 'Technology', 'Media', 'Other',
];
if ($businessArea !== '' && !in_array($businessArea, $allowedAreas, true)) {
    $errors['business_area'] = 'Please select a valid business area.';
}

// Reject obvious header-injection attempts in short fields.
if (preg_match('/[\r\n]/', $name . $email . $subject . $phone . $company)) {
    $errors['message'] = $errors['message'] ?? 'Invalid characters detected.';
}

if ($errors) {
    finish(false, 'Please correct the highlighted fields and try again.', $errors, 422);
}

// ---- 2e. Store in database (prepared statement) ---------------------
$ip = client_ip();
$ua = isset($_SERVER['HTTP_USER_AGENT']) ? mb_substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 255) : null;

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

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
    // Log server-side, never expose details to the visitor.
    error_log('[urrigroup contact] DB error: ' . $e->getMessage());
    finish(false, 'Sorry, we could not save your message right now. Please try again shortly or email admin@urrigroup.com.', [], 500);
}

// Mark submission time for rate limiting only after a successful insert.
$_SESSION['last_submit_at'] = $now;

// ---- 2f. Email notification -----------------------------------------
// Failure to email does NOT fail the request — the message is safely stored.
try {
    $to      = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@urrigroup.com';
    $fromEml = defined('MAIL_FROM') ? MAIL_FROM : ('no-reply@' . (parse_url(SITE_URL, PHP_URL_HOST) ?: 'urrigroup.com'));
    $fromNm  = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Urri Group Website';

    $mailSubject = 'New contact form submission: ' . $subject;

    $lines = [
        'A new message was submitted on urrigroup.com',
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
    ];
    $body = implode("\r\n", $lines);

    // Header values are sanitised above (no CR/LF allowed in name/email/subject).
    $headers   = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'From: ' . $fromNm . ' <' . $fromEml . '>';
    $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    @mail($to, $mailSubject, $body, implode("\r\n", $headers), '-f' . $fromEml);
} catch (Throwable $e) {
    error_log('[urrigroup contact] Mail error: ' . $e->getMessage());
}

// ---- 2g. Rotate CSRF token & respond success ------------------------
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

finish(true, 'Thank you for contacting Urri Group. Your message has been received and our team will get back to you shortly.');
