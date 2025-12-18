<?php

namespace App\Http\Controllers;

use App\Models\KpiIndicator;
use App\Models\KpiQuestion;
use Illuminate\Http\Request;

class KpiQuestionController extends Controller
{
    public function index(Request $request)
    {
        $kpiId = $request->get('kpi_id');

        $indikators = KpiIndicator::orderBy('nama')->get();

        $query = KpiQuestion::with('kpi')->orderBy('kpi_indicator_id')->orderBy('urutan');
        if ($kpiId) {
            $query->where('kpi_indicator_id', $kpiId);
        }
        $questions = $query->get();

        return view('kpi_questions.index', compact('questions', 'indikators', 'kpiId'));
    }

    public function create()
    {
        $indikators = KpiIndicator::orderBy('nama')->get();
        return view('kpi_questions.create', compact('indikators'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kpi_indicator_id' => 'required|exists:kpi_indicators,id',
            'pertanyaan'       => 'required|string|max:255',
            'urutan'           => 'nullable|integer|min:1',
        ]);

        KpiQuestion::create($data);

        return redirect()->route('kpi-questions.index')
            ->with('success', 'Pertanyaan KPI berhasil ditambahkan.');
    }

    public function edit(KpiQuestion $kpi_question)
    {
        $indikators = KpiIndicator::orderBy('nama')->get();
        return view('kpi_questions.edit', [
            'question'   => $kpi_question,
            'indikators' => $indikators,
        ]);
    }

    public function update(Request $request, KpiQuestion $kpi_question)
    {
        $data = $request->validate([
            'kpi_indicator_id' => 'required|exists:kpi_indicators,id',
            'pertanyaan'       => 'required|string|max:255',
            'urutan'           => 'nullable|integer|min:1',
        ]);

        $kpi_question->update($data);

        return redirect()->route('kpi-questions.index')
            ->with('success', 'Pertanyaan KPI berhasil diperbarui.');
    }

    public function destroy(KpiQuestion $kpi_question)
    {
        $kpi_question->delete();

        return redirect()->route('kpi-questions.index')
            ->with('success', 'Pertanyaan KPI berhasil dihapus.');
    }
}
