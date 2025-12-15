<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluator_weights', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_guru', ['wali_kelas', 'non_wali_kelas']);
            $table->integer('kepala_sekolah');
            $table->integer('rekan_guru');
            $table->integer('wali_murid')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluator_weights');
    }
};
