<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Summary / history view.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isStudent()) {
            // Student sees own attendance
            $records = Attendance::with(['subject', 'teacherProfile.user'])
                ->where('student_profile_id', $user->studentProfile?->id)
                ->orderByDesc('tanggal')
                ->paginate(20);

            // Summary stats
            $total = Attendance::where('student_profile_id', $user->studentProfile?->id)->count();
            $summary = [
                'hadir' => Attendance::where('student_profile_id', $user->studentProfile?->id)->where('status', 'hadir')->count(),
                'izin'  => Attendance::where('student_profile_id', $user->studentProfile?->id)->where('status', 'izin')->count(),
                'sakit' => Attendance::where('student_profile_id', $user->studentProfile?->id)->where('status', 'sakit')->count(),
                'alpa'  => Attendance::where('student_profile_id', $user->studentProfile?->id)->where('status', 'alpa')->count(),
            ];

            return view('attendances.student', compact('records', 'summary', 'total'));
        }

        // Teacher / Admin: show attendance form or summary
        $subjects = [];
        if ($user->isTeacher() && $user->teacherProfile) {
            $subjects = $user->teacherProfile->subjects;
        } else {
            $subjects = Subject::all();
        }

        $students = StudentProfile::with('user')->get();

        return view('attendances.index', compact('subjects', 'students'));
    }

    /**
     * Show attendance marking form for a subject on a date.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        if ($user->isStudent()) abort(403);

        $subjectId = $request->get('subject_id');
        $tanggal   = $request->get('tanggal', now()->format('Y-m-d'));

        $subjects = $user->isTeacher()
            ? $user->teacherProfile->subjects
            : Subject::all();

        $students = StudentProfile::with('user')->get();

        // Get existing records for this date & subject
        $existing = [];
        if ($subjectId) {
            $existing = Attendance::where('subject_id', $subjectId)
                ->where('tanggal', $tanggal)
                ->pluck('status', 'student_profile_id')
                ->toArray();
        }

        return view('attendances.create', compact('subjects', 'students', 'subjectId', 'tanggal', 'existing'));
    }

    /**
     * Store/update attendance records (batch).
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->isStudent()) abort(403);

        $validated = $request->validate([
            'subject_id'         => ['required', 'exists:subjects,id'],
            'tanggal'            => ['required', 'date'],
            'attendance'         => ['required', 'array'],
            'attendance.*.status'=> ['required', 'in:hadir,izin,sakit,alpa'],
        ]);

        $teacherProfileId = $user->isTeacher()
            ? $user->teacherProfile->id
            : TeacherProfile::first()?->id;

        foreach ($validated['attendance'] as $studentId => $data) {
            Attendance::updateOrCreate(
                [
                    'student_profile_id'  => $studentId,
                    'subject_id'          => $validated['subject_id'],
                    'tanggal'             => $validated['tanggal'],
                ],
                [
                    'teacher_profile_id' => $teacherProfileId,
                    'status'             => $data['status'],
                    'keterangan'         => $data['keterangan'] ?? null,
                ]
            );
        }

        return redirect()->route('attendances.index')
            ->with('success', 'Absensi berhasil disimpan!');
    }

    /**
     * View attendance report per subject.
     */
    public function show(Request $request, $subjectId)
    {
        $subject = Subject::findOrFail($subjectId);
        $month   = $request->get('month', now()->format('Y-m'));

        $records = Attendance::with(['studentProfile.user'])
            ->where('subject_id', $subjectId)
            ->where('tanggal', 'like', $month . '%')
            ->orderBy('tanggal')
            ->get();

        $students = StudentProfile::with('user')->get();

        // Build grid: student -> date -> status
        $dates = $records->pluck('tanggal')->map(fn($d) => $d->format('Y-m-d'))->unique()->sort()->values();
        $grid  = [];
        foreach ($students as $s) {
            $row = [];
            foreach ($dates as $date) {
                $r = $records->first(fn($rec) => $rec->student_profile_id === $s->id && $rec->tanggal->format('Y-m-d') === $date);
                $row[$date] = $r?->status ?? null;
            }
            $grid[$s->id] = $row;
        }

        return view('attendances.show', compact('subject', 'month', 'students', 'dates', 'grid'));
    }
}
