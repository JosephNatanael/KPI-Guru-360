<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Period;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\KpiIndicator;
use App\Models\FinalScore;
use App\Models\EvaluatorWeight;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class FinalScoreController extends Controller
{
    /**
     * Hitung nilai akhir kinerja guru (360°)
     */
    public function hitung()
    {
        // 1️⃣ Ambil periode aktif
        $periode = Period::where('status', 'aktif')->first();

        if (!$periode) {
            return back()->with('error', 'Tidak ada periode penilaian aktif.');
        }

        // Ambil guru yang sudah dinilai di periode ini saja
        $gurus = Guru::whereHas('evaluations', function ($q) use ($periode) {
            $q->where('periode_id', $periode->id);
        })->get();

        // Ambil semua indikator KPI yang aktif
        $indicators = KpiIndicator::where('is_active', true)->get();
        if ($indicators->isEmpty()) {
            return back()->with('error', 'Belum ada indikator KPI aktif, tidak bisa menghitung nilai akhir.');
        }

        foreach ($gurus as $guru) {

            // 2️⃣ Inisialisasi nilai per penilai (akan dihitung dengan rumus Σ(bobot × nilai / 5))
            $nilaiKepsek = 0;
            $nilaiRekan = 0;
            $nilaiWali  = 0;

            // 3️⃣ Tentukan jenis guru berdasarkan is_wali_kelas
            $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';

            // 4️⃣ Ambil bobot evaluator
            $bobot = EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();

            if (!$bobot) {
                continue; // skip jika bobot belum diset
            }

            // 5️⃣ Validasi total bobot = 100%
            $totalBobot =
                $bobot->kepala_sekolah +
                $bobot->rekan_guru +
                $bobot->wali_murid;

            if ($totalBobot != 100) {
                return back()->with(
                    'error',
                    'Total bobot evaluator harus 100% untuk jenis guru: ' . $jenisGuru
                );
            }

            // 6️⃣ Hitung nilai per indikator sesuai formula yang diminta
            $totalNilaiAkhir = 0;

            foreach ($indicators as $indicator) {
                // 1. Rata-rata nilai setiap penilai dari masing-masing indikator
                $avgKepsekIndikator = EvaluationDetail::whereHas('question', function($q) use ($indicator) {
                        $q->where('kpi_indicator_id', $indicator->id);
                    })
                    ->whereHas('evaluation', function ($q) use ($guru, $periode) {
                        $q->where('guru_id', $guru->id)
                          ->where('periode_id', $periode->id)
                          ->where('role_penilai', 'kepala_sekolah');
                    })
                    ->avg('nilai') ?? 0;

                $avgRekanIndikator = EvaluationDetail::whereHas('question', function($q) use ($indicator) {
                        $q->where('kpi_indicator_id', $indicator->id);
                    })
                    ->whereHas('evaluation', function ($q) use ($guru, $periode) {
                        $q->where('guru_id', $guru->id)
                          ->where('periode_id', $periode->id)
                          ->where('role_penilai', 'guru');
                    })
                    ->avg('nilai') ?? 0;

                $avgWaliIndikator = EvaluationDetail::whereHas('question', function($q) use ($indicator) {
                        $q->where('kpi_indicator_id', $indicator->id);
                    })
                    ->whereHas('evaluation', function ($q) use ($guru, $periode) {
                        $q->where('guru_id', $guru->id)
                          ->where('periode_id', $periode->id)
                          ->where('role_penilai', 'wali_murid');
                    })
                    ->avg('nilai') ?? 0;

                // 2b. Nilai akhir per penilai = Σ(bobot indikator × nilai penilai / 5)
                $nilaiKepsek += $indicator->bobot * ($avgKepsekIndikator / 5);
                $nilaiRekan  += $indicator->bobot * ($avgRekanIndikator / 5);
                $nilaiWali   += $indicator->bobot * ($avgWaliIndikator / 5);

                // 2. Rata-rata 360° setiap indikator
                $rata360Indikator =
                    ($avgKepsekIndikator * $bobot->kepala_sekolah / 100) +
                    ($avgRekanIndikator  * $bobot->rekan_guru     / 100) +
                    ($avgWaliIndikator   * $bobot->wali_murid     / 100);

                // 3. Nilai akhir indikator = bobot indikator * rata-rata 360 / 5
                // (skala nilai 1–5)
                $nilaiAkhirIndikator = $indicator->bobot * ($rata360Indikator / 5);

                // 4. Total nilai keseluruhan = jumlah nilai dari semua indikator
                $totalNilaiAkhir += $nilaiAkhirIndikator;
            }

            $nilaiAkhir = $totalNilaiAkhir;

            // 7️⃣ Tentukan rekomendasi otomatis berdasarkan master rekomendasi
            // FIX: Round dulu sebelum lookup agar konsisten (misal 89.996 -> 90.00 -> Masuk range 90-100)
            $nilaiAkhirRounded = round($nilaiAkhir, 2);

            $rec = Recommendation::where('min_score', '<=', $nilaiAkhirRounded)
                ->orderBy('min_score', 'desc')
                ->first();

            $recommendationId = $rec ? $rec->id : null;

            // 8️⃣ Simpan / update nilai akhir
            // SESUAI REQUEST: Nilai per role dikali dengan bobot persentase role tersebut
            $nilaiKepsekWeighted = $nilaiKepsek * ($bobot->kepala_sekolah / 100);
            $nilaiRekanWeighted  = $nilaiRekan * ($bobot->rekan_guru / 100);
            $nilaiWaliWeighted   = $nilaiWali * ($bobot->wali_murid / 100);

            FinalScore::updateOrCreate(
                [
                    'guru_id'    => $guru->id,
                    'periode_id' => $periode->id,
                ],
                [
                    'nilai_kepala_sekolah' => round($nilaiKepsekWeighted, 2),
                    'nilai_rekan_guru'     => round($nilaiRekanWeighted, 2),
                    'nilai_wali_murid'     => round($nilaiWaliWeighted, 2),
                    'nilai_akhir'          => $nilaiAkhirRounded,
                    'recommendation_id'    => $recommendationId,
                ]
            );
        }

        return back()->with('success', 'Perhitungan nilai akhir 360° berhasil dilakukan.');
    }

    /**
     * Tampilkan laporan nilai akhir
     */
    public function index(Request $request)
    {
        $periode = Period::where('status', 'aktif')->first();

        if (!$periode) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }

        $query = FinalScore::with(['guru', 'recommendation'])
            ->has('guru') // HANYA AMBIL YANG GURUNYA MASIH ADA
            ->where('periode_id', $periode->id);

        if ($request->has('rekomendasi')) {
            // Filter by nama rekomendasi via relationship
            $query->whereHas('recommendation', function($sub) use ($request) {
                $sub->where('nama', $request->rekomendasi);
            });
        }

        if ($request->has('filter') && $request->filter == 'berprestasi') {
             // Berprestasi: Nilai >= 90 (sesuai update sebelumnya)
             // Atau bisa juga cek rekomendasi nama penghargaan business logic
             $query->where('nilai_akhir', '>=', 90);
        }

        $scores = $query->get();

        $indicators = KpiIndicator::where('is_active', true)->get();

        foreach ($scores as $score) {
            $guru = $score->guru;
            $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';
            $bobotEvaluator = EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();

            $competencyStats = [];
            
            // Initialize competencies
            $competencies = ['pedagogik', 'kepribadian', 'sosial', 'profesional'];
            foreach($competencies as $c) {
                $competencyStats[$c] = ['total' => 0, 'count' => 0];
            }

            foreach ($indicators as $ind) {
                // Calc 360 for this indicator
                 $u = function($role) use ($guru, $periode, $ind) {
                    return EvaluationDetail::whereHas('question', function($q) use ($ind) {
                            $q->where('kpi_indicator_id', $ind->id);
                        })
                        ->whereHas('evaluation', function ($q) use ($guru, $periode, $role) {
                            $q->where('guru_id', $guru->id)
                              ->where('periode_id', $periode->id)
                              ->where('role_penilai', $role);
                        })
                        ->avg('nilai') ?? 0;
                };

                $avgKepsek = $u('kepala_sekolah');
                $avgGuru   = $u('guru');
                $avgWali   = $u('wali_murid');

                if ($bobotEvaluator) {
                    $avg360 = 
                        ($avgKepsek * $bobotEvaluator->kepala_sekolah / 100) +
                        ($avgGuru   * $bobotEvaluator->rekan_guru     / 100) +
                        ($avgWali   * $bobotEvaluator->wali_murid     / 100);
                } else {
                    $avg360 = 0;
                }
                
                // Hitung nilai akhir indikator (kontribusi)
                // Rumus: Bobot * (Nilai 360 / 5)
                $nilaiAkhirKontribusi = $ind->bobot * ($avg360 / 5);

                // Add to competency stats
                if (isset($competencyStats[$ind->kompetensi])) {
                    $competencyStats[$ind->kompetensi]['total'] += $nilaiAkhirKontribusi;
                    $competencyStats[$ind->kompetensi]['count']++;
                }
            }

            // Calculate final average per competency
            $finalCompetencyScores = [];
            foreach($competencyStats as $comp => $stat) {
                $finalCompetencyScores[$comp] = $stat['count'] > 0 ? round($stat['total'] / $stat['count'], 2) : 0;
            }
            $score->competency_scores = $finalCompetencyScores;
        }

        return view('finalscore.index', compact('scores', 'periode'));
    }
    /**
     * Tampilkan guru yang belum dinilai
     */
    public function unassessed()
    {
        $periode = Period::where('status', 'aktif')->first();
        if (!$periode) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }

        // Ambil ID guru yang sudah memiliki nilai akhir di periode ini
        $assessedGuruIds = FinalScore::where('periode_id', $periode->id)
            ->pluck('guru_id');

        // Ambil guru yang TIDAK ada di list yang sudah dinilai
        $gurus = Guru::whereNotIn('id', $assessedGuruIds)->get();

        return view('finalscore.unassessed', compact('gurus', 'periode'));
    }
}



