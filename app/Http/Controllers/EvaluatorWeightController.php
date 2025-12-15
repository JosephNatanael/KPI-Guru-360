<?php

namespace App\Http\Controllers;

use App\Models\EvaluatorWeight;
use Illuminate\Http\Request;

class EvaluatorWeightController extends Controller
{
    public function index()
    {
        $weights = EvaluatorWeight::all();
        return view('weights.index', compact('weights'));
    }

    public function create()
    {
        return view('weights.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_guru' => 'required|in:wali_kelas,non_wali_kelas',
            'kepala_sekolah' => 'required|integer|min:0|max:100',
            'rekan_guru' => 'required|integer|min:0|max:100',
            'wali_murid' => 'nullable|integer|min:0|max:100'
        ]);

        EvaluatorWeight::create($request->all());

        return redirect()->route('weights.index')
            ->with('success', 'Bobot penilai berhasil ditambahkan!');
    }

    public function edit(EvaluatorWeight $weight)
    {
        return view('weights.edit', compact('weight'));
    }

    public function update(Request $request, EvaluatorWeight $weight)
    {
        $request->validate([
            'jenis_guru' => 'required|in:wali_kelas,non_wali_kelas',
            'kepala_sekolah' => 'required|integer|min:0|max:100',
            'rekan_guru' => 'required|integer|min:0|max:100',
            'wali_murid' => 'nullable|integer|min:0|max:100'
        ]);

        $weight->update($request->all());

        return redirect()->route('weights.index')
            ->with('success', 'Bobot penilai berhasil diperbarui!');
    }

    public function destroy(EvaluatorWeight $weight)
    {
        $weight->delete();

        return redirect()->route('weights.index')
            ->with('success', 'Bobot penilai berhasil dihapus!');
    }
}
