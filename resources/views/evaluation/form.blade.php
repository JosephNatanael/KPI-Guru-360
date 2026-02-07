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
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="d-none d-sm-table-cell">No</th>
                                        <th>Pertanyaan</th>
                                        <th class="text-center" style="min-width: 120px;">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kpi->questions as $index => $question)
                                        <tr>
                                            <td class="text-center d-none d-sm-table-cell">{{ $index + 1 }}</td>
                                            <td>{{ $question->pertanyaan }}</td>
                                            <td>
                                                @php
                                                    $colors = [
                                                        1 => 'danger',
                                                        2 => 'warning',
                                                        3 => 'secondary',
                                                        4 => 'success',
                                                        5 => 'success',
                                                    ];
                                                @endphp

                                                <div class="btn-group btn-group-responsive" role="group" aria-label="Nilai 1 sampai 5">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <input type="radio" class="btn-check" name="nilai[question_{{ $question->id }}]"
                                                            id="q{{ $question->id }}_{{ $i }}" value="{{ $i }}" required>
                                                            <label
                                                                class="btn btn-outline-{{ $colors[$i] }} rounded-circle"
                                                                style="width:38px;height:38px;line-height:24px"
                                                                for="q{{ $question->id }}_{{ $i }}"> {{ $i }} 
                                                            </label>
                                                    @endfor
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                </table>
                            </div>
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