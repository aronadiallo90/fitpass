<?php

namespace Database\Factories;

use App\Models\Gym;
use App\Models\GymCheckin;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GymCheckin>
 */
class GymCheckinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'gym_id'          => Gym::factory(),
            'subscription_id' => Subscription::factory(),
            'status'          => 'valid',
            'failure_reason'  => null,
        ];
    }

    public function invalid(string $reason = 'no_subscription'): static
    {
        return $this->state(fn() => [
            'status'          => 'invalid',
            'subscription_id' => null,
            'failure_reason'  => $reason,
        ]);
    }

    public function today(): static
    {
        return $this->state(fn() => [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
