<?php
if(!function_exists('sanitize')) {
    function sanitize($data) {
        global $conn;
        return mysqli_real_escape_string($conn, trim($data));
    }
}

if(!function_exists('formatPrice')) {
    function formatPrice($amount) {
        global $site_settings;
        $symbol = $site_settings['currency_symbol'] ?? '₹';
        return $symbol . ' ' . number_format((float)$amount, 2);
    }
}

if(!function_exists('getCartCount')) {
    function getCartCount() {
        return (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) ? count($_SESSION['cart']) : 0;
    }
}

if(!function_exists('redirect')) {
    function redirect($url) {
        header("Location: " . $url);
        exit();
    }
}

if(!function_exists('logAudit')) {
    function logAudit($action, $details) {
        global $conn;
        $admin_id = $_SESSION['admin_id'] ?? 0;
        $admin_name = $_SESSION['admin_name'] ?? 'System';
        $ip = $_SERVER['REMOTE_ADDR'];
        $action = sanitize($action);
        $details = sanitize($details);
        $conn->query("INSERT INTO audit_logs (admin_id, admin_name, action_type, description, ip_address) 
                      VALUES ('$admin_id', '$admin_name', '$action', '$details', '$ip')");
    }
}

if(!function_exists('showAlert')) {
    function showAlert() {
        if (isset($_SESSION['success'])) {
            echo '<div class="bg-emerald-500 text-black p-4 mb-4 rounded-2xl font-bold text-xs uppercase animate-pulse">'.$_SESSION['success'].'</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="bg-red-500 text-white p-4 mb-4 rounded-2xl font-bold text-xs uppercase">'.$_SESSION['error'].'</div>';
            unset($_SESSION['error']);
        }
    }
}
?>