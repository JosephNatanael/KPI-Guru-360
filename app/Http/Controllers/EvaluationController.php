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

        // Ambil ID guru yang SUDAH dinilai
        $evaluatedGuruIds = $evaluations->pluck('guru_id')->toArray();

        // Tentukan guru yang BOLEH dinilai berdasarkan role
        $eligibleGurus = collect();

        if ($user->role === 'admin') {
            $eligibleGurus = Guru::all();
        } elseif ($user->role === 'kepala_sekolah') {
            $eligibleGurus = Guru::where('jenjang', $user->jenjang)->get();
        } elseif ($user->role === 'guru') {
            // Guru menilai rekan sejawat (kecuali diri sendiri, dan dalam jenjang yang sama)
            $currentGuruId = $user->guru->id ?? null;
            $eligibleGurus = Guru::where('jenjang', $user->jenjang)
                ->where('id', '!=', $currentGuruId)
                ->get();
        } elseif ($user->role === 'wali_murid') {
            // Wali murid hanya menilai wali kelas anaknya
            $wali = WaliMurid::where('user_id', $user->id)->first();
            if ($wali) {
                $guruWaliKelas = Guru::where('is_wali_kelas', 1)
                    ->where('kelas', $wali->kelas)
                    ->first();
                if ($guruWaliKelas) {
                    $eligibleGurus = collect([$guruWaliKelas]);
                }
            }
        }

        // Filter guru yang BELUM dinilai
        $unevaluatedGurus = $eligibleGurus->whereNotIn('id', $evaluatedGuruIds);

        return view('evaluation.index', compact('evaluations', 'periode', 'unevaluatedGurus'));
    }

    public function pilihGuru()
    {
        $user = Auth::user();
        $periode = Period::where('status', 'aktif')->first();

        if (!$periode) {
            return back()->with('error', 'Belum ada periode aktif.');
        }

        $evaluatedGuruIds = Evaluation::where('penilai_id', $user->id)
            ->where('periode_id', $periode->id)
            ->pluck('guru_id')
            ->toArray();

        // Admin → boleh menilai semua guru
        if ($user->role === 'admin') {
            $gurus = Guru::all();
            return view('evaluation.pilih-guru', compact('gurus', 'periode', 'evaluatedGuruIds'));
        }

        // Kepala sekolah → boleh menilai guru di jenjangnya
        if ($user->role === 'kepala_sekolah') {
            $gurus = Guru::where('jenjang', $user->jenjang)->get();
            return view('evaluation.pilih-guru', compact('gurus', 'periode', 'evaluatedGuruIds'));
        }

        // Guru → peer review (tidak boleh menilai diri sendiri, dalam jenjang yang sama)
        if ($user->role === 'guru') {
            $currentGuruId = $user->guru->id ?? null;
            $gurus = Guru::where('jenjang', $user->jenjang)
                ->where('id', '!=', $currentGuruId)
                ->get();
            return view('evaluation.pilih-guru', compact('gurus', 'periode', 'evaluatedGuruIds'));
        }

        // Wali murid → langsung diarahkan ke wali kelas anaknya
        if ($user->role === 'wali_murid') {
            
            // Check duplicate check first
            $existing = Evaluation::where('periode_id', $periode->id)
                ->where('penilai_id', $user->id) 
                ->exists();

            if($existing) {
                 return back()->with('error', 'Anda sudah melakukan penilaian untuk periode ini.');
            }

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

        $userRole = Auth::user()->role;
        // Cek KPI aktif dengan pertanyaan-pertanyaannya untuk periode ini dan role penilai ini
        $kpis = KpiIndicator::where('is_active', true)
            ->with(['questions' => function($query) use ($periode, $userRole) {
                $query->where('periode_id', $periode->id)
                      ->where('role_penilai', $userRole)
                      ->orderBy('urutan', 'asc');
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

        // Cek duplicate
        $exists = Evaluation::where('periode_id', $periode->id)
            ->where('guru_id', $guru_id)
            ->where('penilai_id', $user->id)
            ->exists();
        
        if ($exists) {
            return redirect()->route('evaluation.index')
                ->with('error', 'Anda sudah menilai guru ini pada periode aktif.');
        }

        // Ambil semua KPI aktif dengan pertanyaan-pertanyaannya untuk periode ini untuk validasi
        $kpis = KpiIndicator::where('is_active', true)
            ->with(['questions' => function($query) use ($periode, $user) {
                $query->where('periode_id', $periode->id)
                      ->where('role_penilai', $user->role);
            }])->get();
        
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
                    $val = (int)$request->nilai[$key];
                    $questionScores[] = $val;

                    // Simpan nilai per pertanyaan ke evaluation_details
                    EvaluationDetail::create([
                        'evaluation_id' => $evaluation->id,
                        'kpi_question_id' => $question->id,
                        'nilai' => $val,
                    ]);
                }
            }
            
            // Hitung rata-rata nilai untuk KPI ini (untuk perhitungan average_score)
            if (!empty($questionScores)) {
                $kpiAverage = array_sum($questionScores) / count($questionScores);
                
                // Sebelumnya menyimpan rata-rata per KPI, sekarang sudah disimpan per pertanyaan detail di atas.
                
                $total += $kpiAverage;
                $count++;
            }
        }

        // Simpan average score
        $evaluation->average_score = $count > 0 ? round($total / $count, 2) : 0;
        $evaluation->save();

        return redirect()->route('evaluation.index')->with('success', 'Penilaian berhasil disimpan!');
    }

    public function edit($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        
        // Pastikan yang mengedit adalah pemilik penilaian
        if ($evaluation->penilai_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mengedit penilaian simulasi ini.');
        }

        $guru = $evaluation->guru;
        $periode = $evaluation->period; // Menggunakan relasi period di model Evaluation

        // Cek apakah periode masih aktif (opsional, tergantung rules bisnis)
        $activePeriod = Period::where('status', 'aktif')->first();
        if (!$activePeriod || $activePeriod->id !== $periode->id) {
             return back()->with('error', 'Hanya penilaian pada periode aktif yang dapat diubah.');
        }

        // Ambil KPI aktif dan pertanyaan untuk periode evaluasi ini
        $kpis = KpiIndicator::where('is_active', true)
            ->with(['questions' => function($query) use ($periode, $evaluation) {
                $query->where('periode_id', $periode->id)
                      ->where('role_penilai', $evaluation->role_penilai)
                      ->orderBy('urutan', 'asc');
            }])->get();

        // Ambil detail jawaban yang sudah ada
        // Kita map supaya mudah diakses di view: $existingAnswers[question_id] = nilai
        $existingAnswers = $evaluation->details->pluck('nilai', 'kpi_question_id')->toArray();

        return view('evaluation.edit', compact('evaluation', 'guru', 'periode', 'kpis', 'existingAnswers'));
    }

    public function update(Request $request, $id)
    {
        $evaluation = Evaluation::findOrFail($id);

        if ($evaluation->penilai_id !== Auth::id()) {
             return back()->with('error', 'Unauthorized.');
        }
        
        // Validasi sama seperti store
        $kpis = KpiIndicator::where('is_active', true)
            ->with(['questions' => function($query) use ($evaluation) {
                $query->where('periode_id', $evaluation->periode_id)
                      ->where('role_penilai', $evaluation->role_penilai);
            }])->get();
        
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

        // Update detail penilaian
        $total = 0;
        $count = 0;

        foreach ($kpis as $kpi) {
            $questionScores = [];
            foreach ($kpi->questions as $question) {
                $key = 'question_' . $question->id;
                if (isset($request->nilai[$key])) {
                    $val = (int)$request->nilai[$key];
                    $questionScores[] = $val;

                    // Update or create detail
                    // Kita cari berdasarkan evaluation_id dan kpi_question_id
                    EvaluationDetail::updateOrCreate(
                        [
                            'evaluation_id' => $evaluation->id,
                            'kpi_question_id' => $question->id,
                        ],
                        [
                            'nilai' => $val
                        ]
                    );
                }
            }

            if (!empty($questionScores)) {
                 $kpiAverage = array_sum($questionScores) / count($questionScores);
                 $total += $kpiAverage;
                 $count++;
            }
        }

        // Update average score utama
        $evaluation->average_score = $count > 0 ? round($total / $count, 2) : 0;
        $evaluation->save();

        return redirect()->route('evaluation.index')->with('success', 'Penilaian berhasil diperbarui!');
    }
}
