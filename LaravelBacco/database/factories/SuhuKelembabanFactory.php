<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuhuKelembaban>
 */
class SuhuKelembabanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'suhu' => $this->faker->numberBetween(0, 40), // Suhu dalam derajat Celcius
            'kelembaban' => $this->faker->numberBetween(0, 80), // Kelembaban dalam persen
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
