<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─── Admin ───────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin AcaHub',
            'email'    => 'admin@acahub.test',
            'password' => Hash::make('password'),
            'role'     => 'admin',
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
                'name'     => $t['name'],
                'email'    => $t['email'],
                'password' => Hash::make('password'),
                'role'     => 'teacher',
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
                'name'     => $s['name'],
                'email'    => $s['email'],
                'password' => Hash::make('password'),
                'role'     => 'student',
            ]);
            $students[] = StudentProfile::create([
                'user_id' => $user->id,
                'nis'     => $s['nis'],
                'kelas'   => $s['kelas'],
            ]);
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
        $statuses = ['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa'];

        foreach ($students as $student) {
            foreach (array_slice($subjects, 0, 3) as $idx => $subject) {
                // 5 days in the past week
                for ($d = 1; $d <= 5; $d++) {
                    Attendance::create([
                        'student_profile_id'  => $student->id,
                        'subject_id'          => $subject->id,
                        'teacher_profile_id'  => $teachers[$idx % count($teachers)]->id,
                        'tanggal'             => now()->subDays(7 - $d)->format('Y-m-d'),
                        'status'              => $statuses[array_rand($statuses)],
                    ]);
                }
            }
        }
    }
}

