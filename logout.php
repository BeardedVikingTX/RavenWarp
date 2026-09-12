<?php
/**
 * ============================================================
 *  RavenWarp :: logout.php
 * ============================================================
 */

define('RAVENWARP_APP', true);

require_once __DIR__ . '/includes/cookies.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/remember-me.php';

if (!empty($_COOKIE[RW_REMEMBER_COOKIE])) {
    $parts = explode(':', $_COOKIE[RW_REMEMBER_COOKIE], 2);
    $selector = $parts[0] ?? null;
    rw_clear_remember_token($selector);
}

rw_destroy_session();

header('Location: /index.php');
exit;