<?php
/**
 * ============================================================
 *  RavenWarp :: logout.php
 * ------------------------------------------------------------
 *  Destroys the current session AND revokes the Remember Me
 *  token if one exists, so logging out actually logs out —
 *  rather than silently logging the user back in on their next
 *  visit via a lingering cookie.
 * ============================================================
 */

define('RAVENWARP_APP', true);

require_once __DIR__ . '/includes/cookies.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/remember-me.php';

// Revoke the remember-me token (if any) before destroying the session.
if (!empty($_COOKIE[RW_REMEMBER_COOKIE])) {
    $parts = explode(':', $_COOKIE[RW_REMEMBER_COOKIE], 2);
    $selector = $parts[0] ?? null;
    rw_clear_remember_token($selector);
}

rw_destroy_session();

header('Location: /index.php');
exit;