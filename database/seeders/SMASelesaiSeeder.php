<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\SubscriptionPlan;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * SMASelesaiSeeder — IDEMPOTEN
 * ─────────────────────────────────────────────────────────────────
 * Sekolah SMA Negeri 3 Cendekia — semester SUDAH SELESAI.
 * Dirancang agar siswa & wali murid bisa langsung melihat:
 *   ✅ Rapor lengkap (tugas, uts, uas, praktik, rata-rata)
 *   ✅ Absensi per sesi yang sudah closed
 *   ✅ Pengumuman kelulusan semester
 *
 * Data:
 *  - 1 Sekolah baru (slug: sman-3-cendekia)
 *  - Tahun Ajaran 2024/2025 Genap — is_active = false (sudah selesai)
 *  - 1 School Admin
 *  - 5 Guru (masing-masing 1 matkul)
 *  - 3 Kelas (X-A, XI-B, XII-IPA)
 *  - 15 Siswa (5 per kelas) + 10 Wali Murid
 *  - 20 Sesi Absensi per kelas = 60 sesi total
 *  - Record absensi realistis per sesi (campuran hadir/telat/sakit/izin/absen)
 *  - Nilai lengkap: tugas × 3, uts × 1, uas × 1, praktik × 1 per matkul
 *  - Pengumuman akhir semester & kelulusan
 *
 * Password semua akun: password
 */
