<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('state_iots', function (Blueprint $table) {
            $table->id();
            $table->integer('state_kosong_penyimpanan');
            $table->boolean('state_pemanas');
            $table->boolean('state_pendingin');
            $table->boolean('state_dehumideieir');
            $table->boolean('state_humidefier');
            $table->timestamps();
        });
        
        DB::table('state_iots')->insert([
            'state_kosong_penyimpanan' => 0,
            'state_pemanas' => false,
            'state_pendingin' => false,
            'state_dehumideieir' => false,
            'state_humidefier' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('state_iot');
    }
};
