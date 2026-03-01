@extends('layouts.authenticated')
@section('content')
@php $title = 'Mata Pelajaran'; @endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Mata Pelajaran</h1>
        <p class="text-sm text-gray-500 mt-0.5">Daftar semua mata pelajaran</p>
    </div>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('subjects.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Mapel
        </a>
    @endif
</div>

{{-- Search --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('subjects.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode..."
               class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition">Cari</button>
        @if(request('search'))
            <a href="{{ route('subjects.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">Reset</a>
        @endif
    </form>
</div>

{{-- Grid --}}
@if($subjects->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
        <p class="text-gray-500 font-medium">Belum ada mata pelajaran</p>
    </div>
@else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($subjects as $subject)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <span class="inline-flex px-2.5 py-1 rounded-lg bg-brand-50 text-brand-700 text-xs font-bold tracking-wide">{{ $subject->kode }}</span>
                        @if(auth()->user()->isAdmin())
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('subjects.edit', $subject) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('subjects.destroy', $subject) }}" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('subjects.show', $subject) }}" class="block">
                        <h3 class="font-bold text-gray-800 group-hover:text-brand-600 transition">{{ $subject->nama }}</h3>
                        @if($subject->deskripsi)
                            <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $subject->deskripsi }}</p>
                        @endif
                    </a>
                    <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-50 text-xs text-gray-400">
                        <span>{{ $subject->teachers_count }} guru</span>
                        <span>{{ $subject->grades_count }} nilai</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($subjects->hasPages())
        <div class="mt-6">{{ $subjects->links() }}</div>
    @endif
@endif

@endsection
