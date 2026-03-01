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
    public function showRegistrationForm($schoolId)
    {
        $school = School::where('is_active', true)->findOrFail($schoolId);

        // Optional: Check if school has remaining quota before showing form
        if (!$school->canCreateAccount()) {
            return view('errors.quota_exceeded', ['school' => $school]);
        }

        return view('registration.form', compact('school'));
    }

    /**
     * Handle the registration request.
     */
    public function register(Request $request, $schoolId)
    {
        $school = School::where('is_active', true)->findOrFail($schoolId);

        if (!$school->canCreateAccount()) {
            return back()->with('error', 'Maaf, kuota pendaftaran untuk sekolah ini telah penuh.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['nullable', 'string', 'max:20'],
        ]);

        $status = 'pending';
        // Check if an invite code was provided and matches the school's invite code
        if (!empty($validated['invite_code']) && $school->invite_code && strtoupper($validated['invite_code']) === strtoupper($school->invite_code)) {
            $status = 'active';
        }

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'student',
            'school_id' => $school->id,
        ]);

        // Create profile with determined status
        StudentProfile::create([
            'user_id' => $user->id,
            'status'  => $status,
        ]);

        if ($status === 'active') {
            return redirect()->route('login')
                ->with('success', 'Pendaftaran berhasil menggunakan Kode Undangan! Silakan langsung login menggunakan email dan password Anda.');
        }

        return redirect()->route('registration.success', $school->id)
            ->with('success', 'Pendaftaran berhasil! Silakan tunggu persetujuan dari Admin Sekolah.');
    }

    public function success($schoolId)
    {
        $school = School::findOrFail($schoolId);
        return view('registration.success', compact('school'));
    }
}
