@extends('layouts.authenticated')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-black text-gray-900">Kelola Sekolah</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $schools->total() }} sekolah terdaftar</p>
    </div>
    <a href="{{ route('schools.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 shadow-md transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Sekolah
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                <th class="px-6 py-4">Sekolah</th>
                <th class="px-6 py-4">Email</th>
                <th class="px-6 py-4">Kuota Akun</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($schools as $school)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <p class="font-semibold text-gray-800">{{ $school->name }}</p>
                    <p class="text-xs text-gray-400">{{ $school->address ?? '-' }}</p>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $school->email }}</td>
                <td class="px-6 py-4">
                    <div class="text-sm">
                        <span class="font-bold text-gray-800">{{ $school->usedAccountsCount() }}</span>
                        <span class="text-gray-400">/</span>
                        <span class="text-gray-600">{{ $school->totalAccountsQuota() }}</span>
                    </div>
                    <div class="mt-1 w-full bg-gray-100 rounded-full h-1.5">
                        @php
                            $quota = $school->totalAccountsQuota();
                            $used = $school->usedAccountsCount();
                            $pct = $quota > 0 ? min(100, round($used / $quota * 100)) : 0;
                        @endphp
                        <div class="h-1.5 rounded-full {{ $pct > 90 ? 'bg-red-500' : ($pct > 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    @if($school->is_active)
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Nonaktif</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('schools.show', $school) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 transition">Detail</a>
                        <a href="{{ route('schools.edit', $school) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Edit</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                    <p class="text-lg mb-2">🏫</p>
                    Belum ada sekolah terdaftar.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $schools->links() }}</div>

@endsection
