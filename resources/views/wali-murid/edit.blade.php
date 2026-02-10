@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Wali Murid</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('wali-murid.update', $waliMurid->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>User (Akun Wali Murid)</label>
            <!-- Menampilkan User saat ini, jika ingin mengganti email, bisa langsung di sini -->
            <input type="text" class="form-control mb-2" value="{{ $waliMurid->user->name }} ({{ $waliMurid->user->email }})" disabled>
            
            <label>Ganti Email Login</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $waliMurid->user->email) }}">
            <small class="text-muted">Mengganti email ini akan mengubah email login User terkait.</small>
        </div>

        <div class="mb-3">
            <label>Nama Wali</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama', $waliMurid->nama) }}">
        </div>

        <div class="mb-3">
            <label>Nama Anak</label>
            <input type="text" name="nama_anak" class="form-control" value="{{ old('nama_anak', $waliMurid->nama_anak) }}">
        </div>

        <div class="mb-3">
            <label>Kelas Anak</label>
            <select name="kelas" class="form-control">
                <option value="">-- Pilih Kelas (sesuai wali kelas) --</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k }}" {{ old('kelas', $waliMurid->kelas) == $k ? 'selected' : '' }}>
                        {{ $k }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('wali-murid.index') }}" class="btn btn-secondary">Batalkan</a>
    </form>
</div>
@endsection


