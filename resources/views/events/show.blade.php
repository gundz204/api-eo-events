@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm border-dark">
        <div class="card-body">
            <h2 class="text-dark fw-bold">{{ $event->nama }}</h2>
            <hr class="text-dark">

            <ul class="list-unstyled text-dark">
                <li><strong>📍 Lokasi:</strong> {{ $event->lokasi }}</li>
                <li><strong>🕒 Waktu:</strong> {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('d M Y H:i') }}</li>
                <li><strong>Jenis:</strong> <span class="text-uppercase text-danger">{{ $event->jenis }}</span></li>
                <li><strong>Kuota:</strong> {{ $event->kuota }}</li>
                <li><strong>Sertifikat:</strong> {{ $event->mengeluarkan_sertifikat ? '✅ Ya' : '❌ Tidak' }}</li>
            </ul>

            @if ($event->deskripsi)
                <div class="mt-3">
                    <h5 class="text-dark">Deskripsi</h5>
                    <p class="text-muted">{{ $event->deskripsi }}</p>
                </div>
            @endif

            <a href="{{ route('events.register.form', $event->id) }}" class="btn btn-outline-danger mt-3">
                Daftar Sekarang
            </a>
        </div>
    </div>
</div>
@endsection
