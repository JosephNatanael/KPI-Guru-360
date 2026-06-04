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
                <th>Jenjang</th>
                <th class="text-center">Nilai Akhir</th>
                <th class="d-none d-md-table-cell">Rekomendasi</th>
                <th class="text-end">Detail</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($riwayat as $item)
            <tr>
                <td>{{ $item->guru->nama }}</td>
                <td>{{ $item->guru->jenjang }}</td>
                <td class="text-center fw-bold">{{ $item->nilai_akhir }}</td>
                <td class="d-none d-md-table-cell">{{ $item->recommendation->nama ?? '-' }}</td>
                <td class="text-end">
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
