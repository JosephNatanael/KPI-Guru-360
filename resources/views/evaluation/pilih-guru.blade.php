@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Pilih Guru Yang Akan Dinilai</h3>
    <p>Periode Aktif: <b>{{ $periode->tahun_ajaran }} ({{ ucfirst($periode->semester) }})</b></p>

    <ul class="list-group">
        @foreach($gurus as $g)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>{{ $g->nama }}</span>
            
            @if(in_array($g->id, $evaluatedGuruIds ?? []))
                <button class="btn btn-secondary btn-sm" disabled>Sudah Dinilai</button>
            @else
                <a href="{{ route('evaluation.create', $g->id) }}" class="btn btn-primary btn-sm">Nilai</a>
            @endif
        </li>
        @endforeach
    </ul>
</div>
@endsection
