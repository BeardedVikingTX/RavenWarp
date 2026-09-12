<?php
/**
 * ============================================================
 *  RavenWarp :: register.php
 * ------------------------------------------------------------
 *  Registration page. Two tiers, one form:
 *   - Anonymous:    alias + password (+ optional avatar)
 *   - Professional: first/last name, alias, email, password
 *                   (+ optional avatar)
 *  Submits via AJAX to register-process.php.
 * ============================================================
 */

define('RAVENWARP_APP', true);

$pageTitle       = 'Register — RavenWarp';
$pageDescription = 'Create your RavenWarp account — anonymous or professional — and join the flock.';
$pageBodyClass   = 'page-register';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/cookies.php';

// Already logged in? No reason to see the registration form.
if (rw_is_logged_in()) {
    header('Location: /users/dashboard.php');
    exit;
}

$rw_csrf = rw_csrf_token();
?>

<section class="rw-hero" style="padding-block: clamp(3rem, 7vw, 5rem);">
    <div class="rw-floaters" aria-hidden="true">
        <i class="fa-solid fa-user-plus rw-floater rw-floater-1"></i>
        <i class="fa-solid fa-feather-pointed rw-floater rw-ember-glyph rw-floater-2"></i>
    </div>
    <div class="rw-section-inner">
        <span class="rw-hero-eyebrow"><span class="rw-dot"></span> New Signal Detected</span>
        <h1 class="rw-hero-title">Join <span class="rw-highlight">RavenWarp</span></h1>
        <p class="rw-hero-subtitle">
            Choose how much of yourself you want to bring with you. Both paths get the full platform —
            only the depth of your profile changes.
        </p>
    </div>
</section>

