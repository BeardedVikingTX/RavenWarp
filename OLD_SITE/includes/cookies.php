<?php
/**
 * ============================================================
 *  RavenWarp :: includes/cookies.php
 * ------------------------------------------------------------
 *  Handles session bootstrapping, secure cookie configuration,
 *  and session-token helper functions.
 *
 *  This file MUST be required before any output is sent to the
 *  browser (no whitespace/echo before it in header.php), since
 *  session_start() and setcookie() both need to fire before
 *  headers are sent.
 * ============================================================
 */

// Prevent direct access — this file should only ever be pulled
// in via header.php or another trusted include.
if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

/**
 * ------------------------------------------------------------
 *  Environment flag
 * ------------------------------------------------------------
 *  Namecheap shared hosting serves over HTTPS once your SSL is
 *  active. Flip this to true only once your domain is confirmed
 *  running on https:// — while it's false, the "secure" cookie
 *  flag is skipped so local/staging testing over http doesn't
 *  silently break your session.
 */
define('RAVENWARP_FORCE_HTTPS', true);

/**
 * ------------------------------------------------------------
 *  Secure session configuration
 * ------------------------------------------------------------
 *  These must be set BEFORE session_start() is called.
 */
if (session_status() === PHP_SESSION_NONE) {

    $isSecureConnection = RAVENWARP_FORCE_HTTPS
        && (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    session_set_cookie_params([
        'lifetime' => 0,              // Expires when the browser closes
        'path'     => '/',
        'domain'   => '',             // Current domain only
        'secure'   => $isSecureConnection,
        'httponly' => true,           // JavaScript can NEVER read this cookie
        'samesite' => 'Lax',          // CSRF hardening while still allowing normal nav
    ]);

    session_name('ravenwarp_session');
    session_start();
}

/**
 * ------------------------------------------------------------
 *  Session Fixation Protection
 * ------------------------------------------------------------
 *  Rotate the session ID periodically so a stolen/guessed
 *  session ID has a short shelf life. This does NOT log the
 *  user out — it just swaps the token underneath them.
 */
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

/**
 * ------------------------------------------------------------
 *  Session Fingerprint Binding
 * ------------------------------------------------------------
 *  Ties the session lightly to the browser/IP combo that
 *  created it, to make simple session-token theft/replay
 *  noisier to pull off. This is a deterrent layer, not a
 *  silver bullet — pair it with HTTPS + HttpOnly + short
 *  session lifetimes.
 */
function rw_fingerprint(): string
{
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $ipOctets = explode('.', $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    $ipPrefix = implode('.', array_slice($ipOctets, 0, 2)); // tolerate minor IP shifts (mobile networks)
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
    // Fingerprint mismatch — treat as a hijack attempt and nuke the session.
    rw_destroy_session();
}

/**
 * ------------------------------------------------------------
 *  Public helper functions
 * ------------------------------------------------------------
 */

/**
 * Call this immediately after a successful login to establish
 * an authenticated session token for the user.
 *
 * NOTE: Only store the user ID and lightweight display data in
 * the session. Never store passwords, password hashes, or raw
 * encrypted PII here — pull that fresh from the database when
 * you actually need it.
 */
function rw_set_user_session(int $userId, string $username, string $role = 'member'): void
{
    session_regenerate_id(true); // fresh token on every login — prevents fixation

    $_SESSION['user_id']       = $userId;
    $_SESSION['username']      = $username;
    $_SESSION['role']          = $role;
    $_SESSION['logged_in']     = true;
    $_SESSION['login_time']    = time();
    $_SESSION['rw_last_regen'] = time();
    $_SESSION['rw_fingerprint'] = rw_fingerprint();
}

/**
 * Fully tears down the session — used for logout, or when a
 * fingerprint mismatch suggests session hijacking.
 */
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

/**
 * Quick boolean check used all over the site templates.
 */
function rw_is_logged_in(): bool
{
    return !empty($_SESSION['logged_in']) && !empty($_SESSION['user_id']);
}

/**
 * Returns the current user's ID, or null if logged out.
 */
function rw_current_user_id(): ?int
{
    return rw_is_logged_in() ? (int) $_SESSION['user_id'] : null;
}

/**
 * Returns the current user's role, defaulting to 'guest'.
 */
function rw_current_role(): string
{
    return $_SESSION['role'] ?? 'guest';
}

/**
 * Simple role gate — use at the top of any admin-only page.
 * Example: rw_require_role('admin');
 */
function rw_require_role(string $requiredRole): void
{
    if (!rw_is_logged_in() || rw_current_role() !== $requiredRole) {
        http_response_code(403);
        die('Access denied.');
    }
}

/**
 * ------------------------------------------------------------
 *  CSRF Token Helpers
 * ------------------------------------------------------------
 *  Generate one token per session and echo it into every form
 *  as a hidden field: <input type="hidden" name="csrf_token"
 *  value="<?= rw_csrf_token() ?>">. Validate it on every POST.
 */
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