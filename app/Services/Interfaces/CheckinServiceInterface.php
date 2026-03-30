<?php

namespace App\Services\Interfaces;

use App\Models\Gym;
use App\Models\GymCheckin;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface CheckinServiceInterface
{
    /**
     * Valide un QR token pour l'entrée dans une salle.
     * Applique les règles métier : abonnement actif, 1 checkin/salle/jour, séances restantes.
     */
    public function validate(string $qrToken, Gym $gym): GymCheckin;

    /**
     * Retourne l'historique paginé des checkins d'un membre.
     */
    public function getMemberHistory(User $user, int $perPage = 15): LengthAwarePaginator;

    /**
     * Retourne les checkins du jour pour une salle (gym owner).
     */
    public function getTodayForGym(Gym $gym): LengthAwarePaginator;
}
