<?php
/**
 * LOCKINGSTYLE - Technical Audit & integrity Monitor
 */

function performTechAudit() {
    global $conn;
    
    $report = [
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'mysql_stats' => mysqli_get_server_info($conn),
        'total_storage_usage' => getFolderSize('uploads'),
        'active_sessions' => count(glob(session_save_path() . '/*'))
    ];

    $log_data = mysqli_real_escape_string($conn, json_encode($report));
    mysqli_query($conn, "INSERT INTO audit_logs (admin_name, action_type, description) 
                         VALUES ('SYSTEM_DAEMON', 'HEALTH_CHECK', '$log_data')");
}

function getFolderSize($dir) {
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
        $size += $file->getSize();
    }
    return round($size / 1024 / 1024, 2) . ' MB';
}
?>