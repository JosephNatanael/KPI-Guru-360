<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Guru;
use App\Models\Period;
use App\Models\FinalScore;
use App\Models\User;

class ForeignKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_guru_cascades_delete_to_final_scores()
    {
        // 0. Create Kepsek (required for other logic, though maybe not for pure DB test, but safer)
        User::create([
             'name' => 'Kepsek Test',
             'email' => 'kepsek@test.com',
             'password' => bcrypt('password'),
             'role' => 'kepala_sekolah'
        ]);
 
        // 1. Setup Data
        $period = Period::firstOrCreate(
            ['tahun_ajaran' => '2099/2101', 'semester' => 'ganjil'],
            [
                'nama_periode' => 'Periode FK Test',
                'status' => 'aktif', 
                'tanggal_mulai' => now(), 
                'tanggal_selesai' => now()->addMonth()
            ]
        );

        $guru = Guru::create([
            'nama' => 'Guru FK Test',
            'nip' => '888888',
            'jabatan' => 'Guru FK',
            'is_wali_kelas' => 0
        ]);

        $score = FinalScore::create([
            'guru_id' => $guru->id,
            'periode_id' => $period->id,
            'nilai_akhir' => 90.00,
            'rekomendasi' => 'Promosi'
        ]);

        // Assert existence before delete
        $this->assertDatabaseHas('final_scores', ['id' => $score->id]);

        // 2. Perform Delete (Hard delete to trigger DB cascade)
        $guru->forceDelete();

        // 3. Assert Absence (Cascade worked)
        $this->assertDatabaseMissing('final_scores', ['id' => $score->id]);
    }
}
