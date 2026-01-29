@csrf

<div class="mb-3">
    <label for="nama" class="form-label">Nama Rekomendasi</label>
    <input type="text" name="nama" id="nama" class="form-control"
           value="{{ old('nama', $recommendation->nama ?? '') }}" required>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="min_score" class="form-label">Nilai Minimum</label>
        <input type="number" step="0.01" name="min_score" id="min_score" class="form-control"
               value="{{ old('min_score', $recommendation->min_score ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label for="max_score" class="form-label">Nilai Maksimum</label>
        <input type="number" step="0.01" name="max_score" id="max_score" class="form-control"
               value="{{ old('max_score', $recommendation->max_score ?? '') }}" required>
    </div>
</div>

<div class="mb-3">
    <label for="keterangan" class="form-label">Keterangan (opsional)</label>
    <textarea name="keterangan" id="keterangan" rows="3" class="form-control">{{ old('keterangan', $recommendation->keterangan ?? '') }}</textarea>
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('recommendations.index') }}" class="btn btn-secondary">Batal</a>








