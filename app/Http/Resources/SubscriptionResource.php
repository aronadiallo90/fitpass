<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'reference'          => $this->reference,
            'status'             => $this->status,
            'amount_fcfa'        => $this->amount_fcfa,
            'checkins_remaining' => $this->checkins_remaining,
            'is_unlimited'       => $this->isUnlimited(),
            'days_remaining'     => $this->daysRemaining(),
            'is_expiring_soon'   => $this->isExpiringSoon(),
            'starts_at'          => $this->starts_at?->toIso8601String(),
            'expires_at'         => $this->expires_at?->toIso8601String(),
            'created_at'         => $this->created_at->toIso8601String(),
            'plan'               => new SubscriptionPlanResource($this->whenLoaded('plan')),
        ];
    }
}
