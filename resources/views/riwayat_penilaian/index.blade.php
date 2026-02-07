@extends('layouts.app')

@section('content')
<div class="container">
    <h4>
        Riwayat Penilaian Kinerja Guru <br>
        <small class="text-muted fs-6">
            Periode Aktif: {{ $activePeriod->tahun_ajaran ?? '-' }} ({{ ucfirst($activePeriod->semester ?? '-') }})
        </small>
    </h4>

    <div class="table-responsive">
        <table class="table table-bordered mt-3">
        <thead class="table-primary">
            <tr>
                <th>Guru</th>
                <th>Nilai Akhir</th>
                <th>Rekomendasi</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($riwayat as $item)
            <tr>
                <td>{{ $item->guru->nama }}</td>
                <td>{{ $item->nilai_akhir }}</td>
                <td>{{ $item->recommendation->nama ?? '-' }}</td>
                <td>
                    <a href="{{ route('riwayat.penilaian.detail', [
                        $item->guru_id,
                        $item->periode_id
                        ]) }}"
                    class="btn btn-sm btn-primary">
                    Detail
                </a>

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
