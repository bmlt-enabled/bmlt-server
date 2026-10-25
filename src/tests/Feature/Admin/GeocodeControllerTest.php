<?php

namespace Tests\Feature\Admin;

use App\Exceptions\GeocodingException;
use App\Services\GeoApiGeocoder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class GeocodeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaderForAdmin(): array
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => "Bearer $token"];
    }

    public function testGeocodeRequiresAuthentication()
    {
        $this->postJson('/api/v1/geo/geocode', ['address' => '1600 Pennsylvania Ave'])
            ->assertStatus(401);
    }

    public function testGeocodeRequiresAddress()
    {
        $this->withHeaders($this->authHeaderForAdmin())
            ->postJson('/api/v1/geo/geocode', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('address');
    }

    public function testGeocodeReturnsCoordinates()
    {
        $mock = Mockery::mock(GeoApiGeocoder::class);
        $mock->shouldReceive('geocode')
            ->once()
            ->andReturn([
                'latitude' => 38.8976763,
                'longitude' => -77.0365298,
                'county' => '',
                'zipCode' => '20500',
                'formattedAddress' => '1600 Pennsylvania Avenue NW, Washington, DC 20500, USA',
            ]);
        $this->instance(GeoApiGeocoder::class, $mock);

        $this->withHeaders($this->authHeaderForAdmin())
            ->postJson('/api/v1/geo/geocode', ['address' => '1600 Pennsylvania Ave NW, Washington, DC'])
            ->assertStatus(200)
            ->assertExactJson([
                'latitude' => 38.8976763,
                'longitude' => -77.0365298,
                'county' => '',
                'zipCode' => '20500',
                'formattedAddress' => '1600 Pennsylvania Avenue NW, Washington, DC 20500, USA',
            ]);
    }

    public function testGeocodeReturnsUnprocessableOnZeroResults()
    {
        $mock = Mockery::mock(GeoApiGeocoder::class);
        $mock->shouldReceive('geocode')->once()->andThrow(GeocodingException::zeroResults());
        $this->instance(GeoApiGeocoder::class, $mock);

        $this->withHeaders($this->authHeaderForAdmin())
            ->postJson('/api/v1/geo/geocode', ['address' => 'nowhere at all'])
            ->assertStatus(422)
            ->assertJson(['message' => 'The address could not be geocoded.']);
    }

    public function testGeocodeReturnsServiceUnavailableWhenRateLimited()
    {
        $mock = Mockery::mock(GeoApiGeocoder::class);
        $mock->shouldReceive('geocode')
            ->once()
            ->andThrow(new GeocodingException('The geocoding service is temporarily unavailable. Please try again shortly.', 503));
        $this->instance(GeoApiGeocoder::class, $mock);

        $this->withHeaders($this->authHeaderForAdmin())
            ->postJson('/api/v1/geo/geocode', ['address' => '1600 Pennsylvania Ave'])
            ->assertStatus(503);
    }
}
