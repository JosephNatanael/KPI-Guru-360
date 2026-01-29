@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Penilaian Guru</h3>

    <p>Periode Aktif: <b>{{ $periode->tahun_ajaran }} ({{ ucfirst($periode->semester) }})</b></p>

    @if(isset($unevaluatedGurus) && $unevaluatedGurus->count() > 0)
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-exclamation-circle me-1"></i> <b>Guru Belum Dinilai</b>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nama Guru</th>
                            <th>Jabatan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unevaluatedGurus as $g)
                        <tr>
                            <td class="align-middle">{{ $g->nama }}</td>
                            <td class="align-middle">{{ $g->jabatan ?? '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('evaluation.create', $g->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-pen"></i> Nilai Sekarang
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-1"></i> Terima kasih, Anda sudah menilai semua guru yang tersedia.
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead class="table-primary">
            <tr>
                <th>Guru Dinilai</th>
                <th>Role Penilai</th>
                <th>Tanggal</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($evaluations as $e)
            <tr>
                <td class="align-middle">{{ $e->guru->nama }}</td>
                <td class="align-middle">{{ ucfirst(str_replace('_',' ',$e->role_penilai)) }}</td>
                <td class="align-middle">{{ $e->created_at->format('d-m-Y') }}</td>
                <td class="text-end">
                    <a href="{{ route('evaluation.edit', $e->id) }}" class="btn btn-sm btn-info text-black">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
