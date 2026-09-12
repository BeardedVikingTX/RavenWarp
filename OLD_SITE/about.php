<?php
/**
 * ============================================================
 *  RavenWarp :: about.php
 * ------------------------------------------------------------
 *  The About page: the story behind RavenWarp's creator, a full
 *  academic-style breakdown of the 5-way AI build experiment,
 *  and an AJAX-driven voting widget (see vote-process.php).
 * ============================================================
 */

define('RAVENWARP_APP', true);

$pageTitle       = 'About — RavenWarp';
$pageDescription = 'The story behind RavenWarp\'s creator and a full breakdown of the public 5-way AI development experiment comparing Claude, ChatGPT, Gemini, Copilot, and DeepSeek.';
$pageBodyClass   = 'page-about';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/cookies.php';

$rw_projects_for_vote = ['RavenWarp', 'Nexora', 'Valkyrin', 'SagaSphere', 'NexusValhalla'];
$rw_csrf = rw_csrf_token();
?>

<!-- ================================================================
     PAGE HEADER
     ================================================================ -->
<section class="rw-hero" style="padding-block: clamp(3rem, 7vw, 5rem);">
    <div class="rw-floaters" aria-hidden="true">
        <i class="fa-solid fa-user-astronaut rw-floater rw-floater-1"></i>
        <i class="fa-solid fa-shield-halved rw-floater rw-ember-glyph rw-floater-2"></i>
        <i class="fa-solid fa-scroll rw-floater rw-floater-3"></i>
    </div>
    <div class="rw-section-inner">
        <span class="rw-hero-eyebrow"><span class="rw-dot"></span> Dossier &amp; Documentation</span>
        <h1 class="rw-hero-title">About <span class="rw-highlight">RavenWarp</span></h1>
        <p class="rw-hero-subtitle">
            The person behind the project, and the full record of the experiment RavenWarp was built to be part of.
        </p>
    </div>
</section>

<!-- ================================================================
     THE ARCHITECT
     ================================================================ -->
<section class="rw-section" id="rw-architect">
    <div class="rw-section-inner">
        <div class="rw-section-header">
            <span class="rw-section-eyebrow">Dossier 01</span>
            <h2 class="rw-section-title">The Architect Behind the Build</h2>
        </div>

        <p class="rw-section-lede">
            RavenWarp is built and maintained by Bearded Viking, an Irish security researcher whose
            professional path began, as so many in the field do, with curiosity rather than credentials.
            An early fascination with computers led into the IRC-era underground of the late 1990s and
            early 2000s, where a self-taught understanding of networks, exploitation, and digital forensics
            was forged largely through trial, error, and a great deal of independent study.
        </p>
        <p class="rw-section-lede">
            That early, unsupervised period gave way over time to a deliberate academic and professional
            pivot. Bearded Viking went on to earn a Ph.D. in Computer Science from Ashley University, with
            a research focus on network security and cryptographic protocols, and subsequently obtained the
            OSCP (Offensive Security Certified Professional) and CEH (Certified Ethical Hacker)
            certifications &mdash; formal recognition of a transition from unstructured exploration into
            disciplined, authorized security work. Today, that experience is applied through professional
            penetration testing, vulnerability assessment, and an active bug bounty practice, alongside
            technical writing intended to help other practitioners build and defend systems more
            responsibly than the ones once explored without permission.
        </p>
        <p class="rw-section-lede">
            Bearded Viking describes the throughline of that journey simply: turning scars into armor, and
            vulnerabilities &mdash; both technical and personal &mdash; into strengths. RavenWarp, and the
            broader experiment it belongs to, is an extension of that same instinct: build things in the
            open, document the process honestly, and invite scrutiny rather than avoid it.
        </p>

        <div class="rw-reward-grid" style="margin-top:2rem;">
            <div class="rw-panel rw-reward-card">
                <div class="rw-reward-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                <div class="rw-reward-label">Ph.D., Computer Science</div>
            </div>
            <div class="rw-panel rw-reward-card">
                <div class="rw-reward-icon"><i class="fa-solid fa-certificate"></i></div>
                <div class="rw-reward-label">OSCP Certified</div>
            </div>
            <div class="rw-panel rw-reward-card">
                <div class="rw-reward-icon"><i class="fa-solid fa-user-shield"></i></div>
                <div class="rw-reward-label">CEH Certified</div>
            </div>
            <div class="rw-panel rw-reward-card">
                <div class="rw-reward-icon"><i class="fa-solid fa-bug"></i></div>
                <div class="rw-reward-label">Active Bug Bounty Researcher</div>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================
     THE EXPERIMENT — METHODOLOGY
     ================================================================ -->
