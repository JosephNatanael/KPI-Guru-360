@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Rekomendasi</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('recommendations.update', $recommendation->id) }}" method="POST">
        @method('PUT')
        @include('recommendations.form', ['recommendation' => $recommendation])
    </form>
</div>
@endsection








