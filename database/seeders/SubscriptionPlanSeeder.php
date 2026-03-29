<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'           => 'Découverte',
                'slug'           => 'decouverte',
                'price_fcfa'     => 12000,
                'duration_days'  => null,
                'checkins_limit' => 4,
                'is_active'      => true,
                'sort_order'     => 1,
            ],
            [
                'name'           => 'Mensuel',
                'slug'           => 'mensuel',
                'price_fcfa'     => 25000,
                'duration_days'  => 30,
                'checkins_limit' => null,
                'is_active'      => true,
                'sort_order'     => 2,
            ],
            [
                'name'           => 'Trimestriel',
                'slug'           => 'trimestriel',
                'price_fcfa'     => 65000,
                'duration_days'  => 90,
                'checkins_limit' => null,
                'is_active'      => true,
                'sort_order'     => 3,
            ],
            [
                'name'           => 'Annuel',
                'slug'           => 'annuel',
                'price_fcfa'     => 220000,
                'duration_days'  => 365,
                'checkins_limit' => null,
                'is_active'      => true,
                'sort_order'     => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
