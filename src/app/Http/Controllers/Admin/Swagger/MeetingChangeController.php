<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MeetingChangeResource',
    properties: [
        new OA\Property(
            property: 'dateString',
            description: 'Human-readable date and time.',
            type: 'string',
            example: '3:35 PM, 10/14/2024'
        ),
        new OA\Property(
            property: 'userName',
            description: 'Name of the user who made the change.',
            type: 'string',
            example: 'Greater New York Regional Administrator'
        ),
        new OA\Property(
            property: 'serviceBodyName',
            description: 'Name of the service body related to the meeting.',
            type: 'string',
            example: 'Bronx Area Service'
        ),
        new OA\Property(
            property: 'details',
            description: 'List of details about the changes.',
            type: 'array',
            items: new OA\Items(type: 'string', example: 'email_contact was deleted.')
        ),
    ],
    type: 'object'
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
                description: 'ID of the meeting',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64'),
                example: '1'
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
