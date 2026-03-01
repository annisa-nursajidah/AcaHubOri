@extends('layouts.authenticated')
@section('content')
@php $title = 'Detail Nilai'; @endphp

<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('grades.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Detail Nilai</h1>
                <p class="text-sm text-gray-500">Informasi lengkap data nilai</p>
            </div>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
            <div class="flex items-center gap-2">
                <a href="{{ route('grades.edit', $grade) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('grades.destroy', $grade) }}" onsubmit="return confirm('Yakin ingin menghapus?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-red-200 text-sm font-medium text-red-600 hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        Hapus
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- Score card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Score highlight --}}
        @php
            $nilaiColor = $grade->nilai >= 75 ? 'from-green-500 to-emerald-600' : ($grade->nilai >= 50 ? 'from-amber-500 to-orange-600' : 'from-red-500 to-rose-600');
            $nilaiLabel = $grade->nilai >= 75 ? 'Tuntas' : ($grade->nilai >= 50 ? 'Perlu Perbaikan' : 'Belum Tuntas');
        @endphp
        <div class="bg-gradient-to-r {{ $nilaiColor }} px-6 py-8 text-white text-center">
            <p class="text-5xl font-black">{{ number_format($grade->nilai, 1) }}</p>
            <p class="text-white/80 text-sm font-medium mt-1">{{ $nilaiLabel }}</p>
        </div>

        {{-- Details --}}
        <div class="p-6 divide-y divide-gray-50">
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Siswa</span>
                <span class="text-sm font-semibold text-gray-800">{{ $grade->studentProfile?->user?->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">NIS</span>
                <span class="text-sm text-gray-700">{{ $grade->studentProfile?->nis ?? '-' }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Mata Pelajaran</span>
                <span class="text-sm font-semibold text-gray-800">{{ $grade->subject?->nama ?? '-' }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Kode</span>
                <span class="text-sm text-gray-700">{{ $grade->subject?->kode ?? '-' }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Tipe Penilaian</span>
                <span>
                    @php
                        $tipeBadge = match($grade->tipe) {
                            'tugas'   => 'bg-blue-50 text-blue-700',
                            'uts'     => 'bg-amber-50 text-amber-700',
                            'uas'     => 'bg-purple-50 text-purple-700',
                            'praktik' => 'bg-green-50 text-green-700',
                            default   => 'bg-gray-50 text-gray-700',
                        };
                    @endphp
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tipeBadge }}">{{ strtoupper($grade->tipe) }}</span>
                </span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Semester</span>
                <span class="text-sm text-gray-700">{{ $grade->semester }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Tahun Ajaran</span>
                <span class="text-sm text-gray-700">{{ $grade->tahun_ajaran }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Guru</span>
                <span class="text-sm text-gray-700">{{ $grade->teacherProfile?->user?->name ?? '-' }}</span>
            </div>
            @if($grade->catatan)
                <div class="py-3">
                    <p class="text-sm text-gray-500 mb-1">Catatan</p>
                    <p class="text-sm text-gray-800 bg-gray-50 rounded-xl p-3">{{ $grade->catatan }}</p>
                </div>
            @endif
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Terakhir diperbarui</span>
                <span class="text-sm text-gray-700">{{ $grade->updated_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>
</div>

@endsection
