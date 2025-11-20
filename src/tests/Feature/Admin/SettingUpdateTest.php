<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Update settings with test values (migrations already seed them)
        Setting::updateOrCreate(['name' => 'language'], ['value' => 'en']);
        Setting::updateOrCreate(['name' => 'bmltTitle'], ['value' => 'Test Server']);
        Setting::updateOrCreate(['name' => 'autoGeocodingEnabled'], ['value' => true]);
    }

    public function testUpdateSettingsAsAdmin()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->putJson('/api/v1/settings', [
                'language' => 'es',
                'bmltTitle' => 'New Title',
                'autoGeocodingEnabled' => false,
            ])
            ->assertStatus(204);

        $this->assertEquals('es', Setting::where('name', 'language')->first()->value);
        $this->assertEquals('New Title', Setting::where('name', 'bmltTitle')->first()->value);
        $this->assertFalse(Setting::where('name', 'autoGeocodingEnabled')->first()->value);
    }

    public function testUpdateSingleSetting()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->putJson('/api/v1/settings', [
                'bmltTitle' => 'Updated Title Only'
            ])
            ->assertStatus(204);

        $this->assertEquals('en', Setting::where('name', 'language')->first()->value);
        $this->assertEquals('Updated Title Only', Setting::where('name', 'bmltTitle')->first()->value);
    }

    public function testUpdateWithInvalidKey()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->putJson('/api/v1/settings', [
                'invalid_key' => 'value'
            ])
            ->assertStatus(422);
    }

    public function testUpdateWithInvalidValueType()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->putJson('/api/v1/settings', [
                'autoGeocodingEnabled' => 'not a boolean'
            ])
            ->assertStatus(422);
    }

    public function testUpdateArraySetting()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        Setting::updateOrCreate(['name' => 'meetingStatesAndProvinces'], ['value' => []]);

        $this->withHeader('Authorization', "Bearer $token")
            ->putJson('/api/v1/settings', [
                'meetingStatesAndProvinces' => ['CA', 'NY', 'TX']
            ])
            ->assertStatus(204);

        $setting = Setting::where('name', 'meetingStatesAndProvinces')->first();
        $this->assertEquals(['CA', 'NY', 'TX'], $setting->value);
    }

    public function testUpdateNumericSettings()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        Setting::updateOrCreate(['name' => 'searchSpecMapCenterZoom'], ['value' => 6]);
        Setting::updateOrCreate(['name' => 'searchSpecMapCenterLatitude'], ['value' => 34.235918]);

        $this->withHeader('Authorization', "Bearer $token")
            ->putJson('/api/v1/settings', [
                'searchSpecMapCenterZoom' => 10,
                'searchSpecMapCenterLatitude' => 40.7128
            ])
            ->assertStatus(204);

        $this->assertEquals(10, Setting::where('name', 'searchSpecMapCenterZoom')->first()->value);
        $this->assertEquals(40.7128, Setting::where('name', 'searchSpecMapCenterLatitude')->first()->value);
    }

    public function testUpdateSettingsAsServiceBodyAdmin()
    {
        $user = $this->createServiceBodyAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->putJson('/api/v1/settings', [
                'bmltTitle' => 'New Title'
            ])
            ->assertStatus(403);
    }

    public function testUpdateSettingsUnauthenticated()
    {
        $this->putJson('/api/v1/settings', [
            'bmltTitle' => 'New Title'
        ])
        ->assertStatus(401);
    }
}
