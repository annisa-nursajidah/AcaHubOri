@extends('layouts.authenticated')
@section('content')
@php $title = 'Kelola Users'; @endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Kelola Users</h1>
        <p class="text-sm text-gray-500 mt-0.5">Daftar semua pengguna sistem</p>
    </div>
    @php
        $showAddBtn = true;
        if(auth()->user()->isSchoolAdmin()) {
            $school = \App\Models\School::find(auth()->user()->school_id);
            $showAddBtn = $school ? $school->canCreateAccount() : false;
        }
    @endphp

    @if($showAddBtn)
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah User
        </a>
    @else
        <div class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-100 text-gray-500 text-sm font-medium border border-gray-200 cursor-not-allowed cursor-help" title="Kuota akun sekolah Anda telah habis. Harap perbarui langganan.">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Kuota Habis
        </div>
    @endif
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
               class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
        <select name="role" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
            <option value="">Semua Role</option>
            @foreach(['admin' => 'Admin', 'teacher' => 'Teacher', 'student' => 'Student'] as $val => $lbl)
                <option value="{{ $val }}" {{ request('role') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition">Filter</button>
        @if(request()->hasAny(['search', 'role']))
            <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition text-center">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100">
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">User</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Email</th>
                    <th class="text-center px-6 py-3.5 font-semibold text-gray-600">Role</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Bergabung</th>
                    <th class="text-center px-6 py-3.5 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-xs">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-3.5 text-center">
                            @php
                                $roleBadge = match($user->role) {
                                    'admin'   => 'bg-red-50 text-red-700',
                                    'teacher' => 'bg-blue-50 text-blue-700',
                                    'student' => 'bg-green-50 text-green-700',
                                    default   => 'bg-gray-50 text-gray-700',
                                };
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleBadge }}">{{ ucfirst($user->role) }}</span>
                            @if($user->role === 'student' && $user->studentProfile && $user->studentProfile->status === 'pending')
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 ml-1 border border-yellow-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>
                                    Pending PPDB
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-gray-400 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('users.show', $user) }}" title="Detail" class="p-1.5 rounded-lg text-gray-400 hover:text-brand-600 hover:bg-brand-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                </a>
                                <a href="{{ route('users.edit', $user) }}" title="Edit" class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        </button>
                                    </form>
                                @endif
                                @if($user->role === 'student' && $user->studentProfile && $user->studentProfile->status === 'pending' && auth()->user()->isSchoolAdmin())
                                    <form method="POST" action="{{ route('users.approve', $user) }}" class="inline">
                                        @csrf
                                        <button type="submit" title="Terima Pendaftaran" class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition ml-1" onclick="return confirm('Apakah Anda yakin ingin menerima siswa ini? Kuota tersisa akan berkurang.')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>
@endsection
