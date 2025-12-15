@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Periode Penilaian</h3>

    <a href="{{ route('period.create') }}" class="btn btn-primary mb-3">+ Tambah Periode</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tahun Ajaran</th>
                <th>Semester</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($periods as $p)
            <tr>
                <td>{{ $p->tahun_ajaran }}</td>
                <td>{{ ucfirst($p->semester) }}</td>
                <td>{{ $p->tanggal_mulai }}</td>
                <td>{{ $p->tanggal_selesai }}</td>
                <td>
                    <span class="badge {{ $p->status === 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('period.edit', $p->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    
                    @if($p->status !== 'aktif')
                    <form action="{{ route('period.destroy', $p->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus periode ini?')">
                            Hapus
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $periods->links() }}
</div>
@endsection
