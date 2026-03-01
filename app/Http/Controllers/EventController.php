<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $events = Event::with('creator')
            ->where('school_id', auth()->user()->school_id)
            ->whereMonth('tanggal_mulai', $month)
            ->whereYear('tanggal_mulai', $year)
            ->orderBy('tanggal_mulai')
            ->get();

        return view('events.index', compact('events', 'month', 'year'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'           => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tipe'            => 'required|in:akademik,ujian,libur,lainnya',
            'warna'           => 'nullable|string|max:7',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['school_id'] = Auth::user()->school_id;
        if (empty($validated['warna'])) {
            $validated['warna'] = match ($validated['tipe']) {
                'ujian'    => '#ef4444',
                'libur'    => '#22c55e',
                'akademik' => '#0891b2',
                default    => '#6b7280',
            };
        }

        Event::create($validated);

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil ditambahkan.');
    }

    public function show(Event $event)
    {
        if ($event->school_id !== auth()->user()->school_id) abort(403);
        $event->load('creator');

        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        if ($event->school_id !== auth()->user()->school_id) abort(403);
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        if ($event->school_id !== auth()->user()->school_id) abort(403);
        $validated = $request->validate([
            'judul'           => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tipe'            => 'required|in:akademik,ujian,libur,lainnya',
            'warna'           => 'nullable|string|max:7',
        ]);

        $event->update($validated);

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        if ($event->school_id !== auth()->user()->school_id) abort(403);
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    /**
     * JSON endpoint for calendar data.
     */
    public function calendarData(Request $request)
    {
        $events = Event::query()
            ->where('school_id', auth()->user()->school_id)
            ->when($request->start, fn($q) => $q->where('tanggal_mulai', '>=', $request->start))
            ->when($request->end, fn($q) => $q->where('tanggal_selesai', '<=', $request->end))
            ->get()
            ->map(fn($e) => [
                'id'    => $e->id,
                'title' => $e->judul,
                'start' => $e->tanggal_mulai->toIso8601String(),
                'end'   => $e->tanggal_selesai->toIso8601String(),
                'color' => $e->warna,
                'type'  => $e->tipe,
                'url'   => route('events.show', $e),
            ]);

        return response()->json($events);
    }
}
