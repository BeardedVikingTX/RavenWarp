<?php
/**
 * ============================================================
 *  RavenWarp :: includes/slug.php
 * ------------------------------------------------------------
 *  Converts a user-chosen alias ("Bearded Viking") into a safe
 *  slug ("beardedviking") used for:
 *   - the avatar/banner folder name on disk
 *   - future profile URLs (e.g. /profile.php?u=beardedviking)
 *
 *  Guarantees returned by rw_generate_unique_slug():
 *   - lowercase, spaces stripped, only [a-z0-9_-] survive
 *   - never empty (falls back to "user" + random suffix)
 *   - guaranteed unique against the users table, appending
 *     a numeric suffix (beardedviking2, beardedviking3, ...)
 *     if the base slug is already taken
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

/**
 * Pure string transform — no database access. Safe to reuse
 * anywhere you need a slug preview (e.g. client-side-style
 * validation echoed back from the server).
 */
function rw_slugify(string $alias): string
{
    $slug = strtolower(trim($alias));

    // Strip spaces entirely (per spec: "Bearded Viking" -> "beardedviking")
    $slug = str_replace(' ', '', $slug);

    // Drop anything that isn't a-z, 0-9, underscore, or hyphen
    $slug = preg_replace('/[^a-z0-9_-]/', '', $slug);

    if ($slug === '') {
        $slug = 'user' . bin2hex(random_bytes(3));
    }

    // Filesystem folder names have practical length limits — keep it sane
    return substr($slug, 0, 40);
}

/**
 * Slugifies an alias AND guarantees the result is not already
 * taken in the `users` table, appending 2, 3, 4... until it
 * finds a free one.
 */
function rw_generate_unique_slug(PDO $pdo, string $alias): string
{
    $baseSlug = rw_slugify($alias);
    $slug = $baseSlug;
    $suffix = 2;

    $checkStmt = $pdo->prepare('SELECT id FROM users WHERE slug = :slug LIMIT 1');

    while (true) {
        $checkStmt->execute(['slug' => $slug]);
        if (!$checkStmt->fetch()) {
            return $slug;
        }
        $slug = $baseSlug . $suffix;
        $suffix++;
    }
}