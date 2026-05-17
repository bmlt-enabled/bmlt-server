<?php

namespace App\Http\Controllers\Query\Swagger;

use OpenApi\Attributes as OA;

#[OA\Parameter(parameter: 'SemMeetingIds', name: 'meeting_ids', in: 'query', description: 'Comma-separated meeting IDs. Positive values include, negative values exclude (e.g. `123,456,-789`).', schema: new OA\Schema(type: 'string'))]
#[OA\Parameter(parameter: 'SemMeetingIdsArray', name: 'meeting_ids[]', in: 'query', description: 'Repeatable form of `meeting_ids`. Positive includes, negative excludes.', style: 'form', explode: true, schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')))]
#[OA\Parameter(parameter: 'SemWeekdays', name: 'weekdays', in: 'query', description: 'Days of week as comma-separated values. 1=Sunday … 7=Saturday. Negative values exclude.', schema: new OA\Schema(type: 'string', example: '2,3,4'))]
#[OA\Parameter(parameter: 'SemWeekdaysArray', name: 'weekdays[]', in: 'query', description: 'Repeatable form of `weekdays`. 1=Sunday … 7=Saturday. Negative values exclude.', style: 'form', explode: true, schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer', minimum: -7, maximum: 7)))]
#[OA\Parameter(parameter: 'SemVenueTypes', name: 'venue_types', in: 'query', description: 'Venue type filter, comma-separated. 1=In-person, 2=Virtual, 3=Hybrid. Negative values exclude.', schema: new OA\Schema(type: 'string'))]
#[OA\Parameter(parameter: 'SemVenueTypesArray', name: 'venue_types[]', in: 'query', description: 'Repeatable form of `venue_types`. Negative values exclude.', style: 'form', explode: true, schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer', enum: [-3, -2, -1, 1, 2, 3])))]
#[OA\Parameter(parameter: 'SemFormats', name: 'formats', in: 'query', description: 'Format IDs as comma-separated values. Positive includes, negative excludes.', schema: new OA\Schema(type: 'string'))]
#[OA\Parameter(parameter: 'SemFormatsArray', name: 'formats[]', in: 'query', description: 'Repeatable form of `formats`. Positive includes, negative excludes.', style: 'form', explode: true, schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')))]
#[OA\Parameter(parameter: 'SemFormatsComparisonOperator', name: 'formats_comparison_operator', in: 'query', description: 'Logical operator for combining multiple `formats` values. Defaults to AND.', schema: new OA\Schema(type: 'string', enum: ['AND', 'OR'], default: 'AND'))]
#[OA\Parameter(parameter: 'SemServices', name: 'services', in: 'query', description: 'Service body IDs as comma-separated values. Positive includes, negative excludes.', schema: new OA\Schema(type: 'string'))]
#[OA\Parameter(parameter: 'SemServicesArray', name: 'services[]', in: 'query', description: 'Repeatable form of `services`. Positive includes, negative excludes.', style: 'form', explode: true, schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')))]
#[OA\Parameter(parameter: 'SemRecursive', name: 'recursive', in: 'query', description: 'Set to `1` to include child service bodies when filtering by `services`.', schema: new OA\Schema(type: 'string', enum: ['0', '1']))]
#[OA\Parameter(parameter: 'SemGetUsedFormats', name: 'get_used_formats', in: 'query', description: 'Set to `1` to also return the formats used by the matched meetings (alters response shape to `{meetings, formats}`).', schema: new OA\Schema(type: 'string', enum: ['0', '1']))]
#[OA\Parameter(parameter: 'SemGetFormatsOnly', name: 'get_formats_only', in: 'query', description: 'Set to `1` to return only the formats and omit the meetings (requires `get_used_formats=1`).', schema: new OA\Schema(type: 'string', enum: ['0', '1']))]
#[OA\Parameter(parameter: 'SemSearchString', name: 'SearchString', in: 'query', description: 'Free-text search string. URL-encode the value.', schema: new OA\Schema(type: 'string'))]
#[OA\Parameter(parameter: 'SemStringSearchIsAnAddress', name: 'StringSearchIsAnAddress', in: 'query', description: 'Set to `1` to interpret `SearchString` as an address to geocode. Requires a Google API key on the server.', schema: new OA\Schema(type: 'string', enum: ['0', '1']))]
#[OA\Parameter(parameter: 'SemSearchStringRadius', name: 'SearchStringRadius', in: 'query', description: 'Radius for address searches. Positive = distance (miles or km depending on server). Negative integer = auto-radius.', schema: new OA\Schema(type: 'number'))]
#[OA\Parameter(parameter: 'SemStartsAfterH', name: 'StartsAfterH', in: 'query', description: 'Earliest start hour (0–23).', schema: new OA\Schema(type: 'integer', minimum: 0, maximum: 23))]
#[OA\Parameter(parameter: 'SemStartsAfterM', name: 'StartsAfterM', in: 'query', description: 'Earliest start minute (0–59).', schema: new OA\Schema(type: 'integer', minimum: 0, maximum: 59))]
#[OA\Parameter(parameter: 'SemStartsBeforeH', name: 'StartsBeforeH', in: 'query', description: 'Latest start hour (0–23).', schema: new OA\Schema(type: 'integer', minimum: 0, maximum: 23))]
#[OA\Parameter(parameter: 'SemStartsBeforeM', name: 'StartsBeforeM', in: 'query', description: 'Latest start minute (0–59).', schema: new OA\Schema(type: 'integer', minimum: 0, maximum: 59))]
#[OA\Parameter(parameter: 'SemEndsBeforeH', name: 'EndsBeforeH', in: 'query', description: 'Latest end hour (0–23).', schema: new OA\Schema(type: 'integer', minimum: 0, maximum: 23))]
#[OA\Parameter(parameter: 'SemEndsBeforeM', name: 'EndsBeforeM', in: 'query', description: 'Latest end minute (0–59).', schema: new OA\Schema(type: 'integer', minimum: 0, maximum: 59))]
#[OA\Parameter(parameter: 'SemMinDurationH', name: 'MinDurationH', in: 'query', description: 'Minimum duration, hours portion.', schema: new OA\Schema(type: 'integer', minimum: 0))]
#[OA\Parameter(parameter: 'SemMinDurationM', name: 'MinDurationM', in: 'query', description: 'Minimum duration, minutes portion (0–59).', schema: new OA\Schema(type: 'integer', minimum: 0, maximum: 59))]
#[OA\Parameter(parameter: 'SemMaxDurationH', name: 'MaxDurationH', in: 'query', description: 'Maximum duration, hours portion.', schema: new OA\Schema(type: 'integer', minimum: 0))]
#[OA\Parameter(parameter: 'SemMaxDurationM', name: 'MaxDurationM', in: 'query', description: 'Maximum duration, minutes portion (0–59).', schema: new OA\Schema(type: 'integer', minimum: 0, maximum: 59))]
#[OA\Parameter(parameter: 'SemLatVal', name: 'lat_val', in: 'query', description: 'Latitude for geographic search.', schema: new OA\Schema(type: 'number', format: 'float', minimum: -90, maximum: 90))]
#[OA\Parameter(parameter: 'SemLongVal', name: 'long_val', in: 'query', description: 'Longitude for geographic search.', schema: new OA\Schema(type: 'number', format: 'float', minimum: -180, maximum: 180))]
#[OA\Parameter(parameter: 'SemGeoWidth', name: 'geo_width', in: 'query', description: 'Search radius in miles. Negative integer = auto-radius.', schema: new OA\Schema(type: 'number'))]
#[OA\Parameter(parameter: 'SemGeoWidthKm', name: 'geo_width_km', in: 'query', description: 'Search radius in kilometers. Negative integer = auto-radius.', schema: new OA\Schema(type: 'number'))]
#[OA\Parameter(parameter: 'SemSortResultsByDistance', name: 'sort_results_by_distance', in: 'query', description: 'Set to `1` to sort results by distance from `lat_val`/`long_val`.', schema: new OA\Schema(type: 'string', enum: ['0', '1']))]
#[OA\Parameter(parameter: 'SemMeetingKeyFilter', name: 'meeting_key', in: 'query', description: 'Field key to filter on (used with `meeting_key_value`).', schema: new OA\Schema(type: 'string'))]
#[OA\Parameter(parameter: 'SemMeetingKeyValue', name: 'meeting_key_value', in: 'query', description: 'Value to match against the field named by `meeting_key`.', schema: new OA\Schema(type: 'string'))]
#[OA\Parameter(parameter: 'SemDataFieldKey', name: 'data_field_key', in: 'query', description: 'Comma-separated list of fields to include in each returned meeting (whitelist).', schema: new OA\Schema(type: 'string', example: 'id_bigint,meeting_name,weekday_tinyint,start_time'))]
#[OA\Parameter(parameter: 'SemSortKeys', name: 'sort_keys', in: 'query', description: 'Comma-separated list of fields to sort by.', schema: new OA\Schema(type: 'string'))]
#[OA\Parameter(parameter: 'SemSortKey', name: 'sort_key', in: 'query', description: 'Predefined sort alias.', schema: new OA\Schema(type: 'string', enum: ['weekday', 'time', 'town', 'state', 'weekday_state']))]
#[OA\Parameter(parameter: 'SemPageSize', name: 'page_size', in: 'query', description: 'Number of results per page.', schema: new OA\Schema(type: 'integer', minimum: 1))]
#[OA\Parameter(parameter: 'SemPageNum', name: 'page_num', in: 'query', description: 'Page number, 1-based. Only meaningful when `page_size` is set.', schema: new OA\Schema(type: 'integer', minimum: 1, default: 1))]
#[OA\Parameter(parameter: 'SemAdvancedPublished', name: 'advanced_published', in: 'query', description: 'Published-status filter. Omit to return only published meetings. `0` returns both. `-1` returns only unpublished (requires authentication).', schema: new OA\Schema(type: 'integer', enum: [-1, 0]))]
#[OA\Parameter(parameter: 'SemLangEnum', name: 'lang_enum', in: 'query', description: 'Language code for translated format names (en, de, fr, es, it, pl, pt, sv, dk, fa).', schema: new OA\Schema(type: 'string'))]
#[OA\Parameter(parameter: 'SemShowAll', name: 'show_all', in: 'query', description: 'Set to `1` to include all formats (including ones not currently used by any meeting).', schema: new OA\Schema(type: 'string', enum: ['0', '1']))]
#[OA\Parameter(parameter: 'SemRootServerIds', name: 'root_server_ids', in: 'query', description: 'Aggregator mode only. Comma-separated root server IDs. Positive includes, negative excludes.', schema: new OA\Schema(type: 'string'))]
#[OA\Parameter(parameter: 'SemRootServerIdsArray', name: 'root_server_ids[]', in: 'query', description: 'Aggregator mode only. Repeatable form of `root_server_ids`. Positive includes, negative excludes.', style: 'form', explode: true, schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')))]
#[OA\Response(
    response: 'SemBadRequest',
    description: 'Bad request — usually a missing required parameter.',
    content: new OA\JsonContent(ref: '#/components/schemas/SemanticError')
)]
#[OA\Response(
    response: 'SemServerError',
    description: 'Internal server error — for example, a geocoding failure when `StringSearchIsAnAddress=1`.',
    content: new OA\JsonContent(ref: '#/components/schemas/SemanticError')
)]
class Parameters extends Controller
{
}
