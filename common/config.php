<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

// Database Credentials
$host = 'sql100.infinityfree.com';
$user = 'if0_41032782';
$pass = 'D3WW5PxU40n'; 
$db   = 'if0_41032782_love';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("System Offline"); }
$conn->set_charset("utf8mb4");

// Load Settings
$site_settings = [];
$res = $conn->query("SELECT * FROM settings");
if($res) {
    while($row = $res->fetch_assoc()) {
        $site_settings[$row['setting_key']] = $row['setting_value'];
    }
}
ob_start();
?>