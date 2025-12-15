<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Period;
use App\Models\Evaluation;
use App\Models\FinalScore;
use App\Models\EvaluatorWeight;
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

        foreach ($gurus as $guru) {

            // 2️⃣ Ambil rata-rata nilai per evaluator
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

            // 6️⃣ Hitung nilai akhir (360°)
            $nilaiAkhir =
                ($nilaiKepsek * $bobot->kepala_sekolah / 100) +
                ($nilaiRekan  * $bobot->rekan_guru     / 100) +
                ($nilaiWali   * $bobot->wali_murid     / 100);

            // 7️⃣ Tentukan rekomendasi otomatis
            if ($nilaiAkhir >= 85) {
                $rekomendasi = 'promosi';
            } elseif ($nilaiAkhir >= 70) {
                $rekomendasi = 'pelatihan';
            } elseif ($nilaiAkhir >= 55) {
                $rekomendasi = 'evaluasi';
            } else {
                $rekomendasi = 'pembinaan';
            }

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
