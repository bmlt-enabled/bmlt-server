<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeocodingException;
use App\Services\GeoApiGeocoder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeocodeController extends ResourceController
{
    public function store(Request $request, GeoApiGeocoder $geocoder): JsonResponse
    {
        if ($request->user()->isDeactivated()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'address' => 'required|string|max:65535',
        ]);

        $referrer = $request->header('referer') ?: $request->getSchemeAndHttpHost();

        try {
            $result = $geocoder->geocode($validated['address'], $referrer);
        } catch (GeocodingException $e) {
            return response()->json(['message' => $e->getMessage()], $e->status);
        }

        return response()->json($result);
    }
}
