<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\School;
use App\Models\Classroom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $school = School::create([
            'name'      => 'SMA Negeri 1 AcaHub',
            'slug'      => 'sman-1-acahub',
            'address'   => 'Jl. Pendidikan No. 1, Jakarta',
            'email'     => 'info@sman1acahub.sch.id',
            'phone'     => '021-1234567',
            'is_active' => true,
        ]);

        $plan = \App\Models\SubscriptionPlan::create([
            'name' => 'Paket Premium',
            'description' => 'Paket demo langganan B2B AcaHub',
            'price_per_account' => 5000,
            'min_accounts' => 10,
            'max_accounts' => null,
            'features' => json_encode(['Semua Fitur', 'Dukungan Prioritas']),
            'duration_days' => 365,
            'is_active' => true,
        ]);

        // SUNTIKAN: Buka Kuota Pendaftaran Sekolah Pertama
        \App\Models\SchoolSubscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
            'total_accounts' => 1000,
            'price_per_account' => 5000,
            'total_price' => 5000000,
            'status' => 'active',
        ]);

        // ─── Classrooms ─────────────────────────────────────
        $academicYear = AcademicYear::create([
            'school_id'  => $school->id,
            'tahun'      => '2025/2026',
            'semester'   => 'Ganjil',
            'is_active'  => true,
        ]);

        $classrooms = [];
        $classrooms['X-A'] = Classroom::create([
            'school_id' => $school->id,
            'nama'      => 'X-A',
            'tingkat'   => 10,
        ]);
        $classrooms['X-B'] = Classroom::create([
            'school_id' => $school->id,
            'nama'      => 'X-B',
            'tingkat'   => 10,
        ]);
        // Kelas XI untuk testing fitur naik kelas
        Classroom::create([
            'school_id' => $school->id,
            'nama'      => 'XI-A',
            'tingkat'   => 11,
        ]);
        Classroom::create([
            'school_id' => $school->id,
            'nama'      => 'XI-B',
            'tingkat'   => 11,
        ]);

        // ─── Admin (Super Admin Website) ─────────────────────
        $admin = User::create([
            'name'      => 'Admin AcaHub',
            'email'     => 'admin@acahub.test',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'school_id' => $school->id,
        ]);

        // ─── School Admin ─────────────────────────────────────
        User::create([
            'name'      => 'Admin SMA Negeri 1 AcaHub',
            'email'     => 'schooladmin@acahub.test',
            'password'  => Hash::make('password'),
            'role'      => 'school_admin',
            'school_id' => $school->id,
        ]);

        // ─── Teachers ────────────────────────────────────────
        $teacherUsers = [
            ['name' => 'Budi Santoso',   'email' => 'budi@acahub.test',   'nip' => '198501012010011001'],
            ['name' => 'Siti Rahmawati', 'email' => 'siti@acahub.test',   'nip' => '198703152011012002'],
            ['name' => 'Agus Pratama',   'email' => 'agus@acahub.test',   'nip' => '199005202012011003'],
        ];

        $teachers = [];
        foreach ($teacherUsers as $t) {
            $user = User::create([
                'name'      => $t['name'],
                'email'     => $t['email'],
                'password'  => Hash::make('password'),
                'role'      => 'teacher',
                'school_id' => $school->id,
            ]);
            $teachers[] = TeacherProfile::create([
                'user_id' => $user->id,
                'nip'     => $t['nip'],
            ]);
        }

        // ─── Subjects ────────────────────────────────────────
        $subjectsData = [
            ['nama' => 'Matematika',              'kode' => 'MTK'],
            ['nama' => 'Bahasa Indonesia',         'kode' => 'BIN'],
            ['nama' => 'Bahasa Inggris',           'kode' => 'BIG'],
            ['nama' => 'Ilmu Pengetahuan Alam',    'kode' => 'IPA'],
            ['nama' => 'Ilmu Pengetahuan Sosial',  'kode' => 'IPS'],
            ['nama' => 'Pendidikan Kewarganegaraan', 'kode' => 'PKN'],
        ];

        $subjects = [];
        foreach ($subjectsData as $s) {
            $subjects[] = Subject::create($s);
        }

        // Assign subjects to teachers
        $teachers[0]->subjects()->attach([$subjects[0]->id, $subjects[3]->id]); // Budi: MTK, IPA
        $teachers[1]->subjects()->attach([$subjects[1]->id, $subjects[4]->id]); // Siti: BIN, IPS
        $teachers[2]->subjects()->attach([$subjects[2]->id, $subjects[5]->id]); // Agus: BIG, PKN

        // ─── Students ────────────────────────────────────────
        $studentUsers = [
            ['name' => 'Andi Wijaya',    'email' => 'andi@acahub.test',    'nis' => '2024001', 'kelas' => 'X-A'],
            ['name' => 'Dewi Lestari',   'email' => 'dewi@acahub.test',   'nis' => '2024002', 'kelas' => 'X-A'],
            ['name' => 'Fahri Rahman',   'email' => 'fahri@acahub.test',   'nis' => '2024003', 'kelas' => 'X-B'],
            ['name' => 'Gita Permata',   'email' => 'gita@acahub.test',   'nis' => '2024004', 'kelas' => 'X-B'],
            ['name' => 'Hendra Saputra', 'email' => 'hendra@acahub.test', 'nis' => '2024005', 'kelas' => 'X-A'],
        ];

        $students = [];
        foreach ($studentUsers as $s) {
            $user = User::create([
                'name'      => $s['name'],
                'email'     => $s['email'],
                'password'  => Hash::make('password'),
                'role'      => 'student',
                'school_id' => $school->id,
            ]);
            $profile = StudentProfile::create([
                'user_id' => $user->id,
                'nis'     => $s['nis'],
                'kelas'   => $s['kelas'],
            ]);

            // Daftarkan siswa ke kelas yang sesuai
            Enrollment::create([
                'student_profile_id' => $profile->id,
                'classroom_id'       => $classrooms[$s['kelas']]->id,
                'academic_year_id'   => $academicYear->id,
                'status'             => 'active',
            ]);

            $students[] = $profile;
        }

        // ─── Grades (sample data) ───────────────────────────
        $tipes = ['tugas', 'uts', 'uas', 'praktik'];

        foreach ($students as $student) {
            foreach (array_slice($subjects, 0, 4) as $idx => $subject) {
                // 2-3 grades per student per subject
                $count = rand(2, 3);
                for ($i = 0; $i < $count; $i++) {
                    Grade::create([
                        'student_profile_id' => $student->id,
                        'subject_id'         => $subject->id,
                        'teacher_profile_id' => $teachers[$idx % count($teachers)]->id,
                        'nilai'              => rand(55, 98) + (rand(0, 99) / 100),
                        'tipe'               => $tipes[array_rand($tipes)],
                        'semester'           => 'Ganjil',
                        'tahun_ajaran'       => '2025/2026',
                        'catatan'            => null,
                    ]);
                }
            }
        }

        // ─── Announcements ──────────────────────────────────
        Announcement::create([
            'user_id'   => $admin->id,
            'judul'     => 'Selamat Datang di AcaHub!',
            'konten'    => "AcaHub adalah platform manajemen akademik modern untuk sekolah. Kami menyediakan fitur pengelolaan nilai, absensi, rapor, dan banyak lagi.\n\nSelamat menggunakan AcaHub!",
            'target'    => 'all',
            'is_pinned' => true,
        ]);

        Announcement::create([
            'user_id'   => $admin->id,
            'judul'     => 'Jadwal UTS Semester Ganjil 2025/2026',
            'konten'    => "Ujian Tengah Semester (UTS) akan dilaksanakan pada tanggal 10-14 Maret 2026. Mohon seluruh siswa mempersiapkan diri dengan baik.\n\nJadwal lengkap dapat dilihat di papan pengumuman sekolah.",
            'target'    => 'student',
            'is_pinned' => false,
        ]);

        Announcement::create([
            'user_id'   => $teachers[0]->user_id,
            'judul'     => 'Rapat Koordinasi Guru',
            'konten'    => 'Rapat koordinasi guru akan diadakan pada hari Senin, 3 Maret 2026 pukul 14.00 di ruang rapat. Kehadiran seluruh guru diharapkan.',
            'target'    => 'teacher',
            'is_pinned' => false,
        ]);

        // ─── Attendance (sample data) ────────────────────────
        $statuses = ['present', 'present', 'present', 'present', 'present', 'excused', 'sick', 'absent'];

        // Ciptakan 5 sesi absensi (1 per hari) untuk 1 mata pelajaran pertama
        if (count($subjects) > 0 && count($teachers) > 0 && count($classrooms) > 0) {
            $sampleSubject = $subjects[0];
            $sampleTeacher = $teachers[0];
            $sampleClass   = array_values($classrooms)[0];
            
            for ($d = 1; $d <= 5; $d++) {
                $sessionDate = now()->subDays(7 - $d)->format('Y-m-d');
                $session = \App\Models\AttendanceSession::create([
                    'school_id'    => $school->id,
                    'classroom_id' => $sampleClass->id,
                    'subject_id'   => $sampleSubject->id,
                    'teacher_id'   => $sampleTeacher->user_id,
                    'date'         => $sessionDate,
                    'start_time'   => '08:00:00',
                    'end_time'     => '10:00:00',
                    'qr_code_token'=> 'DUMMY' . $d,
                    'status'       => 'closed', // sudah lalu
                ]);

                // Hadirkan semua anak di kelas itu untuk sesi ini
                foreach ($students as $student) {
                    \App\Models\Attendance::create([
                        'school_id'             => $school->id,
                        'attendance_session_id' => $session->id,
                        'student_id'            => $student->user_id, // Penting! user_id
                        'date'                  => $sessionDate,
                        'status'                => $statuses[array_rand($statuses)],
                        'scanned_at'            => now()->subDays(7 - $d)->setTime(rand(8,9), rand(0,59), rand(0,59)),
                    ]);
                }
            }
        }
    }
}

