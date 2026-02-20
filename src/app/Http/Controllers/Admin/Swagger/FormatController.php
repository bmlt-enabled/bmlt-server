<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FormatBase',
    properties: [
        new OA\Property(property: 'worldId', type: 'string', example: 'string'),
        new OA\Property(property: 'type', type: 'string', example: 'string'),
        new OA\Property(property: 'translations', type: 'array', items: new OA\Items(ref: '#/components/schemas/FormatTranslation')),
    ]
)]
#[OA\Schema(
    schema: 'FormatTranslation',
    required: ['key', 'name', 'description', 'language'],
    properties: [
        new OA\Property(property: 'key', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string'),
        new OA\Property(property: 'language', type: 'string'),
    ]
)]
#[OA\Schema(
    schema: 'Format',
    required: ['id', 'worldId', 'type', 'translations'],
    allOf: [new OA\Schema(ref: '#/components/schemas/FormatBase')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 0),
    ]
)]
#[OA\Schema(
    schema: 'FormatCreate',
    required: ['translations'],
    allOf: [new OA\Schema(ref: '#/components/schemas/FormatBase')]
)]
#[OA\Schema(
    schema: 'FormatUpdate',
    required: ['translations'],
    allOf: [new OA\Schema(ref: '#/components/schemas/FormatBase')]
)]
#[OA\Schema(
    schema: 'FormatPartialUpdate',
    allOf: [new OA\Schema(ref: '#/components/schemas/FormatBase')]
)]
#[OA\Schema(
    schema: 'FormatCollection',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/Format')
)]
class FormatController extends Controller
{
    #[OA\Get(
        path: '/api/v1/formats',
        operationId: 'getFormats',
        description: 'Retrieve formats',
        summary: 'Retrieves formats',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 200, description: 'Returns when user is authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/FormatCollection')),
            new OA\Response(response: 401, description: 'Returns when not authenticated', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
        ]
    )]
    public function index()
    {
    }

    #[OA\Get(
        path: '/api/v1/formats/{formatId}',
        operationId: 'getFormat',
        description: 'Retrieve a format',
        summary: 'Retrieves a format',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'formatId', in: 'path', description: 'ID of format', required: true, example: '1', schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returns when user is authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/Format')),
            new OA\Response(response: 401, description: 'Returns when not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 404, description: 'Returns when no format exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function show()
    {
    }

    #[OA\Post(
        path: '/api/v1/formats',
        operationId: 'createFormat',
        description: 'Creates a format.',
        summary: 'Creates a format',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in format object',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/FormatCreate')
        ),
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 201, description: 'Returns when POST is successful.', content: new OA\JsonContent(ref: '#/components/schemas/Format')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 404, description: 'Returns when no format exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store()
    {
    }

    #[OA\Put(
        path: '/api/v1/formats/{formatId}',
        operationId: 'updateFormat',
        description: 'Updates a format.',
        summary: 'Updates a format',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in format object',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/FormatUpdate')
        ),
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'formatId', in: 'path', description: 'ID of format', required: true, example: '1', schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 404, description: 'Returns when no format exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update()
    {
    }

    #[OA\Patch(
        path: '/api/v1/formats/{formatId}',
        operationId: 'patchFormat',
        description: 'Patches a single format by id.',
        summary: 'Patches a format',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in fields you want to update.',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/FormatPartialUpdate')
        ),
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'formatId', in: 'path', description: 'ID of format', required: true, example: '1', schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when not authenticated', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no format exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function partialUpdate()
    {
    }

    #[OA\Delete(
        path: '/api/v1/formats/{formatId}',
        operationId: 'deleteFormat',
        description: 'Deletes a format by id.',
        summary: 'Deletes a format',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'formatId', in: 'path', description: 'ID of format', required: true, example: '1', schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when not authenticated', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no format exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 409, description: 'Returns when format has meetings assigned.', content: new OA\JsonContent(ref: '#/components/schemas/ConflictError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function destroy()
    {
    }
}
