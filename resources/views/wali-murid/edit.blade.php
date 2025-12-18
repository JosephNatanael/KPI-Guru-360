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
            <select name="user_id" class="form-control">
                <option value="">-- Pilih User --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('user_id', $waliMurid->user_id) == $u->id ? 'selected' : '' }}>
                        {{ $u->name }} ({{ $u->email }})
                    </option>
                @endforeach
            </select>
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
        <a href="{{ route('wali-murid.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection


