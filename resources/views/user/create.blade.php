@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah User</h3>

    <form action="{{ route('user.store') }}" method="POST">
        @csrf
        @include('user.form')

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('user.index') }}" class="btn btn-secondary">Batalkan</a>
    </form>
</div>
@endsection
