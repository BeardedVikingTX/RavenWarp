<?php
/**
 * ============================================================
 *  RavenWarp :: users/dashboard.php  (Command Deck rebuild)
 * ============================================================
 */

define('RAVENWARP_APP', true);

require_once __DIR__ . '/../includes/cookies.php';

if (!rw_is_logged_in()) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/encryption.php';

$pageTitle       = 'Dashboard — RavenWarp';
$pageDescription = 'Your RavenWarp dashboard.';
$pageBodyClass   = 'page-dashboard';

require_once __DIR__ . '/../includes/header.php';

$pdo = rw_db();
$stmt = $pdo->prepare(
    'SELECT username, slug, account_type, first_name_enc, last_name_enc, email_enc,
            avatar_path, banner_path, role, reputation_points, status,
            last_login_at, created_at
     FROM users WHERE id = :id LIMIT 1'
);
$stmt->execute(['id' => rw_current_user_id()]);
$rw_user = $stmt->fetch();

if (!$rw_user) {
    rw_destroy_session();
    header('Location: /login.php');
    exit;
}

$rw_first_name = $rw_user['first_name_enc'] ? rw_decrypt($rw_user['first_name_enc']) : null;
$rw_last_name  = $rw_user['last_name_enc']  ? rw_decrypt($rw_user['last_name_enc'])  : null;
$rw_email      = $rw_user['email_enc']      ? rw_decrypt($rw_user['email_enc'])      : null;

$rw_display_name = trim(($rw_first_name ?? '') . ' ' . ($rw_last_name ?? ''));
$rw_member_since = date('F Y', strtotime($rw_user['created_at']));
$rw_last_login   = $rw_user['last_login_at'] ? date('M j, Y \a\t g:i A', strtotime($rw_user['last_login_at'])) : 'This is your first login!';
$rw_avatar_url   = $rw_user['avatar_path'] ?? null;

// Earned badges, most recent first.
$badgeStmt = $pdo->prepare(
    'SELECT b.name, b.description, b.icon, ub.earned_at
     FROM user_badges ub
     JOIN badges b ON b.id = ub.badge_id
     WHERE ub.user_id = :user_id
     ORDER BY ub.earned_at DESC'
);
$badgeStmt->execute(['user_id' => rw_current_user_id()]);
$rw_badges = $badgeStmt->fetchAll();
?>

