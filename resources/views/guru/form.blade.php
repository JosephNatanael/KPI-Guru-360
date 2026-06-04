<div class="mb-3">
    <label>Nama</label>
    <input type="text" name="nama" class="form-control" value="{{ old('nama', $guru->nama ?? '') }}">
</div>

<div class="mb-3">
    <label>Jenjang <span class="text-danger">*</span></label>
    <select name="jenjang" class="form-control">
        <option value="">-- Pilih Jenjang --</option>
        @foreach(['PG/TK', 'SD', 'SMP'] as $u)
            <option value="{{ $u }}" {{ old('jenjang', $guru->jenjang ?? '') == $u ? 'selected' : '' }}>
                {{ $u }}
            </option>
        @endforeach
    </select>
</div>



<div class="mb-3">
    <label>Wali Kelas?</label>
    <select name="is_wali_kelas" class="form-control" id="isWaliKelasSelect">
        <option value="0">Tidak</option>
        <option value="1" {{ old('is_wali_kelas', $guru->is_wali_kelas ?? '') == 1 ? 'selected' : '' }}>Ya</option>
    </select>
</div>

<div class="mb-3" id="kelasInputContainer" style="display: none;">
    <label>Kelas (Jika wali kelas)</label>
    <input type="text" name="kelas" class="form-control @error('kelas') is-invalid @enderror" value="{{ old('kelas', $guru->kelas ?? '') }}"
           placeholder="Contoh: 1 SD, TK A, 1 SMP ">
    @error('kelas')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('isWaliKelasSelect');
        const container = document.getElementById('kelasInputContainer');

        function toggleKelas() {
            if (select.value == '1') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }

        // Listen for changes
        select.addEventListener('change', toggleKelas);

        // Run on load
        toggleKelas();
    });
</script>


