@extends('layouts.authenticated')
@section('content')
@php $title = 'Rapor / Report Card'; @endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">Rapor / Report Card</h1>
        <p class="text-sm text-gray-500 mt-0.5">Pilih siswa untuk melihat rapor</p>
    </div>

    {{-- Student list --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-50">
            @forelse($students as $student)
                <a href="{{ route('report.show', ['student' => $student->id, 'semester' => 'Ganjil', 'tahun' => '2025/2026']) }}"
                   class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition">
                    <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-sm">
                        {{ strtoupper(substr($student->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ $student->user->name }}</p>
                        <p class="text-xs text-gray-400">NIS: {{ $student->nis ?? '-' }} · Kelas: {{ $student->kelas ?? '-' }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            @empty
                <div class="p-12 text-center">
                    <p class="text-gray-400">Belum ada data siswa.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
