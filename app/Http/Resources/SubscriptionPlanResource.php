<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'price_fcfa'     => $this->price_fcfa,
            'price_formatted' => $this->formattedPrice(),
            'duration_days'  => $this->duration_days,
            'checkins_limit' => $this->checkins_limit, // null = illimité
            'is_unlimited'   => $this->isUnlimited(),
            'is_active'      => $this->is_active,
        ];
    }
}
