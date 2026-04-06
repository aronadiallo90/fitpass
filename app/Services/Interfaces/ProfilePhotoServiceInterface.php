<?php

namespace App\Services\Interfaces;

use App\Models\User;
use Illuminate\Http\UploadedFile;

interface ProfilePhotoServiceInterface
{
    /**
     * Redimensionne, stocke et enregistre la photo de profil d'un membre.
     * Supprime l'ancienne photo si elle existe.
     *
     * @return string le path stocké (ex: profiles/uuid.jpg)
     */
    public function store(User $user, UploadedFile $file): string;

    /**
     * Supprime la photo de profil du membre (fichier + colonne DB).
     */
    public function delete(User $user): void;
}
