<?php

namespace Tests\Feature\Admin;

use App\Interfaces\TimeZoneRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class TimeZoneControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaderForAdmin(): array
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => "Bearer $token"];
    }

    public function testTimeZoneRequiresAuthentication()
    {
        $this->postJson('/api/v1/timezone', ['latitude' => 38.8976763, 'longitude' => -77.0365298])
            ->assertStatus(401);
    }

    public function testTimeZoneRequiresCoordinates()
    {
        $this->withHeaders($this->authHeaderForAdmin())
            ->postJson('/api/v1/timezone', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'longitude']);
    }

    public function testTimeZoneRejectsOutOfRangeCoordinates()
    {
        $this->withHeaders($this->authHeaderForAdmin())
            ->postJson('/api/v1/timezone', ['latitude' => 200, 'longitude' => -77.0365298])
            ->assertStatus(422)
            ->assertJsonValidationErrors('latitude');
    }

    public function testTimeZoneReturnsZoneForCoordinates()
    {
        $mock = Mockery::mock(TimeZoneRepositoryInterface::class);
        $mock->shouldReceive('getByCoordinates')
            ->once()
            ->with(38.8976763, -77.0365298)
            ->andReturn('America/New_York');
        $this->instance(TimeZoneRepositoryInterface::class, $mock);

        $this->withHeaders($this->authHeaderForAdmin())
            ->postJson('/api/v1/timezone', ['latitude' => 38.8976763, 'longitude' => -77.0365298])
            ->assertStatus(200)
            ->assertExactJson(['timeZone' => 'America/New_York']);
    }

    public function testTimeZoneReturnsNullOverOcean()
    {
        $mock = Mockery::mock(TimeZoneRepositoryInterface::class);
        $mock->shouldReceive('getByCoordinates')->once()->andReturnNull();
        $this->instance(TimeZoneRepositoryInterface::class, $mock);

        $this->withHeaders($this->authHeaderForAdmin())
            ->postJson('/api/v1/timezone', ['latitude' => 0, 'longitude' => -140])
            ->assertStatus(200)
            ->assertExactJson(['timeZone' => null]);
    }
}
