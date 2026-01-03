<?php

namespace Tests\Feature\Admin;

use App\Models\ServiceBody;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceBodyDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteServiceBodySuccess()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $zone = $this->createZone('test', 'test');

        $this->withHeader('Authorization', "Bearer $token")
            ->delete("/api/v1/servicebodies/$zone->id_bigint")
            ->assertStatus(204);

        $this->assertFalse(ServiceBody::query()->where('id_bigint', $zone->id_bigint)->exists());
    }

    public function testDeleteServiceBodyHasChildren()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $zone = $this->createZone('test', 'test');
        $this->createRegion('region', 'region', $zone->id_bigint);

        $this->withHeader('Authorization', "Bearer $token")
            ->delete("/api/v1/servicebodies/$zone->id_bigint")
            ->assertStatus(409);

        $this->assertTrue(ServiceBody::query()->where('id_bigint', $zone->id_bigint)->exists());
    }

    public function testDeleteServiceBodyHasMeetings()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $zone = $this->createZone('test', 'test');
        $this->createMeeting(['service_body_bigint' => $zone->id_bigint]);

        $this->withHeader('Authorization', "Bearer $token")
            ->delete("/api/v1/servicebodies/$zone->id_bigint")
            ->assertStatus(409);

        $this->assertTrue(ServiceBody::query()->where('id_bigint', $zone->id_bigint)->exists());
    }

    public function testDeleteServiceBodyForceDeleteWithMeetings()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $zone = $this->createZone('test', 'test');
        $meeting1 = $this->createMeeting(['service_body_bigint' => $zone->id_bigint]);
        $meeting2 = $this->createMeeting(['service_body_bigint' => $zone->id_bigint]);

        $this->withHeader('Authorization', "Bearer $token")
            ->delete("/api/v1/servicebodies/$zone->id_bigint?force=true")
            ->assertStatus(204);

        $this->assertFalse(ServiceBody::query()->where('id_bigint', $zone->id_bigint)->exists());
        $this->assertDatabaseMissing('comdef_meetings_main', ['id_bigint' => $meeting1->id_bigint]);
        $this->assertDatabaseMissing('comdef_meetings_main', ['id_bigint' => $meeting2->id_bigint]);
    }

    public function testDeleteServiceBodyForceDeleteWithoutMeetings()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $zone = $this->createZone('test', 'test');

        $this->withHeader('Authorization', "Bearer $token")
            ->delete("/api/v1/servicebodies/$zone->id_bigint?force=true")
            ->assertStatus(204);

        $this->assertFalse(ServiceBody::query()->where('id_bigint', $zone->id_bigint)->exists());
    }

    public function testDeleteServiceBodyForceDeleteWithChildrenFails()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $zone = $this->createZone('test', 'test');
        $region = $this->createRegion('region', 'region', $zone->id_bigint);

        $this->withHeader('Authorization', "Bearer $token")
            ->delete("/api/v1/servicebodies/$zone->id_bigint?force=true")
            ->assertStatus(409);

        $this->assertTrue(ServiceBody::query()->where('id_bigint', $zone->id_bigint)->exists());
        $this->assertTrue(ServiceBody::query()->where('id_bigint', $region->id_bigint)->exists());
    }
}
