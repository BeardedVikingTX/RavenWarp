/**
 * ============================================================
 *  RavenWarp :: assets/js/main.js  (Command Deck rebuild)
 * ------------------------------------------------------------
 *  Shared, site-wide behavior. Page-specific AJAX (register,
 *  login, vote, contact forms) stays inline on those pages —
 *  this file is for things every page benefits from.
 *
 *  NOTE ON VENDORS NOT LOADED HERE:
 *  DOMPurify and Chart.js are vendored under assets/vendors/ but
 *  intentionally NOT loaded on every page — they only matter once
 *  posts/comments (DOMPurify) or the admin analytics dashboard
 *  (Chart.js) exist. Load them on those specific pages when we
 *  build them, not globally, to keep every other page lighter.
 *  The two small wrapper functions below (rw.sanitize, rw.chart)
 *  are ready to use the moment those scripts ARE present.
 * ============================================================
 */

window.rw = window.rw || {};

(function (rw) {
    'use strict';

    /**
     * -------------------------------------------------------
     * Reusable AJAX helper — wraps fetch() with the JSON-in/
     * JSON-out pattern every form on this site already follows
     * (vote-process.php, contact-process.php, register-process.php,
     * login-process.php). New pages can adopt this instead of
     * repeating the same fetch/then/catch block.
     * -------------------------------------------------------
     */
    rw.postForm = function (url, formEl) {
        return fetch(url, {
            method: 'POST',
            body: new FormData(formEl),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        }).then(function (response) {
            return response.json();
        });
    };

    /**
     * -------------------------------------------------------
     * Sanitizes untrusted HTML before it's ever injected into
     * the DOM. No-ops (returns the input escaped-as-text) if
     * DOMPurify hasn't been loaded on the current page — this
     * fails SAFE, never open, if a page forgets to include it.
     * -------------------------------------------------------
     */
    rw.sanitize = function (dirtyHtml) {
        if (typeof DOMPurify !== 'undefined') {
            return DOMPurify.sanitize(dirtyHtml);
        }
        var textNode = document.createElement('div');
        textNode.textContent = dirtyHtml;
        return textNode.innerHTML;
    };

    /**
     * -------------------------------------------------------
     * Thin Chart.js wrapper for the future admin dashboard —
     * no-ops with a console warning if Chart.js isn't loaded on
     * the current page, rather than throwing.
     * -------------------------------------------------------
     */
    rw.chart = function (canvasId, config) {
        if (typeof Chart === 'undefined') {
            console.warn('rw.chart(): Chart.js is not loaded on this page.');
            return null;
        }
        var el = document.getElementById(canvasId);
        if (!el) {
            console.warn('rw.chart(): no canvas found with id "' + canvasId + '".');
            return null;
        }
        return new Chart(el, config);
    };

    /**
     * -------------------------------------------------------
     * Glitch-reveal on load — adds .rw-glitch-in to any element
     * with [data-rw-glitch], triggering the CSS keyframe defined
     * in main.css. Respects prefers-reduced-motion.
     * -------------------------------------------------------
     */
    function initGlitchReveal() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }
        document.querySelectorAll('[data-rw-glitch]').forEach(function (el) {
            // Restart the animation cleanly even if triggered more than once.
            el.classList.remove('rw-glitch-in');
            void el.offsetWidth; // force reflow
            el.classList.add('rw-glitch-in');
        });
    }

    /**
     * -------------------------------------------------------
     * Scroll-reveal for HUD panels — a gentle fade/rise as each
     * .rw-panel enters the viewport, using IntersectionObserver
     * (cheap, no scroll-event polling). Skipped entirely under
     * prefers-reduced-motion.
     * -------------------------------------------------------
     */
    function initScrollReveal() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }
        if (!('IntersectionObserver' in window)) {
            return;
        }

        var panels = document.querySelectorAll('.rw-panel');
        if (!panels.length) return;

        panels.forEach(function (panel) {
            panel.style.opacity = '0';
            panel.style.transform = 'translateY(16px)';
            panel.style.transition = 'opacity 500ms ease, transform 500ms ease';
        });

        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        panels.forEach(function (panel) { observer.observe(panel); });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initGlitchReveal();
        initScrollReveal();
    });

})(window.rw);