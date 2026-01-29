@csrf

@if(isset($activePeriod) && $activePeriod)
    <div class="alert alert-info shadow-sm border-0 mb-4">
        <i class="bi bi-info-circle-fill me-2"></i> Pertanyaan ini akan ditambahkan ke <strong>Periode: {{ $activePeriod->tahun_ajaran }} ({{ ucfirst($activePeriod->semester) }})</strong> yang saat ini aktif.
    </div>
@endif

<div class="mb-3">
    <label for="periode_id" class="form-label">Periode Evaluasi</label>
    <select name="periode_id" id="periode_id" class="form-control" required {{ isset($question) ? 'disabled' : '' }}>
        @foreach($periods as $p)
            <option value="{{ $p->id }}" 
                @if(old('periode_id', $question->periode_id ?? ($activePeriod->id ?? '')) == $p->id) selected @endif>
                {{ $p->tahun_ajaran }} ({{ ucfirst($p->semester) }}) @if($p->status === 'aktif') [AKTIF] @endif
            </option>
        @endforeach
    </select>
    @if(isset($question))
        <input type="hidden" name="periode_id" value="{{ $question->periode_id }}">
        <small class="text-muted">Periode tidak dapat diubah setelah pertanyaan dibuat.</small>
    @endif
</div>

<div class="mb-3">
    <label for="kpi_indicator_id" class="form-label">KPI</label>
    <select name="kpi_indicator_id" id="kpi_indicator_id" class="form-control" required>
        <option value="">-- Pilih KPI --</option>
        @foreach($indikators as $kpi)
            <option value="{{ $kpi->id }}"
                @if(old('kpi_indicator_id', $question->kpi_indicator_id ?? '') == $kpi->id) selected @endif>
                {{ $kpi->nama }} ({{ ucfirst($kpi->kompetensi) }})
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="pertanyaan" class="form-label">Pertanyaan</label>
    <input type="text" name="pertanyaan" id="pertanyaan" class="form-control"
           value="{{ old('pertanyaan', $question->pertanyaan ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="urutan" class="form-label">Urutan</label>
    <input type="number" name="urutan" id="urutan" class="form-control"
           value="{{ old('urutan', $question->urutan ?? 1) }}" min="1">
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('kpi-questions.index') }}" class="btn btn-secondary">Batal</a>








