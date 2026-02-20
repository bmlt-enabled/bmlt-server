<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ErrorTest',
    properties: [
        new OA\Property(property: 'arbitrary_string', type: 'string', example: 'string'),
        new OA\Property(property: 'arbitrary_int', type: 'integer', example: 123),
        new OA\Property(property: 'force_server_error', type: 'boolean', example: true),
    ]
)]
class ErrorTestController extends Controller
{
    #[OA\Post(
        path: '/api/v1/errortest',
        operationId: 'createErrorTest',
        description: 'Tests some errors.',
        summary: 'Tests some errors',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in error test object.',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorTest')
        ),
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 201, description: 'Returns when POST is successful.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorTest')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Server error.', content: new OA\JsonContent(ref: '#/components/schemas/ServerError')),
        ]
    )]
    public function store()
    {
    }
}
