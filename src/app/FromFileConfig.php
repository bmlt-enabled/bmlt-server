<?php

namespace App;

use App\Models\Setting;

class FromFileConfig extends ConfigBase
{
    private static ?array $config = null;
    private static bool $configLoaded = false;

    protected static function getSettingType(string $name): string
    {
        $types = [
            'db_database' => Setting::TYPE_STRING,
            'db_username' => Setting::TYPE_STRING,
            'db_password' => Setting::TYPE_STRING,
            'db_host' => Setting::TYPE_STRING,
            'db_prefix' => Setting::TYPE_STRING,
            'aggregator_mode_enabled' => Setting::TYPE_BOOL,
            'aggregator_max_geo_width_km' => Setting::TYPE_FLOAT,
            'aggregator_user_agent' => Setting::TYPE_STRING,
        ];
        return $types[$name];
    }

    public static function get(?string $name = null, $default = null)
    {
        if (!static::$configLoaded) {
            self::loadConfig();
        }

        if (is_null(static::$config)) {
            return null;
        }

        if (is_null($name)) {
            return static::$config;
        }

        return static::$config[$name] ?? $default;
    }

    public static function set(string $name, $value)
    {
        // should only be used in testing
        if (!static::$configLoaded) {
            static::loadConfig();
        }

        static::$config[$name] = $value;
    }

    public static function reset(): void
    {
        // should only be used in testing
        static::$config = null;
        static::$configLoaded = false;
    }

    private static function loadConfig(): void
    {
        $configFile = base_path() . '/../auto-config.inc.php';
        if (file_exists($configFile)) {
            defined('BMLT_EXEC') or define('BMLT_EXEC', 1);
            require($configFile);
        }

        static::$config = [
            'db_database' => static::fromEnv('db_database') ?? $dbName ?? null,
            'db_username' => static::fromEnv('db_username') ?? $dbUser ?? null,
            'db_password' => static::fromEnv('db_password') ?? $dbPassword ?? null,
            'db_host' => static::fromEnv('db_host') ?? $dbHost ?? null,
            'db_prefix' => static::fromEnv('db_prefix') ?? $dbPrefix ?? null,
            'aggregator_mode_enabled' => static::fromEnv('aggregator_mode_enabled') ?? $aggregator_mode_enabled ?? false,
            'aggregator_max_geo_width_km' => static::fromEnv('aggregator_max_geo_width_km') ?? $aggregator_max_geo_width_km ?? null,
            'aggregator_user_agent' => static::fromEnv('aggregator_user_agent') ?? $aggregator_user_agent ?? 'Mozilla/5.0 (X11; Linux x86_64; rv:146.0) Gecko/20100101 Firefox/146.0 +aggregator',
        ];

        static::$configLoaded = true;
    }
}
