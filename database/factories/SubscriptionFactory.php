<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'            => User::factory(),
            'plan_id'            => SubscriptionPlan::factory(),
            'reference'          => 'FIT-' . now()->year . '-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'status'             => 'active',
            'amount_fcfa'        => 25000,
            'checkins_remaining' => null,
            'starts_at'          => now()->subDays(5),
            'expires_at'         => now()->addDays(25),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn() => [
            'status'     => 'pending',
            'starts_at'  => null,
            'expires_at' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn() => [
            'status'     => 'active',
            'starts_at'  => now()->subDays(5),
            'expires_at' => now()->addDays(25),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn() => [
            'status'     => 'expired',
            'starts_at'  => now()->subDays(35),
            'expires_at' => now()->subDays(5),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn() => [
            'status'     => 'cancelled',
            'starts_at'  => null,
            'expires_at' => null,
        ]);
    }

    public function decouverte(): static
    {
        return $this->state(fn() => [
            'amount_fcfa'        => 15000,
            'checkins_remaining' => 4,
        ]);
    }
}
