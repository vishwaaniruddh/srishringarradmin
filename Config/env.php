<?php
/**
 * Environment & Secret Helper
 * This file handles loading sensitive API keys from environment variables
 * or a local secrets file that is not tracked by Git.
 */

function get_config_secret($key, $default = null) {
    // 1. Try system environment variables (Production)
    $val = getenv($key);
    if ($val !== false) return $val;

    // 2. Try local secrets file (Development/Manual Production)
    $secretsPath = __DIR__ . '/secrets.php';
    if (file_exists($secretsPath)) {
        $secrets = include $secretsPath;
        if (isset($secrets[$key])) {
            return $secrets[$key];
        }
    }

    return $default;
}
