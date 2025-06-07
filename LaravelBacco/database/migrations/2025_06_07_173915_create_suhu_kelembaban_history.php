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
        Schema::create('suhu_kelembaban_history', function (Blueprint $table) {
            $table->id();
            $table->decimal('suhu', 5, 2)->comment('Suhu dalam derajat Celsius');
            $table->decimal('kelembaban', 5, 2)->comment('Kelembaban dalam persentase');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suhu_kelembaban_history');
    }
};
