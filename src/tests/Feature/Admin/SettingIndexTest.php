<?php

namespace Tests\Feature\Admin;

use App\Repositories\SettingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new SettingRepository();
        $repository->update('language', 'en');
        $repository->update('bmltTitle', 'Test Server');
        $repository->update('autoGeocodingEnabled', true);
        $repository->update('searchSpecMapCenterZoom', 8);
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
        $repository = new SettingRepository();
        $repository->update('googleApiKey', 'database_key');
        $repository->update('language', 'en');
        $repository->update('autoGeocodingEnabled', false);

        $_SERVER['GOOGLE_API_KEY'] = 'env_override_key';
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

        unset($_SERVER['GOOGLE_API_KEY']);
        unset($_SERVER['LANGUAGE']);
        unset($_SERVER['AUTO_GEOCODING_ENABLED']);
    }
}
