<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Period;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\KpiIndicator;
use App\Models\KpiQuestion;
use App\Models\WaliMurid;
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

        if (!$periode) {
            return back()->with('error', 'Belum ada periode aktif.');
        }

        // Kepala sekolah → boleh menilai semua guru
        if ($user->role === 'kepala_sekolah') {
            $gurus = Guru::all();
            return view('evaluation.pilih-guru', compact('gurus', 'periode'));
        }

        // Guru → peer review (tidak boleh menilai diri sendiri)
        if ($user->role === 'guru') {
            $gurus = Guru::where('id', '!=', $user->guru_id)->get();
            return view('evaluation.pilih-guru', compact('gurus', 'periode'));
        }

        // Wali murid → langsung diarahkan ke wali kelas anaknya
        if ($user->role === 'wali_murid') {

            // Cari data wali murid berdasarkan user_id
            $wali = WaliMurid::where('user_id', $user->id)->first();

            if (!$wali) {
                return back()->with('error', 'Data wali murid tidak ditemukan. Silakan hubungi admin.');
            }

            // Cari guru wali kelas sesuai kelas anak
            $guruWaliKelas = Guru::where('is_wali_kelas', 1)
                ->where('kelas', $wali->kelas)
                ->first();

            if (!$guruWaliKelas) {
                return back()->with('error', 'Guru wali kelas untuk kelas ' . $wali->kelas . ' belum diatur.');
            }

            // Langsung arahkan ke form penilaian wali kelas tersebut
            return redirect()->route('evaluation.create', $guruWaliKelas->id);
        }

        // Role lain (jika ada) fallback: tidak boleh menilai
        return back()->with('error', 'Role Anda tidak memiliki akses untuk melakukan penilaian.');
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

        // Cek KPI dengan pertanyaan-pertanyaannya
        $kpis = KpiIndicator::with(['questions' => function($query) {
            $query->orderBy('urutan', 'asc');
        }])->get();
        
        if ($kpis->isEmpty()) {
            return back()->with('error', 'Belum ada indikator KPI.');
        }

        // Cek apakah semua KPI memiliki pertanyaan
        $kpisWithoutQuestions = $kpis->filter(function($kpi) {
            return $kpi->questions->isEmpty();
        });

        if ($kpisWithoutQuestions->isNotEmpty()) {
            return back()->with('error', 'Beberapa KPI belum memiliki pertanyaan. Silakan tambahkan pertanyaan terlebih dahulu di menu Pertanyaan KPI.');
        }

        return view('evaluation.form', compact('guru', 'kpis', 'periode'));
    }


    public function store(Request $request, $guru_id)
    {
        $user = Auth::user();
        $periode = Period::where('status', 'aktif')->first();
        if (!$periode) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }

        // Ambil semua KPI dengan pertanyaan-pertanyaannya untuk validasi
        $kpis = KpiIndicator::with('questions')->get();
        
        // Buat rules validasi dinamis untuk setiap pertanyaan
        // Struktur: nilai[question_1], nilai[question_2], dll
        $rules = [];
        foreach ($kpis as $kpi) {
            foreach ($kpi->questions as $question) {
                $key = 'nilai.question_' . $question->id;
                $rules[$key] = 'required|integer|min:1|max:5';
            }
        }

        $messages = [
            'required' => 'Semua pertanyaan harus diisi.',
            'integer' => 'Nilai harus berupa angka.',
            'min' => 'Nilai minimal adalah 1.',
            'max' => 'Nilai maksimal adalah 5.',
        ];

        $request->validate($rules, $messages);

        // Insert evaluation
        $evaluation = Evaluation::create([
            'periode_id' => $periode->id,
            'guru_id' => $guru_id,
            'penilai_id' => $user->id,
            'role_penilai' => $user->role,
        ]);

        // Hitung rata-rata nilai per KPI dari pertanyaan-pertanyaan
        $total = 0;
        $count = 0;

        foreach ($kpis as $kpi) {
            $questionScores = [];
            
            // Ambil nilai untuk setiap pertanyaan dalam KPI ini
            foreach ($kpi->questions as $question) {
                $key = 'question_' . $question->id;
                if (isset($request->nilai[$key])) {
                    $questionScores[] = (int)$request->nilai[$key];
                }
            }
            
            // Hitung rata-rata nilai untuk KPI ini
            if (!empty($questionScores)) {
                $kpiAverage = array_sum($questionScores) / count($questionScores);
                
                // Simpan nilai rata-rata KPI ke evaluation_details
                EvaluationDetail::create([
                    'evaluation_id' => $evaluation->id,
                    'kpi_indicator_id' => $kpi->id,
                    'nilai' => round($kpiAverage, 2),
                ]);
                
                $total += $kpiAverage;
                $count++;
            }
        }

        // Simpan average score
        $evaluation->average_score = $count > 0 ? round($total / $count, 2) : 0;
        $evaluation->save();

        return redirect()->route('evaluation.index')->with('success', 'Penilaian berhasil disimpan!');
    }
}
