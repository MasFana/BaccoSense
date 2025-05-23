<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produk>
 */
class ProdukFactory extends Factory
{
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_produk' => fake()->word(),
            'deskripsi' => fake()->sentence(),
            'harga' => fake()->randomFloat(2, 1, 1000),
            'stok' => fake()->numberBetween(1, 100),
        ];
    }
}
