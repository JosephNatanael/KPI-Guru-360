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
    <strong>Guru:</strong> {{ $guru->nama }} <br>
    <strong>Jenjang:</strong> {{ $guru->jenjang }} <br>
    <strong>Periode:</strong> {{ $periode->tahun_ajaran }} ({{ ucfirst($periode->semester) }})
</p>

<hr>

<h5>Rekap Penilaian per Role</h5>

<div class="table-responsive">
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
</div>

<hr>

<h5>Detail Penilai</h5>

<div class="table-responsive">
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
</div>

<hr>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Rekap Per Indikator (360°)</h5>
    <a href="{{ route('riwayat.penilaian.penilai', [$guru->id, $periode->id]) }}" class="btn btn-info btn-sm">
        <i class="bi bi-clock-history"></i> Lihat Riwayat Lengkap Penilai
    </a>
</div>

@if(!empty($indikatorRekap))
    <div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Sub Kompetensi</th>
                <th>Bobot (%)</th>
                <th>KS</th>
                <th>RG</th>
                <th>WM</th>
                <th>Rata 360°</th>
                <th>Skor (%)</th>
                <th>Kategori</th>
                <th>Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($indikatorRekap as $row)
                <tr>
                    <td>{{ $row['nama'] }}</td>
                    <td class="text-center">{{ $row['bobot'] }}%</td>
                    <td class="text-center">{{ $row['nilai_ks'] }}</td>
                    <td class="text-center">{{ $row['nilai_rg'] }}</td>
                    <td class="text-center">{{ $row['nilai_wm'] }}</td>
                    <td class="text-center fw-bold">{{ $row['rata360'] }}</td>
                    <td class="text-center">
                        @if($row['persentase'] >= 90)
                            <span class="text-success fw-bold">{{ $row['persentase'] }}%</span>
                        @elseif($row['persentase'] >= 80)
                            <span class="text-primary fw-bold">{{ $row['persentase'] }}%</span>
                        @elseif($row['persentase'] > 60)
                            <span class="text-warning fw-bold">{{ $row['persentase'] }}%</span>
                        @else
                            <span class="text-danger fw-bold">{{ $row['persentase'] }}%</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($row['kategori'] == 'Sangat Baik')
                            <span class="badge bg-success">Sangat Baik</span>
                        @elseif($row['kategori'] == 'Baik')
                            <span class="badge bg-primary">Baik</span>
                        @elseif($row['kategori'] == 'Cukup')
                            <span class="badge bg-warning">Cukup</span>
                        @else
                            <span class="badge bg-danger">Kurang</span>
                        @endif
                    </td>
                    <td class="text-center fw-bold">{{ $row['nilai_akhir'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-secondary">
                <th colspan="8" class="text-end">Total Nilai Akhir Kontribusi</th>
                <th class="text-center fs-5">≈ {{ number_format($totalNilaiAkhir, 2) }}</th>
            </tr>
        </tfoot>
    </table>
    </div>
@else
    <p class="text-muted">Belum ada data rekap per indikator.</p>
@endif



@endsection