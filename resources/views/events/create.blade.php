@extends('layouts.app')

@section('content')
<h3>Tambah Event Baru</h3>

<form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label>Nama Event</label>
        <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label>Lokasi</label>
        <input type="text" name="lokasi" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Jenis</label>
        <select name="jenis" class="form-control" required>
            <option value="gratis">Gratis</option>
            <option value="berbayar">Berbayar</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Waktu Mulai</label>
        <input type="datetime-local" name="waktu_mulai" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Waktu Selesai</label>
        <input type="datetime-local" name="waktu_selesai" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Kuota</label>
        <input type="number" name="kuota" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Keluarkan Sertifikat?</label>
        <select name="mengeluarkan_sertifikat" class="form-control" required>
            <option value="1">Ya</option>
            <option value="0">Tidak</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Foto</label>
        <input type="file" name="foto" class="form-control">
    </div>
    <div class="mb-3">
        <label>Image</label>
        <input type="file" name="image" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
</form>
@endsection
