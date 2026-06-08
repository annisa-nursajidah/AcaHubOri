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
 * SMASeeder — IDEMPOTENT
 * ─────────────────────────────────────────────────────────────────
 * Aman dijalankan berkali-kali (php artisan db:seed --class=SMASeeder).
 * Semua entitas pakai firstOrCreate / updateOrCreate.
 *
 * Isi:
 *  - 1 Sekolah  : SMA Negeri 7 Nusantara, Bandung
 *  - 1 Plan + Subscription aktif
 *  - 1 Tahun Ajaran aktif (2025/2026 Semester Ganjil)
 *  - 1 School Admin
 *  - 5 Guru (tiap guru mengampu 1 mata pelajaran)
 *  - 5 Mata Pelajaran (1 per guru)
 *  - 3 Kelas  : X-IPA-1, XI-IPS-2, XII-IPA-1 (dengan wali kelas)
 *  - 15 Siswa (5 per kelas), ter-enroll ke kelas + tahun ajaran
 *  - 10 Wali Murid (3 wali punya 2 anak, 7 punya 1 anak)
 *  - 15 Sesi Absensi (5 per kelas) + 75 record absensi
 *  - 225 Nilai (15 siswa × 5 matkul × 3 tipe: tugas/uts/uas)
 *  - 5 Pengumuman
 *
 * Password semua akun: password
 */
