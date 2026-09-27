<?php
/**
 * =====================================================================
 *  URRI GROUP — Configuration (EXAMPLE)
 * ---------------------------------------------------------------------
 *  HOW TO USE:
 *    1. Copy this file to  config/config.php
 *    2. Fill in your real database credentials and email settings.
 *    3. NEVER commit config.php to version control.
 *
 *  config.php is git-ignored by default (see .gitignore).
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
// Email notifications
// ---------------------------------------------------------------------
// Where contact-form notifications are delivered:
define('ADMIN_EMAIL', 'admin@urrigroup.com');

// The "From" address the server sends notifications as.
// IMPORTANT: For reliable delivery on shared hosting, use an address
// on your OWN domain (e.g. no-reply@urrigroup.com), not a Gmail/Yahoo one.
define('MAIL_FROM',      'no-reply@urrigroup.com');
define('MAIL_FROM_NAME', 'Urri Group Website');

// ---------------------------------------------------------------------
// Site
// ---------------------------------------------------------------------
define('SITE_URL',  'https://urrigroup.com');   // No trailing slash
define('SITE_NAME', 'Urri Group');

// ---------------------------------------------------------------------
// Social media profile URLs (server-side copy; the public frontend
// copy lives in assets/js/config.js). Leave '#' until real URLs exist.
// ---------------------------------------------------------------------
define('SOCIAL_FACEBOOK',  '#');
define('SOCIAL_INSTAGRAM', '#');
define('SOCIAL_LINKEDIN',  '#');

// ---------------------------------------------------------------------
// Security / anti-spam tuning
// ---------------------------------------------------------------------
// Minimum seconds allowed between two submissions from the same session/IP.
define('RATE_LIMIT_SECONDS', 30);
// Environment: set to false in production so raw errors are never shown.
define('DEBUG_MODE', false);
