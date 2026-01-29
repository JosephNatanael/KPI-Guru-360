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
        Schema::table('kpi_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('periode_id')->after('kpi_indicator_id')->nullable();
            
            $table->foreign('periode_id')
                  ->references('id')
                  ->on('periods')
                  ->onDelete('cascade');
        });

        // Update existing questions to the first period (ID 1) as a baseline
        \DB::table('kpi_questions')->whereNull('periode_id')->update(['periode_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_questions', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });
    }
};
