<?php
/**
 * ============================================================
 *  RavenWarp :: vote-process.php
 * ------------------------------------------------------------
 *  AJAX-only endpoint for the AI Race voting widget on about.php.
 *
 *  FLOW:
 *   1. Validate the request (CSRF token, required fields, honeypot).
 *   2. Reject duplicate votes via a one-way email hash lookup.
 *   3. Encrypt the voter's email, then persist the vote.
 *   4. Send a confirmation email to the voter AND a notification
 *      to the lead engineer (info@beardedviking.org).
 *   5. Respond with JSON — this endpoint never renders HTML.
 * ============================================================
 */

define('RAVENWARP_APP', true);

require_once __DIR__ . '/includes/cookies.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/encryption.php';

header('Content-Type: application/json; charset=utf-8');

/**
 * Small helper so every exit point returns consistent JSON.
 */
function rw_vote_respond(bool $success, string $message, int $httpStatus = 200): never
{
    http_response_code($httpStatus);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// ------------------------------------------------------------
// 1. Method + basic request shape
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rw_vote_respond(false, 'Invalid request method.', 405);
}

// Honeypot field — a real user never fills this in; a bot usually does.
if (!empty($_POST['website'] ?? '')) {
    // Pretend success so the bot doesn't learn anything, but do nothing.
    rw_vote_respond(true, 'Thank you for your vote!');
}

// ------------------------------------------------------------
// 2. CSRF validation
// ------------------------------------------------------------
if (!rw_validate_csrf($_POST['csrf_token'] ?? null)) {
    rw_vote_respond(false, 'Your session expired — please refresh the page and try again.', 419);
}

// ------------------------------------------------------------
// 3. Input validation
// ------------------------------------------------------------
$voterName = trim((string) ($_POST['voter_name'] ?? ''));
$voterEmail = trim((string) ($_POST['voter_email'] ?? ''));
$votedFor = trim((string) ($_POST['voted_for'] ?? ''));

$allowedProjects = ['RavenWarp', 'Nexora', 'Valkyrin', 'SagaSphere']; // NexusValhalla removed — disqualified from the race

if ($voterName === '' || mb_strlen($voterName) > 100) {
    rw_vote_respond(false, 'Please enter a valid name.', 422);
}

if (!filter_var($voterEmail, FILTER_VALIDATE_EMAIL)) {
    rw_vote_respond(false, 'Please enter a valid email address.', 422);
}

if (!in_array($votedFor, $allowedProjects, true)) {
    rw_vote_respond(false, 'Please select a valid project to vote for.', 422);
}

// Strip anything that could be used for email header injection, belt-and-suspenders
// alongside filter_var() above.
$voterName = str_replace(["\r", "\n"], '', $voterName);
$voterEmail = str_replace(["\r", "\n"], '', $voterEmail);

// ------------------------------------------------------------
// 4. Duplicate-vote prevention (session flag + DB-level hash check)
// ------------------------------------------------------------
if (!empty($_SESSION['rw_has_voted'])) {
    rw_vote_respond(false, 'Looks like you\'ve already cast a vote in this race. Thanks for participating!', 409);
}

$emailHash = rw_lookup_hash($voterEmail);
$ipHash = rw_lookup_hash($_SERVER['REMOTE_ADDR'] ?? 'unknown');

try {
    $pdo = rw_db();

    $checkStmt = $pdo->prepare('SELECT id FROM votes WHERE voter_email_hash = :hash LIMIT 1');
    $checkStmt->execute(['hash' => $emailHash]);

    if ($checkStmt->fetch()) {
        rw_vote_respond(false, 'That email address has already voted. One vote per person keeps this race fair!', 409);
    }

    // ------------------------------------------------------------
    // 5. Encrypt sensitive data BEFORE it ever touches the database
    // ------------------------------------------------------------
    $encryptedEmail = rw_encrypt($voterEmail);

    $insertStmt = $pdo->prepare(
        'INSERT INTO votes (voter_name, voter_email_enc, voter_email_hash, voted_for, ip_hash)
         VALUES (:name, :email_enc, :email_hash, :voted_for, :ip_hash)'
    );
    $insertStmt->execute([
        'name'       => $voterName,
        'email_enc'  => $encryptedEmail,
        'email_hash' => $emailHash,
        'voted_for'  => $votedFor,
        'ip_hash'    => $ipHash,
    ]);
} catch (PDOException $e) {
    error_log('RavenWarp VOTE DB ERROR: ' . $e->getMessage());
    rw_vote_respond(false, 'A server error occurred while recording your vote. Please try again shortly.', 500);
}

// Mark this session as having voted (defense-in-depth alongside the DB unique index).
$_SESSION['rw_has_voted'] = true;

// ------------------------------------------------------------
// 6. Send confirmation emails
//    NOTE: the plaintext email is only ever used transiently,
//    right here, for sending — it was already encrypted before
//    being written to disk in step 5, and is never logged.
// ------------------------------------------------------------
$safeName = htmlspecialchars($voterName, ENT_QUOTES, 'UTF-8');
$safeVotedFor = htmlspecialchars($votedFor, ENT_QUOTES, 'UTF-8');

$commonHeaders = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: ' . RW_MAIL_FROM_NAME . ' <' . RW_MAIL_FROM . '>',
];

// --- Email 1: confirmation to the voter ---
$voterSubject = 'Your RavenWarp AI Race vote has been recorded';
$voterBody = "
    <p>Hey {$safeName},</p>
    <p>This confirms your vote for <strong>{$safeVotedFor}</strong> in the RavenWarp AI Build-Off Race has been recorded.</p>
    <p>Thanks for helping decide which AI-built platform crosses the finish line first. Keep an eye on the six-month race progress on the site!</p>
    <p>&mdash; The RavenWarp Team</p>
";
@mail($voterEmail, $voterSubject, $voterBody, implode("\r\n", $commonHeaders));

// --- Email 2: notification to the lead engineer ---
$engineerSubject = 'New RavenWarp AI Race vote cast';
$engineerBody = "
    <p>A new vote was just recorded:</p>
    <ul>
        <li><strong>Voted for:</strong> {$safeVotedFor}</li>
        <li><strong>Voter name:</strong> {$safeName}</li>
        <li><strong>Timestamp:</strong> " . htmlspecialchars(date('Y-m-d H:i:s T'), ENT_QUOTES, 'UTF-8') . "</li>
    </ul>
    <p>(Voter email is encrypted at rest and intentionally omitted from this notification.)</p>
";
@mail(RW_LEAD_ENGINEER_EMAIL, $engineerSubject, $engineerBody, implode("\r\n", $commonHeaders));

// ------------------------------------------------------------
// 7. Success
// ------------------------------------------------------------
rw_vote_respond(true, "Your vote for {$votedFor} has been recorded — check your inbox for confirmation!");