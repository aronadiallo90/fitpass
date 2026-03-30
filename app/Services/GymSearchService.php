<?php

namespace App\Services;

use App\Models\Gym;
use App\Services\Interfaces\GymSearchServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GymSearchService implements GymSearchServiceInterface
{
    private const CACHE_TTL_SECONDS = 300; // 5 minutes

    public function search(array $filters): LengthAwarePaginator
    {
        $lat     = isset($filters['lat']) ? (float) $filters['lat'] : null;
        $lng     = isset($filters['lng']) ? (float) $filters['lng'] : null;
        $perPage = (int) ($filters['per_page'] ?? 15);

        // Clé cache — lat/lng arrondis à 2 décimales (~1km de précision)
        $cacheKey = 'gym_search:' . md5(json_encode(array_merge(
            $filters,
            $lat !== null ? ['lat' => round($lat, 2), 'lng' => round($lng, 2)] : [],
        )));

        // Le cache stocke les IDs ordonnés — on recharge les modèles après
        // pour éviter de mettre des objets lourds dans Redis
        $cachedIds = Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($filters, $lat, $lng, $perPage) {
            return $this->buildQuery($filters, $lat, $lng)
                ->paginate($perPage)
                ->pluck('id')
                ->all();
        });

        // Requête principale sans cache (pagination réelle sur les IDs)
        return $this->buildQuery($filters, $lat, $lng)->paginate($perPage);
    }

    private function buildQuery(array $filters, ?float $lat, ?float $lng)
    {
        $query = Gym::active()->with(['activities', 'photos' => fn($q) => $q->where('is_cover', true)]);

        // Filtre par nom
        if (! empty($filters['q'])) {
            $query->where('name', 'like', '%' . $filters['q'] . '%');
        }

        // Filtre par zone
        if (! empty($filters['zone'])) {
            $query->where('zone', $filters['zone']);
        }

        // Filtre par activité (many-to-many via gym_activities)
        if (! empty($filters['activity'])) {
            $query->whereHas('activities', fn($q) => $q->where('slug', $filters['activity']));
        }

        // Tri par distance (Haversine) — uniquement si lat/lng fournis ET driver MySQL
        if ($lat !== null && $lng !== null && $this->supportsHaversine()) {
            $haversine = DB::raw(
                "(6371 * acos(LEAST(1.0, " .
                "cos(radians({$lat})) * cos(radians(latitude)) * " .
                "cos(radians(longitude) - radians({$lng})) + " .
                "sin(radians({$lat})) * sin(radians(latitude))" .
                "))) AS distance_km"
            );

            $query->selectRaw('gyms.*, ' . $haversine->getValue(DB::connection()->getQueryGrammar()))
                  ->orderBy('distance_km');
        } else {
            $query->orderBy('name');
        }

        return $query;
    }

    /** Haversine requiert MySQL/MariaDB — désactivé sur SQLite (tests) */
    private function supportsHaversine(): bool
    {
        return in_array(
            DB::connection()->getDriverName(),
            ['mysql', 'mariadb', 'pgsql'],
            strict: true
        );
    }
}
