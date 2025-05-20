<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use SoftDeletes;
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
        return $this->hasMany(Inventaris::class, 'produk_id');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'produk_id');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'produk_id');
    }
}
