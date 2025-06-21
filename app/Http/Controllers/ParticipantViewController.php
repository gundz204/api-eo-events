<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class ParticipantViewController extends Controller
{
    public function index($eventId, Request $request)
    {
        $event = Event::findOrFail($eventId);

        $registrations = EventRegistration::with('user')
            ->where('event_id', $eventId)
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->get();

        return view('participants.index', compact('event', 'registrations'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_kehadiran' => 'required|in:belum,hadir,tidak',
        ]);

        $registration = EventRegistration::findOrFail($id);
        $registration->status_kehadiran = $request->status_kehadiran;
        $registration->save();

        return back()->with('success', 'Status kehadiran diperbarui.');
    }

    public function scanQR($id)
    {
        $registration = EventRegistration::find($id);

        if (!$registration) {
            return back()->with('error', 'Registrasi tidak ditemukan.');
        }

        if ($registration->status_kehadiran === 'belum') {
            $registration->status_kehadiran = 'hadir';
            $registration->save();
        }

        return back()->with('success', 'Kehadiran diverifikasi untuk: ' . $registration->user->name);
    }

    public function statistic($eventId)
    {
        $event = Event::findOrFail($eventId);

        $total = EventRegistration::where('event_id', $eventId)->count();
        $hadir = EventRegistration::where('event_id', $eventId)->where('status_kehadiran', 'hadir')->count();
        $tidak = EventRegistration::where('event_id', $eventId)->where('status_kehadiran', 'tidak')->count();
        $belum = EventRegistration::where('event_id', $eventId)->where('status_kehadiran', 'belum')->count();

        $kuota = $event->kuota ?: 1;
        $persentase = round(($total / $kuota) * 100, 2);

        return view('participants.statistic', compact(
            'event', 'total', 'hadir', 'tidak', 'belum', 'kuota', 'persentase'
        ));
    }
}
