@extends('layouts.app')
@section('body')

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 via-white to-brand-50 p-6">
    <div class="max-w-lg w-full text-center">
        <div class="mb-8">
            <a href="/" class="text-3xl font-extrabold text-brand-500 tracking-tight">AcaHub</a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            {{-- Success icon --}}
            <div class="w-20 h-20 mx-auto rounded-full bg-green-100 flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            </div>

            <h1 class="text-2xl font-black text-gray-900 mb-2">Pendaftaran Berhasil!</h1>

            @if($subscription)
            <p class="text-gray-500 mb-6">
                Sekolah <strong>{{ $subscription->school->name }}</strong> telah terdaftar.
                @if($subscription->status === 'active')
                    Langganan Anda sudah aktif!
                @else
                    Pembayaran sedang diproses. Akun akan aktif setelah pembayaran dikonfirmasi.
                @endif
            </p>

            <div class="bg-gray-50 rounded-2xl p-5 text-left space-y-2 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Order ID</span>
                    <code class="text-xs bg-gray-200 px-2 py-0.5 rounded">{{ $subscription->midtrans_order_id }}</code>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Status</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                                 bg-{{ $subscription->statusColor() }}-100 text-{{ $subscription->statusColor() }}-700">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Jumlah Akun</span>
                    <span class="font-semibold text-gray-800">{{ $subscription->total_accounts }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total</span>
                    <span class="font-bold text-brand-600">Rp {{ number_format($subscription->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
            @else
            <p class="text-gray-500 mb-6">Terima kasih telah mendaftar.</p>
            @endif

            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-brand-500 text-white font-bold hover:bg-brand-600 shadow-lg transition-all hover:-translate-y-0.5">
                Login ke Dashboard
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </div>
</div>

@endsection
