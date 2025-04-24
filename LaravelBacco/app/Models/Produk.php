<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'satuan',
    ];

    public function inventaris()
    {
        return $this->hasMany(Inventaris::class);
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}
