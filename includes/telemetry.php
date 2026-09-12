<?php
/**
 * ============================================================
 *  RavenWarp :: includes/telemetry.php
 * ------------------------------------------------------------
 *  Logs one row per page view to `telemetry_logs`, so traffic
 *  patterns and bot/crawler activity are visible from the
 *  database (for the admin panel) rather than a flat file that
 *  a compromised server could dump wholesale.
 *
 *  PRIVACY: only a hash of the IP is ever stored — see
 *  rw_lookup_hash() in encryption.php. Never the raw address.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/encryption.php';

/**
 * Coarse, deliberately simple bot/crawler detection — good
 * enough to categorize traffic for analytics, not intended as
 * a security control (never gate access based on this alone).
 */
function rw_categorize_user_agent(string $ua): string
{
    if ($ua === '') {
        return 'Unknown';
    }

    $botPattern = '/bot|crawl|spider|slurp|claudebot|gptbot|chatgpt-user|ccbot|bingpreview|facebookexternalhit|perplexitybot|anthropic-ai|ai2bot/i';
    if (preg_match($botPattern, $ua)) {
        return 'Bot/Crawler';
    }

    if (preg_match('/mobile|android|iphone|ipad|ipod/i', $ua)) {
        return 'Mobile Browser';
    }

    if (preg_match('/mozilla|chrome|safari|firefox|edg/i', $ua)) {
        return 'Standard Browser';
    }

    return 'Unknown';
}

/**
 * Logs the current request. Fails silently (to the error log
 * only) — a telemetry hiccup should never break a page load.
 */
function rw_log_pageview(): void
{
    $rawUa = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
    $category = rw_categorize_user_agent($rawUa);
    $requestUri = substr((string) ($_SERVER['REQUEST_URI'] ?? '/'), 0, 255);
    $referer = isset($_SERVER['HTTP_REFERER']) ? substr($_SERVER['HTTP_REFERER'], 0, 255) : null;
    $ipHash = rw_lookup_hash($_SERVER['REMOTE_ADDR'] ?? 'unknown');

    try {
        $pdo = rw_db();
        $stmt = $pdo->prepare(
            'INSERT INTO telemetry_logs (ip_hash, user_agent_category, user_agent_raw, request_uri, referer)
             VALUES (:ip_hash, :category, :ua, :uri, :referer)'
        );
        $stmt->execute([
            'ip_hash'  => $ipHash,
            'category' => $category,
            'ua'       => $rawUa,
            'uri'      => $requestUri,
            'referer'  => $referer,
        ]);
    } catch (Throwable $e) {
        error_log('RavenWarp TELEMETRY ERROR: ' . $e->getMessage());
    }
}