<?php

namespace Tests\Feature\Admin;

use App\FromDatabaseConfig;
use App\Http\Resources\Admin\MeetingResource;
use App\Models\Meeting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class MeetingTranslateTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        FromDatabaseConfig::reset();
        MeetingResource::resetStaticVariables();
        parent::tearDown();
    }

    public function testTranslateEndpointWithMockedGoogle()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);

        $meeting = $this->createMeeting(
            ['service_body_bigint' => $area->id_bigint],
            [
                'location_text' => 'Community Center',
                'location_street' => '123 Main St',
            ]
        );

        FromDatabaseConfig::set('googleApiKey', 'test-api-key');

        Http::fake([
            'translation.googleapis.com/*' => Http::response([
                'data' => [
                    'translations' => [
                        ['translatedText' => 'Centro Comunitario'],
                        ['translatedText' => 'Calle Principal 123'],
                    ],
                ],
            ]),
        ]);

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/meetings/{$meeting->id_bigint}/translate", [
                'targetLanguages' => ['es'],
                'sourceLanguage' => 'en',
            ])
            ->assertStatus(200)
            ->json();

        $this->assertArrayHasKey('es', $data);
        $this->assertEquals('Centro Comunitario', $data['es']['location_text']);
        $this->assertEquals('Calle Principal 123', $data['es']['location_street']);
    }

    public function testTranslateEndpointWithSourceTexts()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);

        $meeting = $this->createMeeting(
            ['service_body_bigint' => $area->id_bigint],
        );

        FromDatabaseConfig::set('googleApiKey', 'test-api-key');

        Http::fake([
            'translation.googleapis.com/*' => Http::response([
                'data' => [
                    'translations' => [
                        ['translatedText' => 'Iglesia'],
                    ],
                ],
            ]),
        ]);

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/meetings/{$meeting->id_bigint}/translate", [
                'targetLanguages' => ['es'],
                'sourceLanguage' => 'en',
                'sourceTexts' => [
                    'location_text' => 'Church',
                ],
            ])
            ->assertStatus(200)
            ->json();

        $this->assertEquals('Iglesia', $data['es']['location_text']);
    }

    public function testTranslateEndpointWithMultipleTargetLanguages()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);

        $meeting = $this->createMeeting(
            ['service_body_bigint' => $area->id_bigint],
            ['location_text' => 'Church']
        );

        FromDatabaseConfig::set('googleApiKey', 'test-api-key');

        Http::fake([
            'translation.googleapis.com/*' => Http::sequence()
                ->push([
                    'data' => [
                        'translations' => [
                            ['translatedText' => 'Iglesia'],
                        ],
                    ],
                ])
                ->push([
                    'data' => [
                        'translations' => [
                            ['translatedText' => 'Eglise'],
                        ],
                    ],
                ]),
        ]);

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/meetings/{$meeting->id_bigint}/translate", [
                'targetLanguages' => ['es', 'fr'],
                'sourceLanguage' => 'en',
            ])
            ->assertStatus(200)
            ->json();

        $this->assertArrayHasKey('es', $data);
        $this->assertArrayHasKey('fr', $data);
    }

    public function testTranslateEndpointReturns422WithoutGoogleApiKey()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);

        $meeting = $this->createMeeting(
            ['service_body_bigint' => $area->id_bigint],
        );

        // Don't set googleApiKey - it should be empty by default

        $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/meetings/{$meeting->id_bigint}/translate", [
                'targetLanguages' => ['es'],
                'sourceLanguage' => 'en',
            ])
            ->assertStatus(422);
    }

    public function testTranslateEndpointRequiresAuthentication()
    {
        $user = $this->createAdminUser();
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);

        $meeting = $this->createMeeting(
            ['service_body_bigint' => $area->id_bigint],
        );

        $this->postJson("/api/v1/meetings/{$meeting->id_bigint}/translate", [
            'targetLanguages' => ['es'],
            'sourceLanguage' => 'en',
        ])->assertStatus(401);
    }

    public function testTranslateEndpointValidatesRequest()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);

        $meeting = $this->createMeeting(
            ['service_body_bigint' => $area->id_bigint],
        );

        FromDatabaseConfig::set('googleApiKey', 'test-api-key');

        // Missing required fields
        $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/meetings/{$meeting->id_bigint}/translate", [])
            ->assertStatus(422);

        // Missing sourceLanguage
        $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/meetings/{$meeting->id_bigint}/translate", [
                'targetLanguages' => ['es'],
            ])
            ->assertStatus(422);
    }

    public function testTranslateEndpointDoesNotSaveData()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area1', 'area1', 0, adminUserId: $user->id_bigint);

        $meeting = $this->createMeeting(
            ['service_body_bigint' => $area->id_bigint],
            ['location_text' => 'Community Center']
        );

        FromDatabaseConfig::set('googleApiKey', 'test-api-key');

        Http::fake([
            'translation.googleapis.com/*' => Http::response([
                'data' => [
                    'translations' => [
                        ['translatedText' => 'Centro Comunitario'],
                    ],
                ],
            ]),
        ]);

        $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/meetings/{$meeting->id_bigint}/translate", [
                'targetLanguages' => ['es'],
                'sourceLanguage' => 'en',
            ])
            ->assertStatus(200);

        MeetingResource::resetStaticVariables();

        // The translation should NOT be saved in the database
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/meetings/{$meeting->id_bigint}")
            ->assertStatus(200)
            ->json();

        $this->assertEmpty((array) $data['locationTranslations']);
    }
}
