<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\User;
use App\Models\WaliMurid;
use App\Models\Period;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\FinalScore;
use App\Models\KpiIndicator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT DATA GURU (beberapa sebagai wali kelas)
        $gurus = [
            ['nama' => 'Budi Santoso', 'nip' => '196501011987031001', 'is_wali_kelas' => true, 'kelas' => '7A'],
            ['nama' => 'Siti Nurhaliza', 'nip' => '196502151988032002', 'is_wali_kelas' => true, 'kelas' => '7B'],
            ['nama' => 'Ahmad Fauzi', 'nip' => '196503201989031003', 'is_wali_kelas' => true, 'kelas' => '8A'],
            ['nama' => 'Dewi Sartika', 'nip' => '196504251990032004', 'is_wali_kelas' => true, 'kelas' => '8B'],
            ['nama' => 'Rudi Hartono', 'nip' => '196505301991031005', 'is_wali_kelas' => true, 'kelas' => '9A'],
            ['nama' => 'Maya Sari', 'nip' => '196506051992032006', 'is_wali_kelas' => true, 'kelas' => '9B'],
            ['nama' => 'Joko Widodo', 'nip' => '196507101993031007', 'is_wali_kelas' => false, 'kelas' => null],
            ['nama' => 'Indah Permata', 'nip' => '196508151994032008', 'is_wali_kelas' => false, 'kelas' => null],
            ['nama' => 'Bambang Sutrisno', 'nip' => '196509201995031009', 'is_wali_kelas' => false, 'kelas' => null],
            ['nama' => 'Ratna Dewi', 'nip' => '196510251996032010', 'is_wali_kelas' => false, 'kelas' => null],
        ];

        $guruIds = [];
        foreach ($gurus as $guruData) {
            $guru = Guru::create($guruData);
            $guruIds[] = $guru->id;
        }

        // 2. BUAT USER KEPALA SEKOLAH
        $kepsek = User::create([
            'name' => 'Dr. H. Muhammad Rizki, M.Pd',
            'email' => 'kepsek@sekolah.sch.id',
            'password' => Hash::make('kepsek123'),
            'role' => 'kepala_sekolah',
            'guru_id' => null,
        ]);

        // 3. BUAT USER GURU (terhubung ke data guru)
        $userGurus = [];
        foreach ($guruIds as $index => $guruId) {
            $userGuru = User::create([
                'name' => $gurus[$index]['nama'],
                'email' => strtolower(str_replace(' ', '', $gurus[$index]['nama'])) . '@guru.sch.id',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'guru_id' => $guruId,
            ]);
            $userGurus[$guruId] = $userGuru;
        }

        // 4. BUAT USER WALI MURID
        $waliMuridUsers = [];
        $waliMuridData = [
            ['name' => 'Ahmad Hidayat', 'email' => 'ahmad.hidayat@email.com', 'nama_anak' => 'Rizki Pratama', 'kelas' => '7A'],
            ['name' => 'Siti Aisyah', 'email' => 'siti.aisyah@email.com', 'nama_anak' => 'Putri Lestari', 'kelas' => '7A'],
            ['name' => 'Bambang Wijaya', 'email' => 'bambang.wijaya@email.com', 'nama_anak' => 'Dika Maulana', 'kelas' => '7B'],
            ['name' => 'Dewi Kusuma', 'email' => 'dewi.kusuma@email.com', 'nama_anak' => 'Sinta Dewi', 'kelas' => '7B'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko.prasetyo@email.com', 'nama_anak' => 'Ahmad Fauzan', 'kelas' => '8A'],
            ['name' => 'Fitri Handayani', 'email' => 'fitri.handayani@email.com', 'nama_anak' => 'Nadia Putri', 'kelas' => '8A'],
            ['name' => 'Gunawan Sari', 'email' => 'gunawan.sari@email.com', 'nama_anak' => 'Rizki Ramadhan', 'kelas' => '8B'],
            ['name' => 'Hani Kartika', 'email' => 'hani.kartika@email.com', 'nama_anak' => 'Salsa Bella', 'kelas' => '8B'],
            ['name' => 'Indra Kurniawan', 'email' => 'indra.kurniawan@email.com', 'nama_anak' => 'Fajar Nugroho', 'kelas' => '9A'],
            ['name' => 'Juli Astuti', 'email' => 'juli.astuti@email.com', 'nama_anak' => 'Lina Sari', 'kelas' => '9A'],
            ['name' => 'Kurniawan Setiawan', 'email' => 'kurniawan.setiawan@email.com', 'nama_anak' => 'Rizki Aditya', 'kelas' => '9B'],
            ['name' => 'Lina Marlina', 'email' => 'lina.marlina@email.com', 'nama_anak' => 'Putri Ayu', 'kelas' => '9B'],
        ];

        foreach ($waliMuridData as $wmData) {
            $userWali = User::create([
                'name' => $wmData['name'],
                'email' => $wmData['email'],
                'password' => Hash::make('walimurid123'),
                'role' => 'wali_murid',
                'guru_id' => null,
            ]);

            WaliMurid::create([
                'user_id' => $userWali->id,
                'nama' => $wmData['name'],
                'nama_anak' => $wmData['nama_anak'],
                'kelas' => $wmData['kelas'],
            ]);

            $waliMuridUsers[] = $userWali;
        }

        // 5. BUAT PERIODE
        $periode1 = Period::create([
            'tahun_ajaran' => '2023/2024',
            'semester' => 'ganjil',
            'tanggal_mulai' => Carbon::parse('2023-07-01'),
            'tanggal_selesai' => Carbon::parse('2023-12-31'),
            'status' => 'nonaktif',
        ]);

        $periode2 = Period::create([
            'tahun_ajaran' => '2023/2024',
            'semester' => 'genap',
            'tanggal_mulai' => Carbon::parse('2024-01-01'),
            'tanggal_selesai' => Carbon::parse('2024-06-30'),
            'status' => 'nonaktif',
        ]);

        $periodeAktif = Period::create([
            'tahun_ajaran' => '2024/2025',
            'semester' => 'ganjil',
            'tanggal_mulai' => Carbon::parse('2024-07-01'),
            'tanggal_selesai' => Carbon::parse('2024-12-31'),
            'status' => 'aktif',
        ]);

        // 6. BUAT EVALUATIONS & EVALUATION DETAILS
        $kpiIndicators = KpiIndicator::all();
        $periode = $periodeAktif;

        // Fungsi helper untuk membuat penilaian
        $createEvaluation = function($guruId, $penilaiId, $rolePenilai, $periodeId) use ($kpiIndicators) {
            $evaluation = Evaluation::create([
                'periode_id' => $periodeId,
                'guru_id' => $guruId,
                'penilai_id' => $penilaiId,
                'role_penilai' => $rolePenilai,
            ]);

            $total = 0;
            $count = 0;

            foreach ($kpiIndicators as $kpi) {
                // Nilai random antara 3-5 untuk data dummy yang masuk akal
                $nilai = rand(3, 5);
                EvaluationDetail::create([
                    'evaluation_id' => $evaluation->id,
                    'kpi_indicator_id' => $kpi->id,
                    'nilai' => $nilai,
                ]);
                $total += $nilai;
                $count++;
            }

            $evaluation->average_score = $count > 0 ? round($total / $count, 2) : 0;
            $evaluation->save();

            return $evaluation;
        };

        // Kepala Sekolah menilai semua guru (6 wali kelas + 4 non wali kelas)
        foreach ($guruIds as $guruId) {
            $createEvaluation($guruId, $kepsek->id, 'kepala_sekolah', $periode->id);
        }

        // Rekan Guru: setiap guru menilai 3-4 guru lain (peer review)
        foreach ($guruIds as $index => $guruId) {
            $guruLain = array_filter($guruIds, fn($id) => $id != $guruId);
            $guruLain = array_slice($guruLain, 0, rand(3, 4)); // Ambil 3-4 guru random
            
            foreach ($guruLain as $targetGuruId) {
                if (isset($userGurus[$guruId])) {
                    $createEvaluation($targetGuruId, $userGurus[$guruId]->id, 'guru', $periode->id);
                }
            }
        }

        // Wali Murid menilai wali kelas anaknya
        foreach ($waliMuridUsers as $index => $userWali) {
            $waliMurid = WaliMurid::where('user_id', $userWali->id)->first();
            if ($waliMurid) {
                // Cari guru wali kelas sesuai kelas anak
                $guruWaliKelas = Guru::where('is_wali_kelas', 1)
                    ->where('kelas', $waliMurid->kelas)
                    ->first();
                
                if ($guruWaliKelas) {
                    $createEvaluation($guruWaliKelas->id, $userWali->id, 'wali_murid', $periode->id);
                }
            }
        }

        // 7. BUAT FINAL SCORES untuk beberapa guru (hitung nilai akhir 360°)
        // Ambil beberapa guru wali kelas untuk dihitung final score-nya
        $guruWaliKelas = Guru::where('is_wali_kelas', 1)->take(3)->get();
        
        foreach ($guruWaliKelas as $guru) {
            // Ambil semua evaluations untuk guru ini di periode aktif
            $evaluations = Evaluation::where('guru_id', $guru->id)
                ->where('periode_id', $periode->id)
                ->get();

            $nilaiKepsek = $evaluations->where('role_penilai', 'kepala_sekolah')->avg('average_score');
            $nilaiRekan = $evaluations->where('role_penilai', 'guru')->avg('average_score');
            $nilaiWali = $evaluations->where('role_penilai', 'wali_murid')->avg('average_score');

            // Ambil bobot untuk wali kelas
            $weight = \App\Models\EvaluatorWeight::where('jenis_guru', 'wali_kelas')->first();
            
            if ($weight) {
                $nilaiAkhir = 0;
                if ($nilaiKepsek) {
                    $nilaiAkhir += ($nilaiKepsek * $weight->kepala_sekolah / 100);
                }
                if ($nilaiRekan) {
                    $nilaiAkhir += ($nilaiRekan * $weight->rekan_guru / 100);
                }
                if ($nilaiWali) {
                    $nilaiAkhir += ($nilaiWali * $weight->wali_murid / 100);
                }

                // Tentukan rekomendasi berdasarkan nilai akhir
                $rekomendasi = 'pembinaan';
                if ($nilaiAkhir >= 4.5) {
                    $rekomendasi = 'promosi';
                } elseif ($nilaiAkhir >= 4.0) {
                    $rekomendasi = 'pelatihan';
                } elseif ($nilaiAkhir < 3.5) {
                    $rekomendasi = 'evaluasi';
                }

                FinalScore::create([
                    'guru_id' => $guru->id,
                    'periode_id' => $periode->id,
                    'nilai_kepala_sekolah' => round($nilaiKepsek ?? 0, 2),
                    'nilai_rekan_guru' => round($nilaiRekan ?? 0, 2),
                    'nilai_wali_murid' => round($nilaiWali ?? 0, 2),
                    'nilai_akhir' => round($nilaiAkhir, 2),
                    'rekomendasi' => $rekomendasi,
                ]);
            }
        }

        $this->command->info('Data dummy berhasil dibuat!');
        $this->command->info('- ' . count($gurus) . ' Guru');
        $this->command->info('- 1 Kepala Sekolah');
        $this->command->info('- ' . count($waliMuridUsers) . ' Wali Murid');
        $this->command->info('- 3 Periode');
        $this->command->info('- Evaluations & Final Scores');
    }
}





