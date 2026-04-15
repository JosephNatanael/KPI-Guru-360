<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Guru;
use App\Models\Period;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\KpiIndicator;
use App\Models\WaliMurid;
use Illuminate\Support\Facades\DB;

class AutoEvaluate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-evaluate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan penilaian otomatis utk semua guru yang belum dinilai.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Memulai proses penilaian otomatis...");
        
        $periode = Period::where('status', 'aktif')->first();

        if (!$periode) {
            $this->error("Tidak ada periode aktif.");
            return;
        }

        $users = User::all();
        $gurusAll = Guru::all();
        
        $countEvaluations = 0;

        foreach ($users as $user) {
            $eligibleGurus = collect();

            if ($user->role === 'admin') {
                $eligibleGurus = $gurusAll;
            } elseif ($user->role === 'kepala_sekolah') {
                $eligibleGurus = $gurusAll->where('jenjang', $user->jenjang);
            } elseif ($user->role === 'guru') {
                $currentGuruId = $user->guru->id ?? null;
                $eligibleGurus = $gurusAll->where('jenjang', $user->jenjang)
                    ->where('id', '!=', $currentGuruId);
            } elseif ($user->role === 'wali_murid') {
                $wali = WaliMurid::where('user_id', $user->id)->first();
                if ($wali) {
                    $guruWaliKelas = $gurusAll->where('is_wali_kelas', 1)
                        ->where('kelas', $wali->kelas)
                        ->first();
                    if ($guruWaliKelas) {
                        $eligibleGurus = collect([$guruWaliKelas]);
                    }
                }
            }
            
            // Get KPI questions specific to this user's role for this period
            $kpisWithQuestions = KpiIndicator::where('is_active', true)
                ->with(['questions' => function($query) use ($periode, $user) {
                    $query->where('periode_id', $periode->id)
                          ->where('role_penilai', $user->role);
                }])->get();

            $hasQuestions = false;
            foreach ($kpisWithQuestions as $kpi) {
                if ($kpi->questions->count() > 0) {
                    $hasQuestions = true;
                    break;
                }
            }

            if (!$hasQuestions) {
                // If this role has no questions for this period, they can't evaluate anything
                continue;
            }

            foreach ($eligibleGurus as $guru) {
                // Cek apakah user sudah menilai guru ini
                // Wali murid dibatasi 1 saja walau guru wali kelas sama, tapi dari eligibleGurus tadi kan cuma dapat 1.
                // Tetap dicek ke database biar aman.
                $exists = Evaluation::where('periode_id', $periode->id)
                    ->where('guru_id', $guru->id)
                    ->where('penilai_id', $user->id)
                    ->exists();

                if (!$exists) {
                    DB::beginTransaction();
                    try {
                        $evaluation = Evaluation::create([
                            'periode_id' => $periode->id,
                            'guru_id' => $guru->id,
                            'penilai_id' => $user->id,
                            'role_penilai' => $user->role,
                            'average_score' => 0
                        ]);

                        $totalKpiScore = 0;
                        $kpiCount = 0;

                        foreach ($kpisWithQuestions as $kpi) {
                            if ($kpi->questions->count() == 0) continue;

                            $qScores = [];
                            foreach ($kpi->questions as $question) {
                                $val = rand(3, 5);
                                $qScores[] = $val;

                                EvaluationDetail::create([
                                    'evaluation_id' => $evaluation->id,
                                    'kpi_question_id' => $question->id,
                                    'nilai' => $val,
                                ]);
                            }

                            if (!empty($qScores)) {
                                $kpiAvg = array_sum($qScores) / count($qScores);
                                $totalKpiScore += $kpiAvg;
                                $kpiCount++;
                            }
                        }

                        if ($kpiCount > 0) {
                            $evaluation->average_score = round($totalKpiScore / $kpiCount, 2);
                            $evaluation->save();
                            $this->line("Evaluasi dibuat - Penilai ID: {$user->id} ({$user->role}) menilai Guru ID: {$guru->id}");
                            $countEvaluations++;
                        } else {
                            DB::rollBack();
                            continue;
                        }

                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error("Error membuat evaluasi untuk Penilai ID: {$user->id} ke Guru ID: {$guru->id} - " . $e->getMessage());
                    }
                }
            }
        }
        
        $this->info("Proses selesai. Sebanyak {$countEvaluations} penilaian dummy berhasil dibuat.");
    }
}
