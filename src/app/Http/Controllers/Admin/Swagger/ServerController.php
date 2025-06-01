<?php

namespace App\Http\Controllers\Admin\Swagger;

/**
 * @OA\Schema(schema="ServerBase",
 *     @OA\Property(property="sourceId", type="integer", example="0"),
 *     @OA\Property(property="name", type="string", example="string"),
 *     @OA\Property(property="url", type="string", example="https://example.com/main_server"),
 *     @OA\Property(property="statistics", type="object", required={"serviceBodies", "meetings"},
 *         @OA\Property(property="serviceBodies", type="object", required={"numZones", "numRegions", "numAreas", "numGroups"},
 *             @OA\Property(property="numZones", type="integer", example="0"),
 *             @OA\Property(property="numRegions", type="integer", example="0"),
 *             @OA\Property(property="numAreas", type="integer", example="0"),
 *             @OA\Property(property="numGroups", type="integer", example="0"),
 *         ),
 *         @OA\Property(property="meetings", type="object", required={"numTotal", "numInPerson", "numVirtual", "numHybrid", "numUnknown"},
 *             @OA\Property(property="numTotal", type="integer", example="0"),
 *             @OA\Property(property="numInPerson", type="integer", example="0"),
 *             @OA\Property(property="numVirtual", type="integer", example="0"),
 *             @OA\Property(property="numHybrid", type="integer", example="0"),
 *             @OA\Property(property="numUnknown", type="integer", example="0"),
 *         ),
 *     ),
 *     @OA\Property(property="serverInfo", type="string", example="string"),
 *     @OA\Property(property="lastSuccessfulImport", type="string", format="date-time", example="2022-11-25 04:16:26")
 * ),
 * @OA\Schema(schema="Server", required={"id", "sourceId", "name", "url", "lastSuccessfulImport"},
 *     allOf={ @OA\Schema(ref="#/components/schemas/ServerBase") },
 *     @OA\Property(property="id", type="integer", example="0"),
 * ),
 * @OA\Schema(schema="ServerCollection", type="array",
 *     @OA\Items(ref="#/components/schemas/Server")
 * ),
 */
class ServerController extends Controller
{

    /**
     * @OA\Get(path="/api/v1/servers", summary="Retrieves servers", description="Retrieve servers.", operationId="getServers", tags={"server"},
     *     @OA\Response(response=200, description="Successful response.",
     *         @OA\JsonContent(ref="#/components/schemas/ServerCollection")
     *     ),
     *     @OA\Response(response=404, description="Returns when aggregator mode is disabled.",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     ),
     * )
     */
    public function index()
    {
    }

    /**
     * @OA\Get(path="/api/v1/servers/{serverId}", summary="Retrieves a server", description="Retrieve a single server id.", operationId="getServer", tags={"server"},
     *     @OA\Parameter(description="ID of server", in="path", name="serverId", required=true, example="1",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(response=200, description="Successful response.",
     *         @OA\JsonContent(ref="#/components/schemas/Server")
     *     ),
     *     @OA\Response(response=404, description="Returns when no server exists.",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     ),
     * )
     */
    public function show()
    {
    }
}
