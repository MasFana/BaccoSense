<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventaris extends Model
{
    use HasFactory;

    protected $fillable = [
        "produk_id",
        "jumlah",
        "is_rusak"
    ];


    public function produk()
    {
        return $this->belongsTo(Produk::class,'produk_id');
    }

}
