@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Data KPI</h3>
    <a href="{{ route('kpi.create') }}" class="btn btn-primary mb-3">+ Tambah KPI</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama KPI</th>
                <th>Kompetensi</th>
                <th>Bobot (%)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kpis as $k)
            <tr>
                <td>{{ $k->nama }}</td>
                <td>{{ $k->kompetensi }}</td>
                <td>{{ $k->bobot }}</td>
                <td>
                    <a href="{{ route('kpi.edit', $k->id) }}" class="btn btn-warning btn-sm">Edit</a>

                    <form action="{{ route('kpi.destroy', $k->id) }}" class="d-inline" method="POST">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Hapus KPI ini?')" class="btn btn-danger btn-sm">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
