<?php

namespace App\Repositories;

use App\Interfaces\SettingRepositoryInterface;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SettingRepository implements SettingRepositoryInterface
{
    // Map of camelCase setting names to SCREAMING_SNAKE_CASE env var names
    private const ENV_MAP = [
        'googleApiKey' => 'GKEY',
        'changeDepthForMeetings' => 'CHANGE_DEPTH_FOR_MEETINGS',
        'defaultSortKey' => 'DEFAULT_SORT_KEY',
        'language' => 'LANGUAGE',
        'defaultDurationTime' => 'DEFAULT_DURATION_TIME',
        'regionBias' => 'REGION_BIAS',
        'distanceUnits' => 'DISTANCE_UNITS',
        'meetingStatesAndProvinces' => 'MEETING_STATES_AND_PROVINCES',
        'meetingCountiesAndSubProvinces' => 'MEETING_COUNTIES_AND_SUB_PROVINCES',
        'searchSpecMapCenterLongitude' => 'SEARCH_SPEC_MAP_CENTER_LONGITUDE',
        'searchSpecMapCenterLatitude' => 'SEARCH_SPEC_MAP_CENTER_LATITUDE',
        'searchSpecMapCenterZoom' => 'SEARCH_SPEC_MAP_CENTER_ZOOM',
        'numberOfMeetingsForAuto' => 'NUMBER_OF_MEETINGS_FOR_AUTO',
        'autoGeocodingEnabled' => 'AUTO_GEOCODING_ENABLED',
        'countyAutoGeocodingEnabled' => 'COUNTY_AUTO_GEOCODING_ENABLED',
        'zipAutoGeocodingEnabled' => 'ZIP_AUTO_GEOCODING_ENABLED',
        'defaultClosedStatus' => 'DEFAULT_CLOSED_STATUS',
        'enableLanguageSelector' => 'ENABLE_LANGUAGE_SELECTOR',
        'aggregatorModeEnabled' => 'AGGREGATOR_MODE_ENABLED',
        'aggregatorMaxGeoWidthKm' => 'AGGREGATOR_MAX_GEO_WIDTH_KM',
        'includeServiceBodyEmailInSemantic' => 'INCLUDE_SERVICE_BODY_EMAIL_IN_SEMANTIC',
        'bmltTitle' => 'BMLT_TITLE',
        'bmltNotice' => 'BMLT_NOTICE',
        'formatLangNames' => 'FORMAT_LANG_NAMES',
    ];

    public function getByKey(string $key): ?Setting
    {
        return Setting::query()->where('name', $key)->first();
    }

    /**
     * Get a setting value with environment variable override.
     * Priority: environment variable > database > default
     *
     * @param string $name Setting name (camelCase)
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public function getValue(string $name, $default = null)
    {
        // Check environment variables first
        $envValue = $this->getFromEnvironment($name);
        if ($envValue !== null) {
            return $envValue;
        }

        // Only query database if the settings table exists
        // This is necessary during bootstrapping: config/database.php -> legacy_config() -> LegacyConfig::get()
        // At that point, migrations haven't run yet, so the table doesn't exist
        if (DB::connection()->getSchemaBuilder()->hasTable('settings')) {
            $setting = $this->getByKey($name);
            if ($setting) {
                return $setting->value;
            }
        }

        // Fall back to default
        return $default ?? Setting::SETTING_DEFAULTS[$name] ?? null;
    }

    public function getAll(): Collection
    {
        $settings = Setting::all();

        // Apply environment variable overrides
        return $settings->map(function ($setting) {
            $value = $this->getValue($setting->name);
            if ($value !== $setting->value) {
                $setting->value = $value;
            }
            return $setting;
        });
    }

    /**
     * Get setting value from environment variable if it exists.
     */
    private function getFromEnvironment(string $name): mixed
    {
        $envVar = self::ENV_MAP[$name] ?? null;
        if (!$envVar) {
            return null;
        }

        // Check $_SERVER (Docker) or env() (.env file)
        $envValue = $_SERVER[$envVar] ?? env($envVar);

        if ($envValue === null || $envValue === '') {
            return null;
        }

        $type = Setting::SETTING_TYPES[$name];
        return $this->parseEnvValue($envValue, $type);
    }

    /**
     * Parse environment variable value to appropriate type.
     */
    private function parseEnvValue($value, string $type): mixed
    {
        return match ($type) {
            Setting::TYPE_INT => (int)$value,
            Setting::TYPE_FLOAT => (float)$value,
            Setting::TYPE_BOOL => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            Setting::TYPE_ARRAY => $this->parseArrayValue($value),
            default => $value,
        };
    }

    /**
     * Parse array value from comma-separated string or JSON.
     */
    private function parseArrayValue($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        // Try JSON first
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Fall back to comma-separated
        if (is_string($value) && $value !== '') {
            return array_map('trim', explode(',', $value));
        }

        return [];
    }

    public function update(string $key, $value): bool
    {
        $setting = Setting::updateOrCreate(
            ['name' => $key],
            ['value' => $value]
        );

        return $setting->wasRecentlyCreated || $setting->wasChanged();
    }

    public function updateMultiple(array $keyValuePairs): bool
    {
        return DB::transaction(function () use ($keyValuePairs) {
            $success = true;

            foreach ($keyValuePairs as $key => $value) {
                if (!$this->update($key, $value)) {
                    $success = false;
                }
            }

            return $success;
        });
    }
}
