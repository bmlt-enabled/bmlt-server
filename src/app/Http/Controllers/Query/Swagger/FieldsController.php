<?php

namespace App\Http\Controllers\Query\Swagger;

use OpenApi\Attributes as OA;

class FieldsController extends Controller
{
    #[OA\Get(
        path: '/client_interface/json/?switcher=GetFieldKeys',
        operationId: 'getFieldKeys',
        summary: 'Get all available field keys',
        tags: ['Fields'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of field key descriptors',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'key', type: 'string'),
                            new OA\Property(property: 'description', type: 'string'),
                        ]
                    )
                )
            ),
        ]
    )]
    public function getFieldKeys()
    {
    }

    #[OA\Get(
        path: '/client_interface/json/?switcher=GetFieldValues',
        operationId: 'getFieldValues',
        summary: 'Get distinct values for a field',
        tags: ['Fields'],
        parameters: [
            new OA\Parameter(name: 'meeting_key', in: 'query', required: true, description: 'Field key whose distinct values should be returned.', schema: new OA\Schema(type: 'string', example: 'location_municipality')),
            new OA\Parameter(name: 'specific_formats', in: 'query', description: 'Comma-separated list of format IDs to limit the field values to.', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'all_formats', in: 'query', description: 'Set to `1` to include all formats (not just `specific_formats`).', schema: new OA\Schema(type: 'string', enum: ['0', '1'])),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of distinct values with usage counts',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'ids', type: 'string'),
                            new OA\Property(property: 'meeting_key_value', type: 'string'),
                        ]
                    )
                )
            ),
            new OA\Response(response: 400, ref: '#/components/responses/SemBadRequest'),
        ]
    )]
    public function getFieldValues()
    {
    }
}
