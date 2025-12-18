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

        $gurus = Guru::all();

        // Ambil semua indikator KPI
        $indicators = KpiIndicator::all();
        if ($indicators->isEmpty()) {
            return back()->with('error', 'Belum ada indikator KPI, tidak bisa menghitung nilai akhir.');
        }

        foreach ($gurus as $guru) {

            // 2️⃣ (Tambahan) Ambil rata-rata nilai per evaluator (semua indikator) – untuk informasi saja
            $nilaiKepsek = Evaluation::where([
                'guru_id'       => $guru->id,
                'periode_id'    => $periode->id,
                'role_penilai'  => 'kepala_sekolah',
            ])->avg('average_score') ?? 0;

            $nilaiRekan = Evaluation::where([
                'guru_id'       => $guru->id,
                'periode_id'    => $periode->id,
                'role_penilai'  => 'guru',
            ])->avg('average_score') ?? 0;

            $nilaiWali = Evaluation::where([
                'guru_id'       => $guru->id,
                'periode_id'    => $periode->id,
                'role_penilai'  => 'wali_murid',
            ])->avg('average_score') ?? 0;

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
                $avgKepsekIndikator = EvaluationDetail::where('kpi_indicator_id', $indicator->id)
                    ->whereHas('evaluation', function ($q) use ($guru, $periode) {
                        $q->where('guru_id', $guru->id)
                          ->where('periode_id', $periode->id)
                          ->where('role_penilai', 'kepala_sekolah');
                    })
                    ->avg('nilai') ?? 0;

                $avgRekanIndikator = EvaluationDetail::where('kpi_indicator_id', $indicator->id)
                    ->whereHas('evaluation', function ($q) use ($guru, $periode) {
                        $q->where('guru_id', $guru->id)
                          ->where('periode_id', $periode->id)
                          ->where('role_penilai', 'guru');
                    })
                    ->avg('nilai') ?? 0;

                $avgWaliIndikator = EvaluationDetail::where('kpi_indicator_id', $indicator->id)
                    ->whereHas('evaluation', function ($q) use ($guru, $periode) {
                        $q->where('guru_id', $guru->id)
                          ->where('periode_id', $periode->id)
                          ->where('role_penilai', 'wali_murid');
                    })
                    ->avg('nilai') ?? 0;

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
            $rec = Recommendation::where('min_score', '<=', $nilaiAkhir)
                ->where('max_score', '>=', $nilaiAkhir)
                ->orderBy('min_score', 'desc')
                ->first();

            $rekomendasi = $rec ? $rec->nama : null;

            // 8️⃣ Simpan / update nilai akhir
            FinalScore::updateOrCreate(
                [
                    'guru_id'    => $guru->id,
                    'periode_id' => $periode->id,
                ],
                [
                    'nilai_kepala_sekolah' => round($nilaiKepsek, 2),
                    'nilai_rekan_guru'     => round($nilaiRekan, 2),
                    'nilai_wali_murid'     => round($nilaiWali, 2),
                    'nilai_akhir'          => round($nilaiAkhir, 2),
                    'rekomendasi'          => $rekomendasi,
                ]
            );
        }

        return back()->with('success', 'Perhitungan nilai akhir 360° berhasil dilakukan.');
    }

    /**
     * Tampilkan laporan nilai akhir
     */
    public function index()
    {
        $periode = Period::where('status', 'aktif')->first();

        if (!$periode) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }

        $scores = FinalScore::with('guru')
            ->where('periode_id', $periode->id)
            ->get();

        return view('finalscore.index', compact('scores', 'periode'));
    }
}
