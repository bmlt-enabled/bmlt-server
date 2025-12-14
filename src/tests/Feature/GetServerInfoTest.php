<?php

namespace Tests\Feature;

use App\FromFileConfig;
use App\FromDatabaseConfig;
use App\Repositories\SettingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GetServerInfoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        FromFileConfig::reset();
        FromDatabaseConfig::reset();
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
        FromDatabaseConfig::set('language', 'es');

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
        FromDatabaseConfig::set('defaultDurationTime', 'blah');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['defaultDuration' => 'blah']);
    }

    public function testRegionBias()
    {
        FromDatabaseConfig::set('regionBias', 'blah');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['regionBias' => 'blah']);
    }

    public function testDistanceUnits()
    {
        FromDatabaseConfig::set('distanceUnits', 'blah');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['distanceUnits' => 'blah']);
    }

    public function testSemanticAdmin()
    {
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['semanticAdmin' => '1']);
    }

    public function testChangesPerMeeting()
    {
        FromDatabaseConfig::set('changeDepthForMeetings', 99999);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['changesPerMeeting' => '99999']);
    }

    public function testMeetingsStatesProvinces()
    {
        FromDatabaseConfig::set('meetingStatesAndProvinces', []);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['meeting_states_and_provinces' => '']);

        FromDatabaseConfig::set('meetingStatesAndProvinces', ['abc', 'def']);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['meeting_states_and_provinces' => 'abc,def']);
    }

    public function testMeetingsCountiesAndSubprovinces()
    {
        FromDatabaseConfig::set('meetingCountiesAndSubProvinces', []);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['meeting_counties_and_sub_provinces' => '']);

        FromDatabaseConfig::set('meetingCountiesAndSubProvinces', ['abc', 'def']);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['meeting_counties_and_sub_provinces' => 'abc,def']);
    }

    public function testGoogleApiKey()
    {
        FromDatabaseConfig::set('googleApiKey', 'blah');
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['google_api_key' => 'blah']);
    }

    public function testCenterLongitude()
    {
        FromDatabaseConfig::set('searchSpecMapCenterLongitude', -79.793701171875);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['centerLongitude' => '-79.793701171875']);
    }

    public function testCenterLatitude()
    {
        FromDatabaseConfig::set('searchSpecMapCenterLatitude', 36.065752051707);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['centerLatitude' => '36.065752051707']);
    }

    public function testCenterZoom()
    {
        FromDatabaseConfig::set('searchSpecMapCenterZoom', 10);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['centerZoom' => '10']);
    }

    public function testAutoGeocodingEnabled()
    {
        FromDatabaseConfig::set('autoGeocodingEnabled', true);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['auto_geocoding_enabled' => true]);

        FromDatabaseConfig::set('autoGeocodingEnabled', false);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['auto_geocoding_enabled' => false]);
    }

    public function testCountyAutoGeocodingEnabled()
    {
        FromDatabaseConfig::set('countyAutoGeocodingEnabled', true);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['county_auto_geocoding_enabled' => true]);

        FromDatabaseConfig::set('countyAutoGeocodingEnabled', false);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['county_auto_geocoding_enabled' => false]);
    }

    public function testZipAutoGeocodingEnabled()
    {
        FromDatabaseConfig::set('zipAutoGeocodingEnabled', true);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['zip_auto_geocoding_enabled' => true]);

        FromDatabaseConfig::set('zipAutoGeocodingEnabled', false);
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
        FromDatabaseConfig::set('defaultClosedStatus', true);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['default_closed_status' => true]);

        FromDatabaseConfig::set('defaultClosedStatus', false);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['default_closed_status' => false]);
    }

    public function testAggregatorModeEnabled()
    {
        FromFileConfig::set('aggregator_mode_enabled', true);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['aggregator_mode_enabled' => true]);

        FromFileConfig::set('aggregator_mode_enabled', false);
        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['aggregator_mode_enabled' => false]);
    }

    public function testEnvironmentVariableOverridesDatabase()
    {
        $repository = new SettingRepository();
        $repository->update('googleApiKey', 'database_key');

        $_SERVER['GOOGLE_API_KEY'] = 'env_override_key';

        $this->get('/client_interface/json/?switcher=GetServerInfo')
            ->assertStatus(200)
            ->assertJsonFragment(['google_api_key' => 'env_override_key']);

        unset($_SERVER['GOOGLE_API_KEY']);
    }
}
