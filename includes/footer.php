<?php
/**
 * ============================================================
 *  RavenWarp :: includes/footer.php  (Command Deck rebuild)
 * ------------------------------------------------------------
 *  Closes the page shell. Same content pillars as before (brand
 *  blurb + live status, nav links, system/legal links + sitemap,
 *  newsletter signup), now icon'd with Lucide and scripted with
 *  Alpine instead of bespoke inline JS where it makes sense.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

$rw_currentYear = date('Y');
?>

</main><!-- /#main-content -->

<footer class="rw-footer" role="contentinfo">
    <div class="rw-footer-inner">

        <div class="rw-footer-columns">

            <!-- ============= Brand + Status ============= -->
            <div class="rw-footer-col rw-footer-brand-col">
                <a href="/index.php" class="rw-footer-brand">
                    <?php rw_icon('feather'); ?>
                    <span class="rw-brand-text">RavenWarp</span>
                </a>
                <p class="rw-footer-blurb">
                    Two ravens cross the Nine Realms each day and carry back everything they've seen.
                    RavenWarp carries your signal the same way.
                </p>
                <div class="rw-status-ticker" data-status="online">
                    <span class="rw-status-dot"></span>
                    <span class="rw-font-mono">SYSTEMS NOMINAL</span>
                </div>
            </div>

            <!-- ============= Navigate ============= -->
            <div class="rw-footer-col">
                <h2 class="rw-footer-heading">Navigate</h2>
                <ul class="rw-footer-links">
                    <li><a href="/index.php" class="rw-footer-link">Home</a></li>
                    <li><a href="/about.php" class="rw-footer-link">About</a></li>
                    <li><a href="/contact.php" class="rw-footer-link">Contact</a></li>
                    <li><a href="/login.php" class="rw-footer-link">Login</a></li>
                    <li><a href="/register.php" class="rw-footer-link">Register</a></li>
                </ul>
            </div>

            <!-- ============= System ============= -->
            <div class="rw-footer-col">
                <h2 class="rw-footer-heading">System</h2>
                <ul class="rw-footer-links">
                    <li><a href="/privacy.php" class="rw-footer-link">Privacy Policy</a></li>
                    <li><a href="/terms.php" class="rw-footer-link">Terms of Service</a></li>
                    <li><a href="/security.php" class="rw-footer-link">Security &amp; VDP</a></li>
                    <li><a href="/sitemap.xml" class="rw-footer-link">Sitemap</a></li>
                </ul>
            </div>

            <!-- ============= Newsletter + Social ============= -->
            <div class="rw-footer-col rw-footer-newsletter-col"
                 x-data="{ status: '', state: '', sending: false }">
                <h2 class="rw-footer-heading">Join the Signal</h2>
                <p class="rw-footer-newsletter-copy">Build updates from the RavenWarp dev log, straight to your inbox.</p>

                <form class="rw-newsletter-form"
                      @submit.prevent="
                        sending = true; status = 'Subscribing…'; state = '';
                        fetch('/newsletter-process.php', { method: 'POST', body: new FormData($el), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                          .then(r => r.json())
                          .then(d => { status = d.message; state = d.success ? 'success' : 'error'; if (d.success) $el.reset(); })
                          .catch(() => { status = 'Something went wrong. Please try again.'; state = 'error'; })
                          .finally(() => sending = false);
                      ">
                    <div class="rw-newsletter-field">
                        <label for="rwNewsletterEmail" class="visually-hidden">Email address</label>
                        <input type="email" id="rwNewsletterEmail" name="email" class="rw-input" placeholder="you@example.com" required>
                        <button type="submit" class="rw-btn rw-btn-subscribe" :disabled="sending">
                            <span x-text="sending ? 'Sending…' : 'Subscribe'"></span>
                            <?php rw_icon('send'); ?>
                        </button>
                    </div>
                    <p class="rw-newsletter-status" x-text="status" :data-state="state" role="status" aria-live="polite"></p>
                </form>

                <ul class="rw-social-links">
                    <li><a href="https://x.com/TXBeardedViking" class="rw-social-link" target="_blank" rel="noopener noreferrer" aria-label="X"><?php rw_icon('twitter'); ?></a></li>
                    <li><a href="https://www.facebook.com/BeardedVikingTX" class="rw-social-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><?php rw_icon('facebook'); ?></a></li>
                    <li><a href="https://www.linkedin.com/in/bearded-viking-3112a8431/" class="rw-social-link" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><?php rw_icon('linkedin'); ?></a></li>
                    <li><a href="https://www.tiktok.com/@beardedvikingtx" class="rw-social-link" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><?php rw_icon('music-2'); ?></a></li>
                    <li><a href="https://github.com/BeardedVikingTX" class="rw-social-link" target="_blank" rel="noopener noreferrer" aria-label="GitHub"><?php rw_icon('github'); ?></a></li>
                </ul>
            </div>

        </div>

        <div class="rw-footer-bottom">
            <p class="rw-footer-copyright">&copy; <?= htmlspecialchars($rw_currentYear, ENT_QUOTES, 'UTF-8') ?> RavenWarp. All realms reserved.</p>
            <p class="rw-footer-tagline">Built by an Irish Viking with a love for the stars.</p>
        </div>
    </div>

    <button type="button" id="rwBackToTop" class="rw-back-to-top" aria-label="Scroll back to top"
            x-data="{ visible: false }"
            x-init="window.addEventListener('scroll', () => visible = window.scrollY > 400, { passive: true })"
            :class="{ 'rw-visible': visible }"
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })">
        <?php rw_icon('arrow-up'); ?>
    </button>
</footer>

<!-- ================= Scripts (all self-hosted) ================= -->
<script defer src="/assets/vendors/AlpineJS/cdn.min.js"></script>
<script src="/assets/js/main.js"></script>

</body>
</html>