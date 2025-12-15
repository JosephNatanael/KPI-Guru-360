@extends('layouts.app')

@section('content')
<div class="container">

    <h3>Hasil Nilai Akhir 360° - Periode {{ $periode->tahun_ajaran }}</h3>

    <a href="{{ route('finalscore.hitung') }}" class="btn btn-primary mb-3">
        Hitung Nilai Akhir
    </a>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Guru</th>
                <th>Kepala Sekolah</th>
                <th>Rekan Guru</th>
                <th>Wali Murid</th>
                <th>Nilai Akhir 360°</th>
                <th>Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scores as $s)
            <tr>
                <td>{{ $s->guru->nama }}</td>
                <td>{{ number_format($s->nilai_kepala_sekolah, 2) }}</td>
                <td>{{ number_format($s->nilai_rekan_guru, 2) }}</td>
                <td>{{ number_format($s->nilai_wali_murid, 2) }}</td>
                <td><b>{{ number_format($s->nilai_akhir, 2) }}</b></td>
                <td><b>{{ $s->rekomendasi }}</b></td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
