<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SettingsBase',
    properties: [
        new OA\Property(property: 'googleApiKey', type: 'string'),
        new OA\Property(property: 'changeDepthForMeetings', type: 'integer'),
        new OA\Property(property: 'defaultSortKey', type: 'string', nullable: true),
        new OA\Property(property: 'language', type: 'string'),
        new OA\Property(property: 'defaultDurationTime', type: 'string'),
        new OA\Property(property: 'regionBias', type: 'string'),
        new OA\Property(property: 'distanceUnits', type: 'string'),
        new OA\Property(property: 'meetingStatesAndProvinces', type: 'array', items: new OA\Items(type: 'string')),
        new OA\Property(property: 'meetingCountiesAndSubProvinces', type: 'array', items: new OA\Items(type: 'string')),
        new OA\Property(property: 'searchSpecMapCenterLongitude', type: 'number', format: 'float'),
        new OA\Property(property: 'searchSpecMapCenterLatitude', type: 'number', format: 'float'),
        new OA\Property(property: 'searchSpecMapCenterZoom', type: 'integer'),
        new OA\Property(property: 'numberOfMeetingsForAuto', type: 'integer'),
        new OA\Property(property: 'autoGeocodingEnabled', type: 'boolean'),
        new OA\Property(property: 'countyAutoGeocodingEnabled', type: 'boolean'),
        new OA\Property(property: 'zipAutoGeocodingEnabled', type: 'boolean'),
        new OA\Property(property: 'defaultClosedStatus', type: 'boolean'),
        new OA\Property(property: 'enableLanguageSelector', type: 'boolean'),
        new OA\Property(property: 'includeServiceBodyEmailInSemantic', type: 'boolean'),
        new OA\Property(property: 'bmltTitle', type: 'string'),
        new OA\Property(property: 'bmltNotice', type: 'string'),
        new OA\Property(property: 'formatLangNames', type: 'object'),
    ]
)]
#[OA\Schema(
    schema: 'SettingsObject',
    properties: [
        new OA\Property(property: 'googleApiKey', type: 'string', example: ''),
        new OA\Property(property: 'changeDepthForMeetings', type: 'integer', example: 0),
        new OA\Property(property: 'defaultSortKey', type: 'string', example: null, nullable: true),
        new OA\Property(property: 'language', type: 'string', example: 'en'),
        new OA\Property(property: 'defaultDurationTime', type: 'string', example: '01:00'),
        new OA\Property(property: 'regionBias', type: 'string', example: 'us'),
        new OA\Property(property: 'distanceUnits', type: 'string', example: 'mi'),
        new OA\Property(property: 'meetingStatesAndProvinces', type: 'array', items: new OA\Items(type: 'string'), example: []),
        new OA\Property(property: 'meetingCountiesAndSubProvinces', type: 'array', items: new OA\Items(type: 'string'), example: []),
        new OA\Property(property: 'searchSpecMapCenterLongitude', type: 'number', format: 'float', example: -118.563659),
        new OA\Property(property: 'searchSpecMapCenterLatitude', type: 'number', format: 'float', example: 34.235918),
        new OA\Property(property: 'searchSpecMapCenterZoom', type: 'integer', example: 6),
        new OA\Property(property: 'numberOfMeetingsForAuto', type: 'integer', example: 10),
        new OA\Property(property: 'autoGeocodingEnabled', type: 'boolean', example: true),
        new OA\Property(property: 'countyAutoGeocodingEnabled', type: 'boolean', example: false),
        new OA\Property(property: 'zipAutoGeocodingEnabled', type: 'boolean', example: false),
        new OA\Property(property: 'defaultClosedStatus', type: 'boolean', example: true),
        new OA\Property(property: 'enableLanguageSelector', type: 'boolean', example: false),
        new OA\Property(property: 'includeServiceBodyEmailInSemantic', type: 'boolean', example: false),
        new OA\Property(property: 'bmltTitle', type: 'string', example: ''),
        new OA\Property(property: 'bmltNotice', type: 'string', example: ''),
        new OA\Property(property: 'formatLangNames', type: 'object', example: []),
    ]
)]
#[OA\Schema(
    schema: 'SettingsUpdate',
    description: 'Partial update object - include only the settings you want to update',
    allOf: [new OA\Schema(ref: '#/components/schemas/SettingsBase')]
)]
class SettingController extends Controller
{
    #[OA\Get(
        path: '/api/v1/settings',
        operationId: 'getSettings',
        description: 'Retrieve all server settings. Only accessible to server administrators.',
        summary: 'Retrieves all settings',
        security: [['bmltToken' => []]],
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 200, description: 'Returns when user is authenticated as admin.', content: new OA\JsonContent(ref: '#/components/schemas/SettingsObject')),
            new OA\Response(response: 401, description: 'Returns when not authenticated', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is not an admin', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
        ]
    )]
    public function index()
    {
    }

    #[OA\Patch(
        path: '/api/v1/settings',
        operationId: 'updateSettings',
        description: 'Updates one or more server settings. Only accessible to server administrators.',
        summary: 'Update settings',
        security: [['bmltToken' => []]],
        requestBody: new OA\RequestBody(
            description: 'Pass in settings object with values to update',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/SettingsUpdate')
        ),
        tags: ['rootServer'],
        responses: [
            new OA\Response(response: 204, description: 'Success.'),
            new OA\Response(response: 401, description: 'Returns when user is not authenticated.', content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationError')),
            new OA\Response(response: 403, description: 'Returns when user is not an admin.', content: new OA\JsonContent(ref: '#/components/schemas/AuthorizationError')),
            new OA\Response(response: 422, description: 'Validation error.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update()
    {
    }
}
