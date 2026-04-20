<?php
/**
 * LOCKINGSTYLE - High-Density Pulse Engine
 * Enterprise Error Interception & Resource Monitoring
 */

class SystemPulse {
    public static function boot() {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatal']);
    }

    public static function logToPulse($severity, $msg, $file, $line) {
        global $conn;
        if(!$conn) return;

        $msg = mysqli_real_escape_string($conn, $msg);
        $file = mysqli_real_escape_string($conn, $file);
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $uri = $_SERVER['REQUEST_URI'] ?? 'N/A';

        $sql = "INSERT INTO system_errors (severity, message, file_path, line_num, user_ip) 
                VALUES ('$severity', '$msg [$uri]', '$file', '$line', '$ip')";
        mysqli_query($conn, $sql);
    }

    public static function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) return false;
        self::logToPulse('NON-FATAL', $errstr, $errfile, $errline);
        return true;
    }

    public static function handleException($e) {
        self::logToPulse('EXCEPTION', $e->getMessage(), $e->getFile(), $e->getLine());
    }

    public static function handleFatal() {
        $error = error_get_last();
        if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::logToPulse('CRITICAL', $error['message'], $error['file'], $error['line']);
        }
    }

    public static function getResourceUsage() {
        return [
            'ram' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
            'peak_ram' => round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB',
            'load' => function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 'N/A'
        ];
    }
}

SystemPulse::boot();