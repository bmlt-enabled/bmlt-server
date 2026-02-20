<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MeetingBase',
    properties: [
        new OA\Property(property: 'serviceBodyId', type: 'integer', example: 0),
        new OA\Property(property: 'formatIds', type: 'array', items: new OA\Items(type: 'integer')),
        new OA\Property(property: 'venueType', type: 'integer', example: 1),
        new OA\Property(property: 'temporarilyVirtual', type: 'boolean', example: false),
        new OA\Property(property: 'day', type: 'integer', example: 0),
        new OA\Property(property: 'startTime', type: 'string', example: 'string'),
        new OA\Property(property: 'duration', type: 'string', example: '01:00'),
        new OA\Property(property: 'timeZone', type: 'string', example: 'America/New_York'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 35.698741),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', example: -81.26273),
        new OA\Property(property: 'published', type: 'boolean', example: true),
        new OA\Property(property: 'email', type: 'string', example: 'string'),
        new OA\Property(property: 'worldId', type: 'string', example: 'string'),
        new OA\Property(property: 'name', type: 'string', example: 'string'),
        new OA\Property(property: 'location_text', type: 'string', example: 'string'),
        new OA\Property(property: 'location_info', type: 'string', example: 'string'),
        new OA\Property(property: 'location_street', type: 'string', example: 'string'),
        new OA\Property(property: 'location_neighborhood', type: 'string', example: 'string'),
        new OA\Property(property: 'location_city_subsection', type: 'string', example: 'string'),
        new OA\Property(property: 'location_municipality', type: 'string', example: 'string'),
        new OA\Property(property: 'location_sub_province', type: 'string', example: 'string'),
        new OA\Property(property: 'location_province', type: 'string', example: 'string'),
        new OA\Property(property: 'location_postal_code_1', type: 'string', example: 'string'),
        new OA\Property(property: 'location_nation', type: 'string', example: 'string'),
        new OA\Property(property: 'phone_meeting_number', type: 'string', example: 'string'),
        new OA\Property(property: 'virtual_meeting_link', type: 'string', example: 'string'),
        new OA\Property(property: 'virtual_meeting_additional_info', type: 'string', example: 'string'),
        new OA\Property(property: 'contact_name_1', type: 'string', example: 'string'),
        new OA\Property(property: 'contact_name_2', type: 'string', example: 'string'),
        new OA\Property(property: 'contact_phone_1', type: 'string', example: 'string'),
        new OA\Property(property: 'contact_phone_2', type: 'string', example: 'string'),
        new OA\Property(property: 'contact_email_1', type: 'string', example: 'string'),
        new OA\Property(property: 'contact_email_2', type: 'string', example: 'string'),
        new OA\Property(property: 'bus_lines', type: 'string', example: 'string'),
        new OA\Property(property: 'train_lines', type: 'string', example: 'string'),
        new OA\Property(property: 'comments', type: 'string', example: 'string'),
        new OA\Property(property: 'admin_notes', type: 'string', example: 'string'),
        new OA\Property(
            property: 'customFields',
            type: 'object',
            example: ['key1' => 'value1', 'key2' => 'value2'],
            additionalProperties: new OA\AdditionalProperties(type: 'string')
        ),
    ]
)]
#[OA\Schema(
    schema: 'Meeting',
    required: ['id', 'serviceBodyId', 'formatIds', 'venueType', 'temporarilyVirtual', 'day', 'startTime', 'duration', 'timeZone', 'latitude', 'longitude', 'published', 'email', 'worldId', 'name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 0),
    ],
    allOf: [new OA\Schema(ref: '#/components/schemas/MeetingBase')]
)]
#[OA\Schema(
    schema: 'MeetingCreate',
    required: ['serviceBodyId', 'formatIds', 'venueType', 'day', 'startTime', 'duration', 'latitude', 'longitude', 'published', 'name'],
    allOf: [new OA\Schema(ref: '#/components/schemas/MeetingBase')]
)]
#[OA\Schema(
    schema: 'MeetingUpdate',
    required: ['serviceBodyId', 'formatIds', 'venueType', 'day', 'startTime', 'duration', 'latitude', 'longitude', 'published', 'name'],
    allOf: [new OA\Schema(ref: '#/components/schemas/MeetingBase')]
)]
#[OA\Schema(
    schema: 'MeetingPartialUpdate',
    allOf: [new OA\Schema(ref: '#/components/schemas/MeetingBase')]
)]
#[OA\Schema(
    schema: 'MeetingCollection',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/Meeting')
)]
class MeetingController extends Controller
{
    #[OA\Get(
        path: '/api/v1/meetings',
        operationId: 'getMeetings',
        description: 'Retrieve meetings for authenticated user.',
        summary: 'Retrieves meetings',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'meetingIds', description: 'comma delimited meeting ids', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: '1,2'),
            new OA\Parameter(name: 'days', description: 'comma delimited day ids between 0-6', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: '0,1'),
            new OA\Parameter(name: 'serviceBodyIds', description: 'comma delimited service body ids', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: '3,4'),
            new OA\Parameter(name: 'searchString', description: 'string', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: 'Just for Today'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of meetings.', content: new OA\JsonContent(ref: '#/components/schemas/MeetingCollection')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function index()
    {
    }

    #[OA\Get(
        path: '/api/v1/meetings/{meetingId}',
        operationId: 'getMeeting',
        description: 'Retrieve a meeting.',
        summary: 'Retrieves a meeting',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'meetingId', description: 'ID of meeting', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'), example: '1'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returns when user is authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/Meeting')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 404, description: 'Returns when no meeting exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function show()
    {
    }

    #[OA\Post(
        path: '/api/v1/meetings',
        operationId: 'createMeeting',
        description: 'Creates a meeting.',
        summary: 'Creates a meeting',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in meeting object',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/MeetingCreate')
        ),
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 201, description: 'Returns when POST is successful.', content: new OA\JsonContent(ref: '#/components/schemas/Meeting')),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no meeting body exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store()
    {
    }

    #[OA\Put(
        path: '/api/v1/meetings/{meetingId}',
        operationId: 'updateMeeting',
        description: 'Updates a meeting.',
        summary: 'Updates a meeting',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in meeting object',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/MeetingUpdate')
        ),
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'meetingId', description: 'ID of meeting', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'), example: '1'),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no meeting exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update()
    {
    }

    #[OA\Patch(
        path: '/api/v1/meetings/{meetingId}',
        operationId: 'patchMeeting',
        description: 'Patches a meeting by id',
        summary: 'Patches a meeting',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in fields you want to update.',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/MeetingPartialUpdate')
        ),
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'meetingId', description: 'ID of meeting', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'), example: '1'),
            new OA\Parameter(name: 'skipVenueTypeLocationValidation', description: 'specify true to skip venue type location validation', in: 'query', required: false, schema: new OA\Schema(type: 'boolean'), example: 'true'),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no meeting exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function partialUpdate()
    {
    }

    #[OA\Delete(
        path: '/api/v1/meetings/{meetingId}',
        operationId: 'deleteMeeting',
        description: 'Deletes a meeting by id.',
        summary: 'Deletes a meeting',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        parameters: [
            new OA\Parameter(name: 'meetingId', description: 'ID of meeting', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'), example: '1'),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is unauthorized to perform action.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 404, description: 'Returns when no meeting exists.', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function destroy()
    {
    }
}
