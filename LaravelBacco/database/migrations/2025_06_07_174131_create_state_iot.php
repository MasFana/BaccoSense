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
        Schema::create('state_iot', function (Blueprint $table) {
            $table->id();
            $table->integer('state_kosong_penyimpanan');
            $table->boolean('state_pemanas');
            $table->boolean('state_pendingin');
            $table->boolean('state_dehumideieir');
            $table->boolean('state_humidefier');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('state_iot');
    }
};
