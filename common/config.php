<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ob_start();

// Database credentials
$host = 'sql100.infinityfree.com';
$user = 'if0_41032782';
$pass = 'D3WW5PxU40n'; 
$db   = 'if0_41032782_love';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("System Offline"); }
$conn->set_charset("utf8mb4");

// SET SITE URL FOR LIVE SERVER
define('SITE_URL', 'http://locking48.xo.je'); 
?>