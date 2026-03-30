<?php

namespace App\Services;

use App\Exceptions\SubscriptionException;
use App\Jobs\SendActivationSms;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\Interfaces\SubscriptionServiceInterface;
use Illuminate\Support\Facades\DB;

class SubscriptionService implements SubscriptionServiceInterface
{
    public function create(User $user, SubscriptionPlan $plan): Subscription
    {
        // Vérifier qu'il n'y a pas d'abonnement actif existant
        if ($this->getActive($user) !== null) {
            throw SubscriptionException::alreadyActive();
        }

        return DB::transaction(function () use ($user, $plan): Subscription {
            return Subscription::create([
                'user_id'            => $user->id,
                'plan_id'            => $plan->id,
                'reference'          => $this->generateReference(),
                'status'             => 'pending',
                'amount_fcfa'        => $plan->price_fcfa,
                'checkins_remaining' => $plan->checkins_limit, // null = illimité
            ]);
        });
    }

    public function activate(Subscription $subscription): Subscription
    {
        $plan = $subscription->plan;

        $subscription->update([
            'status'    => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
        ]);

        // SMS d'activation en async (Redis queue)
        SendActivationSms::dispatch($subscription)->onQueue('notifications');

        return $subscription->fresh();
    }

    public function expire(Subscription $subscription): Subscription
    {
        $subscription->update(['status' => 'expired']);

        return $subscription->fresh();
    }

    public function cancel(Subscription $subscription): Subscription
    {
        if ($subscription->status === 'expired') {
            throw SubscriptionException::cannotCancel('expired');
        }

        $subscription->update(['status' => 'cancelled']);

        return $subscription->fresh();
    }

    public function getActive(User $user): ?Subscription
    {
        return $user->subscriptions()
            ->where('status', 'active')
            ->whereDate('expires_at', '>=', today())
            ->with('plan')
            ->latest()
            ->first();
    }

    public function generateReference(): string
    {
        $year    = now()->year;
        $lastRef = Subscription::whereYear('created_at', $year)
            ->orderByDesc('created_at')
            ->value('reference');

        if ($lastRef === null) {
            $sequence = 1;
        } else {
            // Format : FIT-2026-00042 → extraire 42
            $parts    = explode('-', $lastRef);
            $sequence = (int) end($parts) + 1;
        }

        return sprintf('FIT-%d-%05d', $year, $sequence);
    }
}
