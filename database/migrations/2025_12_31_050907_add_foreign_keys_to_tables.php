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
        // 1. Cleanup Orphaned Records First (Crucial!)
        // Delete evaluations where guru_id does not exist in gurus table
        \DB::table('evaluations')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('gurus')
                      ->whereRaw('evaluations.guru_id = gurus.id');
            })
            ->delete();

        // Delete evaluations where periode_id does not exist
        \DB::table('evaluations')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('periods')
                      ->whereRaw('evaluations.periode_id = periods.id');
            })
            ->delete();

        // Delete final_scores where guru_id does not exist
        \DB::table('final_scores')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('gurus')
                      ->whereRaw('final_scores.guru_id = gurus.id');
            })
            ->delete();


        // 2. Add Foreign Keys
        Schema::table('evaluations', function (Blueprint $table) {
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('cascade');
            $table->foreign('periode_id')->references('id')->on('periods')->onDelete('cascade');
            // user (penilai) might be different, let's just do cascade for now or set null?
            // User requested robust handling. If user deleted, evaluation should probably go too.
            // But usually we want to keep history. However, for "safety" let's cascade.
            $table->foreign('penilai_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('final_scores', function (Blueprint $table) {
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('cascade');
            $table->foreign('periode_id')->references('id')->on('periods')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropForeign(['periode_id']);
            $table->dropForeign(['penilai_id']);
        });

        Schema::table('final_scores', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropForeign(['periode_id']);
        });
    }
};
