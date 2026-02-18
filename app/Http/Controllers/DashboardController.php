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
        
        // Cek request filter
    $periodeId = request('periode_id');
        if ($periodeId) {
            $periode = Period::find($periodeId);
        } else {
            $periode = Period::where('status', 'aktif')->first();
            // Jika tidak ada yang aktif, ambil yang terakhir
            if (!$periode) {
                $periode = Period::latest('tanggal_mulai')->first();
            }
        }

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
                'guruSudahDinilaiList' => [],
                'guruBelumDinilaiList' => [],
                'guruBerprestasiList' => [],
                'allPeriods' => [],
                'progressPercentage' => 0,
                'readyTeacherCount' => 0,
                'trendLabels' => [],
                'trendData' => [],
            ]);
        }

        // Semua guru
        $totalGuru = Guru::count();

        // Semua nilai akhir untuk periode aktif (Hanya yang GURUNYA MASIH ADA)
        $scores = FinalScore::with(['guru', 'recommendation'])
            ->has('guru')
            ->where('periode_id', $periode->id)
            ->get();

        // 0️⃣ Filter Periode & Trend Data
        $allPeriods = Period::orderBy('tanggal_mulai', 'desc')->get();
        
        // --- LOGIKA PROGRESS PENILAIAN ---
        // --- LOGIKA PROGRESS PENILAIAN (Task Based) ---
        
        // 1. Kepala Sekolah
        // Target: Menilai SEMUA guru aktif
        $jumlahKepsek = \App\Models\User::where('role','kepala_sekolah')->count();

$countKepsekTotal = $totalGuru * $jumlahKepsek;

