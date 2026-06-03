<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamQuestionController extends Controller
{
    /**
     * Store a newly created question to the exam.
     */
    public function store(Request $request, Exam $exam)
    {
        // Security check
        if ($exam->school_id !== auth()->user()->school_id) abort(403);

        $validated = $request->validate([
            'type'          => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'points'        => 'required|integer|min:1|max:100',
            // fields specific to multiple choice
            'options'       => 'required_if:type,multiple_choice|array|min:2',
            'options.*'     => 'required_if:type,multiple_choice|string',
            'correct_option'=> 'required_if:type,multiple_choice|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $question = $exam->questions()->create([
                'type'          => $validated['type'],
                'question_text' => $validated['question_text'],
                'points'        => $validated['points'],
            ]);

            if ($validated['type'] === 'multiple_choice') {
                foreach ($validated['options'] as $index => $optionText) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        // index dari array dicocokkan dengan input radio correct_option
                        'is_correct'  => ((int)$validated['correct_option'] === $index),
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Soal berhasil ditambahkan ke bank soal ujian!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan soal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question from the exam.
     */
    public function destroy(Exam $exam, ExamQuestion $question)
    {
        // Security checks
        if ($exam->school_id !== auth()->user()->school_id) abort(403);
        if ($question->exam_id !== $exam->id) abort(404);

        $question->delete();

        return back()->with('success', 'Soal ujian berhasil dihapus!');
    }
}
