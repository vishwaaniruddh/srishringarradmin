<?php
namespace Core;

class Database {
    private static $instances = [];

    public static function getConnection($type = 'con') {
        $type = ($type === 'conn') ? 'con' : $type;

        if (isset(self::$instances[$type]) && self::$instances[$type]) {
            if (@mysqli_ping(self::$instances[$type])) {
                return self::$instances[$type];
            }
            @mysqli_close(self::$instances[$type]);
            unset(self::$instances[$type]);
        }

        $httpHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';

        $isProduction = (
            str_contains($httpHost, 'srishringarr.com') || 
            str_contains($httpHost, 'yosshitaneha.com') || 
            str_contains($docRoot, 'u464193275') ||
            (php_sapi_name() !== 'cli' && !str_contains($httpHost, 'localhost') && !str_contains($httpHost, '127.0.0.1') && !empty($httpHost))
        );

        // Turn off automatic mysqli exception/warning reporting during connection attempts
        @mysqli_report(MYSQLI_REPORT_OFF);

        try {
            if ($type === 'con') {
                if ($isProduction) {
                    $c = @mysqli_connect("localhost", "u464193275_srishrinjuser", "9b@hMgk!=zI", "u464193275_srishrinjewels");
                } else {
                    $c = @mysqli_connect("localhost", "root", "", "u464193275_srishrinjewels");
                }
                if ($c && !mysqli_connect_errno()) {
                    @mysqli_set_charset($c, 'utf8mb4');
                    self::$instances['con'] = $c;
                    $GLOBALS['con'] = $c;
                    $GLOBALS['conn'] = $c;
                    return $c;
                }
            } elseif ($type === 'con3') {
                if ($isProduction) {
                    $c3 = @mysqli_connect("localhost", "u464193275_sarmicropos", "Mypos1234", "u464193275_srishringarr");
                    if (!$c3 || mysqli_connect_errno()) {
                        // Fallback to primary production DB credentials
                        $c3 = @mysqli_connect("localhost", "u464193275_srishrinjuser", "9b@hMgk!=zI", "u464193275_srishringarr");
                    }
                } else {
                    $c3 = @mysqli_connect("localhost", "root", "", "u464193275_srishringarr");
                }
                if ($c3 && !mysqli_connect_errno()) {
                    @mysqli_set_charset($c3, 'utf8mb4');
                    self::$instances['con3'] = $c3;
                    $GLOBALS['con3'] = $c3;
                    return $c3;
                }
            } elseif ($type === 'woo') {
                if (file_exists(__DIR__ . '/../Config/database.php')) {
                    $creds = include(__DIR__ . '/../Config/database.php');
                    if (is_array($creds)) {
                        $con_woo = @mysqli_connect($creds['host'], $creds['user'], $creds['pass'], $creds['db']);
                        if ($con_woo && !mysqli_connect_errno()) {
                            @mysqli_set_charset($con_woo, 'utf8mb4');
                            self::$instances['woo'] = $con_woo;
                            return $con_woo;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            error_log("Database connection error [$type]: " . $e->getMessage());
        }

        return self::$instances[$type] ?? null;
    }
}
