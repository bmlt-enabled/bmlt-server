<?php

namespace App;

use App\Models\Setting;

class LegacyConfig
{
    private static ?array $config = null;
    private static bool $configLoaded = false;

    private const LEGACY_KEY_MAPPING = [
        'gkey' => 'googleApiKey',
        'google_api_key' => 'googleApiKey',
        'change_depth_for_meetings' => 'changeDepthForMeetings',
        'default_sort_key' => 'defaultSortKey',
        'comdef_global_language' => 'language',
        'language' => 'language',
        'default_duration_time' => 'defaultDurationTime',
        'region_bias' => 'regionBias',
        'distance_units' => 'distanceUnits',
        'comdef_distance_units' => 'distanceUnits',
        'meeting_states_and_provinces' => 'meetingStatesAndProvinces',
        'meeting_counties_and_sub_provinces' => 'meetingCountiesAndSubProvinces',
        'search_spec_map_center_longitude' => 'searchSpecMapCenterLongitude',
        'search_spec_map_center_latitude' => 'searchSpecMapCenterLatitude',
        'search_spec_map_center_zoom' => 'searchSpecMapCenterZoom',
        'number_of_meetings_for_auto' => 'numberOfMeetingsForAuto',
        'auto_geocoding_enabled' => 'autoGeocodingEnabled',
        'county_auto_geocoding_enabled' => 'countyAutoGeocodingEnabled',
        'zip_auto_geocoding_enabled' => 'zipAutoGeocodingEnabled',
        'g_defaultClosedStatus' => 'defaultClosedStatus',
        'default_closed_status' => 'defaultClosedStatus',
        'g_enable_language_selector' => 'enableLanguageSelector',
        'enable_language_selector' => 'enableLanguageSelector',
        'aggregator_mode_enabled' => 'aggregatorModeEnabled',
        'aggregator_max_geo_width_km' => 'aggregatorMaxGeoWidthKm',
        'g_include_service_body_email_in_semantic' => 'includeServiceBodyEmailInSemantic',
        'include_service_body_email_in_semantic' => 'includeServiceBodyEmailInSemantic',
        'bmlt_title' => 'bmltTitle',
        'bmlt_notice' => 'bmltNotice',
        'format_lang_names' => 'formatLangNames',
    ];

    public static function get(?string $key = null, $default = null)
    {
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        if (is_null($key)) {
            // Return only DB credentials when getting all config
            return self::$config;
        }

        // DB credential keys come from legacy config file
        $dbCredentialKeys = ['db_database', 'db_username', 'db_password', 'db_host', 'db_prefix'];
        if (in_array($key, $dbCredentialKeys)) {
            return self::$config[$key] ?? $default;
        }

        // Check for in-memory override (used in tests)
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        // Redirect to Setting model which checks: env > database > defaults
        // Use legacy key mapping to find the Setting name
        $settingName = self::LEGACY_KEY_MAPPING[$key] ?? null;
        if ($settingName) {
            return Setting::get($settingName, $default);
        }

        return $default;
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
        $legacyConfigFile = base_path() . '/../auto-config.inc.php';
        if (file_exists($legacyConfigFile)) {
            defined('BMLT_EXEC') or define('BMLT_EXEC', 1);
            require($legacyConfigFile);
        }

        $config = [];

        // Only load DB credentials from auto-config.inc.php
        // All other settings come from the database via Setting model
        if (isset($dbName)) {
            $config['db_database'] = $dbName;
        }
        if (isset($dbUser)) {
            $config['db_username'] = $dbUser;
        }
        if (isset($dbPassword)) {
            $config['db_password'] = $dbPassword;
        }
        if (isset($dbServer)) {
            $config['db_host'] = $dbServer;
        }
        // Don't load db_prefix in testing - let config/database.php handle it
        if (env('APP_ENV') !== 'testing') {
            $config['db_prefix'] = env('DB_PREFIX') ?? $dbPrefix ?? null;
        }

        self::$config = $config;
        self::$configLoaded = true;
    }
}
