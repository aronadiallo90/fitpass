<?php

namespace Database\Factories;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Gym>
 */
class GymFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Fitness Center Dakar', 'PowerGym Plateau', 'Body Studio Almadies',
            'FitZone Mermoz', 'Iron Gym Point E', 'City Fit Liberté',
        ]);

        return [
            'owner_id'      => User::factory()->gymOwner(),
            'name'          => $name,
            'slug'          => Str::slug($name) . '-' . fake()->unique()->randomNumber(4),
            'description'   => 'Salle de sport moderne équipée à Dakar.',
            'address'       => fake()->streetAddress() . ', Dakar',
            'latitude'      => fake()->latitude(14.68, 14.76),
            'longitude'     => fake()->longitude(-17.49, -17.41),
            'activities'    => ['musculation', 'cardio'],
            'opening_hours' => ['lun-ven' => '06h-22h', 'sam-dim' => '07h-20h'],
            'phone'         => '+221' . fake()->numerify('7########'),
            'is_active'     => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }
}
