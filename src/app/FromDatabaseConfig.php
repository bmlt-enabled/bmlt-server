<?php

namespace App;

use App\Repositories\SettingRepository;

class FromDatabaseConfig extends ConfigBase
{
    private static ?array $config = null;
    private static bool $configLoaded = false;

    public static function get(string $name = null, $default = null)
    {
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        if (is_null($name)) {
            return self::$config;
        }

        return self::$config[$name] ?? $default;
    }

    public static function set(string $name, $value)
    {
        // should only be used in testing
        $repository = app(SettingRepository::class);
        $repository->update($name, $value);
        $setting = $repository->getByName($name);
        self::$config[$name] = $setting->value;
    }

    public static function reset(): void
    {
        // should only be used in testing
        self::$config = null;
        self::$configLoaded = false;
    }

    private static function loadConfig(): void
    {
        $repository = app(SettingRepository::class);
        self::$config = $repository->getAll()
            ->mapWithKeys(function ($setting) {
                $value = self::fromEnv($setting->name) ?? $setting->value;
                return [$setting->name => $value];
            })
            ->toArray();

        self::$configLoaded = true;
    }
}
