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
        Schema::create('kpi_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kpi_indicator_id');
            $table->string('pertanyaan');
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();

            $table->foreign('kpi_indicator_id')
                  ->references('id')->on('kpi_indicators')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_questions');
    }
};
