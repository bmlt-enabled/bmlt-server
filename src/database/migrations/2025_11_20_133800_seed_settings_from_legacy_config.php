<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const TYPE_STRING = 'string';
    private const TYPE_INT = 'int';
    private const TYPE_FLOAT = 'float';
    private const TYPE_BOOL = 'bool';
    private const TYPE_ARRAY = 'array';

    private const SETTING_TYPES = [
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
        'includeServiceBodyEmailInSemantic' => self::TYPE_BOOL,
        'bmltTitle' => self::TYPE_STRING,
        'bmltNotice' => self::TYPE_STRING,
        'formatLangNames' => self::TYPE_ARRAY,
    ];

    private const SETTING_DEFAULTS = [
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
        'includeServiceBodyEmailInSemantic' => false,
        'bmltTitle' => 'BMLT Administration',
        'bmltNotice' => '',
        'formatLangNames' => [],
    ];

    public function up()
    {
        $config = $this->getConfig();
        $settingNames = array_keys(self::SETTING_TYPES);
        foreach ($settingNames as $name) {
            $type = self::SETTING_TYPES[$name];
            $value = $config[$name];
            if (!is_null($value)) {
                $value = $this->cast($value, $type);
                $value = json_encode($value);
            }
            DB::table('settings')->insert(['name' => $name, 'type' => $type, 'value' => $value]);
        }
    }

    private function cast($value, string $type): mixed
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
                return (bool)$value;

            case self::TYPE_INT:
                return (int)$value;

            case self::TYPE_FLOAT:
                return (float)$value;

            case self::TYPE_STRING:
            default:
                return (string)$value;
        }
    }

    private function getConfig(): array
    {
        // Declare all possible global variables so they're accessible
        global $gkey, $change_depth_for_meetings, $default_sort_key, $comdef_global_language;
        global $default_duration_time, $region_bias, $comdef_distance_units;
        global $meeting_states_and_provinces, $meeting_counties_and_sub_provinces;
        global $search_spec_map_center, $number_of_meetings_for_auto;
        global $auto_geocoding_enabled, $county_auto_geocoding_enabled, $zip_auto_geocoding_enabled;
        global $g_defaultClosedStatus, $g_enable_language_selector;
        global $g_include_service_body_email_in_semantic, $bmlt_title, $bmlt_notice, $format_lang_names;

        // If not in testing and file exists, load it
        $legacyConfigFile = base_path() . '/../auto-config.inc.php';
        if (env('APP_ENV') !== 'testing' && file_exists($legacyConfigFile)) {
            defined('BMLT_EXEC') or define('BMLT_EXEC', 1);
            require($legacyConfigFile);
        }

        return [
            'googleApiKey' => $gkey ?? self::SETTING_DEFAULTS['googleApiKey'],
            'changeDepthForMeetings' => $change_depth_for_meetings ?? self::SETTING_DEFAULTS['changeDepthForMeetings'],
            'defaultSortKey' => $default_sort_key ?? self::SETTING_DEFAULTS['defaultSortKey'],
            'language' => $comdef_global_language ?? self::SETTING_DEFAULTS['language'],
            'defaultDurationTime' => $default_duration_time ?? self::SETTING_DEFAULTS['defaultDurationTime'],
            'regionBias' => $region_bias ?? self::SETTING_DEFAULTS['regionBias'],
            'distanceUnits' => $comdef_distance_units ?? self::SETTING_DEFAULTS['distanceUnits'],
            'meetingStatesAndProvinces' => $meeting_states_and_provinces ?? self::SETTING_DEFAULTS['meetingStatesAndProvinces'],
            'meetingCountiesAndSubProvinces' => $meeting_counties_and_sub_provinces ?? self::SETTING_DEFAULTS['meetingCountiesAndSubProvinces'],
            'searchSpecMapCenterLongitude' => (isset($search_spec_map_center) ? ($search_spec_map_center['longitude'] ?? null) : null) ?? self::SETTING_DEFAULTS['searchSpecMapCenterLongitude'],
            'searchSpecMapCenterLatitude' => (isset($search_spec_map_center) ? ($search_spec_map_center['latitude'] ?? null) : null) ?? self::SETTING_DEFAULTS['searchSpecMapCenterLatitude'],
            'searchSpecMapCenterZoom' => (isset($search_spec_map_center) ? ($search_spec_map_center['zoom'] ?? null) : null) ?? self::SETTING_DEFAULTS['searchSpecMapCenterZoom'],
            'numberOfMeetingsForAuto' => $number_of_meetings_for_auto ?? self::SETTING_DEFAULTS['numberOfMeetingsForAuto'],
            'autoGeocodingEnabled' => $auto_geocoding_enabled ?? self::SETTING_DEFAULTS['autoGeocodingEnabled'],
            'countyAutoGeocodingEnabled' => $county_auto_geocoding_enabled ?? self::SETTING_DEFAULTS['countyAutoGeocodingEnabled'],
            'zipAutoGeocodingEnabled' => $zip_auto_geocoding_enabled ?? self::SETTING_DEFAULTS['zipAutoGeocodingEnabled'],
            'defaultClosedStatus' => $g_defaultClosedStatus ?? self::SETTING_DEFAULTS['defaultClosedStatus'],
            'enableLanguageSelector' => $g_enable_language_selector ?? self::SETTING_DEFAULTS['enableLanguageSelector'],
            'includeServiceBodyEmailInSemantic' => $g_include_service_body_email_in_semantic ?? self::SETTING_DEFAULTS['includeServiceBodyEmailInSemantic'],
            'bmltTitle' => $bmlt_title ?? self::SETTING_DEFAULTS['bmltTitle'],
            'bmltNotice' => $bmlt_notice ?? self::SETTING_DEFAULTS['bmltNotice'],
            'formatLangNames' => $format_lang_names ?? self::SETTING_DEFAULTS['formatLangNames'],
        ];
    }

    public function down()
    {
        DB::table('settings')->truncate();
    }
};
