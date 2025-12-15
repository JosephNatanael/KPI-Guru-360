<div class="mb-3">
    <label>Role Penilai</label>

    <select name="role" class="form-control">
        <option value="kepala_sekolah"
            {{ old('role', $weight->role ?? '') == 'kepala_sekolah' ? 'selected' : '' }}>
            Kepala Sekolah
        </option>

        <option value="guru"
            {{ old('role', $weight->role ?? '') == 'guru' ? 'selected' : '' }}>
            Guru
        </option>

        <option value="wali_murid"
            {{ old('role', $weight->role ?? '') == 'wali_murid' ? 'selected' : '' }}>
            Wali Murid
        </option>
    </select>
</div>

<div class="mb-3">
    <label>Bobot (%)</label>
    <input type="number" name="weight" class="form-control" min="1" max="100"
           value="{{ old('weight', $weight->weight ?? '') }}">
</div>
