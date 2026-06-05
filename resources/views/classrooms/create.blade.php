@extends('layouts.authenticated')
@section('content')
@php $title = 'Tambah Kelas'; @endphp

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Kelas</h1>

    <form method="POST" action="{{ route('classrooms.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Kelas <span class="text-red-400">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: 10A" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition @error('nama') border-red-300 @enderror">
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tingkat <span class="text-red-400">*</span></label>
                <input type="number" name="tingkat" value="{{ old('tingkat') }}" min="1" max="12" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition @error('tingkat') border-red-300 @enderror">
                @error('tingkat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Wali Kelas</label>
            <select name="wali_kelas_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                <option value="">— Pilih Wali Kelas —</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ old('wali_kelas_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->user->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran <span class="text-red-400">*</span></label>
            <select name="academic_year_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                <option value="">— Pilih Tahun Ajaran —</option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->full_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">Simpan</button>
            <a href="{{ route('classrooms.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
