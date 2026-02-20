<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ServiceBodyBase',
    properties: [
        new OA\Property(property: 'parentId', type: 'integer', nullable: true, example: 0),
        new OA\Property(property: 'name', type: 'string', example: 'string'),
        new OA\Property(property: 'description', type: 'string', example: 'string'),
        new OA\Property(property: 'type', type: 'string', example: 'string'),
        new OA\Property(property: 'adminUserId', type: 'integer', example: 0),
        new OA\Property(property: 'assignedUserIds', type: 'array', items: new OA\Items(type: 'integer', example: 0)),
        new OA\Property(property: 'url', type: 'string', example: 'string'),
        new OA\Property(property: 'helpline', type: 'string', example: 'string'),
        new OA\Property(property: 'email', type: 'string', example: 'string'),
        new OA\Property(property: 'worldId', type: 'string', example: 'string'),
    ]
)]
#[OA\Schema(
    schema: 'ServiceBody',
    required: ['id', 'parentId', 'name', 'description', 'type', 'adminUserId', 'assignedUserIds', 'url', 'helpline', 'email', 'worldId'],
    allOf: [new OA\Schema(ref: '#/components/schemas/ServiceBodyBase')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 0),
    ]
)]
#[OA\Schema(
    schema: 'ServiceBodyCreate',
    required: ['parentId', 'name', 'description', 'type', 'adminUserId', 'assignedUserIds'],
    allOf: [new OA\Schema(ref: '#/components/schemas/ServiceBodyBase')]
)]
#[OA\Schema(
    schema: 'ServiceBodyUpdate',
    required: ['parentId', 'name', 'description', 'type', 'adminUserId', 'assignedUserIds'],
    allOf: [new OA\Schema(ref: '#/components/schemas/ServiceBodyBase')]
)]
#[OA\Schema(
    schema: 'ServiceBodyPartialUpdate',
    allOf: [new OA\Schema(ref: '#/components/schemas/ServiceBodyBase')]
)]
#[OA\Schema(
    schema: 'ServiceBodyCollection',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/ServiceBody')
)]
class ServiceBodyController extends Controller
{
    #[OA\Get(
        path: '/api/v1/servicebodies',
        operationId: 'getServiceBodies',
        description: 'Retrieve service bodies for authenticated user.',
        summary: 'Retrieves service bodies',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 200, description: 'Returns when user is authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ServiceBodyCollection')),
            new OA\Response(response: 401, description: 'Returns when not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
        ]
    )]
    public function index()
    {
    }

    #[OA\Get(
        path: '/api/v1/servicebodies/{serviceBodyId}',
        operationId: 'getServiceBody',
        description: 'Retrieve a single service body by id.',
        summary: 'Retrieves a service body',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'serviceBodyId', in: 'path', description: 'ID of service body', required: true, example: '1', schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returns when user is authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ServiceBody')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 404, description: 'Returns when no service body exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function show()
    {
    }

    #[OA\Post(
        path: '/api/v1/servicebodies',
        operationId: 'createServiceBody',
        description: 'Creates a service body.',
        summary: 'Creates a service body',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in service body object',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ServiceBodyCreate')
        ),
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 201, description: 'Returns when POST is successful.', content: new OA\JsonContent(ref: '#/components/schemas/ServiceBody')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no service body exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store()
    {
    }

    #[OA\Put(
        path: '/api/v1/servicebodies/{serviceBodyId}',
        operationId: 'updateServiceBody',
        description: 'Updates a single service body.',
        summary: 'Updates a Service Body',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in service body object',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ServiceBodyUpdate')
        ),
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'serviceBodyId', in: 'path', description: 'ID of service body', required: true, example: '1', schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no service body exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update()
    {
    }

    #[OA\Patch(
        path: '/api/v1/servicebodies/{serviceBodyId}',
        operationId: 'patchServiceBody',
        description: 'Patches a single service body by id.',
        summary: 'Patches a service body',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in fields you want to update.',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ServiceBodyPartialUpdate')
        ),
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'serviceBodyId', in: 'path', description: 'ID of service body', required: true, example: '1', schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no service body exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function partialUpdate()
    {
    }

    #[OA\Delete(
        path: '/api/v1/servicebodies/{serviceBodyId}',
        operationId: 'deleteServiceBody',
        description: 'Deletes a service body by id. If the service body has meetings, use force=true to delete them as well.',
        summary: 'Deletes a service body',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'serviceBodyId', in: 'path', description: 'ID of service body', required: true, example: '1', schema: new OA\Schema(type: 'integer', format: 'int64')),
            new OA\Parameter(name: 'force', in: 'query', description: 'Force deletion of service body and all associated meetings', required: false, example: 'false', schema: new OA\Schema(type: 'string', enum: ['true', 'false'], default: 'false')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no service body exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 409, description: 'Returns when service body has children or meetings (when force=false).', content: new OA\JsonContent(ref: '#/components/schemas/ConflictError')),
        ]
    )]
    public function destroy()
    {
    }
}
