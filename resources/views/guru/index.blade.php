@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Data Guru</h3>
    <a href="{{ route('guru.create') }}" class="btn btn-primary mb-3">+ Tambah Guru</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIP</th>
                <th>Jabatan</th>
                <th>Wali Kelas</th>
                <th>Mata Pelajaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gurus as $g)
            <tr>
                <td>{{ $g->nama }}</td>
                <td>{{ $g->nip }}</td>
                <td>{{ $g->jabatan }}</td>
                <td>{{ $g->is_wali_kelas ? 'Ya (' . $g->kelas . ')' : 'Tidak' }}</td>
                <td>{{ $g->mata_pelajaran }}</td>
                <td>
                    <a href="{{ route('guru.edit', $g->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('guru.destroy', $g->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $gurus->links() }}
</div>
@endsection
