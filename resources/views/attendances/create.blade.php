@extends('layouts.authenticated')
@section('content')
@php $title = 'Input Absensi'; @endphp

<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('attendances.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <h1 class="text-2xl font-extrabold text-gray-900">Input Absensi</h1>
    </div>

    <form method="POST" action="{{ route('attendances.store') }}">
        @csrf

        {{-- Info Sesi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
            <h2 class="text-sm font-bold text-gray-700 mb-4">Detail Sesi</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                    <select name="subject_id" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        <option value="">Pilih Mapel</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }} ({{ $s->kode }})</option>
                        @endforeach
                    </select>
                    @error('subject_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kelas <span class="text-red-500">*</span></label>
                    <select name="classroom_id" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        <option value="">Pilih Kelas</option>
                        @foreach($classrooms as $c)
                            <option value="{{ $c->id }}" {{ old('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                        @endforeach
                    </select>
                    @error('classroom_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" value="{{ old('start_time', now()->format('H:i')) }}" required
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('start_time')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Daftar Siswa --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-700">Daftar Siswa</h2>
                <span class="text-xs text-gray-400">{{ $students->count() }} siswa</span>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">#</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Siswa</th>
                        <th class="text-center px-5 py-3.5 font-semibold text-gray-600">Status Kehadiran</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($students as $i => $student)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-[10px]">
                                        {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $student->user->name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $student->nis ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-center gap-1">
                                    @foreach(['present' => '✅ Hadir', 'late' => '⏰ Terlambat', 'sick' => '🏥 Sakit', 'excused' => '📝 Izin', 'absent' => '❌ Alpa'] as $val => $label)
                                        <label class="cursor-pointer" title="{{ $label }}">
                                            <input type="radio" name="attendance[{{ $student->user_id }}][status]" value="{{ $val }}"
                                                {{ $val === 'present' ? 'checked' : '' }}
                                                class="peer sr-only">
                                            <span class="inline-flex px-2 py-1.5 items-center justify-center rounded-lg border-2 border-gray-200 text-xs peer-checked:border-brand-500 peer-checked:bg-brand-50 transition whitespace-nowrap">
                                                {{ $label }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <input type="text" name="attendance[{{ $student->user_id }}][notes]" placeholder="Opsional..."
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-brand-500/30 focus:border-brand-500 transition">
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">Belum ada siswa terdaftar di sekolah ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <button type="submit" class="px-6 py-3 rounded-xl bg-accent-500 text-white font-semibold text-sm hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all">Simpan Absensi</button>
            <a href="{{ route('attendances.index') }}" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
