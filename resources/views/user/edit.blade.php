@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit User</h3>

    <form action="{{ route('user.update', $user->id) }}" method="POST">
        @csrf @method('PUT')
        @include('user.form')

        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection
