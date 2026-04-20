<?php
/**
 * LOCKINGSTYLE - Global Environment Hardening
 */

// 0 = Production (Safe, No Errors), 1 = Debug (Development mode)
define('SYSTEM_ENV', 0); 

if (SYSTEM_ENV === 0) {
    // 1. Disable all error displays for users
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/system_errors.log');

    // 2. Strict Security Headers
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data: https:;");

} else {
    // Debug Mode Enabled
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

/**
 * Global Minification helper (Simulation)
 * In high scale apps, this would compress the output buffer.
 */
function minifyHTML($buffer) {
    if (SYSTEM_ENV === 0) {
        $search = array('/\n/', '/\r/', '/\t/', '/\s\s+/');
        $replace = array('', '', '', ' ');
        return preg_replace($search, $replace, $buffer);
    }
    return $buffer;
}

// Attach to output buffer if in Production
if (SYSTEM_ENV === 0) {
    ob_start("minifyHTML");
}