<?php
/**
 * ============================================================
 *  RavenWarp :: includes/encryption.php
 * ------------------------------------------------------------
 *  Field-level encryption for sensitive data at rest, using
 *  AES-256-GCM (authenticated encryption — it detects tampering,
 *  not just confidentiality). Every encrypted value stores its
 *  own random IV and auth tag alongside the ciphertext, so a
 *  single compromised key can't be used to silently forge data.
 *
 *  This is deliberately generic (rw_encrypt / rw_decrypt) so it
 *  can protect any sensitive column going forward — vote records
 *  today, private messages or profile fields tomorrow.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    http_response_code(403);
    die('Direct access is not permitted.');
}

require_once __DIR__ . '/config.php';

const RW_CIPHER = 'aes-256-gcm';

/**
 * Encrypts a plaintext string for storage.
 * Returns a single base64 string packing [iv][tag][ciphertext]
 * together, so only one database column is needed per field.
 *
 * @throws RuntimeException if the encryption key isn't configured.
 */
function rw_encrypt(string $plaintext): string
{
    $key = rw_encryption_key();

    $ivLength = openssl_cipher_iv_length(RW_CIPHER);
    $iv = random_bytes($ivLength);
    $tag = '';

    $ciphertext = openssl_encrypt(
        $plaintext,
        RW_CIPHER,
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    if ($ciphertext === false) {
        throw new RuntimeException('RavenWarp encryption failed.');
    }

    // Pack iv + tag + ciphertext together, then base64 for safe DB storage.
    return base64_encode($iv . $tag . $ciphertext);
}

/**
 * Decrypts a value previously produced by rw_encrypt().
 * Returns null (rather than throwing) on tampered/corrupt data,
 * so a bad row can't take down an entire admin listing page.
 */
function rw_decrypt(string $packedValue): ?string
{
    $key = rw_encryption_key();
    $raw = base64_decode($packedValue, true);

    if ($raw === false) {
        return null;
    }

    $ivLength = openssl_cipher_iv_length(RW_CIPHER);
    $tagLength = 16; // GCM auth tag is always 16 bytes

    if (strlen($raw) < $ivLength + $tagLength) {
        return null;
    }

    $iv         = substr($raw, 0, $ivLength);
    $tag        = substr($raw, $ivLength, $tagLength);
    $ciphertext = substr($raw, $ivLength + $tagLength);

    $plaintext = openssl_decrypt(
        $ciphertext,
        RW_CIPHER,
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    return $plaintext === false ? null : $plaintext;
}

/**
 * One-way lookup hash — used when you need to check for
 * duplicates (e.g. "has this email already voted?") WITHOUT
 * ever decrypting stored data to compare it. Deterministic by
 * design (same input always produces the same hash), which is
 * exactly why it must never be used for anything you'd want
 * to keep unlinkable, like passwords.
 */
function rw_lookup_hash(string $value): string
{
    if (RW_HASH_PEPPER === '') {
        throw new RuntimeException('RavenWarp: RW_HASH_PEPPER is not configured.');
    }
    return hash('sha256', strtolower(trim($value)) . RW_HASH_PEPPER);
}

/**
 * Resolves and validates the raw encryption key from config.
 */
function rw_encryption_key(): string
{
    if (RW_ENCRYPTION_KEY === '') {
        throw new RuntimeException('RavenWarp: RW_ENCRYPTION_KEY is not configured.');
    }

    $key = base64_decode(RW_ENCRYPTION_KEY, true);

    if ($key === false || strlen($key) !== 32) {
        throw new RuntimeException('RavenWarp: RW_ENCRYPTION_KEY must be a base64-encoded 32-byte key.');
    }

    return $key;
}