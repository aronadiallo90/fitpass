<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'paytech_ref'     => $this->paytech_ref,
            'method'          => $this->method,
            'status'          => $this->status,
            'amount_fcfa'     => $this->amount_fcfa,
            'amount_formatted' => $this->formattedAmount(),
            'paid_at'         => $this->paid_at?->toIso8601String(),
            'created_at'      => $this->created_at->toIso8601String(),
        ];
    }
}
