<?php
/**
 * ============================================================
 *  RavenWarp :: register-process.php  (Command Deck rebuild)
 * ------------------------------------------------------------
 *  1. Create /users/images/avatars/{slug} and /banners/{slug}
 *  2. Encrypt sensitive fields, hash the password, save to DB
 *  3. Award the Welcome badge
 *  4. Send a welcome email (professional tier only)
 *  5. Log the user in, redirect to /users/dashboard.php
 * ============================================================
 */

define('RAVENWARP_APP', true);

require_once __DIR__ . '/includes/cookies.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/encryption.php';
require_once __DIR__ . '/includes/slug.php';
require_once __DIR__ . '/includes/uploads.php';

header('Content-Type: application/json; charset=utf-8');

function rw_register_respond(bool $success, string $message, array $extra = [], int $httpStatus = 200): never
{
    http_response_code($httpStatus);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rw_register_respond(false, 'Invalid request method.', [], 405);
}
if (!empty($_POST['website'] ?? '')) {
    rw_register_respond(true, 'Welcome aboard!');
}
if (!rw_validate_csrf($_POST['csrf_token'] ?? null)) {
    rw_register_respond(false, 'Your session expired — please refresh the page and try again.', [], 419);
}

$accountType = ($_POST['account_type'] ?? '') === 'professional' ? 'professional' : 'anonymous';

$username        = trim((string) ($_POST['username'] ?? ''));
$password        = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');
$firstName = trim((string) ($_POST['first_name'] ?? ''));
$lastName  = trim((string) ($_POST['last_name'] ?? ''));
$email     = trim((string) ($_POST['email'] ?? ''));

$errors = [];

if ($username === '' || mb_strlen($username) < 3 || mb_strlen($username) > 50) {
    $errors[] = 'Alias must be between 3 and 50 characters.';
}
if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}
if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $errors[] = 'Password must include at least one letter and one number.';
}
if ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match.';
}
if ($accountType === 'professional') {
    if ($firstName === '' || mb_strlen($firstName) > 60) $errors[] = 'First name is required.';
    if ($lastName === '' || mb_strlen($lastName) > 60) $errors[] = 'Last name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required for professional registration.';
}
if (!empty($errors)) {
    rw_register_respond(false, implode(' ', $errors), [], 422);
}

$pdo = rw_db();

$usernameCheck = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
$usernameCheck->execute(['username' => $username]);
if ($usernameCheck->fetch()) {
    rw_register_respond(false, 'That alias is already taken. Please choose another.', [], 409);
}

$emailHash = null;
if ($accountType === 'professional') {
    $emailHash = rw_lookup_hash($email);
    $emailCheck = $pdo->prepare('SELECT id FROM users WHERE email_hash = :hash LIMIT 1');
    $emailCheck->execute(['hash' => $emailHash]);
    if ($emailCheck->fetch()) {
        rw_register_respond(false, 'An account with that email already exists.', [], 409);
    }
}

// STEP 1: storage directories
$slug = rw_generate_unique_slug($pdo, $username);
try {
    $mediaDirs = rw_ensure_user_media_dirs($slug);
} catch (RuntimeException $e) {
    error_log('RavenWarp REGISTER DIR ERROR: ' . $e->getMessage());
    rw_register_respond(false, 'We couldn\'t set up your account storage. Please try again shortly.', [], 500);
}

$avatarRelativePath = null;
try {
    $savedAvatarPath = rw_handle_image_upload($_FILES['avatar'] ?? [], $mediaDirs['avatar_dir'], 'avatar');
    if ($savedAvatarPath !== null) {
        $avatarRelativePath = '/users/images/avatars/' . $slug . '/' . basename($savedAvatarPath);
    }
} catch (RuntimeException $e) {
    rw_register_respond(false, $e->getMessage(), [], 422);
}

