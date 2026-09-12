<?php
/**
 * ============================================================
 *  RavenWarp :: login-process.php  (Command Deck rebuild)
 * ------------------------------------------------------------
 *  Accepts username OR email in one field. Wrong identifier and
 *  wrong password return the SAME generic error (no account
 *  enumeration). Failed attempts are tracked per-account in the
 *  database, not per-session, so lockout can't be dodged.
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
const RW_GENERIC_LOGIN_ERROR = 'That username/email or password isn\'t right. Please try again.';

function rw_login_respond(bool $success, string $message, array $extra = [], int $httpStatus = 200): never
{
    http_response_code($httpStatus);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rw_login_respond(false, 'Invalid request method.', [], 405);
}
if (!empty($_POST['website'] ?? '')) {
    rw_login_respond(false, RW_GENERIC_LOGIN_ERROR, [], 422);
}
if (!rw_validate_csrf($_POST['csrf_token'] ?? null)) {
    rw_login_respond(false, 'Your session expired — please refresh the page and try again.', [], 419);
}

$identifier = trim((string) ($_POST['identifier'] ?? ''));
$password   = (string) ($_POST['password'] ?? '');
$rememberMe = !empty($_POST['remember_me'] ?? '');

if ($identifier === '' || $password === '') {
    rw_login_respond(false, RW_GENERIC_LOGIN_ERROR, [], 422);
}

$pdo = rw_db();

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
    rw_login_respond(false, RW_GENERIC_LOGIN_ERROR, [], 401);
}

if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
    $minutesLeft = (int) ceil((strtotime($user['locked_until']) - time()) / 60);
    rw_login_respond(false, "Too many failed attempts. Please try again in about {$minutesLeft} minute(s).", [], 423);
}
if ($user['status'] !== 'active') {
    rw_login_respond(false, 'This account is not currently active. Please contact support.', [], 403);
}

if (!password_verify($password, $user['password_hash'])) {
    $attempts = (int) $user['failed_login_attempts'] + 1;

    if ($attempts >= RW_MAX_LOGIN_ATTEMPTS) {
        $lockUntil = date('Y-m-d H:i:s', time() + (RW_LOCKOUT_MINUTES * 60));
        $pdo->prepare('UPDATE users SET failed_login_attempts = :attempts, locked_until = :locked_until WHERE id = :id')
            ->execute(['attempts' => $attempts, 'locked_until' => $lockUntil, 'id' => $user['id']]);
        rw_login_respond(false, 'Too many failed attempts. This account is locked for ' . RW_LOCKOUT_MINUTES . ' minutes.', [], 423);
    }

    $pdo->prepare('UPDATE users SET failed_login_attempts = :attempts WHERE id = :id')
        ->execute(['attempts' => $attempts, 'id' => $user['id']]);

    rw_login_respond(false, RW_GENERIC_LOGIN_ERROR, [], 401);
}

$pdo->prepare('UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE id = :id')
    ->execute(['id' => $user['id']]);

rw_set_user_session((int) $user['id'], $user['username'], $user['role']);

if ($rememberMe) {
    rw_issue_remember_token((int) $user['id']);
}

rw_login_respond(true, "Welcome back, {$user['username']}!", ['redirect' => '/users/dashboard.php']);