class SMASelesaiSeeder extends Seeder
{
    public function run(): void
    {
        // ── Pastikan ENUM role mendukung 'parent' ──────────────────
        DB::statement(
            "ALTER TABLE users MODIFY COLUMN role "
            . "ENUM('admin','teacher','student','school_admin','parent') DEFAULT 'student'"
        );

        // ─────────────────────────────────────────
        // 1. SEKOLAH
        // ─────────────────────────────────────────
        $school = School::firstOrCreate(
            ['slug' => 'sman-3-cendekia'],
            [
                'name'        => 'SMA Negeri 3 Cendekia',
                'address'     => 'Jl. Cendekia Raya No. 3, Kota Surabaya, Jawa Timur 60111',
                'phone'       => '031-5550003',
                'email'       => 'info@sman3cendekia.sch.id',
                'logo'        => null,
                'is_active'   => true,
                'invite_code' => strtoupper(Str::random(8)),
            ]
        );

        // ─────────────────────────────────────────
        // 2. SUBSCRIPTION (aktif)
        // ─────────────────────────────────────────
        $plan = SubscriptionPlan::firstOrCreate(
            ['name' => 'Paket Sekolah Lengkap'],
            [
                'description'       => 'Plan demo untuk sekolah SMA.',
                'price_per_account' => 4500,
                'min_accounts'      => 10,
                'max_accounts'      => 500,
                'features'          => json_encode([
                    'Manajemen Guru & Siswa',
                    'Absensi QR Code',
                    'Nilai & Rapor Digital',
                    'Pengumuman Sekolah',
                    'Wali Murid Portal',
                ]),
                'duration_days' => 365,
                'is_active'     => true,
                'sort_order'    => 1,
            ]
        );

        SchoolSubscription::firstOrCreate(
            ['school_id' => $school->id, 'plan_id' => $plan->id],
            [
                'total_accounts'    => 200,
                'price_per_account' => 4500,
                'total_price'       => 200 * 4500,
                'status'            => 'active',
                'starts_at'         => '2025-01-01 00:00:00',
                'expires_at'        => '2026-01-01 00:00:00',
                'notes'             => 'Seeder selesai — TA 2024/2025 Genap',
            ]
        );

        // ─────────────────────────────────────────
        // 3. TAHUN AJARAN — SUDAH SELESAI
        //    is_active = false, tanggal_selesai sudah lewat
        // ─────────────────────────────────────────
        // Unique constraint (tahun, semester) global — pakai firstOrCreate
        $academicYear = AcademicYear::firstOrCreate(
            ['tahun' => '2024/2025', 'semester' => 'Genap'],
            [
                'school_id'       => $school->id,
                'tanggal_mulai'   => '2025-01-13',
                'tanggal_selesai' => '2025-06-20',  // Sudah lewat
                'is_active'       => false,          // SEMESTER SELESAI
            ]
        );

        // ─────────────────────────────────────────
        // 4. SCHOOL ADMIN
        // ─────────────────────────────────────────
        $schoolAdmin = User::firstOrCreate(
            ['email' => 'admin@sman3cendekia.sch.id'],
            [
                'name'      => 'Kepala TU SMAN 3 Cendekia',
                'password'  => Hash::make('password'),
                'role'      => 'school_admin',
                'school_id' => $school->id,
            ]
        );

        // ─────────────────────────────────────────
        // 5. GURU + MATA PELAJARAN
        // ─────────────────────────────────────────
        $teacherData = [
            [
                'name'    => 'Dra. Sri Wahyuni, M.Pd',
                'email'   => 'sri.wahyuni@sman3cendekia.sch.id',
                'nip'     => '196803142000122001',
                'telepon' => '0811-1001-0001',
                'alamat'  => 'Jl. Kenari No. 5, Surabaya',
                'subject' => ['nama' => 'Matematika',      'kode' => 'MTK-3'],
            ],
            [
                'name'    => 'Bpk. Andi Saputra, S.Pd',
                'email'   => 'andi.saputra@sman3cendekia.sch.id',
                'nip'     => '197504202003011002',
                'telepon' => '0812-2002-0002',
                'alamat'  => 'Jl. Mawar No. 10, Surabaya',
                'subject' => ['nama' => 'Fisika',           'kode' => 'FIS-3'],
            ],
            [
                'name'    => 'Ibu Dewi Kusuma, M.Hum',
                'email'   => 'dewi.kusuma@sman3cendekia.sch.id',
                'nip'     => '198109052006042003',
                'telepon' => '0813-3003-0003',
                'alamat'  => 'Jl. Melati No. 8, Surabaya',
                'subject' => ['nama' => 'Bahasa Indonesia', 'kode' => 'BIN-3'],
            ],
            [
                'name'    => 'Bpk. Reza Firmansyah, S.Pd',
                'email'   => 'reza.firmansyah@sman3cendekia.sch.id',
                'nip'     => '199205112016011004',
                'telepon' => '0814-4004-0004',
                'alamat'  => 'Jl. Flamboyan No. 3, Surabaya',
                'subject' => ['nama' => 'Kimia',            'kode' => 'KIM-3'],
            ],
            [
                'name'    => 'Ibu Laila Nuraini, S.Pd',
                'email'   => 'laila.nuraini@sman3cendekia.sch.id',
                'nip'     => '198811232014022005',
                'telepon' => '0815-5005-0005',
                'alamat'  => 'Jl. Anggrek No. 17, Surabaya',
                'subject' => ['nama' => 'Biologi',          'kode' => 'BIO-3'],
            ],
        ];

        $teachers = [];
        $subjects  = [];

        foreach ($teacherData as $td) {
            $userTeacher = User::firstOrCreate(
                ['email' => $td['email']],
                [
                    'name'      => $td['name'],
                    'password'  => Hash::make('password'),
                    'role'      => 'teacher',
                    'school_id' => $school->id,
                ]
            );

            $profile = TeacherProfile::firstOrCreate(
                ['user_id' => $userTeacher->id],
                [
                    'nip'     => $td['nip'],
                    'telepon' => $td['telepon'],
                    'alamat'  => $td['alamat'],
                ]
            );

            $subject = Subject::firstOrCreate(
                ['kode' => $td['subject']['kode']],
                [
                    'school_id' => $school->id,
                    'nama'      => $td['subject']['nama'],
                    'deskripsi' => 'Mata pelajaran ' . $td['subject']['nama'] . ' SMA.',
                ]
            );

            if (! $profile->subjects()->where('subjects.id', $subject->id)->exists()) {
                $profile->subjects()->attach($subject->id);
            }

            $teachers[] = $profile;
            $subjects[]  = $subject;
        }

        // ─────────────────────────────────────────
        // 6. KELAS (3 kelas)
        // ─────────────────────────────────────────
        $classroomsData = [
            ['nama' => 'X-A',     'tingkat' => 10, 'wali_idx' => 0],
            ['nama' => 'XI-B',    'tingkat' => 11, 'wali_idx' => 1],
            ['nama' => 'XII-IPA', 'tingkat' => 12, 'wali_idx' => 2],
        ];

        $classrooms = [];
        foreach ($classroomsData as $cd) {
            $classrooms[] = Classroom::firstOrCreate(
                ['school_id' => $school->id, 'nama' => $cd['nama']],
                [
                    'tingkat'          => $cd['tingkat'],
                    'wali_kelas_id'    => $teachers[$cd['wali_idx']]->id,
                    'academic_year_id' => $academicYear->id,
                ]
            );
        }

        // ─────────────────────────────────────────
        // 7. SISWA (5 per kelas = 15 total)
        // ─────────────────────────────────────────
        $studentData = [
            // X-A
            ['name' => 'Aditya Pramana',    'email' => 'aditya.p@sman3cendekia.sch.id',   'nis' => 'C2024001'],
            ['name' => 'Bella Safitri',     'email' => 'bella.s@sman3cendekia.sch.id',    'nis' => 'C2024002'],
            ['name' => 'Chandra Widjaja',   'email' => 'chandra.w@sman3cendekia.sch.id',  'nis' => 'C2024003'],
            ['name' => 'Dian Astuti',       'email' => 'dian.a@sman3cendekia.sch.id',     'nis' => 'C2024004'],
            ['name' => 'Evan Kurniawan',    'email' => 'evan.k@sman3cendekia.sch.id',     'nis' => 'C2024005'],
            // XI-B
            ['name' => 'Fani Rahayu',       'email' => 'fani.r@sman3cendekia.sch.id',    'nis' => 'C2023001'],
            ['name' => 'Gilang Santoso',    'email' => 'gilang.s@sman3cendekia.sch.id',   'nis' => 'C2023002'],
            ['name' => 'Hani Permata',      'email' => 'hani.p@sman3cendekia.sch.id',    'nis' => 'C2023003'],
            ['name' => 'Irfan Maulana',     'email' => 'irfan.m@sman3cendekia.sch.id',   'nis' => 'C2023004'],
            ['name' => 'Julia Andriani',    'email' => 'julia.a@sman3cendekia.sch.id',   'nis' => 'C2023005'],
            // XII-IPA
            ['name' => 'Kevin Pratama',     'email' => 'kevin.p@sman3cendekia.sch.id',   'nis' => 'C2022001'],
            ['name' => 'Linda Sulistya',    'email' => 'linda.s@sman3cendekia.sch.id',   'nis' => 'C2022002'],
            ['name' => 'Mario Agung',       'email' => 'mario.a@sman3cendekia.sch.id',   'nis' => 'C2022003'],
            ['name' => 'Nina Fauziah',      'email' => 'nina.f@sman3cendekia.sch.id',    'nis' => 'C2022004'],
            ['name' => 'Oscar Ramadhan',    'email' => 'oscar.r@sman3cendekia.sch.id',   'nis' => 'C2022005'],
        ];

        $birthDates = [
            '2009-02-10','2009-06-15','2009-09-22','2010-01-07','2009-11-30',
            '2008-03-18','2008-07-04','2008-10-13','2009-01-25','2008-05-21',
            '2007-04-11','2007-08-29','2007-12-03','2008-02-16','2007-07-07',
        ];

        $students = []; // [classroomIdx => [StudentProfile, ...]]

        foreach ($studentData as $idx => $sd) {
            $classroomIdx = (int) floor($idx / 5);
            $classroom    = $classrooms[$classroomIdx];

            $userStudent = User::firstOrCreate(
                ['email' => $sd['email']],
                [
                    'name'      => $sd['name'],
                    'password'  => Hash::make('password'),
                    'role'      => 'student',
                    'school_id' => $school->id,
                ]
            );

            $studentProfile = StudentProfile::firstOrCreate(
                ['nis' => $sd['nis']],
                [
                    'user_id'       => $userStudent->id,
                    'kelas'         => $classroom->nama,
                    'tanggal_lahir' => $birthDates[$idx],
                    'alamat'        => 'Jl. Pelajar No. ' . ($idx + 1) . ', Surabaya',
                    'status'        => 'active',
                ]
            );

            Enrollment::firstOrCreate(
                [
                    'student_profile_id' => $studentProfile->id,
                    'classroom_id'       => $classroom->id,
                    'academic_year_id'   => $academicYear->id,
                ],
                ['status' => 'active']
            );

            $students[$classroomIdx][] = $studentProfile;
        }

        $allStudentProfiles = array_merge(...$students);

        // ─────────────────────────────────────────
        // 8. WALI MURID (10 ortu)
        // ─────────────────────────────────────────
        $parentData = [
            ['name' => 'Bpk. Pramana Jaya',     'email' => 'pramana.ortu@gmail.com',    'children_idx' => [0]],
            ['name' => 'Ibu Safitri Lestari',    'email' => 'safitri.ortu@gmail.com',   'children_idx' => [1]],
            ['name' => 'Bpk. Widjaja Halim',     'email' => 'widjaja.ortu@gmail.com',   'children_idx' => [2, 6]],  // 2 anak
            ['name' => 'Ibu Astuti Wahyu',       'email' => 'astuti.ortu@gmail.com',    'children_idx' => [3]],
            ['name' => 'Bpk. Kurniawan Hadi',    'email' => 'kurniawan.ortu@gmail.com', 'children_idx' => [4]],
            ['name' => 'Ibu Rahayu Indah',       'email' => 'rahayu.ortu@gmail.com',    'children_idx' => [5]],
            ['name' => 'Ibu Permata Sari',       'email' => 'permata.ortu@gmail.com',   'children_idx' => [7, 11]], // 2 anak
            ['name' => 'Bpk. Maulana Eko',       'email' => 'maulana.ortu@gmail.com',   'children_idx' => [8]],
            ['name' => 'Ibu Andriani Dewi',      'email' => 'andriani.ortu@gmail.com',  'children_idx' => [9]],
            ['name' => 'Bpk. Pratama Budi',      'email' => 'pratama.ortu@gmail.com',   'children_idx' => [10, 12]], // 2 anak
        ];

        foreach ($parentData as $pd) {
            $parentUser = User::firstOrCreate(
                ['email' => $pd['email']],
                [
                    'name'      => $pd['name'],
                    'password'  => Hash::make('password'),
                    'role'      => 'parent',
                    'school_id' => $school->id,
                ]
            );

            foreach ($pd['children_idx'] as $childIdx) {
                DB::table('parent_student')->insertOrIgnore([
                    'parent_id'  => $parentUser->id,
                    'student_id' => $allStudentProfiles[$childIdx]->user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ─────────────────────────────────────────
        // 9. SESI ABSENSI + RECORD ABSENSI
        //    20 sesi per kelas = 60 sesi total (sudah closed semua)
        //    Tanggal mundur: semester genap Jan–Jun 2025 (sudah lewat)
        // ─────────────────────────────────────────
        // Pola absensi realistis per sesi (campuran berbeda-beda)
        $attendancePatterns = [
            // [status, status, status, status, status] per 5 siswa
            ['present', 'present', 'present', 'present', 'present'],
            ['present', 'present', 'present', 'late',    'present'],
            ['present', 'present', 'sick',    'present', 'present'],
            ['present', 'late',    'present', 'present', 'absent'],
            ['present', 'present', 'present', 'excused', 'present'],
            ['late',    'present', 'present', 'present', 'present'],
            ['present', 'present', 'absent',  'present', 'present'],
            ['present', 'present', 'present', 'present', 'sick'],
            ['present', 'excused', 'present', 'present', 'present'],
            ['present', 'present', 'present', 'late',    'present'],
        ];

        foreach ($classrooms as $cIdx => $classroom) {
            $classStudents = $students[$cIdx] ?? [];

            for ($session = 1; $session <= 20; $session++) {
                // Tanggal mundur: mulai dari 160 hari lalu sampai 10 hari lalu
                $daysAgo     = 160 - ($session * 7) + $cIdx;
                $sessionDate = now()->subDays($daysAgo)->format('Y-m-d');

                $subjectIdx  = ($cIdx + $session - 1) % count($subjects);
                $teacherIdx  = $subjectIdx;
                $token       = hash('sha256', "sman3-selesai-{$cIdx}-{$session}");

                $sess = AttendanceSession::firstOrCreate(
                    ['qr_code_token' => $token],
                    [
                        'school_id'    => $school->id,
                        'classroom_id' => $classroom->id,
                        'subject_id'   => $subjects[$subjectIdx]->id,
                        'teacher_id'   => $teachers[$teacherIdx]->user->id,
                        'date'         => $sessionDate,
                        'start_time'   => '07:30:00',
                        'end_time'     => '09:00:00',
                        'status'       => 'closed', // Semua sesi sudah tutup
                    ]
                );

                // Pilih pola absensi berdasarkan sesi (deterministik)
                $pattern = $attendancePatterns[$session % count($attendancePatterns)];

                foreach ($classStudents as $sPos => $studentProfile) {
                    $statusVal = $pattern[$sPos % count($pattern)];
                    $scanTime  = $statusVal === 'absent' ? null
                        : $sessionDate . ' 07:' . str_pad(30 + ($sPos * 2), 2, '0', STR_PAD_LEFT) . ':00';

                    Attendance::firstOrCreate(
                        [
                            'attendance_session_id' => $sess->id,
                            'student_id'            => $studentProfile->user_id,
                        ],
                        [
                            'school_id'  => $school->id,
                            'date'       => $sessionDate,
                            'status'     => $statusVal,
                            'scanned_at' => $scanTime,
                            'notes'      => $statusVal === 'sick'    ? 'Surat keterangan dokter' :
                                           ($statusVal === 'excused' ? 'Izin keperluan keluarga' : null),
                        ]
                    );
                }
            }
        }

        // ─────────────────────────────────────────
        // 10. NILAI LENGKAP (RAPOR)
        //     Setiap siswa mendapat:
        //     - 3× tugas per matkul (nilai bervariasi)
        //     - 1× uts per matkul
        //     - 1× uas per matkul
        //     - 1× praktik per matkul (untuk sains)
        //     Nilai deterministik & realistis (KKM 75)
        // ─────────────────────────────────────────
        // Tabel nilai dasar per siswa (15 siswa), bervariasi
        $nilaiBase = [88, 72, 91, 85, 78, 95, 68, 83, 76, 92, 80, 74, 89, 77, 93];

        // Matkul yang punya praktik (Fisika, Kimia, Biologi)
        $subjectsPraktik = ['FIS-3', 'KIM-3', 'BIO-3'];

        foreach ($allStudentProfiles as $sIdx => $studentProfile) {
            $base = $nilaiBase[$sIdx];

            foreach ($subjects as $subIdx => $subject) {
                $assignedTeacher = $teachers[$subIdx];
                $hasPraktik      = in_array($subject->kode, $subjectsPraktik);

                // 3 Tugas (nilai sedikit bervariasi)
                foreach ([0, 1, 2] as $tNo) {
                    $nilaiTugas = max(60, min(100, $base - ($tNo * 2) + ($subIdx * 3) - ($tNo * $subIdx % 5)));
                    Grade::firstOrCreate(
                        [
                            'student_profile_id' => $studentProfile->id,
                            'subject_id'         => $subject->id,
                            'tipe'               => 'tugas',
                            'semester'           => 'Genap',
                            'tahun_ajaran'       => '2024/2025',
                            'catatan'            => "Tugas " . ($tNo + 1),
                        ],
                        [
                            'teacher_profile_id' => $assignedTeacher->id,
                            'nilai'              => $nilaiTugas,
                        ]
                    );
                }

                // UTS
                $nilaiUts = max(60, min(100, $base - 5 + ($subIdx * 2)));
                Grade::firstOrCreate(
                    [
                        'student_profile_id' => $studentProfile->id,
                        'subject_id'         => $subject->id,
                        'tipe'               => 'uts',
                        'semester'           => 'Genap',
                        'tahun_ajaran'       => '2024/2025',
                        'catatan'            => null,
                    ],
                    [
                        'teacher_profile_id' => $assignedTeacher->id,
                        'nilai'              => $nilaiUts,
                    ]
                );

                // UAS
                $nilaiUas = max(60, min(100, $base + 2 - ($subIdx * 1)));
                Grade::firstOrCreate(
                    [
                        'student_profile_id' => $studentProfile->id,
                        'subject_id'         => $subject->id,
                        'tipe'               => 'uas',
                        'semester'           => 'Genap',
                        'tahun_ajaran'       => '2024/2025',
                        'catatan'            => null,
                    ],
                    [
                        'teacher_profile_id' => $assignedTeacher->id,
                        'nilai'              => $nilaiUas,
                    ]
                );

                // Praktik (hanya untuk matkul sains)
                if ($hasPraktik) {
                    $nilaiPraktik = max(60, min(100, $base + 5 - ($subIdx % 3)));
                    Grade::firstOrCreate(
                        [
                            'student_profile_id' => $studentProfile->id,
                            'subject_id'         => $subject->id,
                            'tipe'               => 'praktik',
                            'semester'           => 'Genap',
                            'tahun_ajaran'       => '2024/2025',
                            'catatan'            => null,
                        ],
                        [
                            'teacher_profile_id' => $assignedTeacher->id,
                            'nilai'              => $nilaiPraktik,
                        ]
                    );
                }
            }
        }

        // ─────────────────────────────────────────
        // 11. PENGUMUMAN AKHIR SEMESTER
        // ─────────────────────────────────────────
        $announcements = [
            [
                'judul'  => '🎉 Pengumuman Kelulusan Semester Genap 2024/2025',
                'user'   => $schoolAdmin->id,
                'target' => 'all',
                'pinned' => true,
                'konten' => "Alhamdulillah, Semester Genap Tahun Ajaran 2024/2025 telah resmi berakhir.\n\n"
                          . "Seluruh siswa SMA Negeri 3 Cendekia dinyatakan telah menyelesaikan semester ini.\n\n"
                          . "Rapor dapat diunduh melalui portal AcaHub atau diambil langsung di sekolah mulai\n"
                          . "tanggal 30 Juni 2025 dengan membawa kartu identitas.\n\n"
                          . "Selamat atas capaian belajar Anda semua!",
            ],
            [
                'judul'  => 'Jadwal Pengambilan Rapor Semester Genap',
                'user'   => $schoolAdmin->id,
                'target' => 'all',
                'pinned' => true,
                'konten' => "Pengambilan rapor dilaksanakan pada:\n"
                          . "📅 Tanggal : 30 Juni – 4 Juli 2025\n"
                          . "🕗 Pukul   : 08.00 – 12.00 WIB\n"
                          . "📍 Tempat  : Ruang Kelas Masing-masing\n\n"
                          . "Wali murid diwajibkan hadir untuk menandatangani rapor.",
            ],
            [
                'judul'  => 'Rekap Absensi Semester Genap 2024/2025',
                'user'   => $teachers[0]->user->id,
                'target' => 'student',
                'pinned' => false,
                'konten' => "Kepada seluruh siswa,\n\n"
                          . "Rekap absensi Semester Genap 2024/2025 telah tersedia di portal AcaHub.\n"
                          . "Silakan login dan cek halaman Absensi untuk melihat rekapitulasi kehadiran Anda.\n\n"
                          . "Bagi yang memiliki ketidakhadiran lebih dari 15%, harap segera menemui wali kelas.",
            ],
            [
                'judul'  => 'Libur Akhir Tahun Ajaran 2024/2025',
                'user'   => $schoolAdmin->id,
                'target' => 'all',
                'pinned' => false,
                'konten' => "Libur Akhir Tahun Ajaran 2024/2025 ditetapkan mulai:\n"
                          . "📅 5 Juli – 13 Juli 2025\n\n"
                          . "Tahun Ajaran 2025/2026 akan dimulai pada 14 Juli 2025.\n"
                          . "Informasi MPLS akan diumumkan lebih lanjut.",
            ],
            [
                'judul'  => 'Nilai UAS Matematika Telah Diinput',
                'user'   => $teachers[0]->user->id,
                'target' => 'student',
                'pinned' => false,
                'konten' => "Kepada seluruh siswa,\n\n"
                          . "Nilai UAS Matematika Semester Genap 2024/2025 telah dimasukkan ke sistem.\n"
                          . "Silakan cek di menu Rapor untuk melihat nilai lengkap Anda.\n\n"
                          . "Terima kasih atas kerja keras selama semester ini. Semangat terus!",
            ],
        ];

        foreach ($announcements as $ann) {
            Announcement::firstOrCreate(
                ['judul' => $ann['judul'], 'school_id' => $school->id],
                [
                    'user_id'   => $ann['user'],
                    'konten'    => $ann['konten'],
                    'target'    => $ann['target'],
                    'is_pinned' => $ann['pinned'],
                ]
            );
        }

        // ─────────────────────────────────────────
        // Ringkasan output
        // ─────────────────────────────────────────
        $totalSessions  = count($classrooms) * 20;
        $totalAttend    = count($allStudentProfiles) * 20; // per siswa 20 sesi (approx)
        $totalGrades    = Grade::whereHas(
            'studentProfile.user', fn($q) => $q->where('school_id', $school->id)
        )->count();

        $this->command->info('');
        $this->command->info('✅  SMASelesaiSeeder selesai!');
        $this->command->info("    Sekolah       : {$school->name} (ID: {$school->id})");
        $this->command->info('    Tahun Ajaran  : 2024/2025 Genap — SELESAI (is_active=false)');
        $this->command->info('    School Admin  : admin@sman3cendekia.sch.id');
        $this->command->info('    Guru          : ' . count($teachers) . ' orang');
        $this->command->info('    Matkul        : ' . count($subjects) . ' mata pelajaran');
        $this->command->info('    Kelas         : ' . count($classrooms) . ' kelas');
        $this->command->info('    Siswa         : ' . count($allStudentProfiles) . ' siswa');
        $this->command->info('    Wali Murid    : ' . count($parentData) . ' orang');
        $this->command->info("    Sesi Absensi  : {$totalSessions} sesi (semua closed)");
        $this->command->info("    Nilai/Rapor   : {$totalGrades} record (tugas×3 + uts + uas + praktik)");
        $this->command->info('    Pengumuman    : ' . count($announcements) . ' buah');
        $this->command->info('');
        $this->command->info('    Akun contoh login (Student/Parent tab):');
        $this->command->info('    Siswa : aditya.p@sman3cendekia.sch.id / password');
        $this->command->info('    Ortu  : pramana.ortu@gmail.com / password');
        $this->command->info('');
    }
}
