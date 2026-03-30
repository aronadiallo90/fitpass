<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'          => 'Mensuel',
            'slug'          => 'mensuel',
            'price_fcfa'    => 25000,
            'duration_days' => 30,
            'checkins_limit' => null, // illimité par défaut
            'is_active'     => true,
            'sort_order'    => 2,
        ];
    }

    public function decouverte(): static
    {
        return $this->state(fn() => [
            'name'           => 'Découverte',
            'slug'           => 'decouverte',
            'price_fcfa'     => 15000,
            'duration_days'  => 30,
            'checkins_limit' => 4,
            'sort_order'     => 1,
        ]);
    }

    public function mensuel(): static
    {
        return $this->state(fn() => [
            'name'           => 'Mensuel',
            'slug'           => 'mensuel',
            'price_fcfa'     => 25000,
            'duration_days'  => 30,
            'checkins_limit' => null,
            'sort_order'     => 2,
        ]);
    }

    public function trimestriel(): static
    {
        return $this->state(fn() => [
            'name'           => 'Trimestriel',
            'slug'           => 'trimestriel',
            'price_fcfa'     => 65000,
            'duration_days'  => 90,
            'checkins_limit' => null,
            'sort_order'     => 3,
        ]);
    }

    public function annuel(): static
    {
        return $this->state(fn() => [
            'name'           => 'Annuel',
            'slug'           => 'annuel',
            'price_fcfa'     => 220000,
            'duration_days'  => 365,
            'checkins_limit' => null,
            'sort_order'     => 4,
        ]);
    }
}
