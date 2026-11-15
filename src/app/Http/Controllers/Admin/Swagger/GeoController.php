<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GeocodeRequest',
    required: ['address'],
    properties: [
        new OA\Property(property: 'address', type: 'string', description: 'The address to geocode.', example: '1600 Pennsylvania Ave NW, Washington, DC, us'),
    ]
)]
#[OA\Schema(
    schema: 'GeocodeResponse',
    properties: [
        new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 38.8976763),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', example: -77.0365298),
        new OA\Property(property: 'county', type: 'string', example: ''),
        new OA\Property(property: 'zipCode', type: 'string', example: '20500'),
        new OA\Property(property: 'formattedAddress', type: 'string', example: '1600 Pennsylvania Avenue NW, Washington, DC 20500, USA'),
    ]
)]
#[OA\Schema(
    schema: 'TimeZoneRequest',
    required: ['latitude', 'longitude'],
    properties: [
        new OA\Property(property: 'latitude', type: 'number', format: 'float', description: 'The latitude to look up.', example: 38.8976763),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', description: 'The longitude to look up.', example: -77.0365298),
    ]
)]
#[OA\Schema(
    schema: 'TimeZoneResponse',
    properties: [
        new OA\Property(property: 'timeZone', type: 'string', nullable: true, description: 'The IANA time zone identifier for the coordinates, or null if none applies (for example, open ocean).', example: 'America/New_York'),
    ]
)]
class GeoController extends Controller
{
    #[OA\Post(
        path: '/api/v1/geo/geocode',
        operationId: 'geocode',
        description: 'Geocode an address to coordinates via the BMLT Geo API. Requires a BMLT Geo API key to be configured in settings.',
        summary: 'Geocode an address',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'The address to geocode',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GeocodeRequest')
        ),
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 200, description: 'Returns the geocoded coordinates.', content: new OA\JsonContent(ref: '#/components/schemas/GeocodeResponse')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 422, description: 'Returns when the address is missing or could not be geocoded.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 503, description: 'Returns when the geocoding service is temporarily unavailable.'),
        ]
    )]
    public function geocode()
    {
    }

    #[OA\Post(
        path: '/api/v1/geo/timezone',
        operationId: 'timezone',
        description: 'Look up the IANA time zone for a set of coordinates using the bundled offline time zone boundary data.',
        summary: 'Look up a time zone by coordinates',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in the coordinates to look up.',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TimeZoneRequest')
        ),
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 200, description: 'Returns the time zone for the coordinates.', content: new OA\JsonContent(ref: '#/components/schemas/TimeZoneResponse')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when the user is not allowed.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function timezone()
    {
    }
}
