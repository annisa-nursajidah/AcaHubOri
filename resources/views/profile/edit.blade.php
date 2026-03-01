@extends('layouts.authenticated')
@section('content')
@php $title = 'Edit Profil'; @endphp

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('profile.show') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <h1 class="text-2xl font-extrabold text-gray-900">Edit Profil</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Student fields --}}
            @if($user->isStudent())
                <div class="p-4 rounded-xl bg-green-50/50 border border-green-100 space-y-4">
                    <p class="text-xs font-bold text-green-700 uppercase tracking-wider">Data Siswa</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="nis" class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                            <input id="nis" name="nis" type="text" value="{{ old('nis', $user->studentProfile?->nis) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        </div>
                        <div>
                            <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                            <input id="kelas" name="kelas" type="text" value="{{ old('kelas', $user->studentProfile?->kelas) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        </div>
                    </div>
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $user->studentProfile?->tanggal_lahir) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    </div>
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition resize-none">{{ old('alamat', $user->studentProfile?->alamat) }}</textarea>
                    </div>
                </div>
            @endif

            {{-- Teacher fields --}}
            @if($user->isTeacher())
                <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-100 space-y-4">
                    <p class="text-xs font-bold text-blue-700 uppercase tracking-wider">Data Guru</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                            <input id="nip" name="nip" type="text" value="{{ old('nip', $user->teacherProfile?->nip) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        </div>
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                            <input id="telepon" name="telepon" type="text" value="{{ old('telepon', $user->teacherProfile?->telepon) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        </div>
                    </div>
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition resize-none">{{ old('alamat', $user->teacherProfile?->alamat) }}</textarea>
                    </div>
                </div>
            @endif

            {{-- Password change --}}
            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 space-y-4">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ubah Password</p>
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                    <input id="current_password" name="current_password" type="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition" placeholder="Kosongkan jika tidak ingin mengubah">
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input id="new_password" name="new_password" type="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                        @error('new_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi</label>
                        <input id="new_password_confirmation" name="new_password_confirmation" type="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-3 rounded-xl bg-accent-500 text-white font-semibold text-sm hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all">Simpan Perubahan</button>
                <a href="{{ route('profile.show') }}" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
