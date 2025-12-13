<?php

namespace Tests\Feature\Admin;

use App\FromFileConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RootServerRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        FromFileConfig::reset();
        parent::tearDown();
    }

    public function testIndexRouteNotExists()
    {
        $this->get('/api/v1/rootservers')
            ->assertStatus(404);
    }

    public function testIndexRouteExists()
    {
        FromFileConfig::set('aggregator_mode_enabled', true);
        $this->get('/api/v1/rootservers')->assertStatus(200);
    }

    public function testShowRouteNotExists()
    {
        $rootServer = $this->createRootServer(1);
        $this->get("/api/v1/rootservers/$rootServer->id")
            ->assertStatus(404);
    }

    public function testShowRouteExists()
    {
        FromFileConfig::set('aggregator_mode_enabled', true);
        $rootServer = $this->createRootServer(1);
        $this->get("/api/v1/rootservers/$rootServer->id")->assertStatus(200);
    }
}
