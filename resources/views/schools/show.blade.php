@extends('layouts.authenticated')
@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('schools.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">← Kembali ke Daftar</a>
        <h1 class="text-2xl font-black text-gray-900 mt-1">{{ $school->name }}</h1>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('subscriptions.create', ['school_id' => $school->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 shadow-md transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Langganan
        </a>
        <a href="{{ route('schools.edit', $school) }}" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition">Edit</a>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Info Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-800 mb-4">Informasi Sekolah</h3>
        <div class="space-y-3 text-sm">
            <div><span class="text-gray-500">Email:</span> <span class="font-medium text-gray-800">{{ $school->email }}</span></div>
            <div><span class="text-gray-500">Telepon:</span> <span class="font-medium text-gray-800">{{ $school->phone ?? '-' }}</span></div>
            <div><span class="text-gray-500">Alamat:</span> <span class="font-medium text-gray-800">{{ $school->address ?? '-' }}</span></div>
            <div>
                <span class="text-gray-500">Status:</span>
                @if($school->is_active)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Nonaktif</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Quota Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-800 mb-4">Kuota Akun</h3>
        @php
            $quota = $school->totalAccountsQuota();
            $used = $school->usedAccountsCount();
            $remaining = $school->remainingAccountsQuota();
            $pct = $quota > 0 ? min(100, round($used / $quota * 100)) : 0;
        @endphp
        <div class="text-center mb-4">
            <p class="text-4xl font-black text-brand-600">{{ $used }}<span class="text-lg text-gray-400">/{{ $quota }}</span></p>
            <p class="text-sm text-gray-500">akun terpakai</p>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
            <div class="h-2 rounded-full {{ $pct > 90 ? 'bg-red-500' : ($pct > 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                 style="width: {{ $pct }}%"></div>
        </div>
        <p class="text-xs text-gray-400 text-center">{{ $remaining }} akun tersisa</p>
    </div>

    {{-- Active Subscription --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-800 mb-4">Langganan Aktif</h3>
        @if($activeSubscription)
        <div class="space-y-2 text-sm">
            <div><span class="text-gray-500">Paket:</span> <span class="font-bold text-brand-600">{{ $activeSubscription->plan->name }}</span></div>
            <div><span class="text-gray-500">Akun:</span> <span class="font-medium">{{ $activeSubscription->total_accounts }}</span></div>
            <div><span class="text-gray-500">Aktif hingga:</span> <span class="font-medium">{{ $activeSubscription->expires_at?->format('d M Y') ?? '-' }}</span></div>
        </div>
        @else
        <p class="text-sm text-gray-400">Tidak ada langganan aktif.</p>
        @endif
    </div>
</div>

{{-- Users List --}}
<div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-bold text-gray-800">Pengguna Sekolah ({{ $school->users->count() }})</h3>
    </div>
    <table class="w-full">
        <thead>
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                <th class="px-6 py-3">Nama</th>
                <th class="px-6 py-3">Email</th>
                <th class="px-6 py-3">Role</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($school->users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $user->name }}</td>
                <td class="px-6 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                <td class="px-6 py-3">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                        {{ $user->role === 'school_admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'teacher' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Belum ada pengguna.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Subscription History --}}
<div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Riwayat Langganan</h3>
    </div>
    <table class="w-full">
        <thead>
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                <th class="px-6 py-3">Paket</th>
                <th class="px-6 py-3">Akun</th>
                <th class="px-6 py-3">Total</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Periode</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($school->subscriptions as $sub)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $sub->plan->name }}</td>
                <td class="px-6 py-3 text-sm text-gray-600">{{ $sub->total_accounts }}</td>
                <td class="px-6 py-3 text-sm font-bold text-gray-800">Rp {{ number_format($sub->total_price, 0, ',', '.') }}</td>
                <td class="px-6 py-3">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $sub->statusColor() }}-100 text-{{ $sub->statusColor() }}-700">
                        {{ ucfirst($sub->status) }}
                    </span>
                </td>
                <td class="px-6 py-3 text-xs text-gray-500">
                    {{ $sub->starts_at?->format('d/m/Y') ?? '-' }} — {{ $sub->expires_at?->format('d/m/Y') ?? '-' }}
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada langganan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
