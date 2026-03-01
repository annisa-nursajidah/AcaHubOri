@extends('layouts.authenticated')
@section('content')
@php $title = 'Edit Pengumuman'; @endphp

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('announcements.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <h1 class="text-2xl font-extrabold text-gray-900">Edit Pengumuman</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('announcements.update', $announcement) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-400">*</span></label>
                <input id="judul" name="judul" type="text" value="{{ old('judul', $announcement->judul) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="konten" class="block text-sm font-medium text-gray-700 mb-1">Konten <span class="text-red-400">*</span></label>
                <textarea id="konten" name="konten" rows="6" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition resize-none">{{ old('konten', $announcement->konten) }}</textarea>
                @error('konten')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="target" class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                    <select id="target" name="target" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        @foreach(['all' => 'Semua', 'teacher' => 'Guru saja', 'student' => 'Siswa saja'] as $v => $l)
                            <option value="{{ $v }}" {{ old('target', $announcement->target) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2.5 px-4 py-3 rounded-xl border border-gray-200 cursor-pointer has-[:checked]:border-accent-500 has-[:checked]:bg-accent-50 transition w-full">
                        <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }} class="rounded border-gray-300 text-accent-600 focus:ring-accent-500">
                        <span class="text-sm text-gray-700">📌 Sematkan di atas</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-3 rounded-xl bg-accent-500 text-white font-semibold text-sm hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all">Perbarui</button>
                <a href="{{ route('announcements.index') }}" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
