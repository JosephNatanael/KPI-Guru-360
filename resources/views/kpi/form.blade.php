<div class="mb-3">
    <label>Nama Indikator</label>
    <input type="text" name="nama" class="form-control"
           value="{{ old('nama', $kpi->nama ?? '') }}">
</div>

<div class="mb-3">
    <label>Kategori</label>
    <input type="text" name="kategori" class="form-control"
           value="{{ old('kategori', $kpi->kategori ?? '') }}">
</div>

<div class="mb-3">
    <label>Bobot (%)</label>
    <input type="number" min="0" max="100" name="bobot" class="form-control"
           value="{{ old('bobot', $kpi->bobot ?? '') }}">
</div>

<div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $kpi->deskripsi ?? '') }}</textarea>
</div>
