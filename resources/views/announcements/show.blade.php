@extends('layouts.authenticated')
@section('content')
@php $title = $announcement->judul; @endphp

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('announcements.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <h1 class="text-2xl font-extrabold text-gray-900">{{ $announcement->judul }}</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Meta --}}
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3 text-sm">
                <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-xs">
                    {{ strtoupper(substr($announcement->author->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $announcement->author->name }}</p>
                    <p class="text-xs text-gray-400">{{ $announcement->created_at->format('d M Y, H:i') }} · {{ $announcement->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($announcement->is_pinned)
                    <span class="inline-flex px-2 py-0.5 rounded-full bg-accent-50 text-accent-700 text-xs font-medium">📌 Pinned</span>
                @endif
                @php
                    $tb = match($announcement->target) { 'teacher' => 'bg-blue-50 text-blue-700', 'student' => 'bg-green-50 text-green-700', default => 'bg-gray-100 text-gray-600' };
                    $tl = match($announcement->target) { 'teacher' => 'Guru', 'student' => 'Siswa', default => 'Semua' };
                @endphp
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $tb }}">{{ $tl }}</span>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6 prose prose-sm max-w-none text-gray-700 leading-relaxed">
            {!! nl2br(e($announcement->konten)) !!}
        </div>
    </div>
</div>
@endsection
