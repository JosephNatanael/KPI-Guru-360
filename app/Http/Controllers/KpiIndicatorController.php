<?php

namespace App\Http\Controllers;

use App\Models\KpiIndicator;
use Illuminate\Http\Request;

class KpiIndicatorController extends Controller
{
    public function index()
    {
        // Pisahkan KPI aktif dan nonaktif
        $activeKpis = KpiIndicator::where('is_active', true)->orderBy('id', 'asc')->get();
        $inactiveKpis = KpiIndicator::where('is_active', false)->orderBy('id', 'asc')->get();
        
        $totalBobot = $activeKpis->sum('bobot');
        if ($totalBobot != 100) {
            session()->now('warning', "Total bobot KPI aktif saat ini adalah {$totalBobot}%. Harap sesuaikan hingga mencapai 100%.");
        }

        return view('kpi.index', compact('activeKpis', 'inactiveKpis'));
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
            'is_active'   => 'nullable|boolean',
        ]);

        $data = $request->only('nama', 'kompetensi', 'bobot');
        $isActive = $request->has('is_active') ? true : false;
        
        // Cek total bobot jika diaktifkan
        if ($isActive) {
            $currentTotal = KpiIndicator::where('is_active', true)->sum('bobot');
            if (($currentTotal + $data['bobot']) > 100) {
                // Otomatis nonaktifkan
                $data['is_active'] = false;
                $msg = 'KPI berhasil ditambahkan, namun OTOMATIS DINONAKTIFKAN karena total bobot melebihi 100%.';
                KpiIndicator::create($data);
                return redirect()->route('kpi.index')->with('warning', $msg);
            }
        }
        
        $data['is_active'] = $isActive;
        KpiIndicator::create($data);

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
            'is_active'   => 'nullable|boolean',
        ]);

        $data = $request->only('nama', 'kompetensi', 'bobot');
        $isActive = $request->has('is_active') ? true : false;

        // Cek total bobot jika diaktifkan
        if ($isActive) {
            $currentTotal = KpiIndicator::where('is_active', true)
                ->where('id', '!=', $kpi->id) // exclude current kpi
                ->sum('bobot');
                
            if (($currentTotal + $data['bobot']) > 100) {
                 return back()->withInput()->with('error', "Gagal update! Total bobot akan menjadi " . ($currentTotal + $data['bobot']) . "%. Maksimal 100%. Silahkan kurangi bobot KPI lain atau nonaktifkan KPI ini.");
            }
        }

        $data['is_active'] = $isActive;
        $kpi->update($data);

        return redirect()->route('kpi.index')->with('success', 'KPI berhasil diperbarui!');
    }

    /**
     * Toggle status aktif/nonaktif KPI
     */
    public function toggleStatus(KpiIndicator $kpi)
    {
        // Jika mau mengaktifkan, cek dulu total bobot
        if (!$kpi->is_active) {
            $currentTotal = KpiIndicator::where('is_active', true)->sum('bobot');
            if (($currentTotal + $kpi->bobot) > 100) {
                return back()->with('error', "Gagal mengaktifkan! Total bobot akan menjadi " . ($currentTotal + $kpi->bobot) . "%. Harap kurangi bobot KPI lain terlebih dahulu.");
            }
        }

        $kpi->is_active = !$kpi->is_active;
        $kpi->save();

        $status = $kpi->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('kpi.index')->with('success', "KPI berhasil {$status}!");
    }

    public function destroy(KpiIndicator $kpi)
    {
        $kpi->delete();
        return redirect()->route('kpi.index')->with('success', 'KPI berhasil dihapus!');
    }
}
