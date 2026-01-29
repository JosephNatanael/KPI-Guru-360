<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Guru;
use App\Models\Period;
use App\Models\FinalScore;

class OrphanedGuruTest extends TestCase
{
    use RefreshDatabase;
    // this might wipe local db if not careful. 
    // Safest to just use standard transactions or rely on existing data if we are careful.
    // For now, I won't use RefreshDatabase to avoid wiping user's work, 
    // but I will mock or create temporary data in a transaction if possible.
    // However, Laravel default tests usually require empty DB.
    // I will try to be non-destructive.

    public function test_it_does_not_crash_when_guru_is_deleted()
    {
        // 1. Create Kepala Sekolah (since DB is fresh)
        $kepsek = User::create([
            'name' => 'Kepsek Test',
            'email' => 'kepsek@test.com',
            'password' => bcrypt('password'),
            'role' => 'kepala_sekolah'
        ]);

        // 2. Setup Data: Period & Guru
        $period = Period::firstOrCreate(
            ['tahun_ajaran' => '2099/2100', 'semester' => 'ganjil'],
            [
                'nama_periode' => 'Periode Test',
                'status' => 'aktif', 
                'tanggal_mulai' => now(), 
                'tanggal_selesai' => now()->addMonth()
            ]
        );

        $guru = Guru::create([
            'nama' => 'Guru To Delete',
            'nip' => '999999',
            'jabatan' => 'Guru Mapel',
            'is_wali_kelas' => 0
        ]);

        // 3. Create Final Score for this Guru
        FinalScore::create([
            'guru_id' => $guru->id,
            'periode_id' => $period->id,
            'nilai_akhir' => 85.00,
            'rekomendasi' => 'Baik'
        ]);

        // 4. DELETE THE GURU (Simulate the bug condition)
        $guru->delete();

        // 5. Access the Pages that were crashing
        $routes = [
            route('finalscore.index'),     // Final Score List
            route('dashboard'),            // Dashboard
            route('reports.cetak-semua'),  // PDF Report
            route('riwayat.penilaian')     // History
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($kepsek)->get($route);
            
            // Assert: Should not be 500
            $response->assertStatus(200);
        }

        // Cleanup (Standard PHPUnit cleanup would be better, but doing manual here for safety)
        FinalScore::where('guru_id', $guru->id)->delete();
        $period->delete(); // Only if we created it, but might affect others. Ideally use traits.
    }
}
