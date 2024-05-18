<?php

namespace TetaFramework;

class Config
{
    private static $config;

    public static function get($key, $default = null)
    {
        if (!self::$config) {
            self::$config = require __DIR__ . '/../config/app.php';
        }
        return self::$config[$key] ?? $default;
    }
}