<section class="rw-section" id="rw-register">
    <div class="rw-section-inner" style="max-width: 720px;">

        <!-- ============= Tier Toggle ============= -->
        <div class="rw-panel" style="display:flex; gap:0.75rem; padding:0.6rem; margin-bottom:1.5rem;">
            <button type="button" class="btn rw-btn rw-btn-register" id="rwTabAnonymous" style="flex:1;" aria-pressed="true">
                <i class="fa-solid fa-user-secret"></i> Quick Register
            </button>
            <button type="button" class="btn rw-btn rw-btn-login" id="rwTabProfessional" style="flex:1;" aria-pressed="false">
                <i class="fa-solid fa-id-card"></i> Full Registration
            </button>
        </div>

        <form id="rwRegisterForm" class="rw-panel" novalidate enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($rw_csrf, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="account_type" id="rwAccountType" value="anonymous">

            <!-- Honeypot -->
            <div style="position:absolute; left:-9999px;" aria-hidden="true">
                <label for="rwRegWebsite">Leave this field empty</label>
                <input type="text" id="rwRegWebsite" name="website" tabindex="-1" autocomplete="off">
            </div>

            <p id="rwTierDescription" class="rw-section-lede" style="margin-top:0;">
                Just an alias and a password — no email, no real name required. You can always upgrade later.
            </p>

            <!-- ============= Professional-only fields ============= -->
            <div id="rwProfessionalFields" class="row g-3" style="display:none;">
                <div class="col-12 col-md-6">
                    <label for="rwFirstName" class="rw-footer-heading" style="font-size:0.8rem;">First Name</label>
                    <input type="text" id="rwFirstName" name="first_name" class="form-control rw-newsletter-input" maxlength="60">
                </div>
                <div class="col-12 col-md-6">
                    <label for="rwLastName" class="rw-footer-heading" style="font-size:0.8rem;">Last Name</label>
                    <input type="text" id="rwLastName" name="last_name" class="form-control rw-newsletter-input" maxlength="60">
                </div>
                <div class="col-12">
                    <label for="rwEmail" class="rw-footer-heading" style="font-size:0.8rem;">Email</label>
                    <input type="email" id="rwEmail" name="email" class="form-control rw-newsletter-input">
                </div>
            </div>

            <!-- ============= Shared fields ============= -->
            <div class="row g-3" style="margin-top: 0.25rem;">
                <div class="col-12">
                    <label for="rwUsername" class="rw-footer-heading" style="font-size:0.8rem;">Alias / Username</label>
                    <input type="text" id="rwUsername" name="username" class="form-control rw-newsletter-input" required minlength="3" maxlength="50" placeholder="e.g. Bearded Viking">
                    <small style="color:var(--rw-text-faint);">This becomes your profile handle — spaces will be removed automatically.</small>
                </div>
                <div class="col-12 col-md-6">
                    <label for="rwPassword" class="rw-footer-heading" style="font-size:0.8rem;">Password</label>
                    <input type="password" id="rwPassword" name="password" class="form-control rw-newsletter-input" required minlength="8">
                </div>
                <div class="col-12 col-md-6">
                    <label for="rwConfirmPassword" class="rw-footer-heading" style="font-size:0.8rem;">Confirm Password</label>
                    <input type="password" id="rwConfirmPassword" name="confirm_password" class="form-control rw-newsletter-input" required minlength="8">
                </div>
                <div class="col-12">
                    <label for="rwAvatar" class="rw-footer-heading" style="font-size:0.8rem;">Avatar (optional)</label>
                    <input type="file" id="rwAvatar" name="avatar" class="form-control rw-newsletter-input" accept="image/png, image/jpeg, image/webp">
                    <small style="color:var(--rw-text-faint);">JPG, PNG, or WEBP — up to 3MB. You can skip this and add one later.</small>
                </div>
            </div>

            <button type="submit" class="btn rw-btn rw-btn-register rw-btn-lg" id="rwRegisterSubmit" style="margin-top:1.5rem; width:100%;">
                <span class="rw-btn-label">Create Account</span>
                <i class="fa-solid fa-feather-pointed"></i>
            </button>

            <p class="rw-newsletter-status" id="rwRegisterStatus" role="status" aria-live="polite"></p>

            <p style="text-align:center; margin-top:1rem; margin-bottom:0; color:var(--rw-text-muted); font-size:0.9rem;">
                Already have an account? <a href="/login.php">Log in</a>
            </p>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tabAnon = document.getElementById('rwTabAnonymous');
    var tabPro = document.getElementById('rwTabProfessional');
    var accountTypeField = document.getElementById('rwAccountType');
    var proFields = document.getElementById('rwProfessionalFields');
    var tierDesc = document.getElementById('rwTierDescription');
    var proInputs = proFields.querySelectorAll('input');

    function setTier(tier) {
        accountTypeField.value = tier;
        var isPro = tier === 'professional';

        proFields.style.display = isPro ? 'flex' : 'none';
        proInputs.forEach(function (input) { input.required = isPro; });

        tabAnon.setAttribute('aria-pressed', String(!isPro));
        tabPro.setAttribute('aria-pressed', String(isPro));
        tabAnon.classList.toggle('rw-btn-register', !isPro);
        tabAnon.classList.toggle('rw-btn-login', isPro);
        tabPro.classList.toggle('rw-btn-register', isPro);
        tabPro.classList.toggle('rw-btn-login', !isPro);

        tierDesc.textContent = isPro
            ? 'Your full name and email let you recover your account and unlock the complete RavenWarp profile experience.'
            : 'Just an alias and a password — no email, no real name required. You can always upgrade later.';
    }

    tabAnon.addEventListener('click', function () { setTier('anonymous'); });
    tabPro.addEventListener('click', function () { setTier('professional'); });

    var form = document.getElementById('rwRegisterForm');
    var status = document.getElementById('rwRegisterStatus');
    var submitBtn = document.getElementById('rwRegisterSubmit');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        var password = document.getElementById('rwPassword').value;
        var confirmPassword = document.getElementById('rwConfirmPassword').value;
        if (password !== confirmPassword) {
            status.textContent = 'Passwords do not match.';
            status.setAttribute('data-state', 'error');
            return;
        }

        submitBtn.disabled = true;
        status.removeAttribute('data-state');
        status.textContent = 'Creating your account…';

        var formData = new FormData(form);

        fetch('/register-process.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                status.textContent = data.message;
                status.setAttribute('data-state', data.success ? 'success' : 'error');

                if (data.success && data.redirect) {
                    setTimeout(function () { window.location.href = data.redirect; }, 900);
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