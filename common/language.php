<?php
/**
 * LOCKINGSTYLE - Language Orchestrator
 * This file handles the session-based language switching and dictionary loading.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default Language logic
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en'; // Default to English
}

// Switch Language via GET request
if (isset($_GET['set_lang'])) {
    $requested_lang = $_GET['set_lang'];
    $allowed_langs = ['en', 'hi'];
    if (in_array($requested_lang, $allowed_langs)) {
        $_SESSION['lang'] = $requested_lang;
    }
    // Refresh to apply changes
    $current_url = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: " . $current_url);
    exit();
}

// Load the Dictionary based on current session
if ($_SESSION['lang'] == 'hi') {
    require_once 'dictionary_hi.php';
    $dict = $dict_hi;
} else {
    require_once 'dictionary_en.php';
    $dict = $dict_en;
}

/**
 * The Translate Function (The most used function in the app)
 * Supports fallbacks and dynamic string replacement.
 */
function __($key, $replacements = []) {
    global $dict;
    
    // Check if key exists in the current dictionary
    $string = isset($dict[$key]) ? $dict[$key] : $key;
    
    // Replace placeholders like :name or :count
    if (!empty($replacements)) {
        foreach ($replacements as $placeholder => $value) {
            $string = str_replace(':' . $placeholder, $value, $string);
        }
    }
    
    return $string;
}

/**
 * Helper for echo-ing translations
 */
function _e($key, $replacements = []) {
    echo __($key, $replacements);
}