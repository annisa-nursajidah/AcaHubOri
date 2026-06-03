@extends('layouts.authenticated')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('exams.show', $exam) }}" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
    </a>
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Ujian CBT</h1>
        <p class="text-sm text-gray-500 mt-1">Ubah setelan & waktu ujian untuk <strong>{{ $exam->title }}</strong></p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6 sm:p-8 max-w-3xl">
    <form action="{{ route('exams.update', $exam) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Ujian <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all">
            @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="subject_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Mata Pelajaran <span class="text-red-500">*</span></label>
                <select name="subject_id" id="subject_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all">
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="classroom_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Kelas Ujian <span class="text-red-500">*</span></label>
                <select name="classroom_id" id="classroom_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all">
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" {{ old('classroom_id', $exam->classroom_id) == $classroom->id ? 'selected' : '' }}>{{ $classroom->name }} (Tingkat {{ $classroom->grade_level }})</option>
                    @endforeach
                </select>
                @error('classroom_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi / Petunjuk Ujian</label>
            <textarea name="description" id="description" rows="3"
                      class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all">{{ old('description', $exam->description) }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-1.5">Durasi (Menit) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="5" max="300" required
                           class="w-full pl-4 pr-16 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-mono">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium">Menit</span>
                </div>
                @error('duration_minutes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-1.5">Mulai Pukul</label>
                <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-gray-700">
                @error('start_time')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-1.5">Selesai / Terkunci Pukul</label>
                <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-gray-700">
                @error('end_time')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            
            <input type="hidden" name="status" value="{{ $exam->status }}">
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
            <a href="{{ route('exams.show', $exam) }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-colors shadow-sm">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
