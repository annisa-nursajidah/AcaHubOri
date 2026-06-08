<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            // Tab 'student' dipakai untuk student & parent
            'role'     => ['required', 'in:admin,teacher,student'],
        ]);

        $role = $credentials['role'];
        unset($credentials['role']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Verify the authenticated user has the selected role
            $userRole    = Auth::user()->role;
            $isValidRole = false;

            if ($role === 'admin' && in_array($userRole, ['admin', 'school_admin'])) {
                // Tab Admin: cocok untuk admin & school_admin
                $isValidRole = true;
            } elseif ($role === 'student' && in_array($userRole, ['student', 'parent'])) {
                // Tab Student/Parent: cocok untuk student & parent
                $isValidRole = true;
            } elseif ($role === $userRole) {
                // Tab Teacher: cocok exact
                $isValidRole = true;
            }

            if (!$isValidRole) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'role' => 'The selected role does not match your account.',
                ])->withInput($request->only('email', 'role'));
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'role'));
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
