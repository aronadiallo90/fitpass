<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckinResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'status'         => $this->status,
            'is_valid'       => $this->isValid(),
            'failure_reason' => $this->failure_reason,
            'checked_in_at'  => $this->created_at->toIso8601String(),
            'gym'            => new GymResource($this->whenLoaded('gym')),
            'user'           => new UserResource($this->whenLoaded('user')),
        ];
    }
}
