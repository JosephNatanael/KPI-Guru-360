<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_questions', function (Blueprint $table) {
            $table->enum('role_penilai', ['kepala_sekolah', 'guru', 'wali_murid'])
                  ->default('guru')
                  ->after('kpi_indicator_id');
        });
    }

    public function down(): void
    {
        Schema::table('kpi_questions', function (Blueprint $table) {
            $table->dropColumn('role_penilai');
        });
    }
};
