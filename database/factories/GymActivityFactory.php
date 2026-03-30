<?php

namespace Database\Factories;

use App\Models\GymActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GymActivity>
 */
class GymActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $activities = [
            ['name' => 'Musculation',   'slug' => 'muscu',        'icon' => '🏋️'],
            ['name' => 'Cardio',        'slug' => 'cardio',       'icon' => '🏃'],
            ['name' => 'Yoga',          'slug' => 'yoga',         'icon' => '🧘'],
            ['name' => 'Spinning',      'slug' => 'spinning',     'icon' => '🚴'],
            ['name' => 'CrossFit',      'slug' => 'crossfit',     'icon' => '💪'],
            ['name' => 'Natation',      'slug' => 'natation',     'icon' => '🏊'],
            ['name' => 'Boxe',          'slug' => 'boxe',         'icon' => '🥊'],
            ['name' => 'Arts martiaux', 'slug' => 'arts-martiaux','icon' => '🥋'],
        ];

        $pick = $this->faker->unique()->randomElement($activities);

        return [
            'name' => $pick['name'],
            'slug' => $pick['slug'],
            'icon' => $pick['icon'],
        ];
    }
}
