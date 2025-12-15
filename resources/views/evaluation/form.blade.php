@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Form Penilaian Untuk: <b>{{ $guru->nama }}</b></h3>
    <p>Periode: <b>{{ $periode->tahun_ajaran }} ({{ ucfirst($periode->semester) }})</b></p>
    <hr>

    <form action="{{ route('evaluation.store', $guru->id) }}" method="POST">
        @csrf

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>KPI</th>
                    <th>Nilai (1–5)</th>
                </tr>
            </thead>

            <tbody>
                @foreach($kpis as $kpi)
                <tr>
                    <td>
                        <b>{{ $kpi->nama }}</b><br>
                        <small class="text-muted">{{ $kpi->deskripsi }}</small>
                    </td>
                    <td width="130">
                        <select class="form-control" name="nilai[{{ $kpi->id }}]" required>
                            <option value="">-- pilih --</option>
                            @for($i=1; $i<=5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button class="btn btn-primary">Simpan Penilaian</button>
    </form>
</div>
@endsection
