<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pembelian>
 */
class PembelianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomDate = fake()->dateTimeBetween('-1 year', 'now');
        return [
            'produk_id' => \App\Models\Produk::all()->random()->id,
            'jumlah' => fake()->numberBetween(1, 100),
            'harga' => fake()->numberBetween(10000, 1000000),
            'created_at' => $randomDate,
            'updated_at' => $randomDate,
        ];
    }
}
