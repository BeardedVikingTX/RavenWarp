<?php
/**
 * ============================================================
 *  RavenWarp :: contact.php
 * ------------------------------------------------------------
 *  Contact page: AJAX-driven contact form (see contact-process.php),
 *  social media directory, and dual HQ location cards.
 *  Includes JSON-LD structured data for SEO/rich results.
 * ============================================================
 */

define('RAVENWARP_APP', true);

$pageTitle       = 'Contact — RavenWarp';
$pageDescription = 'Get in touch with the RavenWarp team. Reach out about the platform, the AI build-off experiment, press, or partnerships — HQ\'d across Texas and Illinois.';
$pageBodyClass   = 'page-contact';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/cookies.php';

$rw_csrf = rw_csrf_token();

/**
 * Social directory — single source of truth for both the visible
 * grid and (if you add more later) any future footer/API use.
 */
$rw_social_links = [
    ['label' => 'Medium',   'icon' => 'fa-brands fa-medium',    'url' => 'https://beardedviking.medium.com/'],
    ['label' => 'LinkedIn', 'icon' => 'fa-brands fa-linkedin',  'url' => 'https://www.linkedin.com/in/bearded-viking-3112a8431/'],
    ['label' => 'Facebook', 'icon' => 'fa-brands fa-facebook',  'url' => 'https://www.facebook.com/BeardedVikingTX'],
    ['label' => 'X (Twitter)', 'icon' => 'fa-brands fa-x-twitter', 'url' => 'https://x.com/TXBeardedViking'],
    ['label' => 'TikTok',   'icon' => 'fa-brands fa-tiktok',    'url' => 'https://www.tiktok.com/@beardedvikingtx'],
    ['label' => 'GitHub',   'icon' => 'fa-brands fa-github',    'url' => 'https://github.com/BeardedVikingTX'],
];

/**
 * JSON-LD structured data — helps search engines understand this
 * as an Organization contact point with real social profiles and
 * multiple physical locations, which can surface as rich results.
 */
$rw_structured_data = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => 'RavenWarp',
    'url'      => 'https://ravenwarp.beardedviking.org',
    'sameAs'   => array_column($rw_social_links, 'url'),
    'contactPoint' => [[
        '@type'       => 'ContactPoint',
        'email'       => 'info@beardedviking.org',
        'contactType' => 'customer support',
    ]],
    'location' => [
        [
            '@type' => 'Place',
            'address' => ['@type' => 'PostalAddress', 'addressRegion' => 'TX', 'addressCountry' => 'US'],
        ],
        [
            '@type' => 'Place',
            'address' => ['@type' => 'PostalAddress', 'addressRegion' => 'IL', 'addressCountry' => 'US'],
        ],
    ],
];
?>

<script type="application/ld+json"><?= json_encode($rw_structured_data, JSON_UNESCAPED_SLASHES) ?></script>

<!-- ================================================================
     PAGE HEADER
     ================================================================ -->
<section class="rw-hero" style="padding-block: clamp(3rem, 7vw, 5rem);">
    <div class="rw-floaters" aria-hidden="true">
        <i class="fa-solid fa-satellite-dish rw-floater rw-floater-1"></i>
        <i class="fa-solid fa-tower-broadcast rw-floater rw-ember-glyph rw-floater-2"></i>
        <i class="fa-solid fa-envelope rw-floater rw-floater-3"></i>
    </div>
    <div class="rw-section-inner">
        <span class="rw-hero-eyebrow"><span class="rw-dot"></span> Open Channel</span>
        <h1 class="rw-hero-title">Get in <span class="rw-highlight">Touch</span></h1>
        <p class="rw-hero-subtitle">
            Questions about RavenWarp, the AI build-off experiment, press inquiries, partnership ideas, or
            just want to say hey &mdash; the signal goes straight through. Every message reaches a real
            person, not a queue.
        </p>
    </div>
</section>

<!-- ================================================================
     WHY REACH OUT
     ================================================================ -->
<section class="rw-section" id="rw-why-contact">
    <div class="rw-section-inner">
        <div class="rw-section-header">
            <span class="rw-section-eyebrow">Transmission 01</span>
            <h2 class="rw-section-title">What This Channel Is For</h2>
        </div>
        <p class="rw-section-lede">
            Whether you're a journalist covering the AI build-off experiment, a fellow developer curious
            about the architecture behind RavenWarp, a security researcher with a responsible disclosure
            to report ahead of the formal VDP launch, or a future user with a feature request &mdash; this
            form routes directly to the team behind the project. There's no support-ticket bureaucracy
            here, just a straightforward line of communication.
        </p>
        <p class="rw-section-lede">
            For general inquiries, all messages sent through this page are delivered to
            <strong>info@beardedviking.org</strong>. If you'd rather reach out through a specific platform,
            the full social directory is listed below &mdash; pick whichever channel you're most comfortable
            with.
        </p>
    </div>
