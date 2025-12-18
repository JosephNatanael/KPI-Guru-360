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
    Route::get('/dashboard-guru', [DashboardController::class, 'dashboardGuru'])
        ->name('dashboard.guru');

    /*
    |--------------------------------------------------------------------------
    | ROUTE KHUSUS KEPALA SEKOLAH
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:kepala_sekolah')->group(function () {

        // Dashboard kepala sekolah
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Riwayat penilaian (list guru + periode)
        Route::get(
            '/riwayat-penilaian',
            [RiwayatPenilaianController::class, 'index']
        )->name('riwayat.penilaian');

        // ✅ DETAIL RIWAYAT PENILAIAN (GURU + PERIODE)
        Route::get(
            '/riwayat-penilaian/{guru}/{periode}',
            [RiwayatPenilaianController::class, 'detail']
        )->name('riwayat.penilaian.detail');

        Route::get(
            '/riwayat-penilaian/{guru}/{periode}/penilai',
            [RiwayatPenilaianController::class, 'riwayatPenilai']
        )->name('riwayat.penilaian.penilai');

        // Master rekomendasi hanya boleh diatur kepala sekolah
        Route::resource('recommendations', RecommendationController::class)
            ->except(['show']);

    });

    /*
    |--------------------------------------------------------------------------
    | ROUTE UMUM (SESUSAI HAK AKSES)
    |--------------------------------------------------------------------------
    */
    Route::resource('guru', GuruController::class);
    Route::resource('user', UserController::class);
    Route::resource('wali-murid', WaliMuridController::class);
    Route::resource('kpi', KpiIndicatorController::class);
    Route::resource('kpi-questions', KpiQuestionController::class)->except(['show']);
    Route::resource('period', PeriodController::class);
    Route::resource('weights', EvaluatorWeightController::class);

    /*
    |--------------------------------------------------------------------------
    | PENILAIAN
    |--------------------------------------------------------------------------
    */
    Route::get('evaluation', [EvaluationController::class, 'index'])
        ->name('evaluation.index');

    Route::get('evaluation/pilih-guru', [EvaluationController::class, 'pilihGuru'])
        ->name('evaluation.pilih-guru');

    Route::get('evaluation/{guru_id}/create', [EvaluationController::class, 'create'])
        ->name('evaluation.create');

    Route::post('evaluation/{guru_id}', [EvaluationController::class, 'store'])
        ->name('evaluation.store');

    /*
    |--------------------------------------------------------------------------
    | FINAL SCORE
    |--------------------------------------------------------------------------
    */
    Route::get('/finalscore/hitung', [FinalScoreController::class, 'hitung'])
        ->name('finalscore.hitung');

    Route::get('/finalscore', [FinalScoreController::class, 'index'])
        ->name('finalscore.index');

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
