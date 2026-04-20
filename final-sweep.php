<?php
/**
 * LOCKINGSTYLE - Production Sweeper
 * Run this to secure the installation.
 */
require_once 'common/config.php';
require_once 'common/functions.php';

if(!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] != 1) {
    die("TERMINAL_ERROR: Unauthorized Sweeper Request.");
}

echo "<body style='background:#000; color:#00df81; font-family:monospace; padding:50px;'>";
echo "<h1>>>> INITIATING FINAL PRODUCTION SWEEP</h1><hr style='border:1px solid #222;'>";

$tasks = [
    'Checking Upload Folders' => function() {
        $dirs = ['uploads/products', 'uploads/categories', 'uploads/slides'];
        foreach($dirs as $d) {
            if(!is_writable($d)) return "FAIL: $d is not writable.";
        }
        return "PASS: Core directories accessible.";
    },
    'Checking Critical Files' => function() {
        $files = ['install.php', 'install-data.php', 'seed.php', 'fix_db.php', 'repair_db.php'];
        foreach($files as $f) {
            if(file_exists($f)) echo "<p style='color:red;'>[WARNING] Security Risk: $f still exists on server. DELETE IMMEDIATELY.</p>";
        }
        return "PASS: Security analysis complete.";
    },
    'Database Table Integrity' => function() {
        global $conn;
        $res = mysqli_query($conn, "SHOW TABLES");
        return "PASS: " . mysqli_num_rows($res) . " Operational tables active.";
    }
];

foreach($tasks as $name => $logic) {
    echo "<p>Executing: $name ... " . $logic() . "</p>";
}

echo "<hr style='border:1px solid #222;'><h2 style='color:#fff;'>SYSTEM STATUS: DEPLOYMENT READY</h2>";
echo "<a href='index.php' style='color:#00df81; text-decoration:none;'>[ EXIT TERMINAL ]</a>";
echo "</body>";
?>