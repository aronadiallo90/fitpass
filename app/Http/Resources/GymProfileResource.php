<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GymProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'address'        => $this->address,
            'zone'           => $this->zone,
            'latitude'       => (float) $this->latitude,
            'longitude'      => (float) $this->longitude,
            'phone'          => $this->phone,
            'phone_whatsapp' => $this->phone_whatsapp,
            'photo_url'      => $this->photo_url,
            'is_active'      => $this->is_active,

            // Activités normalisées (many-to-many)
            'activities' => $this->whenLoaded('activities', fn() =>
                $this->activities->map(fn($a) => [
                    'id'   => $a->id,
                    'name' => $a->name,
                    'slug' => $a->slug,
                    'icon' => $a->icon,
                ])
            ),

            // Photos triées par display_order
            'photos' => $this->whenLoaded('photos', fn() =>
                $this->photos->map(fn($p) => [
                    'id'            => $p->id,
                    'url'           => $p->photo_url,
                    'is_cover'      => $p->is_cover,
                    'display_order' => $p->display_order,
                ])
            ),

            // Programmes actifs
            'programs' => $this->whenLoaded('programs', fn() =>
                $this->programs->where('is_active', true)->map(fn($p) => [
                    'id'               => $p->id,
                    'name'             => $p->name,
                    'description'      => $p->description,
                    'schedule'         => $p->schedule,
                    'duration_minutes' => $p->duration_minutes,
                    'max_spots'        => $p->max_spots,
                ])
            ),

            // Horaires par jour avec badge ouvert/fermé temps réel
            'opening_hours' => $this->opening_hours ?? [],
            'is_open_now'   => $this->isOpenNow(),

            // Distance (injectée par GymSearchService si tri par distance)
            'distance_km' => $this->when(
                isset($this->resource->distance_km),
                fn() => round((float) $this->resource->distance_km, 1)
            ),
        ];
    }

    /**
     * Calcule si la salle est ouverte en ce moment (fuseau Africa/Dakar).
     * Format horaires attendu : {"lundi": {"open": "06:00", "close": "22:00", "closed": false}}
     */
    private function isOpenNow(): bool
    {
        if (empty($this->opening_hours)) {
            return false;
        }

        $now     = Carbon::now('Africa/Dakar');
        $dayMap  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $dayKeys = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'];
        $dayKey  = $dayKeys[array_search($now->format('l'), $dayMap)] ?? null;

        if ($dayKey === null) {
            return false;
        }

        $hours = $this->opening_hours[$dayKey] ?? null;

        if (! $hours || ($hours['closed'] ?? false)) {
            return false;
        }

        $open  = Carbon::createFromTimeString($hours['open'] ?? '00:00', 'Africa/Dakar');
        $close = Carbon::createFromTimeString($hours['close'] ?? '00:00', 'Africa/Dakar');

        return $now->between($open, $close);
    }
}
