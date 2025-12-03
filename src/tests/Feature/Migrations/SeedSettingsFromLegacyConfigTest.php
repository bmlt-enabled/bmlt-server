<?php

namespace Tests\Feature\Migrations;

use App\LegacyConfig;
use App\Models\Setting;
use Tests\TestCase;

/**
 * TODO: REMOVE BEFORE MERGE. These tests use Setting::migrateLegacyConfig() to verify the migration logic works correctly
 * Once we are ready to merge we will remove the following as migration logic is self contained.
 * - Setting::migrateLegacyConfig()
 * - Setting::readLegacyConfigFile()
 * - This entire test class
 */
class SeedSettingsFromLegacyConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clear any existing settings before each test
        Setting::truncate();

        // Unset all legacy config globals to prevent test contamination
        unset(
            $GLOBALS['gkey'],
            $GLOBALS['change_depth_for_meetings'],
            $GLOBALS['default_sort_key'],
            $GLOBALS['comdef_global_language'],
            $GLOBALS['default_duration_time'],
            $GLOBALS['region_bias'],
            $GLOBALS['comdef_distance_units'],
            $GLOBALS['meeting_states_and_provinces'],
            $GLOBALS['meeting_counties_and_sub_provinces'],
            $GLOBALS['search_spec_map_center'],
            $GLOBALS['number_of_meetings_for_auto'],
            $GLOBALS['auto_geocoding_enabled'],
            $GLOBALS['county_auto_geocoding_enabled'],
            $GLOBALS['zip_auto_geocoding_enabled'],
            $GLOBALS['g_defaultClosedStatus'],
            $GLOBALS['g_enable_language_selector'],
            $GLOBALS['aggregator_mode_enabled'],
            $GLOBALS['aggregator_max_geo_width_km'],
            $GLOBALS['g_include_service_body_email_in_semantic'],
            $GLOBALS['bmlt_title'],
            $GLOBALS['bmlt_notice'],
            $GLOBALS['format_lang_names']
        );
    }

    /**
     * Set every setting to some value, migrate, and check.
     */
    public function testMigrateAllSettingsWithValues()
    {
        global $gkey, $change_depth_for_meetings, $default_sort_key, $comdef_global_language;
        global $default_duration_time, $region_bias, $comdef_distance_units;
        global $meeting_states_and_provinces, $meeting_counties_and_sub_provinces;
        global $search_spec_map_center, $number_of_meetings_for_auto;
        global $auto_geocoding_enabled, $county_auto_geocoding_enabled, $zip_auto_geocoding_enabled;
        global $g_defaultClosedStatus, $g_enable_language_selector;
        global $aggregator_mode_enabled, $aggregator_max_geo_width_km;
        global $g_include_service_body_email_in_semantic, $bmlt_title, $bmlt_notice, $format_lang_names;

        $gkey = 'AIzaSyTest123456789';
        $change_depth_for_meetings = 5;
        $default_sort_key = 'weekday';
        $comdef_global_language = 'es';
        $default_duration_time = '01:30:00';
        $region_bias = 'ca';
        $comdef_distance_units = 'km';
        $meeting_states_and_provinces = ['CA', 'NY', 'TX'];
        $meeting_counties_and_sub_provinces = ['Los Angeles', 'San Diego'];
        $search_spec_map_center = [
            'longitude' => -122.4194,
            'latitude' => 37.7749,
            'zoom' => 10
        ];
        $number_of_meetings_for_auto = 15;
        $auto_geocoding_enabled = true;
        $county_auto_geocoding_enabled = true;
        $zip_auto_geocoding_enabled = false;
        $g_defaultClosedStatus = false;
        $g_enable_language_selector = true;
        $aggregator_mode_enabled = false;
        $aggregator_max_geo_width_km = 500.0;
        $g_include_service_body_email_in_semantic = true;
        $bmlt_title = 'Northern California BMLT';
        $bmlt_notice = 'Server maintenance scheduled for Sunday';
        $format_lang_names = ['ga' => 'Irish Gaelic', 'cy' => 'Welsh'];

        // Run migration
        // TODO: REMOVE BEFORE MERGE - Using deprecated method for testing
        Setting::migrateLegacyConfig();

        $this->assertEquals('AIzaSyTest123456789', Setting::where('name', 'googleApiKey')->first()->value);
        $this->assertEquals(5, Setting::where('name', 'changeDepthForMeetings')->first()->value);
        $this->assertEquals('weekday', Setting::where('name', 'defaultSortKey')->first()->value);
        $this->assertEquals('es', Setting::where('name', 'language')->first()->value);
        $this->assertEquals('01:30:00', Setting::where('name', 'defaultDurationTime')->first()->value);
        $this->assertEquals('ca', Setting::where('name', 'regionBias')->first()->value);
        $this->assertEquals('km', Setting::where('name', 'distanceUnits')->first()->value);
        $this->assertEquals(['CA', 'NY', 'TX'], Setting::where('name', 'meetingStatesAndProvinces')->first()->value);
        $this->assertEquals(['Los Angeles', 'San Diego'], Setting::where('name', 'meetingCountiesAndSubProvinces')->first()->value);
        $this->assertEquals(-122.4194, Setting::where('name', 'searchSpecMapCenterLongitude')->first()->value);
        $this->assertEquals(37.7749, Setting::where('name', 'searchSpecMapCenterLatitude')->first()->value);
        $this->assertEquals(10, Setting::where('name', 'searchSpecMapCenterZoom')->first()->value);
        $this->assertEquals(15, Setting::where('name', 'numberOfMeetingsForAuto')->first()->value);
        $this->assertTrue(Setting::where('name', 'autoGeocodingEnabled')->first()->value);
        $this->assertTrue(Setting::where('name', 'countyAutoGeocodingEnabled')->first()->value);
        $this->assertFalse(Setting::where('name', 'zipAutoGeocodingEnabled')->first()->value);
        $this->assertFalse(Setting::where('name', 'defaultClosedStatus')->first()->value);
        $this->assertTrue(Setting::where('name', 'enableLanguageSelector')->first()->value);
        $this->assertFalse(Setting::where('name', 'aggregatorModeEnabled')->first()->value);
        $this->assertEquals(500.0, Setting::where('name', 'aggregatorMaxGeoWidthKm')->first()->value);
        $this->assertTrue(Setting::where('name', 'includeServiceBodyEmailInSemantic')->first()->value);
        $this->assertEquals('Northern California BMLT', Setting::where('name', 'bmltTitle')->first()->value);
        $this->assertEquals('Server maintenance scheduled for Sunday', Setting::where('name', 'bmltNotice')->first()->value);
        $this->assertEquals(['ga' => 'Irish Gaelic', 'cy' => 'Welsh'], Setting::where('name', 'formatLangNames')->first()->value);
    }

    /**
     *  Set everything to different values, migrate, and check.
     */
    public function testMigrateAllSettingsWithDifferentValues()
    {
        global $gkey, $change_depth_for_meetings, $default_sort_key, $comdef_global_language;
        global $default_duration_time, $region_bias, $comdef_distance_units;
        global $meeting_states_and_provinces, $meeting_counties_and_sub_provinces;
        global $search_spec_map_center, $number_of_meetings_for_auto;
        global $auto_geocoding_enabled, $county_auto_geocoding_enabled, $zip_auto_geocoding_enabled;
        global $g_defaultClosedStatus, $g_enable_language_selector;
        global $aggregator_mode_enabled, $aggregator_max_geo_width_km;
        global $g_include_service_body_email_in_semantic, $bmlt_title, $bmlt_notice, $format_lang_names;

        $gkey = 'DifferentKey456';
        $change_depth_for_meetings = 10;
        $default_sort_key = 'town';
        $comdef_global_language = 'fr';
        $default_duration_time = '02:00:00';
        $region_bias = 'uk';
        $comdef_distance_units = 'mi';
        $meeting_states_and_provinces = ['FL', 'GA'];
        $meeting_counties_and_sub_provinces = ['Orange'];
        $search_spec_map_center = [
            'longitude' => -80.1918,
            'latitude' => 25.7617,
            'zoom' => 8
        ];
        $number_of_meetings_for_auto = 20;
        $auto_geocoding_enabled = false;
        $county_auto_geocoding_enabled = false;
        $zip_auto_geocoding_enabled = true;
        $g_defaultClosedStatus = true;
        $g_enable_language_selector = false;
        $aggregator_mode_enabled = true;
        $aggregator_max_geo_width_km = 250.0;
        $g_include_service_body_email_in_semantic = false;
        $bmlt_title = 'Florida BMLT';
        $bmlt_notice = 'Welcome';
        $format_lang_names = ['es' => 'Spanish'];

        // Run migration
        // TODO: REMOVE BEFORE MERGE - Using deprecated method for testing
        Setting::migrateLegacyConfig();

        $this->assertEquals('DifferentKey456', Setting::where('name', 'googleApiKey')->first()->value);
        $this->assertEquals(10, Setting::where('name', 'changeDepthForMeetings')->first()->value);
        $this->assertEquals('town', Setting::where('name', 'defaultSortKey')->first()->value);
        $this->assertEquals('fr', Setting::where('name', 'language')->first()->value);
        $this->assertEquals('02:00:00', Setting::where('name', 'defaultDurationTime')->first()->value);
        $this->assertEquals('uk', Setting::where('name', 'regionBias')->first()->value);
        $this->assertEquals('mi', Setting::where('name', 'distanceUnits')->first()->value);
        $this->assertEquals(['FL', 'GA'], Setting::where('name', 'meetingStatesAndProvinces')->first()->value);
        $this->assertEquals(['Orange'], Setting::where('name', 'meetingCountiesAndSubProvinces')->first()->value);
        $this->assertEquals(-80.1918, Setting::where('name', 'searchSpecMapCenterLongitude')->first()->value);
        $this->assertEquals(25.7617, Setting::where('name', 'searchSpecMapCenterLatitude')->first()->value);
        $this->assertEquals(8, Setting::where('name', 'searchSpecMapCenterZoom')->first()->value);
        $this->assertEquals(20, Setting::where('name', 'numberOfMeetingsForAuto')->first()->value);
        $this->assertFalse(Setting::where('name', 'autoGeocodingEnabled')->first()->value);
        $this->assertFalse(Setting::where('name', 'countyAutoGeocodingEnabled')->first()->value);
        $this->assertTrue(Setting::where('name', 'zipAutoGeocodingEnabled')->first()->value);
        $this->assertTrue(Setting::where('name', 'defaultClosedStatus')->first()->value);
        $this->assertFalse(Setting::where('name', 'enableLanguageSelector')->first()->value);
        $this->assertTrue(Setting::where('name', 'aggregatorModeEnabled')->first()->value);
        $this->assertEquals(250.0, Setting::where('name', 'aggregatorMaxGeoWidthKm')->first()->value);
        $this->assertFalse(Setting::where('name', 'includeServiceBodyEmailInSemantic')->first()->value);
        $this->assertEquals('Florida BMLT', Setting::where('name', 'bmltTitle')->first()->value);
        $this->assertEquals('Welcome', Setting::where('name', 'bmltNotice')->first()->value);
        $this->assertEquals(['es' => 'Spanish'], Setting::where('name', 'formatLangNames')->first()->value);
    }

    /**
     * Unset all settings and make sure reasonable defaults are written.
     */
    public function testMigrateWithDefaultValues()
    {
        // TODO: REMOVE BEFORE MERGE - Using deprecated method for testing
        Setting::migrateLegacyConfig();

        $settingKeys = array_keys(Setting::SETTING_TYPES);
        foreach ($settingKeys as $key) {
            $setting = Setting::where('name', $key)->first();
            $this->assertNotNull($setting, "Setting {$key} should exist");
            $this->assertEquals(Setting::SETTING_DEFAULTS[$key], $setting->value, "Setting {$key} should have default value");
            $this->assertEquals(Setting::SETTING_TYPES[$key], $setting->type, "Setting {$key} should have correct type");
        }

        $this->assertEquals(count($settingKeys), Setting::count());
    }

    /**
     * Verify LegacyConfig::get() returns values from database, NOT from file.
     * This ensures that after migration, the database is the source of truth.
     */
    public function testLegacyConfigGetsValueFromDatabaseNotFile()
    {
        // Set a value in a global variable (simulating auto-config.inc.php)
        global $gkey;
        $gkey = 'OldFileValue';

        // Create a setting in the database with a different value
        Setting::create([
            'name' => 'googleApiKey',
            'type' => 'string',
            'value' => 'DatabaseValue123'
        ]);

        // Verify setting was created
        $setting = Setting::where('name', 'googleApiKey')->first();
        $this->assertNotNull($setting);
        $this->assertEquals('DatabaseValue123', $setting->value);

        // Reset LegacyConfig to force it to reload (and not find gkey in self::$config)
        LegacyConfig::reset();

        // LegacyConfig::get() should return the database value, not the file value
        // Test direct DB query to make sure that works
        $dbSetting = Setting::where('name', 'googleApiKey')->first();
        $this->assertEquals('DatabaseValue123', $dbSetting->getTypedValue());

        $result = legacy_config('gkey');
        $this->assertEquals('DatabaseValue123', $result, 'legacy_config should query database for gkey');

        // Verify the global is still set (not overwritten)
        $this->assertEquals('OldFileValue', $gkey);
    }

    /**
     * Verify DB credentials still come from LegacyConfig (file), not database.
     */
    public function testLegacyConfigGetsDatabaseCredentialsFromConfig()
    {
        // DB credentials should come from LegacyConfig, not from Setting model
        LegacyConfig::set('db_database', 'test_db');
        $this->assertEquals('test_db', legacy_config('db_database'));

        // Even if we create a Setting with this name, it shouldn't be used for DB creds
        Setting::create([
            'name' => 'dbDatabase',
            'type' => 'string',
            'value' => 'wrong_db'
        ]);

        // Should still get the config value, not from Setting
        $this->assertEquals('test_db', legacy_config('db_database'));
    }

    protected function tearDown(): void
    {
        LegacyConfig::reset();
        parent::tearDown();
    }
}
