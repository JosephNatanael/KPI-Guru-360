<div class="mb-3">
    <label>Tahun Ajaran</label>
    <input type="text" name="tahun_ajaran" class="form-control"
           value="{{ old('tahun_ajaran', $period->tahun_ajaran ?? '') }}"
           placeholder="contoh: 2024/2025">
</div>

<div class="mb-3">
    <label>Semester</label>
    <select name="semester" class="form-control">
        <option value="ganjil" {{ old('semester', $period->semester ?? '') == 'ganjil' ? 'selected' : '' }}>
            Ganjil
        </option>
        <option value="genap" {{ old('semester', $period->semester ?? '') == 'genap' ? 'selected' : '' }}>
            Genap
        </option>
    </select>
</div>

<div class="mb-3">
    <label>Tanggal Mulai</label>
    <input type="date" name="tanggal_mulai" class="form-control"
           value="{{ old('tanggal_mulai', $period->tanggal_mulai ?? '') }}">
</div>


<div class="mb-3">
    <label>Tanggal Selesai</label>
    <input type="date" name="tanggal_selesai" class="form-control"
           value="{{ old('tanggal_selesai', $period->tanggal_selesai ?? '') }}">
</div>


<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="aktif" {{ old('status', $period->status ?? '') == 'aktif' ? 'selected' : '' }}>
            Aktif
        </option>
        <option value="nonaktif" {{ old('status', $period->status ?? '') == 'nonaktif' ? 'selected' : '' }}>
            Nonaktif
        </option>
    </select>
</div>
