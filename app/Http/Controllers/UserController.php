<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $user = auth()->user();
        if ($user->isSchoolAdmin()) {
            $school = \App\Models\School::find($user->school_id);
            if ($school && !$school->canCreateAccount()) {
                return redirect()->route('users.index')
                    ->with('error', 'Kuota pembuatan akun untuk sekolah Anda telah habis. Harap perbarui langganan.');
            }
        }
        return view('users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->isSchoolAdmin()) {
            $school = \App\Models\School::find($user->school_id);
            if ($school && !$school->canCreateAccount()) {
                return redirect()->route('users.index')
                    ->with('error', 'Gagal menyimpan! Kuota pembuatan akun untuk sekolah Anda telah habis.');
            }
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:admin,teacher,student'],
        ]);

        $newUser = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
            'school_id' => $user->school_id, // Tetapkan ID Sekolah Admin
        ]);

        // Auto-create profile based on role
        if ($newUser->role === 'student') {
            StudentProfile::create(['user_id' => $newUser->id]);
        } elseif ($newUser->role === 'teacher') {
            TeacherProfile::create(['user_id' => $newUser->id]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['studentProfile', 'teacherProfile.subjects']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $user->load(['studentProfile', 'teacherProfile']);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:admin,teacher,student'],
            // Student profile fields
            'nis'           => ['nullable', 'string', 'max:50'],
            'kelas'         => ['nullable', 'string', 'max:50'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat'        => ['nullable', 'string', 'max:500'],
            // Teacher profile fields
            'nip'     => ['nullable', 'string', 'max:50'],
            'telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Update or create profile
        if ($user->role === 'student') {
            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nis'           => $validated['nis'] ?? null,
                    'kelas'         => $validated['kelas'] ?? null,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                    'alamat'        => $validated['alamat'] ?? null,
                ]
            );
        } elseif ($user->role === 'teacher') {
            TeacherProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip'     => $validated['nip'] ?? null,
                    'telepon' => $validated['telepon'] ?? null,
                    'alamat'  => $validated['alamat'] ?? null,
                ]
            );
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus!');
    }

    /**
     * Approve pending student registration.
     */
    public function approve(User $user)
    {
        $admin = auth()->user();
        if (!$admin->isSchoolAdmin() || $user->school_id !== $admin->school_id || $user->role !== 'student') {
            abort(403);
        }

        // Check if there's enough quota
        $school = \App\Models\School::find($admin->school_id);
        if ($school && !$school->canCreateAccount()) {
            return back()->with('error', 'Gagal menyetujui pendaftaran. Kuota akun sekolah Anda telah habis.');
        }

        $profile = clone $user->studentProfile;
        if ($profile && $profile->status === 'pending') {
            $user->studentProfile()->update(['status' => 'active']);
            return back()->with('success', 'Pendaftaran Siswa ' . $user->name . ' berhasil disetujui!');
        }

        return back()->with('error', 'Siswa ini tidak dalam status pending.');
    }
}
