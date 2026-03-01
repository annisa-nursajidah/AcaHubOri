@extends('layouts.authenticated')
@section('content')
@php $title = 'Edit Mata Pelajaran'; @endphp

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('subjects.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <h1 class="text-2xl font-extrabold text-gray-900">Edit Mata Pelajaran</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('subjects.update', $subject) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Mapel <span class="text-red-400">*</span></label>
                    <input id="nama" name="nama" type="text" value="{{ old('nama', $subject->nama) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode <span class="text-red-400">*</span></label>
                    <input id="kode" name="kode" type="text" value="{{ old('kode', $subject->kode) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('kode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition resize-none">{{ old('deskripsi', $subject->deskripsi) }}</textarea>
                @error('deskripsi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Guru Pengampu</label>
                @php $assigned = old('teachers', $subject->teachers->pluck('id')->toArray()); @endphp
                <div class="grid grid-cols-2 gap-2">
                    @foreach($teachers as $t)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border border-gray-200 cursor-pointer has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 transition">
                            <input type="checkbox" name="teachers[]" value="{{ $t->id }}" {{ in_array($t->id, $assigned) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-gray-700">{{ $t->user->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-3 rounded-xl bg-accent-500 text-white font-semibold text-sm hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all">Perbarui</button>
                <a href="{{ route('subjects.index') }}" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
