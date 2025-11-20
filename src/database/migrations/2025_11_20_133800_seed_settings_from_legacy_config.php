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
        'aggregatorModeEnabled' => self::TYPE_BOOL,
        'aggregatorMaxGeoWidthKm' => self::TYPE_FLOAT,
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
        'aggregatorModeEnabled' => false,
        'aggregatorMaxGeoWidthKm' => 160.0,
        'includeServiceBodyEmailInSemantic' => false,
        'bmltTitle' => 'BMLT Administration',
        'bmltNotice' => '',
        'formatLangNames' => [],
    ];

    /**
     * Run the migrations.
     *
     * Migrates settings from auto-config.inc.php to the database.
     * Uses defaults if the legacy config file doesn't exist or doesn't contain the setting.
     *
     * @return void
     */
    public function up()
    {
        $legacyConfig = $this->readLegacyConfigFile();

        $settingsToMigrate = array_keys(self::SETTING_TYPES);

        foreach ($settingsToMigrate as $key) {
            // Priority: legacy file > defaults
            $value = $legacyConfig[$key] ?? self::SETTING_DEFAULTS[$key];
            $type = self::SETTING_TYPES[$key];

            $storedValue = $this->convertToStorableValue($value, $type);

            $existing = DB::table('settings')->where('name', $key)->first();
            if ($existing) {
                DB::table('settings')
                    ->where('name', $key)
                    ->update([
                        'type' => $type,
                        'value' => $storedValue,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('settings')->insert([
                    'name' => $key,
                    'type' => $type,
                    'value' => $storedValue,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->truncate();
    }

    /**
     * Convert value to storable JSON format.
     */
    private function convertToStorableValue($value, string $type): string
    {
        $typedValue = $this->convertToType($value, $type);
        return json_encode($typedValue);
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
     * Read settings directly from auto-config.inc.php file.
     * Returns values with camelCase keys matching Setting model constants.
     */
    private function readLegacyConfigFile(): array
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
};
