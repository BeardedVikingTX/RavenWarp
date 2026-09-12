<?php
/**
 * ============================================================
 *  RavenWarp :: includes/nav.php  (Command Deck rebuild)
 * ------------------------------------------------------------
 *  Same placeholder links as before (Home / About / Contact /
 *  Login|Register), same login-state awareness — now driven by
 *  Alpine.js instead of Bootstrap's JS bundle, so we can drop
 *  bootstrap.bundle.min.js entirely and ship less script.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

$rw_currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');

function rw_nav_active(string $fileName, string $currentPage): string
{
    return $fileName === $currentPage ? ' active' : '';
}
?>
<header>
    <nav class="rw-navbar" aria-label="Primary navigation" x-data="{ accountOpen: false }">
        <div class="rw-navbar-container">

            <!-- ============= Left :: Brand ============= -->
            <a class="rw-brand" href="/index.php" aria-label="RavenWarp — go to homepage">
                <?php rw_icon('feather', 'rw-brand-icon'); ?>
                <span class="rw-brand-text">RavenWarp</span>
            </a>

            <!-- ============= Mobile Toggler ============= -->
            <button type="button" class="rw-navbar-toggler"
                    @click="mobileNavOpen = !mobileNavOpen"
                    :aria-expanded="mobileNavOpen.toString()"
                    aria-controls="rwNavbarContent"
                    aria-label="Toggle navigation menu">
                <span x-show="!mobileNavOpen"><?php rw_icon('menu'); ?></span>
                <span x-show="mobileNavOpen" x-cloak><?php rw_icon('x'); ?></span>
            </button>

            <!-- ============= Right :: Links + Auth ============= -->
            <div class="rw-nav-links-wrap" id="rwNavbarContent"
                 :class="{ 'rw-nav-open': mobileNavOpen }">

                <ul class="rw-nav-links">
                    <li>
                        <a class="rw-nav-link<?= rw_nav_active('index.php', $rw_currentPage) ?>" href="/index.php">Home</a>
                    </li>
                    <li>
                        <a class="rw-nav-link<?= rw_nav_active('about.php', $rw_currentPage) ?>" href="/about.php">About</a>
                    </li>
                    <li>
                        <a class="rw-nav-link<?= rw_nav_active('contact.php', $rw_currentPage) ?>" href="/contact.php">Contact</a>
                    </li>

                    <?php if (function_exists('rw_is_logged_in') && rw_is_logged_in()): ?>
                        <!-- ============= Logged-in state ============= -->
                        <li class="rw-account-menu" @click.outside="accountOpen = false">
                            <button type="button" class="rw-nav-link rw-account-toggle"
                                    @click="accountOpen = !accountOpen"
                                    :aria-expanded="accountOpen.toString()">
                                <?php rw_icon('circle-user-round'); ?>
                                <span><?= htmlspecialchars($_SESSION['username'] ?? 'Account', ENT_QUOTES, 'UTF-8') ?></span>
                            </button>
                            <ul class="rw-dropdown-menu" x-show="accountOpen" x-transition x-cloak>
                                <li><a class="rw-dropdown-item" href="/profile.php"><?php rw_icon('id-card'); ?> Profile</a></li>
                                <li><a class="rw-dropdown-item" href="/messages.php"><?php rw_icon('message-square'); ?> Messages</a></li>
                                <li><a class="rw-dropdown-item" href="/settings.php"><?php rw_icon('settings'); ?> Settings</a></li>
                                <?php if (function_exists('rw_current_role') && rw_current_role() === 'admin'): ?>
                                <li><a class="rw-dropdown-item" href="/admin/index.php"><?php rw_icon('radar'); ?> Admin Panel</a></li>
                                <?php endif; ?>
                                <li><hr class="rw-dropdown-divider"></li>
                                <li><a class="rw-dropdown-item" href="/logout.php"><?php rw_icon('log-out'); ?> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- ============= Logged-out state ============= -->
                        <li>
                            <a class="rw-btn rw-btn-login<?= rw_nav_active('login.php', $rw_currentPage) ?>" href="/login.php">Login</a>
                        </li>
                        <li>
                            <a class="rw-btn rw-btn-register<?= rw_nav_active('register.php', $rw_currentPage) ?>" href="/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>