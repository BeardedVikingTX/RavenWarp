<?php
/**
 * ============================================================
 *  RavenWarp :: includes/nav.php
 * ------------------------------------------------------------
 *  Primary site navigation. Included by header.php on every
 *  page, so all links are root-relative ("/about.php") — that
 *  way they resolve correctly no matter how deep the current
 *  page lives in the folder structure.
 *
 *  Dynamic behavior:
 *   - Brand name always links back to home.
 *   - If a session is active (rw_is_logged_in()), the Login /
 *     Register buttons swap for a lightweight account menu.
 *   - Nav items get an "active" class when they match the
 *     current script, for a clear "you are here" state.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

// Determine current page filename so we can flag the active nav link.
$rw_currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');

/**
 * Small helper to echo "active" on the nav-link that matches
 * the page currently being viewed.
 */
function rw_nav_active(string $fileName, string $currentPage): string
{
    return $fileName === $currentPage ? ' active' : '';
}
?>
<header>
    <nav class="navbar navbar-expand-lg rw-navbar" aria-label="Primary navigation">
        <div class="container-fluid rw-navbar-container">

            <!-- ============= Left Side :: Brand ============= -->
            <a class="navbar-brand rw-brand" href="/index.php" aria-label="RavenWarp — go to homepage">
                <i class="fa-solid fa-feather-pointed rw-brand-icon" aria-hidden="true"></i>
                <span class="rw-brand-text">RavenWarp</span>
            </a>

            <!-- ============= Mobile Toggler ============= -->
            <button class="navbar-toggler rw-navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#rwNavbarContent"
                    aria-controls="rwNavbarContent" aria-expanded="false"
                    aria-label="Toggle navigation menu">
                <span class="rw-toggler-icon">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                </span>
            </button>

            <!-- ============= Right Side :: Links + Auth ============= -->
            <div class="collapse navbar-collapse" id="rwNavbarContent">

                <ul class="navbar-nav rw-nav-links ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link rw-nav-link<?= rw_nav_active('index.php', $rw_currentPage) ?>"
                           href="/index.php">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rw-nav-link<?= rw_nav_active('about.php', $rw_currentPage) ?>"
                           href="/about.php">
                            About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rw-nav-link<?= rw_nav_active('contact.php', $rw_currentPage) ?>"
                           href="/contact.php">
                            Contact
                        </a>
                    </li>

                    <?php if (function_exists('rw_is_logged_in') && rw_is_logged_in()): ?>
                        <!-- ============= Logged-in state ============= -->
                        <li class="nav-item dropdown rw-account-menu">
                            <a class="nav-link dropdown-toggle rw-nav-link rw-account-toggle"
                               href="#" id="rwAccountDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-user" aria-hidden="true"></i>
                                <span class="rw-account-name">
                                    <?= htmlspecialchars($_SESSION['username'] ?? 'Account', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end rw-dropdown-menu" aria-labelledby="rwAccountDropdown">
                                <li><a class="dropdown-item rw-dropdown-item" href="/profile.php"><i class="fa-solid fa-id-badge"></i> Profile</a></li>
                                <li><a class="dropdown-item rw-dropdown-item" href="/messages.php"><i class="fa-solid fa-message"></i> Messages</a></li>
                                <li><a class="dropdown-item rw-dropdown-item" href="/settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
                                <?php if (function_exists('rw_current_role') && rw_current_role() === 'admin'): ?>
                                <li><a class="dropdown-item rw-dropdown-item" href="/admin/index.php"><i class="fa-solid fa-satellite-dish"></i> Admin Panel</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider rw-dropdown-divider"></li>
                                <li><a class="dropdown-item rw-dropdown-item" href="/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- ============= Logged-out state ============= -->
                        <li class="nav-item">
                            <a class="btn rw-btn rw-btn-login<?= rw_nav_active('login.php', $rw_currentPage) ?>"
                               href="/login.php">
                                Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn rw-btn rw-btn-register<?= rw_nav_active('register.php', $rw_currentPage) ?>"
                               href="/register.php">
                                Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

            </div>
        </div>
    </nav>
</header>