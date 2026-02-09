<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Period;
use App\Models\FinalScore;
use App\Models\EvaluationDetail;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Cetak laporan rekap seluruh guru (PDF)
     */
    public function cetakSemua()
    {
        $periode = Period::where('status', 'aktif')->first();

        if (!$periode) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }

        $scores = FinalScore::with(['guru', 'recommendation'])
            ->has('guru')
            ->where('periode_id', $periode->id)
            ->get();

        // Statistik
        $totalGuru = $scores->count();
        $rataRataSekolah = $scores->avg('nilai_akhir');

        // Kategori (Simulasi map dari rekomendasi atau kategori kinerja)
        // Asumsi: Rekomendasi di DB sudah sesuai, atau kita map manual jika stringnya beda.
        // Berdasarkan request user: Penghargaan, Pelatihan, Pembinaan, Evaluasi
        
        $stats = [
            'Penghargaan' => 0,
            'Pelatihan'   => 0,
            'Pembinaan'   => 0,
            'Evaluasi'    => 0,
        ];

        foreach ($scores as $s) {
            // Kita coba cocokan rekomendasi dengan key di atas.
            // Jika rekomendasi stringnya "Diberikan Penghargaan", kita anggap masuk "Penghargaan"
            // Implementasi sederhana: string matching
            $rec = $s->recommendation->nama ?? ''; 
            
            if (stripos($rec, 'Penghargaan') !== false) {
                $stats['Penghargaan']++;
            } elseif (stripos($rec, 'Pelatihan') !== false) {
                $stats['Pelatihan']++;
            } elseif (stripos($rec, 'Pembinaan') !== false) {
                $stats['Pembinaan']++;
            } elseif (stripos($rec, 'Evaluasi') !== false) {
                $stats['Evaluasi']++;
            } else {
                // If unknown, maybe default or ignore? 
                // For now, let's just leave it.
                // Or maybe Map score to category directly if recommendation is empty?
                // But user requested "Jumlah guru per kategori".
            }
        }

        // --- Hitung Ringkasan Performa Per Indikator (School-Wide) ---
        $indicators = \App\Models\KpiIndicator::where('is_active', true)->get();
        $indicatorPerformance = [];

        foreach ($indicators as $ind) {
            $totalAvg360 = 0;
            $validTeacherCount = 0;

            foreach ($scores as $s) {
                $guru = $s->guru;
                $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';
                // Note: Optimization - could cache evaluator weights to avoid query in loop
                $bobotEvaluator = \App\Models\EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();

                // Calculate 360 for this teacher on this indicator
                // We need to re-calculate because it's not stored directly in FinalScore
                // Logic identical to DashboardController
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
            
            // Metrics
            $persentaseKinerja = ($schoolAvg360 / 5) * 100;
            $nilaiKontribusi = ($schoolAvg360 / 5) * $ind->bobot;

            // Category
            if ($persentaseKinerja >= 90) $kategori = 'Sangat Baik';
            elseif ($persentaseKinerja >= 80) $kategori = 'Baik';
            elseif ($persentaseKinerja > 50) $kategori = 'Cukup';
            else $kategori = 'Kurang';

            $indicatorPerformance[] = [
                'nama' => $ind->nama,
                'bobot' => $ind->bobot,
                'nilai_kontribusi' => $nilaiKontribusi,
                'persentase_kinerja' => $persentaseKinerja,
                'kategori' => $kategori
            ];
        }

        $pdf = Pdf::loadView('reports.pdf_all', compact('scores', 'periode', 'totalGuru', 'rataRataSekolah', 'stats', 'indicatorPerformance'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Rekap_KPI_' . str_replace('/', '-', $periode->tahun_ajaran) . '.pdf');
    }

    /**
     * Cetak laporan per guru (PDF)
     */
    public function cetakGuru($guru_id)
    {
        $periode = Period::where('status', 'aktif')->first();

        if (!$periode) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }

        $guru = Guru::findOrFail($guru_id);

        $finalScore = FinalScore::where('guru_id', $guru->id)
            ->where('periode_id', $periode->id)
            ->first();

        if (!$finalScore) {
            return back()->with('error', 'Belum ada nilai akhir untuk guru tersebut.');
        }

        // --- Detail Per Indikator & Kompetensi ---
        
        $indicators = \App\Models\KpiIndicator::where('is_active', true)->get();
        $jenisGuru = $guru->is_wali_kelas ? 'wali_kelas' : 'non_wali_kelas';
        $bobotEvaluator = \App\Models\EvaluatorWeight::where('jenis_guru', $jenisGuru)->first();

        // Siapkan array untuk menampung nilai detail per kompetensi
        // Structure: ['Pedagogik' => ['total_score' => 0, 'count' => 0], ...]
        $competencyStats = []; 
        $indicatorDetails = [];

        foreach ($indicators as $ind) {
            // Hitung rata-rata per role untuk indikator ini
            // Logic diambil mirip FinalScoreController tapi per indikator
            
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

            // Hitung nilai akhir indikator (360) - Average 0-5
            if ($bobotEvaluator) {
                $avg360 = 
                    ($avgKepsek * $bobotEvaluator->kepala_sekolah / 100) +
                    ($avgGuru   * $bobotEvaluator->rekan_guru     / 100) +
                    ($avgWali   * $bobotEvaluator->wali_murid     / 100);
            } else {
                $avg360 = 0;
            }

            // SESUAI REQUEST: Nilai yang ditampilkan adalah Nilai Akhir (Kontribusi)
            // Rumus: Bobot KPI * (Nilai 360 / 5)
            $nilaiAkhirKontribusi = $ind->bobot * ($avg360 / 5);

            // Simpan ke detail indikator
            $indicatorDetails[] = [
                'nama' => $ind->nama,
                'kompetensi' => $ind->kompetensi,
                'bobot' => $ind->bobot,
                'nilai_akhir' => $nilaiAkhirKontribusi 
            ];

            // Akumulasi skor kompetensi
            if (!isset($competencyStats[$ind->kompetensi])) {
                $competencyStats[$ind->kompetensi] = ['total' => 0, 'count' => 0, 'bobot_total' => 0];
            }
            
            // Gunakan rata-rata nilai akhir kontribusi (bukan scale 0-5)
            $competencyStats[$ind->kompetensi]['total'] += $nilaiAkhirKontribusi;
            $competencyStats[$ind->kompetensi]['count']++;
        }

        // Finalisasi nilai kompetensi (Rata-rata)
        $competencies = [];
        foreach ($competencyStats as $name => $stat) {
            $competencies[$name] = $stat['count'] > 0 ? ($stat['total'] / $stat['count']) : 0;
        }

        $pdf = Pdf::loadView('reports.pdf_individual', compact('guru', 'finalScore', 'periode', 'competencies', 'indicatorDetails', 'bobotEvaluator'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_KPI_' . str_replace(' ', '_', $guru->nama) . '.pdf');
    }
}
