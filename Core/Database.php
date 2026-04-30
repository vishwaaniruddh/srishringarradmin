<?php
namespace Core;

class Database {
    private static $instances = [];

    public static function getConnection($type = 'con') {
        if (!isset(self::$instances[$type])) {
            // Using include instead of require_once to ensure variables are available in this scope
            // even if the file was previously included elsewhere.
            include(__DIR__ . '/../../config.php');
            
            if (isset($con)) self::$instances['con'] = $con;
            if (isset($con3)) self::$instances['con3'] = $con3;
        }
        return self::$instances[$type] ?? null;
    }
}
