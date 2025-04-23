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

        Produk::factory()->count(10)->create()->each(function ($produk) {
            $jenis = fake()->randomElement(['Virginia', 'Mole', 'Srinthil', 'Garangan']);
            $produk->update([
                'nama_produk' => 'Tembakau ' . $jenis,
                'deskripsi' => 'Tembakau Aseli ' . $jenis,
                'harga' => fake()->randomFloat(0, 150000, 300000),
                'stok' => fake()->numberBetween(100, 200),
            ]);
        });
    }
}
