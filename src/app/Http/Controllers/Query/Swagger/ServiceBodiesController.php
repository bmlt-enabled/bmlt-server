<?php

namespace App\Http\Controllers\Query\Swagger;

use OpenApi\Attributes as OA;

class ServiceBodiesController extends Controller
{
    #[OA\Get(
        path: '/client_interface/json/?switcher=GetServiceBodies',
        operationId: 'getSemanticServiceBodies',
        summary: 'Get service bodies',
        tags: ['Service Bodies'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/SemServices'),
            new OA\Parameter(ref: '#/components/parameters/SemRecursive'),
            new OA\Parameter(name: 'parents', in: 'query', description: 'Set to `1` to include parent service bodies in the result.', schema: new OA\Schema(type: 'string', enum: ['0', '1'])),
            new OA\Parameter(ref: '#/components/parameters/SemRootServerIds'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of service bodies',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/SemanticServiceBody'))
            ),
        ]
    )]
    public function getServiceBodies()
    {
    }
}
