<?php

namespace Tests\Feature\Admin;

use App\Http\Resources\Admin\MeetingResource;
use App\Models\Change;
use App\Models\Format;
use App\Models\Meeting;
use App\Models\MeetingData;
use App\Models\MeetingLongData;
use App\Repositories\MeetingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MeetingTranslateTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        MeetingResource::resetStaticVariables();
        parent::tearDown();
    }
    protected static $fieldNameMap = [
        "venue_type" => 'venueType',
        "weekday_tinyint" => 'day',
        "start_time" => 'startTime',
        "duration_time" => 'duration',
        "latitude" => 'latitude',
        "longitude" => 'longitude',
        "time_zone" => 'timeZone',
        "published" => 'published',
        "worldid_mixed" => 'worldId',
        "service_body_bigint" => 'serviceBodyId',
        "formats" => 'formats',
        "meeting_name" => 'name',
        "email_contact" => 'email'
    ];
    protected $mainFields;
    protected $dataFields;
    protected $longDataFields;
    protected function createMeeting(array $mainFields = [], array $dataFields = [], array $longDataFields = [], array $removeFieldKeys = [])
    {
        $this->mainFields = collect([
            'formats' => '',
            'venue_type' => Meeting::VENUE_TYPE_IN_PERSON,
            'weekday_tinyint' => 0,
            'start_time' => '20:00:00',
            'duration_time' => '01:00:00',
            'latitude' => 35.7079,
            'longitude' => 79.8136,
            'published' => 1,
            'time_zone' => 'America/New_York'
        ])
        ->reject(fn ($_, $key) => in_array($key, $removeFieldKeys))
        ->merge($mainFields)
        ->toArray();

        $this->dataFields = collect([
            'location_street' => '813 Darby St.',
            'location_municipality' => 'Raleigh',
            'location_province' => 'NC',
            'location_postal_code_1' => '27610',
            'virtual_meeting_link' => 'https://zoom.us',
            'phone_meeting_number' => '5555555555',
        ])
        ->reject(fn ($_, $key) => in_array($key, $removeFieldKeys) || array_key_exists($key, $longDataFields))
        ->merge($dataFields)
        ->toArray();

        $this->longDataFields = $longDataFields;
        $ret = parent::createMeeting($this->mainFields, $this->dataFields, $longDataFields);
        $this->mainFields = array_merge(self::$meetingMainFieldDefaults, $this->mainFields);
        $this->dataFields = array_merge(self::$meetingDataFieldDefaults, $this->dataFields);

        return $ret;
    }
    protected function addTranslations($meeting, $lang, $values)
    {
        foreach ($values as $key => $value) {
            $meeting->data()->create([
                'key' => $key,
                'field_prompt' => 'doesnt matter',
                'lang_enum' => $lang,
                'data_string' => $value,
                'visibility' => 0,
            ]);
        }
    }
    public function testTranslateMeetingAllFields()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);
        $format = Format::query()->first();
        $meeting = $this->createMeeting(['service_body_bigint' => $area->id_bigint, 'formats' => strval($format->shared_id_bigint)]);
        $meetingId = $meeting->id_bigint;
        $original = $meeting;
        foreach (collect(Meeting::$mainFields)->merge(MeetingData::STOCK_FIELDS) as $fieldName) {
            if ($fieldName == 'id_bigint' || $fieldName == 'formats' || $fieldName == 'time_zone' || $fieldName == 'lang_enum' || $fieldName == 'root_server_id' || $fieldName == 'source_id') {
                continue;
            }

            $payload = [];

            if ($fieldName == 'service_body_bigint') {
                $payload['serviceBodyId'] = $area->id_bigint;
            } elseif ($fieldName == 'venue_type') {
                $payload['venueType'] = Meeting::VENUE_TYPE_HYBRID;
            } elseif ($fieldName == 'weekday_tinyint') {
                $payload['day'] = 6;
            } elseif ($fieldName == 'start_time') {
                $payload['startTime'] = '08:00';
            } elseif ($fieldName == 'duration_time') {
                $payload['duration'] = '01:30';
            } elseif ($fieldName == 'published') {
                $payload['published'] = false;
            } elseif ($fieldName == 'email_contact') {
                $payload['email'] = 'test2@test2.com';
            } elseif ($fieldName == 'worldid_mixed') {
                $payload['worldId'] = 'test worldid!';
            } elseif ($fieldName == 'meeting_name') {
                $payload['name'] = 'test meeting name';
            } elseif ($fieldName == 'latitude') {
                $payload['latitude'] = 45.0;
            } elseif ($fieldName == 'longitude') {
                $payload['longitude'] = 45.0;
            } elseif ($fieldName == 'location_street') {
                $payload['longitude'] = 'translated street';
            } else {
                $payload[$fieldName] = "$fieldName test test test";
            }

            $this->withHeader('Authorization', "Bearer $token")
                ->patch("/api/v1/translations/meetings/$meetingId/de", $payload)
                ->assertStatus(204);

            $meeting = $this->withHeader('Authorization', "Bearer $token")
                ->get("/api/v1/meetings/$meetingId")
                ->assertStatus(200)
                ->decodeResponseJson()->json();
            $translation = $this->withHeader('Authorization', "Bearer $token")
                ->get("/api/v1/translations/meetings/$meetingId/de")
                ->assertStatus(200)
                ->decodeResponseJson()->json();

            $isMainField = in_array($fieldName, Meeting::$mainFields);
            $databaseFieldName = $fieldName;
            if (isset(self::$fieldNameMap[$fieldName])) {
                $fieldName = self::$fieldNameMap[$fieldName];
            }
            if ($fieldName == 'duration') {
                $this->assertEquals('01:00', $meeting[$fieldName]);
                $this->assertEquals('01:00', $translation[$fieldName]);
            } elseif ($fieldName == 'startTime') {
                $this->assertEquals('20:00', $meeting[$fieldName]);
                $this->assertEquals('20:00', $translation[$fieldName]);
            } elseif ($isMainField && isset($original[$fieldName])) {
                $this->assertEquals($original[$fieldName], $meeting[$fieldName]);
                $this->assertEquals($original[$fieldName], $translation[$fieldName]);
            } elseif ($isMainField && !isset($original[$databaseFieldName])) {
                $this->assertEmpty($meeting[$fieldName]);
                $this->assertEmpty($translation[$fieldName]);
            } elseif (isset($original[$fieldName]) && isset($payload[$fieldName])) {
                $this->assertEquals($original[$fieldName], $meeting[$fieldName]);
                $this->assertEquals($payload[$fieldName], $translation[$fieldName]);
            } elseif (isset($original[$fieldName]) && !isset($payload[$fieldName])) {
                $this->assertEquals($original[$fieldName], $meeting[$fieldName]);
                $this->assertEquals($original[$fieldName], $translation[$fieldName]);
            }
        }
    }
    public function testPartialUpdateClearsTranslation()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);
        $format = Format::query()->first();
        $meetingModel = $this->createMeeting(['meeting_name' => 'My name', 'service_body_bigint' => $area->id_bigint, 'formats' => strval($format->shared_id_bigint)]);
        $meetingId = $meetingModel->id_bigint;
        $translatedValues = ["meeting_name" => 'translated name', "location_street" => 'translated street'];
        $this->addTranslations($meetingModel, 'de', $translatedValues);
        $meeting = $this->withHeader('Authorization', "Bearer $token")
                ->get("/api/v1/meetings/$meetingId")
                ->assertStatus(200)
                ->decodeResponseJson()->json();
        $translation = $this->withHeader('Authorization', "Bearer $token")
                ->get("/api/v1/translations/meetings/$meetingId/de")
                ->assertStatus(200)
                ->decodeResponseJson()->json();
        foreach (array_keys($translatedValues) as $modelFieldName) {
            $fieldName = self::$fieldNameMap[$modelFieldName] ?? $modelFieldName;
            $this->assertEquals($this->dataFields[$modelFieldName], $meeting[$fieldName]);
            $this->assertEquals($translatedValues[$modelFieldName], $translation[$fieldName]);
        }
        $payload = ['location_street' => 'updated value'];
        $this->withHeader('Authorization', "Bearer $token")
            ->patch("/api/v1/meetings/$meetingId", $payload)
            ->assertStatus(204);
        $meeting = $this->withHeader('Authorization', "Bearer $token")
                ->get("/api/v1/meetings/$meetingId")
                ->assertStatus(200)
                ->decodeResponseJson()->json();
        $translation = $this->withHeader('Authorization', "Bearer $token")
                ->get("/api/v1/translations/meetings/$meetingId/de")
                ->assertStatus(200)
                ->decodeResponseJson()->json();
        foreach (array_keys($payload) as $fieldName) {
            $this->assertEquals($payload[$fieldName], $meeting[$fieldName]);
            $this->assertEquals($payload[$fieldName], $translation[$fieldName]);
        }
    }
    public function testTranslateCustomFields()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);
        $format = Format::query()->first();
        $customFields = ['customField1' => 'value1', 'customField2' => 'value2', 'customField3' => 'value3'];
        foreach (array_keys($customFields) as $fieldName) {
            $this->addCustomField($fieldName);
        }
        $meetingModel = $this->createMeeting(['meeting_name' => 'My name', 'service_body_bigint' => $area->id_bigint, 'formats' => strval($format->shared_id_bigint)], $customFields);
        $meetingId = $meetingModel->id_bigint;
        $meeting = $this->withHeader('Authorization', "Bearer $token")
                ->get("/api/v1/meetings/$meetingId")
                ->assertStatus(200)
                ->decodeResponseJson()->json();
        $translation = $this->withHeader('Authorization', "Bearer $token")
                ->get("/api/v1/translations/meetings/$meetingId/de")
                ->assertStatus(200)
                ->decodeResponseJson()->json();
        foreach (array_keys($customFields) as $fieldName) {
            $this->assertEquals($customFields[$fieldName], $meeting['customFields'][$fieldName]);
            $this->assertEquals($customFields[$fieldName], $translation['customFields'][$fieldName]);
        }

        $payload = ['customField1' => 'translated1'];
        $this->withHeader('Authorization', "Bearer $token")
            ->patch("/api/v1/translations/meetings/$meetingId/de", ['customFields' => $payload])
            ->assertStatus(204);

        $meeting = $this->withHeader('Authorization', "Bearer $token")
                ->get("/api/v1/meetings/$meetingId")
                ->assertStatus(200)
                ->decodeResponseJson()->json();
        $translation = $this->withHeader('Authorization', "Bearer $token")
                ->get("/api/v1/translations/meetings/$meetingId/de")
                ->assertStatus(200)
                ->decodeResponseJson()->json();
        foreach (array_keys($customFields) as $fieldName) {
            if (isset($payload[$fieldName])) {
                $this->assertEquals($customFields[$fieldName], $meeting['customFields'][$fieldName]);
                $this->assertEquals($payload[$fieldName], $translation['customFields'][$fieldName]);
            } else {
                $this->assertEquals($customFields[$fieldName], $meeting['customFields'][$fieldName]);
                $this->assertEquals($customFields[$fieldName], $translation['customFields'][$fieldName]);
            }
        }
    }
}
