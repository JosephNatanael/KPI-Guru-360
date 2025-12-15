@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Tambah Bobot Evaluator</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali input Anda:</strong>
            <ul>
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('weights.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Jenis Guru</label>
            <select name="jenis_guru" class="form-control">
                <option value="wali_kelas">Wali Kelas</option>
                <option value="non_wali_kelas">Non Wali Kelas</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Bobot Kepala Sekolah (%)</label>
            <input type="number" name="kepala_sekolah" class="form-control"
                   min="0" max="100" required>
        </div>

        <div class="mb-3">
            <label>Bobot Rekan Guru (%)</label>
            <input type="number" name="rekan_guru" class="form-control"
                   min="0" max="100" required>
        </div>

        <div class="mb-3">
            <label>Bobot Wali Murid (%) <small>(opsional)</small></label>
            <input type="number" name="wali_murid" class="form-control"
                   min="0" max="100">
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('weights.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
