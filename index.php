<?php
/**
 * ============================================================
 *  RavenWarp :: index.php
 * ------------------------------------------------------------
 *  Homepage. Sets page metadata, then hands off to the shared
 *  header/nav/footer shell.
 * ============================================================
 */

define('RAVENWARP_APP', true);

$pageTitle       = 'RavenWarp — Fly the Realms. Share the Signal.';
$pageDescription = 'RavenWarp is a secure, next-gen social network fusing Norse mythology with sci-fi futurism — built live as part of a public 5-way AI development experiment.';
$pageBodyClass   = 'page-home';

require_once __DIR__ . '/includes/header.php';

/**
 * ------------------------------------------------------------
 *  Data: the five competing builds in the AI experiment.
 *  Centralizing this makes it trivial to update a link or swap
 *  a screenshot later without touching the markup below.
 * ------------------------------------------------------------
 */
$rw_competitors = [
    [
        'ai_name'   => 'Claude',
        'project'   => 'RavenWarp',
        'image'     => '/assets/img/media/Claude_Homepage.png',
        'site_url'  => 'https://ravenwarp.beardedviking.org',
        'repo_url'  => 'https://github.com/BeardedVikingTX/RavenWarp',
        'featured'  => true,
    ],
    [
        'ai_name'   => 'ChatGPT',
        'project'   => 'Nexora',
        'image'     => '/assets/img/media/ChatGPT_Homepage.png',
        'site_url'  => 'https://nexora.beardedviking.org',
        'repo_url'  => 'https://github.com/BeardedVikingTX/Nexora',
        'featured'  => false,
    ],
    [
        'ai_name'   => 'Gemini',
        'project'   => 'Valkyrin',
        'image'     => '/assets/img/media/Gemini_Homepage.png',
        'site_url'  => 'https://valkyrin.beardedviking.org',
        'repo_url'  => 'https://github.com/BeardedVikingTX/Valkyrin',
        'featured'  => false,
    ],
    [
        'ai_name'   => 'Copilot',
        'project'   => 'SagaSphere',
        'image'     => '/assets/img/media/CoPilot_Homepage.png',
        'site_url'  => 'https://sagasphere.beardedviking.org',
        'repo_url'  => 'https://github.com/BeardedVikingTX/SagaSphere',
        'featured'  => false,
    ],
    [
        'ai_name'   => 'DeepSeek',
        'project'   => 'NexusValhalla',
        'image'     => '/assets/img/media/DeepSeek_Homepage.png',
        'site_url'  => 'https://nexusvalhalla.beardedviking.org',
        'repo_url'  => 'https://github.com/BeardedVikingTX/NexusValhalla',
        'featured'  => false,
    ],
];
?>

<!-- ================================================================
     HERO
     ================================================================ -->
<section class="rw-hero">
    <div class="rw-floaters" aria-hidden="true">
        <i class="fa-solid fa-feather-pointed rw-floater rw-floater-1"></i>
        <i class="fa-solid fa-atom rw-floater rw-ember-glyph rw-floater-2"></i>
        <i class="fa-solid fa-shield-halved rw-floater rw-floater-3"></i>
        <i class="fa-solid fa-meteor rw-floater rw-ember-glyph rw-floater-4"></i>
        <i class="fa-solid fa-satellite rw-floater rw-floater-5"></i>
    </div>

    <div class="rw-section-inner">
        <span class="rw-hero-eyebrow"><span class="rw-dot"></span> Live Build &mdash; Public AI Experiment</span>
        <h1 class="rw-hero-title">Fly the Realms.<br><span class="rw-highlight">Share the Signal.</span></h1>
        <p class="rw-hero-subtitle">
            RavenWarp is a secure, next-generation social network where posts, friendships, and reputation
            travel like Huginn and Muninn once did &mdash; fast, far, and straight back to the people who
            matter. Built in the open, themed for Vikings and starfarers alike.
        </p>
        <div class="rw-hero-actions">
            <a href="/register.php" class="btn rw-btn rw-btn-register rw-btn-lg">Join RavenWarp</a>
            <a href="#rw-ai-race" class="btn rw-btn rw-btn-login rw-btn-lg">See the AI Race</a>
        </div>
    </div>
</section>

<!-- ================================================================
     ABOUT / MISSION
     ================================================================ -->
