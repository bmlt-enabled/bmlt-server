<?php

namespace Tests\Feature\Admin;

use App\ConfigFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RootServerShowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        ConfigFile::reset();
        parent::tearDown();
    }

    public function testNullLastSuccessfulImport()
    {
        ConfigFile::set('aggregator_mode_enabled', true);
        $rootServer = $this->createRootServer(123);
        $this->get("/api/v1/rootservers/$rootServer->id")
            ->assertStatus(200)
            ->assertExactJson([
                'id' => $rootServer->id,
                'sourceId' => $rootServer->source_id,
                'name' => $rootServer->name,
                'url' => $rootServer->url,
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
        ConfigFile::set('aggregator_mode_enabled', true);
        $rootServer = $this->createRootServer(123);
        $rootServer->last_successful_import = $rootServer->updated_at;
        $rootServer->save();
        $this->get("/api/v1/rootservers/$rootServer->id")
            ->assertStatus(200)
            ->assertExactJson([
                'id' => $rootServer->id,
                'sourceId' => $rootServer->source_id,
                'name' => $rootServer->name,
                'url' => $rootServer->url,
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
                'lastSuccessfulImport' => $rootServer->last_successful_import->format('Y-m-d H:i:s'),
            ]);
    }
}
