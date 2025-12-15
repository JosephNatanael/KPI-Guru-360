<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {

            // Tambah kolom jika belum ada
            if (!Schema::hasColumn('periods', 'tahun_ajaran')) {
                $table->string('tahun_ajaran', 20)->after('id');
            }

            if (!Schema::hasColumn('periods', 'semester')) {
                $table->enum('semester', ['ganjil', 'genap'])->after('tahun_ajaran');
            }

            if (!Schema::hasColumn('periods', 'status')) {
                $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif')->after('semester');
            }

            // Tambahkan timestamps jika belum ada
            if (!Schema::hasColumn('periods', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (!Schema::hasColumn('periods', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {

            // Menghapus kolom hanya jika diperlukan
            if (Schema::hasColumn('periods', 'tahun_ajaran')) {
                $table->dropColumn('tahun_ajaran');
            }
            if (Schema::hasColumn('periods', 'semester')) {
                $table->dropColumn('semester');
            }
            if (Schema::hasColumn('periods', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('periods', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('periods', 'updated_at')) {
                $table->dropColumn('updated_at');
            }

        });
    }
};
