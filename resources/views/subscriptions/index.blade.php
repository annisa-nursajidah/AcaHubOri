@extends('layouts.authenticated')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-black text-gray-900">Kelola Langganan</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $subscriptions->total() }} langganan</p>
    </div>
    <a href="{{ route('subscriptions.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 shadow-md transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Buat Langganan
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                <th class="px-6 py-4">Sekolah</th>
                <th class="px-6 py-4">Paket</th>
                <th class="px-6 py-4">Akun</th>
                <th class="px-6 py-4">Total</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Periode</th>
                <th class="px-6 py-4">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($subscriptions as $sub)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $sub->school->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->plan->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->total_accounts }}</td>
                <td class="px-6 py-4 text-sm font-bold text-gray-800">Rp {{ number_format($sub->total_price, 0, ',', '.') }}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $sub->statusColor() }}-100 text-{{ $sub->statusColor() }}-700">
                        {{ ucfirst($sub->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-gray-500">
                    {{ $sub->starts_at?->format('d/m/Y') ?? '-' }}<br>{{ $sub->expires_at?->format('d/m/Y') ?? '-' }}
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('subscriptions.show', $sub) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 transition">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">Belum ada langganan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $subscriptions->links() }}</div>

@endsection
