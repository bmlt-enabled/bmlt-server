<?php

namespace App\Http\Controllers\Admin\Swagger;

/**
 * @OA\Schema(
 *     schema="SettingsBase",
 *     @OA\Property(property="googleApiKey", type="string"),
 *     @OA\Property(property="changeDepthForMeetings", type="integer"),
 *     @OA\Property(property="defaultSortKey", type="string", nullable=true),
 *     @OA\Property(property="language", type="string"),
 *     @OA\Property(property="defaultDurationTime", type="string"),
 *     @OA\Property(property="regionBias", type="string"),
 *     @OA\Property(property="distanceUnits", type="string"),
 *     @OA\Property(property="meetingStatesAndProvinces", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="meetingCountiesAndSubProvinces", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="searchSpecMapCenterLongitude", type="number", format="float"),
 *     @OA\Property(property="searchSpecMapCenterLatitude", type="number", format="float"),
 *     @OA\Property(property="searchSpecMapCenterZoom", type="integer"),
 *     @OA\Property(property="numberOfMeetingsForAuto", type="integer"),
 *     @OA\Property(property="autoGeocodingEnabled", type="boolean"),
 *     @OA\Property(property="countyAutoGeocodingEnabled", type="boolean"),
 *     @OA\Property(property="zipAutoGeocodingEnabled", type="boolean"),
 *     @OA\Property(property="defaultClosedStatus", type="boolean"),
 *     @OA\Property(property="enableLanguageSelector", type="boolean"),
 *     @OA\Property(property="includeServiceBodyEmailInSemantic", type="boolean"),
 *     @OA\Property(property="bmltTitle", type="string"),
 *     @OA\Property(property="bmltNotice", type="string"),
 *     @OA\Property(property="formatLangNames", type="object")
 * ),
 * @OA\Schema(schema="SettingsObject",
 *     @OA\Property(property="googleApiKey", type="string", example=""),
 *     @OA\Property(property="changeDepthForMeetings", type="integer", example=0),
 *     @OA\Property(property="defaultSortKey", type="string", nullable=true, example=null),
 *     @OA\Property(property="language", type="string", example="en"),
 *     @OA\Property(property="defaultDurationTime", type="string", example="01:00"),
 *     @OA\Property(property="regionBias", type="string", example="us"),
 *     @OA\Property(property="distanceUnits", type="string", example="mi"),
 *     @OA\Property(property="meetingStatesAndProvinces", type="array", @OA\Items(type="string"), example={}),
 *     @OA\Property(property="meetingCountiesAndSubProvinces", type="array", @OA\Items(type="string"), example={}),
 *     @OA\Property(property="searchSpecMapCenterLongitude", type="number", format="float", example=-118.563659),
 *     @OA\Property(property="searchSpecMapCenterLatitude", type="number", format="float", example=34.235918),
 *     @OA\Property(property="searchSpecMapCenterZoom", type="integer", example=6),
 *     @OA\Property(property="numberOfMeetingsForAuto", type="integer", example=10),
 *     @OA\Property(property="autoGeocodingEnabled", type="boolean", example=true),
 *     @OA\Property(property="countyAutoGeocodingEnabled", type="boolean", example=false),
 *     @OA\Property(property="zipAutoGeocodingEnabled", type="boolean", example=false),
 *     @OA\Property(property="defaultClosedStatus", type="boolean", example=true),
 *     @OA\Property(property="enableLanguageSelector", type="boolean", example=false),
 *     @OA\Property(property="includeServiceBodyEmailInSemantic", type="boolean", example=false),
 *     @OA\Property(property="bmltTitle", type="string", example=""),
 *     @OA\Property(property="bmltNotice", type="string", example=""),
 *     @OA\Property(property="formatLangNames", type="object", example={})
 * ),
 * @OA\Schema(schema="SettingsUpdate",
 *     description="Partial update object - include only the settings you want to update",
 *     allOf={ @OA\Schema(ref="#/components/schemas/SettingsBase") }
 * )
 */
class SettingController extends Controller
{
    /**
     * @OA\Get(path="/api/v1/settings", summary="Retrieves all settings", description="Retrieve all server settings. Only accessible to server administrators.", operationId="getSettings", tags={"rootServer"}, security={{"bmltToken":{}}},
     *     @OA\Response(response=200, description="Returns when user is authenticated as admin.",
     *         @OA\JsonContent(ref="#/components/schemas/SettingsObject")
     *     ),
     *     @OA\Response(response=401, description="Returns when not authenticated",
     *         @OA\JsonContent(ref="#/components/schemas/AuthenticationError")
     *     ),
     *     @OA\Response(response=403, description="Returns when user is not an admin",
     *         @OA\JsonContent(ref="#/components/schemas/AuthorizationError")
     *     )
     * )
     */
    public function index()
    {
    }

    /**
     * @OA\Patch(path="/api/v1/settings", summary="Update settings", description="Updates one or more server settings. Only accessible to server administrators.", operationId="updateSettings", tags={"rootServer"}, security={{"bmltToken":{}}},
     *     @OA\RequestBody(required=true, description="Pass in settings object with values to update",
     *         @OA\JsonContent(ref="#/components/schemas/SettingsUpdate"),
     *     ),
     *     @OA\Response(response=204, description="Success."),
     *     @OA\Response(response=401, description="Returns when user is not authenticated.",
     *         @OA\JsonContent(ref="#/components/schemas/AuthenticationError")
     *     ),
     *     @OA\Response(response=403, description="Returns when user is not an admin.",
     *         @OA\JsonContent(ref="#/components/schemas/AuthorizationError")
     *     ),
     *     @OA\Response(response=422, description="Validation error.",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     * )
     */
    public function update()
    {
    }
}
