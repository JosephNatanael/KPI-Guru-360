@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Periode Penilaian</h3>

    <form action="{{ route('period.store') }}" method="POST">
        @csrf
        @include('period.form')

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
