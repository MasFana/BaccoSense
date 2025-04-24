<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;
    protected $fillable = [
        "jumlah",
        "harga"
    ];

    public function produk(){
        return $this->belongsTo(Produk::class);
    }
}
