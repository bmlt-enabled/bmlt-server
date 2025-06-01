<?php

namespace Tests\Feature\Aggregator;

use App\Models\Server;
use App\Repositories\External\ExternalServer;
use App\Repositories\ServerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\TestCase;

class ImportServerTest extends TestCase
{
    use RefreshDatabase;

    private function externalServer(): ExternalServer
    {
        return new ExternalServer([
            'id' => 1,
            'name' => 'test',
            'url' => 'https://blah.com/blah',
        ]);
    }

    public function testCreate()
    {
        $serverRepository = new ServerRepository();
        $external = $this->externalServer();
        $serverRepository->import(collect([$external]));
        $servers = $serverRepository->search();
        $this->assertEquals(1, $servers->count());
        $this->assertTrue($external->isEqual($servers->first()));
    }

    public function testUpdate()
    {
        $serverRepository = new ServerRepository();
        $serverRepository->create(['source_id' => 1, 'name' => 'test', 'url' => 'https://test.com']);
        $external = $this->externalServer();
        $serverRepository->import(collect([$external]));
        $servers = $serverRepository->search();
        $this->assertEquals(1, $servers->count());
        $this->assertTrue($external->isEqual($servers->first()));
    }

    public function testDelete()
    {
        $serverRepository = new ServerRepository();
        $serverRepository->create(['source_id' => 2, 'name' => 'test', 'url' => 'https://test.com']);
        $external = $this->externalServer();
        $serverRepository->import(collect([$external]));
        $servers = $serverRepository->search();
        $this->assertEquals(1, $servers->count());
        $this->assertTrue($external->isEqual($servers->first()));
    }
}
