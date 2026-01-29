@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Pertanyaan KPI</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kpi-questions.update', $question->id) }}" method="POST">
        @method('PUT')
        @include('kpi_questions.form', ['question' => $question])
    </form>
</div>
@endsection








