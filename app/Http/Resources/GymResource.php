<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GymResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'address'       => $this->address,
            'latitude'      => (float) $this->latitude,
            'longitude'     => (float) $this->longitude,
            'activities'    => $this->activities ?? [],
            'opening_hours' => $this->opening_hours ?? [],
            'phone'         => $this->phone,
            'photo_url'     => $this->photo_url,
            'is_active'     => $this->is_active,
        ];
    }
}
