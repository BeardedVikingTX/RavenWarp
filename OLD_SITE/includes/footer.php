<?php
/**
 * ============================================================
 *  RavenWarp :: includes/footer.php
 * ------------------------------------------------------------
 *  Closes out the page shell opened in header.php. Required at
 *  the bottom of every page, AFTER the page's own content:
 *
 *      <?php require_once __DIR__ . '/includes/footer.php'; ?>
 *
 *  Responsible for:
 *   - Closing the <main> tag opened in header.php
 *   - Rendering the multi-column footer (brand, links, social,
 *     newsletter signup, live status ticker)
 *   - Linking sitemap.xml
 *   - Loading Bootstrap's JS bundle + our own functions.js
 *   - Wiring up the AJAX/DOM behavior for the back-to-top
 *     control and the newsletter form
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

$rw_currentYear = date('Y');
?>

</main><!-- /#main-content (opened in header.php) -->

<footer class="rw-footer" role="contentinfo">
    <div class="container-fluid rw-footer-inner">

        <div class="row rw-footer-columns gy-4">

            <!-- ============= Column 1 :: Brand + Blurb ============= -->
            <div class="col-12 col-lg-4 rw-footer-col rw-footer-brand-col">
                <a href="/index.php" class="rw-footer-brand" aria-label="RavenWarp — go to homepage">
                    <i class="fa-solid fa-feather-pointed rw-brand-icon" aria-hidden="true"></i>
                    <span class="rw-brand-text">RavenWarp</span>
                </a>
                <p class="rw-footer-blurb">
                    Two ravens cross the Nine Realms each day and carry back everything they've seen.
                    RavenWarp carries your signal the same way — connect, share, and build your
                    reputation across the network.
                </p>

                <!-- Live status ticker — populated by assets/js/functions.js via rw_initStatusTicker() -->
                <div class="rw-status-ticker" id="rwStatusTicker" data-status="connecting">
                    <span class="rw-status-dot" aria-hidden="true"></span>
                    <span class="rw-status-text" id="rwStatusText">Establishing uplink&hellip;</span>
                </div>
            </div>

            <!-- ============= Column 2 :: Quick Links ============= -->
            <div class="col-6 col-lg-2 rw-footer-col">
                <h2 class="rw-footer-heading">Navigate</h2>
                <ul class="rw-footer-links list-unstyled">
                    <li><a href="/index.php" class="rw-footer-link">Home</a></li>
                    <li><a href="/about.php" class="rw-footer-link">About</a></li>
                    <li><a href="/contact.php" class="rw-footer-link">Contact</a></li>
                    <li><a href="/login.php" class="rw-footer-link">Login</a></li>
                    <li><a href="/register.php" class="rw-footer-link">Register</a></li>
                </ul>
            </div>

            <!-- ============= Column 3 :: Legal / System ============= -->
            <div class="col-6 col-lg-2 rw-footer-col">
                <h2 class="rw-footer-heading">System</h2>
                <ul class="rw-footer-links list-unstyled">
                    <li><a href="/privacy.php" class="rw-footer-link">Privacy Policy</a></li>
                    <li><a href="/terms.php" class="rw-footer-link">Terms of Service</a></li>
                    <li><a href="/security.php" class="rw-footer-link">Security &amp; VDP</a></li>
                    <li><a href="/sitemap.xml" class="rw-footer-link">Sitemap</a></li>
                </ul>
            </div>

            <!-- ============= Column 4 :: Newsletter Signup (AJAX) ============= -->
            <div class="col-12 col-lg-4 rw-footer-col rw-footer-newsletter-col">
                <h2 class="rw-footer-heading">Join the Signal</h2>
                <p class="rw-footer-newsletter-copy">
                    Get build updates from the RavenWarp development log straight to your inbox.
                </p>

                <form id="rwNewsletterForm" class="rw-newsletter-form" novalidate>
                    <div class="rw-newsletter-field">
                        <label for="rwNewsletterEmail" class="visually-hidden">Email address</label>
                        <input
                            type="email"
                            id="rwNewsletterEmail"
                            name="email"
                            class="form-control rw-newsletter-input"
                            placeholder="you@example.com"
                            required
                            autocomplete="email">
                        <button type="submit" class="btn rw-btn rw-btn-subscribe" id="rwNewsletterSubmit">
                            <span class="rw-btn-label">Subscribe</span>
                            <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                        </button>
                    </div>
                    <p class="rw-newsletter-status" id="rwNewsletterStatus" role="status" aria-live="polite"></p>
                </form>

                <!-- ============= Social Icons ============= -->
                <ul class="rw-social-links list-unstyled">
                    <li><a href="#" class="rw-social-link" aria-label="RavenWarp on X"><i class="fa-brands fa-x-twitter"></i></a></li>
                    <li><a href="#" class="rw-social-link" aria-label="RavenWarp on Instagram"><i class="fa-brands fa-instagram"></i></a></li>
                    <li><a href="#" class="rw-social-link" aria-label="RavenWarp on Discord"><i class="fa-brands fa-discord"></i></a></li>
                    <li><a href="https://github.com" class="rw-social-link" aria-label="RavenWarp on GitHub" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-github"></i></a></li>
                </ul>
            </div>

        </div>

        <!-- ============= Bottom Bar ============= -->
        <div class="rw-footer-bottom">
            <p class="rw-footer-copyright">
                &copy; <?= htmlspecialchars($rw_currentYear, ENT_QUOTES, 'UTF-8') ?> RavenWarp. All realms reserved.
            </p>
            <p class="rw-footer-tagline">Built by an Irish Viking with a love for the stars.</p>
        </div>

    </div>

    <!-- ============= Back To Top Control ============= -->
    <button type="button" id="rwBackToTop" class="rw-back-to-top" aria-label="Scroll back to top">
        <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
    </button>
</footer>

<!-- ================= Scripts ================= -->
<!-- Bootstrap 5 JS bundle (includes Popper — required for the account dropdown) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- RavenWarp custom JS (AJAX handlers, DOM behavior, status ticker) -->
<script src="/assets/js/functions.js"></script>

<!-- ================= Inline bootstrap-behavior for this footer ================= -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ---------- Back-to-top button: show/hide on scroll ---------- */
        var backToTopBtn = document.getElementById('rwBackToTop');
        if (backToTopBtn) {
            window.addEventListener('scroll', function () {
                var shouldShow = window.scrollY > 400;
                backToTopBtn.classList.toggle('rw-visible', shouldShow);
            }, { passive: true });

            backToTopBtn.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        /* ---------- Newsletter form: AJAX submit, no page reload ---------- */
        var newsletterForm = document.getElementById('rwNewsletterForm');
        if (newsletterForm && typeof rw_handleNewsletterSubmit === 'function') {
            newsletterForm.addEventListener('submit', rw_handleNewsletterSubmit);
        }

        /* ---------- Live status ticker ---------- */
        if (typeof rw_initStatusTicker === 'function') {
            rw_initStatusTicker();
        }
    });
</script>

</body>
</html>