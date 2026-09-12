<?php
/**
 * ============================================================
 *  RavenWarp :: includes/remember-me.php
 * ------------------------------------------------------------
 *  Silently logs a returning visitor back in if they have a
 *  valid "Remember Me" cookie and no active session — included
 *  from header.php, right after cookies.php, on every page.
 *
 *  SECURITY DESIGN (selector/validator pattern):
 *   - The cookie holds "selector:validator".
 *   - `selector` is used to find the row (safe to index/query).
 *   - `validator` is compared against a stored HASH of itself —
 *     the raw validator is never stored, so a database leak
 *     alone can't be used to forge logins.
 *   - Every successful use ROTATES the token (old one deleted,
 *     a fresh selector/validator issued). This means a stolen-
 *     but-unused cookie stops working the moment the legitimate
 *     user's browser uses its copy — a classic and effective
 *     theft-detection side effect of rotation.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

require_once __DIR__ . '/db.php';

const RW_REMEMBER_COOKIE = 'ravenwarp_remember';
const RW_REMEMBER_DAYS = 30;

/**
 * Issues a fresh remember-me token for a user, sets the cookie,
 * and returns nothing — call this right after a successful
 * login (or from the rotation logic below).
 */
function rw_issue_remember_token(int $userId): void
{
    $pdo = rw_db();

    $selector  = bin2hex(random_bytes(12));   // 24 hex chars
    $validator = bin2hex(random_bytes(32));   // 64 hex chars, the actual secret
    $validatorHash = hash('sha256', $validator);
    $expiresAt = date('Y-m-d H:i:s', time() + (RW_REMEMBER_DAYS * 86400));

    $stmt = $pdo->prepare(
        'INSERT INTO auth_tokens (user_id, selector, validator_hash, expires_at)
         VALUES (:user_id, :selector, :validator_hash, :expires_at)'
    );
    $stmt->execute([
        'user_id'        => $userId,
        'selector'       => $selector,
        'validator_hash' => $validatorHash,
        'expires_at'     => $expiresAt,
    ]);

    setcookie(
        RW_REMEMBER_COOKIE,
        $selector . ':' . $validator,
        [
            'expires'  => time() + (RW_REMEMBER_DAYS * 86400),
            'path'     => '/',
            'secure'   => RAVENWARP_FORCE_HTTPS && (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
}

/**
 * Deletes a specific remember-me token (by selector) and clears
 * the cookie — used on logout and when validation fails.
 */
function rw_clear_remember_token(?string $selector = null): void
{
    if ($selector !== null) {
        $pdo = rw_db();
        $stmt = $pdo->prepare('DELETE FROM auth_tokens WHERE selector = :selector');
        $stmt->execute(['selector' => $selector]);
    }

    setcookie(RW_REMEMBER_COOKIE, '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

/**
 * The actual auto-login check. No-op if the user already has an
 * active session or there's no remember-me cookie to check.
 */
function rw_attempt_remember_login(): void
{
    if (rw_is_logged_in() || empty($_COOKIE[RW_REMEMBER_COOKIE])) {
        return;
    }

    $parts = explode(':', $_COOKIE[RW_REMEMBER_COOKIE], 2);
    if (count($parts) !== 2) {
        rw_clear_remember_token();
        return;
    }

    [$selector, $validator] = $parts;

    $pdo = rw_db();
    $stmt = $pdo->prepare(
        'SELECT at.id, at.user_id, at.validator_hash, at.expires_at, u.username, u.role, u.status
         FROM auth_tokens at
         JOIN users u ON u.id = at.user_id
         WHERE at.selector = :selector
         LIMIT 1'
    );
    $stmt->execute(['selector' => $selector]);
    $token = $stmt->fetch();

    if (!$token) {
        rw_clear_remember_token();
        return;
    }

    // Expired — clean up and bail.
    if (strtotime($token['expires_at']) < time()) {
        rw_clear_remember_token($selector);
        return;
    }

    // Validator mismatch — possible theft/tampering. Revoke and bail.
    if (!hash_equals($token['validator_hash'], hash('sha256', $validator))) {
        rw_clear_remember_token($selector);
        return;
    }

    if ($token['status'] !== 'active') {
        rw_clear_remember_token($selector);
        return;
    }

    // Valid — log the user in, then ROTATE the token (delete old, issue new).
    rw_set_user_session((int) $token['user_id'], $token['username'], $token['role']);

    $deleteStmt = $pdo->prepare('DELETE FROM auth_tokens WHERE id = :id');
    $deleteStmt->execute(['id' => $token['id']]);

    rw_issue_remember_token((int) $token['user_id']);
}

rw_attempt_remember_login();