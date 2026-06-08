@extends('layouts.authenticated')
@section('content')
@php $title = 'Tambah Nilai'; @endphp

<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('grades.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Tambah Nilai</h1>
            <p class="text-sm text-gray-500">Masukkan data nilai baru</p>
        </div>
    </div>

    {{-- Info scope untuk guru --}}
    @if(auth()->user()->isTeacher())
        <div class="mb-5 flex items-start gap-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 text-sm">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            <span>Anda hanya dapat memberikan nilai untuk <strong>mata pelajaran yang Anda ampu</strong> dan <strong>siswa aktif</strong> di sekolah Anda.</span>
        </div>
    @endif

    {{-- Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('grades.store') }}" class="space-y-5">
            @csrf

            {{-- Student --}}
            <div>
                <label for="student_profile_id" class="block text-sm font-medium text-gray-700 mb-1">Siswa <span class="text-red-400">*</span></label>
                <select id="student_profile_id" name="student_profile_id" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    <option value="">Pilih Siswa</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_profile_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->user->name }} {{ $student->nis ? '('.$student->nis.')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('student_profile_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject --}}
            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-400">*</span></label>
                @if(auth()->user()->isTeacher() && $subjects->count() === 1)
                    {{-- Guru hanya punya 1 matkul: auto-select dan lock --}}
                    <input type="hidden" name="subject_id" value="{{ $subjects->first()->id }}">
                    <div class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-700">
                        {{ $subjects->first()->nama }} ({{ $subjects->first()->kode }})
                        <span class="ml-2 text-xs text-gray-400">— mata pelajaran Anda</span>
                    </div>
                @else
                    <select id="subject_id" name="subject_id" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->nama }} ({{ $subject->kode }})
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('subject_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teacher (if admin) --}}
            @if(auth()->user()->isAdmin())
                <div>
                    <label for="teacher_profile_id" class="block text-sm font-medium text-gray-700 mb-1">Guru</label>
                    <select id="teacher_profile_id" name="teacher_profile_id"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        <option value="">Pilih Guru</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_profile_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_profile_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            {{-- Nilai & Tipe row --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="nilai" class="block text-sm font-medium text-gray-700 mb-1">Nilai <span class="text-red-400">*</span></label>
                    <input id="nilai" name="nilai" type="number" step="0.01" min="0" max="100" value="{{ old('nilai') }}" required
                        placeholder="0 - 100"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('nilai')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe Penilaian <span class="text-red-400">*</span></label>
                    <select id="tipe" name="tipe" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        <option value="">Pilih Tipe</option>
                        @foreach(['tugas' => 'Tugas', 'uts' => 'UTS', 'uas' => 'UAS', 'praktik' => 'Praktik'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('tipe') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('tipe')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Semester & Tahun Ajaran row --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-400">*</span></label>
                    <select id="semester" name="semester" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        <option value="">Pilih Semester</option>
                        <option value="Ganjil" {{ old('semester') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ old('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                    @error('semester')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran <span class="text-red-400">*</span></label>
                    <input id="tahun_ajaran" name="tahun_ajaran" type="text" value="{{ old('tahun_ajaran', '2025/2026') }}" required
                        placeholder="2025/2026"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('tahun_ajaran')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Catatan --}}
            <div>
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea id="catatan" name="catatan" rows="3" placeholder="Catatan tambahan (opsional)"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition resize-none">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-accent-500 text-white font-semibold text-sm hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40">
                    Simpan Nilai
                </button>
                <a href="{{ route('grades.index') }}" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
