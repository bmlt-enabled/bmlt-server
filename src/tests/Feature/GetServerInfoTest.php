<?php

namespace Tests\Feature;

use App\ConfigFile;
use App\LegacyConfig;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GetServerInfoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        ConfigFile::reset();
        LegacyConfig::reset();
        parent::tearDown();
    }

    public function testJsonp()
    {
        $content = $this->get('/client_interface/jsonp/?switcher=GetServerInfo&callback=asdf')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'text/javascript; charset=UTF-8')
            ->content();
        $this->assertStringStartsWith('/**/asdf([', $content);
        $this->assertStringEndsWith(']);', $content);
    }

    public function testIsList()
    {
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function testVersion()
    {
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['version' => config('app.version')]);
    }

    public function testVersionIntBeta()
    {
        Config::set('app.version', '3.0.2-beta');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['versionInt' => '3000002']);
    }

    public function testVersionIntNonBeta()
    {
        Config::set('app.version', '3.0.2');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['versionInt' => '3000002']);
    }

    public function testNativeLang()
    {
        // Update the setting value
        LegacyConfig::set('language', 'es');

        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['nativeLang' => 'es']);
    }

    public function testLangs()
    {
        $langs = collect(scandir(base_path('lang')))->reject(fn ($dir) => str_starts_with($dir, '.'))->sort()->join(',');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['langs' => $langs]);
    }

    public function testDefaultDuration()
    {
        LegacyConfig::set('default_duration_time', 'blah');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['defaultDuration' => 'blah']);
    }

    public function testRegionBias()
    {
        LegacyConfig::set('region_bias', 'blah');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['regionBias' => 'blah']);
    }

    public function testDistanceUnits()
    {
        LegacyConfig::set('distance_units', 'blah');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['distanceUnits' => null]);
    }

    public function testSemanticAdmin()
    {
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['semanticAdmin' => '1']);
    }

    public function testChangesPerMeeting()
    {
        LegacyConfig::set('change_depth_for_meetings', 99999);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['changesPerMeeting' => '99999']);
    }

    public function testMeetingsStatesProvinces()
    {
        LegacyConfig::set('meeting_states_and_provinces', []);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['meeting_states_and_provinces' => '']);

        LegacyConfig::set('meeting_states_and_provinces', ['abc', 'def']);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['meeting_states_and_provinces' => 'abc,def']);
    }

    public function testMeetingsCountiesAndSubprovinces()
    {
        LegacyConfig::set('meeting_counties_and_sub_provinces', []);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['meeting_counties_and_sub_provinces' => '']);

        LegacyConfig::set('meeting_counties_and_sub_provinces', ['abc', 'def']);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['meeting_counties_and_sub_provinces' => 'abc,def']);
    }

    public function testGoogleApiKey()
    {
        LegacyConfig::set('google_api_key', 'blah');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['google_api_key' => 'blah']);
    }

    public function testCenterLongitude()
    {
        LegacyConfig::set('search_spec_map_center_longitude', -79.793701171875);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['centerLongitude' => '-79.793701171875']);
    }

    public function testCenterLatitude()
    {
        LegacyConfig::set('search_spec_map_center_latitude', 36.065752051707);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['centerLatitude' => '36.065752051707']);
    }

    public function testCenterZoom()
    {
        LegacyConfig::set('search_spec_map_center_zoom', 10);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['centerZoom' => '10']);
    }

    public function testAutoGeocodingEnabled()
    {
        LegacyConfig::set('auto_geocoding_enabled', true);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['auto_geocoding_enabled' => true]);

        LegacyConfig::set('auto_geocoding_enabled', false);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['auto_geocoding_enabled' => false]);
    }

    public function testCountyAutoGeocodingEnabled()
    {
        LegacyConfig::set('county_auto_geocoding_enabled', true);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['county_auto_geocoding_enabled' => true]);

        LegacyConfig::set('county_auto_geocoding_enabled', false);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['county_auto_geocoding_enabled' => false]);
    }

    public function testZipAutoGeocodingEnabled()
    {
        LegacyConfig::set('zip_auto_geocoding_enabled', true);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['zip_auto_geocoding_enabled' => true]);

        LegacyConfig::set('zip_auto_geocoding_enabled', false);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['zip_auto_geocoding_enabled' => false]);
    }

    public function testCommit()
    {
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['commit' => config('app.commit')]);
    }

    public function testDefaultClosedStatusEnabled()
    {
        LegacyConfig::set('default_closed_status', true);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['default_closed_status' => true]);

        LegacyConfig::set('default_closed_status', false);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['default_closed_status' => false]);
    }

    public function testAggregatorModeEnabled()
    {
        ConfigFile::set('aggregator_mode_enabled', true);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['aggregator_mode_enabled' => true]);

        ConfigFile::set('aggregator_mode_enabled', false);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['aggregator_mode_enabled' => false]);
    }

    public function testEnvironmentVariableOverridesDatabase()
    {
        Setting::updateOrCreate(['name' => 'googleApiKey'], ['value' => 'database_key']);

        $_SERVER['GKEY'] = 'env_override_key';

        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['google_api_key' => 'env_override_key']);

        unset($_SERVER['GKEY']);
    }
}
