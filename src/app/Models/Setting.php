<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['name', 'type', 'value'];

    public const TYPE_STRING = 'string';
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';
    public const TYPE_BOOL = 'bool';
    public const TYPE_ARRAY = 'array';

    public const SETTING_TYPES = [
        'googleApiKey' => self::TYPE_STRING,
        'changeDepthForMeetings' => self::TYPE_INT,
        'defaultSortKey' => self::TYPE_STRING,
        'language' => self::TYPE_STRING,
        'defaultDurationTime' => self::TYPE_STRING,
        'regionBias' => self::TYPE_STRING,
        'distanceUnits' => self::TYPE_STRING,
        'meetingStatesAndProvinces' => self::TYPE_ARRAY,
        'meetingCountiesAndSubProvinces' => self::TYPE_ARRAY,
        'searchSpecMapCenterLongitude' => self::TYPE_FLOAT,
        'searchSpecMapCenterLatitude' => self::TYPE_FLOAT,
        'searchSpecMapCenterZoom' => self::TYPE_INT,
        'numberOfMeetingsForAuto' => self::TYPE_INT,
        'autoGeocodingEnabled' => self::TYPE_BOOL,
        'countyAutoGeocodingEnabled' => self::TYPE_BOOL,
        'zipAutoGeocodingEnabled' => self::TYPE_BOOL,
        'defaultClosedStatus' => self::TYPE_BOOL,
        'enableLanguageSelector' => self::TYPE_BOOL,
        'aggregatorModeEnabled' => self::TYPE_BOOL,
        'aggregatorMaxGeoWidthKm' => self::TYPE_FLOAT,
        'includeServiceBodyEmailInSemantic' => self::TYPE_BOOL,
        'bmltTitle' => self::TYPE_STRING,
        'bmltNotice' => self::TYPE_STRING,
        'formatLangNames' => self::TYPE_ARRAY,
    ];

    public const SETTING_DEFAULTS = [
        'googleApiKey' => '',
        'changeDepthForMeetings' => 0,
        'defaultSortKey' => null,
        'language' => 'en',
        'defaultDurationTime' => '01:00',
        'regionBias' => 'us',
        'distanceUnits' => 'mi',
        'meetingStatesAndProvinces' => [],
        'meetingCountiesAndSubProvinces' => [],
        'searchSpecMapCenterLongitude' => -118.563659,
        'searchSpecMapCenterLatitude' => 34.235918,
        'searchSpecMapCenterZoom' => 6,
        'numberOfMeetingsForAuto' => 10,
        'autoGeocodingEnabled' => true,
        'countyAutoGeocodingEnabled' => false,
        'zipAutoGeocodingEnabled' => false,
        'defaultClosedStatus' => true,
        'enableLanguageSelector' => false,
        'aggregatorModeEnabled' => false,
        'aggregatorMaxGeoWidthKm' => 160.0,
        'includeServiceBodyEmailInSemantic' => false,
        'bmltTitle' => 'BMLT Administration',
        'bmltNotice' => '',
        'formatLangNames' => [],
    ];

    /**
     * Automatically JSON encodes/decodes and converts types.
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_null($value) ? null : json_decode($value, true),
            set: function ($value) {
                if (is_null($value)) {
                    return null;
                }

                $type = $this->type ?? self::SETTING_TYPES[$this->name] ?? self::TYPE_STRING;
                $value = $this->convertToType($value, $type);

                return json_encode($value);
            },
        );
    }

    /**
     * Convert a value to the specified type.
     */
    private function convertToType($value, string $type)
    {
        switch ($type) {
            case self::TYPE_ARRAY:
                if (is_array($value)) {
                    return $value;
                }
                if (is_string($value)) {
                    return $value === '' ? [] : array_map('trim', explode(',', $value));
                }
                return [];

            case self::TYPE_BOOL:
                return (bool) $value;

            case self::TYPE_INT:
                return (int) $value;

            case self::TYPE_FLOAT:
                return (float) $value;

            case self::TYPE_STRING:
            default:
                return (string) $value;
        }
    }


    /**
     * Get a setting value with environment variable override.
     * Priority: environment variable > database > default
     *
     * @param string $name Setting name (camelCase)
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        // Check environment variables first
        $envConfig = self::readFromEnvironment();
        if (isset($envConfig[$name])) {
            return $envConfig[$name];
        }

        $setting = self::where('name', $name)->first();
        if ($setting) {
            return $setting->value;
        }

        // Fall back to default
        return $default ?? self::SETTING_DEFAULTS[$name] ?? null;
    }


    /**
     * Read settings from environment variables.
     * Returns values with camelCase keys matching Setting model constants.
     */
    private static function readFromEnvironment(): array
    {
        $config = [];

        // Map of camelCase setting names to SCREAMING_SNAKE_CASE env var names
        $envMap = [
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

        foreach ($envMap as $settingKey => $envVar) {
            // Check $_SERVER (Docker) or env() (.env file)
            $envValue = $_SERVER[$envVar] ?? env($envVar);

            if ($envValue !== null && $envValue !== '') {
                $type = self::SETTING_TYPES[$settingKey];
                $config[$settingKey] = self::parseEnvValue($envValue, $type);
            }
        }

        return $config;
    }

    /**
     * Parse environment variable value to appropriate type.
     */
    private static function parseEnvValue($value, string $type)
    {
        return match ($type) {
            self::TYPE_INT => (int)$value,
            self::TYPE_FLOAT => (float)$value,
            self::TYPE_BOOL => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            self::TYPE_ARRAY => self::parseArrayValue($value),
            default => $value,
        };
    }

    /**
     * Parse array value from comma-separated string or JSON.
     */
    private static function parseArrayValue($value): array
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
}
