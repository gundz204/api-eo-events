@extends('layouts.app')

@section('content')
<h1 class="mb-4 text-black fw-bold">Daftar Event</h1>

@forelse ($events as $event)
    <div class="card mb-4 border-dark shadow-sm">
        <div class="card-body d-flex flex-column flex-md-row align-items-start">
            <div class="flex-grow-1">
                <h4 class="card-title text-dark fw-bold">{{ $event->nama }}</h4>
                <p class="text-muted mb-2">{{ $event->deskripsi ?? 'Tidak ada deskripsi.' }}</p>

                <ul class="list-unstyled mb-3">
                    <li><strong>📍 Lokasi:</strong> {{ $event->lokasi }}</li>
                    <li><strong>🕒 Waktu:</strong> {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('d M Y H:i') }}</li>
                    <li><strong>Jenis:</strong> <span class="text-uppercase text-danger">{{ $event->jenis }}</span></li>
                    <li><strong>Kuota:</strong> {{ $event->kuota }}</li>
                    <li><strong>Sertifikat:</strong> {{ $event->mengeluarkan_sertifikat ? '✅ Ya' : '❌ Tidak' }}</li>
                </ul>
            </div>

            <div class="text-end mt-3 mt-md-0">
                <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-danger">
                    Lihat Detail
                </a>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-info">Belum ada event yang tersedia.</div>
@endforelse
@endsection
