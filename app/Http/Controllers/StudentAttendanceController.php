<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    /**
     * Menampilkan riwayat absensi siswa.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role !== 'student') abort(403);

        $attendances = Attendance::with(['session.subject', 'session.teacher'])
            ->where('student_id', $user->id)
            ->latest('scanned_at')
            ->paginate(15);

        return view('student.attendances.index', compact('attendances'));
    }

    /**
     * Menampilkan UI Kamera Scanner QR.
     * Bisa diakses via Scanner Manual atau langsung dari link URL QR.
     */
    public function scan(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'student') abort(403);

        $prefilledToken = $request->query('token', '');

        return view('student.attendances.scan', compact('prefilledToken'));
    }

    /**
     * Memproses Token QR dari Kamera Scanner.
     */
    public function processScan(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'student') abort(403);

        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $token = $request->input('qr_token');

        // Cari sesi absen yang aktif dengan token ini
        // Parsing URL dulu jika kamera mengirim string berupa full URL
        if(filter_var($token, FILTER_VALIDATE_URL)) {
             $parsedUrl = parse_url($token);
             if(isset($parsedUrl['query'])) {
                 parse_str($parsedUrl['query'], $queryParams);
                 if(isset($queryParams['token'])) {
                     $token = $queryParams['token'];
                 }
             }
        }

        $session = AttendanceSession::where('qr_code_token', $token)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid, sudah kadaluarsa, atau Kelas telah ditutup.'
            ], 404);
        }

        // Cek apakah siswa ini sudah absen di sesi yang sama
        $alreadyScanned = Attendance::where('attendance_session_id', $session->id)
            ->where('student_id', $user->id)
            ->exists();

        if ($alreadyScanned) {
             return response()->json([
                'success' => true, // True agar flow scanner berhenti, tapi kasih pesan ini
                'message' => 'Anda sudah tercatat HADIR di sesi kelas ini sebelumnya.',
                'already_scanned' => true
            ]);
        }
        
        // Kalkulasi keterlambatan. Jika scan lebih dari 15 menit dari start_time
        $status = 'present';
        $diffMinutes = $session->start_time->diffInMinutes(now(), false);
        if ($diffMinutes > 15) {
            $status = 'late';
        }

        Attendance::create([
            'school_id'             => $user->school_id,
            'attendance_session_id' => $session->id,
            'student_id'            => $user->id,
            'date'                  => today(),
            'status'                => $status,
            'scanned_at'            => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil! Anda tercatat ' . ($status === 'late' ? 'TERLAMBAT' : 'HADIR') . ' pada ' . $session->subject->name,
            'redirect' => route('student.attendances.index')
        ]);
    }
}
