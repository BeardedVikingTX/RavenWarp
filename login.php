<?php
/**
 * ============================================================
 *  RavenWarp :: login.php
 * ============================================================
 */

define('RAVENWARP_APP', true);

$pageTitle       = 'Login — RavenWarp';
$pageDescription = 'Log in to your RavenWarp account.';
$pageBodyClass   = 'page-login';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/cookies.php';

if (rw_is_logged_in()) {
    header('Location: /users/dashboard.php');
    exit;
}

$rw_csrf = rw_csrf_token();
?>

<section class="rw-hero" style="padding-block: clamp(3rem, 7vw, 5rem);">
    <div class="rw-floaters" aria-hidden="true">
        <i class="fa-solid fa-tower-broadcast rw-floater rw-floater-1"></i>
        <i class="fa-solid fa-feather-pointed rw-floater rw-ember-glyph rw-floater-2"></i>
    </div>
    <div class="rw-section-inner">
        <span class="rw-hero-eyebrow"><span class="rw-dot"></span> Reopening the Signal</span>
        <h1 class="rw-hero-title">Welcome <span class="rw-highlight">Back</span></h1>
        <p class="rw-hero-subtitle">Log back in to pick up right where you left off.</p>
    </div>
</section>

<section class="rw-section" id="rw-login">
    <div class="rw-section-inner" style="max-width: 520px;">
        <form id="rwLoginForm" class="rw-panel" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($rw_csrf, ENT_QUOTES, 'UTF-8') ?>">

            <div style="position:absolute; left:-9999px;" aria-hidden="true">
                <label for="rwLoginWebsite">Leave this field empty</label>
                <input type="text" id="rwLoginWebsite" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="rwIdentifier" class="rw-footer-heading" style="font-size:0.8rem;">Username or Email</label>
                <input type="text" id="rwIdentifier" name="identifier" class="form-control rw-newsletter-input" required autocomplete="username">
            </div>

            <div class="mb-2">
                <label for="rwLoginPassword" class="rw-footer-heading" style="font-size:0.8rem;">Password</label>
                <input type="password" id="rwLoginPassword" name="password" class="form-control rw-newsletter-input" required autocomplete="current-password">
            </div>

            <div class="form-check" style="margin-top:0.75rem;">
                <input type="checkbox" class="form-check-input" id="rwRememberMe" name="remember_me" value="1">
                <label class="form-check-label" for="rwRememberMe" style="color:var(--rw-text-muted); font-size:0.9rem;">
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" class="btn rw-btn rw-btn-register rw-btn-lg" id="rwLoginSubmit" style="margin-top:1.5rem; width:100%;">
                <span class="rw-btn-label">Log In</span>
                <i class="fa-solid fa-right-to-bracket"></i>
            </button>

            <p class="rw-newsletter-status" id="rwLoginStatus" role="status" aria-live="polite"></p>

            <p style="text-align:center; margin-top:1rem; margin-bottom:0; color:var(--rw-text-muted); font-size:0.9rem;">
                New here? <a href="/register.php">Create an account</a>
            </p>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('rwLoginForm');
    var status = document.getElementById('rwLoginStatus');
    var submitBtn = document.getElementById('rwLoginSubmit');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        submitBtn.disabled = true;
        status.removeAttribute('data-state');
        status.textContent = 'Verifying…';

        var formData = new FormData(form);

        fetch('/login-process.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                status.textContent = data.message;
                status.setAttribute('data-state', data.success ? 'success' : 'error');

                if (data.success && data.redirect) {
                    setTimeout(function () { window.location.href = data.redirect; }, 700);
                } else {
                    submitBtn.disabled = false;
                }
            })
            .catch(function () {
                status.textContent = 'Something went wrong. Please try again.';
                status.setAttribute('data-state', 'error');
                submitBtn.disabled = false;
            });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>