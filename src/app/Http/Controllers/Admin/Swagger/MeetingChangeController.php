<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MeetingChangeResource',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'dateString',
            type: 'string',
            description: 'Human-readable date and time.',
            example: '3:35 PM, 10/14/2024'
        ),
        new OA\Property(
            property: 'userName',
            type: 'string',
            description: 'Name of the user who made the change.',
            example: 'Greater New York Regional Administrator'
        ),
        new OA\Property(
            property: 'serviceBodyName',
            type: 'string',
            description: 'Name of the service body related to the meeting.',
            example: 'Bronx Area Service'
        ),
        new OA\Property(
            property: 'details',
            type: 'array',
            description: 'List of details about the changes.',
            items: new OA\Items(type: 'string', example: 'email_contact was deleted.')
        ),
    ]
)]
class MeetingChangeController extends Controller
{
    #[OA\Get(
        path: '/api/v1/meetings/{meetingId}/changes',
        operationId: 'getMeetingChanges',
        description: 'Retrieve all changes made to a specific meeting.',
        summary: 'Retrieve changes for a meeting',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(
                name: 'meetingId',
                in: 'path',
                description: 'ID of the meeting',
                required: true,
                example: '1',
                schema: new OA\Schema(type: 'integer', format: 'int64')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of changes for the meeting.',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/MeetingChangeResource'))
            ),
            new OA\Response(response: 401, description: 'Unauthorized.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Meeting not found.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function index()
    {
    }
}
