@extends('layouts.app')

@section('content')

<div class="position-relative mb-3 text-center">
    <a href="{{ route('riwayat.penilaian.detail', [$guru->id, $periode->id]) }}" class="btn btn-secondary position-absolute start-0 top-0">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h4 class="mb-0 d-inline-block pt-1">
        Riwayat Lengkap Penilaian
    </h4>
</div>

<p>
    <strong>Guru:</strong> {{ $guru->nama }} <br>
    <strong>Periode:</strong> {{ $periode->tahun_ajaran }} ({{ ucfirst($periode->semester) }})
</p>

<hr>

@foreach ($evaluations as $e)
<div class="card mb-3">
    <div class="card-header">
        <strong>{{ $e->penilai->name ?? '-' }}</strong>
        <span class="badge bg-secondary">
            {{ ucfirst(str_replace('_',' ', $e->role_penilai)) }}
        </span>
        <span class="float-end">
            Nilai Rata-rata: <strong>{{ $e->average_score }}</strong>
        </span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Indikator KPI</th>
                    <th>Pertanyaan</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($e->details as $d)
                <tr>
                    <td>{{ $d->question->kpi->nama ?? '-' }}</td>
                    <td>{{ $d->question->pertanyaan ?? '-' }}</td>
                    <td>{{ $d->nilai }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endforeach
@endsection