$countKepsekDone = Evaluation::where('periode_id', $periode->id)
    ->where('role_penilai', 'kepala_sekolah')
    ->whereHas('guru')
    ->count(); // 1 kepala sekolah = 1 tugas

        
        // 2. Rekan Guru (360)
        // Target: Setiap guru menilai semua guru LAINNYA.
        // Total Tugas = Total Guru * (Total Guru - 1)
        // Jika cuma 1 guru, tugas = 0.
        $countGuruTotal = $totalGuru > 1 ? $totalGuru * ($totalGuru - 1) : 0;
        $countGuruDone = Evaluation::where('periode_id', $periode->id)
            ->where('role_penilai', 'guru')
            ->whereHas('guru')
            ->count(); // Tetap hitung total row, asumsi aplikasi prevent duplicate entry

        // 3. Wali Murid
        // Target: Setiap Wali Murid menilai 1 Guru (Wali Kelasnya)
        // Kita asumsikan jumlah tugas = jumlah akun wali murid aktif
        $totalWaliMurid = \App\Models\WaliMurid::count(); // Gunakan model WaliMurid
        $countWaliTotal = $totalWaliMurid;
        $countWaliDone = Evaluation::where('periode_id', $periode->id)
            ->where('role_penilai', 'wali_murid')
            ->whereHas('guru')
            ->distinct('penilai_id') // Satu wali murid hanya boleh dihitung 1x (jika sistem 1-to-1)
            ->count('penilai_id');
            
        // Hitung Persentase Per Role
        $progressKepsek = $countKepsekTotal > 0 ? round(($countKepsekDone / $countKepsekTotal) * 100) : ($countKepsekTotal == 0 ? 100 : 0);
        $progressGuru   = $countGuruTotal > 0   ? round(($countGuruDone / $countGuruTotal) * 100)   : ($countGuruTotal == 0 ? 100 : 0);
        $progressWali   = $countWaliTotal > 0   ? round(($countWaliDone / $countWaliTotal) * 100)   : ($countWaliTotal == 0 ? 100 : 0);

        // Hitung Overall Progress
        // Total Tasks
        $grandTotalTask = $countKepsekTotal + $countGuruTotal + $countWaliTotal;
        $grandTotalDone = $countKepsekDone + $countGuruDone + $countWaliDone;
        
        $progressPercentage = $grandTotalTask > 0 ? round(($grandTotalDone / $grandTotalTask) * 100) : 0;
        
        // Unused legacy counter, but kept if view needs it (logic changed to pure task count)
        $readyTeacherCount = 0; // Not used in new logic, but var might be needed by view if logic persists?
        // View uses readyTeacherCount? checked view: used in old progress bar text "X dari Y guru telah menerima...".
        // Current view: uses $progressPercentage only in top bar.
        // Detail progress bars use $countKepsekDone etc.

        // We can keep $readyTeacherCount logic SEPARATE if needed for "Guru yang sudah FINAL/COMPLETE" logic.
        // But user asked to calculate PROGRESS based on TASKS.
        // I will remove the old loop-based logic.

        // --- TREND DATA (Rata-rata Nilai per Periode) ---
        // Kita ambil data statistik periodik
        $trendLabels = [];
        $trendData = [];
        
        // Ambil data urut dari yang terlama ke terbaru untuk grafik
        // Ambil data urut dari yang terlama ke terbaru untuk grafik
        // HANYA ambil periode yang sudah ada FinalScore-nya (sudah pernah dinilai)
        $periodsAsc = Period::whereIn('id', FinalScore::select('periode_id')->distinct())
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        foreach ($periodsAsc as $p) {
             $avg = FinalScore::where('periode_id', $p->id)->avg('nilai_akhir');
             $trendLabels[] = $p->tahun_ajaran . ' ' . $p->semester;
             $trendData[]   = $avg ? round($avg, 2) : 0;
        }


        // 1️⃣ Ringkasan umum (PERSONALIZED CARDS)
        $isAdmin = $user->role === 'admin';
        
        // 1️⃣ Ringkasan umum (PERSONALIZED CARDS)
        $isAdmin = $user->role === 'admin';
        
        // Unified Logic: "Sudah Dinilai" = Assessed by ME (Logged-in User)
        // Admin also acts as an evaluator (if applicable) or sees their own input
        $MyEvaluations = Evaluation::with('guru')
            ->where('periode_id', $periode->id)
            ->where('penilai_id', $user->id) 
            ->get();
            
        $guruSudahDinilai = $MyEvaluations->count();
        
        // List for Modal - Personalized
        $guruSudahDinilaiList = $MyEvaluations->map(function($eval) {
            return [
                'nama' => $eval->guru->nama,
                'nilai_akhir' => $eval->average_score, // Show score given by User
            ];
        })->values();
        
        $guruIdsSudahDinilai = $MyEvaluations->pluck('guru_id')->toArray();

        $guruBelumDinilai = max(0, $totalGuru - $guruSudahDinilai);
        
        // List Belum Dinilai
        $guruBelumDinilaiList = Guru::whereNotIn('id', $guruIdsSudahDinilai)
            ->get()
            ->map(function($guru) {
                return [
                    'nama' => $guru->nama,
                    'kelas' => $guru->kelas
                ];
            })->values();
            
        // Rata-rata & Berprestasi tetap Global (Monitoring Sekolah)
        $rataRataNilai = $scores->avg('nilai_akhir') ?? 0;

        $jumlahGuruBerprestasi = $scores
            ->filter(function ($row) {
                return $row->nilai_akhir >= 90;
            })
            ->count();
            
        // List Berprestasi (Global)
        $guruBerprestasiList = $scores->filter(function ($row) {
            return $row->nilai_akhir >= 90;
        })->map(function($score) {
            return [
                'nama' => $score->guru->nama,
                'nilai_akhir' => $score->nilai_akhir,
                'rekomendasi' => $score->recommendation->nama ?? '-'
            ];
        })->values();

        // 3️⃣ Grafik rata-rata nilai per kompetensi (Global)
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

        // 5️⃣ INDICATOR PERFORMANCE SUMMARY (SCHOOL-WIDE)
        $indicatorPerformance = [];
        $strongestIndicator = null;
        $weakestIndicator = null;
        $maxPersentase = -1;
        $minPersentase = 101;
        
        foreach ($indicators as $ind) {
            // Calculate school-wide average 360° for this indicator
            $totalAvg360 = 0;
            $validTeacherCount = 0;
            
            foreach ($scores as $score) {
                $guru = $score->guru;
                $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';
                $bobotEvaluator = EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();
                
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
                
                $totalAvg360 += $avg360;
                $validTeacherCount++;
            }
            
            $schoolAvg360 = $validTeacherCount > 0 ? $totalAvg360 / $validTeacherCount : 0;
            $persentaseKinerja = ($schoolAvg360 / 5) * 100;
            $nilaiKontribusi = ($schoolAvg360 / 5) * $ind->bobot;
            
            if ($persentaseKinerja >= 90) {
                $kategori = 'Sangat Baik';
                $kategoriIcon = '⭐';
                $kategoriClass = 'success';
            } elseif ($persentaseKinerja >= 80) {
                $kategori = 'Baik';
                $kategoriIcon = '✅';
                $kategoriClass = 'primary';
            } elseif ($persentaseKinerja > 50) {
                $kategori = 'Cukup';
                $kategoriIcon = '⚠';
                $kategoriClass = 'warning';
            } else {
                $kategori = 'Kurang';
                $kategoriIcon = '❌';
                $kategoriClass = 'danger';
            }
            
            $indicatorData = [
                'nama' => $ind->nama,
                'bobot' => $ind->bobot,
                'nilai_kontribusi' => round($nilaiKontribusi, 2),
                'persentase_kinerja' => round($persentaseKinerja, 2),
                'kategori' => $kategori,
                'kategori_icon' => $kategoriIcon,
                'kategori_class' => $kategoriClass,
            ];
            
            $indicatorPerformance[] = $indicatorData;
            
            if ($persentaseKinerja > $maxPersentase) {
                $maxPersentase = $persentaseKinerja;
                $strongestIndicator = $indicatorData;
            }
            if ($persentaseKinerja < $minPersentase) {
                $minPersentase = $persentaseKinerja;
                $weakestIndicator = $indicatorData;
            }
        }


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
            // Filter & Trend & Progress
            'allPeriods' => $allPeriods,
            'progressPercentage' => $progressPercentage,
            'readyTeacherCount' => $readyTeacherCount,
            // Detail Progress
            'progressKepsek' => $progressKepsek, 'countKepsekDone' => $countKepsekDone, 'countKepsekTotal' => $countKepsekTotal,
            'progressGuru'   => $progressGuru,   'countGuruDone'   => $countGuruDone,   'countGuruTotal'   => $countGuruTotal,
            'progressWali'   => $progressWali,   'countWaliDone'   => $countWaliDone,   'countWaliTotal'   => $countWaliTotal,
            
            'trendLabels' => $trendLabels,
            'trendData' => $trendData,
            'isAdmin' => $isAdmin,
            
            // Indicator Performance
            'indicatorPerformance' => $indicatorPerformance,
            'strongestIndicator' => $strongestIndicator,
            'weakestIndicator' => $weakestIndicator,
            'scores' => $scores,
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
        
        // Ambil semua periode untuk filter
        $allPeriods = Period::orderBy('id', 'desc')->get();
        
        // Tentukan periode yang dipilih (dari input atau default aktif)
        $periodeId = request('periode_id');
        $periode = $periodeId 
            ? Period::find($periodeId) 
            : Period::where('status', 'aktif')->first();

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
                if ($nilaiPersentase >= 90) {
                    $kategoriKinerja = 'Sangat Baik';
                } elseif ($nilaiPersentase >= 80) {
                    $kategoriKinerja = 'Baik';
                } elseif ($nilaiPersentase >= 51) {
                    $kategoriKinerja = 'Cukup';
                } else {
                    $kategoriKinerja = 'Perlu Perbaikan';
                }
            }
        }

        // 3️⃣ Grafik Persentase Kinerja Per Indikator (Ganti Ringkasan Kompetensi)
        $indicatorPerformance = [];
        
        if ($periode) {
            $indicators = KpiIndicator::where('is_active', true)->get();
            $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';
            $bobotEvaluator = EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();
            
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
                
                // Persentase Kinerja (Avg360 / 5 * 100)
                $persentaseKinerja = ($avg360 / 5) * 100;

                $indicatorPerformance[] = [
                    'nama' => $ind->nama,
                    'persentase' => round($persentaseKinerja, 2),
                    'kompetensi' => ucfirst($ind->kompetensi)
                ];
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
        
        // 6️⃣ Statistik Penilaian Sejawat (Sebagai Penilai)
        $rekanSudahDinilaiCount = 0;
        $rekanBelumDinilaiCount = 0;
        $rekanSudahDinilaiList = [];
        $rekanBelumDinilaiList = [];
        
        if ($periode) {
            // Guru menilai rekan sejawat (kecuali diri sendiri)
            // Total Rekan = Total Guru - 1 (Diri sendiri)
            $totalRekan = max(0, Guru::count() - 1);
            
            // Yang sudah dinilai oleh user ini
            $myEvaluations = Evaluation::with('guru')
                ->where('periode_id', $periode->id)
                ->where('penilai_id', $user->id)
                ->get();
            
            $rekanSudahDinilaiCount = $myEvaluations->count();
            $rekanBelumDinilaiCount = max(0, $totalRekan - $rekanSudahDinilaiCount);
            
            // Lists
            $rekanSudahDinilaiList = $myEvaluations->map(function($eval) {
                return [
                    'nama' => $eval->guru->nama,
                    'nilai' => $eval->average_score
                ];
            });
            
            $evaluatedIds = $myEvaluations->pluck('guru_id')->toArray();
            $rekanBelumDinilaiList = Guru::where('id', '!=', $guru->id)
                ->whereNotIn('id', $evaluatedIds)
                ->get()
                ->map(function($g) {
                    return [
                        'nama' => $g->nama,
                        'kelas' => $g->kelas
                    ];
                });
        }

        return view('dashboard.guru', compact(
            'guru',
            'periode',
            'statusPenilaian',
            'nilaiPersentase',
            'kategoriKinerja',
            'rekomendasi',
            'rekomendasi',
            'indicatorPerformance',
            'nilaiKepalaSekolah',
            'nilaiRekanGuru',
            'nilaiWaliMurid',
            'jumlahKepalaSekolah',
            'jumlahRekanGuru',
            'jumlahWaliMurid',
            'riwayatPenilaian',
            // Data Penilai
            'rekanSudahDinilaiCount',
            'rekanBelumDinilaiCount',
            'rekanSudahDinilaiList',
            'rekanBelumDinilaiList',
            'allPeriods'
        ));
    }
}
