<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

class LogController extends Controller
{
    #[OA\Get(
        path: '/api/v1/logs/laravel',
        operationId: 'getLaravelLog',
        description: 'Retrieve the laravel log if it exists.',
        summary: 'Retrieves laravel log',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns when user is authenticated.',
                content: [new OA\MediaType(mediaType: 'application/gzip', schema: new OA\Schema(type: 'string', format: 'binary'))]
            ),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no laravel log file exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function laravel()
    {
    }
}
