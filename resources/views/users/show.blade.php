@extends('layouts.authenticated')
@section('content')
@php $title = $user->name; @endphp

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $user->name }}</h1>
        </div>
        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
            Edit
        </a>
    </div>

    {{-- User info card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
        <div class="bg-gradient-to-r from-brand-500 to-brand-600 px-6 py-8 text-white flex items-center gap-5">
            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-3xl font-black">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-xl font-bold">{{ $user->name }}</p>
                <p class="text-white/80 text-sm">{{ $user->email }}</p>
            </div>
        </div>

        <div class="p-6 divide-y divide-gray-50">
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Role</span>
                @php
                    $roleBadge = match($user->role) {
                        'admin'   => 'bg-red-50 text-red-700',
                        'teacher' => 'bg-blue-50 text-blue-700',
                        'student' => 'bg-green-50 text-green-700',
                        default   => 'bg-gray-50 text-gray-700',
                    };
                @endphp
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleBadge }}">{{ ucfirst($user->role) }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Bergabung</span>
                <span class="text-sm text-gray-700">{{ $user->created_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- Profile details --}}
    @if($user->isStudent() && $user->studentProfile)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Profil Siswa</h3>
            <div class="space-y-2">
                <div class="flex justify-between py-2"><span class="text-sm text-gray-500">NIS</span><span class="text-sm text-gray-700">{{ $user->studentProfile->nis ?? '-' }}</span></div>
                <div class="flex justify-between py-2"><span class="text-sm text-gray-500">Kelas</span><span class="text-sm text-gray-700">{{ $user->studentProfile->kelas ?? '-' }}</span></div>
                <div class="flex justify-between py-2"><span class="text-sm text-gray-500">Tanggal Lahir</span><span class="text-sm text-gray-700">{{ $user->studentProfile->tanggal_lahir ?? '-' }}</span></div>
                <div class="flex justify-between py-2"><span class="text-sm text-gray-500">Alamat</span><span class="text-sm text-gray-700">{{ $user->studentProfile->alamat ?? '-' }}</span></div>
            </div>
        </div>
    @endif

    @if($user->isTeacher() && $user->teacherProfile)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Profil Guru</h3>
            <div class="space-y-2">
                <div class="flex justify-between py-2"><span class="text-sm text-gray-500">NIP</span><span class="text-sm text-gray-700">{{ $user->teacherProfile->nip ?? '-' }}</span></div>
                <div class="flex justify-between py-2"><span class="text-sm text-gray-500">Telepon</span><span class="text-sm text-gray-700">{{ $user->teacherProfile->telepon ?? '-' }}</span></div>
                <div class="flex justify-between py-2"><span class="text-sm text-gray-500">Alamat</span><span class="text-sm text-gray-700">{{ $user->teacherProfile->alamat ?? '-' }}</span></div>
            </div>
        </div>

        @if($user->teacherProfile->subjects->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-3">Mata Pelajaran ({{ $user->teacherProfile->subjects->count() }})</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($user->teacherProfile->subjects as $subject)
                        <a href="{{ route('subjects.show', $subject) }}" class="inline-flex px-3 py-1.5 rounded-lg bg-brand-50 text-brand-700 text-xs font-medium hover:bg-brand-100 transition">
                            {{ $subject->nama }} ({{ $subject->kode }})
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
