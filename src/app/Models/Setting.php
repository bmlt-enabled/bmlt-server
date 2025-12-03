<?php

namespace App\Models;

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
     * Get the value attribute with JSON decoding.
     */
    public function getValueAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        return json_decode($value, true);
    }

    /**
     * Set the value attribute with JSON encoding.
     * Converts values to the correct type before storing.
     */
    public function setValueAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['value'] = null;
            return;
        }

        $type = $this->type ?? self::SETTING_TYPES[$this->name] ?? self::TYPE_STRING;

        $value = $this->convertToType($value, $type);

        $this->attributes['value'] = json_encode($value);
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
     * Get typed value for a setting key.
     */
    public function getTypedValue()
    {
        return $this->value;
    }

    // TODO: REMOVE BEFORE MERGE - This method is only kept for testing the migration.
    // Once tests are verified, this method and readLegacyConfigFile() should be removed.
    /**
     * Migrate settings from legacy config file to database.
     * Reads from: 1) environment variables, 2) auto-config.inc.php file, 3) defaults.
     * Always overwrites existing settings (for use in migrations).
     *
     */
    public static function migrateLegacyConfig(): void
    {
        $legacyConfig = self::readLegacyConfigFile();
        $envConfig = self::readFromEnvironment();

        $settingsToMigrate = array_keys(self::SETTING_TYPES);

        foreach ($settingsToMigrate as $key) {
            // Priority: environment variables > legacy file > defaults
            $value = $envConfig[$key] ?? $legacyConfig[$key] ?? self::SETTING_DEFAULTS[$key];
            $type = self::SETTING_TYPES[$key];
            self::updateOrCreate(
                ['name' => $key],
                ['type' => $type, 'value' => $value]
            );
        }
    }

    /**
     * Sync settings from environment variables to database.
     * Reads from: 1) environment variables, 2) defaults.
     *
     * @param bool $force If false, skip settings that already exist in database
     * @return array ['synced' => [...], 'skipped' => [...], 'actions' => [...]]
     */
    public static function syncFromEnvironment(bool $force = false): array
    {
        $envConfig = self::readFromEnvironment();
        $synced = [];
        $skipped = [];
        $actions = [];

        foreach (array_keys(self::SETTING_TYPES) as $key) {
            $existing = self::where('name', $key)->first();

            if ($existing && !$force) {
                $skipped[$key] = $existing->value;
                continue;
            }

            // Priority: environment variables > defaults
            $value = $envConfig[$key] ?? self::SETTING_DEFAULTS[$key];
            $type = self::SETTING_TYPES[$key];

            self::updateOrCreate(
                ['name' => $key],
                ['type' => $type, 'value' => $value]
            );

            $synced[$key] = $value;
            $actions[$key] = $existing ? 'Updated' : 'Created';
        }

        return [
            'synced' => $synced,
            'skipped' => $skipped,
            'actions' => $actions,
            'synced_count' => count($synced),
            'skipped_count' => count($skipped),
        ];
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

    // TODO: REMOVE BEFORE MERGE - This method is only kept for testing the migration.
    // Once tests are verified, this method should be removed.
    /**
     * Read settings directly from auto-config.inc.php file.
     * Returns values with camelCase keys matching Setting model constants.
     * In tests, global variables can be set directly without requiring the file.
     *
     */
    private static function readLegacyConfigFile(): array
    {
        // Declare all possible global variables so they're accessible
        global $gkey, $change_depth_for_meetings, $default_sort_key, $comdef_global_language;
        global $default_duration_time, $region_bias, $comdef_distance_units;
        global $meeting_states_and_provinces, $meeting_counties_and_sub_provinces;
        global $search_spec_map_center, $number_of_meetings_for_auto;
        global $auto_geocoding_enabled, $county_auto_geocoding_enabled, $zip_auto_geocoding_enabled;
        global $g_defaultClosedStatus, $g_enable_language_selector;
        global $aggregator_mode_enabled, $aggregator_max_geo_width_km;
        global $g_include_service_body_email_in_semantic, $bmlt_title, $bmlt_notice, $format_lang_names;

        // If not in testing and file exists, load it
        $legacyConfigFile = base_path() . '/../auto-config.inc.php';
        if (env('APP_ENV') !== 'testing' && file_exists($legacyConfigFile)) {
            defined('BMLT_EXEC') or define('BMLT_EXEC', 1);
            require($legacyConfigFile);
        }
        // In testing, globals are set directly by tests

        $config = [];

        if (isset($gkey)) {
            $config['googleApiKey'] = $gkey;
        }
        if (isset($change_depth_for_meetings)) {
            $config['changeDepthForMeetings'] = $change_depth_for_meetings;
        }
        if (isset($default_sort_key)) {
            $config['defaultSortKey'] = $default_sort_key;
        }
        if (isset($comdef_global_language)) {
            $config['language'] = $comdef_global_language;
        }
        if (isset($default_duration_time)) {
            $config['defaultDurationTime'] = $default_duration_time;
        }
        if (isset($region_bias)) {
            $config['regionBias'] = $region_bias;
        }
        if (isset($comdef_distance_units)) {
            $config['distanceUnits'] = $comdef_distance_units;
        }
        if (isset($meeting_states_and_provinces)) {
            $config['meetingStatesAndProvinces'] = $meeting_states_and_provinces;
        }
        if (isset($meeting_counties_and_sub_provinces)) {
            $config['meetingCountiesAndSubProvinces'] = $meeting_counties_and_sub_provinces;
        }
        if (isset($search_spec_map_center)) {
            if (isset($search_spec_map_center['longitude'])) {
                $config['searchSpecMapCenterLongitude'] = $search_spec_map_center['longitude'];
            }
            if (isset($search_spec_map_center['latitude'])) {
                $config['searchSpecMapCenterLatitude'] = $search_spec_map_center['latitude'];
            }
            if (isset($search_spec_map_center['zoom'])) {
                $config['searchSpecMapCenterZoom'] = $search_spec_map_center['zoom'];
            }
        }
        if (isset($number_of_meetings_for_auto)) {
            $config['numberOfMeetingsForAuto'] = $number_of_meetings_for_auto;
        }
        if (isset($auto_geocoding_enabled)) {
            $config['autoGeocodingEnabled'] = $auto_geocoding_enabled;
        }
        if (isset($county_auto_geocoding_enabled)) {
            $config['countyAutoGeocodingEnabled'] = $county_auto_geocoding_enabled;
        }
        if (isset($zip_auto_geocoding_enabled)) {
            $config['zipAutoGeocodingEnabled'] = $zip_auto_geocoding_enabled;
        }
        if (isset($g_defaultClosedStatus)) {
            $config['defaultClosedStatus'] = $g_defaultClosedStatus;
        }
        if (isset($g_enable_language_selector)) {
            $config['enableLanguageSelector'] = $g_enable_language_selector;
        }
        if (isset($aggregator_mode_enabled)) {
            $config['aggregatorModeEnabled'] = $aggregator_mode_enabled;
        }
        if (isset($aggregator_max_geo_width_km)) {
            $config['aggregatorMaxGeoWidthKm'] = $aggregator_max_geo_width_km;
        }
        if (isset($g_include_service_body_email_in_semantic)) {
            $config['includeServiceBodyEmailInSemantic'] = $g_include_service_body_email_in_semantic;
        }
        if (isset($bmlt_title)) {
            $config['bmltTitle'] = $bmlt_title;
        }
        if (isset($bmlt_notice)) {
            $config['bmltNotice'] = $bmlt_notice;
        }
        if (isset($format_lang_names)) {
            $config['formatLangNames'] = $format_lang_names;
        }

        return $config;
    }
}
