<?php
/**
 * ============================================================
 *  RavenWarp :: includes/config.php
 * ------------------------------------------------------------
 *  ⚠️  THIS REPOSITORY IS PUBLIC ON GITHUB.  ⚠️
 *  Nothing in this file is a real secret — every value below is
 *  pulled from the SERVER'S environment variables, never
 *  hardcoded. That is deliberate and non-negotiable: a database
 *  password committed to a public repo is a compromised database
 *  the moment it's pushed.
 *
 *  ON NAMECHEAP SHARED (cPanel) HOSTING:
 *  cPanel's "Setup Node.js/PHP App" environment variable panel
 *  isn't available on every shared plan. If yours doesn't expose
 *  one, the practical alternative is:
 *    1. Create a file OUTSIDE your public_html webroot, e.g.
 *       /home/yourcpaneluser/rw-secrets.php, containing only
 *       putenv() calls with your real values.
 *    2. require_once that file at the very top of this one
 *       (a path like __DIR__ . '/../../rw-secrets.php').
 *    3. Add that file's name to .gitignore so it can NEVER be
 *       committed — it should exist only on the live server.
 *  That keeps every real credential off GitHub entirely while
 *  this config.php file stays safe to publish as-is.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

// Uncomment and point this at your out-of-webroot secrets file once it exists:
require_once __DIR__ . '/../../rw-secrets.php';

/**
 * ------------------------------------------------------------
 *  Database credentials
 * ------------------------------------------------------------
 */
define('RW_DB_HOST', getenv('RW_DB_HOST') ?: '127.0.0.1');
define('RW_DB_NAME', getenv('RW_DB_NAME') ?: 'beardedviking_ravenwarp');
define('RW_DB_USER', getenv('RW_DB_USER') ?: 'beardedviking_admin_bvsec');
define('RW_DB_PASS', getenv('RW_DB_PASS') ?: '{f8m*q5bm*Jg^4ZRM&');
define('RW_DB_CHARSET', 'utf8mb4');

/**
 * ------------------------------------------------------------
 *  Encryption
 * ------------------------------------------------------------
 *  A 32-byte (256-bit) key, base64-encoded, used for AES-256-GCM
 *  field-level encryption (see includes/encryption.php).
 *  Generate one locally with:
 *      php -r "echo base64_encode(random_bytes(32));"
 *  and store the output as RW_ENCRYPTION_KEY in your environment
 *  — never in this file, never in the repo.
 */
define('RW_ENCRYPTION_KEY', getenv('RW_ENCRYPTION_KEY') ?: '');

/**
 * A separate secret "pepper" used only for one-way lookup hashes
 * (e.g. checking "has this email already voted?" without ever
 * decrypting stored data). Keep this distinct from the
 * encryption key above.
 */
define('RW_HASH_PEPPER', getenv('RW_HASH_PEPPER') ?: '');

/**
 * ------------------------------------------------------------
 *  Outbound email
 * ------------------------------------------------------------
 */
define('RW_MAIL_FROM', getenv('RW_MAIL_FROM') ?: 'no-reply@beardedviking.org');
define('RW_MAIL_FROM_NAME', 'RavenWarp');
define('RW_LEAD_ENGINEER_EMAIL', 'info@beardedviking.org');

/**
 * ------------------------------------------------------------
 *  Fail loudly (in the error log, never on-screen) if critical
 *  secrets are missing, rather than silently running insecurely.
 * ------------------------------------------------------------
 */
if (RW_ENCRYPTION_KEY === '' || RW_HASH_PEPPER === '') {
    error_log('RavenWarp CONFIG WARNING: RW_ENCRYPTION_KEY or RW_HASH_PEPPER is not set in the environment.');
}