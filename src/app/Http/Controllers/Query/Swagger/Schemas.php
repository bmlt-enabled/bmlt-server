<?php

namespace App\Http\Controllers\Query\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SemanticMeeting',
    type: 'object',
    description: "A meeting record. The actual field set depends on the server's enabled fields and the `data_field_key` parameter.",
    additionalProperties: true,
    properties: [
        new OA\Property(property: 'id_bigint', type: 'string', description: 'Numeric meeting ID, returned as a string.'),
        new OA\Property(property: 'worldid_mixed', type: 'string', description: 'NAWS world committee code.'),
        new OA\Property(property: 'shared_group_id_bigint', type: 'string'),
        new OA\Property(property: 'service_body_bigint', type: 'string'),
        new OA\Property(property: 'weekday_tinyint', type: 'string', description: '1=Sunday … 7=Saturday.'),
        new OA\Property(property: 'venue_type', type: 'string', description: '1=In-person, 2=Virtual, 3=Hybrid.'),
        new OA\Property(property: 'start_time', type: 'string', description: 'Local start time, HH:MM:SS.'),
        new OA\Property(property: 'duration_time', type: 'string', description: 'Duration, HH:MM:SS.'),
        new OA\Property(property: 'time_zone', type: 'string'),
        new OA\Property(property: 'formats', type: 'string', description: 'Comma-separated format key strings.'),
        new OA\Property(property: 'lang_enum', type: 'string'),
        new OA\Property(property: 'longitude', type: 'string'),
        new OA\Property(property: 'latitude', type: 'string'),
        new OA\Property(property: 'distance_in_km', type: 'string', description: 'Present only when sorting by distance.'),
        new OA\Property(property: 'distance_in_miles', type: 'string', description: 'Present only when sorting by distance.'),
        new OA\Property(property: 'meeting_name', type: 'string'),
        new OA\Property(property: 'location_text', type: 'string'),
        new OA\Property(property: 'location_info', type: 'string'),
        new OA\Property(property: 'location_street', type: 'string'),
        new OA\Property(property: 'location_city_subsection', type: 'string'),
        new OA\Property(property: 'location_neighborhood', type: 'string'),
        new OA\Property(property: 'location_municipality', type: 'string'),
        new OA\Property(property: 'location_sub_province', type: 'string'),
        new OA\Property(property: 'location_province', type: 'string'),
        new OA\Property(property: 'location_postal_code_1', type: 'string'),
        new OA\Property(property: 'location_nation', type: 'string'),
        new OA\Property(property: 'comments', type: 'string'),
        new OA\Property(property: 'train_lines', type: 'string'),
        new OA\Property(property: 'bus_lines', type: 'string'),
        new OA\Property(property: 'phone_meeting_number', type: 'string'),
        new OA\Property(property: 'virtual_meeting_link', type: 'string'),
        new OA\Property(property: 'virtual_meeting_additional_info', type: 'string'),
        new OA\Property(property: 'contact_name_1', type: 'string'),
        new OA\Property(property: 'contact_phone_1', type: 'string'),
        new OA\Property(property: 'contact_email_1', type: 'string'),
        new OA\Property(property: 'root_server_id', type: 'string', description: 'Aggregator mode only — ID of the root server this meeting came from.'),
        new OA\Property(property: 'root_server_uri', type: 'string'),
        new OA\Property(property: 'format_shared_id_list', type: 'string'),
    ]
)]
#[OA\Schema(
    schema: 'SemanticFormat',
    type: 'object',
    additionalProperties: true,
    properties: [
        new OA\Property(property: 'key_string', type: 'string'),
        new OA\Property(property: 'name_string', type: 'string'),
        new OA\Property(property: 'description_string', type: 'string'),
        new OA\Property(property: 'lang', type: 'string'),
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'world_id', type: 'string'),
        new OA\Property(property: 'root_server_id', type: 'string', description: 'Aggregator mode only.'),
        new OA\Property(property: 'root_server_uri', type: 'string', description: 'Aggregator mode only.'),
        new OA\Property(property: 'format_type_enum', type: 'string'),
    ]
)]
#[OA\Schema(
    schema: 'SemanticServiceBody',
    type: 'object',
    additionalProperties: true,
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'parent_id', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string'),
        new OA\Property(property: 'type', type: 'string'),
        new OA\Property(property: 'url', type: 'string'),
        new OA\Property(property: 'helpline', type: 'string'),
        new OA\Property(property: 'world_id', type: 'string'),
        new OA\Property(property: 'root_server_id', type: 'string', description: 'Aggregator mode only.'),
        new OA\Property(property: 'root_server_uri', type: 'string', description: 'Aggregator mode only.'),
    ]
)]
#[OA\Schema(
    schema: 'SemanticMeetingChange',
    type: 'object',
    additionalProperties: true,
    properties: [
        new OA\Property(property: 'date_int', type: 'string', description: 'Unix timestamp of the change.'),
        new OA\Property(property: 'date_string', type: 'string'),
        new OA\Property(property: 'change_type', type: 'string', description: 'e.g. comdef_change_type_change, comdef_change_type_new, comdef_change_type_delete.'),
        new OA\Property(property: 'meeting_id', type: 'string'),
        new OA\Property(property: 'meeting_name', type: 'string'),
        new OA\Property(property: 'user_id', type: 'string'),
        new OA\Property(property: 'user_name', type: 'string'),
        new OA\Property(property: 'service_body_id', type: 'string'),
        new OA\Property(property: 'service_body_name', type: 'string'),
        new OA\Property(property: 'details', type: 'object', additionalProperties: true, description: 'Per-field before/after values for change events.'),
    ]
)]
#[OA\Schema(
    schema: 'SemanticServerInfo',
    type: 'object',
    additionalProperties: true,
    properties: [
        new OA\Property(property: 'version', type: 'string'),
        new OA\Property(property: 'versionInt', type: 'string'),
        new OA\Property(property: 'langs', type: 'string', description: 'Comma-separated language codes.'),
        new OA\Property(property: 'nativeLang', type: 'string'),
        new OA\Property(property: 'centerLongitude', type: 'string'),
        new OA\Property(property: 'centerLatitude', type: 'string'),
        new OA\Property(property: 'centerZoom', type: 'string'),
        new OA\Property(property: 'defaultDuration', type: 'string'),
        new OA\Property(property: 'regionBias', type: 'string'),
        new OA\Property(property: 'charSet', type: 'string'),
        new OA\Property(property: 'distanceUnits', type: 'string', enum: ['mi', 'km']),
        new OA\Property(property: 'semanticAdmin', type: 'string'),
        new OA\Property(property: 'emailEnabled', type: 'string'),
        new OA\Property(property: 'emailIncludesServiceBodies', type: 'string'),
        new OA\Property(property: 'changesPerMeeting', type: 'string'),
        new OA\Property(property: 'meeting_states_and_provinces', type: 'string'),
        new OA\Property(property: 'meeting_counties_and_sub_provinces', type: 'string'),
        new OA\Property(property: 'available_keys', type: 'string', description: 'Comma-separated list of field keys exposed by this server.'),
        new OA\Property(property: 'google_api_key', type: 'string'),
        new OA\Property(property: 'dbVersion', type: 'string', description: 'Identifier of the most recently applied database migration.'),
        new OA\Property(property: 'dbPrefix', type: 'string', description: 'Configured database table prefix.'),
        new OA\Property(property: 'phpVersion', type: 'string', description: 'PHP version powering the server.'),
        new OA\Property(property: 'auto_geocoding_enabled', oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'boolean')], description: 'Whether automatic geocoding from address fields is enabled.'),
        new OA\Property(property: 'county_auto_geocoding_enabled', oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'boolean')], description: 'Whether automatic geocoding of the county field is enabled.'),
        new OA\Property(property: 'zip_auto_geocoding_enabled', oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'boolean')], description: 'Whether automatic geocoding from postal codes is enabled.'),
        new OA\Property(property: 'commit', type: 'string', description: 'Source-control commit identifier of the running build.'),
        new OA\Property(property: 'default_closed_status', oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'boolean')], description: 'Default open/closed status applied when a meeting has neither the OPEN nor CLOSED format.'),
        new OA\Property(property: 'aggregator_mode_enabled', oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'boolean')], description: '`1` / `true` if this server is running in aggregator mode.'),
    ]
)]
#[OA\Schema(
    schema: 'SemanticError',
    type: 'object',
    additionalProperties: true,
    properties: [
        new OA\Property(property: 'error', type: 'string', description: 'Human-readable error message.'),
    ]
)]
class Schemas extends Controller
{
}
