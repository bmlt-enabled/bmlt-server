<?php

namespace Tests\Feature\Admin;

use App\Repositories\SettingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new SettingRepository();
        $repository->update('language', 'en');
        $repository->update('bmltTitle', 'Test Server');
        $repository->update('autoGeocodingEnabled', true);
    }

    public function testUpdateSettingsAsAdmin()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->patchJson('/api/v1/settings', [
                'language' => 'es',
                'bmltTitle' => 'New Title',
                'autoGeocodingEnabled' => false,
            ])
            ->assertStatus(204);

        $repository = new SettingRepository();
        $this->assertEquals('es', $repository->getByName('language')?->value);
        $this->assertEquals('New Title', $repository->getByName('bmltTitle')?->value);
        $this->assertFalse($repository->getByName('autoGeocodingEnabled')?->value);
    }

    public function testUpdateSingleSetting()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->patchJson('/api/v1/settings', [
                'bmltTitle' => 'Updated Title Only'
            ])
            ->assertStatus(204);

        $repository = new SettingRepository();
        $this->assertEquals('en', $repository->getByName('language')?->value);
        $this->assertEquals('Updated Title Only', $repository->getByName('bmltTitle')?->value);
    }

    public function testUpdateWithInvalidKey()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->patchJson('/api/v1/settings', [
                'invalid_key' => 'value'
            ])
            ->assertStatus(422);
    }

    public function testUpdateWithInvalidValueType()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->patchJson('/api/v1/settings', [
                'autoGeocodingEnabled' => 'not a boolean'
            ])
            ->assertStatus(422);
    }

    public function testUpdateArraySetting()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $repository = new SettingRepository();
        $repository->update('meetingStatesAndProvinces', []);

        $this->withHeader('Authorization', "Bearer $token")
            ->patchJson('/api/v1/settings', [
                'meetingStatesAndProvinces' => ['CA', 'NY', 'TX']
            ])
            ->assertStatus(204);

        $setting = $repository->getByName('meetingStatesAndProvinces');
        $this->assertEquals(['CA', 'NY', 'TX'], $setting->value);
    }

    public function testUpdateNumericSettings()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $repository = new SettingRepository();
        $repository->update('searchSpecMapCenterZoom', 6);
        $repository->update('searchSpecMapCenterLatitude', 34.235918);

        $this->withHeader('Authorization', "Bearer $token")
            ->patchJson('/api/v1/settings', [
                'searchSpecMapCenterZoom' => 10,
                'searchSpecMapCenterLatitude' => 40.7128
            ])
            ->assertStatus(204);

        $this->assertEquals(10, $repository->getByName('searchSpecMapCenterZoom')?->value);
        $this->assertEquals(40.7128, $repository->getByName('searchSpecMapCenterLatitude')?->value);
    }

    public function testUpdateSettingsAsServiceBodyAdmin()
    {
        $user = $this->createServiceBodyAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->patchJson('/api/v1/settings', [
                'bmltTitle' => 'New Title'
            ])
            ->assertStatus(403);
    }

    public function testUpdateSettingsUnauthenticated()
    {
        $this->patchJson('/api/v1/settings', [
            'bmltTitle' => 'New Title'
        ])
        ->assertStatus(401);
    }
}
