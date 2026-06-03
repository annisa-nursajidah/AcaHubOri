<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    /**
     * Menampilkan daftar Orang Tua (Wali Murid) beserta anak mereka.
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user->isSchoolAdmin() && !$user->isAdmin()) abort(403);

        $parents = User::where('role', 'parent')
            ->where('school_id', $user->school_id)
            ->with('children') // Relasi Many-to-Many
            ->latest()
            ->paginate(15);

        return view('parents.index', compact('parents'));
    }

    /**
     * Menampilkan Form Pendaftaran Orang Tua.
     */
    public function create()
    {
         $user = auth()->user();
         if (!$user->isSchoolAdmin() && !$user->isAdmin()) abort(403);

         // Ambil semua murid yang ada di sekolah ini untuk dipilih di form
         $students = User::where('role', 'student')
             ->where('school_id', $user->school_id)
             ->orderBy('name', 'asc')
             ->get();

         return view('parents.create', compact('students'));
    }

    /**
     * Memproses Pendaftaran Akun Orang Tua dan menyambungkannya ke Anak.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSchoolAdmin() && !$user->isAdmin()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'student_ids' => 'required|array', // Harus pilih setidaknya 1 anak
            'student_ids.*' => 'exists:users,id',
        ]);

        // Pastikan student yg dipilih benar-benar anak dari sekolah yg sama (Proteksi)
        $validStudents = User::whereIn('id', $request->student_ids)
            ->where('role', 'student')
            ->where('school_id', $user->school_id)
            ->pluck('id')
            ->toArray();

        // 1. Buat User Parent
        $parent = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'parent',
            'school_id' => $user->school_id, 
        ]);

        // 2. Hubungkan Parent dengan Anak (Pivot parent_student)
        $parent->children()->sync($validStudents);

        return redirect()->route('parents.index')->with('success', 'Akun Wali Murid Berhasil Dibuat dan Dihubungkan ke Siswa!');
    }

    /**
     * Menghapus Akun Orang Tua (Cascade akan langsung memutus Pivotnya).
     */
    public function destroy(User $parent)
    {
        $user = auth()->user();
        if (!$user->isSchoolAdmin() && !$user->isAdmin()) abort(403);

        if ($parent->school_id !== $user->school_id || $parent->role !== 'parent') {
            abort(403, 'Akses tidak sah.');
        }

        $parent->delete();

        return redirect()->route('parents.index')->with('success', 'Akun Wali Murid telah dihapus.');
    }
}
