@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Master Rekomendasi</h3>

    <a href="{{ route('recommendations.create') }}" class="btn btn-primary mb-3">
        + Tambah Rekomendasi
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Range Nilai</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recommendations as $rec)
                <tr>
                    <td>{{ $rec->nama }}</td>
                    <td>{{ $rec->min_score }} - {{ $rec->max_score }}</td>
                    <td>{{ $rec->keterangan }}</td>
                    <td>
                        <a href="{{ route('recommendations.edit', $rec->id) }}" class="btn btn-warning btn-sm">
                            Edit
                        </a>
                        <form action="{{ route('recommendations.destroy', $rec->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus rekomendasi ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada data rekomendasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $recommendations->links() }}
</div>
@endsection





