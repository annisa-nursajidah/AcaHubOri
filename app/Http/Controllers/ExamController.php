<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the exams.
     */
    public function index()
    {
        $user = auth()->user();
        
        $query = Exam::with(['subject', 'classroom', 'teacher'])
                     ->where('school_id', $user->school_id);
                     
        if ($user->isTeacher()) {
            $query->where('teacher_id', $user->id);
        }

        $exams = $query->latest()->paginate(10);

        return view('exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        $user = auth()->user();
        $subjects = Subject::where('school_id', $user->school_id)->get();
        $classrooms = Classroom::where('school_id', $user->school_id)->get();
        // Option to preselect based on teacher's assignments could be added here
        
        return view('exams.create', compact('subjects', 'classrooms'));
    }

    /**
     * Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'subject_id'       => 'required|exists:subjects,id',
            'classroom_id'     => 'required|exists:classrooms,id',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'start_time'       => 'nullable|date',
            'end_time'         => 'nullable|date|after_or_equal:start_time',
            'status'           => 'required|in:draft,published,completed',
        ]);

        $validated['school_id'] = auth()->user()->school_id;
        $validated['teacher_id'] = auth()->id();

        $exam = Exam::create($validated);

        return redirect()->route('exams.index')
            ->with('success', 'Ujian CBT baru berhasil dibuat!');
    }

    /**
     * Display the specified exam (Will act as Question Builder).
     */
    public function show(Exam $exam)
    {
        // Security check
        if ($exam->school_id !== auth()->user()->school_id) abort(403);
        
        $exam->load('questions.options');
        return view('exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        // Security check
        if ($exam->school_id !== auth()->user()->school_id) abort(403);

        $user = auth()->user();
        $subjects = Subject::where('school_id', $user->school_id)->get();
        $classrooms = Classroom::where('school_id', $user->school_id)->get();
        
        return view('exams.edit', compact('exam', 'subjects', 'classrooms'));
    }

    /**
     * Update the specified exam in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        if ($exam->school_id !== auth()->user()->school_id) abort(403);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'subject_id'       => 'required|exists:subjects,id',
            'classroom_id'     => 'required|exists:classrooms,id',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'start_time'       => 'nullable|date',
            'end_time'         => 'nullable|date|after_or_equal:start_time',
            'status'           => 'required|in:draft,published,completed',
        ]);

        $exam->update($validated);

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Pengaturan Ujian berhasil diperbarui!');
    }

    /**
     * Remove the specified exam from storage.
     */
    public function destroy(Exam $exam)
    {
        if ($exam->school_id !== auth()->user()->school_id) abort(403);
        
        $exam->delete();

        return redirect()->route('exams.index')
            ->with('success', 'Ujian beserta seluruh soalnya berhasil dihapus.');
    }
}
