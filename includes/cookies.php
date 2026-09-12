<?php
/**
 * ============================================================
 *  RavenWarp :: includes/cookies.php  (Command Deck rebuild)
 * ------------------------------------------------------------
 *  Session bootstrapping, secure cookie configuration, session-
 *  token helpers, CSRF helpers, AND (new) database-backed page-
 *  view telemetry.
 *
 *  Function names are UNCHANGED from the previous version —
 *  register-process.php, login-process.php, vote-process.php,
 *  contact-process.php, remember-me.php, and dashboard.php all
 *  call these exact functions and keep working untouched.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

define('RAVENWARP_FORCE_HTTPS', true);

// ------------------------------------------------------------
// Secure session configuration — must run BEFORE session_start().
// ------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {

    $isSecureConnection = RAVENWARP_FORCE_HTTPS
        && (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isSecureConnection,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_name('ravenwarp_session');
    session_start();
}

// ------------------------------------------------------------
// Session fixation protection — rotate ID periodically.
// ------------------------------------------------------------
function rw_regenerate_session_if_stale(int $intervalSeconds = 900): void
{
    if (!isset($_SESSION['rw_last_regen'])) {
        $_SESSION['rw_last_regen'] = time();
        return;
    }
    if (time() - $_SESSION['rw_last_regen'] > $intervalSeconds) {
        session_regenerate_id(true);
        $_SESSION['rw_last_regen'] = time();
    }
}
rw_regenerate_session_if_stale();

// ------------------------------------------------------------
// Lightweight session fingerprint binding.
// ------------------------------------------------------------
function rw_fingerprint(): string
{
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $ipOctets = explode('.', $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    $ipPrefix = implode('.', array_slice($ipOctets, 0, 2));
    return hash('sha256', $ua . '|' . $ipPrefix);
}

function rw_validate_fingerprint(): bool
{
    if (!isset($_SESSION['rw_fingerprint'])) {
        $_SESSION['rw_fingerprint'] = rw_fingerprint();
        return true;
    }
    return hash_equals($_SESSION['rw_fingerprint'], rw_fingerprint());
}

if (!rw_validate_fingerprint()) {
    rw_destroy_session();
}

// ------------------------------------------------------------
// Public session helpers (unchanged API).
// ------------------------------------------------------------
function rw_set_user_session(int $userId, string $username, string $role = 'member'): void
{
    session_regenerate_id(true);

    $_SESSION['user_id']       = $userId;
    $_SESSION['username']      = $username;
    $_SESSION['role']          = $role;
    $_SESSION['logged_in']     = true;
    $_SESSION['login_time']    = time();
    $_SESSION['rw_last_regen'] = time();
    $_SESSION['rw_fingerprint'] = rw_fingerprint();
}

function rw_destroy_session(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

function rw_is_logged_in(): bool
{
    return !empty($_SESSION['logged_in']) && !empty($_SESSION['user_id']);
}

function rw_current_user_id(): ?int
{
    return rw_is_logged_in() ? (int) $_SESSION['user_id'] : null;
}

function rw_current_role(): string
{
    return $_SESSION['role'] ?? 'guest';
}

function rw_require_role(string $requiredRole): void
{
    if (!rw_is_logged_in() || rw_current_role() !== $requiredRole) {
        http_response_code(403);
        die('Access denied.');
    }
}

// ------------------------------------------------------------
// CSRF helpers (unchanged API).
// ------------------------------------------------------------
function rw_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function rw_validate_csrf(?string $submittedToken): bool
{
    return !empty($submittedToken)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $submittedToken);
}

// ------------------------------------------------------------
// NEW: database-backed telemetry.
// Logged here (rather than in header.php) so it fires on EVERY
// page that bootstraps a session — including any future page
// that doesn't happen to include header.php.
// ------------------------------------------------------------
require_once __DIR__ . '/telemetry.php';
rw_log_pageview();