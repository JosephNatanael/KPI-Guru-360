<div class="mb-3">
    <label>Nama</label>
    <input type="text" name="name" class="form-control"
           value="{{ old('name', $user->name ?? '') }}">
</div>

<div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control"
           value="{{ old('email', $user->email ?? '') }}">
</div>

<div class="mb-3">
    <label>Role</label>
    <select name="role" class="form-control" id="roleSelect">
        @foreach(['admin','kepala_sekolah','guru','wali_murid'] as $r)
        <option value="{{ $r }}"
            {{ old('role', $user->role ?? '') == $r ? 'selected' : '' }}>
            {{ ucfirst($r) }}
        </option>
        @endforeach
    </select>
</div>

<div class="mb-3" id="guruSelectBox" style="display:none">
    <label>Pilih Guru (Jika role Guru)</label>
    <select name="guru_id" class="form-control">
        <option value="">-- Pilih Guru --</option>

        @foreach($gurus as $g)
        <option value="{{ $g->id }}"
            {{ old('guru_id', $user->guru_id ?? '') == $g->id ? 'selected' : '' }}>
            {{ $g->nama }}
        </option>
        @endforeach
    </select>
</div>

<script>
    function toggleGuruBox() {
        let role = document.getElementById('roleSelect').value;
        document.getElementById('guruSelectBox').style.display =
            (role === 'guru') ? 'block' : 'none';
    }
    document.getElementById('roleSelect').addEventListener('change', toggleGuruBox);
    toggleGuruBox();
</script>
