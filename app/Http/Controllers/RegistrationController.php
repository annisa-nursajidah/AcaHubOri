<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegistrationController extends Controller
{
    /**
     * Show the public registration form for a specific school.
     */
    public function showRegistrationForm($schoolIdentifier)
    {
        $school = School::where('is_active', true)
            ->where(function($query) use ($schoolIdentifier) {
                $query->where('id', $schoolIdentifier)
                      ->orWhere('slug', $schoolIdentifier);
            })->firstOrFail();

        // Optional: Check if school has remaining quota before showing form
        if (!$school->canCreateAccount()) {
            return view('errors.quota_exceeded', ['school' => $school]);
        }

        return view('registration.form', compact('school'));
    }

    /**
     * Handle the registration request.
     */
    public function register(Request $request, $schoolIdentifier)
    {
        $school = School::where('is_active', true)
            ->where(function($query) use ($schoolIdentifier) {
                $query->where('id', $schoolIdentifier)
                      ->orWhere('slug', $schoolIdentifier);
            })->firstOrFail();

        if (!$school->canCreateAccount()) {
            return back()->with('error', 'Maaf, kuota pendaftaran untuk sekolah ini telah penuh.');
        }

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code'  => ['nullable', 'string', 'max:20'],
            'parent_name'  => ['nullable', 'string', 'max:255', 'required_with:parent_email'],
            'parent_email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email', 'required_with:parent_name'],
        ], [
            'parent_email.unique' => 'Email Wali Murid ini sudah terdaftar di sistem AcaHub. Silakan gunakan email lain atau hubungi admin sekolah.',
        ]);

        $status = 'pending';
        // Check if an invite code was provided and matches the school's invite code
        if (!empty($validated['invite_code']) && $school->invite_code && strtoupper($validated['invite_code']) === strtoupper($school->invite_code)) {
            $status = 'active';
        }

        // 1. Buat Akun Siswa
        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'student',
            'school_id' => $school->id,
        ]);

        // 2. Buat Profil Pendidikan
        StudentProfile::create([
            'user_id' => $user->id,
            'status'  => $status,
        ]);

        // 3. (Otomatis) Buat Akun Parent Jika Diisi
        $hasParentAccount = false;
        if (!empty($validated['parent_name']) && !empty($validated['parent_email'])) {
            $parent = User::create([
                'name'      => $validated['parent_name'],
                'email'     => $validated['parent_email'],
                'password'  => Hash::make($validated['password']), // Samakan sandinya dengan siswa
                'role'      => 'parent',
                'school_id' => $school->id,
            ]);

            // Tautkan relasi Pivot Parent <-> Student
            $parent->children()->sync([$user->id]);
            $hasParentAccount = true;
        }

        if ($status === 'active') {
             $successMsg = 'Pendaftaran Siswa berhasil menggunakan Kode Undangan! Silakan langsung login.';
             if ($hasParentAccount) {
                 $successMsg .= ' Akun Parent/Wali juga telah diregistrasikan dengan Password yang sama.';
             }
             return redirect()->route('login')->with('success', $successMsg);
        }

        $pendingMsg = 'Pendaftaran berhasil! Silakan tunggu persetujuan dari Admin Sekolah.';
        if ($hasParentAccount) {
             $pendingMsg = 'Pendaftaran Siswa & Akun Wali Murid berhasil! Silakan tunggu persetujuan Admin Sekolah.';
        }
        return redirect()->route('registration.success', $school->id)->with('success', $pendingMsg);
    }

    public function success($schoolId)
    {
        $school = School::findOrFail($schoolId);
        return view('registration.success', compact('school'));
    }
}
