<?php

namespace App\Http\Controllers;

use App\Models\KpiIndicator;
use Illuminate\Http\Request;

class KpiIndicatorController extends Controller
{
    public function index()
    {
        $kpis = KpiIndicator::latest()->paginate(10);
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
            'deskripsi'   => 'nullable|string',
            'kategori'    => 'nullable|string|max:100',
            'bobot'       => 'required|numeric|min:0|max:100',
        ]);

        KpiIndicator::create($request->all());

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
            'deskripsi'   => 'nullable|string',
            'kategori'    => 'nullable|string|max:100',
            'bobot'       => 'required|numeric|min:0|max:100',
        ]);

        $kpi->update($request->all());

        return redirect()->route('kpi.index')->with('success', 'KPI berhasil diperbarui!');
    }

    public function destroy(KpiIndicator $kpi)
    {
        $kpi->delete();
        return redirect()->route('kpi.index')->with('success', 'KPI berhasil dihapus!');
    }
}