<section class="rw-section" style="padding-top: 3rem;">
    <div class="rw-section-inner">

        <!-- ================= Profile Header ================= -->
        <div class="rw-panel" style="padding:0; overflow:hidden; margin-bottom:1.5rem;">
            <div style="height:160px; background:linear-gradient(135deg, rgba(94,232,224,0.18), rgba(255,157,66,0.18)), var(--rw-surface-alt); position:relative;">
                <?php if ($rw_user['banner_path']): ?>
                    <img src="<?= htmlspecialchars($rw_user['banner_path'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:100%; height:100%; object-fit:cover;">
                <?php endif; ?>
            </div>

            <div style="padding: 0 2rem 1.75rem; display:flex; flex-wrap:wrap; gap:1.5rem; align-items:flex-end; margin-top:-46px;">
                <div style="width:92px; height:92px; border-radius:50%; border:4px solid var(--rw-surface); background:var(--rw-surface-alt); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                    <?php if ($rw_avatar_url): ?>
                        <img src="<?= htmlspecialchars($rw_avatar_url, ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                        <?php rw_icon('user-round', 'rw-icon-xl'); ?>
                    <?php endif; ?>
                </div>

                <div style="flex:1; min-width:200px; padding-top:0.5rem;">
                    <h1 style="margin:0; font-size:1.6rem;"><?= htmlspecialchars($rw_user['username'], ENT_QUOTES, 'UTF-8') ?></h1>
                    <?php if ($rw_display_name !== ''): ?>
                        <p style="margin:0.15rem 0 0; color:var(--rw-text-muted);"><?= htmlspecialchars($rw_display_name, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <div style="display:flex; gap:0.5rem; margin-top:0.6rem; flex-wrap:wrap;">
                        <span class="rw-compare-badge"><?= htmlspecialchars(ucfirst($rw_user['account_type']), ENT_QUOTES, 'UTF-8') ?> Account</span>
                        <?php if ($rw_user['role'] !== 'member'): ?>
                            <span class="rw-compare-badge" style="background:var(--rw-frost);"><?= htmlspecialchars(ucfirst($rw_user['role']), ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <a href="/logout.php" class="rw-btn rw-btn-login" style="align-self:center;">
                    <?php rw_icon('log-out'); ?> Logout
                </a>
            </div>
        </div>

        <!-- ================= Stats ================= -->
        <div class="rw-reward-grid" style="margin-bottom:1.5rem;">
            <div class="rw-panel rw-reward-card"><div class="rw-reward-icon"><?php rw_icon('star', 'rw-icon-lg'); ?></div><div class="rw-reward-label"><?= (int) $rw_user['reputation_points'] ?> Reputation</div></div>
            <div class="rw-panel rw-reward-card"><div class="rw-reward-icon"><?php rw_icon('calendar-days', 'rw-icon-lg'); ?></div><div class="rw-reward-label">Member Since<br><?= htmlspecialchars($rw_member_since, ENT_QUOTES, 'UTF-8') ?></div></div>
            <div class="rw-panel rw-reward-card"><div class="rw-reward-icon"><?php rw_icon('clock', 'rw-icon-lg'); ?></div><div class="rw-reward-label" style="font-size:0.95rem;">Last Login<br><?= htmlspecialchars($rw_last_login, ENT_QUOTES, 'UTF-8') ?></div></div>
            <div class="rw-panel rw-reward-card"><div class="rw-reward-icon"><?php rw_icon('badge-check', 'rw-icon-lg'); ?></div><div class="rw-reward-label"><?= count($rw_badges) ?> Badge<?= count($rw_badges) === 1 ? '' : 's' ?> Earned</div></div>
        </div>

        <div class="row g-4">
            <!-- ================= Account Details ================= -->
            <div class="col-12 col-lg-6">
                <div class="rw-panel" style="height:100%;">
                    <h2 class="rw-footer-heading">Account Details</h2>
                    <ul class="list-unstyled" style="margin:0;">
                        <li style="padding:0.6rem 0; border-bottom:1px solid var(--rw-border); display:flex; justify-content:space-between;">
                            <span style="color:var(--rw-text-muted);">Alias</span>
                            <strong><?= htmlspecialchars($rw_user['username'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </li>
                        <?php if ($rw_email): ?>
                        <li style="padding:0.6rem 0; border-bottom:1px solid var(--rw-border); display:flex; justify-content:space-between;">
                            <span style="color:var(--rw-text-muted);">Email</span>
                            <strong><?= htmlspecialchars($rw_email, ENT_QUOTES, 'UTF-8') ?></strong>
                        </li>
                        <?php endif; ?>
                        <li style="padding:0.6rem 0; display:flex; justify-content:space-between;">
                            <span style="color:var(--rw-text-muted);">Account Type</span>
                            <strong><?= htmlspecialchars(ucfirst($rw_user['account_type']), ENT_QUOTES, 'UTF-8') ?></strong>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- ================= Quick Actions ================= -->
            <div class="col-12 col-lg-6">
                <div class="rw-panel" style="height:100%;">
                    <h2 class="rw-footer-heading">Quick Actions</h2>
                    <div style="display:flex; flex-direction:column; gap:0.6rem;">
                        <span class="rw-btn rw-btn-login" style="text-align:left; opacity:0.6; cursor:not-allowed;"><?php rw_icon('user-pen'); ?> Edit Profile <span style="margin-left:auto; font-size:0.75rem;">Coming Soon</span></span>
                        <span class="rw-btn rw-btn-login" style="text-align:left; opacity:0.6; cursor:not-allowed;"><?php rw_icon('message-square'); ?> Messages <span style="margin-left:auto; font-size:0.75rem;">Coming Soon</span></span>
                        <span class="rw-btn rw-btn-login" style="text-align:left; opacity:0.6; cursor:not-allowed;"><?php rw_icon('users'); ?> Friends <span style="margin-left:auto; font-size:0.75rem;">Coming Soon</span></span>
                        <?php if ($rw_user['role'] === 'admin'): ?>
                        <a href="/admin/index.php" class="rw-btn rw-btn-register" style="text-align:left;"><?php rw_icon('radar'); ?> Admin Panel</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= Badges ================= -->
        <div class="rw-panel" style="margin-top:1.5rem;">
            <h2 class="rw-footer-heading">Badges</h2>
            <?php if (empty($rw_badges)): ?>
                <p style="color:var(--rw-text-muted); margin:0;">No badges earned yet — post, connect, and comment to start collecting them.</p>
            <?php else: ?>
                <div class="rw-reward-grid">
                    <?php foreach ($rw_badges as $rw_badge): ?>
                        <div class="rw-panel rw-reward-card" title="<?= htmlspecialchars($rw_badge['description'], ENT_QUOTES, 'UTF-8') ?>">
                            <div class="rw-reward-icon"><?php rw_icon($rw_badge['icon'], 'rw-icon-lg'); ?></div>
                            <div class="rw-reward-label"><?= htmlspecialchars($rw_badge['name'], ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>