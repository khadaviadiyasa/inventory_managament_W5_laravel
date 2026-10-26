<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Supplier> */
class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'           => 'PT ' . fake()->unique()->company(),
            'contact_person' => fake()->name(),
            'phone'          => fake()->numerify('08##########'),
            'email'          => fake()->unique()->companyEmail(),
            'address'        => fake()->address(),
        ];
    }
}
