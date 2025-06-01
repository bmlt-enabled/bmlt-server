<?php

namespace Tests\Feature\Admin;

use App\LegacyConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nette\Utils\DateTime;

class ServerShowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        LegacyConfig::reset();
        parent::tearDown();
    }

    public function testNullLastSuccessfulImport()
    {
        LegacyConfig::set('aggregator_mode_enabled', true);
        $server = $this->createServer(123);
        $this->get("/api/v1/servers/$server->id")
            ->assertStatus(200)
            ->assertExactJson([
                'id' => $server->id,
                'sourceId' => $server->source_id,
                'name' => $server->name,
                'url' => $server->url,
                'statistics' => [
                    'meetings' => [
                        'numTotal' => null,
                        'numInPerson' => null,
                        'numVirtual' => null,
                        'numHybrid' => null,
                        'numUnknown' => null,
                    ],
                    'serviceBodies' => [
                        'numAreas' => null,
                        'numRegions' => null,
                        'numZones' => null,
                        'numGroups' => null,
                    ]
                ],
                'serverInfo' => null,
                'lastSuccessfulImport' => null,
            ]);
    }

    public function testNonNullLastSuccessfulImport()
    {
        LegacyConfig::set('aggregator_mode_enabled', true);
        $server = $this->createServer(123);
        $server->last_successful_import = $server->updated_at;
        $server->save();
        $this->get("/api/v1/servers/$server->id")
            ->assertStatus(200)
            ->assertExactJson([
                'id' => $server->id,
                'sourceId' => $server->source_id,
                'name' => $server->name,
                'url' => $server->url,
                'statistics' => [
                    'meetings' => [
                        'numTotal' => null,
                        'numInPerson' => null,
                        'numVirtual' => null,
                        'numHybrid' => null,
                        'numUnknown' => null,
                    ],
                    'serviceBodies' => [
                        'numAreas' => null,
                        'numRegions' => null,
                        'numZones' => null,
                        'numGroups' => null,
                    ]
                ],
                'serverInfo' => null,
                'lastSuccessfulImport' => $server->last_successful_import->format('Y-m-d H:i:s'),
            ]);
    }
}
