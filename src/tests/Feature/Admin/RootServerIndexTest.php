<?php

namespace Tests\Feature\Admin;

use App\ConfigFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RootServerIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        ConfigFile::reset();
        parent::tearDown();
    }

    public function test()
    {
        ConfigFile::set('aggregator_mode_enabled', true);
        $this->createRootServer(123);
        $this->createRootServer(123, 'test2', 'https://test2.com');
        $this->get("/api/v1/rootservers")
            ->assertStatus(200)
            ->assertJsonCount(2);
    }
}
