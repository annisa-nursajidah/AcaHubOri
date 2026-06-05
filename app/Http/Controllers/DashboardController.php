<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\Classroom;
use App\Models\Attendance;
use App\Models\Enrollment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the role-appropriate dashboard with statistics.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'parent') {
            return redirect()->route('parent.dashboard');
        }

        // Statistik Dummy untuk Demo
        $data = [
            'user'          => $user,
        ];

        // ─── Scope Data based on Role ───
        if ($user->isAdmin()) {
            // Super Admin melihat statistik seluruh platform AcaHub
            $data['totalSchools']  = \App\Models\School::count();
            $data['totalStudents'] = User::where('role', 'student')->count();
            $data['totalTeachers'] = User::where('role', 'teacher')->count();
            $data['totalSubjects'] = Subject::count();
            $data['totalRevenue']  = \App\Models\SchoolSubscription::where('status', 'active')->sum('total_price');
        } elseif ($user->isSchoolAdmin()) {
            // Admin Sekolah hanya melihat statistik sekolahnya
            $school = \App\Models\School::find($user->school_id);
            $totalStudents = User::where('school_id', $user->school_id)->where('role', 'student')->count();
            $totalTeachers = User::where('school_id', $user->school_id)->where('role', 'teacher')->count();
            $totalParents  = User::where('school_id', $user->school_id)->where('role', 'parent')->count();
            $data['totalStudents']   = $totalStudents;
            $data['totalTeachers']   = $totalTeachers;
            $data['totalSubjects']   = Subject::where('school_id', $user->school_id)->count();
            $data['totalClassrooms'] = Classroom::where('school_id', $user->school_id)->count();

            // Sisa Kuota
            $data['totalQuota']     = $school ? $school->totalAccountsQuota() : 0;
            $data['remainingQuota'] = $school ? $school->remainingAccountsQuota() : 0;

            // ── Chart 1: Komposisi Pengguna (Donut) ──
            $data['userComposition'] = [
                'labels' => ['Siswa', 'Guru', 'Wali Murid'],
                'data'   => [$totalStudents, $totalTeachers, $totalParents],
            ];

            // ── Chart 2: Siswa per Kelas (Bar) ──
            $classroomsWithStudents = Classroom::where('school_id', $user->school_id)
                ->withCount(['enrollments as student_count'])
                ->orderBy('tingkat')
                ->get()
                ->map(fn($c) => [
                    'name'  => $c->nama,
                    'count' => $c->student_count,
                ]);
            $data['classroomStudents'] = $classroomsWithStudents;

            // ── Chart 3: Tren Kehadiran 6 Bulan Terakhir (Line) ──
            $attendanceTrend = collect();
            for ($i = 5; $i >= 0; $i--) {
                $month    = Carbon::now()->subMonths($i);
                $total    = Attendance::where('school_id', $user->school_id)
                                ->whereYear('date', $month->year)
                                ->whereMonth('date', $month->month)
                                ->count();
                $hadir    = Attendance::where('school_id', $user->school_id)
                                ->whereYear('date', $month->year)
                                ->whereMonth('date', $month->month)
                                ->where('status', 'hadir')
                                ->count();
                $attendanceTrend->push([
                    'label'   => $month->translatedFormat('M Y'),
                    'total'   => $total,
                    'hadir'   => $hadir,
                    'pct'     => $total > 0 ? round($hadir / $total * 100, 1) : 0,
                ]);
            }
            $data['attendanceTrend'] = $attendanceTrend;
        } else {
            // Teacher & Student — scoped to their school
            $data['totalStudents'] = User::where('role', 'student')->where('school_id', $user->school_id)->count();
            $data['totalTeachers'] = User::where('role', 'teacher')->where('school_id', $user->school_id)->count();
            $data['totalSubjects'] = Subject::where('school_id', $user->school_id)->count();
            $data['totalGrades']   = Grade::whereHas('subject', fn($q) => $q->where('school_id', $user->school_id))->count();
        }

        // Grade distribution for chart — scoped to current user's school via subject
        $schoolId  = $user->school_id;
        $allGrades = Grade::whereHas('subject', fn($q) => $q->where('school_id', $schoolId))->get();
        $data['gradeDistribution'] = [
            'A' => $allGrades->where('nilai', '>=', 90)->count(),
            'B' => $allGrades->whereBetween('nilai', [75, 89.99])->count(),
            'C' => $allGrades->whereBetween('nilai', [60, 74.99])->count(),
            'D' => $allGrades->whereBetween('nilai', [50, 59.99])->count(),
            'E' => $allGrades->where('nilai', '<', 50)->count(),
        ];

        // Average per subject for chart — scoped to current school
        $subjectAverages = Grade::selectRaw('subject_id, AVG(nilai) as avg')
            ->whereHas('subject', fn($q) => $q->where('school_id', $schoolId))
            ->groupBy('subject_id')
            ->with('subject')
            ->get()
            ->map(fn($g) => [
                'name' => $g->subject->nama ?? '?',
                'avg'  => round($g->avg, 1),
            ]);
        $data['subjectAverages'] = $subjectAverages;

        // Role-specific data
        if ($user->isStudent() && $user->studentProfile) {
            $myGrades = Grade::where('student_profile_id', $user->studentProfile->id)->get();
            $data['myGradeCount']   = $myGrades->count();
            $data['myAverage']      = $myGrades->avg('nilai');
            $data['myHighest']      = $myGrades->max('nilai');
            $data['myLowest']       = $myGrades->min('nilai');
        }

        if ($user->isTeacher() && $user->teacherProfile) {
            $teacherProfileId = $user->teacherProfile->id;
            $data['mySubjects']    = $user->teacherProfile->subjects()->count();
            $data['myGradesGiven'] = Grade::where('teacher_profile_id', $teacherProfileId)->count();

            // ── Chart: Rata-rata Nilai per Mapel yang Diajarkan ──
            $teacherSubjectAvg = Grade::selectRaw('subject_id, AVG(nilai) as avg, COUNT(*) as total_nilai')
                ->where('teacher_profile_id', $teacherProfileId)
                ->groupBy('subject_id')
                ->with('subject')
                ->get()
                ->map(fn($g) => [
                    'name'        => $g->subject->nama ?? '?',
                    'avg'         => round($g->avg, 1),
                    'total_nilai' => $g->total_nilai,
                    'status'      => $g->avg >= 75 ? 'lulus' : 'perlu_perhatian',
                ]);
            $data['teacherSubjectAvg'] = $teacherSubjectAvg;

            // ── Chart: Distribusi Nilai Siswa yang Diajar ──
            $myStudentGrades = Grade::where('teacher_profile_id', $teacherProfileId)->get();
            $data['teacherGradeDist'] = [
                'A' => $myStudentGrades->where('nilai', '>=', 90)->count(),
                'B' => $myStudentGrades->whereBetween('nilai', [75, 89.99])->count(),
                'C' => $myStudentGrades->whereBetween('nilai', [60, 74.99])->count(),
                'D' => $myStudentGrades->whereBetween('nilai', [50, 59.99])->count(),
                'E' => $myStudentGrades->where('nilai', '<', 50)->count(),
            ];

            // ── Ringkasan ──
            $data['teacherAvgOverall'] = $myStudentGrades->count() > 0
                ? round($myStudentGrades->avg('nilai'), 1)
                : 0;
        }

        return view('dashboard', $data);
    }
}
