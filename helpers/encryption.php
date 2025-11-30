<?php
/**
 * Encryption Helper for Vendor Slugs
 * Provides simple URL-safe encoding/decoding for vendor slugs
 */

/**
 * Encrypt a vendor slug for use in URLs
 * @param string $slug The vendor slug to encrypt
 * @return string URL-safe encrypted slug
 */
function encrypt_slug($slug) {
    // Use base64 encoding for URL-safe encryption
    // Add a simple salt for obfuscation
    $salted = "preorda_" . $slug . "_vendor";
    return rtrim(strtr(base64_encode($salted), '+/', '-_'), '=');
}

/**
 * Decrypt a vendor slug from URL parameter
 * @param string $encrypted The encrypted slug from URL
 * @return string|false The original slug or false on failure
 */
function decrypt_slug($encrypted) {
    try {
        // Reverse the base64 encoding
        $decoded = base64_decode(strtr($encrypted, '-_', '+/'));
        
        // Remove the salt
        if (strpos($decoded, 'preorda_') === 0 && strpos($decoded, '_vendor') !== false) {
            $slug = str_replace(['preorda_', '_vendor'], '', $decoded);
            return $slug;
        }
        
        return false;
    } catch (Exception $e) {
        return false;
    }
}
?>
