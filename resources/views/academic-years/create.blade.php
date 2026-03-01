@extends('layouts.authenticated')
@section('content')
@php $title = 'Tambah Tahun Ajaran'; @endphp

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Tahun Ajaran</h1>

    <form method="POST" action="{{ route('academic-years.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran</label>
            <input type="text" name="tahun" value="{{ old('tahun') }}" placeholder="Contoh: 2025/2026"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition @error('tahun') border-red-300 @enderror">
            @error('tahun') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Semester</label>
            <select name="semester" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                <option value="Ganjil" {{ old('semester') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                <option value="Genap" {{ old('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
            </select>
            @error('semester') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition @error('tanggal_mulai') border-red-300 @enderror">
                @error('tanggal_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition @error('tanggal_selesai') border-red-300 @enderror">
                @error('tanggal_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">Simpan</button>
            <a href="{{ route('academic-years.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
