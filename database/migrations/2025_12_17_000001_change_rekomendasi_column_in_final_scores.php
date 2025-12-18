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
        Schema::table('final_scores', function (Blueprint $table) {
            // Ubah kolom enum menjadi string agar bisa menampung rekomendasi dinamis
            $table->string('rekomendasi', 191)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_scores', function (Blueprint $table) {
            // Kembalikan ke enum default jika diperlukan
            $table->enum('rekomendasi', ['promosi', 'pelatihan', 'pembinaan', 'evaluasi'])->nullable()->change();
        });
    }
};





