<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceSessionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = AttendanceSession::with(['subject', 'classroom'])
            ->where('school_id', $user->school_id)
            ->whereDate('date', today());

        if ($user->isTeacher()) {
            $query->where('teacher_id', $user->id);
        }

        $sessions = $query->latest()->get();

        return view('attendances.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $user = auth()->user();

        // Guru hanya melihat subject yang diampu sendiri
        if ($user->isTeacher()) {
            $teacherProfile = $user->teacherProfile;
            $subjects = $teacherProfile
                ? $teacherProfile->subjects()->where('subjects.school_id', $user->school_id)->get()
                : collect();
        } else {
            $subjects = Subject::where('school_id', $user->school_id)->get();
        }


        $classrooms = Classroom::where('school_id', $user->school_id)->get();

        return view('attendances.sessions.create', compact('subjects', 'classrooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'   => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        $session = AttendanceSession::create([
            'school_id'    => auth()->user()->school_id,
            'teacher_id'   => auth()->id(),
            'subject_id'   => $validated['subject_id'],
            'classroom_id' => $validated['classroom_id'],
            'date'         => today(),
            'start_time'   => now(),
            'qr_code_token'=> Str::random(40),
            'status'       => 'active',
        ]);

        return redirect()->route('attendance-sessions.show', $session)
            ->with('success', 'Sesi absensi berhasil dibuka!');
    }

    public function show(AttendanceSession $attendanceSession)
    {
        if ($attendanceSession->school_id !== auth()->user()->school_id) abort(403);
        
        $attendanceSession->load(['subject', 'classroom', 'attendances.student']);
        
        return view('attendances.sessions.show', compact('attendanceSession'));
    }

    public function refreshQr(AttendanceSession $attendanceSession)
    {
        if ($attendanceSession->school_id !== auth()->user()->school_id) abort(403);
        
        $attendanceSession->update([
            'qr_code_token' => Str::random(40)
        ]);

        return back()->with('success', 'QR Code berhasil di-refresh!');
    }

    public function close(AttendanceSession $attendanceSession)
    {
        if ($attendanceSession->school_id !== auth()->user()->school_id) abort(403);

        $attendanceSession->update([
            'status'   => 'closed',
            'end_time' => now(),
        ]);

        return redirect()->route('attendance-sessions.index')
            ->with('success', 'Sesi absensi untuk kelas ini telah ditutup.');
    }
}
