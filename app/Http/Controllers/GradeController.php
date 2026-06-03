<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    /**
     * Display a listing of grades.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $query = Grade::with(['studentProfile.user', 'subject', 'teacherProfile.user'])
            ->whereHas('studentProfile.user', fn($q) => $q->where('school_id', $schoolId));

        // Filter based on role
        if ($user->isTeacher()) {
            $query->where('teacher_profile_id', $user->teacherProfile?->id);
        } elseif ($user->isStudent()) {
            $query->where('student_profile_id', $user->studentProfile?->id);
        }
        // Admin/school_admin sees all within their school

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('studentProfile.user', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('subject', fn($q2) => $q2->where('nama', 'like', "%{$search}%"));
            });
        }

        // Semester filter
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $grades = $query->latest()->paginate(15)->withQueryString();

        return view('grades.index', compact('grades'));
    }

    /**
     * Show the form for creating a new grade.
     */
    public function create()
    {
        $this->authorizeTeacherOrAdmin();

        $schoolId = auth()->user()->school_id;
        $students = StudentProfile::with('user')->whereHas('user', fn($q) => $q->where('school_id', $schoolId))->get();
        $subjects = Subject::where('school_id', $schoolId)->get();
        $teachers = TeacherProfile::with('user')->whereHas('user', fn($q) => $q->where('school_id', $schoolId))->get();

        return view('grades.create', compact('students', 'subjects', 'teachers'));
    }

    /**
     * Store a newly created grade in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeTeacherOrAdmin();

        $validated = $request->validate([
            'student_profile_id' => ['required', 'exists:student_profiles,id'],
            'subject_id'         => ['required', 'exists:subjects,id'],
            'teacher_profile_id' => ['nullable', 'exists:teacher_profiles,id'],
            'nilai'              => ['required', 'numeric', 'min:0', 'max:100'],
            'tipe'               => ['required', 'in:tugas,uts,uas,praktik'],
            'semester'           => ['required', 'string', 'max:50'],
            'tahun_ajaran'       => ['required', 'string', 'max:20'],
            'catatan'            => ['nullable', 'string', 'max:1000'],
        ]);

        // Auto-assign teacher if current user is a teacher
        $user = $request->user();
        if ($user->isTeacher() && ! $request->filled('teacher_profile_id')) {
            $validated['teacher_profile_id'] = $user->teacherProfile?->id;
        }

        Grade::create($validated);

        return redirect()->route('grades.index')
            ->with('success', 'Nilai berhasil ditambahkan!');
    }

    /**
     * Display the specified grade.
     */
    public function show(Grade $grade)
    {
        $grade->load(['studentProfile.user', 'subject', 'teacherProfile.user']);

        return view('grades.show', compact('grade'));
    }

    /**
     * Show the form for editing the specified grade.
     */
    public function edit(Grade $grade)
    {
        $this->authorizeTeacherOrAdmin();

        $schoolId = auth()->user()->school_id;

        // Pastikan grade ini milik sekolah yang sama
        if ($grade->studentProfile?->user?->school_id !== $schoolId) {
            abort(403);
        }

        $grade->load(['studentProfile.user', 'subject', 'teacherProfile.user']);
        $students = StudentProfile::with('user')->whereHas('user', fn($q) => $q->where('school_id', $schoolId))->get();
        $subjects = Subject::where('school_id', $schoolId)->get();
        $teachers = TeacherProfile::with('user')->whereHas('user', fn($q) => $q->where('school_id', $schoolId))->get();

        return view('grades.edit', compact('grade', 'students', 'subjects', 'teachers'));
    }

    /**
     * Update the specified grade in storage.
     */
    public function update(Request $request, Grade $grade)
    {
        $this->authorizeTeacherOrAdmin();

        $validated = $request->validate([
            'student_profile_id' => ['required', 'exists:student_profiles,id'],
            'subject_id'         => ['required', 'exists:subjects,id'],
            'teacher_profile_id' => ['nullable', 'exists:teacher_profiles,id'],
            'nilai'              => ['required', 'numeric', 'min:0', 'max:100'],
            'tipe'               => ['required', 'in:tugas,uts,uas,praktik'],
            'semester'           => ['required', 'string', 'max:50'],
            'tahun_ajaran'       => ['required', 'string', 'max:20'],
            'catatan'            => ['nullable', 'string', 'max:1000'],
        ]);

        $grade->update($validated);

        return redirect()->route('grades.index')
            ->with('success', 'Nilai berhasil diperbarui!');
    }

    /**
     * Remove the specified grade from storage.
     */
    public function destroy(Grade $grade)
    {
        $this->authorizeTeacherOrAdmin();

        $grade->delete();

        return redirect()->route('grades.index')
            ->with('success', 'Nilai berhasil dihapus!');
    }

    /**
     * Ensure the current user is a teacher or admin.
     */
    private function authorizeTeacherOrAdmin(): void
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isTeacher()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }
    }
}
