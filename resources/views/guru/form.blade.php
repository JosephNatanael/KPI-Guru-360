<div class="mb-3">
    <label>Nama</label>
    <input type="text" name="nama" class="form-control" value="{{ old('nama', $guru->nama ?? '') }}">
</div>

<div class="mb-3">
    <label>NIP</label>
    <input type="text" name="nip" class="form-control" value="{{ old('nip', $guru->nip ?? '') }}">
</div>

<div class="mb-3">
    <label>Wali Kelas?</label>
    <select name="is_wali_kelas" class="form-control">
        <option value="0">Tidak</option>
        <option value="1" {{ old('is_wali_kelas', $guru->is_wali_kelas ?? '') == 1 ? 'selected' : '' }}>Ya</option>
    </select>
</div>

<div class="mb-3">
    <label>Kelas (Jika wali kelas)</label>
    <input type="text" name="kelas" class="form-control" value="{{ old('kelas', $guru->kelas ?? '') }}">
</div>


