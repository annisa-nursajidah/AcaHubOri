@extends('layouts.authenticated')
@section('content')
@php $title = 'Kalender Akademik'; @endphp

@php
    $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);
    $prevMonth = $currentDate->copy()->subMonth();
    $nextMonth = $currentDate->copy()->addMonth();
    $daysInMonth = $currentDate->daysInMonth;
    $startDow = $currentDate->dayOfWeek; // 0=Sun
    $today = now();
@endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Kalender Akademik</h1>
        <p class="text-sm text-gray-500 mt-1">Jadwal dan event penting</p>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
    <a href="{{ route('events.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Event
    </a>
    @endif
</div>

{{-- Month Navigation --}}
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('events.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
       class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
        ← {{ $prevMonth->translatedFormat('F') }}
    </a>
    <h2 class="text-xl font-bold text-gray-800">{{ $currentDate->translatedFormat('F Y') }}</h2>
    <a href="{{ route('events.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
       class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
        {{ $nextMonth->translatedFormat('F') }} →
    </a>
</div>

{{-- Calendar Grid --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Day headers --}}
    <div class="grid grid-cols-7 bg-gray-50/80 border-b border-gray-100">
        @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day)
        <div class="px-2 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $day }}</div>
        @endforeach
    </div>

    {{-- Calendar days --}}
    <div class="grid grid-cols-7">
        {{-- Empty cells before month starts --}}
        @for($i = 0; $i < $startDow; $i++)
        <div class="min-h-[100px] border-b border-r border-gray-50 bg-gray-50/30"></div>
        @endfor

        @for($day = 1; $day <= $daysInMonth; $day++)
        @php
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dayEvents = $events->filter(function($e) use ($dateStr) {
                return $e->tanggal_mulai->format('Y-m-d') <= $dateStr && $e->tanggal_selesai->format('Y-m-d') >= $dateStr;
            });
            $isToday = $today->format('Y-m-d') === $dateStr;
        @endphp
        <div class="min-h-[100px] border-b border-r border-gray-50 p-2 {{ $isToday ? 'bg-brand-50/50' : 'hover:bg-gray-50/50' }} transition">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium {{ $isToday ? 'w-7 h-7 bg-brand-500 text-white rounded-full flex items-center justify-center' : 'text-gray-700' }}">{{ $day }}</span>
            </div>
            @foreach($dayEvents->take(3) as $ev)
            <a href="{{ route('events.show', $ev) }}" class="block px-2 py-1 mb-1 rounded-md text-xs font-medium truncate transition hover:opacity-80"
               style="background-color: {{ $ev->warna }}20; color: {{ $ev->warna }};">
                {{ $ev->judul }}
            </a>
            @endforeach
            @if($dayEvents->count() > 3)
            <p class="text-xs text-gray-400 px-2">+{{ $dayEvents->count() - 3 }} lagi</p>
            @endif
        </div>
        @endfor

        {{-- Empty cells after month ends --}}
        @php $remaining = (7 - (($startDow + $daysInMonth) % 7)) % 7; @endphp
        @for($i = 0; $i < $remaining; $i++)
        <div class="min-h-[100px] border-b border-r border-gray-50 bg-gray-50/30"></div>
        @endfor
    </div>
</div>

{{-- Event List Below Calendar --}}
@if($events->count() > 0)
<div class="mt-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Event Bulan Ini</h3>
    <div class="space-y-3">
        @foreach($events as $ev)
        <a href="{{ route('events.show', $ev) }}" class="flex items-center gap-4 bg-white rounded-xl border border-gray-100 p-4 hover:shadow-sm transition">
            <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $ev->warna }}"></div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800">{{ $ev->judul }}</p>
                <p class="text-xs text-gray-500">{{ $ev->tanggal_mulai->format('d M H:i') }} — {{ $ev->tanggal_selesai->format('d M H:i') }}</p>
            </div>
            <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background-color: {{ $ev->warna }}15; color: {{ $ev->warna }}">{{ ucfirst($ev->tipe) }}</span>
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection
