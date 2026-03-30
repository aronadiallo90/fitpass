<?php

namespace App\Services;

use App\Exceptions\CheckinException;
use App\Jobs\SendCheckinSms;
use App\Models\Gym;
use App\Models\GymCheckin;
use App\Models\User;
use App\Services\Interfaces\CheckinServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CheckinService implements CheckinServiceInterface
{
    public function validate(string $qrToken, Gym $gym): GymCheckin
    {
        // 1. Retrouver le membre par son QR token
        $user = User::where('qr_token', $qrToken)->first();

        if ($user === null) {
            return $this->recordInvalidCheckin(null, $gym, 'QR code inconnu');
        }

        // 2. Vérifier l'abonnement actif
        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->whereDate('expires_at', '>=', today())
            ->latest()
            ->first();

        if ($subscription === null) {
            return $this->recordInvalidCheckin($user, $gym, 'Pas d\'abonnement actif');
        }

        // 3. Vérifier la règle 1 checkin/salle/jour
        $alreadyCheckedIn = GymCheckin::where('user_id', $user->id)
            ->where('gym_id', $gym->id)
            ->where('status', 'valid')
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyCheckedIn) {
            return $this->recordInvalidCheckin($user, $gym, 'Déjà validé aujourd\'hui dans cette salle');
        }

        // 4. Vérifier les séances restantes (plan découverte)
        if (! $subscription->isUnlimited() && ! $subscription->hasCheckinsLeft()) {
            return $this->recordInvalidCheckin($user, $gym, 'Plus de séances disponibles');
        }

        // 5. Enregistrer le checkin valide + décrémenter si pass limité
        return DB::transaction(function () use ($user, $gym, $subscription): GymCheckin {
            if (! $subscription->isUnlimited()) {
                $subscription->decrement('checkins_remaining');
            }

            $checkin = GymCheckin::create([
                'user_id'         => $user->id,
                'gym_id'          => $gym->id,
                'subscription_id' => $subscription->id,
                'status'          => 'valid',
            ]);

            // SMS confirmation async
            SendCheckinSms::dispatch($user, $gym->name)->onQueue('notifications');

            return $checkin;
        });
    }

    public function getMemberHistory(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return GymCheckin::with('gym')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    public function getTodayForGym(Gym $gym): LengthAwarePaginator
    {
        return GymCheckin::with('user')
            ->where('gym_id', $gym->id)
            ->whereDate('created_at', today())
            ->latest()
            ->paginate(50);
    }

    /**
     * Enregistre un checkin invalide (statut 'invalid') sans lever d'exception.
     * Le failure_reason contient le détail pour audit.
     */
    private function recordInvalidCheckin(?User $user, Gym $gym, string $reason): GymCheckin
    {
        return GymCheckin::create([
            'user_id'         => $user?->id,
            'gym_id'          => $gym->id,
            'subscription_id' => null,
            'status'          => 'invalid',
            'failure_reason'  => $reason,
        ]);
    }
}
