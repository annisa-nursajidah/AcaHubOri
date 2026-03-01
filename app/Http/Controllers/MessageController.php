<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->get();

        return view('messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id|different:' . Auth::id(),
            'subject'     => 'required|string|max:255',
            'body'        => 'required|string',
        ]);

        $validated['sender_id'] = Auth::id();

        $message = Message::create($validated);

        // Send notification to receiver
        try {
            $receiver = User::find($validated['receiver_id']);
            $receiver->notify(new NewMessageNotification($message));
        } catch (\Exception $e) {
            // Notification sending failed, but message was created
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
}
