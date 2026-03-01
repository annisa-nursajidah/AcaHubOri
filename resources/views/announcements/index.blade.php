@extends('layouts.authenticated')
@section('content')
@php $title = 'Pengumuman'; @endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Pengumuman</h1>
        <p class="text-sm text-gray-500 mt-0.5">Informasi dan pemberitahuan terbaru</p>
    </div>
    @if(in_array(auth()->user()->role, ['admin', 'teacher']))
        <a href="{{ route('announcements.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Buat Pengumuman
        </a>
    @endif
</div>

{{-- Search --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('announcements.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengumuman..."
               class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition">Cari</button>
    </form>
</div>

{{-- Feed --}}
@forelse($announcements as $a)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden hover:shadow-md transition-shadow {{ $a->is_pinned ? 'ring-2 ring-accent-300' : '' }}">
        <div class="p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        @if($a->is_pinned)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-accent-50 text-accent-700 text-[10px] font-bold uppercase">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.162-.682 22.045 22.045 0 01-2.734-2.025C3.647 12.354 2 10.143 2 7.5A5.5 5.5 0 0112 3.528 5.5 5.5 0 0117.5 7.5c0 2.643-1.648 4.854-3.733 6.695a22.045 22.045 0 01-3.527 2.635l-.093.055-.019.01-.005.003z"/></svg>
                                Disematkan
                            </span>
                        @endif
                        @php
                            $targetBadge = match($a->target) {
                                'teacher' => 'bg-blue-50 text-blue-700',
                                'student' => 'bg-green-50 text-green-700',
                                default   => 'bg-gray-100 text-gray-600',
                            };
                            $targetLabel = match($a->target) {
                                'teacher' => 'Guru',
                                'student' => 'Siswa',
                                default   => 'Semua',
                            };
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $targetBadge }}">{{ $targetLabel }}</span>
                    </div>
                    <a href="{{ route('announcements.show', $a) }}" class="block">
                        <h3 class="text-lg font-bold text-gray-800 hover:text-brand-600 transition">{{ $a->judul }}</h3>
                    </a>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($a->konten), 180) }}</p>
                </div>

                @if(auth()->user()->isAdmin() || $a->user_id === auth()->id())
                    <div class="flex gap-1 flex-shrink-0">
                        <a href="{{ route('announcements.edit', $a) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('announcements.destroy', $a) }}" onsubmit="return confirm('Yakin hapus pengumuman ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-50 text-xs text-gray-400">
                <div class="flex items-center gap-1.5">
                    <div class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-[9px]">
                        {{ strtoupper(substr($a->author->name, 0, 1)) }}
                    </div>
                    <span>{{ $a->author->name }}</span>
                </div>
                <span>·</span>
                <span>{{ $a->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
@empty
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
        <p class="text-gray-500 font-medium">Belum ada pengumuman</p>
    </div>
@endforelse

@if($announcements->hasPages())
    <div class="mt-4">{{ $announcements->links() }}</div>
@endif
@endsection
