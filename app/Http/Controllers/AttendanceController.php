<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\StudentProfile;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    /**
     * Summary / history view.
     */
    public function index(Request $request)
    {
        $user     = $request->user();
        $schoolId = $user->school_id;

        if ($user->isStudent()) {
            // Siswa melihat riwayat absensinya sendiri
            $records = Attendance::with(['session.subject', 'session.classroom'])
                ->where('student_id', $user->id)
                ->orderByDesc('date')
                ->paginate(20);

            $total   = Attendance::where('student_id', $user->id)->count();
            $summary = [
                'present' => Attendance::where('student_id', $user->id)->where('status', 'present')->count(),
                'late'    => Attendance::where('student_id', $user->id)->where('status', 'late')->count(),
                'sick'    => Attendance::where('student_id', $user->id)->where('status', 'sick')->count(),
                'excused' => Attendance::where('student_id', $user->id)->where('status', 'excused')->count(),
                'absent'  => Attendance::where('student_id', $user->id)->where('status', 'absent')->count(),
            ];

            return view('attendances.student', compact('records', 'summary', 'total'));
        }

        // Teacher / Admin: tampilkan daftar sesi absensi
        $sessionsQuery = AttendanceSession::with(['subject', 'classroom', 'teacher'])
            ->where('school_id', $schoolId);

        if ($user->isTeacher()) {
            $sessionsQuery->where('teacher_id', $user->id);
        }

        $sessions = $sessionsQuery->orderByDesc('date')->paginate(20);

        return view('attendances.index', compact('sessions'));
    }

    /**
     * Buat sesi absensi baru.
     */
    public function create(Request $request)
    {
        $user     = $request->user();
        if ($user->isStudent()) abort(403);

        $schoolId = $user->school_id;

        $subjects = $user->isTeacher()
            ? $user->teacherProfile->subjects->where('school_id', $schoolId)
            : Subject::where('school_id', $schoolId)->get();

        $classrooms = Classroom::where('school_id', $schoolId)->get();

        $students = StudentProfile::with('user')
            ->whereHas('user', fn($q) => $q->where('school_id', $schoolId))
            ->get();

        return view('attendances.create', compact('subjects', 'classrooms', 'students'));
    }


    /**
     * Simpan sesi absensi + rekaman kehadiran siswa.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->isStudent()) abort(403);

        $validated = $request->validate([
            'subject_id'           => ['required', 'exists:subjects,id'],
            'classroom_id'         => ['required', 'exists:classrooms,id'],
            'date'                 => ['required', 'date'],
            'start_time'           => ['required'],
            'attendance'           => ['required', 'array'],
            'attendance.*.status'  => ['required', 'in:present,late,absent,excused,sick'],
        ]);

        $schoolId  = $user->school_id;
        $teacherId = $user->id;

        // Buat atau ambil sesi absensi hari ini
        $session = AttendanceSession::firstOrCreate(
            [
                'school_id'    => $schoolId,
                'teacher_id'   => $teacherId,
                'subject_id'   => $validated['subject_id'],
                'classroom_id' => $validated['classroom_id'],
                'date'         => $validated['date'],
            ],
            [
                'start_time'    => $validated['start_time'],
                'qr_code_token' => Str::random(32),
                'status'        => 'active',
            ]
        );

        // Simpan/update rekaman kehadiran per siswa
        foreach ($validated['attendance'] as $studentId => $data) {
            Attendance::updateOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'student_id'            => $studentId,
                ],
                [
                    'school_id' => $schoolId,
                    'date'      => $validated['date'],
                    'status'    => $data['status'],
                    'notes'     => $data['notes'] ?? null,
                ]
            );
        }

        return redirect()->route('attendances.index')
            ->with('success', 'Absensi berhasil disimpan!');
    }

    /**
     * Tampilkan rekap absensi per sesi.
     */
    public function show(Request $request, AttendanceSession $attendance)
    {
        $session = $attendance;

        // Pastikan sesi milik sekolah yang sama
        if ($session->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $session->load(['subject', 'classroom', 'teacher', 'attendances.student']);

        $schoolId = auth()->user()->school_id;
        $students = StudentProfile::with('user')
            ->whereHas('user', fn($q) => $q->where('school_id', $schoolId))
            ->get();

        // Build grid: student_id -> status
        $grid = $session->attendances->keyBy('student_id');

        return view('attendances.show', compact('session', 'students', 'grid'));
    }
}
