<?php

namespace Tests\Feature\Admin;

use App\LegacyConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServerIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        LegacyConfig::reset();
        parent::tearDown();
    }

    public function test()
    {
        LegacyConfig::set('aggregator_mode_enabled', true);
        $this->createServer(123);
        $this->createServer(123, 'test2', 'https://test2.com');
        $this->get("/api/v1/servers")
            ->assertStatus(200)
            ->assertJsonCount(2);
    }
}
