@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Form Penilaian Untuk: <b>{{ $guru->nama }}</b></h3>
    <p>Periode: <b>{{ $periode->tahun_ajaran }} ({{ ucfirst($periode->semester) }})</b></p>
    <hr>

    <form action="{{ route('evaluation.store', $guru->id) }}" method="POST">
        @csrf

        @foreach($kpis as $kpi)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <strong>{{ $kpi->nama }}</strong>
                    <span class="badge bg-light text-dark ms-2">{{ ucfirst($kpi->kompetensi) }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($kpi->questions->isEmpty())
                    <div class="alert alert-warning">
                        KPI ini belum memiliki pertanyaan. Silakan tambahkan pertanyaan di menu Pertanyaan KPI.
                    </div>
                @else
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Pertanyaan</th>
                                <th width="15%">Nilai (1–5)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kpi->questions as $index => $question)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $question->pertanyaan }}</td>
                                <td>
                                    <select class="form-control" name="nilai[question_{{ $question->id }}]" required>
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
                @endif
            </div>
        </div>
        @endforeach

        <div class="mt-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Simpan Penilaian
            </button>
            <a href="{{ route('evaluation.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection
