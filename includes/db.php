<?php
/**
 * ============================================================
 *  RavenWarp :: includes/db.php
 * ------------------------------------------------------------
 *  Single shared PDO connection, configured for safe defaults:
 *  exceptions on error (never silent failures), real prepared
 *  statements (no emulation, so parameter binding is handled by
 *  MySQL itself — a strong defense against SQL injection).
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

require_once __DIR__ . '/config.php';

/**
 * Returns a shared PDO instance, creating it on first call.
 * Using a function (rather than a bare global) keeps this easy
 * to unit-test and avoids polluting the global namespace.
 */
function rw_db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        RW_DB_HOST,
        RW_DB_NAME,
        RW_DB_CHARSET
    );

    try {
        $pdo = new PDO(RW_DB_USER === '' ? $dsn : $dsn, RW_DB_USER, RW_DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        // Never leak connection details (host, db name, credentials) to the client.
        error_log('RavenWarp DB CONNECTION ERROR: ' . $e->getMessage());
        http_response_code(500);
        die('A server error occurred. Please try again shortly.');
    }

    return $pdo;
}