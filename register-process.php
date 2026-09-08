<?php
/**
 * ============================================================
 *  RavenWarp :: register-process.php
 * ------------------------------------------------------------
 *  AJAX-only backend for register.php. Handles both account
 *  tiers (anonymous / professional).
 *
 *  FLOW (matches the spec exactly):
 *   1. Create /users/images/avatars/{slug} and /banners/{slug}
 *   2. Encrypt sensitive fields, hash the password, save to DB
 *   3. Send a welcome email (professional tier only — anonymous
 *      accounts have no email to send to)
 *   4. Log the user in and tell the browser to redirect to
 *      /users/dashboard.php
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

// ------------------------------------------------------------
// 1. Method + CSRF
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rw_register_respond(false, 'Invalid request method.', [], 405);
}

if (!empty($_POST['website'] ?? '')) { // honeypot
    rw_register_respond(true, 'Welcome aboard!');
}

if (!rw_validate_csrf($_POST['csrf_token'] ?? null)) {
    rw_register_respond(false, 'Your session expired — please refresh the page and try again.', [], 419);
}

// ------------------------------------------------------------
// 2. Gather + validate input
// ------------------------------------------------------------
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
    if ($firstName === '' || mb_strlen($firstName) > 60) {
        $errors[] = 'First name is required.';
    }
    if ($lastName === '' || mb_strlen($lastName) > 60) {
        $errors[] = 'Last name is required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required for professional registration.';
    }
}

if (!empty($errors)) {
    rw_register_respond(false, implode(' ', $errors), [], 422);
}

$pdo = rw_db();

// ------------------------------------------------------------
// 3. Uniqueness checks (username + email)
// ------------------------------------------------------------
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

// ------------------------------------------------------------
// STEP 1: Create the user's storage directories
// ------------------------------------------------------------
$slug = rw_generate_unique_slug($pdo, $username);

try {
    $mediaDirs = rw_ensure_user_media_dirs($slug);
} catch (RuntimeException $e) {
    error_log('RavenWarp REGISTER DIR ERROR: ' . $e->getMessage());
    rw_register_respond(false, 'We couldn\'t set up your account storage. Please try again shortly.', [], 500);
}

// ------------------------------------------------------------
// Handle optional avatar upload (banner upload can follow the same
// pattern later from the dashboard — not part of registration per spec)
// ------------------------------------------------------------
$avatarRelativePath = null;
try {
    $savedAvatarPath = rw_handle_image_upload($_FILES['avatar'] ?? [], $mediaDirs['avatar_dir'], 'avatar');
    if ($savedAvatarPath !== null) {
        // Store a web-relative path, not a filesystem path.
        $avatarRelativePath = '/users/images/avatars/' . $slug . '/' . basename($savedAvatarPath);
    }
} catch (RuntimeException $e) {
    rw_register_respond(false, $e->getMessage(), [], 422);
}

// ------------------------------------------------------------
// STEP 2: Encrypt sensitive fields, hash the password, save to DB
// ------------------------------------------------------------
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
        'username'       => $username,
        'slug'           => $slug,
        'account_type'   => $accountType,
        'password_hash'  => $passwordHash,
        'first_name_enc' => $firstNameEnc,
        'last_name_enc'  => $lastNameEnc,
        'email_enc'      => $emailEnc,
        'email_hash'     => $emailHash,
        'avatar_path'    => $avatarRelativePath,
    ]);

    $newUserId = (int) $pdo->lastInsertId();

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('RavenWarp REGISTER DB ERROR: ' . $e->getMessage());
    rw_register_respond(false, 'A server error occurred while creating your account. Please try again shortly.', [], 500);
}

// ------------------------------------------------------------
// STEP 3: Send a welcome email (professional tier only — anonymous
// accounts have no email on file to send to)
// ------------------------------------------------------------
if ($accountType === 'professional' && $email !== '') {
    $safeFirstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
    $safeUsername  = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . RW_MAIL_FROM_NAME . ' <' . RW_MAIL_FROM . '>',
    ];

    $body = "
        <div style=\"font-family:sans-serif; max-width:520px; margin:auto;\">
            <h2 style=\"color:#0d1326;\">Welcome to RavenWarp, {$safeFirstName}! 🐦‍⬛</h2>
            <p>Your account <strong>{$safeUsername}</strong> has just taken flight. We're genuinely glad you're here.</p>
            <p>RavenWarp is still early in its journey — built in the open, secured from the ground up, and shaped
               as much by the people who join it as by the code behind it. That now includes you.</p>
            <p>Head over to your dashboard any time to fill out your profile, connect with others, and start
               earning your first badges:</p>
            <p><a href=\"https://ravenwarp.beardedviking.org/users/dashboard.php\">Go to your Dashboard →</a></p>
            <p style=\"color:#8d97b3; font-size:0.9em;\">Fly far, fly true.<br>— The RavenWarp Team</p>
        </div>
    ";

    @mail($email, 'Welcome to RavenWarp 🐦‍⬛', $body, implode("\r\n", $headers));
}

// ------------------------------------------------------------
// STEP 4: Log the user in and hand back the redirect target
// ------------------------------------------------------------
rw_set_user_session($newUserId, $username, 'member');

rw_register_respond(true, 'Account created! Redirecting you to your dashboard…', [
    'redirect' => '/users/dashboard.php',
]);