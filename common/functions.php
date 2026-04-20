<?php
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

function formatPrice($amount) {
    global $site_settings;
    $symbol = $site_settings['currency_symbol'] ?? '₹';
    return $symbol . number_format((float)$amount, 2);
}

function getCartCount() {
    return (isset($_SESSION['cart'])) ? count($_SESSION['cart']) : 0;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}
?>