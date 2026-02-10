@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Wali Murid</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('wali-murid.store') }}" method="POST">
        @csrf



        <div class="mb-3">
            <label>Nama Wali</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label>Nama Anak</label>
            <input type="text" name="nama_anak" class="form-control" value="{{ old('nama_anak') }}">
        </div>

        <div class="mb-3">
            <label>Kelas Anak</label>
            <select name="kelas" class="form-control">
                <option value="">-- Pilih Kelas (sesuai wali kelas) --</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k }}" {{ old('kelas') == $k ? 'selected' : '' }}>
                        {{ $k }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('wali-murid.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection


