<?php
/**
 * =====================================================================
 *  URRI GROUP — Configuration (EXAMPLE)
 * ---------------------------------------------------------------------
 *  HOW TO USE:
 *    1. Copy this file to  config/config.php  (on the server)
 *    2. Fill in your real database credentials and email settings.
 *    3. NEVER commit config.php to version control (it is git-ignored).
 *
 *  config.php is blocked from web access by .htaccess.
 * =====================================================================
 */

// ---------------------------------------------------------------------
// Database (MySQL / MariaDB)
// ---------------------------------------------------------------------
define('DB_HOST', 'localhost');          // Usually 'localhost' on cPanel/shared hosting
define('DB_NAME', 'your_database_name');  // e.g. urrigrp_main
define('DB_USER', 'your_database_user');  // e.g. urrigrp_user
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// ---------------------------------------------------------------------
// Site
// ---------------------------------------------------------------------
define('SITE_URL',  'https://urrigroup.com');   // No trailing slash
define('SITE_NAME', 'Urri Group');

// ---------------------------------------------------------------------
// Email notifications
// ---------------------------------------------------------------------
// Where contact-form notifications are delivered:
define('ADMIN_EMAIL', 'admin@urrigroup.com');

// The address notifications are sent FROM. Use an address on your OWN
// domain (create it in your hosting control panel), not Gmail/Yahoo.
define('MAIL_FROM',      'no-reply@urrigroup.com');
define('MAIL_FROM_NAME', 'Urri Group Website');

// How to send:
//   'mail' = the server's built-in PHP mail() — simplest, but messages
//            sometimes land in spam.
//   'smtp' = log in to the MAIL_FROM mailbox and send through it —
//            recommended for reliable delivery. Fill in the SMTP_* values
//            from your hosting control panel's email settings.
define('MAIL_TRANSPORT', 'mail');

define('SMTP_HOST',   'mail.urrigroup.com');  // your host's outgoing mail server
define('SMTP_PORT',   465);                   // 465 with 'ssl', or 587 with 'tls'
define('SMTP_SECURE', 'ssl');                 // 'ssl' (port 465) or 'tls' (port 587)
define('SMTP_USER',   'no-reply@urrigroup.com');
define('SMTP_PASS',   'your_mailbox_password');

// ---------------------------------------------------------------------
// Social media profile URLs (server-side copy; the public website reads
// them from assets/js/config.js). Leave '#' until real URLs exist.
// ---------------------------------------------------------------------
define('SOCIAL_FACEBOOK',  '#');
define('SOCIAL_INSTAGRAM', '#');
define('SOCIAL_LINKEDIN',  '#');

// ---------------------------------------------------------------------
// Anti-spam tuning
// ---------------------------------------------------------------------
define('RATE_LIMIT_SECONDS',  30);  // minimum gap between messages (per visitor and per IP)
define('RATE_LIMIT_PER_HOUR', 5);   // maximum messages per IP address per hour
define('MIN_FILL_SECONDS',    3);   // faster than this = a bot (silently ignored)

// Only set to true if the site sits behind a proxy/CDN you control
// (e.g. Cloudflare). Otherwise visitors could fake their IP address.
define('TRUST_PROXY_HEADERS', false);

// Set to true only while debugging; never on the live site.
define('DEBUG_MODE', false);
