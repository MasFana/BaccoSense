<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SuhuKelembaban extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'suhu',
        'kelembaban',
        'created_at',
        'updated_at'
    ];
}
