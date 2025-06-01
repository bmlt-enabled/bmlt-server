<?php

namespace Tests\Feature\Admin;

use App\LegacyConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServerRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        LegacyConfig::reset();
        parent::tearDown();
    }

    public function testIndexRouteNotExists()
    {
        $this->get('/api/v1/servers')
            ->assertStatus(404);
    }

    public function testIndexRouteExists()
    {
        LegacyConfig::set('aggregator_mode_enabled', true);
        $this->get('/api/v1/servers')->assertStatus(200);
    }

    public function testShowRouteNotExists()
    {
        $server = $this->createServer(1);
        $this->get("/api/v1/servers/$server->id")
            ->assertStatus(404);
    }

    public function testShowRouteExists()
    {
        LegacyConfig::set('aggregator_mode_enabled', true);
        $server = $this->createServer(1);
        $this->get("/api/v1/servers/$server->id")->assertStatus(200);
    }
}
