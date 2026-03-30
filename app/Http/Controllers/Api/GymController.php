<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GymResource;
use App\Models\Gym;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GymController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $gyms = Gym::active()
            ->orderBy('name')
            ->paginate(20);

        return GymResource::collection($gyms);
    }

    public function show(Gym $gym): JsonResponse
    {
        if (! $gym->is_active) {
            abort(404);
        }

        return response()->json(['data' => new GymResource($gym)]);
    }

    public function geojson(): JsonResponse
    {
        $gyms = Gym::active()
            ->select(['id', 'name', 'address', 'latitude', 'longitude', 'activities', 'phone'])
            ->get();

        // Format GeoJSON pour Leaflet.js
        $features = $gyms->map(fn(Gym $gym) => [
            'type' => 'Feature',
            'geometry' => [
                'type'        => 'Point',
                'coordinates' => [(float) $gym->longitude, (float) $gym->latitude],
            ],
            'properties' => [
                'id'         => $gym->id,
                'name'       => $gym->name,
                'address'    => $gym->address,
                'activities' => $gym->activities ?? [],
                'phone'      => $gym->phone,
            ],
        ]);

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
