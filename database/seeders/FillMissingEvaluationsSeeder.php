<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\FinalScore;
use App\Models\Guru;
use App\Models\KpiQuestion;
use App\Models\Period;
use App\Models\User;
use App\Models\WaliMurid;
use App\Models\EvaluatorWeight;
use App\Models\Recommendation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FillMissingEvaluationsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // =============================
            // 1. AMBIL PERIODE AKTIF
            // =============================
            $periode = Period::where('status', 'aktif')->first();

            if (!$periode) {
                $this->command->error('Tidak ada periode aktif!');
                return;
            }

            if (Period::where('status','aktif')->count() > 1) {
                $this->command->error('Terdapat lebih dari 1 periode aktif!');
                return;
            }

            // =============================
            // 2. AMBIL PERTANYAAN KPI PERIODE
            // =============================
            $questions = KpiQuestion::where('periode_id', $periode->id)->get();

            if ($questions->isEmpty()) {
                $this->command->error('Tidak ada pertanyaan KPI untuk periode aktif!');
                return;
            }

            // =============================
            // 3. AMBIL DATA MASTER
            // =============================
            $teachers = Guru::all();
            $kepsekUsers = User::where('role', 'kepala_sekolah')->get();
            $teachersUsers = User::where('role', 'guru')->whereHas('guru')->get();
            $parents = WaliMurid::with('user')->get();

            if ($teachers->isEmpty()) {
                $this->command->error('Tidak ada data guru!');
                return;
            }

            $this->command->info("Periode: {$periode->tahun_ajaran} - {$periode->semester}");
            $this->command->info("Total Guru: ".$teachers->count());

            $evaluationCount = 0;

            // =====================================================
            // A. PENILAIAN KEPALA SEKOLAH → MENILAI SEMUA GURU
            // =====================================================
            foreach ($kepsekUsers as $kepsek) {

                $this->command->info("Kepala Sekolah {$kepsek->name} menilai...");

                foreach ($teachers as $targetGuru) {

                    $exists = Evaluation::where([
                        'periode_id' => $periode->id,
                        'guru_id' => $targetGuru->id,
                        'penilai_id' => $kepsek->id
                    ])->exists();

                    if (!$exists) {
                        $this->createRandomEvaluation(
                            $periode->id,
                            $targetGuru->id,
                            $kepsek->id,
                            'kepala_sekolah',
                            $questions
                        );
                        $evaluationCount++;
                    }
                }
            }

            // =====================================================
            // B. PEER REVIEW → GURU MENILAI GURU LAIN
            // =====================================================
            $this->command->info("Peer Review Guru...");

            foreach ($teachersUsers as $penilaiUser) {

                $penilaiGuru = $penilaiUser->guru;

                foreach ($teachers as $targetGuru) {

                    // guru tidak menilai diri sendiri
                    if ($penilaiGuru->id == $targetGuru->id) {
                        continue;
                    }

                    $exists = Evaluation::where([
                        'periode_id' => $periode->id,
                        'guru_id' => $targetGuru->id,
                        'penilai_id' => $penilaiUser->id
                    ])->exists();

                    if (!$exists) {
                        $this->createRandomEvaluation(
                            $periode->id,
                            $targetGuru->id,
                            $penilaiUser->id,
                            'guru',
                            $questions
                        );
                        $evaluationCount++;
                    }
                }
            }

            // =====================================================
            // C. PENILAIAN WALI MURID → MENILAI WALI KELAS ANAK
            // =====================================================
            $this->command->info("Penilaian Wali Murid...");

            foreach ($parents as $parent) {

                if (!$parent->user) {
                    continue;
                }

                // cari wali kelas berdasarkan kelas anak (case insensitive)
                $targetGuru = Guru::where('is_wali_kelas', 1)
                    ->whereRaw('LOWER(kelas) = ?', [strtolower(trim($parent->kelas))])
                    ->first();

                if (!$targetGuru) {
                    $this->command->warn("Wali kelas untuk {$parent->kelas} tidak ditemukan.");
                    continue;
                }

                $exists = Evaluation::where([
                    'periode_id' => $periode->id,
                    'guru_id' => $targetGuru->id,
                    'penilai_id' => $parent->user_id
                ])->exists();

                if (!$exists) {
                    $this->createRandomEvaluation(
                        $periode->id,
                        $targetGuru->id,
                        $parent->user_id,
                        'wali_murid',
                        $questions
                    );
                    $evaluationCount++;
                }
            }

            $this->command->info("Total penilaian dibuat: {$evaluationCount}");

            // =====================================================
            // D. HITUNG FINAL SCORE
            // =====================================================
            $this->calculateFinalScores($periode->id);

            $this->command->info("Final Score berhasil dihitung.");
        });
    }

    // =====================================================
    // CREATE RANDOM EVALUATION
    // =====================================================
    private function createRandomEvaluation($periodeId, $guruId, $penilaiId, $role, $questions)
    {
        $evaluation = Evaluation::create([
            'periode_id' => $periodeId,
            'guru_id' => $guruId,
            'penilai_id' => $penilaiId,
            'role_penilai' => $role,
        ]);

        $totalScore = 0;

        foreach ($questions as $q) {

            $nilai = rand(3, 5);

            EvaluationDetail::create([
                'evaluation_id' => $evaluation->id,
                'kpi_question_id' => $q->id,
                'nilai' => $nilai,
            ]);

            $totalScore += $nilai;
        }

        $evaluation->update([
            'average_score' => $totalScore / $questions->count()
        ]);
    }

    // =====================================================
    // HITUNG FINAL SCORE 360°
    // =====================================================
    private function calculateFinalScores($periodeId)
    {
        $teachers = Guru::all();
        $recommendations = Recommendation::orderBy('min_score', 'desc')->get();

        foreach ($teachers as $guru) {

            $evaluations = Evaluation::where([
                'guru_id' => $guru->id,
                'periode_id' => $periodeId
            ])->get();

            if ($evaluations->isEmpty()) continue;

            $avgKS = $evaluations->where('role_penilai', 'kepala_sekolah')->avg('average_score') ?? 0;
            $avgRG = $evaluations->where('role_penilai', 'guru')->avg('average_score') ?? 0;
            $avgWM = $evaluations->where('role_penilai', 'wali_murid')->avg('average_score') ?? 0;

            $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';

            $weight = EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();
            if (!$weight) continue;

            $finalScoreValue =
                ($avgKS * $weight->kepala_sekolah / 100) +
                ($avgRG * $weight->rekan_guru / 100) +
                ($avgWM * $weight->wali_murid / 100);

            // convert ke persentase
            $persentase = ($finalScoreValue / 5) * 100;

            // cari rekomendasi
            $recId = null;
            foreach ($recommendations as $rec) {
                if ($persentase >= $rec->min_score) {
                    $recId = $rec->id;
                    break;
                }
            }

            FinalScore::updateOrCreate(
                [
                    'guru_id' => $guru->id,
                    'periode_id' => $periodeId,
                ],
                [
                    'nilai_kepala_sekolah' => $avgKS,
                    'nilai_rekan_guru' => $avgRG,
                    'nilai_wali_murid' => $avgWM,
                    'nilai_akhir' => $persentase,
                    'recommendation_id' => $recId,
                ]
            );
        }
    }
}
