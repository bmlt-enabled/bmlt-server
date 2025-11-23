<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
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
        // Read directly from auto-config.inc.php
        $legacyConfig = $this->readLegacyConfig();

        $settingsToMigrate = array_keys(Setting::SETTING_TYPES);

        foreach ($settingsToMigrate as $key) {
            $value = $legacyConfig[$key] ?? Setting::SETTING_DEFAULTS[$key];
            $type = Setting::SETTING_TYPES[$key];
            Setting::updateOrCreate(
                ['name' => $key],
                ['type' => $type, 'value' => $value]
            );
        }
    }

    /**
     * Read settings directly from auto-config.inc.php file.
     * Returns values with camelCase keys matching Setting model constants.
     * This bypasses LegacyConfig::get() which now only returns DB credentials.
     */
    private function readLegacyConfig(): array
    {
        $legacyConfigFile = base_path() . '/../auto-config.inc.php';
        if (!file_exists($legacyConfigFile)) {
            return [];
        }

        // Load the legacy config file
        defined('BMLT_EXEC') or define('BMLT_EXEC', 1);
        require($legacyConfigFile);

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::truncate();
    }
};
