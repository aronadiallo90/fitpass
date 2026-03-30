<?php

namespace App\Http\Requests\Api;

use App\Models\Gym;
use Illuminate\Foundation\Http\FormRequest;

class ValidateCheckinRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Valider que le gym_api_token correspond à une salle active
        $apiToken = $this->header('X-Gym-Token') ?? $this->input('gym_api_token');
        $gym      = Gym::where('api_token', $apiToken)->where('is_active', true)->first();

        return $gym !== null;
    }

    public function rules(): array
    {
        return [
            'qr_token'      => ['required', 'string', 'uuid'],
            'gym_api_token' => ['sometimes', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'qr_token.required' => 'Le QR token est obligatoire.',
            'qr_token.uuid'     => 'Format de QR token invalide.',
        ];
    }
}
