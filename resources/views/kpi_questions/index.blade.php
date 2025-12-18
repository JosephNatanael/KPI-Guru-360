@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Pertanyaan KPI</h3>

    <a href="{{ route('kpi-questions.create') }}" class="btn btn-primary mb-3">+ Tambah Pertanyaan</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <select name="kpi_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Filter berdasarkan KPI --</option>
                @foreach($indikators as $kpi)
                    <option value="{{ $kpi->id }}" {{ $kpiId == $kpi->id ? 'selected' : '' }}>
                        {{ $kpi->nama }} ({{ ucfirst($kpi->kompetensi) }})
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>KPI</th>
                <th>Kompetensi</th>
                <th>Pertanyaan</th>
                <th>Urutan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($questions as $q)
                <tr>
                    <td>{{ $q->kpi->nama ?? '-' }}</td>
                    <td>{{ $q->kpi->kompetensi ?? '-' }}</td>
                    <td>{{ $q->pertanyaan }}</td>
                    <td>{{ $q->urutan }}</td>
                    <td>
                        <a href="{{ route('kpi-questions.edit', $q->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('kpi-questions.destroy', $q->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus pertanyaan ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada pertanyaan KPI.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection





