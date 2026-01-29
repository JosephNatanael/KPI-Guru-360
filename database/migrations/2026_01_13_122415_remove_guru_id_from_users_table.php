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
        // Dynamic FK dropping
        $tableName = 'users';
        $columnName = 'guru_id';
        $dbName = DB::getDatabaseName();

        $fks = DB::select(
            "SELECT CONSTRAINT_NAME 
             FROM information_schema.KEY_COLUMN_USAGE 
             WHERE TABLE_SCHEMA = ? 
             AND TABLE_NAME = ? 
             AND COLUMN_NAME = ? 
             AND REFERENCED_TABLE_NAME IS NOT NULL", 
            [$dbName, $tableName, $columnName]
        );

        foreach ($fks as $fk) {
            $constraintName = $fk->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$constraintName}`");
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'guru_id')) {
                // Drop index if exists (usually has same name as FK or column)
                // We'll let dropColumn handle it usually, but sometimes index remains.
                // dropColumn should work now that FK is gone.
                $table->dropColumn('guru_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'guru_id')) {
                $table->unsignedBigInteger('guru_id')->nullable()->after('role');
            }
        });
    }
};
