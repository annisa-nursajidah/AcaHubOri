<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Grade;
use App\Models\Subject;

class ReportController extends Controller
{
    /**
     * Show report card selection page / student list for teacher/admin.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isStudent()) {
            // Redirect student directly to their own report
            return redirect()->route('report.show', [
                'student' => $user->studentProfile?->id,
                'semester' => $request->get('semester', 'Ganjil'),
                'tahun' => $request->get('tahun', '2025/2026'),
            ]);
        }

        // Teacher/Admin: show student list to pick
        $students = \App\Models\StudentProfile::with('user')
            ->whereHas('user', fn($q) => $q->where('school_id', $user->school_id))
            ->get();
        return view('reports.index', compact('students'));
    }

    /**
     * Show report card for a specific student.
     */
    public function show(Request $request, $studentId)
    {
        $user = $request->user();
        $student = \App\Models\StudentProfile::with('user.school')->findOrFail($studentId);

        // Students can only view their own report
        if ($user->isStudent() && $user->studentProfile?->id != $studentId) {
            abort(403);
        }

        // Teacher/admin can only view students from their school
        if (!$user->isStudent() && $student->user?->school_id !== $user->school_id) {
            abort(403);
        }

        $semester    = $request->get('semester', 'Ganjil');
        $tahunAjaran = $request->get('tahun', '2025/2026');

        // Get all grades for this student, semester, and year
        $grades = Grade::with(['subject', 'teacherProfile.user'])
            ->where('student_profile_id', $studentId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get();

        // Group by subject and compute statistics
        $subjectGrades = $grades->groupBy('subject_id')->map(function ($items) {
            $subject = $items->first()->subject;
            $teacher = $items->first()->teacherProfile?->user?->name ?? '-';

            $tugas   = $items->where('tipe', 'tugas')->avg('nilai');
            $uts     = $items->where('tipe', 'uts')->avg('nilai');
            $uas     = $items->where('tipe', 'uas')->avg('nilai');
            $praktik = $items->where('tipe', 'praktik')->avg('nilai');

            // Weighted average: tugas 25%, uts 25%, uas 35%, praktik 15%
            $components = [];
            if ($tugas !== null)   $components[] = ['val' => $tugas, 'weight' => 25];
            if ($uts !== null)     $components[] = ['val' => $uts, 'weight' => 25];
            if ($uas !== null)     $components[] = ['val' => $uas, 'weight' => 35];
            if ($praktik !== null) $components[] = ['val' => $praktik, 'weight' => 15];

            $totalWeight = array_sum(array_column($components, 'weight'));
            $average = $totalWeight > 0
                ? array_sum(array_map(fn($c) => $c['val'] * $c['weight'], $components)) / $totalWeight
                : 0;

            return (object) [
                'subject'  => $subject,
                'teacher'  => $teacher,
                'tugas'    => $tugas,
                'uts'      => $uts,
                'uas'      => $uas,
                'praktik'  => $praktik,
                'average'  => round($average, 1),
                'status'   => $average >= 75 ? 'Tuntas' : 'Belum Tuntas',
            ];
        })->values();

        $overallAvg = $subjectGrades->avg('average');

        return view('reports.show', compact(
            'student', 'semester', 'tahunAjaran',
            'subjectGrades', 'overallAvg'
        ));
    }

    /**
     * Export report card as PDF.
     */
    public function exportPdf(Request $request, $studentId)
    {
        $user = $request->user();
        $student = \App\Models\StudentProfile::with('user.school')->findOrFail($studentId);

        if ($user->isStudent() && $user->studentProfile?->id != $studentId) {
            abort(403);
        }

        // Teacher/admin can only export students from their school
        if (!$user->isStudent() && $student->user?->school_id !== $user->school_id) {
            abort(403);
        }

        $semester    = $request->get('semester', 'Ganjil');
        $tahunAjaran = $request->get('tahun', '2025/2026');

        $grades = Grade::with(['subject', 'teacherProfile.user'])
            ->where('student_profile_id', $studentId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get();

        $subjectGrades = $grades->groupBy('subject_id')->map(function ($items) {
            $subject = $items->first()->subject;
            $teacher = $items->first()->teacherProfile?->user?->name ?? '-';

            $tugas   = $items->where('tipe', 'tugas')->avg('nilai');
            $uts     = $items->where('tipe', 'uts')->avg('nilai');
            $uas     = $items->where('tipe', 'uas')->avg('nilai');
            $praktik = $items->where('tipe', 'praktik')->avg('nilai');

            $components = [];
            if ($tugas !== null)   $components[] = ['val' => $tugas, 'weight' => 25];
            if ($uts !== null)     $components[] = ['val' => $uts, 'weight' => 25];
            if ($uas !== null)     $components[] = ['val' => $uas, 'weight' => 35];
            if ($praktik !== null) $components[] = ['val' => $praktik, 'weight' => 15];

            $totalWeight = array_sum(array_column($components, 'weight'));
            $average = $totalWeight > 0
                ? array_sum(array_map(fn($c) => $c['val'] * $c['weight'], $components)) / $totalWeight
                : 0;

            return (object) [
                'subject'  => $subject,
                'teacher'  => $teacher,
                'tugas'    => $tugas,
                'uts'      => $uts,
                'uas'      => $uas,
                'praktik'  => $praktik,
                'average'  => round($average, 1),
                'status'   => $average >= 75 ? 'Tuntas' : 'Belum Tuntas',
            ];
        })->values();

        $overallAvg = $subjectGrades->avg('average');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.report-pdf', compact(
            'student', 'semester', 'tahunAjaran',
            'subjectGrades', 'overallAvg'
        ));

        $pdf->setPaper('A4', 'portrait');

        $safeTahun = str_replace(['/', '\\'], '-', $tahunAjaran);
        $filename = 'Rapor_' . str_replace(' ', '_', $student->user->name) . "_{$semester}_{$safeTahun}.pdf";

        return $pdf->download($filename);
    }
}
