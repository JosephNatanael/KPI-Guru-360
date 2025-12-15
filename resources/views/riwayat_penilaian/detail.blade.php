@extends('layouts.app')

@section('content')

<h4>
    Detail Penilaian Guru: {{ $guru->nama }}
</h4>

<p>
    Periode: {{ $periode->nama ?? $periode->id }}
</p>

<hr>

<h5>Rekap Penilaian per Role</h5>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Role Penilai</th>
            <th>Jumlah Penilai</th>
            <th>Rata-rata Nilai</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rekap as $row)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $row->role_penilai)) }}</td>
                <td>{{ $row->jumlah_penilai }}</td>
                <td>{{ $row->rata_rata }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<hr>

<h5>Detail Penilai</h5>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Nama Penilai</th>
            <th>Role</th>
            <th>Nilai</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($evaluations as $e)
            <tr>
                <td>{{ $e->penilai->name ?? '-' }}</td>
                <td>{{ ucfirst(str_replace('_',' ', $e->role_penilai)) }}</td>
                <td>{{ $e->average_score }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<a href="{{ route('riwayat.penilaian.penilai', [
        $guru->id,
        $periode->id
    ]) }}"
   class="btn btn-info mb-3">
    <i class="bi bi-clock-history"></i>
    Lihat Riwayat Lengkap Penilai
</a>

@endsection