<section class="rw-section rw-section-alt" id="rw-methodology">
    <div class="rw-section-inner">
        <div class="rw-section-header">
            <span class="rw-section-eyebrow">Dossier 02</span>
            <h2 class="rw-section-title">Methodology: A Controlled Comparison of Five AI Systems</h2>
        </div>
        <p class="rw-section-lede">
            RavenWarp exists as one arm of a broader, publicly documented research exercise: a controlled,
            side-by-side comparison of how five distinct large language model systems approach an identical
            software engineering brief. The objective is not to produce marketing copy for any single
            vendor, but to generate a transparent, reproducible record of each system's practical
            capabilities when tasked with a real, non-trivial build &mdash; a themed, database-backed,
            security-conscious social media platform &mdash; under matched constraints.
        </p>
        <p class="rw-section-lede">
            Each participating system, GitHub Copilot, Google Gemini, OpenAI's ChatGPT, Anthropic's
            Claude, and DeepSeek, was given the same functional requirements, the same technology
            constraints (PHP, JavaScript, AJAX, MySQL, HTML5, CSS3, Bootstrap 5), and the same Sci-Fi
            Viking creative brief. No system received a different scope, a different feature list, or
            preferential guidance. The resulting builds are deployed independently, each to its own
            subdomain, with its own public GitHub repository documenting the full commit history behind
            it, so the development process itself &mdash; not just the finished product &mdash; remains
            open to inspection.
        </p>

        <div class="rw-compare-grid">
            <?php
            $rw_all_builds = [
                ['name' => 'RavenWarp',     'ai' => 'Claude',   'url' => 'https://ravenwarp.beardedviking.org',      'repo' => 'https://github.com/BeardedVikingTX/RavenWarp'],
                ['name' => 'Nexora',        'ai' => 'ChatGPT',  'url' => 'https://nexora.beardedviking.org',         'repo' => 'https://github.com/BeardedVikingTX/Nexora'],
                ['name' => 'Valkyrin',      'ai' => 'Gemini',   'url' => 'https://valkyrin.beardedviking.org',       'repo' => 'https://github.com/BeardedVikingTX/Valkyrin'],
                ['name' => 'SagaSphere',    'ai' => 'Copilot',  'url' => 'https://sagasphere.beardedviking.org',     'repo' => 'https://github.com/BeardedVikingTX/SagaSphere'],
                ['name' => 'NexusValhalla', 'ai' => 'DeepSeek', 'url' => 'https://nexusvalhalla.beardedviking.org',  'repo' => 'https://github.com/BeardedVikingTX/NexusValhalla'],
            ];
            foreach ($rw_all_builds as $rw_build): ?>
                <div class="rw-panel" style="display:flex; flex-direction:column; gap:0.6rem;">
                    <h3 class="rw-compare-name" style="margin:0;"><?= htmlspecialchars($rw_build['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p style="margin:0; color:var(--rw-text-faint); font-family:var(--rw-font-nav);">Built by <?= htmlspecialchars($rw_build['ai'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="rw-compare-links">
                        <a href="<?= htmlspecialchars($rw_build['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square"></i> Visit</a>
                        <a href="<?= htmlspecialchars($rw_build['repo'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-github"></i> Repo</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ================================================================
     THE EXPERIMENT — EVALUATION CRITERIA
     ================================================================ -->
<section class="rw-section" id="rw-criteria">
    <div class="rw-section-inner">
        <div class="rw-section-header">
            <span class="rw-section-eyebrow">Dossier 03</span>
            <h2 class="rw-section-title">Evaluation: What "Winning" Actually Means</h2>
        </div>
        <p class="rw-section-lede">
            The determining metric across this six-month experiment is deliberately simple and resistant
            to manipulation: genuine, active user engagement on each live platform. Sign-ups, return
            visits, posts, and community activity are tracked publicly as the race unfolds, rather than
            relying on subjective code review or a closed panel of judges. A platform that looks impressive
            in a screenshot but fails to attract or retain real users has not, by this experiment's
            definition, won anything.
        </p>
        <p class="rw-section-lede">
            To add a second, community-driven signal alongside raw traffic, visitors to this page may also
            cast a single vote for the build they find most compelling. Votes are recorded once per
            verified email address, and every submission is confirmed by email to both the voter and the
            project's lead engineer, keeping the process auditable rather than anonymous and unaccountable.
        </p>
    </div>
</section>

<!-- ================================================================
     VOTING WIDGET
     ================================================================ -->
<section class="rw-section rw-section-alt" id="rw-vote">
    <div class="rw-section-inner">
        <div class="rw-section-header">
            <span class="rw-section-eyebrow">Dossier 04</span>
            <h2 class="rw-section-title">Cast Your Vote</h2>
            <p class="rw-section-lede">
                Pick the build you think deserves to win the six-month race. One vote per email address —
                you'll get a confirmation in your inbox the moment it's recorded.
            </p>
        </div>

        <form id="rwVoteForm" class="rw-panel" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($rw_csrf, ENT_QUOTES, 'UTF-8') ?>">

            <!-- Honeypot field — hidden from real users via main.css, invisible to screen readers -->
            <div style="position:absolute; left:-9999px;" aria-hidden="true">
                <label for="rwWebsite">Leave this field empty</label>
                <input type="text" id="rwWebsite" name="website" tabindex="-1" autocomplete="off">
            </div>

            <fieldset style="border:0; padding:0; margin:0 0 1.25rem;">
                <legend class="rw-footer-heading" style="margin-bottom:1rem;">Choose a build</legend>
                <div class="rw-compare-grid">
                    <?php foreach ($rw_projects_for_vote as $rw_project): ?>
                        <label class="rw-panel" style="cursor:pointer; display:flex; align-items:center; gap:0.75rem; padding:1rem;">
                            <input type="radio" name="voted_for" value="<?= htmlspecialchars($rw_project, ENT_QUOTES, 'UTF-8') ?>" required>
                            <span class="rw-compare-name" style="font-size:1rem;"><?= htmlspecialchars($rw_project, ENT_QUOTES, 'UTF-8') ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="rwVoterName" class="visually-hidden">Your name</label>
                    <input type="text" id="rwVoterName" name="voter_name" class="form-control rw-newsletter-input" placeholder="Your name" required maxlength="100">
                </div>
                <div class="col-12 col-md-6">
                    <label for="rwVoterEmail" class="visually-hidden">Your email</label>
                    <input type="email" id="rwVoterEmail" name="voter_email" class="form-control rw-newsletter-input" placeholder="you@example.com" required>
                </div>
            </div>

            <button type="submit" class="btn rw-btn rw-btn-register rw-btn-lg" id="rwVoteSubmit" style="margin-top:1.25rem;">
                <span class="rw-btn-label">Submit Vote</span>
                <i class="fa-solid fa-check-to-slot"></i>
            </button>

            <p class="rw-newsletter-status" id="rwVoteStatus" role="status" aria-live="polite"></p>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var voteForm = document.getElementById('rwVoteForm');
    var voteStatus = document.getElementById('rwVoteStatus');
    var submitBtn = document.getElementById('rwVoteSubmit');

    if (!voteForm) return;

    voteForm.addEventListener('submit', function (event) {
        event.preventDefault();

        submitBtn.disabled = true;
        voteStatus.removeAttribute('data-state');
        voteStatus.textContent = 'Submitting your vote…';

        var formData = new FormData(voteForm);

        fetch('/vote-process.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                voteStatus.textContent = data.message;
                voteStatus.setAttribute('data-state', data.success ? 'success' : 'error');
                if (data.success) {
                    voteForm.reset();
                    submitBtn.innerHTML = '<span class="rw-btn-label">Vote Recorded</span> <i class="fa-solid fa-circle-check"></i>';
                } else {
                    submitBtn.disabled = false;
                }
            })
            .catch(function () {
                voteStatus.textContent = 'Something went wrong sending your vote. Please try again.';
                voteStatus.setAttribute('data-state', 'error');
                submitBtn.disabled = false;
            });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>