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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('periode_id');
            $table->unsignedBigInteger('guru_id');
            $table->unsignedBigInteger('penilai_id'); // user yg memberi nilai

            $table->enum('role_penilai', ['kepala_sekolah', 'rekan_guru', 'wali_murid']);
            $table->decimal('average_score', 5, 2)->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
