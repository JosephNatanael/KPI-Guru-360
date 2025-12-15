<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KpiIndicatorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kpi_indicators')->insert([
            ['nama' => 'Perencanaan Pembelajaran', 'kompetensi' => 'pedagogik', 'bobot' => 8],
            ['nama' => 'Pelaksanaan Pembelajaran', 'kompetensi' => 'pedagogik', 'bobot' => 8],
            ['nama' => 'Penilaian Pembelajaran', 'kompetensi' => 'pedagogik', 'bobot' => 8],
            ['nama' => 'Kepribadian yang Mantap', 'kompetensi' => 'kepribadian', 'bobot' => 8],
            ['nama' => 'Berakhlak Mulia', 'kompetensi' => 'kepribadian', 'bobot' => 6],
            ['nama' => 'Kemandirian dalam Bertugas', 'kompetensi' => 'kepribadian', 'bobot' => 6],
            ['nama' => 'Keteladanan', 'kompetensi' => 'kepribadian', 'bobot' => 6],
            ['nama' => 'Kemampuan Komunikasi Guru', 'kompetensi' => 'sosial', 'bobot' => 8],
            ['nama' => 'Hubungan Sosial dengan Rekan Guru', 'kompetensi' => 'sosial', 'bobot' => 6],
            ['nama' => 'Kemampuan Kerjasama', 'kompetensi' => 'sosial', 'bobot' => 6],
            ['nama' => 'Penguasaan Materi Pembelajaran', 'kompetensi' => 'profesional', 'bobot' => 8],
            ['nama' => 'Pengembangan Profesionalitas Berkelanjutan', 'kompetensi' => 'profesional', 'bobot' => 8],
            ['nama' => 'Pemanfaatan Teknologi Pembelajaran', 'kompetensi' => 'profesional', 'bobot' => 8],
        ]);
    }
}
