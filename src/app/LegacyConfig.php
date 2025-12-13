<?php

namespace App;

use App\Models\Setting;
use App\Repositories\SettingRepository;

class LegacyConfig
{
    private static ?array $config = null;
    private static bool $configLoaded = false;
    private const SETTING_TO_ENV_VAR_MAP = [
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
        'includeServiceBodyEmailInSemantic' => 'INCLUDE_SERVICE_BODY_EMAIL_IN_SEMANTIC',
        'bmltTitle' => 'BMLT_TITLE',
        'bmltNotice' => 'BMLT_NOTICE',
        'formatLangNames' => 'FORMAT_LANG_NAMES',
    ];

    private const LEGACY_NAME_TO_NEW_NAME_MAP = [
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
        'g_include_service_body_email_in_semantic' => 'includeServiceBodyEmailInSemantic',
        'include_service_body_email_in_semantic' => 'includeServiceBodyEmailInSemantic',
        'bmlt_title' => 'bmltTitle',
        'bmlt_notice' => 'bmltNotice',
        'format_lang_names' => 'formatLangNames',
    ];

    public static function get(string $legacyName, $default = null)
    {
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        return self::$config[$legacyName] ?? $default;
    }

    public static function fromEnv(string $name): mixed
    {
        $envName = self::SETTING_TO_ENV_VAR_MAP[$name] ?? null;
        if (!$envName) {
            return null;
        }

        $value = $_SERVER[$envName] ?? env($envName);

        if (is_null($value) || $value === '') {
            return null;
        }

        $type = Setting::SETTING_TYPES[$name];
        return match ($type) {
            Setting::TYPE_INT => (int)$value,
            Setting::TYPE_FLOAT => (float)$value,
            Setting::TYPE_BOOL => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            Setting::TYPE_ARRAY => self::parseArrayValue($value),
            default => $value,
        };
    }

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

    public static function set(string $legacyName, $value)
    {
        // should only be used in testing
        $name = self::LEGACY_NAME_TO_NEW_NAME_MAP[$legacyName];
        $repository = app(SettingRepository::class);
        $repository->update($name, $value);
        $setting = $repository->getByName($name);
        self::$config[$legacyName] = $setting->value;
    }

    public static function reset()
    {
        // should only be used in testing
        self::$config = null;
        self::$configLoaded = false;
    }

    private static function loadConfig()
    {
        $newNameToLegacyName = collect(self::LEGACY_NAME_TO_NEW_NAME_MAP)->mapWithKeys(fn ($new, $legacy) => [$new => $legacy]);
        $repository = app(SettingRepository::class);
        self::$config = $repository->getAll()
            ->mapWithKeys(function ($setting) use ($newNameToLegacyName) {
                $value = self::fromEnv($setting->name) ?? $setting->value;
                $legacyName = $newNameToLegacyName->get($setting->name);
                return [$legacyName => $value];
            })
            ->toArray();

        self::$configLoaded = true;
    }
}
