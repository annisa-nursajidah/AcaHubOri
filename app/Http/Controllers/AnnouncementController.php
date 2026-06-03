<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display all announcements visible to the current user.
     */
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Announcement::with('author')
            ->where('school_id', $user->school_id)
            ->visibleTo($user->role)
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(10)->withQueryString();

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Show form to create an announcement.
     */
    public function create()
    {
        if (! in_array(auth()->user()->role, ['admin', 'school_admin', 'teacher'])) {
            abort(403);
        }
        return view('announcements.create');
    }

    /**
     * Store a new announcement.
     */
    public function store(Request $request)
    {
        if (! in_array($request->user()->role, ['admin', 'school_admin', 'teacher'])) {
            abort(403);
        }

        $validated = $request->validate([
            'judul'     => ['required', 'string', 'max:255'],
            'konten'    => ['required', 'string'],
            'target'    => ['required', 'in:all,teacher,student'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $validated['user_id']   = $request->user()->id;
        $validated['school_id'] = $request->user()->school_id;
        $validated['is_pinned'] = $request->boolean('is_pinned');

        Announcement::create($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat!');
    }

    /**
     * Show a single announcement.
     */
    public function show(Announcement $announcement)
    {
        if ($announcement->school_id !== auth()->user()->school_id) abort(403);
        $announcement->load('author');
        return view('announcements.show', compact('announcement'));
    }

    /**
     * Show form to edit an announcement.
     */
    public function edit(Announcement $announcement)
    {
        $user = auth()->user();
        if ($announcement->school_id !== $user->school_id) abort(403);
        if (! $user->isAdmin() && $announcement->user_id !== $user->id) {
            abort(403);
        }
        return view('announcements.edit', compact('announcement'));
    }

    /**
     * Update an announcement.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $user = $request->user();
        if ($announcement->school_id !== $user->school_id) abort(403);
        if (! $user->isAdmin() && $announcement->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'judul'     => ['required', 'string', 'max:255'],
            'konten'    => ['required', 'string'],
            'target'    => ['required', 'in:all,teacher,student'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $validated['is_pinned'] = $request->boolean('is_pinned');
        $announcement->update($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Announcement $announcement)
    {
        $user = auth()->user();
        if ($announcement->school_id !== $user->school_id) abort(403);
        if (! $user->isAdmin() && $announcement->user_id !== $user->id) {
            abort(403);
        }

        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus!');
    }
}
