<?php

namespace Tests\Feature\Aggregator;

use App\LegacyConfig;
use App\Models\ServiceBody;
use App\Repositories\External\ExternalServiceBody;
use App\Repositories\ServiceBodyRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\TestCase;

class ImportServiceBodyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        LegacyConfig::reset();
        parent::tearDown();
    }

    private function external(): ExternalServiceBody
    {
        return new ExternalServiceBody([
            'id' => '171',
            'parent_id' => '0',
            'name' => 'Trans Umbrella Area',
            'description' => 'description',
            'type' => 'AS',
            'url' => 'http://transuana.org',
            'helpline' => 'helpline',
            'world_id' => 'AR6339',
        ]);
    }

    private function create(int $serverId, int $sourceId): ServiceBody
    {
        $repository = new ServiceBodyRepository();
        return $repository->create([
            'server_id' => $serverId,
            'source_id' => $sourceId,
            'name_string' => 'some name',
            'description_string' => 'some description',
            'sb_type' => 'some type',
            'uri_string' => 'https://otherurl.com',
            'kml_file_uri_string' => 'some helpline',
            'worldid_mixed' => 'some world id',
            'sb_meeting_email' => '',
        ]);
    }

    public function testCreate()
    {
        LegacyConfig::set('aggregator_mode_enabled', true);
        $server1 = $this->createServer(1);

        $external = $this->external();

        $repository = new ServiceBodyRepository();
        $repository->import($server1->id, collect([$external]));

        $all = $repository->search();
        $this->assertEquals(1, $all->count());

        $db = $all->first();
        $this->assertEquals($server1->id, $db->server_id);
        $this->assertTrue($external->isEqual($db));
    }

    public function testUpdate()
    {
        LegacyConfig::set('aggregator_mode_enabled', true);
        $server1 = $this->createServer(1);
        $server2 = $this->createServer(2);

        $external = $this->external();

        $this->create($server1->id, $external->id);
        $this->create($server2->id, $external->id);

        $repository = new ServiceBodyRepository();
        $repository->import($server1->id, collect([$external]));

        $all = $repository->search();
        $this->assertEquals(2, $all->count());

        $db = $all->firstWhere('server_id', $server1->id);
        $this->assertNotNull($db);
        $this->assertEquals($server1->id, $db->server_id);
        $this->assertTrue($external->isEqual($db));

        $db = $all->firstWhere('server_id', $server2->id);
        $this->assertNotNull($db);
        $this->assertEquals($server2->id, $db->server_id);
        $this->assertFalse($external->isEqual($db));
    }

    public function testDelete()
    {
        LegacyConfig::set('aggregator_mode_enabled', true);
        $server1 = $this->createServer(1);
        $server2 = $this->createServer(2);
        $server3 = $this->createServer(3);

        $external = $this->external();

        $this->create($server1->id, $external->id);
        $this->create($server1->id, $external->id + 1);
        $this->create($server2->id, $external->id);
        $this->create($server3->id, $external->id);

        $repository = new ServiceBodyRepository();
        $repository->import($server1->id, collect([$external]));

        $all = $repository->search();
        $this->assertEquals(3, $all->count());

        $this->assertEquals(1, $all->where('server_id', $server1->id)->count());
        $db = $all->firstWhere('server_id', $server1->id);
        $this->assertNotNull($db);
        $this->assertEquals($server1->id, $db->server_id);
        $this->assertTrue($external->isEqual($db));

        $this->assertEquals(1, $all->where('server_id', $server2->id)->count());
        $db = $all->firstWhere('server_id', $server2->id);
        $this->assertNotNull($db);
        $this->assertEquals($server2->id, $db->server_id);
        $this->assertFalse($external->isEqual($db));

        $this->assertEquals(1, $all->where('server_id', $server3->id)->count());
        $db = $all->firstWhere('server_id', $server3->id);
        $this->assertNotNull($db);
        $this->assertEquals($server3->id, $db->server_id);
        $this->assertFalse($external->isEqual($db));
    }

    public function testSbOwnerAssignment()
    {
        LegacyConfig::set('aggregator_mode_enabled', true);
        $server1 = $this->createServer(1);
        $server2 = $this->createServer(2);

        $externalTop = $this->external();
        $externalTop->id = 1;
        $externalTop->parentId = 0;

        $externalMiddle = $this->external();
        $externalMiddle->id = 2;
        $externalMiddle->parentId = 1;

        $externalBottom = $this->external();
        $externalBottom->id = 3;
        $externalBottom->parentId = 2;

        $this->create($server2->id, $externalTop->id);
        $this->create($server2->id, $externalMiddle->id);
        $this->create($server2->id, $externalBottom->id);

        $repository = new ServiceBodyRepository();
        $repository->import($server1->id, collect([$externalTop, $externalMiddle, $externalBottom]));

        $all = $repository->search(serversInclude: [$server1->id]);
        $this->assertEquals(3, $all->count());
        $serviceBodyTop = $all->firstWhere('source_id', $externalTop->id);
        $serviceBodyMiddle = $all->firstWhere('source_id', $externalMiddle->id);
        $serviceBodyBottom = $all->firstWhere('source_id', $externalBottom->id);
        $this->assertEquals(0, $serviceBodyTop->sb_owner);
        $this->assertEquals($serviceBodyTop->id_bigint, $serviceBodyMiddle->sb_owner);
        $this->assertEquals($serviceBodyMiddle->id_bigint, $serviceBodyBottom->sb_owner);

        $all = $repository->search(serversInclude: [$server2->id]);
        $this->assertEquals(3, $all->count());
        $serviceBodyTop = $all->firstWhere('source_id', $externalTop->id);
        $serviceBodyMiddle = $all->firstWhere('source_id', $externalMiddle->id);
        $serviceBodyBottom = $all->firstWhere('source_id', $externalBottom->id);
        $this->assertEquals(0, $serviceBodyTop->sb_owner);
        $this->assertEquals(0, $serviceBodyMiddle->sb_owner);
        $this->assertEquals(0, $serviceBodyBottom->sb_owner);
    }
}