// STEP 2: encrypt + hash + save
$passwordHash = password_hash($password, defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT);
$firstNameEnc = $accountType === 'professional' ? rw_encrypt($firstName) : null;
$lastNameEnc  = $accountType === 'professional' ? rw_encrypt($lastName) : null;
$emailEnc     = $accountType === 'professional' ? rw_encrypt($email) : null;

try {
    $pdo->beginTransaction();

    $insertStmt = $pdo->prepare(
        'INSERT INTO users
            (username, slug, account_type, password_hash, first_name_enc, last_name_enc, email_enc, email_hash, avatar_path)
         VALUES
            (:username, :slug, :account_type, :password_hash, :first_name_enc, :last_name_enc, :email_enc, :email_hash, :avatar_path)'
    );
    $insertStmt->execute([
        'username' => $username, 'slug' => $slug, 'account_type' => $accountType,
        'password_hash' => $passwordHash, 'first_name_enc' => $firstNameEnc,
        'last_name_enc' => $lastNameEnc, 'email_enc' => $emailEnc,
        'email_hash' => $emailHash, 'avatar_path' => $avatarRelativePath,
    ]);
    $newUserId = (int) $pdo->lastInsertId();

    // Create the (initially empty) extended profile row too, so later
    // profile-edit pages can always assume it exists (INSERT ... ON DUPLICATE
    // free — one row per user, created at signup).
    $pdo->prepare('INSERT INTO user_profiles (user_id) VALUES (:user_id)')
        ->execute(['user_id' => $newUserId]);

    // STEP 3: award the Welcome badge — best-effort, never blocks registration
    // if the badges table doesn't have the 'welcome' row seeded yet.
    try {
        $badgeStmt = $pdo->prepare('SELECT id FROM badges WHERE slug = :slug LIMIT 1');
        $badgeStmt->execute(['slug' => 'welcome']);
        $welcomeBadge = $badgeStmt->fetch();
        if ($welcomeBadge) {
            $pdo->prepare('INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (:user_id, :badge_id)')
                ->execute(['user_id' => $newUserId, 'badge_id' => $welcomeBadge['id']]);
        }
    } catch (PDOException $e) {
        error_log('RavenWarp REGISTER WELCOME BADGE WARNING: ' . $e->getMessage());
        // Not fatal — registration proceeds even if the badges table isn't ready yet.
    }

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('RavenWarp REGISTER DB ERROR: ' . $e->getMessage());
    rw_register_respond(false, 'A server error occurred while creating your account. Please try again shortly.', [], 500);
}

// STEP 4: welcome email (professional tier only)
if ($accountType === 'professional' && $email !== '') {
    $safeFirstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
    $safeUsername  = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $headers = [
        'MIME-Version: 1.0', 'Content-Type: text/html; charset=UTF-8',
        'From: ' . RW_MAIL_FROM_NAME . ' <' . RW_MAIL_FROM . '>',
    ];
    $body = "
        <div style=\"font-family:sans-serif; max-width:520px; margin:auto;\">
            <h2 style=\"color:#0d1326;\">Welcome to RavenWarp, {$safeFirstName}!</h2>
            <p>Your account <strong>{$safeUsername}</strong> has just taken flight. We're genuinely glad you're here.</p>
            <p>You've already earned your first badge just for joining. Head to your dashboard to see it:</p>
            <p><a href=\"https://ravenwarp.beardedviking.org/users/dashboard.php\">Go to your Dashboard &rarr;</a></p>
            <p style=\"color:#8d97b3; font-size:0.9em;\">Fly far, fly true.<br>&mdash; The RavenWarp Team</p>
        </div>
    ";
    @mail($email, 'Welcome to RavenWarp', $body, implode("\r\n", $headers));
}

// STEP 5: log in + redirect
rw_set_user_session($newUserId, $username, 'member');

rw_register_respond(true, 'Account created! Redirecting you to your dashboard…', [
    'redirect' => '/users/dashboard.php',
]);