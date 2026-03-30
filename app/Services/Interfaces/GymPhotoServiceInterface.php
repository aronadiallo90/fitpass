<?php

namespace App\Services\Interfaces;

use App\Models\Gym;
use App\Models\GymPhoto;
use Illuminate\Http\UploadedFile;

interface GymPhotoServiceInterface
{
    /** Enregistre une photo uploadée et retourne le modèle GymPhoto créé */
    public function store(Gym $gym, UploadedFile $file, bool $isCover = false): GymPhoto;

    /** Supprime la photo du stockage et de la base */
    public function delete(GymPhoto $photo): void;

    /** Définit une photo comme cover (décoche les autres de la même salle) */
    public function setCover(GymPhoto $photo): void;

    /** Réordonne les photos selon le tableau d'IDs fourni */
    public function reorder(Gym $gym, array $orderedIds): void;
}
