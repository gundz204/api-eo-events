@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Dashboard</h2>
    <p>Selamat datang, {{ auth()->user()->name }}!</p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-danger">Logout</button>
    </form>
</div>
@endsection
