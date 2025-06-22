<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
        ]);

        $registration = EventRegistration::where('user_id', $request->user_id)
            ->where('event_id', $request->event_id)
            ->where('status_kehadiran', 'hadir')
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'User belum dinyatakan hadir pada event ini.'
            ], 403);
        }

        $user = User::find($request->user_id);
        $event = Event::find($request->event_id);

        $html = '
            <h1 style="text-align:center;">SERTIFIKAT</h1>
            <p style="text-align:center;">Diberikan kepada:</p>
            <h2 style="text-align:center;">' . $user->name . '</h2>
            <p style="text-align:center;">Sebagai peserta dalam acara:</p>
            <h3 style="text-align:center;">' . $event->nama . '</h3>
            <p style="text-align:center;">Pada tanggal: ' . $event->waktu_mulai . '</p>
        ';

        $pdf = Pdf::loadHTML($html);

        $fileName = 'sertifikat_' . $user->id . '_' . $event->id . '.pdf';
        $filePath = 'public/sertifikat/' . $fileName;

        Storage::put($filePath, $pdf->output());

        return response()->json([
            'success' => true,
            'message' => 'Sertifikat berhasil dibuat.',
            'file_url' => asset('storage/sertifikat/' . $fileName)
        ]);
    }

    public function generateById($user_id, $event_id)
    {
        $registration = EventRegistration::where('user_id', $user_id)
            ->where('event_id', $event_id)
            ->where('status_kehadiran', 'hadir')
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'User belum dinyatakan hadir pada event ini.'
            ], 403);
        }

        $user = User::find($user_id);
        $event = Event::find($event_id);

        $html = '
        <h1 style="text-align:center;">SERTIFIKAT</h1>
        <p style="text-align:center;">Diberikan kepada:</p>
        <h2 style="text-align:center;">' . $user->name . '</h2>
        <p style="text-align:center;">Sebagai peserta dalam acara:</p>
        <h3 style="text-align:center;">' . $event->nama . '</h3>
        <p style="text-align:center;">Pada tanggal: ' . date('d F Y', strtotime($event->waktu_mulai)) . '</p>
    ';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);

        $fileName = 'sertifikat_' . $user->id . '_' . $event->id . '.pdf';

        return $pdf->download($fileName); // ⬅️ langsung download, tidak disimpan
    }
}
