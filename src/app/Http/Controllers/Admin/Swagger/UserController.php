<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserBase',
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'string'),
        new OA\Property(property: 'type', type: 'string', example: 'string'),
        new OA\Property(property: 'displayName', type: 'string', example: 'string'),
        new OA\Property(property: 'description', type: 'string', example: 'string'),
        new OA\Property(property: 'email', type: 'string', example: 'string'),
        new OA\Property(property: 'ownerId', type: 'integer', example: 0),
    ]
)]
#[OA\Schema(
    schema: 'User',
    required: ['id', 'username', 'type', 'displayName', 'description', 'email', 'ownerId'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 0),
        new OA\Property(property: 'lastLoginAt', type: 'string', format: 'date-time', example: '2019-05-02T05:05:00.000000Z', nullable: true),
    ],
    allOf: [new OA\Schema(ref: '#/components/schemas/UserBase')]
)]
#[OA\Schema(
    schema: 'UserCreate',
    required: ['username', 'password', 'type', 'displayName'],
    properties: [
        new OA\Property(property: 'password', type: 'string', example: 'string'),
    ],
    allOf: [new OA\Schema(ref: '#/components/schemas/UserBase')]
)]
#[OA\Schema(
    schema: 'UserUpdate',
    required: ['username', 'type', 'displayName'],
    properties: [
        new OA\Property(property: 'password', type: 'string', example: 'string'),
    ],
    allOf: [new OA\Schema(ref: '#/components/schemas/UserBase')]
)]
#[OA\Schema(
    schema: 'UserPartialUpdate',
    properties: [
        new OA\Property(property: 'password', type: 'string', example: 'string'),
    ],
    allOf: [new OA\Schema(ref: '#/components/schemas/UserBase')]
)]
#[OA\Schema(
    schema: 'UserCollection',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/User')
)]
class UserController extends Controller
{
    #[OA\Get(
        path: '/api/v1/users',
        operationId: 'getUsers',
        description: 'Retrieve users for authenticated user.',
        summary: 'Retrieves users',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 200, description: 'Returns when user is authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/UserCollection')),
            new OA\Response(response: 401, description: 'Returns when not authenticated', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
        ]
    )]
    public function index()
    {
    }

    #[OA\Get(
        path: '/api/v1/users/{userId}',
        operationId: 'getUser',
        description: 'Retrieve single user.',
        summary: 'Retrieves a single user',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'userId', description: 'ID of user', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'), example: '1'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returns when user is authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 401, description: 'Returns when not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 404, description: 'Returns when no user exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function show()
    {
    }

    #[OA\Post(
        path: '/api/v1/users',
        operationId: 'createUser',
        description: 'Creates a user.',
        summary: 'Creates a user',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in user object',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserCreate')
        ),
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 201, description: 'Returns when POST is successful.', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 404, description: 'Returns when no user exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store()
    {
    }

    #[OA\Put(
        path: '/api/v1/users/{userId}',
        operationId: 'updateUser',
        description: 'Updates a user.',
        summary: 'Update single user',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in user object',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserUpdate')
        ),
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'userId', description: 'ID of user', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'), example: '1'),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 404, description: 'Returns when no user exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update()
    {
    }

    #[OA\Patch(
        path: '/api/v1/users/{userId}',
        operationId: 'partialUpdateUser',
        description: 'Patches a user by id.',
        summary: 'Patches a user',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in fields you want to update.',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserPartialUpdate')
        ),
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'userId', description: 'ID of user', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'), example: '1'),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when not authenticated', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no user exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function partialUpdate()
    {
    }

    #[OA\Delete(
        path: '/api/v1/users/{userId}',
        operationId: 'deleteUser',
        description: 'Deletes a user by id',
        summary: 'Deletes a user',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'userId', description: 'ID of user', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'), example: '1'),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when not authenticated', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no user exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 409, description: 'Returns when user is still referenced by service bodies.', content: new OA\JsonContent(ref: '#/components/schemas/ConflictError')),
        ]
    )]
    public function destroy()
    {
    }
}
