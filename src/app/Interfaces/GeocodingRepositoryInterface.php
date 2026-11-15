<?php

namespace App\Interfaces;

use App\Exceptions\GeocodingException;

interface GeocodingRepositoryInterface
{
    /**
     * Geocode an address string to coordinates via the BMLT Geo API.
     *
     * @return array{latitude: float, longitude: float, county: string, zipCode: string, formattedAddress: string}
     *
     * @throws GeocodingException
     */
    public function geocode(string $address, string $referrer): array;
}
