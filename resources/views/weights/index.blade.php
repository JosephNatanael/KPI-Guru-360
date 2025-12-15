@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Daftar Bobot Evaluator</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('weights.create') }}" class="btn btn-primary mb-3">+ Tambah Bobot</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Jenis Guru</th>
                <th>Kepala Sekolah (%)</th>
                <th>Rekan Guru (%)</th>
                <th>Wali Murid (%)</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($weights as $weight)
            <tr>
                <td>{{ ucwords(str_replace('_', ' ', $weight->jenis_guru)) }}</td>
                <td>{{ $weight->kepala_sekolah }}</td>
                <td>{{ $weight->rekan_guru }}</td>
                <td>{{ $weight->wali_murid ?? '-' }}</td>
                <td>
                    <a href="{{ route('weights.edit', $weight->id) }}" class="btn btn-warning btn-sm">Edit</a>

                    <form action="{{ route('weights.destroy', $weight->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Hapus bobot ini?')" 
                                class="btn btn-danger btn-sm">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada data bobot penilai.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
