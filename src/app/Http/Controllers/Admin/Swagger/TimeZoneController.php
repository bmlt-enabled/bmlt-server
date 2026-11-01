<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TimeZoneRequest',
    required: ['latitude', 'longitude'],
    properties: [
        new OA\Property(property: 'latitude', type: 'number', format: 'float', description: 'The latitude to look up.', example: 38.8976763),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', description: 'The longitude to look up.', example: -77.0365298),
    ]
)]
#[OA\Schema(
    schema: 'TimeZoneResult',
    properties: [
        new OA\Property(property: 'timeZone', type: 'string', nullable: true, description: 'The IANA time zone identifier for the coordinates, or null if none applies (for example, open ocean).', example: 'America/New_York'),
    ]
)]
class TimeZoneController extends Controller
{
    #[OA\Post(
        path: '/api/v1/timezone',
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
            new OA\Response(response: 200, description: 'Returns the time zone for the coordinates.', content: new OA\JsonContent(ref: '#/components/schemas/TimeZoneResult')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when the user is not allowed.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store()
    {
    }
}
