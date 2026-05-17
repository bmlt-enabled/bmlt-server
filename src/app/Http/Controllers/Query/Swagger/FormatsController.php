<?php

namespace App\Http\Controllers\Query\Swagger;

use OpenApi\Attributes as OA;

class FormatsController extends Controller
{
    #[OA\Get(
        path: '/client_interface/json/?switcher=GetFormats',
        operationId: 'getSemanticFormats',
        summary: 'Get meeting formats',
        tags: ['Formats'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/SemLangEnum'),
            new OA\Parameter(ref: '#/components/parameters/SemShowAll'),
            new OA\Parameter(name: 'format_ids', in: 'query', description: 'Format IDs to include (positive) or exclude (negative). Comma-separated.', schema: new OA\Schema(type: 'string', example: '1,2,-3')),
            new OA\Parameter(name: 'key_strings', in: 'query', description: 'Format key strings to filter by. Comma-separated.', schema: new OA\Schema(type: 'string', example: 'O,C')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of formats',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/SemanticFormat'))
            ),
        ]
    )]
    public function getFormats()
    {
    }
}
