@extends('layouts.authenticated')
@section('content')
@php $title = 'Profil Saya'; @endphp

<div class="max-w-2xl mx-auto">
    {{-- Header card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
        <div class="bg-gradient-to-r from-brand-500 to-brand-600 px-6 py-10 text-white text-center relative">
            <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-4xl font-black mx-auto mb-3">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <p class="text-xl font-bold">{{ $user->name }}</p>
            <p class="text-white/70 text-sm">{{ $user->email }}</p>
            @php
                $roleBadge = match($user->role) {
                    'admin'   => 'bg-red-400/30',
                    'teacher' => 'bg-blue-400/30',
                    'student' => 'bg-green-400/30',
                    default   => 'bg-gray-400/30',
                };
            @endphp
            <span class="inline-flex mt-2 px-3 py-1 rounded-full text-xs font-medium {{ $roleBadge }} text-white">{{ ucfirst($user->role) }}</span>
            <a href="{{ route('profile.edit') }}" class="absolute top-4 right-4 p-2 rounded-xl bg-white/10 hover:bg-white/20 transition text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
            </a>
        </div>

        <div class="p-6 divide-y divide-gray-50">
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Bergabung</span>
                <span class="text-sm text-gray-700">{{ $user->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Student profile --}}
    @if($user->isStudent() && $user->studentProfile)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500"></span> Profil Siswa
            </h3>
            <div class="grid grid-cols-2 gap-y-3">
                <div><p class="text-xs text-gray-400">NIS</p><p class="text-sm font-medium text-gray-800">{{ $user->studentProfile->nis ?? '-' }}</p></div>
                <div><p class="text-xs text-gray-400">Kelas</p><p class="text-sm font-medium text-gray-800">{{ $user->studentProfile->kelas ?? '-' }}</p></div>
                <div><p class="text-xs text-gray-400">Tanggal Lahir</p><p class="text-sm font-medium text-gray-800">{{ $user->studentProfile->tanggal_lahir ?? '-' }}</p></div>
                <div><p class="text-xs text-gray-400">Alamat</p><p class="text-sm font-medium text-gray-800">{{ $user->studentProfile->alamat ?? '-' }}</p></div>
            </div>
        </div>
    @endif

    {{-- Teacher profile --}}
    @if($user->isTeacher() && $user->teacherProfile)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span> Profil Guru
            </h3>
            <div class="grid grid-cols-2 gap-y-3">
                <div><p class="text-xs text-gray-400">NIP</p><p class="text-sm font-medium text-gray-800">{{ $user->teacherProfile->nip ?? '-' }}</p></div>
                <div><p class="text-xs text-gray-400">Telepon</p><p class="text-sm font-medium text-gray-800">{{ $user->teacherProfile->telepon ?? '-' }}</p></div>
                <div class="col-span-2"><p class="text-xs text-gray-400">Alamat</p><p class="text-sm font-medium text-gray-800">{{ $user->teacherProfile->alamat ?? '-' }}</p></div>
            </div>
        </div>

        @if($user->teacherProfile->subjects->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-3">Mata Pelajaran Saya</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($user->teacherProfile->subjects as $s)
                        <a href="{{ route('subjects.show', $s) }}" class="px-3 py-1.5 rounded-lg bg-brand-50 text-brand-700 text-xs font-medium hover:bg-brand-100 transition">
                            {{ $s->nama }} ({{ $s->kode }})
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
