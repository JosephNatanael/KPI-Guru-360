<?php

namespace App\Http\Controllers;

use App\Models\FinalScore;
use App\Models\Period;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $periode = Period::where('status', 'aktif')->first();

        if (!$periode) {
            return view('dashboard.index', [
                'periode' => null,
                'labels' => [],
                'scores' => [],
            ]);
        }

        $scores = FinalScore::with('guru')
            ->where('periode_id', $periode->id)
            ->get();

        // Untuk chart
        $labels = $scores->pluck('guru.nama');
        $nilai_akhir = $scores->pluck('nilai_akhir');

        return view('dashboard.index', [
            'periode' => $periode,
            'labels' => $labels,
            'scores' => $nilai_akhir,
        ]);
    }
}
