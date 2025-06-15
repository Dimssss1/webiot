<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('thresholds', function (Blueprint $table) {
            $table->id();
            $table->float('gas_threshold')->default(300); // contoh ppm gas limit
            $table->boolean('fire_threshold')->default(true); // api deteksi threshold (true/false)
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thresholds');
    }
};
