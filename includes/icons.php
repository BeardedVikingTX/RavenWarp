<?php
/**
 * ============================================================
 *  RavenWarp :: includes/icons.php
 * ------------------------------------------------------------
 *  Renders a Lucide icon as INLINE SVG (not an <img> or webfont
 *  glyph). Inlining means the icon inherits `color: currentColor`
 *  and can be styled/animated with plain CSS, exactly like the
 *  Font Awesome classes it replaces — just lighter and sharper.
 *
 *  Expects Lucide's static package layout:
 *      assets/vendors/Lucide/icons/<name>.svg
 *  If your download used a different folder structure, update
 *  RW_LUCIDE_PATH below to match.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

const RW_LUCIDE_PATH = __DIR__ . '/../assets/vendors/Lucide/icons/';

/**
 * Outputs (echoes) an inline Lucide SVG icon.
 *
 * @param string $name       Icon name, e.g. "feather", "shield", "user-plus" (matches Lucide's kebab-case filenames).
 * @param string $extraClass Additional CSS class(es) to add, e.g. "rw-icon-lg".
 */
function rw_icon(string $name, string $extraClass = ''): void
{
    static $cache = [];

    $safeName = preg_replace('/[^a-z0-9-]/', '', strtolower($name));

    if (!isset($cache[$safeName])) {
        $path = RW_LUCIDE_PATH . $safeName . '.svg';
        $cache[$safeName] = is_file($path) ? file_get_contents($path) : null;
    }

    $svg = $cache[$safeName];

    if ($svg === null) {
        // Fails visibly-but-quietly in markup rather than breaking the page —
        // an empty inline comment is easy to spot in dev tools if an icon name is wrong.
        echo '<!-- rw_icon: missing icon "' . htmlspecialchars($safeName, ENT_QUOTES, 'UTF-8') . '" -->';
        return;
    }

    $classAttr = 'rw-icon' . ($extraClass !== '' ? ' ' . htmlspecialchars($extraClass, ENT_QUOTES, 'UTF-8') : '');

    // Inject our class + aria-hidden onto Lucide's root <svg> tag. Lucide's raw
    // SVGs already ship width/height/stroke attributes we want to keep.
    $svg = preg_replace('/<svg /', '<svg class="' . $classAttr . '" aria-hidden="true" ', $svg, 1);

    echo $svg;
}

/**
 * Returns the icon as a string instead of echoing — useful inside
 * attribute contexts or when building strings (e.g. email HTML).
 */
function rw_icon_string(string $name, string $extraClass = ''): string
{
    ob_start();
    rw_icon($name, $extraClass);
    return ob_get_clean();
}