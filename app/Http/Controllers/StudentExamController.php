<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentExamController extends Controller
{
    /**
     * Menampilkan daftar Ujian CBT yang aktif (Published) untuk siswa di kelas ini.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role !== 'student') abort(403);
        
        $profile = StudentProfile::where('user_id', $user->id)->first();
        if (!$profile || $profile->status !== 'active') {
            return redirect()->route('dashboard')->with('error', 'Status akun siswa Anda belum Aktif.');
        }

        // Tampilkan ujian yang Published di kelas dan sekolah siswa ini
        $exams = Exam::with('subject', 'teacher')
            ->where('school_id', $user->school_id)
            ->where('status', 'published')
            // Harusnya ada field classroom_id di profil, tapi untuk simplifikasi kita asumsikan siswa bisa melihat semua ujian aktif untuk kelas manapun di db sekolahnya,
            // (Dalam versi produksi, di-filter based on the student's actual enrolled classroom_id)
            ->latest()
            ->paginate(10);

        // Ambil riwayat ujian yang sudah pernah/sedang dikerjakan
        $attempts = ExamAttempt::where('student_id', $user->id)
            ->get()
            ->keyBy('exam_id');

        return view('student.exams.index', compact('exams', 'attempts'));
    }

    /**
     * Memulai sesi ujian (Start Timer)
     */
    public function start(Exam $exam)
    {
        $user = auth()->user();
        if ($user->role !== 'student' || $exam->school_id !== $user->school_id || $exam->status !== 'published') {
            abort(403, 'Akses Ujian Ditolak.');
        }

        // Cek apakah sudah pernah mengerjakan?
        $attempt = ExamAttempt::firstOrCreate(
            ['exam_id' => $exam->id, 'student_id' => $user->id],
            [
                'start_time' => now(),
                'status'     => 'in_progress',
            ]
        );

        if ($attempt->status === 'submitted' || $attempt->status === 'time_up') {
            return redirect()->route('student.exams.index')->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        return redirect()->route('student.exams.take', [$exam->id, $attempt->id]);
    }

    /**
     * Halaman Pengerjaan Ujian (Kertas Kerja Soal)
     */
    public function take(Exam $exam, ExamAttempt $attempt)
    {
        $user = auth()->user();
        if ($attempt->student_id !== $user->id || $attempt->exam_id !== $exam->id) abort(403);
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.index')->with('info', 'Ujian ini sudah selesai.');
        }

        // Hitung sisa waktu mundur
        // Cek jika durasi telah habis secara server (anti bypass lewat console frontend)
        $endTimeCalculated = $attempt->start_time->clone()->addMinutes($exam->duration_minutes);
        $remainingSeconds = $endTimeCalculated->diffInSeconds(now(), false);
        
        // Atur status jika batas absolut waktu aslinya sudah lewat (misal end_time ujian jam 8.00 pagi tetapi baru dikerjakan jam 8.05)
        if ($exam->end_time && now()->isAfter($exam->end_time)) {
             $attempt->update(['status' => 'time_up', 'end_time' => now()]);
             return redirect()->route('student.exams.index')->with('error', 'Waktu ujian telah ditutup.');
        }

        if ($remainingSeconds >= 0) {
            // Waktu sebenarnya habis
            $attempt->update(['status' => 'time_up', 'end_time' => now()]);
            return redirect()->route('student.exams.index')->with('error', 'Kehabisan waktu! Ujian Anda otomatis dikunci.');
        }
        
        $timeLeftMs = abs($remainingSeconds) * 1000;

        $exam->load(['questions.options']);
        return view('student.exams.take', compact('exam', 'attempt', 'timeLeftMs'));
    }

    /**
     * Sumbit Evaluasi & Auto-Grading PG
     */
    public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $user = auth()->user();
        if ($attempt->student_id !== $user->id || $attempt->exam_id !== $exam->id) abort(403);
        if ($attempt->status !== 'in_progress') return back()->with('error', 'Ujian ditutup.');

        DB::beginTransaction();
        try {
            $exam->load('questions.options');
            $totalScore = 0;
            $maxScore = $exam->questions->sum('points') ?: 1;

            $answers = $request->input('answers', []); // [question_id => option_id OR string essay]

            foreach ($exam->questions as $question) {
                $answerData = isset($answers[$question->id]) ? $answers[$question->id] : null;
                $pointsAwarded = 0;
                $optionId = null;
                $essayText = null;

                if ($question->type === 'multiple_choice' && $answerData) {
                    $optionId = $answerData;
                    // Auto-grade PG
                    $chosenOption = $question->options->where('id', $optionId)->first();
                    if ($chosenOption && $chosenOption->is_correct) {
                        $pointsAwarded = $question->points;
                        $totalScore += $pointsAwarded;
                    }
                } elseif ($question->type === 'essay' && $answerData) {
                    $essayText = $answerData;
                    // Essay dinilai manual nanti
                }

                $attempt->answers()->create([
                    'exam_question_id' => $question->id,
                    'exam_option_id'   => $optionId,
                    'essay_answer'     => $essayText,
                    'points_awarded'   => $pointsAwarded,
                ]);
            }

            // Normalisasi Skala 100 Berdasarkan PG yang terjawab benar / total semua point soal
            $finalScore = ($totalScore / $maxScore) * 100;

            $attempt->update([
                'status'   => 'submitted',
                'end_time' => now(),
                'score'    => $finalScore,
            ]);

            DB::commit();
            return redirect()->route('student.exams.index')->with('success', 'Ujian telah dikirim! Terima kasih.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses jawaban. ' . $e->getMessage());
        }
    }
}
