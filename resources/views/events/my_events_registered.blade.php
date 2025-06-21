@extends('layouts.app')

@section('content')
<h3 class="mb-4">🎫 Tiket Event Kamu</h3>

@if ($registrations->isEmpty())
    <div class="alert alert-info">Kamu belum mendaftar ke event manapun.</div>
@else
    <div class="row">
        @foreach ($registrations as $reg)
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 d-flex flex-column flex-md-row align-items-center">
                        <div class="flex-grow-1 me-md-4">
                            <h4 class="card-title fw-bold">{{ $reg->event->nama }}</h4>
                            <p class="mb-2">
                                <strong>📍 Lokasi:</strong> {{ $reg->event->lokasi }}
                            </p>
                            <p class="mb-2">
                                <strong>🕒 Tanggal:</strong> {{ \Carbon\Carbon::parse($reg->event->waktu_mulai)->format('d M Y H:i') }}
                                &mdash;
                                {{ \Carbon\Carbon::parse($reg->event->waktu_selesai)->format('d M Y H:i') }}
                            </p>
                            <p class="mb-0">
                                <strong>Status:</strong> {{ ucfirst($reg->status_kehadiran) }}
                            </p>
                        </div>
                        <div class="text-center mt-4 mt-md-0">
                            <div class="bg-white p-2 border rounded">
                                {!! QrCode::size(150)->generate(url('/api/participants/' . $reg->id . '/status')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
