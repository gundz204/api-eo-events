@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm border-dark">
        <div class="card-body">
            <h3 class=" fw-bold mb-4">Pendaftaran Event: {{ $event->nama }}</h3>

            {{-- Tampilkan pesan sukses --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tampilkan pesan error --}}
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Informasi event --}}
            <ul class="list-unstyled text-dark mb-4">
                <li><strong>📍 Lokasi:</strong> {{ $event->lokasi }}</li>
                <li><strong>🕒 Waktu:</strong> {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('d M Y H:i') }}</li>
                <li><strong>Jenis:</strong> <span class="text-uppercase text-danger">{{ $event->jenis }}</span></li>
                <li><strong>Kuota:</strong> {{ $event->kuota }}</li>
                <li><strong>Sertifikat:</strong> {{ $event->mengeluarkan_sertifikat ? '✅ Ya' : '❌ Tidak' }}</li>
            </ul>

            {{-- Form daftar --}}
            <form method="POST" action="{{ route('events.register', $event->id) }}">
                @csrf
                <p class="fw-semibold">Apakah kamu yakin ingin mendaftar ke event ini?</p>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">Daftar</button>
                    <a href="{{ route('events.index') }}" class="btn btn-outline-dark">Kembali</a>
                </div>
            </form>

            {{-- QR Code jika berhasil --}}
            @if (session('qrcode_url'))
                <div class="mt-5 text-center">
                    <h5 class="fw-bold">Scan QR Code untuk Verifikasi Kehadiran</h5>
                    <div class="my-3">
                        {!! QrCode::size(200)->generate(session('qrcode_url')) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
