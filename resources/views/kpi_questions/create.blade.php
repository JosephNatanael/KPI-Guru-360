@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Pertanyaan KPI</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kpi-questions.store') }}" method="POST">
        @include('kpi_questions.form')
    </form>
</div>
@endsection








