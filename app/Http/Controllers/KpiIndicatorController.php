<?php

namespace App\Http\Controllers;

use App\Models\KpiIndicator;
use Illuminate\Http\Request;

class KpiIndicatorController extends Controller
{
    public function index()
    {
        // Tampilkan semua data KPI tanpa paginasi
        $kpis = KpiIndicator::orderBy('id', 'asc')->get();
        return view('kpi.index', compact('kpis'));
    }

    public function create()
    {
        return view('kpi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'kompetensi'  => 'required|in:pedagogik,kepribadian,sosial,profesional',
            'bobot'       => 'required|numeric|min:0|max:100',
        ]);

        KpiIndicator::create($request->only('nama', 'kompetensi', 'bobot'));

        return redirect()->route('kpi.index')->with('success', 'KPI berhasil ditambahkan!');
    }

    public function edit(KpiIndicator $kpi)
    {
        return view('kpi.edit', compact('kpi'));
    }

    public function update(Request $request, KpiIndicator $kpi)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'kompetensi'  => 'required|in:pedagogik,kepribadian,sosial,profesional',
            'bobot'       => 'required|numeric|min:0|max:100',
        ]);

        $kpi->update($request->only('nama', 'kompetensi', 'bobot'));

        return redirect()->route('kpi.index')->with('success', 'KPI berhasil diperbarui!');
    }

    public function destroy(KpiIndicator $kpi)
    {
        $kpi->delete();
        return redirect()->route('kpi.index')->with('success', 'KPI berhasil dihapus!');
    }
}
