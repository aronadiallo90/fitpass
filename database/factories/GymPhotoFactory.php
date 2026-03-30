<?php

namespace Database\Factories;

use App\Models\GymPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GymPhoto>
 */
class GymPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo_url'         => 'https://picsum.photos/seed/' . $this->faker->uuid() . '/800/600',
            'photo_storage_key' => null, // null en local (URL externe)
            'display_order'     => $this->faker->numberBetween(0, 10),
            'is_cover'          => false,
        ];
    }

    public function cover(): static
    {
        return $this->state(['is_cover' => true, 'display_order' => 0]);
    }
}
