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
        Schema::create('final_scores', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('guru_id');
            $table->unsignedBigInteger('periode_id');

            $table->decimal('nilai_kepala_sekolah', 5, 2)->nullable();
            $table->decimal('nilai_rekan_guru', 5, 2)->nullable();
            $table->decimal('nilai_wali_murid', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();

            $table->enum('rekomendasi', ['promosi', 'pelatihan', 'pembinaan', 'evaluasi'])->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_scores');
    }
};
