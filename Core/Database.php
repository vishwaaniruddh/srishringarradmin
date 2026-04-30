<?php
namespace Core;

class Database {
    private static $instances = [];

    public static function getConnection($type = 'con') {
        if (!isset(self::$instances[$type])) {
            // Using include instead of require_once to ensure variables are available in this scope
            // even if the file was previously included elsewhere.
            // Include legacy config
            include(__DIR__ . '/../../config.php');
            
            if (isset($con)) self::$instances['con'] = $con;
            if (isset($con3)) self::$instances['con3'] = $con3;

            // Handle lazy loading for remote 'woo' connection
            if ($type === 'woo') {
                if (file_exists(__DIR__ . '/../Config/database.php')) {
                    $creds = include(__DIR__ . '/../Config/database.php');
                    if (is_array($creds)) {
                        $con_woo = @mysqli_connect($creds['host'], $creds['user'], $creds['pass'], $creds['db']);
                        if ($con_woo) {
                            self::$instances['woo'] = $con_woo;
                        }
                    }
                }
            }
        }
        return self::$instances[$type] ?? null;
    }
}
