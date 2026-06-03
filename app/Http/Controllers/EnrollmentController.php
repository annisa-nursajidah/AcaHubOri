<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $query = Enrollment::with(['studentProfile.user', 'classroom', 'academicYear'])
            ->whereHas('classroom', fn($q) => $q->where('school_id', $schoolId));

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $enrollments = $query->latest()->paginate(20);
        $classrooms  = Classroom::where('school_id', $schoolId)->orderBy('nama')->get();
        $years       = AcademicYear::where('school_id', $schoolId)->orderByDesc('tahun')->get();

        return view('enrollments.index', compact('enrollments', 'classrooms', 'years'));
    }

    public function create()
    {
        $schoolId   = auth()->user()->school_id;
        $students   = StudentProfile::with('user')->whereHas('user', fn($q) => $q->where('school_id', $schoolId))->get();
        $classrooms = Classroom::with('academicYear')->where('school_id', $schoolId)->orderBy('nama')->get();
        $years      = AcademicYear::where('school_id', $schoolId)->orderByDesc('tahun')->get();

        return view('enrollments.create', compact('students', 'classrooms', 'years'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_profile_id' => 'required|exists:student_profiles,id',
            'classroom_id'       => 'required|exists:classrooms,id',
            'academic_year_id'   => 'required|exists:academic_years,id',
        ]);

        // Check for duplicate
        $exists = Enrollment::where($validated)->exists();
        if ($exists) {
            return back()->withErrors(['student_profile_id' => 'Siswa sudah terdaftar di kelas ini untuk tahun ajaran yang dipilih.'])->withInput();
        }

        Enrollment::create($validated);

        return redirect()->route('enrollments.index')
            ->with('success', 'Siswa berhasil didaftarkan ke kelas.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('enrollments.index')
            ->with('success', 'Pendaftaran berhasil dihapus.');
    }
}
