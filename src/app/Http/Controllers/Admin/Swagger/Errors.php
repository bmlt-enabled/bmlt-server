<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AuthenticationError',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
    ]
)]
#[OA\Schema(
    schema: 'AuthorizationError',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'This action is unauthorized.'),
    ]
)]
#[OA\Schema(
    schema: 'NotFoundError',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The requested resource was not found.'),
    ]
)]
#[OA\Schema(
    schema: 'ConflictError',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Conflict Error'),
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    required: ['message', 'errors'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The field is required. (and 1 more error)'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string', example: 'error details')
            )
        ),
    ]
)]
#[OA\Schema(
    schema: 'ServerError',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Server Error'),
    ]
)]
class Errors extends Controller
{
}
