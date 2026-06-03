<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::where('school_id', auth()->user()->school_id)
            ->orderByDesc('is_active')
            ->orderByDesc('tahun')
            ->orderBy('semester')
            ->paginate(15);

        return view('academic-years.index', compact('years'));
    }

    public function create()
    {
        return view('academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'           => 'required|string|max:20',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        $validated['school_id'] = auth()->user()->school_id;
        AcademicYear::create($validated);

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academic_year)
    {
        if ($academic_year->school_id !== auth()->user()->school_id) abort(403);
        return view('academic-years.edit', ['year' => $academic_year]);
    }

    public function update(Request $request, AcademicYear $academic_year)
    {
        if ($academic_year->school_id !== auth()->user()->school_id) abort(403);
        $validated = $request->validate([
            'tahun'           => 'required|string|max:20',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        $academic_year->update($validated);

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academic_year)
    {
        if ($academic_year->school_id !== auth()->user()->school_id) abort(403);
        $academic_year->delete();

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * Set a specific academic year as the active one.
     */
    public function activate(AcademicYear $academic_year)
    {
        if ($academic_year->school_id !== auth()->user()->school_id) abort(403);
        AcademicYear::where('school_id', auth()->user()->school_id)->where('is_active', true)->update(['is_active' => false]);
        $academic_year->update(['is_active' => true]);

        return redirect()->route('academic-years.index')
            ->with('success', "Tahun ajaran {$academic_year->full_name} telah diaktifkan.");
    }

    /**
     * Start a new semester automatically:
     * 1. Close current active semester
     * 2. Deactivate old enrollments
     * 3. Create new semester as active
     * 4. Re-enroll students (promote grade if new school year)
     * 5. Send notifications
     */
    public function startNewSemester(Request $request)
    {
        $user = $request->user();
        if (!$user->isSchoolAdmin() && !$user->isAdmin()) abort(403);

        $validated = $request->validate([
            'tahun'           => 'required|string|max:20',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $schoolId = $user->school_id;

        DB::transaction(function () use ($validated, $schoolId) {
            // ── 1. Ambil semester aktif sekarang ────────────────
            $currentYear = AcademicYear::where('school_id', $schoolId)
                ->where('is_active', true)
                ->first();

            $isNewSchoolYear = false;
            if ($currentYear) {
                // Tentukan apakah ini ganti tahun ajaran
                $isNewSchoolYear = ($currentYear->tahun !== $validated['tahun']);

                // Nonaktifkan semester lama
                $currentYear->update(['is_active' => false]);

                // Nonaktifkan semua enrollment semester lama
                \App\Models\Enrollment::whereHas('classroom', function ($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })->where('academic_year_id', $currentYear->id)
                  ->update(['status' => 'inactive']);
            }

            // ── 2. Buat semester baru ────────────────────────────
            $newYear = AcademicYear::create([
                'school_id'       => $schoolId,
                'tahun'           => $validated['tahun'],
                'semester'        => $validated['semester'],
                'tanggal_mulai'   => $validated['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
                'is_active'       => true,
            ]);

            // ── 3. Re-enroll siswa ───────────────────────────────
            // Ambil semua kelas aktif di sekolah ini
            $classrooms = \App\Models\Classroom::where('school_id', $schoolId)
                ->get()
                ->keyBy('id');

            // Ambil enrollment aktif dari semester sebelumnya
            $oldEnrollments = $currentYear
                ? \App\Models\Enrollment::where('academic_year_id', $currentYear->id)
                    ->whereHas('classroom', fn($q) => $q->where('school_id', $schoolId))
                    ->with(['studentProfile', 'classroom'])
                    ->get()
                : collect();

            foreach ($oldEnrollments as $enrollment) {
                $student   = $enrollment->studentProfile;
                $oldClass  = $enrollment->classroom;

                if ($isNewSchoolYear) {
                    // Naik kelas: cari classroom dengan tingkat+1
                    $nextTingkat = $oldClass->tingkat + 1;

                    // Siswa kelas 12 dianggap lulus — tidak di-enroll lagi
                    if ($nextTingkat > 12) continue;

                    // Cari kelas dengan tingkat berikutnya (ambil pertama yang cocok)
                    $nextClass = \App\Models\Classroom::where('school_id', $schoolId)
                        ->where('tingkat', $nextTingkat)
                        ->first();

                    if (!$nextClass) continue; // Tidak ada kelas berikutnya, skip
                } else {
                    // Semester dalam tahun yang sama: tetap di kelas yang sama
                    $nextClass = $oldClass;
                }

                // Hindari duplikat
                $exists = \App\Models\Enrollment::where('student_profile_id', $student->id)
                    ->where('classroom_id', $nextClass->id)
                    ->where('academic_year_id', $newYear->id)
                    ->exists();

                if (!$exists) {
                    \App\Models\Enrollment::create([
                        'student_profile_id' => $student->id,
                        'classroom_id'       => $nextClass->id,
                        'academic_year_id'   => $newYear->id,
                        'status'             => 'active',
                    ]);

                    // Update field kelas di student_profile juga
                    $student->update(['kelas' => $nextClass->nama]);
                }
            }

            // ── 4. Kirim notifikasi ke semua user sekolah ────────
            $semesterLabel = "{$validated['tahun']} — {$validated['semester']}";
            $allUsers = \App\Models\User::where('school_id', $schoolId)->get();
            $now = now();

            $notifRows = $allUsers->map(fn($u) => [
                'id'              => \Illuminate\Support\Str::uuid()->toString(),
                'type'            => 'App\Notifications\SemesterChanged',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $u->id,
                'data'            => json_encode([
                    'title'   => 'Semester Baru Dimulai',
                    'message' => "Semester {$semesterLabel} telah dimulai.",
                    'url'     => route('academic-years.index'),
                ]),
                'read_at'    => null,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

            // Batch insert untuk efisiensi
            foreach (array_chunk($notifRows, 100) as $chunk) {
                DB::table('notifications')->insert($chunk);
            }
        });

        return redirect()->route('academic-years.index')
            ->with('success', "Semester {$validated['tahun']} {$validated['semester']} berhasil dimulai! Siswa telah otomatis didaftarkan ulang.");
    }
}
