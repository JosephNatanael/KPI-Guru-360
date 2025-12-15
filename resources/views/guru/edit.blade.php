@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Guru</h3>
    <form action="{{ route('guru.update', $guru->id) }}" method="POST">
        @csrf @method('PUT')
        @include('guru.form')
        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection
