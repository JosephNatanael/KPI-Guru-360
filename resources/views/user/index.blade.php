@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Data User</h3>
    <a href="{{ route('user.create') }}" class="btn btn-primary mb-3">+ Tambah User</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Guru (jika role guru)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ ucfirst($u->role) }}</td>
                <td>
                    @if($u->role == 'guru' && $u->guru)
                        {{ $u->guru->nama }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    <a href="{{ route('user.edit', $u->id) }}" class="btn btn-warning btn-sm">Edit</a>

                    <form action="{{ route('user.destroy', $u->id) }}" class="d-inline" method="POST">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Hapus user ini?')" class="btn btn-danger btn-sm">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}
</div>
@endsection
