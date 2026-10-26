<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StockMovement> */
class StockMovementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id'    => User::factory(),
            'type'       => StockMovement::TYPE_IN,
            'quantity'   => fake()->numberBetween(1, 50),
            'notes'      => fake()->optional()->sentence(),
        ];
    }

    public function in(): static
    {
        return $this->state(fn () => ['type' => StockMovement::TYPE_IN]);
    }

    public function out(): static
    {
        return $this->state(fn () => ['type' => StockMovement::TYPE_OUT]);
    }
}
