<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RootServerBase',
    properties: [
        new OA\Property(property: 'sourceId', type: 'integer', example: 0),
        new OA\Property(property: 'name', type: 'string', example: 'string'),
        new OA\Property(property: 'url', type: 'string', example: 'https://example.com/main_server'),
        new OA\Property(
            property: 'statistics',
            type: 'object',
            required: ['serviceBodies', 'meetings'],
            properties: [
                new OA\Property(
                    property: 'serviceBodies',
                    type: 'object',
                    required: ['numZones', 'numRegions', 'numAreas', 'numGroups'],
                    properties: [
                        new OA\Property(property: 'numZones', type: 'integer', example: 0),
                        new OA\Property(property: 'numRegions', type: 'integer', example: 0),
                        new OA\Property(property: 'numAreas', type: 'integer', example: 0),
                        new OA\Property(property: 'numGroups', type: 'integer', example: 0),
                    ]
                ),
                new OA\Property(
                    property: 'meetings',
                    type: 'object',
                    required: ['numTotal', 'numInPerson', 'numVirtual', 'numHybrid', 'numUnknown'],
                    properties: [
                        new OA\Property(property: 'numTotal', type: 'integer', example: 0),
                        new OA\Property(property: 'numInPerson', type: 'integer', example: 0),
                        new OA\Property(property: 'numVirtual', type: 'integer', example: 0),
                        new OA\Property(property: 'numHybrid', type: 'integer', example: 0),
                        new OA\Property(property: 'numUnknown', type: 'integer', example: 0),
                    ]
                ),
            ]
        ),
        new OA\Property(property: 'serverInfo', type: 'string', example: 'string'),
        new OA\Property(property: 'lastSuccessfulImport', type: 'string', format: 'date-time', example: '2022-11-25 04:16:26'),
    ]
)]
#[OA\Schema(
    schema: 'RootServer',
    required: ['id', 'sourceId', 'name', 'url', 'lastSuccessfulImport'],
    allOf: [new OA\Schema(ref: '#/components/schemas/RootServerBase')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 0),
    ]
)]
#[OA\Schema(
    schema: 'RootServerCollection',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/RootServer')
)]
class RootServerController extends Controller
{
    #[OA\Get(
        path: '/api/v1/rootservers',
        operationId: 'getRootServers',
        description: 'Retrieve root servers.',
        summary: 'Retrieves root servers',
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 200, description: 'Successful response.', content: new OA\JsonContent(ref: '#/components/schemas/RootServerCollection')),
            new OA\Response(response: 404, description: 'Returns when aggregator mode is disabled.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function index()
    {
    }

    #[OA\Get(
        path: '/api/v1/rootservers/{rootServerId}',
        operationId: 'getRootServer',
        description: 'Retrieve a single root server id.',
        summary: 'Retrieves a root server',
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'rootServerId', in: 'path', description: 'ID of root server', required: true, example: '1', schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful response.', content: new OA\JsonContent(ref: '#/components/schemas/RootServer')),
            new OA\Response(response: 404, description: 'Returns when no root server exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function show()
    {
    }
}
