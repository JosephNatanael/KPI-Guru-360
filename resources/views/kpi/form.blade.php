<div class="mb-3">
    <label>Nama Indikator</label>
    <input type="text" name="nama" class="form-control"
           value="{{ old('nama', $kpi->nama ?? '') }}">
</div>

<div class="mb-3">
    <label>Kompetensi</label>
    <select name="kompetensi" class="form-control">
        @php
            $current = old('kompetensi', $kpi->kompetensi ?? '');
        @endphp
        <option value="">-- Pilih Kompetensi --</option>
        <option value="pedagogik" {{ $current === 'pedagogik' ? 'selected' : '' }}>Pedagogik</option>
        <option value="kepribadian" {{ $current === 'kepribadian' ? 'selected' : '' }}>Kepribadian</option>
        <option value="sosial" {{ $current === 'sosial' ? 'selected' : '' }}>Sosial</option>
        <option value="profesional" {{ $current === 'profesional' ? 'selected' : '' }}>Profesional</option>
    </select>
</div>

<div class="mb-3">
    <label>Bobot (%)</label>
    <input type="number" min="0" max="100" name="bobot" class="form-control"
           value="{{ old('bobot', $kpi->bobot ?? '') }}">
</div>