<section class="rw-section" id="rw-about">
    <div class="rw-section-inner">
        <div class="rw-section-header">
            <span class="rw-section-eyebrow">Transmission 01</span>
            <h2 class="rw-section-title">A Social Network Built for Explorers</h2>
        </div>
        <p class="rw-section-lede">
            RavenWarp exists for people who want their online communities to feel like they belong to
            something bigger than a feed. Every profile carries a reputation score and a wall of earned
            badges, every friendship is a deliberate connection rather than an algorithmic suggestion, and
            every conversation &mdash; public post or private message &mdash; is protected by encryption
            that's applied before a single byte reaches the database.
        </p>
        <p class="rw-section-lede">
            Free members get the full core experience: posting, commenting, friending, and messaging,
            individually and in groups. Premium members unlock deeper customization, exclusive badge
            tiers, and priority support, without ever gatekeeping the community itself behind a paywall.
            It's a network designed to grow the way a good crew grows &mdash; one earned bond at a time.
        </p>
    </div>
</section>

<!-- ================================================================
     THE AI RACE / COMPARISON
     ================================================================ -->
<section class="rw-section rw-section-alt" id="rw-ai-race">
    <div class="rw-section-inner">
        <div class="rw-section-header">
            <span class="rw-section-eyebrow">Transmission 02</span>
            <h2 class="rw-section-title">The AI Race: Five Minds, One Brief</h2>
        </div>
        <p class="rw-section-lede">
            This site is one contender in a live, public experiment: the exact same brief &mdash; the same
            feature list, the same tech stack, the same Sci-Fi Viking theme &mdash; was handed to five
            different AI systems to see how each one thinks, builds, and documents under identical
            constraints. No cherry-picking, no do-overs: what you see live at each domain is that AI's
            unedited output, warts and all.
        </p>
        <p class="rw-section-lede">
            Every build below is publicly deployed and every line of code is publicly committed, so you
            don't have to take anyone's word for how these systems compare. Click through, poke around,
            read the commit history, and judge the work for yourself.
        </p>

        <div class="rw-compare-grid" role="list">
            <?php foreach ($rw_competitors as $rw_bot): ?>
                <article class="rw-panel rw-compare-card<?= $rw_bot['featured'] ? ' rw-compare-featured' : '' ?>" role="listitem">
                    <?php if ($rw_bot['featured']): ?>
                        <span class="rw-compare-badge"><i class="fa-solid fa-star"></i> You Are Here</span>
                    <?php endif; ?>
                    <div class="rw-compare-thumb">
                        <img src="<?= htmlspecialchars($rw_bot['image'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($rw_bot['ai_name'] . ' build homepage screenshot — ' . $rw_bot['project'], ENT_QUOTES, 'UTF-8') ?>"
                             loading="lazy">
                    </div>
                    <h3 class="rw-compare-name"><?= htmlspecialchars($rw_bot['project'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="rw-compare-ai" style="margin:0; color:var(--rw-text-faint); font-family:var(--rw-font-nav);">
                        Built by <?= htmlspecialchars($rw_bot['ai_name'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <div class="rw-compare-links">
                        <a href="<?= htmlspecialchars($rw_bot['site_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Live Site
                        </a>
                        <a href="<?= htmlspecialchars($rw_bot['repo_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                            <i class="fa-brands fa-github"></i> Repo
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ================================================================
     WHY CLAUDE / RAVENWARP
     ================================================================ -->
<section class="rw-section" id="rw-why-claude">
    <div class="rw-section-inner">
        <div class="rw-section-header">
            <span class="rw-section-eyebrow">Transmission 03</span>
            <h2 class="rw-section-title">The Case for RavenWarp</h2>
        </div>
        <p class="rw-section-lede">
            RavenWarp was built the way a careful engineer builds anything meant to last: security
            decisions made before a single form was drawn, session handling hardened at the foundation
            instead of bolted on later, and a theme system driven by real design tokens rather than
            scattered magic numbers. Every include file was written to explain itself &mdash; comments
            that describe not just what the code does, but why it's structured that way &mdash; so this
            project stays maintainable long after the article is published.
        </p>
        <p class="rw-section-lede">
            More than that, RavenWarp was designed to feel like a place, not a template. The Niflheim-and-
            Muspelheim color language, the HUD-panel card styling, the raven mythology woven into the
            copy from the brand name down to the footer &mdash; none of it is decoration bolted onto a
            generic layout. It's a coherent identity built specifically for an Irish Viking who loves
            Star Wars as much as sagas, and that specificity is exactly what separates a memorable
            platform from a forgettable one.
        </p>
    </div>
</section>

<!-- ================================================================
     THE FUTURE OF VDP PROGRAMS
     ================================================================ -->
<section class="rw-section rw-section-alt" id="rw-vdp-future">
    <div class="rw-section-inner">
        <div class="rw-section-header">
            <span class="rw-section-eyebrow">Transmission 04</span>
            <h2 class="rw-section-title">What Happens After We Win: HackerOne &amp; BugCrowd</h2>
        </div>
        <p class="rw-section-lede">
            Whichever build earns the most genuine, active users doesn't stop at bragging rights &mdash;
            it graduates into a real Vulnerability Disclosure Program on HackerOne and BugCrowd. That
            means opening the winning platform up to the global security research community: ethical
            hackers probing authentication flows, testing encrypted data handling, and hunting for the
            kind of subtle logic flaws that only adversarial testing ever finds. It's the difference
            between a site that feels secure and a site that's actually been proven secure by people
            whose entire job is breaking things.
        </p>
        <p class="rw-section-lede">
            For RavenWarp specifically, that future starts from a genuine head start: encrypted-at-rest
            data, hardened session handling with fingerprint binding and CSRF protection, and defense-in-
            depth baked in from the very first file. A strong VDP program doesn't patch a weak
            foundation &mdash; it stress-tests a strong one, and that's the foundation this build is
            racing to prove.
        </p>
    </div>
</section>

<!-- ================================================================
     FINAL RACE OPERATION
     ================================================================ -->
<section class="rw-section" id="rw-final-race">
    <div class="rw-section-inner">
        <div class="rw-section-header">
            <span class="rw-section-eyebrow">Transmission 05</span>
            <h2 class="rw-section-title">The Final Race: 6 Months to Prove It</h2>
        </div>
        <p class="rw-section-lede">
            Every one of the five builds goes live and stays live for six months, competing on the only
            metric that actually matters for a social platform: real, active users. No vanity metrics,
            no rigged voting &mdash; just genuine traffic, genuine sign-ups, and genuine engagement,
            tracked publicly as the experiment unfolds. At the end of the six months, the build with the
            most active users doesn't just win an article &mdash; it wins a future.
        </p>

        <div class="rw-timeline">
            <div class="rw-timeline-item">
                <h3 class="rw-timeline-title"><i class="fa-solid fa-flag-checkered"></i> Month 0 &mdash; Launch</h3>
                <p>All five builds go live simultaneously on their subdomains, each publicly documented and openly committed to GitHub for full transparency.</p>
            </div>
            <div class="rw-timeline-item">
                <h3 class="rw-timeline-title"><i class="fa-solid fa-chart-line"></i> Months 1&ndash;5 &mdash; The Race</h3>
                <p>Active users, engagement, and community growth are tracked across all five platforms as build updates, fixes, and features roll out live.</p>
            </div>
            <div class="rw-timeline-item">
                <h3 class="rw-timeline-title"><i class="fa-solid fa-trophy"></i> Month 6 &mdash; The Reward</h3>
                <p>The platform with the most active users graduates from the experiment into a fully independent, permanent platform.</p>
            </div>
        </div>

        <div class="rw-reward-grid">
            <div class="rw-panel rw-reward-card">
                <div class="rw-reward-icon"><i class="fa-solid fa-globe"></i></div>
                <div class="rw-reward-label">Own Domain Name</div>
            </div>
            <div class="rw-panel rw-reward-card">
                <div class="rw-reward-icon"><i class="fa-solid fa-server"></i></div>
                <div class="rw-reward-label">Dedicated Hosted Server</div>
            </div>
            <div class="rw-panel rw-reward-card">
                <div class="rw-reward-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="rw-reward-label">Increased Security</div>
            </div>
            <div class="rw-panel rw-reward-card">
                <div class="rw-reward-icon"><i class="fa-solid fa-award"></i></div>
                <div class="rw-reward-label">Founding User Roles &amp; Badges</div>
            </div>
            <div class="rw-panel rw-reward-card">
                <div class="rw-reward-icon"><i class="fa-solid fa-ellipsis"></i></div>
                <div class="rw-reward-label">&amp; So Much More</div>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================
     CLOSING CTA
     ================================================================ -->
<section class="rw-section rw-section-alt" id="rw-cta">
    <div class="rw-section-inner text-center">
        <h2 class="rw-section-title">The Realms Are Waiting</h2>
        <p class="rw-section-lede" style="margin-inline:auto;">
            Create an account, claim your founding badge early, and watch this platform build itself in
            public over the next six months.
        </p>
        <div class="rw-hero-actions" style="justify-content:center;">
            <a href="/register.php" class="btn rw-btn rw-btn-register rw-btn-lg">Create Your Account</a>
            <a href="/about.php" class="btn rw-btn rw-btn-login rw-btn-lg">Read the Full Story</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>