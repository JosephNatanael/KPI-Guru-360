<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KpiIndicatorController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\EvaluatorWeightController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\FinalScoreController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WaliMuridController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RiwayatPenilaianController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\KpiQuestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| ROUTE PUBLIK (TANPA LOGIN)
|--------------------------------------------------------------------------
*/

// root → redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// login
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

/*
|--------------------------------------------------------------------------
| ROUTE PROTEKSI LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD KHUSUS GURU
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD KHUSUS GURU
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard-guru', [DashboardController::class, 'dashboardGuru'])
        ->name('dashboard.guru');

    /*
    |--------------------------------------------------------------------------
    | ADMIN & KEPALA SEKOLAH (Shared Dashboard & Reports & FinalScore)
    |--------------------------------------------------------------------------
    /*
    |--------------------------------------------------------------------------
    | ADMIN & KEPALA SEKOLAH (Shared Dashboard & Reports & FinalScore & History)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,kepala_sekolah')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Reports
        Route::get('/reports/cetak-semua', [ReportController::class, 'cetakSemua'])->name('reports.cetak-semua');
        Route::get('/reports/cetak-guru/{guru}', [ReportController::class, 'cetakGuru'])->name('reports.cetak-guru');

        // Final Score (Nilai Akhir)
        Route::get('/finalscore/hitung', [FinalScoreController::class, 'hitung'])->name('finalscore.hitung');
        Route::get('/finalscore/unassessed', [FinalScoreController::class, 'unassessed'])->name('finalscore.unassessed');
        Route::get('/finalscore', [FinalScoreController::class, 'index'])->name('finalscore.index');

        // Riwayat Penilaian
        Route::get('/riwayat-penilaian', [RiwayatPenilaianController::class, 'index'])->name('riwayat.penilaian');
        Route::get('/riwayat-penilaian/{guru}/{periode}', [RiwayatPenilaianController::class, 'detail'])->name('riwayat.penilaian.detail');
        Route::get('/riwayat-penilaian/{guru}/{periode}/penilai', [RiwayatPenilaianController::class, 'riwayatPenilai'])->name('riwayat.penilaian.penilai');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY (Master Data Management)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::resource('guru', GuruController::class);
        Route::resource('user', UserController::class);
        Route::resource('wali-murid', WaliMuridController::class);
        Route::resource('period', PeriodController::class);
        Route::resource('weights', EvaluatorWeightController::class);
        Route::resource('recommendations', RecommendationController::class)->except(['show']);

        // Pertanyaan KPI (Admin only)
        Route::post('kpi-questions/copy', [KpiQuestionController::class, 'copyQuestions'])->name('kpi-questions.copy');
        Route::resource('kpi-questions', KpiQuestionController::class)->except(['show']);
    });

    /*
    |--------------------------------------------------------------------------
    | KEPALA SEKOLAH ONLY (KPI Management)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:kepala_sekolah')->group(function () {
        Route::resource('kpi', KpiIndicatorController::class);
        Route::post('kpi/{kpi}/toggle-status', [KpiIndicatorController::class, 'toggleStatus'])->name('kpi.toggle-status');
    });

    /*
    |--------------------------------------------------------------------------
    | PENILAIAN (Guru, Kepsek, Wali Murid) - Admin BLOCKED
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:guru,kepala_sekolah,wali_murid')->group(function () {
        Route::get('evaluation', [EvaluationController::class, 'index'])->name('evaluation.index');
        Route::get('evaluation/pilih-guru', [EvaluationController::class, 'pilihGuru'])->name('evaluation.pilih-guru');
        Route::get('evaluation/{guru_id}/create', [EvaluationController::class, 'create'])->name('evaluation.create');
        Route::post('evaluation/{guru_id}', [EvaluationController::class, 'store'])->name('evaluation.store');
        
        // Edit Penilaian
        Route::get('evaluation/{id}/edit', [EvaluationController::class, 'edit'])->name('evaluation.edit');
        Route::put('evaluation/{id}', [EvaluationController::class, 'update'])->name('evaluation.update');
    });

    /*
    |--------------------------------------------------------------------------
    | USER PROFILE (All Authenticated)
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
