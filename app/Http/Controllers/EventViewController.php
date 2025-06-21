<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventViewController extends Controller
{
    public function index()
    {
        $events = Event::where('is_active', 1)->latest()->get();
        return view('events.index', compact('events'));
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('events.show', compact('event'));
    }

    public function showRegisterForm($id)
    {
        $event = Event::findOrFail($id);
        return view('events.register', compact('event'));
    }

    public function register(Request $request, $eventId)
    {
        $user = Auth::user();

        $event = Event::where('id', $eventId)->where('is_active', 1)->first();

        if (!$event) {
            return redirect()->back()->with('error', 'Event tidak ditemukan atau tidak aktif.');
        }

        $existing = EventRegistration::where('user_id', $user->id)
            ->where('event_id', $eventId)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Kamu sudah terdaftar pada event ini.');
        }

        $registration = EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $eventId,
            'status_kehadiran' => 'belum',
        ]);

        return redirect()->route('events.register.form', $eventId)
            ->with([
                'success' => 'Pendaftaran berhasil.',
                'qrcode_url' => url('/api/participants/' . $registration->id . '/status'),
            ]);
    }

    public function create()
    {
        return view('events.create');
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
            'image' => 'nullable|image|max:2048',
            'foto' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        $fotoPath = null;

        if ($request->hasFile('image')) {
            $imageName = Str::random(20) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('public/uploads/events', $imageName);
            $imagePath = $imageName;
        }

        if ($request->hasFile('foto')) {
            $fotoName = Str::random(20) . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->storeAs('public/uploads/events', $fotoName);
            $fotoPath = $fotoName;
        }

        Event::create([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'lokasi' => $validated['lokasi'],
            'jenis' => $validated['jenis'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'kuota' => $validated['kuota'],
            'mengeluarkan_sertifikat' => $validated['mengeluarkan_sertifikat'],
            'form_pendaftaran' => $validated['form_pendaftaran'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'image' => $imagePath,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('events.index')->with('success', 'Event berhasil ditambahkan.');
    }

    public function myEventsView()
    {
        $user = Auth::user();

        $registrations = EventRegistration::with('event')
            ->where('user_id', $user->id)
            ->where('status_kehadiran', 'hadir')
            ->get();

        return view('events.my_events', compact('registrations'));
    }

    public function myRegisteredEvents()
    {
        $user = Auth::user();

        $registrations = EventRegistration::with('event')
            ->where('user_id', $user->id)
            ->where('status_kehadiran', ['tidak', 'belum'])
            ->get();

        return view('events.my_events_registered', [
            'registrations' => $registrations
        ]);
    }
}
