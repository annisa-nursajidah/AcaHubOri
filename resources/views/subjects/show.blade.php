@extends('layouts.authenticated')
@section('content')
@php $title = $subject->nama; @endphp

<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('subjects.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">{{ $subject->nama }}</h1>
                <span class="inline-flex px-2 py-0.5 rounded-md bg-brand-50 text-brand-700 text-xs font-bold">{{ $subject->kode }}</span>
            </div>
        </div>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('subjects.edit', $subject) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                Edit
            </a>
        @endif
    </div>

    @if($subject->deskripsi)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
            <p class="text-sm text-gray-600">{{ $subject->deskripsi }}</p>
        </div>
    @endif

    {{-- Teachers --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
        <h3 class="text-sm font-bold text-gray-700 mb-3">Guru Pengampu ({{ $subject->teachers->count() }})</h3>
        @forelse($subject->teachers as $t)
            <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-xs">
                    {{ strtoupper(substr($t->user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $t->user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $t->nip ?? '-' }}</p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">Belum ada guru yang ditugaskan.</p>
        @endforelse
    </div>

    {{-- Recent grades --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50">
            <h3 class="text-sm font-bold text-gray-700">Nilai Terbaru ({{ $subject->grades->count() }})</h3>
        </div>
        @forelse($subject->grades->take(10) as $grade)
            <div class="flex items-center justify-between px-5 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-[10px]">
                        {{ strtoupper(substr($grade->studentProfile?->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm text-gray-700">{{ $grade->studentProfile?->user?->name ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400">{{ strtoupper($grade->tipe) }} · {{ $grade->semester }}</p>
                    </div>
                </div>
                @php $c = $grade->nilai >= 75 ? 'text-green-600' : ($grade->nilai >= 50 ? 'text-amber-600' : 'text-red-600'); @endphp
                <span class="font-bold text-sm {{ $c }}">{{ number_format($grade->nilai, 1) }}</span>
            </div>
        @empty
            <div class="px-5 py-6 text-center text-sm text-gray-400">Belum ada nilai.</div>
        @endforelse
    </div>
</div>
@endsection
