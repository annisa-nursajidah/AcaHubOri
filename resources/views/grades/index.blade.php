@extends('layouts.authenticated')
@section('content')
@php $title = 'Nilai / Grades'; @endphp

{{-- Header row --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Daftar Nilai</h1>
        <p class="text-sm text-gray-500 mt-0.5">Kelola data nilai siswa</p>
    </div>

    @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
        <a href="{{ route('grades.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Nilai
        </a>
    @endif
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('grades.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari siswa atau mata pelajaran..."
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
        </div>
        <select name="semester" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
            <option value="">Semua Semester</option>
            <option value="Ganjil" {{ request('semester') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
            <option value="Genap" {{ request('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
        </select>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition">
            Filter
        </button>
        @if(request()->hasAny(['search', 'semester']))
            <a href="{{ route('grades.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition text-center">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($grades->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            <p class="text-gray-500 font-medium">Belum ada data nilai</p>
            <p class="text-gray-400 text-sm mt-1">Klik "Tambah Nilai" untuk memulai.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">#</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Siswa</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Mata Pelajaran</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Tipe</th>
                        <th class="text-center px-6 py-3.5 font-semibold text-gray-600">Nilai</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Semester</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Guru</th>
                        <th class="text-center px-6 py-3.5 font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($grades as $i => $grade)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-3.5 text-gray-400">{{ $grades->firstItem() + $i }}</td>
                            <td class="px-6 py-3.5 font-medium text-gray-800">{{ $grade->studentProfile?->user?->name ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-600">{{ $grade->subject?->nama ?? '-' }}</td>
                            <td class="px-6 py-3.5">
                                @php
                                    $tipeBadge = match($grade->tipe) {
                                        'tugas'   => 'bg-blue-50 text-blue-700',
                                        'uts'     => 'bg-amber-50 text-amber-700',
                                        'uas'     => 'bg-purple-50 text-purple-700',
                                        'praktik' => 'bg-green-50 text-green-700',
                                        default   => 'bg-gray-50 text-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tipeBadge }}">
                                    {{ strtoupper($grade->tipe) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                @php
                                    $nilaiColor = $grade->nilai >= 75 ? 'text-green-600' : ($grade->nilai >= 50 ? 'text-amber-600' : 'text-red-600');
                                @endphp
                                <span class="font-bold {{ $nilaiColor }}">{{ number_format($grade->nilai, 1) }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-gray-600">{{ $grade->semester }} — {{ $grade->tahun_ajaran }}</td>
                            <td class="px-6 py-3.5 text-gray-500">{{ $grade->teacherProfile?->user?->name ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('grades.show', $grade) }}" title="Detail" class="p-1.5 rounded-lg text-gray-400 hover:text-brand-600 hover:bg-brand-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                    </a>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                                        <a href="{{ route('grades.edit', $grade) }}" title="Edit" class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('grades.destroy', $grade) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus nilai ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($grades->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $grades->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
