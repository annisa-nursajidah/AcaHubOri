<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::where('school_id', auth()->user()->school_id)
            ->with(['waliKelas.user', 'academicYear'])
            ->withCount('enrollments')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->paginate(15);

        return view('classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        $teachers = TeacherProfile::where('school_id', auth()->user()->school_id)->with('user')->get();
        $years    = AcademicYear::where('school_id', auth()->user()->school_id)->orderByDesc('tahun')->get();

        return view('classrooms.create', compact('teachers', 'years'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:50',
            'tingkat'          => 'required|integer|min:1|max:12',
            'wali_kelas_id'    => 'nullable|exists:teacher_profiles,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $validated['school_id'] = auth()->user()->school_id;
        Classroom::create($validated);

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Classroom $classroom)
    {
        if ($classroom->school_id !== auth()->user()->school_id) abort(403);
        $classroom->load(['waliKelas.user', 'academicYear', 'enrollments.studentProfile.user']);

        return view('classrooms.show', compact('classroom'));
    }

    public function edit(Classroom $classroom)
    {
        if ($classroom->school_id !== auth()->user()->school_id) abort(403);
        $teachers = TeacherProfile::where('school_id', auth()->user()->school_id)->with('user')->get();
        $years    = AcademicYear::where('school_id', auth()->user()->school_id)->orderByDesc('tahun')->get();

        return view('classrooms.edit', compact('classroom', 'teachers', 'years'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        if ($classroom->school_id !== auth()->user()->school_id) abort(403);
        $validated = $request->validate([
            'nama'             => 'required|string|max:50',
            'tingkat'          => 'required|integer|min:1|max:12',
            'wali_kelas_id'    => 'nullable|exists:teacher_profiles,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $classroom->update($validated);

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        if ($classroom->school_id !== auth()->user()->school_id) abort(403);
        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
