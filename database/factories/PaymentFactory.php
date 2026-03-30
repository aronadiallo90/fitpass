<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'user_id'         => User::factory(),
            'paytech_ref'     => 'FAKE-' . strtoupper(Str::random(12)),
            'paytech_token'   => Str::random(32),
            'method'          => fake()->randomElement(['wave', 'orange_money']),
            'status'          => 'completed',
            'amount_fcfa'     => 25000,
            'paytech_payload' => ['response_text' => 'SUCCESS'],
            'paid_at'         => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn() => [
            'status'          => 'pending',
            'paytech_payload' => null,
            'paid_at'         => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn() => [
            'status'  => 'failed',
            'paid_at' => null,
        ]);
    }

    public function wave(): static
    {
        return $this->state(fn() => ['method' => 'wave']);
    }

    public function orangeMoney(): static
    {
        return $this->state(fn() => ['method' => 'orange_money']);
    }
}
