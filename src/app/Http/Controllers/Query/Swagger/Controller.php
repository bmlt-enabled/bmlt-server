<?php

namespace App\Http\Controllers\Query\Swagger;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: '3.1.0',
    security: []
)]
#[OA\Info(
    version: '1.0.0',
    description: <<<DESC
OpenAPI description of the BMLT Semantic Interface (the read-only meeting-query API) for the JSON data format.

The semantic interface dispatches all operations from a single endpoint (`/client_interface/json/`) using a `switcher` query parameter. To keep each operation discoverable, this spec models each `switcher` value as a distinct path key with the query string baked in. Tools such as Swagger UI, Redoc, and openapi-generator handle these path keys correctly even though the OpenAPI spec technically expects unique URL paths.

### Things that do not map cleanly onto OpenAPI

- **PHP array syntax** — repeatable array parameters use trailing `[]` in the name (`weekdays[]=2&weekdays[]=3`).
- **Sign-as-operator** — many filters (`formats`, `services`, `weekdays`, `venue_types`, `meeting_ids`, `root_server_ids`, `format_ids`) use *positive* values to include and *negative* values to exclude. JSON Schema cannot enforce that semantics; it is documented per parameter.
- **Cross-parameter constraints** — in aggregator mode `GetSearchResults` requires at least one filter parameter. Invalid combinations typically return an empty array `[]` instead of an HTTP error.
- **Empty-array errors** — many endpoints return `[]` for invalid input rather than a 4xx response body.
DESC,
    title: 'BMLT Semantic API'
)]
#[OA\Server(
    url: 'L5_SWAGGER_CONST_HOST',
    description: 'this server'
)]
#[OA\Tag(name: 'Meetings', description: 'Meeting search and change history')]
#[OA\Tag(name: 'Formats', description: 'Meeting format metadata')]
#[OA\Tag(name: 'Service Bodies', description: 'Service body (region/area) metadata')]
#[OA\Tag(name: 'Fields', description: 'Schema introspection')]
#[OA\Tag(name: 'Server', description: 'Server information and coverage')]
class Controller
{
}
