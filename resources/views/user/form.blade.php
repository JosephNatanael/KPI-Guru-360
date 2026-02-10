{{-- ROLE FIRST --}}
<div class="mb-3">
    <label>Role</label>
    <select name="role" class="form-control" id="roleSelect">
        <option value="">-- Pilih Role --</option>
        @foreach(['admin','kepala_sekolah','guru','wali_murid'] as $r)
        <option value="{{ $r }}" {{ old('role', $user->role ?? '') == $r ? 'selected' : '' }}>
            {{ ucfirst($r) }}
        </option>
        @endforeach
    </select>
</div>

{{-- GENERIC FIELDS (Hidden until role selected) --}}
<div id="genericFields">
    <div class="mb-3">
        <label>Nama Lengkap</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $user->name ?? '') }}">
    </div>

    <div class="mb-3" id="emailBox">
        <label>Email</label>
        <input type="email" name="email" class="form-control"
               value="{{ old('email', $user->email ?? '') }}">
    </div>
</div>

<!-- Profile Guru Fields (muncul jika role = guru) -->
<div id="guruProfileBox" style="display:none">
    <hr>
    <h5>Data Profil Guru</h5>
    
    {{-- Nama Guru removed (use generic Name) --}}

    <div class="mb-3 form-check">
        <input type="checkbox" name="is_wali_kelas" class="form-check-input" id="waliKelasCheck"
               value="1" {{ old('is_wali_kelas', $user->guru->is_wali_kelas ?? 0) ? 'checked' : '' }}>
        <label class="form-check-label" for="waliKelasCheck">Wali Kelas</label>
    </div>

    <div class="mb-3" id="kelasBox" style="display:none">
        <label>Kelas</label>
        <input type="text" name="kelas" class="form-control @error('kelas') is-invalid @enderror" 
               value="{{ old('kelas', $user->guru->kelas ?? '') }}"
               placeholder="Contoh: 1 SD, TK A, 1 SMP ">
        @error('kelas')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Profile Wali Murid Fields (muncul jika role = wali_murid) -->
<div id="waliMuridProfileBox" style="display:none">
    <hr>
    <h5>Data Profil Wali Murid</h5>
    
    {{-- Nama Wali removed (use generic Name) --}}

    <div class="mb-3">
        <label>Nama Anak <span class="text-danger">*</span></label>
        <input type="text" name="nama_anak" class="form-control" 
               value="{{ old('nama_anak', $user->waliMurid->nama_anak ?? '') }}"
               placeholder="Nama lengkap anak">
    </div>

    <div class="mb-3">
        <label>Kelas Anak <span class="text-danger">*</span></label>
        <select name="kelas_wali" class="form-control">
            <option value="">-- Pilih Kelas (sesuai wali kelas) --</option>
            @foreach($kelasList as $k)
                <option value="{{ $k }}" {{ old('kelas_wali', $user->waliMurid->kelas ?? '') == $k ? 'selected' : '' }}>
                    {{ $k }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<script>
    function toggleProfileBoxes() {
        let role = document.getElementById('roleSelect').value;
        
        // Toggle Guru profile
        document.getElementById('guruProfileBox').style.display = 
            (role === 'guru') ? 'block' : 'none';
        
        // Toggle Wali Murid profile
        document.getElementById('waliMuridProfileBox').style.display = 
            (role === 'wali_murid') ? 'block' : 'none';

        // Toggle Generic Fields visibility
        let genericFields = document.getElementById('genericFields');
        if (role === '') {
            genericFields.style.display = 'none';
        } else {
            genericFields.style.display = 'block';
        }

        // Hide Email manual input for Wali Murid (Auto-generated) - REMOVED
        // let emailBox = document.getElementById('emailBox');
        // if (role === 'wali_murid') {
        //     emailBox.style.display = 'none';
        // } else {
        //     emailBox.style.display = 'block';
        // }
        // Always show email box
        document.getElementById('emailBox').style.display = 'block';
        
        // Toggle Kelas box for wali kelas
        toggleKelasBox();
    }
    
    function toggleKelasBox() {
        let isWaliKelas = document.getElementById('waliKelasCheck').checked;
        document.getElementById('kelasBox').style.display = 
            isWaliKelas ? 'block' : 'none';
    }
    
    document.getElementById('roleSelect').addEventListener('change', toggleProfileBoxes);
    document.getElementById('waliKelasCheck').addEventListener('change', toggleKelasBox);
    
    // Initialize on page load
    toggleProfileBoxes();
</script>
