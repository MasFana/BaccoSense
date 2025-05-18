<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventaris>
 */
class InventarisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "produk_id" => fake()->numberBetween(1, 4),
            "jumlah" => fake()->numberBetween(1, 100),
            "is_rusak" => fake()->boolean(),
        ];
    }
}
