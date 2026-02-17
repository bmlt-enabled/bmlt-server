<?php

namespace Tests\Feature\Admin;

use App\FromDatabaseConfig;
use App\Http\Resources\Admin\MeetingResource;
use App\Models\Format;
use App\Models\Meeting;
use App\Models\MeetingData;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MeetingLocationTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        FromDatabaseConfig::reset();
        MeetingResource::resetStaticVariables();
        parent::tearDown();
    }

    private function validPayload($serviceBody, array $formats): array
    {
        return [
            'name' => 'Sunday Serenity',
            'serviceBodyId' => $serviceBody->id_bigint,
            'formatIds' => collect($formats)->map(fn ($fmt) => $fmt->shared_id_bigint)->sort()->toArray(),
            'venueType' => Meeting::VENUE_TYPE_IN_PERSON,
            'temporarilyVirtual' => false,
            'day' => 0,
            'startTime' => '20:00',
            'duration' => '01:00',
            'latitude' => 35.7079,
            'longitude' => 79.8136,
            'published' => true,
            'email' => 'test@test.com',
            'location_street' => '813 Darby St.',
            'location_municipality' => 'Raleigh',
            'location_province' => 'NC',
            'location_text' => 'Community Center',
            'timeZone' => 'America/New_York',
        ];
    }

    public function testCreateMeetingWithTranslations()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);
        $format = Format::query()->first();

        $payload = $this->validPayload($area, [$format]);
        $payload['locationTranslations'] = [
            'es' => [
                'location_text' => 'Centro Comunitario',
                'location_street' => 'Calle Darby 813',
                'location_municipality' => 'Raleigh',
            ],
            'fr' => [
                'location_text' => 'Centre Communautaire',
            ],
        ];

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->post('/api/v1/meetings', $payload)
            ->assertStatus(201)
            ->json();

        $this->assertEquals('Community Center', $data['location_text']);
        $this->assertArrayHasKey('locationTranslations', $data);
        $this->assertEquals('Centro Comunitario', $data['locationTranslations']['es']['location_text']);
        $this->assertEquals('Calle Darby 813', $data['locationTranslations']['es']['location_street']);
        $this->assertEquals('Centre Communautaire', $data['locationTranslations']['fr']['location_text']);
    }

    public function testShowMeetingWithTranslations()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);

        $meeting = $this->createMeeting(
            ['service_body_bigint' => $area->id_bigint],
            ['location_text' => 'Main Hall', 'location_street' => '123 Main St']
        );

        // Add translation rows directly
        $template = MeetingData::query()->where('meetingid_bigint', 0)->where('key', 'location_text')->first();
        MeetingData::create([
            'meetingid_bigint' => $meeting->id_bigint,
            'key' => 'location_text',
            'field_prompt' => $template->field_prompt,
            'lang_enum' => 'es',
            'data_string' => 'Sala Principal',
            'visibility' => $template->visibility,
        ]);

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/meetings/{$meeting->id_bigint}")
            ->assertStatus(200)
            ->json();

        $this->assertEquals('Main Hall', $data['location_text']);
        $this->assertArrayHasKey('locationTranslations', $data);
        $this->assertEquals('Sala Principal', $data['locationTranslations']['es']['location_text']);
    }

    public function testUpdateMeetingWithTranslations()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);
        $format = Format::query()->first();

        // Create meeting first
        $payload = $this->validPayload($area, [$format]);
        $createData = $this->withHeader('Authorization', "Bearer $token")
            ->post('/api/v1/meetings', $payload)
            ->assertStatus(201)
            ->json();

        // Update with translations
        $payload['locationTranslations'] = [
            'es' => [
                'location_text' => 'Centro Comunitario',
            ],
        ];

        $this->withHeader('Authorization', "Bearer $token")
            ->put("/api/v1/meetings/{$createData['id']}", $payload)
            ->assertStatus(204);

        MeetingResource::resetStaticVariables();

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/meetings/{$createData['id']}")
            ->assertStatus(200)
            ->json();

        $this->assertEquals('Centro Comunitario', $data['locationTranslations']['es']['location_text']);
    }

    public function testUpdateMeetingPreservesTranslationsWhenNotProvided()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);
        $format = Format::query()->first();

        // Create meeting with translations
        $payload = $this->validPayload($area, [$format]);
        $payload['locationTranslations'] = [
            'es' => [
                'location_text' => 'Centro Comunitario',
            ],
        ];

        $createData = $this->withHeader('Authorization', "Bearer $token")
            ->post('/api/v1/meetings', $payload)
            ->assertStatus(201)
            ->json();

        MeetingResource::resetStaticVariables();

        // Partial update without locationTranslations should preserve them
        $this->withHeader('Authorization', "Bearer $token")
            ->patch("/api/v1/meetings/{$createData['id']}", [
                'name' => 'Updated Meeting Name',
            ])
            ->assertStatus(204);

        MeetingResource::resetStaticVariables();

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/meetings/{$createData['id']}")
            ->assertStatus(200)
            ->json();

        $this->assertEquals('Updated Meeting Name', $data['name']);
        $this->assertEquals('Centro Comunitario', $data['locationTranslations']['es']['location_text']);
    }

    public function testUpdateMeetingClearsTranslationsWhenEmptyProvided()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);
        $format = Format::query()->first();

        // Create meeting with translations
        $payload = $this->validPayload($area, [$format]);
        $payload['locationTranslations'] = [
            'es' => [
                'location_text' => 'Centro Comunitario',
            ],
        ];

        $createData = $this->withHeader('Authorization', "Bearer $token")
            ->post('/api/v1/meetings', $payload)
            ->assertStatus(201)
            ->json();

        MeetingResource::resetStaticVariables();

        // Update with empty translations should clear them
        $payload['locationTranslations'] = [];
        $this->withHeader('Authorization', "Bearer $token")
            ->put("/api/v1/meetings/{$createData['id']}", $payload)
            ->assertStatus(204);

        MeetingResource::resetStaticVariables();

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/meetings/{$createData['id']}")
            ->assertStatus(200)
            ->json();

        $this->assertEmpty((array) $data['locationTranslations']);
    }

    public function testTranslationsDontOverwritePrimaryData()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);
        $format = Format::query()->first();

        $payload = $this->validPayload($area, [$format]);
        $payload['locationTranslations'] = [
            'es' => [
                'location_text' => 'Centro Comunitario',
                'location_street' => 'Calle Darby 813',
            ],
        ];

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->post('/api/v1/meetings', $payload)
            ->assertStatus(201)
            ->json();

        // Primary language data should not be overwritten by translations
        $this->assertEquals('Community Center', $data['location_text']);
        $this->assertEquals('813 Darby St.', $data['location_street']);
    }

    public function testOnlyTranslatableFieldsAreAccepted()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);
        $format = Format::query()->first();

        $payload = $this->validPayload($area, [$format]);
        $payload['locationTranslations'] = [
            'es' => [
                'location_text' => 'Centro Comunitario',
                'meeting_name' => 'Should be ignored', // Not a translatable location field
            ],
        ];

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->post('/api/v1/meetings', $payload)
            ->assertStatus(201)
            ->json();

        // The meeting_name translation should not exist
        $this->assertEquals('Centro Comunitario', $data['locationTranslations']['es']['location_text']);
        $this->assertArrayNotHasKey('meeting_name', $data['locationTranslations']['es'] ?? []);
    }

    public function testResponseIncludesEmptyTranslationsObject()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);
        $format = Format::query()->first();

        $payload = $this->validPayload($area, [$format]);

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->post('/api/v1/meetings', $payload)
            ->assertStatus(201)
            ->json();

        $this->assertArrayHasKey('locationTranslations', $data);
        $this->assertEmpty((array) $data['locationTranslations']);
    }
}
