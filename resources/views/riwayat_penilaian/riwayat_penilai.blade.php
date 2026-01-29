@extends('layouts.app')

@section('content')

<h4>
    Riwayat Lengkap Penilaian
</h4>

<p>
    <strong>Guru:</strong> {{ $guru->nama }} <br>
    <strong>Periode:</strong> {{ $periode->nama ?? $periode->id }}
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
@endforeach
@endsection