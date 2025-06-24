<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            'form_pendaftaran' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload file image dan foto jika ada
        $imagePath = null;
        $fotoPath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/uploads/events', $imageName);
            $imagePath = $imageName;
        }

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoName = Str::random(20) . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/uploads/events', $fotoName);
            $fotoPath = $fotoName;
        }

        $event = Event::create([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'lokasi' => $validated['lokasi'],
            'jenis' => $validated['jenis'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'kuota' => $validated['kuota'],
            'mengeluarkan_sertifikat' => $validated['mengeluarkan_sertifikat'],
            'form_pendaftaran' => $validated['form_pendaftaran'] ?? null,
            'is_active' => $validated['is_active'] ?? 1,
            'image' => $imagePath,
            'foto' => $fotoPath,
        ]);

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
            'form_pendaftaran' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($event->image && Storage::exists('public/uploads/events/' . $event->image)) {
                Storage::delete('public/uploads/events/' . $event->image);
            }

            $image = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/uploads/events', $imageName);
            $event->image = $imageName;
        }

        if ($request->hasFile('foto')) {
            if ($event->foto && Storage::exists('public/uploads/events/' . $event->foto)) {
                Storage::delete('public/uploads/events/' . $event->foto);
            }

            $foto = $request->file('foto');
            $fotoName = Str::random(20) . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/uploads/events', $fotoName);
            $event->foto = $fotoName;
        }

        $event->fill($validated);
        $event->save();

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
            ->whereIn('status_kehadiran', ['belum', 'tidak'])
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

        $statusPembayaran = strtolower($event->jenis) === 'gratis' ? 'diterima' : 'pending';

        $registration = EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $eventId,
            'status_kehadiran' => 'belum',
            'status_pembayaran' => $statusPembayaran,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil.',
            'data' => $registration
        ], 201);
    }
}
