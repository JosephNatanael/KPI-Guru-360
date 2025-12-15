<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Guru;
use App\Models\Period;
use App\Models\FinalScore;
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

        return view(
            'riwayat_penilaian.detail',
            compact('guru', 'periode', 'rekap', 'evaluations')
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
