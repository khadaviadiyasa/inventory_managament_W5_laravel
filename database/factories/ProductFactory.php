<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id'   => Category::factory(),
            'sku'           => fake()->unique()->bothify('SKU-####??'),
            'name'          => ucfirst(fake()->unique()->words(3, true)),
            'description'   => fake()->sentence(),
            'price'         => fake()->randomFloat(2, 10000, 500000),
            // Stok awal 0: stok diisi lewat transaksi IN di seeder agar konsisten dengan histori.
            'stock'         => 0,
            'minimum_stock' => fake()->numberBetween(3, 15),
        ];
    }
}
