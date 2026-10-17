<?php

namespace Tests\Feature;

use App\Repositories\TimeZoneRepository;
use Tests\TestCase;

class TimeZoneRepositoryTest extends TestCase
{
    public function testResolvesKnownCoordinatesToIanaZones()
    {
        $repository = new TimeZoneRepository();

        $this->assertEquals('America/New_York', $repository->getByCoordinates(40.7128, -74.0060));
        $this->assertEquals('Europe/London', $repository->getByCoordinates(51.5074, -0.1278));
        $this->assertEquals('Australia/Sydney', $repository->getByCoordinates(-33.8688, 151.2093));
    }

    public function testReturnsNullForOutOfRangeCoordinates()
    {
        $repository = new TimeZoneRepository();

        $this->assertNull($repository->getByCoordinates(200.0, 0.0));
        $this->assertNull($repository->getByCoordinates(0.0, 999.0));
        $this->assertNull($repository->getByCoordinates(NAN, 0.0));
    }

    public function testResolvesSingleZoneNationsHoweverTheyAreWritten()
    {
        $repository = new TimeZoneRepository();

        $this->assertEquals('Asia/Kathmandu', $repository->getByNation('Nepal'));
        $this->assertEquals('Asia/Kathmandu', $repository->getByNation('  NEPAL. '));
        $this->assertEquals('Europe/Copenhagen', $repository->getByNation('dk'));
        $this->assertEquals('Europe/London', $repository->getByNation('Northern  Ireland'));
        $this->assertEquals('Asia/Bangkok', $repository->getByNation('THAÏLANDE'));
    }

    public function testReturnsNullForNationsWithSeveralZones()
    {
        $repository = new TimeZoneRepository();

        $this->assertNull($repository->getByNation('US'));
        $this->assertNull($repository->getByNation('Brasil'));
    }

    public function testReturnsNullForUnrecognizedNations()
    {
        $repository = new TimeZoneRepository();

        $this->assertNull($repository->getByNation(''));
        $this->assertNull($repository->getByNation('   '));
        $this->assertNull($repository->getByNation('Deutsch'));
        $this->assertNull($repository->getByNation('ZZ'));
    }

    public function testReturnsNullWhenBoundaryDataMissing()
    {
        config([
            'aggregator.timezone_index_path' => '/nonexistent/index.json',
            'aggregator.timezone_data_path' => '/nonexistent/geo.dat',
        ]);

        $repository = new TimeZoneRepository();

        $this->assertNull($repository->getByCoordinates(40.7128, -74.0060));
    }
}
