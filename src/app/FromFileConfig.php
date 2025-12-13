<?php

namespace App;

class FromFileConfig extends ConfigBase
{
    private static ?array $config = null;
    private static bool $configLoaded = false;

    public static function get(?string $name = null, $default = null)
    {
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        if (is_null(self::$config)) {
            return null;
        }

        if (is_null($name)) {
            return self::$config;
        }

        return self::$config[$name] ?? $default;
    }

    public static function set(string $name, $value)
    {
        // should only be used in testing
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        self::$config[$name] = $value;
    }

    public static function reset(): void
    {
        // should only be used in testing
        self::$config = null;
        self::$configLoaded = false;
    }

    private static function loadConfig(): void
    {
        $configFile = base_path() . '/../auto-config.inc.php';
        if (file_exists($configFile)) {
            defined('BMLT_EXEC') or define('BMLT_EXEC', 1);
            require($configFile);
        }

        $aggregator_max_geo_width_km = self::fromEnv('aggregator_max_geo_width_km') ?? $aggregator_max_geo_width_km ?? null;

        self::$config = [
            'db_database' => self::fromEnv('db_database') ?? $dbName ?? null,
            'db_username' => self::fromEnv('db_username') ?? $dbUsername ?? null,
            'db_password' => self::fromEnv('db_password') ?? $dbPassword ?? null,
            'db_host' => self::fromEnv('db_host') ?? $dbHost ?? null,
            'db_prefix' => self::fromEnv('db_prefix') ?? $dbPrefix ?? null,
            'aggregator_mode_enabled' => boolval(self::fromEnv('aggregator_mode_enabled') ?? $aggregator_mode_enabled ?? false),
            'aggregator_max_geo_width_km' => is_numeric($aggregator_max_geo_width_km) ? floatval($aggregator_max_geo_width_km) : 1000,
        ];

        self::$configLoaded = true;
    }
}
