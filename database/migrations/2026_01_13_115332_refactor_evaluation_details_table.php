<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Truncate first to be safe
        DB::table('evaluation_details')->truncate();

        Schema::table('evaluation_details', function (Blueprint $table) {
            
            if (Schema::hasColumn('evaluation_details', 'kpi_indicator_id')) {
                 // Try dropping FK if it exists, use raw SQL to be safe if Schema builder fails
                 // But we previously saw it doesn't exist.
                 $table->dropColumn('kpi_indicator_id');
            }
            
            if (!Schema::hasColumn('evaluation_details', 'kpi_question_id')) {
                $table->unsignedBigInteger('kpi_question_id')->after('evaluation_id');
                $table->foreign('kpi_question_id')->references('id')->on('kpi_questions')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('evaluation_details')->truncate();

        Schema::table('evaluation_details', function (Blueprint $table) {
            if (Schema::hasColumn('evaluation_details', 'kpi_question_id')) {
                $table->dropForeign(['kpi_question_id']);
                $table->dropColumn('kpi_question_id');
            }

            if (!Schema::hasColumn('evaluation_details', 'kpi_indicator_id')) {
                $table->unsignedBigInteger('kpi_indicator_id')->after('evaluation_id');
            }
        });
    }
};
