@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Guru</h3>
    <form action="{{ route('guru.store') }}" method="POST">
        @csrf
        @include('guru.form')
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
