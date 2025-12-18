@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Data Wali Murid</h3>
    <a href="{{ route('wali-murid.create') }}" class="btn btn-primary mb-3">+ Tambah Wali Murid</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Nama Wali</th>
                <th>Nama Anak</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($waliMurids as $wm)
                <tr>
                    <td>{{ $wm->user->name ?? '-' }} ({{ $wm->user->email ?? '-' }})</td>
                    <td>{{ $wm->nama }}</td>
                    <td>{{ $wm->nama_anak }}</td>
                    <td>{{ $wm->kelas }}</td>
                    <td>
                        <a href="{{ route('wali-murid.edit', $wm->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('wali-murid.destroy', $wm->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data wali murid.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $waliMurids->links() }}
</div>
@endsection





