<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        return response()->json(Event::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'required|string',
            'jenis' => 'required|in:gratis,berbayar',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'kuota' => 'required|integer|min:1',
            'mengeluarkan_sertifikat' => 'required|boolean',
            'image' => 'nullable|string',
            'form_pendaftaran' => 'nullable|string',
            'is_active' => 'boolean',
            'foto' => 'nullable|string',
        ]);

        $event = Event::create($validated);

        return response()->json([
            'message' => 'Event berhasil dibuat.',
            'event' => $event,
        ], 201);
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'sometimes|required|string',
            'jenis' => 'sometimes|required|in:gratis,berbayar',
            'waktu_mulai' => 'sometimes|required|date',
            'waktu_selesai' => 'sometimes|required|date|after:waktu_mulai',
            'kuota' => 'sometimes|required|integer|min:1',
            'mengeluarkan_sertifikat' => 'sometimes|required|boolean',
            'image' => 'nullable|string',
            'form_pendaftaran' => 'nullable|string',
            'is_active' => 'boolean',
            'foto' => 'nullable|string',
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Event berhasil diperbarui.',
            'event' => $event,
        ]);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->is_active = 0;
        $event->save();

        return response()->json(['message' => 'Event dinonaktifkan.']);
    }

    public function myEvents()
    {
        $user = Auth::user();

        $registrations = EventRegistration::with('event')
            ->where('user_id', $user->id)
            ->where('status_kehadiran', 'hadir')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $registrations
        ]);
    }

    public function registerToEvent(Request $request, $eventId)
    {
        $user = Auth::user();

        $event = Event::where('id', $eventId)->where('is_active', 1)->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan atau tidak aktif.'
            ], 404);
        }

        $existing = EventRegistration::where('user_id', $user->id)
            ->where('event_id', $eventId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah terdaftar pada event ini.'
            ], 409);
        }

        $registration = EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $eventId,
            'status_kehadiran' => 'belum',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil.',
            'data' => $registration
        ], 201);
    }
}
