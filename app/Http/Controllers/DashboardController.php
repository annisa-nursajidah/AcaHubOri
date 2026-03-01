<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;

class DashboardController extends Controller
{
    /**
     * Show the role-appropriate dashboard with statistics.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $data = [
            'user'          => $user,
        ];

        // ─── Scope Data based on Role ───
        if ($user->isAdmin()) {
            // Super Admin melihat statistik seluruh platform AcaHub
            $data['totalSchools']  = \App\Models\School::count();
            $data['totalStudents'] = User::where('role', 'student')->count();
            $data['totalTeachers'] = User::where('role', 'teacher')->count();
            $data['totalRevenue']  = \App\Models\SchoolSubscription::where('status', 'active')->sum('total_price');
        } elseif ($user->isSchoolAdmin()) {
            // Admin Sekolah hanya melihat statistik sekolahnya
            $school = \App\Models\School::find($user->school_id);
            $data['totalStudents'] = User::where('school_id', $user->school_id)->where('role', 'student')->count();
            $data['totalTeachers'] = User::where('school_id', $user->school_id)->where('role', 'teacher')->count();
            $data['totalSubjects'] = Subject::where('school_id', $user->school_id)->count();
            $data['totalClassrooms'] = \App\Models\Classroom::where('school_id', $user->school_id)->count();

            // Sisa Kuota
            $data['totalQuota']     = $school ? $school->totalAccountsQuota() : 0;
            $data['remainingQuota'] = $school ? $school->remainingAccountsQuota() : 0;
        } else {
            // Teacher & Student
            $data['totalSubjects'] = Subject::count();
            $data['totalGrades']   = Grade::count();
        }

        // Grade distribution for chart (If School Admin, Teacher, or Student)
        if (!$user->isAdmin()) {
            $gradeQuery = Grade::query();
            // TODO: In a real multi-tenant app, filter Grade by school_id
            $allGrades = $gradeQuery->get();
            $data['gradeDistribution'] = [
                'A'  => $allGrades->where('nilai', '>=', 90)->count(),
                'B'  => $allGrades->whereBetween('nilai', [75, 89.99])->count(),
                'C'  => $allGrades->whereBetween('nilai', [60, 74.99])->count(),
                'D'  => $allGrades->whereBetween('nilai', [50, 59.99])->count(),
                'E'  => $allGrades->where('nilai', '<', 50)->count(),
            ];

            // Average per subject for chart
            $subjectAverages = Grade::selectRaw('subject_id, AVG(nilai) as avg')
                ->groupBy('subject_id')
                ->with('subject')
                ->get()
                ->map(fn($g) => [
                    'name' => $g->subject->nama ?? '?',
                    'avg'  => round($g->avg, 1),
                ]);
            $data['subjectAverages'] = $subjectAverages;
        }

        // Role-specific data
        if ($user->isStudent() && $user->studentProfile) {
            $myGrades = Grade::where('student_profile_id', $user->studentProfile->id)->get();
            $data['myGradeCount']   = $myGrades->count();
            $data['myAverage']      = $myGrades->avg('nilai');
            $data['myHighest']      = $myGrades->max('nilai');
            $data['myLowest']       = $myGrades->min('nilai');
        }

        if ($user->isTeacher() && $user->teacherProfile) {
            $data['mySubjects']    = $user->teacherProfile->subjects()->count();
            $data['myGradesGiven'] = Grade::where('teacher_profile_id', $user->teacherProfile->id)->count();
        }

        return view('dashboard', $data);
    }
}
