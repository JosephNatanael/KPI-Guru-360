@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Riwayat Penilaian 360° Anda</h3>

    <p>Periode Aktif: <b>{{ $periode->tahun_ajaran }} ({{ ucfirst($periode->semester) }})</b></p>

    <a href="{{ route('evaluation.pilih-guru') }}" class="btn btn-success mb-3">
        + Lakukan Penilaian
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Guru Dinilai</th>
                <th>Role Penilai</th>
                <th>Rata-rata Nilai</th>
                <th>Tanggal</th>
            </tr>
        </thead>

        <tbody>
            @foreach($evaluations as $e)
            <tr>
                <td>{{ $e->guru->nama }}</td>
                <td>{{ ucfirst(str_replace('_',' ',$e->role_penilai)) }}</td>
                <td>{{ number_format($e->average_score, 2) }}</td>
                <td>{{ $e->created_at->format('d-m-Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
