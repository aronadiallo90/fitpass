<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth gérée par le middleware Sanctum
    }

    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'uuid', 'exists:subscription_plans,id'],
            'method'  => ['required', 'string', 'in:wave,orange_money'],
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'Le plan est obligatoire.',
            'plan_id.exists'   => 'Ce plan n\'existe pas ou n\'est pas disponible.',
            'method.required'  => 'Le moyen de paiement est obligatoire.',
            'method.in'        => 'Moyen de paiement invalide. Valeurs acceptées : wave, orange_money.',
        ];
    }
}
