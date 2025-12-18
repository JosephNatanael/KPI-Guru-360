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
        $riwayat = FinalScore::with(['guru', 'period'])
            ->orderBy('periode_id', 'desc')
            ->get();

        return view('riwayat_penilaian.index', compact('riwayat'));
    }

    /**
     * DETAIL RIWAYAT PER GURU & PERIODE
     */
    public function detail($guru_id, $periode_id)
    {
        $guru = Guru::findOrFail($guru_id);
        $periode = Period::findOrFail($periode_id);

        // 🔑 AMBIL SEMUA PENILAI (TANPA FILTER USER)
        $rekap = Evaluation::where('guru_id', $guru_id)
            ->where('periode_id', $periode_id)
            ->selectRaw('
                role_penilai,
                COUNT(*) as jumlah_penilai,
                ROUND(AVG(average_score), 2) as rata_rata
            ')
            ->groupBy('role_penilai')
            ->get();

        // OPTIONAL: daftar penilai detail
        $evaluations = Evaluation::with('penilai')
            ->where('guru_id', $guru_id)
            ->where('periode_id', $periode_id)
            ->get();

        // 📊 Rekap per indikator (sesuai tabel contoh)
        $indicators = KpiIndicator::all();

        $indikatorRekap = [];
        $totalNilaiAkhir = 0;

        // Tentukan jenis guru (wali_kelas / non_wali_kelas) untuk bobot evaluator
        $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';
        $bobot = EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();

        if ($bobot && $indicators->isNotEmpty()) {
            foreach ($indicators as $indicator) {
                // Rata-rata per role untuk indikator ini
                $avgKepsek = EvaluationDetail::where('kpi_indicator_id', $indicator->id)
                    ->whereHas('evaluation', function ($q) use ($guru_id, $periode_id) {
                        $q->where('guru_id', $guru_id)
                          ->where('periode_id', $periode_id)
                          ->where('role_penilai', 'kepala_sekolah');
                    })
                    ->avg('nilai') ?? 0;

                $avgRekan = EvaluationDetail::where('kpi_indicator_id', $indicator->id)
                    ->whereHas('evaluation', function ($q) use ($guru_id, $periode_id) {
                        $q->where('guru_id', $guru_id)
                          ->where('periode_id', $periode_id)
                          ->where('role_penilai', 'guru');
                    })
                    ->avg('nilai') ?? 0;

                $avgWali = EvaluationDetail::where('kpi_indicator_id', $indicator->id)
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
            'details.kpi'
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
