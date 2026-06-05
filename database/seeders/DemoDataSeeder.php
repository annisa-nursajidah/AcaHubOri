<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamOption;
use App\Models\ExamQuestion;
use App\Models\Grade;
use App\Models\Message;
use App\Models\School;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Ambil data yang sudah ada ────────────────────────────
        $school      = School::where('slug', 'sman-1-acahub')->first();
        $academicYear = AcademicYear::where('school_id', $school->id)->first();
        $classrooms  = Classroom::where('school_id', $school->id)->get()->keyBy('nama');
        $subjects    = Subject::where('school_id', $school->id)->get();
        $teachers    = TeacherProfile::whereHas('user', fn($q) => $q->where('school_id', $school->id))->get();
        $admin       = User::where('email', 'admin@acahub.test')->first();

        // ── Tambah Siswa Baru ────────────────────────────────────
        $newStudents = [
            ['name' => 'Rina Sari',       'email' => 'rina@acahub.test',    'nis' => '2024006', 'kelas' => 'X-A', 'lahir' => '2009-03-12', 'alamat' => 'Jl. Mawar No. 5, Jakarta'],
            ['name' => 'Dimas Prakoso',   'email' => 'dimas@acahub.test',   'nis' => '2024007', 'kelas' => 'X-B', 'lahir' => '2009-07-22', 'alamat' => 'Jl. Melati No. 8, Depok'],
            ['name' => 'Layla Hasanah',   'email' => 'layla@acahub.test',   'nis' => '2024008', 'kelas' => 'X-A', 'lahir' => '2009-11-05', 'alamat' => 'Jl. Anggrek No. 12, Bogor'],
            ['name' => 'Rizky Firmansyah','email' => 'rizky@acahub.test',   'nis' => '2024009', 'kelas' => 'X-B', 'lahir' => '2009-01-30', 'alamat' => 'Jl. Dahlia No. 3, Bekasi'],
            ['name' => 'Putri Amalia',    'email' => 'putri@acahub.test',   'nis' => '2024010', 'kelas' => 'X-A', 'lahir' => '2009-09-17', 'alamat' => 'Jl. Flamboyan No. 9, Tangerang'],
            ['name' => 'Bagas Nugroho',   'email' => 'bagas@acahub.test',   'nis' => '2024011', 'kelas' => 'X-B', 'lahir' => '2009-05-08', 'alamat' => 'Jl. Kenanga No. 14, Depok'],
            ['name' => 'Zahra Aulia',     'email' => 'zahra@acahub.test',   'nis' => '2024012', 'kelas' => 'X-A', 'lahir' => '2009-12-25', 'alamat' => 'Jl. Cempaka No. 7, Jakarta Selatan'],
            ['name' => 'Arif Hidayat',    'email' => 'arif@acahub.test',    'nis' => '2024013', 'kelas' => 'X-B', 'lahir' => '2009-04-14', 'alamat' => 'Jl. Tulip No. 2, Bekasi'],
        ];

        $allStudentProfiles = StudentProfile::whereHas('user', fn($q) => $q->where('school_id', $school->id))->get()->all();

        foreach ($newStudents as $s) {
            if (User::where('email', $s['email'])->exists()) continue;

            $user = User::create([
                'name'      => $s['name'],
                'email'     => $s['email'],
                'password'  => Hash::make('password'),
                'role'      => 'student',
                'school_id' => $school->id,
            ]);
            $profile = StudentProfile::create([
                'user_id'       => $user->id,
                'nis'           => $s['nis'],
                'kelas'         => $s['kelas'],
                'tanggal_lahir' => $s['lahir'],
                'alamat'        => $s['alamat'],
            ]);
            if (isset($classrooms[$s['kelas']])) {
                Enrollment::firstOrCreate([
                    'student_profile_id' => $profile->id,
                    'classroom_id'       => $classrooms[$s['kelas']]->id,
                    'academic_year_id'   => $academicYear->id,
                ], ['status' => 'active']);
            }
            $allStudentProfiles[] = $profile;
        }

        // Re-fetch all student profiles
        $allStudents = StudentProfile::whereHas('user', fn($q) => $q->where('school_id', $school->id))->with('user')->get();

        // ── Tambah Nilai (Grades) Lebih Lengkap ─────────────────
        $tipes = ['tugas', 'uts', 'uas', 'praktik'];
        $semesters = ['Ganjil', 'Genap'];

        foreach ($allStudents as $student) {
            foreach ($subjects as $idx => $subject) {
                foreach ($semesters as $sem) {
                    $count = rand(3, 5);
                    for ($i = 0; $i < $count; $i++) {
                        // Cek duplikat kasar
                        $existing = Grade::where('student_profile_id', $student->id)
                            ->where('subject_id', $subject->id)
                            ->where('semester', $sem)
                            ->count();
                        if ($existing >= 3) continue;

                        Grade::create([
                            'student_profile_id' => $student->id,
                            'subject_id'         => $subject->id,
                            'teacher_profile_id' => $teachers[$idx % $teachers->count()]->id,
                            'nilai'              => round(rand(60, 98) + (rand(0, 99) / 100), 2),
                            'tipe'               => $tipes[array_rand($tipes)],
                            'semester'           => $sem,
                            'tahun_ajaran'       => '2025/2026',
                            'catatan'            => null,
                        ]);
                    }
                }
            }
        }

        // ── Events / Kalender ────────────────────────────────────
        $events = [
            [
                'judul'          => 'Ujian Tengah Semester Ganjil',
                'deskripsi'      => 'Pelaksanaan UTS Semester Ganjil 2025/2026 untuk seluruh kelas X dan XI.',
                'tanggal_mulai'  => now()->addDays(10)->setTime(7, 30),
                'tanggal_selesai'=> now()->addDays(14)->setTime(12, 0),
                'tipe'           => 'ujian',
                'warna'          => '#ef4444',
            ],
            [
                'judul'          => 'Hari Libur Nasional - Hari Pancasila',
                'deskripsi'      => 'Libur nasional memperingati Hari Lahir Pancasila.',
                'tanggal_mulai'  => now()->addDays(20)->setTime(0, 0),
                'tanggal_selesai'=> now()->addDays(20)->setTime(23, 59),
                'tipe'           => 'libur',
                'warna'          => '#22c55e',
            ],
            [
                'judul'          => 'Pembagian Rapor Semester Ganjil',
                'deskripsi'      => 'Pembagian rapor semester ganjil kepada orang tua/wali siswa.',
                'tanggal_mulai'  => now()->addDays(30)->setTime(8, 0),
                'tanggal_selesai'=> now()->addDays(30)->setTime(12, 0),
                'tipe'           => 'akademik',
                'warna'          => '#0891b2',
            ],
            [
                'judul'          => 'Orientasi Siswa Baru',
                'deskripsi'      => 'Kegiatan pengenalan sekolah bagi siswa baru kelas X.',
                'tanggal_mulai'  => now()->subDays(45)->setTime(7, 0),
                'tanggal_selesai'=> now()->subDays(43)->setTime(15, 0),
                'tipe'           => 'akademik',
                'warna'          => '#8b5cf6',
            ],
            [
                'judul'          => 'Pekan Olahraga Sekolah',
                'deskripsi'      => 'Pertandingan olahraga antar kelas dalam rangka HUT Sekolah.',
                'tanggal_mulai'  => now()->addDays(45)->setTime(7, 0),
                'tanggal_selesai'=> now()->addDays(47)->setTime(17, 0),
                'tipe'           => 'akademik',
                'warna'          => '#f59e0b',
            ],
            [
                'judul'          => 'Rapat Orang Tua Siswa',
                'deskripsi'      => 'Pertemuan rutin antara pihak sekolah dan orang tua/wali siswa.',
                'tanggal_mulai'  => now()->addDays(5)->setTime(9, 0),
                'tanggal_selesai'=> now()->addDays(5)->setTime(12, 0),
                'tipe'           => 'akademik',
                'warna'          => '#0891b2',
            ],
            [
                'judul'          => 'Ujian Praktek Komputer',
                'deskripsi'      => 'Ujian praktik mata pelajaran TIK untuk kelas X.',
                'tanggal_mulai'  => now()->subDays(7)->setTime(8, 0),
                'tanggal_selesai'=> now()->subDays(7)->setTime(11, 0),
                'tipe'           => 'ujian',
                'warna'          => '#ef4444',
            ],
        ];

        foreach ($events as $e) {
            Event::create(array_merge($e, [
                'school_id' => $school->id,
                'user_id'   => $admin->id,
            ]));
        }

        // ── Pengumuman Tambahan ──────────────────────────────────
        $moreAnnouncements = [
            [
                'judul'     => 'Perubahan Jadwal Pelajaran Minggu Ini',
                'konten'    => "Diberitahukan kepada seluruh siswa bahwa jadwal pelajaran minggu ini mengalami perubahan:\n\n- Senin: Matematika dipindah ke jam ke-5\n- Rabu: Olahraga ditiadakan\n- Jumat: Tambahan jam Bahasa Inggris\n\nMohon perhatian dan kehadiran tepat waktu.",
                'target'    => 'student',
                'is_pinned' => false,
            ],
            [
                'judul'     => 'Pengumuman Beasiswa Prestasi 2026',
                'konten'    => "AcaHub membuka pendaftaran beasiswa prestasi untuk tahun ajaran 2025/2026.\n\nSyarat:\n- Nilai rata-rata minimal 85\n- Aktif dalam kegiatan sekolah\n- Surat rekomendasi dari wali kelas\n\nPendaftaran dibuka hingga 30 Juni 2026.",
                'target'    => 'all',
                'is_pinned' => true,
            ],
            [
                'judul'     => 'Jadwal Piket Guru Bulan Juni',
                'konten'    => "Berikut adalah jadwal piket guru bulan Juni 2026:\n\nSenin: Budi Santoso\nSelasa: Siti Rahmawati\nRabu: Agus Pratama\nKamis: Budi Santoso\nJumat: Siti Rahmawati\n\nHarap tepat waktu.",
                'target'    => 'teacher',
                'is_pinned' => false,
            ],
        ];

        foreach ($moreAnnouncements as $ann) {
            Announcement::create(array_merge($ann, ['user_id' => $admin->id]));
        }

        // ── Pesan / Messages ─────────────────────────────────────
        $teacherUsers = User::where('school_id', $school->id)->where('role', 'teacher')->get();
        $studentUsers = User::where('school_id', $school->id)->where('role', 'student')->take(5)->get();
        $schoolAdmin  = User::where('email', 'schooladmin@acahub.test')->first();

        $messages = [
            [
                'sender_id'   => $admin->id,
                'receiver_id' => $schoolAdmin->id,
                'subject'     => 'Laporan Bulanan Sistem',
                'body'        => "Halo,\n\nBerikut laporan bulanan sistem AcaHub untuk bulan Mei 2026:\n\n- Total pengguna aktif: 45\n- Sesi absensi: 120\n- Nilai yang diinputkan: 340\n- Ujian online: 5\n\nSemua berjalan baik. Salam,\nAdmin AcaHub",
                'read_at'     => now()->subHours(2),
            ],
            [
                'sender_id'   => $teacherUsers->first()->id,
                'receiver_id' => $admin->id,
                'subject'     => 'Permintaan Tambah Materi Ujian',
                'body'        => "Yth. Admin,\n\nSaya ingin meminta akses untuk menambahkan soal ujian pada modul Matematika Kelas X. Apakah bisa dibantu?\n\nTerima kasih,\nBudi Santoso",
                'read_at'     => null,
            ],
            [
                'sender_id'   => $schoolAdmin->id,
                'receiver_id' => $teacherUsers->first()->id,
                'subject'     => 'RE: Informasi Input Nilai UTS',
                'body'        => "Pak Budi,\n\nInput nilai UTS sudah dibuka mulai hari ini. Mohon diinputkan sebelum tanggal 20 Juni 2026.\n\nTerima kasih,\nAdmin Sekolah",
                'read_at'     => now()->subDay(),
            ],
            [
                'sender_id'   => $teacherUsers->get(1) ? $teacherUsers->get(1)->id : $teacherUsers->first()->id,
                'receiver_id' => $studentUsers->first()->id,
                'subject'     => 'Tugas Bahasa Indonesia Minggu Ini',
                'body'        => "Hai Andi,\n\nJangan lupa kumpulkan tugas esai Bahasa Indonesia paling lambat Jumat, 7 Juni 2026 pukul 08.00 WIB.\n\nTopik: Dampak Media Sosial terhadap Pelajar\nPanjang: minimal 3 halaman A4\n\nSalam,\nIbu Siti",
                'read_at'     => null,
            ],
            [
                'sender_id'   => $studentUsers->first()->id,
                'receiver_id' => $teacherUsers->first()->id,
                'subject'     => 'Izin Tidak Masuk',
                'body'        => "Yth. Bapak Budi,\n\nSaya ingin memberitahukan bahwa saya tidak dapat hadir pada hari Kamis, 6 Juni 2026 karena sakit. Surat izin dari dokter sudah saya lampirkan.\n\nMohon maaf atas ketidakhadirannya.\n\nSalam hormat,\nAndi Wijaya",
                'read_at'     => now()->subHours(5),
            ],
        ];

        foreach ($messages as $msg) {
            Message::create($msg);
        }

        // ── Ujian Online (Exams) ─────────────────────────────────
        $mathSubject    = $subjects->firstWhere('kode', 'MTK');
        $biSubject      = $subjects->firstWhere('kode', 'BIN');
        $engSubject     = $subjects->firstWhere('kode', 'BIG');
        $classXA        = $classrooms->get('X-A');
        $classXB        = $classrooms->get('X-B');
        $teacher1       = $teacherUsers->first();

        $examsData = [
            [
                'subject'     => $mathSubject,
                'classroom'   => $classXA,
                'title'       => 'Kuis Matematika - Aljabar Dasar',
                'description' => 'Kuis singkat materi aljabar dasar kelas X semester ganjil.',
                'duration'    => 45,
                'status'      => 'published',
                'start'       => now()->subDays(3)->setTime(8, 0),
                'end'         => now()->subDays(3)->setTime(9, 0),
                'questions'   => [
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'Berapakah nilai x jika 3x + 6 = 21?',
                        'points'  => 10,
                        'options' => [
                            ['text' => '3',  'correct' => false],
                            ['text' => '5',  'correct' => true],
                            ['text' => '7',  'correct' => false],
                            ['text' => '9',  'correct' => false],
                        ],
                    ],
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'Hasil dari (x + 3)(x - 2) adalah...',
                        'points'  => 10,
                        'options' => [
                            ['text' => 'x² + x - 6',  'correct' => true],
                            ['text' => 'x² - x - 6',  'correct' => false],
                            ['text' => 'x² + 5x - 6', 'correct' => false],
                            ['text' => 'x² - 5x + 6', 'correct' => false],
                        ],
                    ],
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'Jika 2x - 4y = 8 dan x = 6, berapakah nilai y?',
                        'points'  => 10,
                        'options' => [
                            ['text' => '1',  'correct' => true],
                            ['text' => '2',  'correct' => false],
                            ['text' => '3',  'correct' => false],
                            ['text' => '4',  'correct' => false],
                        ],
                    ],
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'FPB dari 24 dan 36 adalah...',
                        'points'  => 10,
                        'options' => [
                            ['text' => '6',  'correct' => false],
                            ['text' => '8',  'correct' => false],
                            ['text' => '12', 'correct' => true],
                            ['text' => '18', 'correct' => false],
                        ],
                    ],
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'Persamaan garis yang melalui titik (0,3) dan (2,7) adalah...',
                        'points'  => 10,
                        'options' => [
                            ['text' => 'y = 2x + 3', 'correct' => true],
                            ['text' => 'y = 3x + 2', 'correct' => false],
                            ['text' => 'y = x + 3',  'correct' => false],
                            ['text' => 'y = 2x - 3', 'correct' => false],
                        ],
                    ],
                ],
            ],
            [
                'subject'     => $biSubject,
                'classroom'   => $classXA,
                'title'       => 'Ulangan Harian Bahasa Indonesia - Teks Narasi',
                'description' => 'Ulangan harian materi teks narasi dan struktur cerita.',
                'duration'    => 60,
                'status'      => 'published',
                'start'       => now()->addDays(7)->setTime(10, 0),
                'end'         => now()->addDays(7)->setTime(11, 0),
                'questions'   => [
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'Bagian awal cerita yang memperkenalkan tokoh dan latar disebut...',
                        'points'  => 10,
                        'options' => [
                            ['text' => 'Klimaks',    'correct' => false],
                            ['text' => 'Orientasi',  'correct' => true],
                            ['text' => 'Resolusi',   'correct' => false],
                            ['text' => 'Komplikasi', 'correct' => false],
                        ],
                    ],
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'Penggunaan tanda baca yang benar pada kalimat berikut adalah...',
                        'points'  => 10,
                        'options' => [
                            ['text' => 'Saya pergi, ke pasar.',     'correct' => false],
                            ['text' => 'Saya pergi ke pasar.',      'correct' => true],
                            ['text' => 'Saya; pergi ke pasar.',     'correct' => false],
                            ['text' => 'Saya pergi: ke pasar.',     'correct' => false],
                        ],
                    ],
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'Antonim dari kata "rajin" adalah...',
                        'points'  => 10,
                        'options' => [
                            ['text' => 'Tekun',  'correct' => false],
                            ['text' => 'Giat',   'correct' => false],
                            ['text' => 'Malas',  'correct' => true],
                            ['text' => 'Aktif',  'correct' => false],
                        ],
                    ],
                ],
            ],
            [
                'subject'     => $engSubject,
                'classroom'   => $classXB,
                'title'       => 'English Quiz - Simple Present Tense',
                'description' => 'Quiz on simple present tense usage and vocabulary.',
                'duration'    => 30,
                'status'      => 'draft',
                'start'       => now()->addDays(14)->setTime(8, 0),
                'end'         => now()->addDays(14)->setTime(9, 0),
                'questions'   => [
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'She ___ to school every day.',
                        'points'  => 10,
                        'options' => [
                            ['text' => 'go',   'correct' => false],
                            ['text' => 'goes', 'correct' => true],
                            ['text' => 'went', 'correct' => false],
                            ['text' => 'gone', 'correct' => false],
                        ],
                    ],
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'Which sentence is correct?',
                        'points'  => 10,
                        'options' => [
                            ['text' => 'He don\'t like coffee.',    'correct' => false],
                            ['text' => 'He doesn\'t like coffee.', 'correct' => true],
                            ['text' => 'He not like coffee.',      'correct' => false],
                            ['text' => 'He isn\'t like coffee.',   'correct' => false],
                        ],
                    ],
                    [
                        'type'    => 'multiple_choice',
                        'text'    => 'The opposite of "happy" is...',
                        'points'  => 10,
                        'options' => [
                            ['text' => 'Joyful', 'correct' => false],
                            ['text' => 'Glad',   'correct' => false],
                            ['text' => 'Sad',    'correct' => true],
                            ['text' => 'Excited','correct' => false],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($examsData as $examData) {
            if (!$examData['subject'] || !$examData['classroom']) continue;

            $exam = Exam::create([
                'school_id'        => $school->id,
                'teacher_id'       => $teacher1->id,
                'subject_id'       => $examData['subject']->id,
                'classroom_id'     => $examData['classroom']->id,
                'title'            => $examData['title'],
                'description'      => $examData['description'],
                'duration_minutes' => $examData['duration'],
                'start_time'       => $examData['start'],
                'end_time'         => $examData['end'],
                'status'           => $examData['status'],
            ]);

            foreach ($examData['questions'] as $qData) {
                $question = ExamQuestion::create([
                    'exam_id'       => $exam->id,
                    'type'          => $qData['type'],
                    'question_text' => $qData['text'],
                    'points'        => $qData['points'],
                ]);

                foreach ($qData['options'] as $opt) {
                    ExamOption::create([
                        'exam_question_id' => $question->id,
                        'option_text'      => $opt['text'],
                        'is_correct'       => $opt['correct'],
                    ]);
                }
            }

            // Buat attempt siswa untuk exam yang sudah selesai
            if ($examData['status'] === 'published' && $examData['end']->isPast()) {
                $enrolledStudents = Enrollment::where('classroom_id', $examData['classroom']->id)
                    ->with('studentProfile')
                    ->get();

                foreach ($enrolledStudents as $enrollment) {
                    ExamAttempt::create([
                        'exam_id'    => $exam->id,
                        'student_id' => $enrollment->studentProfile->user_id,
                        'start_time' => $examData['start']->copy()->addMinutes(rand(0, 5)),
                        'end_time'   => $examData['start']->copy()->addMinutes(rand(20, $examData['duration'])),
                        'score'      => rand(60, 100),
                        'status'     => 'submitted',
                    ]);
                }
            }
        }

        // ── Sesi Absensi Tambahan (lebih banyak mata pelajaran) ──
        $statuses = ['present', 'present', 'present', 'present', 'absent', 'sick', 'excused'];

        foreach ($subjects->take(3) as $subIdx => $subject) {
            $classroom = $subIdx % 2 === 0 ? $classXA : $classXB;
            if (!$classroom) continue;

            $teacher = $teacherUsers->get($subIdx % $teacherUsers->count());

            for ($d = 1; $d <= 8; $d++) {
                $sessionDate = now()->subDays(30 - ($d * 3))->format('Y-m-d');
                $token = 'SESS' . strtoupper(substr(md5($subject->id . $d), 0, 8));

                if (AttendanceSession::where('qr_code_token', $token)->exists()) continue;

                $session = AttendanceSession::create([
                    'school_id'     => $school->id,
                    'classroom_id'  => $classroom->id,
                    'subject_id'    => $subject->id,
                    'teacher_id'    => $teacher->id,
                    'date'          => $sessionDate,
                    'start_time'    => '08:00:00',
                    'end_time'      => '09:30:00',
                    'qr_code_token' => $token,
                    'status'        => 'closed',
                ]);

                $sessionStudents = Enrollment::where('classroom_id', $classroom->id)
                    ->with('studentProfile')
                    ->get();

                foreach ($sessionStudents as $enrollment) {
                    Attendance::create([
                        'school_id'             => $school->id,
                        'attendance_session_id' => $session->id,
                        'student_id'            => $enrollment->studentProfile->user_id,
                        'date'                  => $sessionDate,
                        'status'                => $statuses[array_rand($statuses)],
                        'scanned_at'            => now()->subDays(30 - ($d * 3))->setTime(rand(7, 8), rand(50, 59), rand(0, 59)),
                    ]);
                }
            }
        }
    }
}
