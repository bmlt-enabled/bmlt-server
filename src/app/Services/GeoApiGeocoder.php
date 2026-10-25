<?php

namespace App\Services;

use App\Exceptions\GeocodingException;
use Illuminate\Support\Facades\App;
use OpenAPI\Client\Api\GeocodingApi;
use OpenAPI\Client\ApiException;
use OpenAPI\Client\Configuration;

class GeoApiGeocoder
{
    private const GEO_API_HOST = 'https://geo.aws.bmlt.app';
    private const POSTAL_CODE_TYPE = 'postal_code';
    private const ADMIN_AREA_LEVEL_2 = 'administrative_area_level_2';

    public function __construct(private ?GeocodingApi $api = null)
    {
    }

    /**
     * Geocode an address string to coordinates via the BMLT Geo API.
     *
     * @return array{latitude: float, longitude: float, county: string, zipCode: string, formattedAddress: string}
     *
     * @throws GeocodingException
     */
    public function geocode(string $address, string $referrer): array
    {
        $application = (string) (bmlt_config('bmltTitle') ?: 'bmlt-server');
        $regionBias = bmlt_config('regionBias');
        $region = is_string($regionBias) && $regionBias !== '' ? $regionBias : null;

        try {
            $response = $this->api()->geocode($application, $address, $referrer, App::currentLocale(), $region);
        } catch (ApiException $e) {
            throw GeocodingException::fromApiException($e);
        }

        if ($response->getStatus() !== 'OK' || empty($response->getResults())) {
            throw GeocodingException::zeroResults();
        }

        $result = $response->getResults()[0];
        $location = $result->getGeometry()?->getLocation();
        if (!$location || is_null($location->getLat()) || is_null($location->getLng())) {
            throw GeocodingException::zeroResults();
        }

        $county = '';
        $zipCode = '';
        foreach ($result->getAddressComponents() ?? [] as $component) {
            $types = $component->getTypes() ?? [];
            if (in_array(self::POSTAL_CODE_TYPE, $types, true)) {
                $zipCode = (string) $component->getLongName();
            }
            if (in_array(self::ADMIN_AREA_LEVEL_2, $types, true)) {
                $county = $this->stripCountySuffix((string) $component->getLongName());
            }
        }

        return [
            'latitude' => (float) $location->getLat(),
            'longitude' => (float) $location->getLng(),
            'county' => $county,
            'zipCode' => $zipCode,
            'formattedAddress' => (string) $result->getFormattedAddress(),
        ];
    }

    private function api(): GeocodingApi
    {
        if ($this->api === null) {
            $config = (new Configuration())
                ->setHost(self::GEO_API_HOST)
                ->setAccessToken((string) bmlt_config('geoApiKey'));
            $this->api = new GeocodingApi(config: $config);
        }

        return $this->api;
    }

    private function stripCountySuffix(string $county): string
    {
        return preg_replace('/ County$/', '', $county) ?? $county;
    }
}
