<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Period;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\KpiIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Periode aktif
        $periode = Period::where('status', 'aktif')->first();

        if (!$periode) {
            return back()->with('error', 'Belum ada periode aktif.');
        }

        // Ambil semua penilaian yang dilakukan user
        $evaluations = Evaluation::with('guru')
            ->where('penilai_id', $user->id)
            ->where('periode_id', $periode->id)
            ->orderBy('id', 'DESC')
            ->get();

        return view('evaluation.index', compact('evaluations', 'periode'));
    }

    public function pilihGuru()
    {
        $user = Auth::user();
        $periode = Period::where('status', 'aktif')->first();

        // Kepala sekolah → boleh menilai semua guru
        if ($user->role === 'kepala_sekolah') {
            $gurus = Guru::all();
        }

        // Guru → peer review (tidak boleh menilai diri sendiri)
        elseif ($user->role === 'guru') {
            $gurus = Guru::where('id', '!=', $user->guru_id)->get();
        }

        // Wali murid → menilai wali kelas anaknya (jika ada data siswa)
        else {
            // jika belum ada struktur wali murid, untuk sementara:
            $gurus = Guru::where('is_wali_kelas', 1)->get();
        }

        return view('evaluation.pilih-guru', compact('gurus', 'periode'));
    }

    public function create($guru_id)
    {
        // Cek guru
        $guru = Guru::find($guru_id);
        if (!$guru) {
            return back()->with('error', 'Guru tidak ditemukan.');
        }

        // Cek periode aktif
        $periode = Period::where('status', 'aktif')->first();
        if (!$periode) {
            return redirect()->route('period.index')
                ->with('error', 'Tidak ada periode aktif. Silakan aktifkan periode terlebih dahulu.');
        }

        // Cek KPI
        $kpis = KpiIndicator::all();
        if ($kpis->isEmpty()) {
            return back()->with('error', 'Belum ada indikator KPI.');
        }

        return view('evaluation.form', compact('guru', 'kpis', 'periode'));
    }


    public function store(Request $request, $guru_id)
    {
        $request->validate([
            'nilai.*' => 'required|integer|min:1|max:5'
        ]);

        $user = Auth::user();
        $periode = Period::where('status', 'aktif')->first();
        if (!$periode) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }


        // Insert evaluation
        $evaluation = Evaluation::create([
            'periode_id' => $periode->id,
            'guru_id' => $guru_id,
            'penilai_id' => $user->id,
            'role_penilai' => $user->role,
        ]);

        // Insert detail KPI
        $total = 0;
        $count = 0;

        foreach ($request->nilai as $kpi_id => $nilai) {
            EvaluationDetail::create([
                'evaluation_id' => $evaluation->id,
                'kpi_indicator_id' => $kpi_id,
                'nilai' => $nilai,
            ]);
            $total += $nilai;
            $count++;
        }

        // Simpan average score
        $evaluation->average_score = $count > 0 ? $total / $count : 0;
        $evaluation->save();

        return redirect()->route('evaluation.index')->with('success', 'Penilaian berhasil disimpan!');
    }
}