</section>

<!-- ================================================================
     CONTACT FORM + HQ / SOCIAL SIDEBAR
     ================================================================ -->
<section class="rw-section rw-section-alt" id="rw-contact-main">
    <div class="rw-section-inner">
        <div class="row g-4">

            <!-- ============= Contact Form ============= -->
            <div class="col-12 col-lg-7">
                <form id="rwContactForm" class="rw-panel" novalidate>
                    <h2 class="rw-section-title" style="font-size:1.6rem; margin-top:0;">Send a Message</h2>

                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($rw_csrf, ENT_QUOTES, 'UTF-8') ?>">

                    <!-- Honeypot -->
                    <div style="position:absolute; left:-9999px;" aria-hidden="true">
                        <label for="rwContactWebsite">Leave this field empty</label>
                        <input type="text" id="rwContactWebsite" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="rwSenderName" class="rw-footer-heading" style="font-size:0.8rem;">Name</label>
                            <input type="text" id="rwSenderName" name="sender_name" class="form-control rw-newsletter-input" required maxlength="100">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="rwSenderEmail" class="rw-footer-heading" style="font-size:0.8rem;">Email</label>
                            <input type="email" id="rwSenderEmail" name="sender_email" class="form-control rw-newsletter-input" required>
                        </div>
                        <div class="col-12">
                            <label for="rwSubject" class="rw-footer-heading" style="font-size:0.8rem;">Subject</label>
                            <input type="text" id="rwSubject" name="subject" class="form-control rw-newsletter-input" required maxlength="150">
                        </div>
                        <div class="col-12">
                            <label for="rwMessage" class="rw-footer-heading" style="font-size:0.8rem;">Message</label>
                            <textarea id="rwMessage" name="message" class="form-control rw-newsletter-input" rows="6" required maxlength="5000"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn rw-btn rw-btn-register rw-btn-lg" id="rwContactSubmit" style="margin-top:1.25rem;">
                        <span class="rw-btn-label">Send Message</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>

                    <p class="rw-newsletter-status" id="rwContactStatus" role="status" aria-live="polite"></p>
                </form>
            </div>

            <!-- ============= HQ + Social Sidebar ============= -->
            <div class="col-12 col-lg-5">

                <div class="rw-panel" style="margin-bottom:1.5rem;">
                    <h2 class="rw-footer-heading" style="font-size:0.9rem;">Headquarters</h2>
                    <div style="display:flex; gap:1rem; margin-bottom:0.9rem;">
                        <i class="fa-solid fa-location-dot" style="color:var(--rw-frost); margin-top:0.2rem;"></i>
                        <div>
                            <strong style="font-family:var(--rw-font-nav);">Texas, USA</strong>
                            <p style="margin:0; color:var(--rw-text-muted); font-size:0.92rem;">Primary operations base.</p>
                        </div>
                    </div>
                    <div style="display:flex; gap:1rem;">
                        <i class="fa-solid fa-location-dot" style="color:var(--rw-ember); margin-top:0.2rem;"></i>
                        <div>
                            <strong style="font-family:var(--rw-font-nav);">Illinois, USA</strong>
                            <p style="margin:0; color:var(--rw-text-muted); font-size:0.92rem;">Secondary operations base.</p>
                        </div>
                    </div>
                </div>

                <div class="rw-panel">
                    <h2 class="rw-footer-heading" style="font-size:0.9rem;">Find Us Elsewhere</h2>
                    <ul class="rw-footer-links list-unstyled" style="margin:0;">
                        <?php foreach ($rw_social_links as $rw_social): ?>
                            <li>
                                <a href="<?= htmlspecialchars($rw_social['url'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="rw-footer-link"
                                   target="_blank" rel="noopener noreferrer"
                                   style="display:flex; align-items:center; gap:0.6rem;">
                                    <i class="<?= htmlspecialchars($rw_social['icon'], ENT_QUOTES, 'UTF-8') ?>" style="width:1.1em;"></i>
                                    <?= htmlspecialchars($rw_social['label'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var contactForm = document.getElementById('rwContactForm');
    var contactStatus = document.getElementById('rwContactStatus');
    var submitBtn = document.getElementById('rwContactSubmit');

    if (!contactForm) return;

    contactForm.addEventListener('submit', function (event) {
        event.preventDefault();

        submitBtn.disabled = true;
        contactStatus.removeAttribute('data-state');
        contactStatus.textContent = 'Sending your message…';

        var formData = new FormData(contactForm);

        fetch('/contact-process.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                contactStatus.textContent = data.message;
                contactStatus.setAttribute('data-state', data.success ? 'success' : 'error');
                submitBtn.disabled = false;
                if (data.success) {
                    contactForm.reset();
                }
            })
            .catch(function () {
                contactStatus.textContent = 'Something went wrong sending your message. Please try again.';
                contactStatus.setAttribute('data-state', 'error');
                submitBtn.disabled = false;
            });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>