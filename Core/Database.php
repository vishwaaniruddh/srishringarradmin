<?php
namespace Core;

class Database {
    private static $instances = [];

    public static function getConnection($type = 'con') {
        if (isset(self::$instances[$type]) && !@mysqli_ping(self::$instances[$type])) {
            unset(self::$instances[$type]); // Connection died, clear it so it reconnects
        }

        if (!isset(self::$instances[$type]) || !self::$instances[$type]) {
            global $con, $con3;
            require_once(__DIR__ . '/../../API/config.php');
            
            if ($type === 'con' || $type === 'conn') {
                if (isset($GLOBALS['con']) && $GLOBALS['con']) self::$instances['con'] = $GLOBALS['con'];
                else if (isset($con) && $con) self::$instances['con'] = $con;
            } else if ($type === 'con3') {
                if (isset($GLOBALS['con3']) && $GLOBALS['con3']) {
                    self::$instances['con3'] = $GLOBALS['con3'];
                } else if (isset($con3) && $con3) {
                    self::$instances['con3'] = $con3;
                } else {
                    $is_local = isset($_SERVER['HTTP_HOST']) && (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']) || strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0);
                    $con3_conn = $is_local ? @mysqli_connect("localhost", "root", "", "u464193275_srishringarr") : @mysqli_connect("localhost", "u464193275_sarmicropos", "Mypos1234", "u464193275_srishringarr");
                    $GLOBALS['con3'] = $con3_conn;
                    self::$instances['con3'] = $con3_conn;
                }
            }

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