class SMASeeder extends Seeder
{
    public function run(): void
    {
        // ── Pastikan ENUM role mendukung 'parent' ────────────────
        // Migration 2026_02_28_000004 menimpa ENUM dan menghapus 'parent'.
        DB::statement(
            "ALTER TABLE users MODIFY COLUMN role "
            . "ENUM('admin','teacher','student','school_admin','parent') DEFAULT 'student'"
        );

        // ────────────────────────────────────────────
        // 1. SEKOLAH
        // ────────────────────────────────────────────
        $school = School::firstOrCreate(
            ['slug' => 'sman-7-nusantara'],
            [
                'name'        => 'SMA Negeri 7 Nusantara',
                'address'     => 'Jl. Pelajar Raya No. 7, Kota Bandung, Jawa Barat 40123',
                'phone'       => '022-7654321',
                'email'       => 'info@sman7nusantara.sch.id',
                'logo'        => null,
                'is_active'   => true,
                'invite_code' => strtoupper(Str::random(8)),
            ]
        );

        // ────────────────────────────────────────────
        // 2. SUBSCRIPTION PLAN & SCHOOL SUBSCRIPTION
        // ────────────────────────────────────────────
        $plan = SubscriptionPlan::firstOrCreate(
            ['name' => 'Paket Sekolah Lengkap'],
            [
                'description'       => 'Plan demo untuk 1 sekolah SMA dengan semua fitur aktif.',
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

        // Satu subscription per sekolah cukup
        SchoolSubscription::firstOrCreate(
            ['school_id' => $school->id, 'plan_id' => $plan->id],
            [
                'total_accounts'    => 200,
                'price_per_account' => 4500,
                'total_price'       => 200 * 4500,
                'status'            => 'active',
                'starts_at'         => now()->startOfYear(),
                'expires_at'        => now()->startOfYear()->addYear(),
                'notes'             => 'Seeder — aktif untuk TA 2025/2026',
            ]
        );

        // ────────────────────────────────────────────
        // 3. TAHUN AJARAN
        // ────────────────────────────────────────────
        // Unique constraint global di (tahun, semester), pakai firstOrCreate
        $academicYear = AcademicYear::firstOrCreate(
            ['tahun' => '2025/2026', 'semester' => 'Ganjil'],
            [
                'school_id'       => $school->id,
                'tanggal_mulai'   => '2025-07-14',
                'tanggal_selesai' => '2025-12-20',
                'is_active'       => true,
            ]
        );

        // ────────────────────────────────────────────
        // 4. SCHOOL ADMIN
        // ────────────────────────────────────────────
        $schoolAdmin = User::firstOrCreate(
            ['email' => 'admin@sman7nusantara.sch.id'],
            [
                'name'      => 'Kepala TU SMAN 7',
                'password'  => Hash::make('password'),
                'role'      => 'school_admin',
                'school_id' => $school->id,
            ]
        );

        // ────────────────────────────────────────────
        // 5. GURU + MATA PELAJARAN
        // ────────────────────────────────────────────
        $teacherData = [
            [
                'name'    => 'Drs. Ahmad Fauzi, M.Pd',
                'email'   => 'ahmad.fauzi@sman7nusantara.sch.id',
                'nip'     => '197601152005011002',
                'telepon' => '0812-1111-0001',
                'alamat'  => 'Jl. Veteran No. 12, Bandung',
                'subject' => ['nama' => 'Matematika',      'kode' => 'MTK-7'],
            ],
            [
                'name'    => 'Ibu Sari Dewi, S.Pd',
                'email'   => 'sari.dewi@sman7nusantara.sch.id',
                'nip'     => '198203102008012001',
                'telepon' => '0813-2222-0002',
                'alamat'  => 'Jl. Merdeka No. 5, Bandung',
                'subject' => ['nama' => 'Bahasa Indonesia', 'kode' => 'BIN-7'],
            ],
            [
                'name'    => 'Bpk. Rizky Pratama, S.Pd',
                'email'   => 'rizky.pratama@sman7nusantara.sch.id',
                'nip'     => '199001202015011003',
                'telepon' => '0814-3333-0003',
                'alamat'  => 'Jl. Pahlawan No. 20, Bandung',
                'subject' => ['nama' => 'Bahasa Inggris',  'kode' => 'BIG-7'],
            ],
            [
                'name'    => 'Ibu Nurul Hidayah, M.Si',
                'email'   => 'nurul.hidayah@sman7nusantara.sch.id',
                'nip'     => '198507182010012004',
                'telepon' => '0815-4444-0004',
                'alamat'  => 'Jl. Diponegoro No. 9, Bandung',
                'subject' => ['nama' => 'Fisika',           'kode' => 'FIS-7'],
            ],
            [
                'name'    => 'Bpk. Hendra Gunawan, S.E',
                'email'   => 'hendra.gunawan@sman7nusantara.sch.id',
                'nip'     => '197912052006011005',
                'telepon' => '0816-5555-0005',
                'alamat'  => 'Jl. Sudirman No. 33, Bandung',
                'subject' => ['nama' => 'Ekonomi',          'kode' => 'EKO-7'],
            ],
        ];

        $teachers = [];
        $subjects  = [];

        foreach ($teacherData as $td) {
            // User guru
            $userTeacher = User::firstOrCreate(
                ['email' => $td['email']],
                [
                    'name'      => $td['name'],
                    'password'  => Hash::make('password'),
                    'role'      => 'teacher',
                    'school_id' => $school->id,
                ]
            );

            // Teacher profile
            $profile = TeacherProfile::firstOrCreate(
                ['user_id' => $userTeacher->id],
                [
                    'nip'     => $td['nip'],
                    'telepon' => $td['telepon'],
                    'alamat'  => $td['alamat'],
                ]
            );

            // Mata pelajaran (1 per guru)
            $subject = Subject::firstOrCreate(
                ['kode' => $td['subject']['kode']],
                [
                    'school_id'  => $school->id,
                    'nama'       => $td['subject']['nama'],
                    'deskripsi'  => 'Mata pelajaran ' . $td['subject']['nama'] . ' untuk SMA.',
                ]
            );

            // Assign subject → teacher (pivot), hindari duplikat
            if (! $profile->subjects()->where('subjects.id', $subject->id)->exists()) {
                $profile->subjects()->attach($subject->id);
            }

            $teachers[] = $profile;
            $subjects[]  = $subject;
        }

        // ────────────────────────────────────────────
        // 6. KELAS (3 kelas dengan wali kelas)
        // ────────────────────────────────────────────
        $classroomsData = [
            ['nama' => 'X-IPA-1',   'tingkat' => 10, 'wali_idx' => 0],
            ['nama' => 'XI-IPS-2',  'tingkat' => 11, 'wali_idx' => 1],
            ['nama' => 'XII-IPA-1', 'tingkat' => 12, 'wali_idx' => 2],
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

        // ────────────────────────────────────────────
        // 7. SISWA (5 per kelas = 15 total)
        // ────────────────────────────────────────────
        $studentData = [
            // X-IPA-1
            ['name' => 'Alfarizi Nugraha',    'email' => 'alfarizi@sman7nusantara.sch.id',    'nis' => '2025001'],
            ['name' => 'Bunga Citra Lestari', 'email' => 'bunga.citra@sman7nusantara.sch.id', 'nis' => '2025002'],
            ['name' => 'Cahya Ramadhan',      'email' => 'cahya.r@sman7nusantara.sch.id',     'nis' => '2025003'],
            ['name' => 'Dinda Permatasari',   'email' => 'dinda.p@sman7nusantara.sch.id',     'nis' => '2025004'],
            ['name' => 'Eko Prasetyo',        'email' => 'eko.p@sman7nusantara.sch.id',       'nis' => '2025005'],
            // XI-IPS-2
            ['name' => 'Fajar Sidiq',         'email' => 'fajar.sidiq@sman7nusantara.sch.id', 'nis' => '2024001'],
            ['name' => 'Gita Maharani',       'email' => 'gita.m@sman7nusantara.sch.id',      'nis' => '2024002'],
            ['name' => 'Hasan Basri',         'email' => 'hasan.b@sman7nusantara.sch.id',     'nis' => '2024003'],
            ['name' => 'Intan Rahayu',        'email' => 'intan.r@sman7nusantara.sch.id',     'nis' => '2024004'],
            ['name' => 'Joko Santoso',        'email' => 'joko.s@sman7nusantara.sch.id',      'nis' => '2024005'],
            // XII-IPA-1
            ['name' => 'Kartika Sari',        'email' => 'kartika.s@sman7nusantara.sch.id',   'nis' => '2023001'],
            ['name' => 'Lukman Hakim',        'email' => 'lukman.h@sman7nusantara.sch.id',    'nis' => '2023002'],
            ['name' => 'Maya Anggraeni',      'email' => 'maya.a@sman7nusantara.sch.id',      'nis' => '2023003'],
            ['name' => 'Nando Firmansyah',    'email' => 'nando.f@sman7nusantara.sch.id',     'nis' => '2023004'],
            ['name' => 'Olivia Putri',        'email' => 'olivia.p@sman7nusantara.sch.id',    'nis' => '2023005'],
        ];

        // Tanggal lahir deterministik agar tidak berubah tiap run
        $birthDates = [
            '2009-03-12', '2009-07-25', '2009-11-05', '2010-01-18', '2009-05-30',
            '2008-04-14', '2008-09-22', '2008-12-01', '2009-02-17', '2008-06-09',
            '2007-08-20', '2007-10-03', '2008-01-27', '2007-03-15', '2007-12-11',
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
                    'alamat'        => 'Jl. Siswa No. ' . ($idx + 1) . ', Bandung',
                    'status'        => 'active',
                ]
            );

            // Enrollment — idempoten lewat unique constraint
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

        // ────────────────────────────────────────────
        // 8. WALI MURID (10 ortu, 3 punya 2 anak)
        // ────────────────────────────────────────────
        $parentData = [
            ['name' => 'Bpk. Nugraha Santosa',  'email' => 'nugraha.ortu@gmail.com',   'children_idx' => [0]],
            ['name' => 'Ibu Lestari Wahyuni',    'email' => 'lestari.ortu@gmail.com',   'children_idx' => [1]],
            ['name' => 'Bpk. Ramadhan Hadi',     'email' => 'ramadhan.ortu@gmail.com',  'children_idx' => [2]],
            ['name' => 'Ibu Wulandari Puri',     'email' => 'wulandari.ortu@gmail.com', 'children_idx' => [3, 5]], // 2 anak
            ['name' => 'Bpk. Prasetyo Utama',    'email' => 'prasetyo.ortu@gmail.com',  'children_idx' => [4]],
            ['name' => 'Ibu Maharani Susanti',   'email' => 'maharani.ortu@gmail.com',  'children_idx' => [6]],
            ['name' => 'Bpk. Basri Hidayat',     'email' => 'basri.ortu@gmail.com',     'children_idx' => [7]],
            ['name' => 'Ibu Rahayu Ningsih',     'email' => 'rahayu.ortu@gmail.com',    'children_idx' => [8, 10]], // 2 anak
            ['name' => 'Bpk. Santoso Budi',      'email' => 'santoso.ortu@gmail.com',   'children_idx' => [9]],
            ['name' => 'Ibu Anggraeni Dewi',     'email' => 'anggraeni.ortu@gmail.com', 'children_idx' => [11, 12]], // 2 anak
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

        // ────────────────────────────────────────────
        // 9. SESI ABSENSI + RECORD ABSENSI
        //    5 sesi per kelas (tanggal berbeda, matkul round-robin)
        // ────────────────────────────────────────────
        $attendanceStatuses = ['present', 'present', 'present', 'present', 'late', 'excused', 'sick', 'absent'];
        // Seed PRNG agar status tidak berubah tiap run
        mt_srand(42);

        foreach ($classrooms as $cIdx => $classroom) {
            $classStudents = $students[$cIdx] ?? [];

            for ($day = 1; $day <= 5; $day++) {
                $subjectIdx  = ($cIdx + $day - 1) % count($subjects);
                $teacherIdx  = ($cIdx + $day - 1) % count($teachers);
                $sessionDate = now()->subDays(20 - ($day * 3))->format('Y-m-d');
                $token       = hash('sha256', "sma7-{$cIdx}-{$day}-session");

                $session = AttendanceSession::firstOrCreate(
                    ['qr_code_token' => $token],
                    [
                        'school_id'    => $school->id,
                        'classroom_id' => $classroom->id,
                        'subject_id'   => $subjects[$subjectIdx]->id,
                        'teacher_id'   => $teachers[$teacherIdx]->user->id,
                        'date'         => $sessionDate,
                        'start_time'   => '07:30:00',
                        'end_time'     => '09:00:00',
                        'status'       => 'closed',
                    ]
                );

                foreach ($classStudents as $sPos => $studentProfile) {
                    Attendance::firstOrCreate(
                        [
                            'attendance_session_id' => $session->id,
                            'student_id'            => $studentProfile->user_id,
                        ],
                        [
                            'school_id'  => $school->id,
                            'date'       => $sessionDate,
                            'status'     => $attendanceStatuses[($cIdx + $day + $sPos) % count($attendanceStatuses)],
                            'scanned_at' => $sessionDate . ' 07:' . str_pad(30 + ($sPos * 3), 2, '0', STR_PAD_LEFT) . ':00',
                            'notes'      => null,
                        ]
                    );
                }
            }
        }

        // ────────────────────────────────────────────
        // 10. NILAI (GRADES)
        //     15 siswa × 5 matkul × 3 tipe = 225 nilai
        //     Nilai deterministik agar tidak berubah tiap run
        // ────────────────────────────────────────────
        $tipes = ['tugas', 'uts', 'uas'];
        $nilaiTable = [88, 75, 90, 82, 70, 95, 78, 85, 68, 92, 80, 73, 88, 76, 91];

        foreach ($allStudentProfiles as $sIdx => $studentProfile) {
            foreach ($subjects as $subIdx => $subject) {
                $assignedTeacher = $teachers[$subIdx];
                foreach ($tipes as $tPos => $tipe) {
                    // Nilai deterministik dari tabel, variasi per tipe
                    $baseNilai = $nilaiTable[$sIdx] - ($tPos * 3) + ($subIdx * 2);
                    $nilai     = max(60, min(100, $baseNilai));

                    Grade::firstOrCreate(
                        [
                            'student_profile_id' => $studentProfile->id,
                            'subject_id'         => $subject->id,
                            'tipe'               => $tipe,
                            'semester'           => 'Ganjil',
                            'tahun_ajaran'       => '2025/2026',
                        ],
                        [
                            'teacher_profile_id' => $assignedTeacher->id,
                            'nilai'              => $nilai,
                            'catatan'            => null,
                        ]
                    );
                }
            }
        }

        // ────────────────────────────────────────────
        // 11. PENGUMUMAN (5 buah)
        // ────────────────────────────────────────────
        $announcements = [
            [
                'judul'  => 'Selamat Datang di Tahun Ajaran 2025/2026',
                'user'   => $schoolAdmin->id,
                'target' => 'all',
                'pinned' => true,
                'konten' => "Dengan penuh semangat kami membuka TA 2025/2026 Semester Ganjil.\n\n"
                          . "Seluruh siswa diharap hadir tepat waktu dan mengikuti MPLS selama minggu pertama.",
            ],
            [
                'judul'  => 'Jadwal Ujian Tengah Semester Ganjil',
                'user'   => $schoolAdmin->id,
                'target' => 'student',
                'pinned' => false,
                'konten' => "UTS Ganjil 2025/2026 dilaksanakan pada:\n- Tanggal: 6–10 Oktober 2025\n"
                          . "- Pukul: 07.30–11.30 WIB\n\nBawa kartu ujian dan alat tulis lengkap.",
            ],
            [
                'judul'  => 'Remedial Matematika Kelas X-IPA-1',
                'user'   => $teachers[0]->user->id,
                'target' => 'student',
                'pinned' => false,
                'konten' => "Siswa X-IPA-1 yang belum mencapai KKM harap hadir sesi remedial:\n"
                          . "- Rabu, 17 September 2025 | 14.00–15.30 WIB | Ruang X-IPA-1",
            ],
            [
                'judul'  => 'Rapat Orang Tua/Wali Murid',
                'user'   => $schoolAdmin->id,
                'target' => 'all',
                'pinned' => true,
                'konten' => "Undangan rapat wali murid:\n- Sabtu, 4 Oktober 2025 | 09.00 WIB\n"
                          . "- Aula SMA Negeri 7 Nusantara\n\nKehadiran sangat diharapkan.",
            ],
            [
                'judul'  => 'Tugas Bahasa Inggris – Speaking Practice',
                'user'   => $teachers[2]->user->id,
                'target' => 'student',
                'pinned' => false,
                'konten' => "Tugas minggu ini: video speaking 2–3 menit tema \"My Dream Career\".\n"
                          . "Upload via portal AcaHub, deadline Jumat 19 September 2025.",
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

        // ────────────────────────────────────────────
        // Ringkasan output
        // ────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅  SMASeeder selesai!');
        $this->command->info("    Sekolah      : {$school->name} (ID: {$school->id})");
        $this->command->info('    School Admin : admin@sman7nusantara.sch.id');
        $this->command->info('    Guru         : ' . count($teachers) . ' orang (password: password)');
        $this->command->info('    Matkul       : ' . count($subjects) . ' mata pelajaran');
        $this->command->info('    Kelas        : ' . count($classrooms) . ' kelas');
        $this->command->info('    Siswa        : ' . count($allStudentProfiles) . ' siswa');
        $this->command->info('    Wali Murid   : ' . count($parentData) . ' orang');
        $this->command->info('    Sesi Absensi : ' . (count($classrooms) * 5) . ' sesi');
        $this->command->info('    Nilai        : ' . (count($allStudentProfiles) * count($subjects) * count($tipes)) . ' record');
        $this->command->info('    Pengumuman   : ' . count($announcements) . ' buah');
        $this->command->info('');
    }
}
