<?php

namespace App\Http\Controllers;

use App\Models\Period;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function index()
    {
        $periods = Period::latest()->paginate(10);
        $activePeriod = Period::where('status', 'aktif')->first();
        return view('period.index', compact('periods', 'activePeriod'));
    }

    public function create()
    {
        return view('period.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran'     => 'required|string',
            'semester'         => 'required|in:ganjil,genap',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
            'status'           => 'required|in:aktif,nonaktif',
        ]);

        // Nonaktifkan periode lain jika membuat periode aktif
        if ($request->status === 'aktif') {
            Period::where('status', 'aktif')->update(['status' => 'nonaktif']);
        }

        Period::create($request->all());

        return redirect()->route('period.index')
            ->with('success', 'Periode penilaian berhasil ditambahkan!');
    }

    public function edit(Period $period)
    {
        return view('period.edit', compact('period'));
    }

    public function update(Request $request, Period $period)
    {
        $request->validate([
            'tahun_ajaran'     => 'required|string',
            'semester'         => 'required|in:ganjil,genap',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
            'status'           => 'required|in:aktif,nonaktif',
        ]);

        // Jika mengaktifkan periode ini → nonaktifkan yang lain
        if ($request->status === 'aktif') {
            Period::where('status', 'aktif')
                  ->where('id', '!=', $period->id)
                  ->update(['status' => 'nonaktif']);
        }

        $period->update($request->all());

        return redirect()->route('period.index')
            ->with('success', 'Periode penilaian berhasil diperbarui!');
    }

    public function destroy(Period $period)
    {
        // Cegah hapus periode aktif
        if ($period->status === 'aktif') {
            return back()->with('error', 'Tidak dapat menghapus periode yang sedang aktif.');
        }

        $period->delete();

        return redirect()->route('period.index')
            ->with('success', 'Periode penilaian berhasil dihapus!');
    }
}
