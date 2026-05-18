<?php

namespace App\Http\Controllers\Query\Swagger;

use OpenApi\Attributes as OA;

class ServerController extends Controller
{
    #[OA\Get(
        path: '/client_interface/json/?switcher=GetServerInfo',
        operationId: 'getSemanticServerInfo',
        summary: 'Get server information',
        tags: ['Server'],
        responses: [
            new OA\Response(
                response: 200,
                description: "Server metadata such as version, language, semantic admin URL, etc.",
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/SemanticServerInfo'))
            ),
        ]
    )]
    public function getServerInfo()
    {
    }

    #[OA\Get(
        path: '/client_interface/json/?switcher=GetCoverageArea',
        operationId: 'getCoverageArea',
        summary: "Get the geographic coverage bounding box for this server",
        tags: ['Server'],
        responses: [
            new OA\Response(
                response: 200,
                description: "Bounding box for the area covered by this server's meetings",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'nw_corner_longitude', type: 'string', description: 'Longitude of the north-west corner of the bounding box.'),
                            new OA\Property(property: 'nw_corner_latitude', type: 'string', description: 'Latitude of the north-west corner of the bounding box.'),
                            new OA\Property(property: 'se_corner_longitude', type: 'string', description: 'Longitude of the south-east corner of the bounding box.'),
                            new OA\Property(property: 'se_corner_latitude', type: 'string', description: 'Latitude of the south-east corner of the bounding box.'),
                        ]
                    )
                )
            ),
        ]
    )]
    public function getCoverageArea()
    {
    }
}
