<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Database\Seeder;

class GymSeeder extends Seeder
{
    public function run(): void
    {
        $owner1 = User::where('phone', '+221770000010')->first();
        $owner2 = User::where('phone', '+221770000011')->first();

        $gyms = [
            [
                'owner_id'    => $owner1->id,
                'name'        => 'Ideal Gym Dakar',
                'slug'        => 'ideal-gym-dakar',
                'description' => 'Salle de sport moderne au cœur de Dakar avec équipements haut de gamme.',
                'address'     => 'Rue de Thiong, Plateau, Dakar',
                'latitude'    => 14.6928,
                'longitude'   => -17.4467,
                'activities'  => ['muscu', 'cardio', 'crossfit'],
                'opening_hours' => ['lun-ven' => '6h-22h', 'sam' => '7h-20h', 'dim' => '8h-18h'],
                'phone'       => '+221338217890',
                'is_active'   => true,
            ],
            [
                'owner_id'    => $owner2->id,
                'name'        => 'Fitness Almadies',
                'slug'        => 'fitness-almadies',
                'description' => 'Salle premium aux Almadies avec vue mer, spinning et yoga.',
                'address'     => 'Route des Almadies, Ngor, Dakar',
                'latitude'    => 14.7491,
                'longitude'   => -17.5200,
                'activities'  => ['spinning', 'yoga', 'cardio', 'muscu'],
                'opening_hours' => ['lun-ven' => '7h-22h', 'sam-dim' => '8h-20h'],
                'phone'       => '+221338221234',
                'is_active'   => true,
            ],
            [
                'owner_id'    => $owner1->id,
                'name'        => 'FitZone Mermoz',
                'slug'        => 'fitzone-mermoz',
                'description' => 'CrossFit et HIIT dans un cadre industriel à Mermoz.',
                'address'     => 'Cité Mermoz, Dakar',
                'latitude'    => 14.7167,
                'longitude'   => -17.4744,
                'activities'  => ['crossfit', 'hiit', 'muscu'],
                'opening_hours' => ['lun-sam' => '6h-21h'],
                'phone'       => '+221778901234',
                'is_active'   => true,
            ],
            [
                'owner_id'    => $owner2->id,
                'name'        => 'Body Studio Plateau',
                'slug'        => 'body-studio-plateau',
                'description' => 'Studio fitness & pilates au Plateau pour professionnels.',
                'address'     => 'Avenue Léopold Sédar Senghor, Plateau, Dakar',
                'latitude'    => 14.6897,
                'longitude'   => -17.4387,
                'activities'  => ['pilates', 'yoga', 'cardio'],
                'opening_hours' => ['lun-ven' => '7h-20h', 'sam' => '9h-14h'],
                'phone'       => '+221338201122',
                'is_active'   => true,
            ],
            [
                'owner_id'    => $owner1->id,
                'name'        => 'PowerGym Liberté',
                'slug'        => 'powergym-liberte',
                'description' => 'Musculation et boxe aux Parcelles Assainies / Liberté.',
                'address'     => 'Liberté 6, Dakar',
                'latitude'    => 14.7389,
                'longitude'   => -17.4622,
                'activities'  => ['muscu', 'boxe', 'cardio'],
                'opening_hours' => ['lun-sam' => '6h30-22h', 'dim' => '9h-17h'],
                'phone'       => '+221779123456',
                'is_active'   => true,
            ],
        ];

        foreach ($gyms as $gym) {
            Gym::firstOrCreate(['slug' => $gym['slug']], $gym);
        }
    }
}
