<?php

namespace App;

class ConfigFile
{
    private static ?array $config = null;
    private static bool $configLoaded = false;

    public static function get(?string $key = null, $default = null)
    {
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        if (is_null(self::$config)) {
            return null;
        }

        if (is_null($key)) {
            return self::$config;
        }

        return self::$config[$key] ?? $default;
    }

    public static function set(string $key, $value)
    {
        // really should only be used in testing
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        self::$config[$key] = $value;
    }

    public static function remove(string $key)
    {
        // really should only be used in testing
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        unset(self::$config[$key]);
    }

    public static function reset()
    {
        // really should only be used in testing
        self::$config = null;
        self::$configLoaded = false;
    }

    private static function loadConfig()
    {
        $configFile = base_path() . '/../auto-config.inc.php';
        if (file_exists($configFile)) {
            defined('BMLT_EXEC') or define('BMLT_EXEC', 1);
            require($configFile);
        }

        self::$config = [
            'db_database' => env('DB_DATABASE') ?? $dbName ?? null,
            'db_username' => env('DB_USERNAME') ?? $dbUsername ?? null,
            'db_password' => env('DB_PASSWORD') ?? $dbPassword ?? null,
            'db_host' => env('DB_HOST') ?? $dbHost ?? null,
            'db_prefix' => env('DB_PREFIX') ?? $dbPrefix ?? null,
            'aggregator_mode_enabled' => $aggregator_mode_enabled ?? false,
            'aggregator_max_geo_width_km' => isset($aggregator_max_geo_width_km) && is_numeric($aggregator_max_geo_width_km) ? floatval($aggregator_max_geo_width_km) : 1000,
        ];

        self::$configLoaded = true;
    }
}
