<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'         => fake()->name(),
            'phone'        => '+221' . fake()->unique()->numerify('7########'),
            'email'        => fake()->unique()->safeEmail(),
            'password'     => static::$password ??= Hash::make('password'),
            'role'         => 'member',
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn() => ['role' => 'admin']);
    }

    public function superAdmin(): static
    {
        return $this->state(fn() => ['role' => 'super_admin']);
    }

    public function gymOwner(): static
    {
        return $this->state(fn() => ['role' => 'gym_owner']);
    }
}
