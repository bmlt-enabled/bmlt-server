<?php

namespace App\Http\Controllers\Query\Swagger;

use OpenApi\Attributes as OA;

class MeetingsController extends Controller
{
    #[OA\Get(
        path: '/client_interface/json/?switcher=GetSearchResults',
        operationId: 'getSearchResults',
        summary: 'Search for meetings',
        description: 'Search meetings with extensive filtering (location, day, time, format, service body, text). In aggregator mode at least one filter parameter is required, otherwise the response will be an empty array.',
        tags: ['Meetings'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/SemMeetingIds'),
            new OA\Parameter(ref: '#/components/parameters/SemWeekdays'),
            new OA\Parameter(ref: '#/components/parameters/SemVenueTypes'),
            new OA\Parameter(ref: '#/components/parameters/SemFormats'),
            new OA\Parameter(ref: '#/components/parameters/SemFormatsComparisonOperator'),
            new OA\Parameter(ref: '#/components/parameters/SemServices'),
            new OA\Parameter(ref: '#/components/parameters/SemRecursive'),
            new OA\Parameter(ref: '#/components/parameters/SemGetUsedFormats'),
            new OA\Parameter(ref: '#/components/parameters/SemGetFormatsOnly'),
            new OA\Parameter(ref: '#/components/parameters/SemSearchString'),
            new OA\Parameter(ref: '#/components/parameters/SemStringSearchIsAnAddress'),
            new OA\Parameter(ref: '#/components/parameters/SemSearchStringRadius'),
            new OA\Parameter(ref: '#/components/parameters/SemStartsAfterH'),
            new OA\Parameter(ref: '#/components/parameters/SemStartsAfterM'),
            new OA\Parameter(ref: '#/components/parameters/SemStartsBeforeH'),
            new OA\Parameter(ref: '#/components/parameters/SemStartsBeforeM'),
            new OA\Parameter(ref: '#/components/parameters/SemEndsBeforeH'),
            new OA\Parameter(ref: '#/components/parameters/SemEndsBeforeM'),
            new OA\Parameter(ref: '#/components/parameters/SemMinDurationH'),
            new OA\Parameter(ref: '#/components/parameters/SemMinDurationM'),
            new OA\Parameter(ref: '#/components/parameters/SemMaxDurationH'),
            new OA\Parameter(ref: '#/components/parameters/SemMaxDurationM'),
            new OA\Parameter(ref: '#/components/parameters/SemLatVal'),
            new OA\Parameter(ref: '#/components/parameters/SemLongVal'),
            new OA\Parameter(ref: '#/components/parameters/SemGeoWidth'),
            new OA\Parameter(ref: '#/components/parameters/SemGeoWidthKm'),
            new OA\Parameter(ref: '#/components/parameters/SemSortResultsByDistance'),
            new OA\Parameter(ref: '#/components/parameters/SemMeetingKeyFilter'),
            new OA\Parameter(ref: '#/components/parameters/SemMeetingKeyValue'),
            new OA\Parameter(ref: '#/components/parameters/SemDataFieldKey'),
            new OA\Parameter(ref: '#/components/parameters/SemSortKeys'),
            new OA\Parameter(ref: '#/components/parameters/SemSortKey'),
            new OA\Parameter(ref: '#/components/parameters/SemPageSize'),
            new OA\Parameter(ref: '#/components/parameters/SemPageNum'),
            new OA\Parameter(ref: '#/components/parameters/SemAdvancedPublished'),
            new OA\Parameter(ref: '#/components/parameters/SemLangEnum'),
            new OA\Parameter(ref: '#/components/parameters/SemRootServerIds'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Meeting search results. May be a bare array of meetings, an array of formats when `get_formats_only=1`, or a `{meetings, formats}` envelope when `get_used_formats=1`.',
                content: new OA\JsonContent(
                    oneOf: [
                        new OA\Schema(type: 'array', items: new OA\Items(ref: '#/components/schemas/SemanticMeeting')),
                        new OA\Schema(type: 'array', items: new OA\Items(ref: '#/components/schemas/SemanticFormat')),
                        new OA\Schema(
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'meetings', type: 'array', items: new OA\Items(ref: '#/components/schemas/SemanticMeeting')),
                                new OA\Property(property: 'formats', type: 'array', items: new OA\Items(ref: '#/components/schemas/SemanticFormat')),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 400, ref: '#/components/responses/SemBadRequest'),
            new OA\Response(response: 500, ref: '#/components/responses/SemServerError'),
        ]
    )]
    public function getSearchResults()
    {
    }

    #[OA\Get(
        path: '/client_interface/json/?switcher=GetChanges',
        operationId: 'getChanges',
        summary: 'Get meeting changes within a date range',
        tags: ['Meetings'],
        parameters: [
            new OA\Parameter(name: 'start_date', in: 'query', description: 'Start date (inclusive) in YYYY-MM-DD format.', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'end_date', in: 'query', description: 'End date (inclusive) in YYYY-MM-DD format.', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'meeting_id', in: 'query', description: 'Restrict to changes for a single meeting.', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'service_body_id', in: 'query', description: 'Restrict to changes within a single service body.', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of meeting changes',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/SemanticMeetingChange'))
            ),
        ]
    )]
    public function getChanges()
    {
    }
}
