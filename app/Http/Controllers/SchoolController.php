<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolSubscription;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Display a listing of schools (admin only).
     */
    public function index()
    {
        $schools = School::withCount('users')
            ->latest()
            ->paginate(15);

        return view('schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        return view('schools.create');
    }

    /**
     * Store a newly created school.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'required|email|unique:schools,email',
        ]);

        $school = School::create($validated);

        return redirect()->route('schools.show', $school)
            ->with('success', 'Sekolah berhasil ditambahkan.');
    }

    /**
     * Display the specified school.
     */
    public function show(School $school)
    {
        $school->load(['users', 'subscriptions.plan']);
        $activeSubscription = $school->activeSubscription();

        return view('schools.show', compact('school', 'activeSubscription'));
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    /**
     * Update the specified school.
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string|max:500',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'required|email|unique:schools,email,' . $school->id,
            'is_active' => 'boolean',
        ]);

        $school->update($validated);

        return redirect()->route('schools.show', $school)
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    /**
     * Remove the specified school.
     */
    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('schools.index')
            ->with('success', 'Sekolah berhasil dihapus.');
    }

    /**
     * Regenerate the invite code for the school.
     */
    public function regenerateInviteCode(School $school)
    {
        $user = auth()->user();
        
        // Ensure only the super admin or the school's admin can do this
        if (!$user->isAdmin() && ($user->role !== 'school_admin' || $user->school_id !== $school->id)) {
            abort(403, 'Unauthorized action.');
        }

        $school->update([
            'invite_code' => strtoupper(\Illuminate\Support\Str::random(8))
        ]);

        return back()->with('success', 'Kode undangan PPDB berhasil diperbarui menjadi: ' . $school->invite_code);
    }
}
