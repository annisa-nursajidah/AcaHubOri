<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects.
     */
    public function index(Request $request)
    {
        $query = Subject::where('school_id', auth()->user()->school_id)->withCount(['teachers', 'grades']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $subjects = $query->latest()->paginate(15)->withQueryString();

        return view('subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $schoolId = auth()->user()->school_id;
        $teachers = TeacherProfile::with('user')
            ->whereHas('user', fn($q) => $q->where('school_id', $schoolId))
            ->get();
        return view('subjects.create', compact('teachers'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:255'],
            'kode'      => ['required', 'string', 'max:20', 'unique:subjects,kode'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'teachers'  => ['nullable', 'array'],
            'teachers.*'=> ['exists:teacher_profiles,id'],
        ]);

        $subject = Subject::create([
            'school_id' => auth()->user()->school_id,
            'nama'      => $validated['nama'],
            'kode'      => $validated['kode'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        if (! empty($validated['teachers'])) {
            $subject->teachers()->sync($validated['teachers']);
        }

        return redirect()->route('subjects.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        if ($subject->school_id !== auth()->user()->school_id) abort(403);
        $subject->load(['teachers.user', 'grades.studentProfile.user']);
        return view('subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        if ($subject->school_id !== auth()->user()->school_id) abort(403);
        $subject->load('teachers');
        $schoolId = auth()->user()->school_id;
        $teachers = TeacherProfile::with('user')
            ->whereHas('user', fn($q) => $q->where('school_id', $schoolId))
            ->get();
        return view('subjects.edit', compact('subject', 'teachers'));
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, Subject $subject)
    {
        if ($subject->school_id !== auth()->user()->school_id) abort(403);
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:255'],
            'kode'      => ['required', 'string', 'max:20', 'unique:subjects,kode,' . $subject->id],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'teachers'  => ['nullable', 'array'],
            'teachers.*'=> ['exists:teacher_profiles,id'],
        ]);

        $subject->update([
            'nama'      => $validated['nama'],
            'kode'      => $validated['kode'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        $subject->teachers()->sync($validated['teachers'] ?? []);

        return redirect()->route('subjects.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui!');
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject)
    {
        if ($subject->school_id !== auth()->user()->school_id) abort(403);
        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus!');
    }
}
