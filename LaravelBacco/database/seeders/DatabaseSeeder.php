<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SuhuKelembaban;
use App\Models\Produk;
use App\Models\Penjualan;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Pembelian;
use App\Models\Inventaris;

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

        collect(['Virginia', 'Mole', 'Srinthil', 'Garangan'])->each(function ($jenis) {
            Produk::factory()->create([
                'nama_produk' => "Tembakau {$jenis}",
                'deskripsi' => "Tembakau Aseli {$jenis}",
                'harga' => fake()->randomFloat(0, 150000, 300000),
                'stok' => fake()->numberBetween(100, 200),
            ]);
        });

        Inventaris::factory(10)->create()->each(function ($inventaris) {
        });

        Penjualan::factory(100)->create()->each(function ($penjualan) {
            $penjualan->produk()->associate(Produk::inRandomOrder()->first());
            $penjualan->save();
        });

        Pembelian::factory(100)->create()->each(function ($pembelian) {
            $pembelian->produk()->associate(Produk::inRandomOrder()->first());
            $pembelian->save();
        });

        SuhuKelembaban::factory(10)->create()->each(function ($suhuKelembaban) {
            $suhuKelembaban->created_at = now()->subHours(rand(0, 10));
            $suhuKelembaban->save();
        });
    }
}
