@extends('layouts.app')
@section('body')

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-brand-50 via-white to-accent-50 p-6">
    <div class="max-w-lg w-full">
        <div class="text-center mb-8">
            <a href="/" class="text-3xl font-extrabold text-brand-500 tracking-tight">AcaHub</a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-brand-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>
                </div>
                <h1 class="text-2xl font-black text-gray-900">Pembayaran</h1>
                <p class="text-sm text-gray-500 mt-1">Selesaikan pembayaran untuk mengaktifkan akun sekolah</p>
            </div>

            {{-- Order summary --}}
            <div class="bg-gray-50 rounded-2xl p-5 mb-6 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Sekolah</span>
                    <span class="font-semibold text-gray-800">{{ $school->name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Paket</span>
                    <span class="font-semibold text-gray-800">{{ $subscription->plan->name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Jumlah Akun</span>
                    <span class="font-semibold text-gray-800">{{ $subscription->total_accounts }} akun</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Harga per Akun</span>
                    <span class="font-semibold text-gray-800">Rp {{ number_format($subscription->price_per_account, 0, ',', '.') }}</span>
                </div>
                <hr class="border-gray-200">
                <div class="flex justify-between">
                    <span class="font-bold text-gray-800">Total</span>
                    <span class="text-xl font-black text-brand-600">Rp {{ number_format($subscription->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($snapToken)
                <button id="pay-button"
                        class="w-full px-6 py-4 rounded-2xl bg-accent-500 text-white font-bold text-lg hover:bg-accent-600 shadow-xl shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5">
                    Bayar Sekarang
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                </button>
            @else
                <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm">
                    <p class="font-semibold mb-1">⚠️ Payment gateway belum dikonfigurasi</p>
                    <p>Silakan hubungi admin untuk menyelesaikan pembayaran secara manual, atau konfigurasikan Midtrans di file .env</p>
                    <p class="mt-2 text-xs text-yellow-600">Order ID: <code class="bg-yellow-100 px-1 rounded">{{ $subscription->midtrans_order_id }}</code></p>
                </div>
                <a href="{{ route('login') }}" class="mt-4 block w-full text-center px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition">
                    Kembali ke Login
                </a>
            @endif

            <p class="text-xs text-gray-400 text-center mt-4">
                Pembayaran diproses secara aman oleh Midtrans
            </p>
        </div>
    </div>
</div>

@if($snapToken)
@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
document.getElementById('pay-button').addEventListener('click', function() {
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            window.location.href = '{{ route("schools.payment.success") }}?order_id={{ $subscription->midtrans_order_id }}';
        },
        onPending: function(result) {
            window.location.href = '{{ route("schools.payment.success") }}?order_id={{ $subscription->midtrans_order_id }}';
        },
        onError: function(result) {
            alert('Pembayaran gagal. Silakan coba lagi.');
        },
        onClose: function() {
        }
    });
});
</script>
@endpush
@endif

@endsection
