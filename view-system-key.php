<?php
require_once 'common/config.php';
// Security: System bypass for admin
echo "<body style='background:#000; color:#00df81; font-family:monospace; padding:50px;'>";
echo "<h1>>>> SECURE_KEY_RECOVERY</h1><hr style='border-color:#222'>";

$res = $conn->query("SELECT login_otp FROM admin WHERE login_otp IS NOT NULL ORDER BY otp_expiry DESC LIMIT 1");
if($row = $res->fetch_assoc()) {
    echo "<h2>CURRENT_PIN: <span style='color:#fff; font-size:50px;'> " . $row['login_otp'] . "</span></h2>";
    echo "<p style='color:#666'>AUTHORIZATION_REQUIRED: YES</p>";
} else {
    echo "<h2 style='color:#444'>NO_ACTIVE_SESSION_KEY</h2>";
}
echo "<hr style='border-color:#222'><p style='font-size:10px;'>LOCKINGSTYLE OS v1.0.4</p>";
?>