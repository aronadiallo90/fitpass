<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GymProfileResource;
use App\Services\Interfaces\GymSearchServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GymSearchController extends Controller
{
    public function __construct(private GymSearchServiceInterface $searchService) {}

    /**
     * GET /api/v1/gyms/search
     * Paramètres : q, zone, activity, lat, lng, per_page
     */
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'q'        => ['nullable', 'string', 'max:100'],
            'zone'     => ['nullable', 'string', 'max:50'],
            'activity' => ['nullable', 'string', 'max:50'],
            'lat'      => ['nullable', 'numeric', 'between:-90,90'],
            'lng'      => ['nullable', 'numeric', 'between:-180,180'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $gyms = $this->searchService->search($request->only([
            'q', 'zone', 'activity', 'lat', 'lng', 'per_page',
        ]));

        return GymProfileResource::collection($gyms);
    }
}
