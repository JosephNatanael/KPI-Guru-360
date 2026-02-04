<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\Guru;
use App\Models\Period;
use App\Models\FinalScore;
use App\Models\KpiIndicator;
use App\Models\EvaluatorWeight;
use Illuminate\Http\Request;

class RiwayatPenilaianController extends Controller
{
    /**
     * LIST RIWAYAT (SUMMARY)
     */
    public function index()
    {
        // Ambil periode aktif
        $activePeriod = Period::where('status', 'aktif')->first();

        if (!$activePeriod) {
            $riwayat = collect([]); // Kosongkan jika tidak ada periode aktif
        } else {
            $riwayat = FinalScore::with(['guru', 'period', 'recommendation'])
                ->has('guru')
                ->where('periode_id', $activePeriod->id) // Filter hanya periode aktif
                ->orderBy('nilai_akhir', 'desc') // Urutkan berdasarkan nilai tertinggi (opsional)
                ->get();
        }

        return view('riwayat_penilaian.index', compact('riwayat', 'activePeriod'));
    }

    /**
     * DETAIL RIWAYAT PER GURU & PERIODE
     */
    public function detail($guru_id, $periode_id)
    {
        $guru = Guru::findOrFail($guru_id);
        $periode = Period::findOrFail($periode_id);

        // 🔑 AMBIL SEMUA PENILAI (TANPA FILTER USER)
        // 🔑 AMBIL SEMUA PENILAI (TANPA FILTER USER)
        // (Moved after $evaluations processing to use calculated values)

        // OPTIONAL: daftar penilai detail (sertakan detail KPI untuk hitung nilai akhir per penilai)
        $evaluations = Evaluation::with(['penilai', 'details.question.kpi'])
            ->where('guru_id', $guru_id)
            ->where('periode_id', $periode_id)
            ->get();

        // Hitung nilai akhir per penilai: Σ(bobot indikator × nilai penilai / 5)
        $evaluations = $evaluations->map(function ($e) {
            $nilaiAkhir = 0;
            
            // Group details by Indicator ID to calculate average per indicator first
            $detailsByIndicator = $e->details->groupBy(function($detail) {
                return $detail->question->kpi_indicator_id ?? 0;
            });

            foreach ($detailsByIndicator as $indicatorId => $details) {
                // Ambil bobot dari indicator (ambil dari salah satu detail)
                $firstDetail = $details->first();
                $bobot = $firstDetail->question->kpi->bobot ?? 0;
                
                // Hitung rata-rata nilai pertanyaan untuk indikator ini
                $avgScore = $details->avg('nilai');
                
                // Tambahkan ke nilai akhir
                $nilaiAkhir += $bobot * ($avgScore / 5);
            }
            
            $e->nilai_akhir_penilai = round($nilaiAkhir, 2);
            return $e;
        });

        // ⚖️ AMBIL BOBOT PENILAI
        $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';
        $bobotEvaluator = EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();

        // 📊 REKAP PER ROLE (RATA-RATA NILAI AKHIR * BOBOT)
        $rekap = $evaluations->groupBy('role_penilai')->map(function ($group, $role) use ($bobotEvaluator) {
            $count = $group->count();
            $total = $group->sum('nilai_akhir_penilai');
            $avg = $count > 0 ? $total / $count : 0; // Don't round yet

            // Tentukan bobot berdasarkan role
            $persentaseBobot = 0;
            if ($bobotEvaluator) {
                switch ($role) {
                    case 'kepala_sekolah':
                        $persentaseBobot = $bobotEvaluator->kepala_sekolah;
                        break;
                    case 'guru': // rekan guru
                        $persentaseBobot = $bobotEvaluator->rekan_guru;
                        break;
                    case 'wali_murid':
                        $persentaseBobot = $bobotEvaluator->wali_murid;
                        break;
                }
            }

            // Hitung nilai akhir weighted
            $nilaiBerbobot = $avg * ($persentaseBobot / 100);

            return (object) [
                'role_penilai' => $role,
                'jumlah_penilai' => $count,
                'rata_rata' => round($nilaiBerbobot, 2)
            ];
        })->values();

        // 📊 Rekap per indikator (sesuai tabel contoh)
        $indicators = KpiIndicator::where('is_active', true)->get();

        $indikatorRekap = [];
        $totalNilaiAkhir = 0;

        // Note: $bobotEvaluator already retrieved above
        $bobot = $bobotEvaluator;


        if ($bobot && $indicators->isNotEmpty()) {
            foreach ($indicators as $indicator) {
                // Rata-rata per role untuk indikator ini
                // Gunakan whereHas('question') karena kpi_indicator_id tidak ada lagi di tabel details
                
                $avgKepsek = EvaluationDetail::whereHas('question', function($q) use ($indicator) {
                        $q->where('kpi_indicator_id', $indicator->id);
                    })
                    ->whereHas('evaluation', function ($q) use ($guru_id, $periode_id) {
                        $q->where('guru_id', $guru_id)
                          ->where('periode_id', $periode_id)
                          ->where('role_penilai', 'kepala_sekolah');
                    })
                    ->avg('nilai') ?? 0;

                $avgRekan = EvaluationDetail::whereHas('question', function($q) use ($indicator) {
                        $q->where('kpi_indicator_id', $indicator->id);
                    })
                    ->whereHas('evaluation', function ($q) use ($guru_id, $periode_id) {
                        $q->where('guru_id', $guru_id)
                          ->where('periode_id', $periode_id)
                          ->where('role_penilai', 'guru');
                    })
                    ->avg('nilai') ?? 0;

                $avgWali = EvaluationDetail::whereHas('question', function($q) use ($indicator) {
                        $q->where('kpi_indicator_id', $indicator->id);
                    })
                    ->whereHas('evaluation', function ($q) use ($guru_id, $periode_id) {
                        $q->where('guru_id', $guru_id)
                          ->where('periode_id', $periode_id)
                          ->where('role_penilai', 'wali_murid');
                    })
                    ->avg('nilai') ?? 0;

                // Rata-rata 360° untuk indikator ini
                $rata360 =
                    ($avgKepsek * $bobot->kepala_sekolah / 100) +
                    ($avgRekan  * $bobot->rekan_guru     / 100) +
                    ($avgWali   * $bobot->wali_murid     / 100);

                // Nilai akhir indikator = Bobot KPI × Rata-rata / 5
                $nilaiAkhirIndikator = $indicator->bobot * ($rata360 / 5);

                $totalNilaiAkhir += $nilaiAkhirIndikator;

                $indikatorRekap[] = [
                    'nama'        => $indicator->nama,
                    'bobot'       => $indicator->bobot,
                    'nilai_ks'    => round($avgKepsek, 2),
                    'nilai_rg'    => round($avgRekan, 2),
                    'nilai_wm'    => round($avgWali, 2),
                    'rata360'     => round($rata360, 2),
                    'nilai_akhir' => round($nilaiAkhirIndikator, 2),
                ];
            }
        }

        return view(
            'riwayat_penilaian.detail',
            compact('guru', 'periode', 'rekap', 'evaluations', 'indikatorRekap', 'totalNilaiAkhir')
        );
    }

    public function riwayatPenilai($guru_id, $periode_id)
    {
        $guru = Guru::findOrFail($guru_id);
        $periode = Period::findOrFail($periode_id);

        // Ambil SEMUA riwayat penilaian
        $evaluations = Evaluation::with([
            'penilai',
            'details.question.kpi'
        ])
            ->where('guru_id', $guru_id)
            ->where('periode_id', $periode_id)
            ->orderBy('role_penilai')
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'riwayat_penilaian.riwayat_penilai',
            compact('guru', 'periode', 'evaluations')
        );
    }
}
