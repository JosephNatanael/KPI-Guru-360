@extends('layouts.app')

@section('content')

<div class="position-relative mb-3 text-center">
    <a href="{{ route('riwayat.penilaian') }}" class="btn btn-secondary position-absolute start-0 top-0">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h4 class="mb-0 d-inline-block pt-1">
        Detail Penilaian Guru: {{ $guru->nama }}
    </h4>
</div>

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
            <th>Nilai Akhir Per Role</th>
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
            <th>Nilai Akhir Penilai (Σ Bobot × Nilai / 5)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($evaluations as $e)
            <tr>
                <td>{{ $e->penilai->name ?? '-' }}</td>
                <td>{{ ucfirst(str_replace('_',' ', $e->role_penilai)) }}</td>
                <td>{{ $e->nilai_akhir_penilai ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<hr>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Rekap Per Indikator (360°)</h5>
    <a href="{{ route('riwayat.penilaian.penilai', [$guru->id, $periode->id]) }}" class="btn btn-info btn-sm">
        <i class="bi bi-clock-history"></i> Lihat Riwayat Lengkap Penilai
    </a>
</div>

@if(!empty($indikatorRekap))
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sub Kompetensi</th>
                <th>Bobot (%)</th>
                <th>Nilai KS</th>
                <th>Nilai RG</th>
                <th>Nilai WM</th>
                <th>Rata-Rata 360°</th>
                <th>Nilai Akhir (Bobot×Rata-rata/5)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($indikatorRekap as $row)
                <tr>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['bobot'] }}</td>
                    <td>{{ $row['nilai_ks'] }}</td>
                    <td>{{ $row['nilai_rg'] }}</td>
                    <td>{{ $row['nilai_wm'] }}</td>
                    <td>{{ $row['rata360'] }}</td>
                    <td>{{ $row['nilai_akhir'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total Nilai Akhir 360° Guru</th>
                <th>{{ collect($indikatorRekap)->sum('bobot') }}%</th>
                <th colspan="4"></th>
                <th>≈ {{ number_format($totalNilaiAkhir, 2) }}</th>
            </tr>
        </tfoot>
    </table>
@else
    <p class="text-muted">Belum ada data rekap per indikator.</p>
@endif



@endsection