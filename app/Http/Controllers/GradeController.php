<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    /**
     * Display a listing of grades.
     */
    public function index(Request $request)
    {
        $user     = $request->user();
        $schoolId = $user->school_id;

        $query = Grade::with(['studentProfile.user', 'subject', 'teacherProfile.user'])
            ->whereHas('studentProfile.user', fn($q) => $q->where('school_id', $schoolId));

        // Filter based on role
        if ($user->isTeacher()) {
            $teacherProfile = $user->teacherProfile;
            // Hanya nilai dari mata pelajaran yang diampu guru ini
            $ownSubjectIds = $teacherProfile
                ? $teacherProfile->subjects()->pluck('subjects.id')
                : collect();

            $query->where('teacher_profile_id', $teacherProfile?->id)
                  ->whereIn('subject_id', $ownSubjectIds);

        } elseif ($user->isStudent()) {
            $query->where('student_profile_id', $user->studentProfile?->id);
        }
        // Admin / school_admin melihat semua nilai di sekolahnya

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

        $user     = auth()->user();
        $schoolId = $user->school_id;

        [$students, $subjects, $teachers] = $this->resolveFormData($user, $schoolId);

        return view('grades.create', compact('students', 'subjects', 'teachers'));
    }

    /**
     * Store a newly created grade in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeTeacherOrAdmin();

        $user     = $request->user();
        $schoolId = $user->school_id;

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

        if ($user->isTeacher()) {
            $teacherProfile = $user->teacherProfile;

            // Paksa teacher_profile_id ke diri sendiri
            $validated['teacher_profile_id'] = $teacherProfile?->id;

            // Pastikan subject yang dipilih memang diampu guru ini
            $ownSubjectIds = $teacherProfile
                ? $teacherProfile->subjects()->pluck('subjects.id')
                : collect();

            if (! $ownSubjectIds->contains($validated['subject_id'])) {
                return back()->withErrors(['subject_id' => 'Anda hanya dapat memberikan nilai untuk mata pelajaran yang Anda ampu.'])->withInput();
            }

            // Pastikan siswa yang dipilih memang terdaftar di sekolah ini
            $studentBelongsToSchool = StudentProfile::whereId($validated['student_profile_id'])
                ->whereHas('user', fn($q) => $q->where('school_id', $schoolId))
                ->exists();

            if (! $studentBelongsToSchool) {
                return back()->withErrors(['student_profile_id' => 'Siswa tidak ditemukan di sekolah Anda.'])->withInput();
            }
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
        $this->authorizeViewGrade($grade);

        $grade->load(['studentProfile.user', 'subject', 'teacherProfile.user']);

        return view('grades.show', compact('grade'));
    }

    /**
     * Show the form for editing the specified grade.
     */
    public function edit(Grade $grade)
    {
        $this->authorizeTeacherOrAdmin();

        $user     = auth()->user();
        $schoolId = $user->school_id;

        // Pastikan grade ini milik sekolah yang sama
        if ($grade->studentProfile?->user?->school_id !== $schoolId) {
            abort(403);
        }

        // Guru hanya bisa edit nilai yang ia buat sendiri
        if ($user->isTeacher() && $grade->teacher_profile_id !== $user->teacherProfile?->id) {
            abort(403, 'Anda hanya dapat mengedit nilai yang Anda berikan sendiri.');
        }

        $grade->load(['studentProfile.user', 'subject', 'teacherProfile.user']);

        [$students, $subjects, $teachers] = $this->resolveFormData($user, $schoolId);

        return view('grades.edit', compact('grade', 'students', 'subjects', 'teachers'));
    }

    /**
     * Update the specified grade in storage.
     */
    public function update(Request $request, Grade $grade)
    {
        $this->authorizeTeacherOrAdmin();

        $user     = $request->user();
        $schoolId = $user->school_id;

        // Guru hanya bisa edit nilai yang ia buat sendiri
        if ($user->isTeacher() && $grade->teacher_profile_id !== $user->teacherProfile?->id) {
            abort(403, 'Anda hanya dapat mengedit nilai yang Anda berikan sendiri.');
        }

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

        if ($user->isTeacher()) {
            $teacherProfile = $user->teacherProfile;

            $validated['teacher_profile_id'] = $teacherProfile?->id;

            $ownSubjectIds = $teacherProfile
                ? $teacherProfile->subjects()->pluck('subjects.id')
                : collect();

            if (! $ownSubjectIds->contains($validated['subject_id'])) {
                return back()->withErrors(['subject_id' => 'Anda hanya dapat memberikan nilai untuk mata pelajaran yang Anda ampu.'])->withInput();
            }

            $studentBelongsToSchool = StudentProfile::whereId($validated['student_profile_id'])
                ->whereHas('user', fn($q) => $q->where('school_id', $schoolId))
                ->exists();

            if (! $studentBelongsToSchool) {
                return back()->withErrors(['student_profile_id' => 'Siswa tidak ditemukan di sekolah Anda.'])->withInput();
            }
        }

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

        $user = auth()->user();

        // Guru hanya bisa hapus nilai yang ia buat sendiri
        if ($user->isTeacher() && $grade->teacher_profile_id !== $user->teacherProfile?->id) {
            abort(403, 'Anda hanya dapat menghapus nilai yang Anda berikan sendiri.');
        }

        $grade->delete();

        return redirect()->route('grades.index')
            ->with('success', 'Nilai berhasil dihapus!');
    }

    // ─── Private Helpers ─────────────────────────────────────────

    /**
     * Kembalikan [students, subjects, teachers] yang sesuai dengan role user.
     * Guru hanya melihat: subject yang diampu + siswa di sekolahnya.
     * Admin melihat semua.
     */
    private function resolveFormData(User $user, int $schoolId): array
    {
        if ($user->isTeacher()) {
            $teacherProfile = $user->teacherProfile;

            // Hanya subject yang diampu guru ini
            $subjects = $teacherProfile
                ? $teacherProfile->subjects()->where('school_id', $schoolId)->get()
                : collect();

            // Siswa yang ter-enroll di sekolah ini (aktif)
            $students = StudentProfile::with('user')
                ->whereHas('user', fn($q) => $q->where('school_id', $schoolId))
                ->whereHas('enrollments', fn($q) => $q->where('status', 'active'))
                ->get();

            $teachers = collect(); // guru tidak perlu pilih guru lain
        } else {
            // Admin / school_admin: lihat semua
            $students = StudentProfile::with('user')
                ->whereHas('user', fn($q) => $q->where('school_id', $schoolId))
                ->get();

            $subjects = Subject::where('school_id', $schoolId)->get();

            $teachers = TeacherProfile::with('user')
                ->whereHas('user', fn($q) => $q->where('school_id', $schoolId))
                ->get();
        }

        return [$students, $subjects, $teachers];
    }

    /**
     * Ensure current user may view the given grade.
     */
    private function authorizeViewGrade(Grade $grade): void
    {
        $user = auth()->user();

        if ($user->isStudent() && $grade->student_profile_id !== $user->studentProfile?->id) {
            abort(403);
        }

        if ($user->isTeacher() && $grade->teacher_profile_id !== $user->teacherProfile?->id) {
            abort(403);
        }
    }

    /**
     * Ensure the current user is a teacher or admin.
     */
    private function authorizeTeacherOrAdmin(): void
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isTeacher() && ! $user->isSchoolAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }
    }
}
