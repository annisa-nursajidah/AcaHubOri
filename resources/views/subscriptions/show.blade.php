@extends('layouts.authenticated')
@section('content')

<div class="max-w-3xl">
    <a href="{{ route('subscriptions.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">← Kembali</a>
    <h1 class="text-2xl font-black text-gray-900 mt-1 mb-6">Detail Langganan</h1>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4">Informasi Langganan</h3>
            <div class="space-y-3 text-sm">
                <div><span class="text-gray-500">Order ID:</span> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $subscription->midtrans_order_id }}</code></div>
                <div><span class="text-gray-500">Sekolah:</span> <a href="{{ route('schools.show', $subscription->school) }}" class="font-semibold text-brand-600 hover:underline">{{ $subscription->school->name }}</a></div>
                <div><span class="text-gray-500">Paket:</span> <span class="font-medium">{{ $subscription->plan->name }}</span></div>
                <div><span class="text-gray-500">Jumlah Akun:</span> <span class="font-bold">{{ $subscription->total_accounts }}</span></div>
                <div><span class="text-gray-500">Harga/Akun:</span> <span class="font-medium">Rp {{ number_format($subscription->price_per_account, 0, ',', '.') }}</span></div>
                <div><span class="text-gray-500">Total:</span> <span class="text-lg font-black text-brand-600">Rp {{ number_format($subscription->total_price, 0, ',', '.') }}</span></div>
                <div>
                    <span class="text-gray-500">Status:</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $subscription->statusColor() }}-100 text-{{ $subscription->statusColor() }}-700">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
                <div><span class="text-gray-500">Aktif dari:</span> <span class="font-medium">{{ $subscription->starts_at?->format('d M Y, H:i') ?? '-' }}</span></div>
                <div><span class="text-gray-500">Berakhir:</span> <span class="font-medium">{{ $subscription->expires_at?->format('d M Y, H:i') ?? '-' }}</span></div>
                @if($subscription->notes)
                <div><span class="text-gray-500">Catatan:</span> <span class="text-gray-700">{{ $subscription->notes }}</span></div>
                @endif
            </div>
        </div>

        {{-- Status Actions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4">Ubah Status</h3>
            <div class="space-y-3">
                @php
                $statuses = [
                    'pending'   => ['label' => 'Pending',   'color' => 'yellow', 'desc' => 'Menunggu pembayaran'],
                    'paid'      => ['label' => 'Paid',      'color' => 'blue',   'desc' => 'Pembayaran diterima'],
                    'active'    => ['label' => 'Aktifkan',  'color' => 'green',  'desc' => 'Aktifkan langganan + sekolah'],
                    'expired'   => ['label' => 'Expired',   'color' => 'gray',   'desc' => 'Tandai kedaluwarsa'],
                    'cancelled' => ['label' => 'Cancelled', 'color' => 'red',    'desc' => 'Batalkan langganan'],
                ];
                @endphp
                @foreach($statuses as $status => $info)
                <form method="POST" action="{{ route('subscriptions.update-status', $subscription) }}" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $status }}">
                    <button type="submit"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl border transition
                                   {{ $subscription->status === $status
                                      ? 'border-' . $info['color'] . '-500 bg-' . $info['color'] . '-50 ring-2 ring-' . $info['color'] . '-200'
                                      : 'border-gray-200 hover:border-' . $info['color'] . '-300 hover:bg-gray-50' }}"
                            {{ $subscription->status === $status ? 'disabled' : '' }}>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-gray-800">{{ $info['label'] }}</p>
                            <p class="text-xs text-gray-500">{{ $info['desc'] }}</p>
                        </div>
                        @if($subscription->status === $status)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $info['color'] }}-100 text-{{ $info['color'] }}-700">Saat ini</span>
                        @endif
                    </button>
                </form>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
