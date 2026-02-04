@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Periode Penilaian</h3>

    <form action="{{ route('period.update', $period->id) }}" method="POST">
        @csrf @method('PUT')
        @include('period.form')

        <button class="btn btn-success">Update</button>
        <a href="{{ route('period.index') }}" class="btn btn-secondary ms-2">Batalkan</a>
    </form>
</div>
@endsection
