<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2 Mo maximum
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'Veuillez sélectionner une photo.',
            'photo.image'    => 'Le fichier doit être une image.',
            'photo.mimes'    => 'Formats acceptés : JPG, PNG, WEBP.',
            'photo.max'      => 'La photo ne doit pas dépasser 2 Mo.',
        ];
    }
}
