@extends('layouts.app')

@section('content')
<h3>Event yang Kamu Hadiri</h3>

@if ($registrations->count())
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Nama Event</th>
                <th>Lokasi</th>
                <th>Waktu</th>
                <th>Sertifikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registrations as $reg)
            <tr>
                <td>{{ $reg->event->nama }}</td>
                <td>{{ $reg->event->lokasi }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($reg->event->waktu_mulai)->format('d M Y H:i') }} -
                    {{ \Carbon\Carbon::parse($reg->event->waktu_selesai)->format('H:i') }}
                </td>
                <td>
                    @if ($reg->event->mengeluarkan_sertifikat)
                    <a href="{{ url('/participants/' . $reg->id . '/certificate') }}" class="btn btn-sm btn-success">
                        Lihat Sertifikat
                    </a>
                    @else
                    Tidak Tersedia
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p>Kamu belum menghadiri event apapun.</p>
@endif
@endsection