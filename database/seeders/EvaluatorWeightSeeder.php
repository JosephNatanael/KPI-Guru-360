<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluatorWeightSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('evaluator_weights')->insert([
            [
                'jenis_guru' => 'wali_kelas',
                'kepala_sekolah' => 50,
                'rekan_guru' => 30,
                'wali_murid' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_guru' => 'non_wali_kelas',
                'kepala_sekolah' => 70,
                'rekan_guru' => 30,
                'wali_murid' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
