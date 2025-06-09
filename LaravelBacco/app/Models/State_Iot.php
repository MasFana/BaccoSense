<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State_Iot extends Model
{
    protected $table = 'state_iots';
    protected $fillable = [
        'id',
        'state_kosong_penyimpanan',
        'state_pemanas',
        'state_pendingin',
        'state_dehumideieir',
        'state_humidefier',
        'created_at',
        'updated_at'
    ];

}
