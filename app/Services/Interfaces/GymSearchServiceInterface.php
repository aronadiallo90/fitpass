<?php

namespace App\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface GymSearchServiceInterface
{
    /**
     * Recherche les salles actives selon les filtres fournis.
     *
     * @param array{
     *   q?:        string,   // recherche par nom (LIKE)
     *   activity?: string,   // slug activité (ex: 'muscu')
     *   zone?:     string,   // quartier Dakar (ex: 'Plateau')
     *   lat?:      float,    // latitude membre (tri par distance si fourni)
     *   lng?:      float,    // longitude membre
     *   per_page?: int,      // défaut 15
     * } $filters
     */
    public function search(array $filters): LengthAwarePaginator;
}
