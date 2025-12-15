@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Riwayat Penilaian Kinerja Guru</h4>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Guru</th>
                <th>Periode</th>
                <th>Nilai Akhir</th>
                <th>Rekomendasi</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($riwayat as $item)
            <tr>
                <td>{{ $item->guru->nama }}</td>
                <td>{{ $item->period->nama ?? 'Periode '.$item->periode_id }}</td>
                <td>{{ $item->nilai_akhir }}</td>
                <td>
                    @switch($item->rekomendasi)
                        @case('promosi') Sangat Baik @break
                        @case('pelatihan') Baik @break
                        @case('evaluasi') Cukup @break
                        @case('pembinaan') Perlu Pembinaan @break
                    @endswitch
                </td>
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
@endsection
