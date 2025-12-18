<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index()
    {
        $recommendations = Recommendation::orderBy('min_score', 'desc')->paginate(10);
        return view('recommendations.index', compact('recommendations'));
    }

    public function create()
    {
        return view('recommendations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'       => 'required|string|max:191',
            'min_score'  => 'required|numeric|min:0|max:100',
            'max_score'  => 'required|numeric|min:0|max:100|gte:min_score',
            'keterangan' => 'nullable|string',
        ]);

        Recommendation::create($data);

        return redirect()->route('recommendations.index')
            ->with('success', 'Rekomendasi berhasil ditambahkan.');
    }

    public function edit(Recommendation $recommendation)
    {
        return view('recommendations.edit', compact('recommendation'));
    }

    public function update(Request $request, Recommendation $recommendation)
    {
        $data = $request->validate([
            'nama'       => 'required|string|max:191',
            'min_score'  => 'required|numeric|min:0|max:100',
            'max_score'  => 'required|numeric|min:0|max:100|gte:min_score',
            'keterangan' => 'nullable|string',
        ]);

        $recommendation->update($data);

        return redirect()->route('recommendations.index')
            ->with('success', 'Rekomendasi berhasil diperbarui.');
    }

    public function destroy(Recommendation $recommendation)
    {
        $recommendation->delete();

        return redirect()->route('recommendations.index')
            ->with('success', 'Rekomendasi berhasil dihapus.');
    }
}





