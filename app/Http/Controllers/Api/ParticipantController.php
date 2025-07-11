<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ParticipantController extends Controller
{
    public function index(Request $request, $eventId)
    {
        $event = Event::find($eventId);
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ada'
            ], 404);
        }

        $query = EventRegistration::with('user')
            ->where('event_id', $eventId);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        return response()->json([
            'success' => true,
            'event' => $event->nama,
            'data' => $query->get()
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_kehadiran' => 'required|in:belum,hadir,tidak',
        ]);

        $registration = EventRegistration::findOrFail($id);
        $registration->status_kehadiran = $request->status_kehadiran;
        $registration->save();

        return response()->json([
            'success' => true,
            'message' => 'Status kehadiran berhasil diperbarui.',
            'data' => $registration
        ]);
    }
    public function updateStatusWithQR($id)
    {
        $registration = EventRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi tidak ditemukan.'
            ], 404);
        }

        if (strtolower($registration->status_pembayaran) !== 'diterima') {
            return response()->json([
                'success' => false,
                'message' => 'Status pembayaran belum diterima. Tidak dapat mengubah status kehadiran.',
                'data' => $registration
            ], 403);
        }

        if ($registration->status_kehadiran === 'belum') {
            $registration->status_kehadiran = 'hadir';
            $registration->save();

            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran otomatis diubah menjadi HADIR.',
                'data' => $registration
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Status kehadiran sudah diperbarui sebelumnya.',
            'data' => $registration
        ]);
    }
    public function updateStatusWithQRView($id)
    {
        $registration = EventRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi tidak ditemukan.'
            ], 404);
        }

        if ($registration->status_kehadiran === 'belum') {
            $registration->status_kehadiran = 'hadir';
            $registration->save();

            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran otomatis diubah menjadi HADIR.',
                'data' => $registration
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Status kehadiran sudah diperbarui sebelumnya.',
            'data' => $registration
        ]);
    }

    public function getEventStatistics($eventId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan.'
            ], 404);
        }

        // Total peserta
        $total = EventRegistration::where('event_id', $eventId)->count();

        // Hadir
        $hadir = EventRegistration::where('event_id', $eventId)
            ->where('status_kehadiran', 'hadir')
            ->count();

        // Tidak hadir atau belum hadir
        $tidak = EventRegistration::where('event_id', $eventId)
            ->whereIn('status_kehadiran', ['tidak', 'belum'])
            ->count();

        $kuota = $event->kuota > 0 ? $event->kuota : 1;
        $persentase = ($total / $kuota) * 100;

        return response()->json([
            'success' => true,
            'event' => $event->nama,
            'kuota' => $event->kuota,
            'jumlah_pendaftar' => $total,
            'hadir' => $hadir,
            'tidak_hadir' => $tidak,
            'persentase_kuota_terisi' => round($persentase, 2) . '%'
        ]);
    }

    public function generateCertificate($id)
    {
        $registration = EventRegistration::with(['user', 'event'])
            ->where('id', $id)
            ->where('status_kehadiran', 'hadir')
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Sertifikat tidak ada.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $registration
        ]);
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

    public function uploadBuktiPembayaran(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $registration = EventRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Data registrasi tidak ditemukan.'
            ], 404);
        }

        $file = $request->file('bukti_pembayaran');
        $filename = time() . '_' . $file->getClientOriginalName();

        $path = $file->storeAs('public/uploads/bukti-pembayaran', $filename);

        $registration->bukti_pembayaran = $filename;
        $registration->save();

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diunggah.',
            'data' => [
                'file_name' => $filename,
                'file_url' => asset('storage/uploads/bukti-pembayaran/' . $filename),
                'registration' => $registration
            ]
        ]);
    }


    public function updateStatusPembayaran($id)
    {
        $registration = EventRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Data registrasi tidak ditemukan.'
            ], 404);
        }

        $registration->status_pembayaran = 'diterima';
        $registration->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui menjadi DITERIMA.',
            'data' => $registration
        ]);
    }
}
