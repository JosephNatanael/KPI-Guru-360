@csrf

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





