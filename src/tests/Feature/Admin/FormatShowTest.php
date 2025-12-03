<?php

namespace Tests\Feature\Admin;

use App\Models\Format;
use App\Models\FormatTranslation;
use App\Models\FormatMain;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FormatShowTest extends TestCase
{
    use RefreshDatabase;


    private function createFormat(array $values): Format
    {
        $values = array_merge([
            'key_string' => 'T',
            'worldid_mixed' => 'test',
            'lang_enum' => 'en',
            'name_string' => 'test',
            'description_string' => 'test',
            'format_type_enum' => 'FC1',
        ], $values);
        $main = FormatMain::create([
            'worldid_mixed' => $values['worldid_mixed'],
            'format_type_enum' => $values['format_type_enum'],
        ]);
        return Format::create([
            'shared_id_bigint' => $main->shared_id_bigint,
            'key_string' => $values['key_string'],
            'name_string' => $values['name_string'],
            'lang_enum' => $values['lang_enum'],
            'description_string' => $values['description_string'],

        ]);
    }

    public function testShowFormatWorldIdNotNull()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['worldid_mixed' => 'blah']);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['worldId']);
        $this->assertEquals($format->main->worldid_mixed, $data['worldId']);
    }

    public function testShowFormatWorldIdNull()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['worldid_mixed' => null]);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertNull($data['worldId']);
    }

    public function testShowFormatTypeNotNull()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['format_type_enum' => 'FC3']);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['type']);
        $this->assertEquals(FormatTypeConsts::COMDEF_TYPE_TO_TYPE_MAP[$format->main->format_type_enum], $data['type']);
    }

    public function testShowFormatTypeNull()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['format_type_enum' => null]);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertNull($data['type']);
    }

    public function testShowFormatKeyNotNull()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['key_string' => 'blah']);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['translations'][0]['key']);
        $this->assertEquals($format->key_string, $data['translations'][0]['key']);
    }

    public function testShowFormatKeyNull()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['key_string' => null]);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['translations'][0]['key']);
        $this->assertEquals('', $data['translations'][0]['key']);
    }

    public function testShowFormatNameNotNull()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['name_string' => 'blah']);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['translations'][0]['name']);
        $this->assertEquals($format->name_string, $data['translations'][0]['name']);
    }

    public function testShowFormatNameNull()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['name_string' => null]);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['translations'][0]['name']);
        $this->assertEquals('', $data['translations'][0]['name']);
    }

    public function testShowFormatDescriptionNotNull()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['description_string' => 'blah']);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['translations'][0]['description']);
        $this->assertEquals($format->description_string, $data['translations'][0]['description']);
    }

    public function testShowFormatDescriptionNull()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['description_string' => null]);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['translations'][0]['description']);
        $this->assertEquals('', $data['translations'][0]['description']);
    }

    public function testShowFormatLanguage()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $format = $this->createFormat(['lang_enum' => 'blah']);
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/formats/$format->shared_id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['translations'][0]['language']);
        $this->assertEquals($format->lang_enum, $data['translations'][0]['language']);
    }
}
