<?php

namespace App;

use App\Repositories\SettingRepository;
use Illuminate\Support\Collection;

class FromDatabaseConfig extends ConfigBase
{
    private static ?array $config = null;
    private static ?Collection $settingTypes = null;
    private static bool $configLoaded = false;

    protected static function getSettingType(string $name): string
    {
        if (!static::$configLoaded) {
            static::loadConfig();
        }

        return static::$settingTypes->get($name);
    }

    public static function get(string $name = null, $default = null)
    {
        if (!static::$configLoaded) {
            static::loadConfig();
        }

        if (is_null($name)) {
            return static::$config;
        }

        return static::$config[$name] ?? $default;
    }

    public static function set(string $name, $value)
    {
        // should only be used in testing
        $repository = app(SettingRepository::class);
        $repository->update($name, $value);
        $setting = $repository->getByName($name);
        static::$config[$name] = $setting->value;
    }

    public static function reset(): void
    {
        // should only be used in testing
        static::$config = null;
        static::$settingTypes = null;
        static::$configLoaded = false;
    }

    private static function loadConfig(): void
    {
        static::$configLoaded = true; // we set this first thing to prevent infinite looping when calling into fromEnv
        $repository = app(SettingRepository::class);
        $settings = $repository->getAll();
        static::$settingTypes = $settings->mapWithKeys(fn ($s) => [$s->name => $s->type]);
        static::$config = $settings
            ->mapWithKeys(function ($setting) {
                $value = static::fromEnv($setting->name) ?? $setting->value;
                return [$setting->name => $value];
            })
            ->toArray();
    }
}
