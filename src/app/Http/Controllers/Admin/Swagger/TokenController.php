<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Token',
    required: ['access_token', 'expires_at', 'token_type', 'user_id'],
    properties: [
        new OA\Property(property: 'access_token', type: 'string', example: '2|tR6PIqa8tiBJWMu4zyb3qw4eECuERjLd7xeLKgBu'),
        new OA\Property(property: 'expires_at', type: 'integer', example: 1667342171),
        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
    ]
)]
#[OA\Schema(
    schema: 'TokenCredentials',
    required: ['username', 'password'],
    properties: [
        new OA\Property(property: 'username', type: 'string', format: 'username', example: 'MyUsername'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'PassWord12345'),
    ]
)]
class TokenController extends Controller
{
    #[OA\Post(
        path: '/api/v1/auth/token',
        operationId: 'authToken',
        description: 'Exchange credentials for a new token',
        summary: 'Creates a token',
        requestBody: new OA\RequestBody(
            description: 'User credentials',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TokenCredentials')
        ),
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 200, description: 'Returns when POST is successful.', content: new OA\JsonContent(ref: '#/components/schemas/Token')),
            new OA\Response(response: 401, description: 'Returns when credentials are incorrect.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when unauthorized.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    private function token()
    {
    }

    #[OA\Post(
        path: '/api/v1/auth/refresh',
        operationId: 'authRefresh',
        description: 'Refresh token.',
        summary: 'Revokes and issues a new token',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 200, description: 'Returns when refresh is successful.', content: new OA\JsonContent(ref: '#/components/schemas/Token')),
            new OA\Response(response: 401, description: 'Returns when request is unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
        ]
    )]
    public function refresh()
    {
    }

    #[OA\Post(
        path: '/api/v1/auth/logout',
        operationId: 'authLogout',
        description: 'Revoke token and logout.',
        summary: 'Revokes a token',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 200, description: 'Returns when token was logged out.'),
            new OA\Response(response: 401, description: 'Returns when request is unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
        ]
    )]
    public function logout()
    {
    }
}
