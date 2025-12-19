<?php

namespace App;

use App\Models\Setting;

abstract class ConfigBase
{
    abstract public static function get(?string $name = null, $default = null);
    abstract public static function set(string $name, $value);
    abstract public static function reset(): void;

    abstract protected static function getSettingType(string $name): string;

    public static function fromEnv(string $name): mixed
    {
        $envName = strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

        $value = $_SERVER[$envName] ?? env($envName);

        if (is_null($value) || $value === '') {
            return null;
        }

        $type = static::getSettingType($name);
        return match ($type) {
            Setting::TYPE_INT => (int)$value,
            Setting::TYPE_FLOAT => (float)$value,
            Setting::TYPE_BOOL => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            Setting::TYPE_ARRAY => static::parseEnvArray($value),
            default => $value,
        };
    }

    private static function parseEnvArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (is_string($value) && $value !== '') {
            return array_map('trim', explode(',', $value));
        }

        return [];
    }
}
