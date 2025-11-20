<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Update settings with test values (migrations already seed them)
        Setting::updateOrCreate(['name' => 'language'], ['value' => 'en']);
        Setting::updateOrCreate(['name' => 'bmltTitle'], ['value' => 'Test Server']);
        Setting::updateOrCreate(['name' => 'autoGeocodingEnabled'], ['value' => true]);
        Setting::updateOrCreate(['name' => 'searchSpecMapCenterZoom'], ['value' => 8]);
    }

    public function testGetSettingsAsAdmin()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/v1/settings')
            ->assertStatus(200)
            ->json();

        $this->assertArrayHasKey('language', $data);
        $this->assertEquals('en', $data['language']);
        $this->assertArrayHasKey('bmltTitle', $data);
        $this->assertEquals('Test Server', $data['bmltTitle']);
        $this->assertArrayHasKey('autoGeocodingEnabled', $data);
        $this->assertTrue($data['autoGeocodingEnabled']);
        $this->assertArrayHasKey('searchSpecMapCenterZoom', $data);
        $this->assertEquals(8, $data['searchSpecMapCenterZoom']);
    }

    public function testGetSettingsAsServiceBodyAdmin()
    {
        $user = $this->createServiceBodyAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/v1/settings')
            ->assertStatus(403);
    }

    public function testGetSettingsAsObserver()
    {
        $user = $this->createServiceBodyObserverUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/v1/settings')
            ->assertStatus(403);
    }

    public function testGetSettingsUnauthenticated()
    {
        $this->get('/api/v1/settings')
            ->assertStatus(401);
    }

    public function testEnvironmentVariableOverridesDatabase()
    {
        Setting::updateOrCreate(['name' => 'googleApiKey'], ['value' => 'database_key']);
        Setting::updateOrCreate(['name' => 'language'], ['value' => 'en']);
        Setting::updateOrCreate(['name' => 'autoGeocodingEnabled'], ['value' => false]);

        $_SERVER['GKEY'] = 'env_override_key';
        $_SERVER['LANGUAGE'] = 'es';
        $_SERVER['AUTO_GEOCODING_ENABLED'] = 'true';

        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/v1/settings')
            ->assertStatus(200)
            ->json();

        $this->assertEquals('env_override_key', $data['googleApiKey']);
        $this->assertEquals('es', $data['language']);
        $this->assertTrue($data['autoGeocodingEnabled']);

        unset($_SERVER['GKEY']);
        unset($_SERVER['LANGUAGE']);
        unset($_SERVER['AUTO_GEOCODING_ENABLED']);
    }
}
