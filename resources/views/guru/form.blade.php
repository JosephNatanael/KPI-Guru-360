<div class="mb-3">
    <label>Nama</label>
    <input type="text" name="nama" class="form-control" value="{{ old('nama', $guru->nama ?? '') }}">
</div>

<div class="mb-3">
    <label>NIP</label>
    <input type="text" name="nip" class="form-control" value="{{ old('nip', $guru->nip ?? '') }}">
</div>

<div class="mb-3">
    <label>Jabatan</label>
    <select name="jabatan" class="form-control">
        <option value="Guru" {{ old('jabatan', $guru->jabatan ?? '') == 'Guru' ? 'selected' : '' }}>Guru</option>
        <option value="Kepala Sekolah" {{ old('jabatan', $guru->jabatan ?? '') == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
    </select>
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

<div class="mb-3">
    <label>Mata Pelajaran</label>
    <input type="text" name="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran', $guru->mata_pelajaran ?? '') }}">
</div>
