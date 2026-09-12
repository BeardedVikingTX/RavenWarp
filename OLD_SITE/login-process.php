<?php
/**
 * ============================================================
 *  RavenWarp :: login-process.php
 * ------------------------------------------------------------
 *  AJAX-only login backend. Accepts either a username or an
 *  email address in the same "identifier" field.
 *
 *  SECURITY NOTES:
 *   - Invalid username/email and invalid password return the
 *     exact same generic error message, so a login attempt
 *     can't be used to enumerate which accounts/emails exist.
 *   - Failed attempts are tracked PER ACCOUNT in the database
 *     (not just per-session), so an attacker can't dodge
 *     lockout by simply clearing cookies. Five failed attempts
 *     locks the account for 15 minutes.
 * ============================================================
 */

define('RAVENWARP_APP', true);

require_once __DIR__ . '/includes/cookies.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/encryption.php';
require_once __DIR__ . '/includes/remember-me.php';

header('Content-Type: application/json; charset=utf-8');

const RW_MAX_LOGIN_ATTEMPTS = 5;
const RW_LOCKOUT_MINUTES = 15;

function rw_login_respond(bool $success, string $message, array $extra = [], int $httpStatus = 200): never
{
    http_response_code($httpStatus);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

const RW_GENERIC_LOGIN_ERROR = 'That username/email or password isn\'t right. Please try again.';

// ------------------------------------------------------------
// 1. Method + CSRF + honeypot
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rw_login_respond(false, 'Invalid request method.', [], 405);
}

if (!empty($_POST['website'] ?? '')) {
    rw_login_respond(false, RW_GENERIC_LOGIN_ERROR, [], 422);
}

if (!rw_validate_csrf($_POST['csrf_token'] ?? null)) {
    rw_login_respond(false, 'Your session expired — please refresh the page and try again.', [], 419);
}

// ------------------------------------------------------------
// 2. Input
// ------------------------------------------------------------
$identifier = trim((string) ($_POST['identifier'] ?? ''));
$password   = (string) ($_POST['password'] ?? '');
$rememberMe = !empty($_POST['remember_me'] ?? '');

if ($identifier === '' || $password === '') {
    rw_login_respond(false, RW_GENERIC_LOGIN_ERROR, [], 422);
}

$pdo = rw_db();

// ------------------------------------------------------------
// 3. Look the account up — by email if it looks like one,
//    otherwise by username.
// ------------------------------------------------------------
if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
    $stmt = $pdo->prepare(
        'SELECT id, username, password_hash, role, status, failed_login_attempts, locked_until
         FROM users WHERE email_hash = :hash LIMIT 1'
    );
    $stmt->execute(['hash' => rw_lookup_hash($identifier)]);
} else {
    $stmt = $pdo->prepare(
        'SELECT id, username, password_hash, role, status, failed_login_attempts, locked_until
         FROM users WHERE username = :username LIMIT 1'
    );
    $stmt->execute(['username' => $identifier]);
}
$user = $stmt->fetch();

if (!$user) {
    // Deliberately generic — don't reveal that this identifier doesn't exist.
    rw_login_respond(false, RW_GENERIC_LOGIN_ERROR, [], 401);
}

// ------------------------------------------------------------
// 4. Lockout check
// ------------------------------------------------------------
if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
    $minutesLeft = (int) ceil((strtotime($user['locked_until']) - time()) / 60);
    rw_login_respond(false, "Too many failed attempts. Please try again in about {$minutesLeft} minute(s).", [], 423);
}

if ($user['status'] !== 'active') {
    rw_login_respond(false, 'This account is not currently active. Please contact support.', [], 403);
}

// ------------------------------------------------------------
// 5. Verify password
// ------------------------------------------------------------
if (!password_verify($password, $user['password_hash'])) {
    $attempts = (int) $user['failed_login_attempts'] + 1;

    if ($attempts >= RW_MAX_LOGIN_ATTEMPTS) {
        $lockUntil = date('Y-m-d H:i:s', time() + (RW_LOCKOUT_MINUTES * 60));
        $updateStmt = $pdo->prepare('UPDATE users SET failed_login_attempts = :attempts, locked_until = :locked_until WHERE id = :id');
        $updateStmt->execute(['attempts' => $attempts, 'locked_until' => $lockUntil, 'id' => $user['id']]);
        rw_login_respond(false, 'Too many failed attempts. This account is locked for ' . RW_LOCKOUT_MINUTES . ' minutes.', [], 423);
    }

    $updateStmt = $pdo->prepare('UPDATE users SET failed_login_attempts = :attempts WHERE id = :id');
    $updateStmt->execute(['attempts' => $attempts, 'id' => $user['id']]);

    rw_login_respond(false, RW_GENERIC_LOGIN_ERROR, [], 401);
}

// ------------------------------------------------------------
// 6. Success — reset lockout counters, update last login
// ------------------------------------------------------------
$updateStmt = $pdo->prepare(
    'UPDATE users
     SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW()
     WHERE id = :id'
);
$updateStmt->execute(['id' => $user['id']]);

rw_set_user_session((int) $user['id'], $user['username'], $user['role']);

if ($rememberMe) {
    rw_issue_remember_token((int) $user['id']);
}

rw_login_respond(true, "Welcome back, {$user['username']}!", [
    'redirect' => '/users/dashboard.php',
]);