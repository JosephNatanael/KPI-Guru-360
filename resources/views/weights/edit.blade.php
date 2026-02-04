@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Edit Bobot Evaluator</h3>

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

    <form action="{{ route('weights.update', $weight->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Jenis Guru</label>
            <select name="jenis_guru" class="form-control">
                <option value="wali_kelas" 
                    {{ $weight->jenis_guru == 'wali_kelas' ? 'selected' : '' }}>
                    Wali Kelas
                </option>

                <option value="non_wali_kelas"
                    {{ $weight->jenis_guru == 'non_wali_kelas' ? 'selected' : '' }}>
                    Non Wali Kelas
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label>Bobot Kepala Sekolah (%)</label>
            <input type="number" name="kepala_sekolah" class="form-control"
                   value="{{ $weight->kepala_sekolah }}" min="0" max="100" required>
        </div>

        <div class="mb-3">
            <label>Bobot Rekan Guru (%)</label>
            <input type="number" name="rekan_guru" class="form-control"
                   value="{{ $weight->rekan_guru }}" min="0" max="100" required>
        </div>

        <div class="mb-3">
            <label>Bobot Wali Murid (%) <small>(opsional)</small></label>
            <input type="number" name="wali_murid" class="form-control"
                   value="{{ $weight->wali_murid }}" min="0" max="100">
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('weights.index') }}" class="btn btn-secondary">Batalkan</a>
    </form>
</div>
@endsection
