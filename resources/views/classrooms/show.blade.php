@extends('layouts.authenticated')
@section('content')
@php $title = 'Detail Kelas — ' . $classroom->nama; @endphp

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelas {{ $classroom->nama }}</h1>
            <p class="text-sm text-gray-500 mt-1">Tingkat {{ $classroom->tingkat }}
                @if($classroom->academicYear) — {{ $classroom->academicYear->full_name }} @endif
            </p>
        </div>
        <a href="{{ route('classrooms.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">← Kembali</a>
    </div>

    {{-- Info Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Wali Kelas</p>
                <p class="text-sm font-medium text-gray-800">{{ $classroom->waliKelas?->user?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tahun Ajaran</p>
                <p class="text-sm font-medium text-gray-800">{{ $classroom->academicYear?->full_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Jumlah Siswa</p>
                <p class="text-sm font-medium text-gray-800">{{ $classroom->enrollments->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Student List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Daftar Siswa</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600">#</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600">Nama</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600">NIS</th>
                    <th class="text-center px-6 py-3 font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($classroom->enrollments as $i => $enrollment)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-3 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $enrollment->studentProfile->user->name }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $enrollment->studentProfile->nis ?? '—' }}</td>
                    <td class="px-6 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $enrollment->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada siswa terdaftar di kelas ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
