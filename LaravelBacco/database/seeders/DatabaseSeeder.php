<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Produk;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make($value = 'admin'),
        ]);

        Produk::factory(10)->create([
            'nama_produk' => 'Produk ' . fake()->word(),
            'deskripsi' => fake()->sentence(),
            'harga' => fake()->randomFloat(2, 1, 1000),
            'stok' => fake()->numberBetween(1, 100),
            'satuan' => fake()->word(),
        ]);
    }
}
