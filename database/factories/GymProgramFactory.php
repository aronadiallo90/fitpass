<?php

namespace Database\Factories;

use App\Models\GymProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GymProgram>
 */
class GymProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        $schedule = [];
        foreach ($this->faker->randomElements($days, $this->faker->numberBetween(2, 4)) as $day) {
            $schedule[$day] = [$this->faker->randomElement(['07:00', '08:00', '09:00', '17:00', '18:00'])];
        }

        return [
            'name'             => $this->faker->randomElement([
                'Yoga du matin', 'HIIT Express', 'CrossFit Avancé',
                'Spinning Cardio', 'Boxe Débutant', 'Pilates', 'Zumba',
            ]),
            'description'      => $this->faker->optional()->sentence(),
            'schedule'         => $schedule,
            'duration_minutes' => $this->faker->randomElement([30, 45, 60, 90]),
            'max_spots'        => $this->faker->optional()->numberBetween(8, 20),
            'is_active'        => true,
        ];
    }
}
