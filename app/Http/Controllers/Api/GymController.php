<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GymProfileResource;
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

    /**
     * GET /api/v1/gyms/{slug}/profile
     * Profil complet : activités, photos, programmes actifs, horaires, distance
     */
    public function profile(string $slug): GymProfileResource
    {
        $gym = Gym::active()
            ->where('slug', $slug)
            ->with([
                'gymActivities',
                'photos'   => fn($q) => $q->orderBy('display_order'),
                'programs' => fn($q) => $q->where('is_active', true)->orderBy('name'),
            ])
            ->firstOrFail();

        return new GymProfileResource($gym);
    }

    /**
     * GET /api/v1/gyms/{slug}/programs
     * Liste des programmes actifs de la salle
     */
    public function programs(string $slug): JsonResponse
    {
        $gym = Gym::active()->where('slug', $slug)->firstOrFail();

        $programs = $gym->programs()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'               => $p->id,
                'name'             => $p->name,
                'description'      => $p->description,
                'schedule'         => $p->schedule,
                'duration_minutes' => $p->duration_minutes,
                'max_spots'        => $p->max_spots,
            ]);

        return response()->json(['data' => $programs]);
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
