<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('jenjang', ['PG/TK', 'SD', 'SMP'])->nullable()->default('SD')->after('role');
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->enum('jenjang', ['PG/TK', 'SD', 'SMP'])->default('SD')->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('jenjang');
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn('jenjang');
        });
    }
};
