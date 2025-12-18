<?php

namespace App\Http\Controllers;

use App\Models\FinalScore;
use App\Models\Guru;
use App\Models\EvaluationDetail;
use App\Models\KpiIndicator;
use App\Models\Period;
use App\Models\Recommendation;
use App\Models\Evaluation;
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
        
        // Hanya kepala sekolah yang bisa melihat dashboard ini
        if ($user->role !== 'kepala_sekolah') {
            abort(403, 'Akses ditolak. Hanya kepala sekolah yang dapat mengakses dashboard ini.');
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

        // Semua nilai akhir untuk periode aktif
        $scores = FinalScore::with('guru')
            ->where('periode_id', $periode->id)
            ->get();

        // 1️⃣ Ringkasan umum
        $guruSudahDinilai = $scores->count();
        $guruBelumDinilai = max(0, $totalGuru - $guruSudahDinilai);
        $rataRataNilai = $scores->avg('nilai_akhir') ?? 0;

        // Anggap "guru berprestasi" = rekomendasi Penghargaan atau Promosi
        $jumlahGuruBerprestasi = $scores
            ->filter(function ($row) {
                $r = strtolower($row->rekomendasi ?? '');
                return in_array($r, ['penghargaan', 'promosi']);
            })
            ->count();

        // 3️⃣ Grafik rata-rata nilai per kompetensi
        // Hitung rata-rata nilai (1–5) per kompetensi berdasarkan evaluation_details
        $kompetensiAgg = EvaluationDetail::query()
            ->selectRaw('kpi_indicators.kompetensi, AVG(evaluation_details.nilai) as rata_nilai')
            ->join('kpi_indicators', 'evaluation_details.kpi_indicator_id', '=', 'kpi_indicators.id')
            ->join('evaluations', 'evaluation_details.evaluation_id', '=', 'evaluations.id')
            ->where('evaluations.periode_id', $periode->id)
            ->groupBy('kpi_indicators.kompetensi')
            ->pluck('rata_nilai', 'kompetensi');

        // Pastikan urutan tetap (Pedagogik, Kepribadian, Sosial, Profesional)
        $kompetensiOrder = ['pedagogik', 'kepribadian', 'sosial', 'profesional'];
        $kompetensiLabels = [];
        $kompetensiScores = [];
        foreach ($kompetensiOrder as $k) {
            $kompetensiLabels[] = ucfirst($k);
            $kompetensiScores[] = round($kompetensiAgg[$k] ?? 0, 2);
        }

        // 4️⃣ Grafik kategori hasil penilaian (rekomendasi) - ambil dari tabel recommendations
        $recommendations = Recommendation::orderBy('min_score', 'desc')->get();
        $kategoriLabels = [];
        $kategoriCounts = [];

        foreach ($recommendations as $rec) {
            $kategoriLabels[] = $rec->nama;
            // Hitung jumlah guru dengan rekomendasi ini
            $count = $scores->filter(function ($row) use ($rec) {
                return strtolower($row->rekomendasi ?? '') === strtolower($rec->nama);
            })->count();
            $kategoriCounts[] = $count;
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
        ]);
    }

    /**
     * Dashboard khusus untuk guru
     */
    public function dashboardGuru()
    {
        $user = Auth::user();
        
        // Pastikan user adalah guru
        if ($user->role !== 'guru' || !$user->guru_id) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Hanya untuk guru.');
        }

        $guru = $user->guru;
        $periode = Period::where('status', 'aktif')->first();

        // 1️⃣ Informasi Umum
        $jabatan = $guru->is_wali_kelas ? 'Wali Kelas ' . $guru->kelas : 'Guru';
        $statusPenilaian = 'Belum Dinilai';
        
        // 2️⃣ Nilai Akhir Kinerja
        $finalScore = null;
        $nilaiPersentase = 0;
        $kategoriKinerja = '-';
        $rekomendasi = '-';
        
        if ($periode) {
            $finalScore = FinalScore::where('guru_id', $guru->id)
                ->where('periode_id', $periode->id)
                ->first();
            
            if ($finalScore) {
                $statusPenilaian = 'Sudah Dinilai';
                $nilaiPersentase = round($finalScore->nilai_akhir, 2);
                $rekomendasi = $finalScore->rekomendasi ?? '-';
                
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

        // 3️⃣ Ringkasan Kompetensi
        $kompetensiData = [];
        $kompetensiOrder = ['pedagogik', 'kepribadian', 'sosial', 'profesional'];
        
        if ($periode) {
            foreach ($kompetensiOrder as $komp) {
                $avgNilai = EvaluationDetail::query()
                    ->selectRaw('AVG(evaluation_details.nilai) as rata_nilai')
                    ->join('kpi_indicators', 'evaluation_details.kpi_indicator_id', '=', 'kpi_indicators.id')
                    ->join('evaluations', 'evaluation_details.evaluation_id', '=', 'evaluations.id')
                    ->where('evaluations.guru_id', $guru->id)
                    ->where('evaluations.periode_id', $periode->id)
                    ->where('kpi_indicators.kompetensi', $komp)
                    ->value('rata_nilai') ?? 0;
                
                $kompetensiData[$komp] = round($avgNilai, 2);
            }
        } else {
            foreach ($kompetensiOrder as $komp) {
                $kompetensiData[$komp] = 0;
            }
        }

        // 4️⃣ Penilaian 360 Derajat
        $nilaiKepalaSekolah = 0;
        $nilaiRekanGuru = 0;
        $nilaiWaliMurid = 0;
        $jumlahRekanGuru = 0;
        $jumlahWaliMurid = 0;
        
        if ($periode && $finalScore) {
            $nilaiKepalaSekolah = $finalScore->nilai_kepala_sekolah ?? 0;
            $nilaiRekanGuru = $finalScore->nilai_rekan_guru ?? 0;
            $nilaiWaliMurid = $finalScore->nilai_wali_murid ?? 0;
            
            // Hitung jumlah penilai
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
            $riwayat = FinalScore::with('period')
                ->where('guru_id', $guru->id)
                ->orderBy('periode_id', 'desc')
                ->limit(5)
                ->get();
            
            foreach ($riwayat as $r) {
                $riwayatPenilaian[] = [
                    'periode' => $r->period->tahun_ajaran . ' (' . ucfirst($r->period->semester) . ')',
                    'nilai' => round($r->nilai_akhir, 2),
                    'rekomendasi' => $r->rekomendasi ?? '-',
                    'is_current' => $periode && $r->periode_id == $periode->id,
                ];
            }
        }

        return view('dashboard.guru', compact(
            'guru',
            'jabatan',
            'periode',
            'statusPenilaian',
            'nilaiPersentase',
            'kategoriKinerja',
            'rekomendasi',
            'kompetensiData',
            'nilaiKepalaSekolah',
            'nilaiRekanGuru',
            'nilaiWaliMurid',
            'jumlahRekanGuru',
            'jumlahWaliMurid',
            'riwayatPenilaian'
        ));
    }
}
