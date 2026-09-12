<?php
/**
 * ============================================================
 *  RavenWarp :: includes/config.php
 * ------------------------------------------------------------
 *  Loads .env (via env.php) and exposes everything as constants.
 *  This repo is PUBLIC on GitHub — nothing in this file is ever
 *  a real value; every value comes from .env, which is excluded
 *  via .gitignore and blocked at the web-server level by .htaccess.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

require_once __DIR__ . '/.env';
rw_load_env(__DIR__ . '/../.env');

// ------------------------------------------------------------
// Database
// ------------------------------------------------------------
define('RW_DB_HOST', getenv('RW_DB_HOST') ?: '127.0.0.1');
define('RW_DB_NAME', getenv('RW_DB_NAME') ?: '');
define('RW_DB_USER', getenv('RW_DB_USER') ?: '');
define('RW_DB_PASS', getenv('RW_DB_PASS') ?: '');
define('RW_DB_CHARSET', 'utf8mb4');

// ------------------------------------------------------------
// Encryption
// ------------------------------------------------------------
define('RW_ENCRYPTION_KEY', getenv('RW_ENCRYPTION_KEY') ?: '');
define('RW_HASH_PEPPER', getenv('RW_HASH_PEPPER') ?: '');

// ------------------------------------------------------------
// Mail
// ------------------------------------------------------------
define('RW_MAIL_FROM', getenv('RW_MAIL_FROM') ?: 'no-reply@beardedviking.org');
define('RW_MAIL_FROM_NAME', 'RavenWarp');
define('RW_LEAD_ENGINEER_EMAIL', 'info@beardedviking.org');

// ------------------------------------------------------------
// Reserved for future features — empty until the feature exists.
// Defined here now so adding the feature later never means
// touching this file's structure, just filling in .env.
// ------------------------------------------------------------
define('RW_STRIPE_SECRET_KEY', getenv('RW_STRIPE_SECRET_KEY') ?: '');
define('RW_AI_CHAT_API_KEY', getenv('RW_AI_CHAT_API_KEY') ?: '');

// ------------------------------------------------------------
// Fail loudly in the error log (never on-screen) if critical
// secrets are missing.
// ------------------------------------------------------------
if (RW_ENCRYPTION_KEY === '' || RW_HASH_PEPPER === '') {
    error_log('RavenWarp CONFIG WARNING: RW_ENCRYPTION_KEY or RW_HASH_PEPPER is not set.');
}
if (RW_DB_NAME === '' || RW_DB_USER === '') {
    error_log('RavenWarp CONFIG WARNING: Database credentials are not set in .env.');
}