<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username'  => fake()->unique()->userName(),
            'full_name' => fake()->name(),
            'password'  => 'password123',   // otomatis di-hash oleh cast 'hashed'
            'role'      => User::ROLE_STAFF,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_ADMIN]);
    }

    public function staff(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_STAFF]);
    }
}
