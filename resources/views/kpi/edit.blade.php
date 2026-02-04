@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit KPI</h3>

    <form action="{{ route('kpi.update', $kpi->id) }}" method="POST">
        @csrf @method('PUT')
        @include('kpi.form')
        <button class="btn btn-success">Update</button>
        <a href="{{ route('kpi.index') }}" class="btn btn-secondary ms-2">Batalkan</a>
    </form>
</div>
@endsection
