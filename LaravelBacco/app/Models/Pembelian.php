<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        "produk_id",
        "jumlah",
        "harga"
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class,'produk_id');
    }
}
