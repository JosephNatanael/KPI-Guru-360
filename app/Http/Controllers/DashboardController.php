<?php

namespace App\Http\Controllers;

use App\Models\FinalScore;
use App\Models\Guru;
use App\Models\EvaluationDetail;
use App\Models\KpiIndicator;
use App\Models\Period;
use App\Models\Recommendation;
use App\Models\Evaluation;
use App\Models\EvaluatorWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Redirect guru ke dashboard guru
        if ($user->role === 'guru') {
            return redirect()->route('dashboard.guru');
        }
        
        // Wali murid tidak memiliki dashboard khusus, bisa redirect ke evaluation atau tampilkan pesan
        if ($user->role === 'wali_murid') {
            return redirect()->route('evaluation.index')
                ->with('info', 'Dashboard khusus hanya untuk kepala sekolah dan guru. Silakan gunakan menu Penilaian untuk melakukan penilaian.');
        }
        
        // Hanya kepala sekolah dan admin yang bisa melihat dashboard ini
        if (!in_array($user->role, ['kepala_sekolah', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya kepala sekolah atau admin yang dapat mengakses dashboard ini.');
        }
        
        $periode = Period::where('status', 'aktif')->first();

        if (!$periode) {
            return view('dashboard.index', [
                'periode' => null,
                'totalGuru' => 0,
                'guruSudahDinilai' => 0,
                'guruBelumDinilai' => 0,
                'rataRataNilai' => 0,
                'jumlahGuruBerprestasi' => 0,
                'kompetensiLabels' => [],
                'kompetensiScores' => [],
                'kategoriLabels' => [],
                'kategoriCounts' => [],
                'recommendations' => [],
            ]);
        }

        // Semua guru
        $totalGuru = Guru::count();

        // Semua nilai akhir untuk periode aktif (Hanya yang GURUNYA MASIH ADA)
        $scores = FinalScore::with(['guru', 'recommendation'])
            ->has('guru')
            ->where('periode_id', $periode->id)
            ->get();
        


        // 1️⃣ Ringkasan umum
        $guruSudahDinilai = $scores->count();
        $guruBelumDinilai = max(0, $totalGuru - $guruSudahDinilai);
        $rataRataNilai = $scores->avg('nilai_akhir') ?? 0;

        // Anggap "guru berprestasi" = rekomendasi Penghargaan atau Promosi
        $jumlahGuruBerprestasi = $scores
            ->filter(function ($row) {
                return $row->nilai_akhir >= 90;
            })
            ->count();

        // 3️⃣ Grafik rata-rata nilai per kompetensi
        // Hitung rata-rata nilai (1–5) per kompetensi berdasarkan evaluation_details (hanya KPI aktif)
        // 3️⃣ Grafik rata-rata nilai per kompetensi
        // Hitung rata-rata nilai per kompetensi menggunakan logika "Nilai Akhir Kontribusi"
        // Agar konsisten dengan halaman FinalScore
        $indicators = KpiIndicator::where('is_active', true)->get();
        $globalCompetencyAccumulator = [
            'pedagogik' => 0, 
            'kepribadian' => 0, 
            'sosial' => 0, 
            'profesional' => 0
        ];
        $validTeacherCount = 0;

        foreach ($scores as $score) {
            $guru = $score->guru;
            $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';
            $bobotEvaluator = EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();

            $competencyStats = [];
            $competencies = ['pedagogik', 'kepribadian', 'sosial', 'profesional'];
            foreach($competencies as $c) {
                $competencyStats[$c] = ['total' => 0, 'count' => 0];
            }

            foreach ($indicators as $ind) {
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
                
                // Rumus: Bobot * (Nilai 360 / 5)
                $nilaiAkhirKontribusi = $ind->bobot * ($avg360 / 5);

                if (isset($competencyStats[$ind->kompetensi])) {
                    $competencyStats[$ind->kompetensi]['total'] += $nilaiAkhirKontribusi;
                    $competencyStats[$ind->kompetensi]['count']++;
                }
            }

            foreach($competencyStats as $comp => $stat) {
                $compScore = $stat['count'] > 0 ? $stat['total'] / $stat['count'] : 0;
                $globalCompetencyAccumulator[$comp] += $compScore;
            }
            $validTeacherCount++;
        }

        // Rata-rata sekolah per kompetensi
        $kompetensiLabels = [];
        $kompetensiScores = [];
        $kompetensiOrder = ['pedagogik', 'kepribadian', 'sosial', 'profesional'];
        
        foreach ($kompetensiOrder as $k) {
            $kompetensiLabels[] = ucfirst($k);
            $avg = $validTeacherCount > 0 ? $globalCompetencyAccumulator[$k] / $validTeacherCount : 0;
            $kompetensiScores[] = round($avg, 2);
        }

        // 4️⃣ Grafik kategori hasil penilaian (rekomendasi) - ambil dari tabel recommendations
        $recommendations = Recommendation::orderBy('min_score', 'desc')->get();
        $kategoriLabels = [];
        $kategoriCounts = [];

        foreach ($recommendations as $rec) {
            $kategoriLabels[] = $rec->nama;
            // Hitung jumlah guru dengan rekomendasi ini
            $count = $scores->filter(function ($row) use ($rec) {
                // Use relationship
                $recName = $row->recommendation->nama ?? '';
                return strtolower($recName) === strtolower($rec->nama);
            })->count();
            $kategoriCounts[] = $count;
        }

        // Data untuk Modal Popups
        $guruSudahDinilaiList = $scores->map(function($score) {
            return [
                'nama' => $score->guru->nama,
                // nip removed
                'nilai_akhir' => $score->nilai_akhir,
                'rekomendasi' => $score->recommendation->nama ?? '-'
            ];
        })->values();

        $guruIdsSudahDinilai = $scores->pluck('guru_id')->toArray();
        $guruBelumDinilaiList = Guru::whereNotIn('id', $guruIdsSudahDinilai)
            ->get()
            ->map(function($guru) {
                return [
                    'nama' => $guru->nama,
                    // nip removed
                    'kelas' => $guru->kelas // Adjusted to use 'kelas' instead of 'jabatan'
                ];
            })->values();

        $guruBerprestasiList = $scores->filter(function ($row) {
            return $row->nilai_akhir >= 90;
        })->map(function($score) {
            return [
                'nama' => $score->guru->nama,
                // nip removed
                'nilai_akhir' => $score->nilai_akhir,
                'rekomendasi' => $score->recommendation->nama ?? '-'
            ];
        })->values();

        return view('dashboard.index', [
            'periode' => $periode,
            'totalGuru' => $totalGuru,
            'guruSudahDinilai' => $guruSudahDinilai,
            'guruBelumDinilai' => $guruBelumDinilai,
            'rataRataNilai' => round($rataRataNilai, 2),
            'jumlahGuruBerprestasi' => $jumlahGuruBerprestasi,
            'kompetensiLabels' => $kompetensiLabels,
            'kompetensiScores' => $kompetensiScores,
            'kategoriLabels' => $kategoriLabels,
            'kategoriCounts' => $kategoriCounts,
            'recommendations' => $recommendations,
            // Data untuk Modal
            'guruSudahDinilaiList' => $guruSudahDinilaiList,
            'guruBelumDinilaiList' => $guruBelumDinilaiList,
            'guruBerprestasiList' => $guruBerprestasiList,
        ]);
    }

    /**
     * Dashboard khusus untuk guru
     */
    public function dashboardGuru()
    {
        $user = Auth::user();
        
        // Pastikan user adalah guru
        if ($user->role !== 'guru' || !$user->guru) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Hanya untuk guru.');
        }

        $guru = $user->guru;
        $periode = Period::where('status', 'aktif')->first();

        // 1️⃣ Informasi Umum
        // $jabatan removed
        $statusPenilaian = 'Belum Dinilai';
        
        // 2️⃣ Nilai Akhir Kinerja
        $finalScore = null;
        $nilaiPersentase = 0;
        $kategoriKinerja = '-';
        $rekomendasi = '-';
        
        if ($periode) {
            $finalScore = FinalScore::with('recommendation')
                ->where('guru_id', $guru->id)
                ->where('periode_id', $periode->id)
                ->first();
            
            if ($finalScore) {
                $statusPenilaian = 'Sudah Dinilai';
                $nilaiPersentase = round($finalScore->nilai_akhir, 2);
                $rekomendasi = $finalScore->recommendation->nama ?? '-';
                
                // Tentukan kategori kinerja berdasarkan nilai
                if ($nilaiPersentase >= 85) {
                    $kategoriKinerja = 'Sangat Baik';
                } elseif ($nilaiPersentase >= 70) {
                    $kategoriKinerja = 'Baik';
                } elseif ($nilaiPersentase >= 55) {
                    $kategoriKinerja = 'Cukup';
                } else {
                    $kategoriKinerja = 'Perlu Perbaikan';
                }
            }
        }

        // 3️⃣ Ringkasan Kompetensi (hanya KPI aktif)
        $kompetensiData = [
            'pedagogik' => 0,
            'kepribadian' => 0,
            'sosial' => 0,
            'profesional' => 0
        ];
        
        if ($periode) {
            $indicators = KpiIndicator::where('is_active', true)->get();
            $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';
            $bobotEvaluator = EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();
            
            $competencyStats = [];
            foreach(['pedagogik', 'kepribadian', 'sosial', 'profesional'] as $c) {
                $competencyStats[$c] = ['total' => 0, 'count' => 0];
            }

            foreach ($indicators as $ind) {
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
                
                // Rumus: Bobot * (Nilai 360 / 5)
                $nilaiAkhirKontribusi = $ind->bobot * ($avg360 / 5);

                if (isset($competencyStats[$ind->kompetensi])) {
                    $competencyStats[$ind->kompetensi]['total'] += $nilaiAkhirKontribusi;
                    $competencyStats[$ind->kompetensi]['count']++;
                }
            }

            foreach($competencyStats as $comp => $stat) {
                $kompetensiData[$comp] = $stat['count'] > 0 ? round($stat['total'] / $stat['count'], 2) : 0;
            }
        }

        // 4️⃣ Penilaian 360 Derajat
        $nilaiKepalaSekolah = 0;
        $nilaiRekanGuru = 0;
        $nilaiWaliMurid = 0;
        $jumlahKepalaSekolah = 0;
        $jumlahRekanGuru = 0;
        $jumlahWaliMurid = 0;
        
        if ($periode && $finalScore) {
            $nilaiKepalaSekolah = $finalScore->nilai_kepala_sekolah ?? 0;
            $nilaiRekanGuru = $finalScore->nilai_rekan_guru ?? 0;
            $nilaiWaliMurid = $finalScore->nilai_wali_murid ?? 0;
            
            // Hitung jumlah penilai

            $jumlahKepalaSekolah = Evaluation::where('guru_id', $guru->id)
                ->where('periode_id', $periode->id)
                ->where('role_penilai', 'kepala_sekolah')
                ->count();

            $jumlahRekanGuru = Evaluation::where('guru_id', $guru->id)
                ->where('periode_id', $periode->id)
                ->where('role_penilai', 'guru')
                ->count();
            
            $jumlahWaliMurid = Evaluation::where('guru_id', $guru->id)
                ->where('periode_id', $periode->id)
                ->where('role_penilai', 'wali_murid')
                ->count();
        }

        // 5️⃣ Riwayat Penilaian - Perbandingan dengan periode sebelumnya
        $riwayatPenilaian = [];
        if ($guru) {
            $riwayat = FinalScore::with(['period', 'recommendation'])
                ->where('guru_id', $guru->id)
                ->orderBy('periode_id', 'desc')
                ->limit(5)
                ->get();
            
            foreach ($riwayat as $r) {
                $riwayatPenilaian[] = [
                    'periode' => $r->period->tahun_ajaran . ' (' . ucfirst($r->period->semester) . ')',
                    'nilai' => round($r->nilai_akhir, 2),
                    'rekomendasi' => $r->recommendation->nama ?? '-',
                    'is_current' => $periode && $r->periode_id == $periode->id,
                ];
            }
        }

        return view('dashboard.guru', compact(
            'guru',
            'periode',
            'statusPenilaian',
            'nilaiPersentase',
            'kategoriKinerja',
            'rekomendasi',
            'kompetensiData',
            'nilaiKepalaSekolah',
            'nilaiRekanGuru',
            'nilaiWaliMurid',
            'jumlahKepalaSekolah',
            'jumlahRekanGuru',
            'jumlahWaliMurid',
            'riwayatPenilaian'
        ));
    }
}
