<?php
/**
 * ============================================================
 *  RavenWarp :: contact-process.php
 * ------------------------------------------------------------
 *  AJAX-only endpoint for the contact form on contact.php.
 *
 *  FLOW:
 *   1. Validate the request (CSRF token, required fields, honeypot).
 *   2. Encrypt sender email + message BEFORE persisting.
 *   3. Store a record in contact_messages (for the admin panel).
 *   4. Email the submission to info@beardedviking.org.
 *   5. Respond with JSON — this endpoint never renders HTML.
 * ============================================================
 */

define('RAVENWARP_APP', true);

require_once __DIR__ . '/includes/cookies.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/encryption.php';

header('Content-Type: application/json; charset=utf-8');

function rw_contact_respond(bool $success, string $message, int $httpStatus = 200): never
{
    http_response_code($httpStatus);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// ------------------------------------------------------------
// 1. Method check
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rw_contact_respond(false, 'Invalid request method.', 405);
}

// Honeypot — real visitors never fill this in.
if (!empty($_POST['website'] ?? '')) {
    rw_contact_respond(true, 'Message sent — thank you!');
}

// ------------------------------------------------------------
// 2. CSRF validation
// ------------------------------------------------------------
if (!rw_validate_csrf($_POST['csrf_token'] ?? null)) {
    rw_contact_respond(false, 'Your session expired — please refresh the page and try again.', 419);
}

// ------------------------------------------------------------
// 3. Basic rate limiting — one submission per 60 seconds per session
// ------------------------------------------------------------
$now = time();
if (!empty($_SESSION['rw_last_contact_at']) && ($now - $_SESSION['rw_last_contact_at']) < 60) {
    rw_contact_respond(false, 'Please wait a moment before sending another message.', 429);
}

// ------------------------------------------------------------
// 4. Input validation
// ------------------------------------------------------------
$senderName  = trim((string) ($_POST['sender_name'] ?? ''));
$senderEmail = trim((string) ($_POST['sender_email'] ?? ''));
$subject     = trim((string) ($_POST['subject'] ?? ''));
$message     = trim((string) ($_POST['message'] ?? ''));

if ($senderName === '' || mb_strlen($senderName) > 100) {
    rw_contact_respond(false, 'Please enter a valid name.', 422);
}
if (!filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
    rw_contact_respond(false, 'Please enter a valid email address.', 422);
}
if ($subject === '' || mb_strlen($subject) > 150) {
    rw_contact_respond(false, 'Please enter a subject (up to 150 characters).', 422);
}
if ($message === '' || mb_strlen($message) > 5000) {
    rw_contact_respond(false, 'Please enter a message (up to 5000 characters).', 422);
}

// Strip newlines from header-eligible fields to prevent email header injection.
$senderName  = str_replace(["\r", "\n"], '', $senderName);
$senderEmail = str_replace(["\r", "\n"], '', $senderEmail);
$subject     = str_replace(["\r", "\n"], '', $subject);

// ------------------------------------------------------------
// 5. Encrypt sensitive fields BEFORE they touch the database
// ------------------------------------------------------------
$emailHash = rw_lookup_hash($senderEmail);
$ipHash    = rw_lookup_hash($_SERVER['REMOTE_ADDR'] ?? 'unknown');

try {
    $pdo = rw_db();

    $encryptedEmail   = rw_encrypt($senderEmail);
    $encryptedMessage = rw_encrypt($message);

    $insertStmt = $pdo->prepare(
        'INSERT INTO contact_messages (sender_name, sender_email_enc, sender_email_hash, subject, message_enc, ip_hash)
         VALUES (:name, :email_enc, :email_hash, :subject, :message_enc, :ip_hash)'
    );
    $insertStmt->execute([
        'name'        => $senderName,
        'email_enc'   => $encryptedEmail,
        'email_hash'  => $emailHash,
        'subject'     => $subject,
        'message_enc' => $encryptedMessage,
        'ip_hash'     => $ipHash,
    ]);
} catch (PDOException $e) {
    error_log('RavenWarp CONTACT DB ERROR: ' . $e->getMessage());
    rw_contact_respond(false, 'A server error occurred while sending your message. Please try again shortly.', 500);
}

$_SESSION['rw_last_contact_at'] = $now;

// ------------------------------------------------------------
// 6. Email the submission to the lead engineer
//    (plaintext used only transiently here, never logged/stored)
// ------------------------------------------------------------
$safeName    = htmlspecialchars($senderName, ENT_QUOTES, 'UTF-8');
$safeSubject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: ' . RW_MAIL_FROM_NAME . ' <' . RW_MAIL_FROM . '>',
    'Reply-To: ' . $senderName . ' <' . $senderEmail . '>',
];

$emailBody = "
    <p>New contact form submission from RavenWarp:</p>
    <ul>
        <li><strong>Name:</strong> {$safeName}</li>
        <li><strong>Email:</strong> " . htmlspecialchars($senderEmail, ENT_QUOTES, 'UTF-8') . "</li>
        <li><strong>Subject:</strong> {$safeSubject}</li>
    </ul>
    <p><strong>Message:</strong></p>
    <p>{$safeMessage}</p>
";

@mail(RW_LEAD_ENGINEER_EMAIL, 'New Contact Form Submission: ' . $subject, $emailBody, implode("\r\n", $headers));

// ------------------------------------------------------------
// 7. Success
// ------------------------------------------------------------
rw_contact_respond(true, "Thanks {$senderName} — your message is on its way! We'll get back to you soon.");