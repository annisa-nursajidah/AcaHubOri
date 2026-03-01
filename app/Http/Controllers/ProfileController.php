<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;

class ProfileController extends Controller
{
    /**
     * Show the current user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $user->load(['studentProfile', 'teacherProfile.subjects']);

        return view('profile.show', compact('user'));
    }

    /**
     * Show the edit form for the current user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $user->load(['studentProfile', 'teacherProfile']);

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the current user's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password'  => ['nullable', 'required_with:new_password'],
            'new_password'      => ['nullable', 'confirmed', 'min:8'],
            // Student fields
            'nis'               => ['nullable', 'string', 'max:50'],
            'kelas'             => ['nullable', 'string', 'max:50'],
            'tanggal_lahir'     => ['nullable', 'date'],
            'alamat'            => ['nullable', 'string', 'max:500'],
            // Teacher fields
            'nip'               => ['nullable', 'string', 'max:50'],
            'telepon'           => ['nullable', 'string', 'max:20'],
        ]);

        // Verify current password if changing
        if ($request->filled('new_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.'])->withInput();
            }
            $user->password = Hash::make($validated['new_password']);
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        // Update profile
        if ($user->isStudent()) {
            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                array_filter([
                    'nis'           => $validated['nis'] ?? null,
                    'kelas'         => $validated['kelas'] ?? null,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                    'alamat'        => $validated['alamat'] ?? null,
                ], fn($v) => $v !== null)
            );
        } elseif ($user->isTeacher()) {
            TeacherProfile::updateOrCreate(
                ['user_id' => $user->id],
                array_filter([
                    'nip'     => $validated['nip'] ?? null,
                    'telepon' => $validated['telepon'] ?? null,
                    'alamat'  => $validated['alamat'] ?? null,
                ], fn($v) => $v !== null)
            );
        }

        return redirect()->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}
