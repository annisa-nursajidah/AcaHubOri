<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\Message;
use App\Models\StudentProfile;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class MessageController extends Controller
{
    public function inbox()
    {
        $messages = Message::where('receiver_id', Auth::id())
            ->with('sender')
            ->latest()
            ->paginate(20);

        return view('messages.inbox', compact('messages'));
    }

    public function sent()
    {
        $messages = Message::where('sender_id', Auth::id())
            ->with('receiver')
            ->latest()
            ->paginate(20);

        return view('messages.sent', compact('messages'));
    }

    public function show(Message $message)
    {
        // Only sender or receiver can view
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        // Mark as read if receiver is viewing
        if ($message->receiver_id === Auth::id()) {
            $message->markAsRead();
        }

        return view('messages.show', compact('message'));
    }

    public function create()
    {
        $user  = Auth::user();
        $users = $this->resolveAllowedRecipients($user);

        return view('messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id|different:' . Auth::id(),
            'subject'     => 'required|string|max:255',
            'body'        => 'required|string',
        ]);

        // Guru: pastikan penerima memang boleh dihubungi
        if ($user->isTeacher()) {
            $allowedIds = $this->resolveAllowedRecipients($user)->pluck('id');
            if (! $allowedIds->contains($validated['receiver_id'])) {
                return back()->withErrors(['receiver_id' => 'Anda hanya dapat mengirim pesan kepada siswa atau wali murid yang ada dalam mata pelajaran Anda.'])->withInput();
            }
        }

        $validated['sender_id'] = Auth::id();
        $message = Message::create($validated);

        // Send notification to receiver
        try {
            $receiver = User::find($validated['receiver_id']);
            $receiver->notify(new NewMessageNotification($message));
        } catch (\Exception $e) {
            // Notification failed, message was still created
        }

        return redirect()->route('messages.inbox')
            ->with('success', 'Pesan berhasil dikirim.');
    }

    public function destroy(Message $message)
    {
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return redirect()->route('messages.inbox')
            ->with('success', 'Pesan berhasil dihapus.');
    }

    // ─── Private Helper ──────────────────────────────────────────────────────

    /**
     * Kembalikan koleksi User yang boleh dikirimi pesan oleh $user.
     *
     * - Guru      : hanya siswa yang ter-enroll di kelas yang dia ajar
     *               (melalui AttendanceSession di mata pelajarannya)
     *               + wali murid dari siswa tersebut
     * - Lainnya   : semua user di sekolah (kecuali dirinya sendiri)
     */
    private function resolveAllowedRecipients(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if (! $user->isTeacher()) {
            return User::where('id', '!=', $user->id)
                ->where('school_id', $user->school_id)
                ->orderBy('name')
                ->get();
        }

        $teacherProfile = $user->teacherProfile;

        if (! $teacherProfile) {
            return collect();
        }

        // Ambil ID subject yang diampu guru ini
        $subjectIds = $teacherProfile->subjects()->pluck('subjects.id');

        if ($subjectIds->isEmpty()) {
            return collect();
        }

        // Ambil classroom_id dari attendance_sessions milik guru & subject yang diampu
        // (classroom yang pernah ada sesi absensi dengan subject guru ini)
        $classroomIds = AttendanceSession::where('teacher_id', $user->id)
            ->whereIn('subject_id', $subjectIds)
            ->pluck('classroom_id')
            ->unique();

        // Jika belum ada sesi absensi, fallback: ambil semua siswa aktif di sekolah
        // yang ada di kelas yang wali kelasnya adalah guru ini (atau sekolah yang sama)
        if ($classroomIds->isEmpty()) {
            // Fallback: ambil student_profile_id dari enrollments aktif di sekolah ini
            $studentProfileIds = Enrollment::whereHas(
                'classroom',
                fn($q) => $q->where('school_id', $user->school_id)
            )->where('status', 'active')
              ->pluck('student_profile_id');
        } else {
            // Siswa yang aktif terdaftar di kelas-kelas tersebut
            $studentProfileIds = Enrollment::whereIn('classroom_id', $classroomIds)
                ->where('status', 'active')
                ->pluck('student_profile_id');
        }

        // User siswa dari profil tersebut
        $studentUserIds = StudentProfile::whereIn('id', $studentProfileIds)
            ->pluck('user_id');

        // Wali murid dari siswa-siswa tersebut
        $parentUserIds = DB::table('parent_student')
            ->whereIn('student_id', $studentUserIds)
            ->pluck('parent_id');

        // Gabungkan siswa + wali murid, exclude diri sendiri
        $allowedIds = $studentUserIds->merge($parentUserIds)->unique()->values();

        return User::whereIn('id', $allowedIds)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();
    }
}
