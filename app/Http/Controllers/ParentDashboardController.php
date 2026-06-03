<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Utama Orang Tua (Berisi daftar Anak-anaknya).
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role !== 'parent') abort(403);

        // Ambil profil sang wali beserta anak-anak yang tertaut (beserta profil siswanya)
        $parent = User::with('children.studentProfile', 'children.school')->find($user->id);

        return view('parent-dashboard.index', compact('parent'));
    }

    /**
     * Menampilkan Detail Laporan Akademik dan Absensi dari Spesifik Anak.
     */
    public function showChild(User $child)
    {
        $user = auth()->user();
        if ($user->role !== 'parent') abort(403);

        // Verifikasi bahwa anak ini TEPAT terhubung dengan parent yang sedang login
        if (!$user->children->contains($child->id)) {
            abort(403, 'Akses ditolak: Anak tidak tertaut pada akun Anda.');
        }

        // Load data Rapor (Grades), Absensi (Attendances), dan Jadwal Kelas (Enrollments)
        $child->load([
            'studentProfile',
            'school',
        ]);

        return view('parent-dashboard.show', compact('child'));
    }
}
