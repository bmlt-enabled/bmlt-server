<?php

namespace App\Http\Controllers\Admin;

use App\Interfaces\TimeZoneRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeZoneController extends ResourceController
{
    public function store(Request $request, TimeZoneRepositoryInterface $timeZones): JsonResponse
    {
        if ($request->user()->isDeactivated()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $timeZone = $timeZones->getByCoordinates(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
        );

        return response()->json(['timeZone' => $timeZone]);
    }
}
