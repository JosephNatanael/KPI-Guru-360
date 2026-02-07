@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Daftar Guru Belum Dinilai - Periode {{ $periode->tahun_ajaran }}</h3>
    
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    @if($gurus->isEmpty())
        <div class="alert alert-success">
            Semua guru sudah dinilai pada periode ini.
        </div>
    @else
        <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-danger">
                <tr>
                    <th class="d-none d-sm-table-cell">No</th>
                    <th>Nama Guru</th>
                    <th class="d-none d-sm-table-cell">Jabatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gurus as $index => $guru)
                <tr>
                    <td class="d-none d-sm-table-cell">{{ $index + 1 }}</td>
                    <td>{{ $guru->nama }}</td>
                    <td class="d-none d-sm-table-cell">{{ $guru->is_wali_kelas ? 'Wali Kelas ' . $guru->kelas : 'Guru Mapel' }}</td>
                    <td>
                         <a href="{{ route('evaluation.index') }}" class="btn btn-sm btn-primary">
                            Mulai Penilaian
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif
</div>
@endsection
