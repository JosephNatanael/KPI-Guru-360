<?php

namespace App\Http\Controllers;

use App\Models\KpiIndicator;
use App\Models\KpiQuestion;
use App\Models\Period;
use Illuminate\Http\Request;

class KpiQuestionController extends Controller
{
    public function index(Request $request)
    {
        $kpiId = $request->get('kpi_id');
        $periodeId = $request->get('periode_id');
        $rolePenilai = $request->get('role_penilai', 'kepala_sekolah');

        $activePeriod = Period::where('status', 'aktif')->first();
        
        // Default ke periode aktif jika tidak ada filter
        if (!$periodeId && $activePeriod) {
            $periodeId = $activePeriod->id;
        }

        $indikators = KpiIndicator::orderBy('nama')->get();
        $periods = Period::orderBy('tanggal_mulai', 'desc')->get();

        $query = KpiQuestion::with(['kpi', 'period'])
            ->where('role_penilai', $rolePenilai)
            ->orderBy('kpi_indicator_id')
            ->orderBy('urutan');
        
        if ($kpiId) {
            $query->where('kpi_indicator_id', $kpiId);
        }

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $questions = $query->get();

        return view('kpi_questions.index', compact('questions', 'indikators', 'kpiId', 'periods', 'periodeId', 'activePeriod', 'rolePenilai'));
    }

    public function create(Request $request)
    {
        $indikators = KpiIndicator::orderBy('nama')->get();
        $periods = Period::orderBy('tanggal_mulai', 'desc')->get();
        $activePeriod = Period::where('status', 'aktif')->first();
        $rolePenilai = $request->get('role_penilai', 'kepala_sekolah');

        return view('kpi_questions.create', compact('indikators', 'periods', 'activePeriod', 'rolePenilai'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kpi_indicator_id' => 'required|exists:kpi_indicators,id',
            'periode_id'       => 'nullable|exists:periods,id',
            'role_penilai'     => 'required|in:kepala_sekolah,guru,wali_murid',
            'pertanyaan'       => 'required|string|max:255',
            'urutan'           => 'nullable|integer|min:1',
        ]);

        // Jika periode_id tidak dikirim, gunakan periode aktif
        if (empty($data['periode_id'])) {
            $activePeriod = Period::where('status', 'aktif')->first();
            if (!$activePeriod) {
                return back()->with('error', 'Tidak ada periode aktif. Pilih periode secara manual atau aktifkan periode.');
            }
            $data['periode_id'] = $activePeriod->id;
        }

        // Cek apakah periode yang dipilih sudah selesai (tidak aktif)
        $targetPeriod = Period::find($data['periode_id']);
        if ($targetPeriod && $targetPeriod->status !== 'aktif') {
            return back()->with('error', 'Tidak dapat menambah pertanyaan pada periode yang sudah selesai atau nonaktif.');
        }

        KpiQuestion::create($data);

        return redirect()->route('kpi-questions.index', ['periode_id' => $data['periode_id'], 'role_penilai' => $data['role_penilai']])
            ->with('success', 'Pertanyaan KPI berhasil ditambahkan.');
    }

    public function edit(KpiQuestion $kpi_question)
    {
        $indikators = KpiIndicator::orderBy('nama')->get();
        $periods = Period::orderBy('tanggal_mulai', 'desc')->get();
        $activePeriod = Period::where('status', 'aktif')->first();

        return view('kpi_questions.edit', [
            'question'   => $kpi_question,
            'indikators' => $indikators,
            'periods'    => $periods,
            'activePeriod' => $activePeriod,
        ]);
    }

    public function update(Request $request, KpiQuestion $kpi_question)
    {
        // Cek locking mechanism
        if ($kpi_question->period && $kpi_question->period->status !== 'aktif') {
            return back()->with('error', 'Pertanyaan tidak dapat diubah karena periode evaluasi telah selesai atau nonaktif.');
        }

        $data = $request->validate([
            'kpi_indicator_id' => 'required|exists:kpi_indicators,id',
            'role_penilai'     => 'required|in:kepala_sekolah,guru,wali_murid',
            'pertanyaan'       => 'required|string|max:255',
            'urutan'           => 'nullable|integer|min:1',
        ]);

        $kpi_question->update($data);

        return redirect()->route('kpi-questions.index', ['periode_id' => $kpi_question->periode_id, 'role_penilai' => $kpi_question->role_penilai])
            ->with('success', 'Pertanyaan KPI berhasil diperbarui.');
    }

    public function destroy(KpiQuestion $kpi_question)
    {
        // Cek locking mechanism
        if ($kpi_question->period && $kpi_question->period->status !== 'aktif') {
            return back()->with('error', 'Pertanyaan tidak dapat dihapus karena periode evaluasi telah selesai atau nonaktif.');
        }

        $periodeId = $kpi_question->periode_id;
        $rolePenilai = $kpi_question->role_penilai;
        $kpi_question->delete();

        return redirect()->route('kpi-questions.index', ['periode_id' => $periodeId, 'role_penilai' => $rolePenilai])
            ->with('success', 'Pertanyaan KPI berhasil dihapus.');
    }

    public function copyQuestions(Request $request)
    {
        $request->validate([
            'from_period_id' => 'required|exists:periods,id',
            'to_period_id'   => 'required|exists:periods,id',
        ]);

        $from_id = $request->from_period_id;
        $to_id = $request->to_period_id;

        if ($from_id == $to_id) {
            return back()->with('error', 'Periode asal dan tujuan tidak boleh sama.');
        }

        $targetPeriod = Period::findOrFail($to_id);
        if ($targetPeriod->status !== 'aktif') {
            return back()->with('error', 'Tidak dapat menyalin ke periode yang sudah selesai atau nonaktif.');
        }

        $sourceQuestions = KpiQuestion::where('periode_id', $from_id)->get();

        if ($sourceQuestions->isEmpty()) {
            return back()->with('error', 'Tidak ada pertanyaan untuk disalin dari periode tersebut.');
        }

        // Hapus pertanyaan yang sudah ada di periode tujuan
        KpiQuestion::where('periode_id', $to_id)->delete();

        $count = 0;
        foreach ($sourceQuestions as $q) {
            KpiQuestion::create([
                'kpi_indicator_id' => $q->kpi_indicator_id,
                'periode_id'       => $to_id,
                'role_penilai'     => $q->role_penilai,
                'pertanyaan'       => $q->pertanyaan,
                'urutan'           => $q->urutan,
            ]);
            $count++;
        }

        return redirect()->route('kpi-questions.index', ['periode_id' => $to_id])
            ->with('success', "$count pertanyaan berhasil disalin ke periode tujuan.");
    }
}
