@extends('layouts.app')

@section('content')
<h3>Peserta Event: {{ $event->nama }}</h3>

<form method="GET" class="mb-3">
    <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}">
</form>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Status Kehadiran</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($registrations as $reg)
        <tr>
            <td>{{ $reg->user->name }}</td>
            <td>{{ $reg->user->email }}</td>
            <td>{{ ucfirst($reg->status_kehadiran) }}</td>
            <td>
                <form action="{{ route('participants.update', $reg->id) }}" method="POST">
                    @csrf
                    <select name="status_kehadiran" class="form-select d-inline w-auto">
                        <option value="belum" {{ $reg->status_kehadiran == 'belum' ? 'selected' : '' }}>Belum</option>
                        <option value="hadir" {{ $reg->status_kehadiran == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="tidak" {{ $reg->status_kehadiran == 'tidak' ? 'selected' : '' }}>Tidak Hadir</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
