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
        Schema::table('final_scores', function (Blueprint $table) {
            $table->foreignId('recommendation_id')->nullable()->constrained('recommendations')->onDelete('set null')->after('nilai_akhir');
            // Make 'rekomendasi' nullable if it isn't already, or just leave it for now.
            // We want to keep it temporarily or null it out?
            // Let's just add the ID for now.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_scores', function (Blueprint $table) {
            $table->dropForeign(['recommendation_id']);
            $table->dropColumn('recommendation_id');
        });
    }
};
