@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah KPI</h3>

    <form action="{{ route('kpi.store') }}" method="POST">
        @csrf
        @include('kpi.form')
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
