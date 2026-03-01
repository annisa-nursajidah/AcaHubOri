@extends('layouts.app')
@section('body')

<nav class="fixed top-0 w-full bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="/" class="text-2xl font-extrabold text-brand-500 tracking-tight">AcaHub</a>
        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-brand-600 transition">Sign In</a>
            <a href="{{ route('pricing') }}" class="text-sm font-medium text-gray-700 hover:text-brand-600 transition">Lihat Paket</a>
        </div>
    </div>
</nav>

<div class="min-h-screen pt-24 pb-16 bg-gradient-to-br from-brand-50 via-white to-accent-50">
    <div class="max-w-4xl mx-auto px-6">
        {{-- Header --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-black text-gray-900">Daftarkan <span class="text-brand-500">Sekolah</span> Anda</h1>
            <p class="mt-3 text-gray-500">Isi data sekolah dan akun admin, lalu pilih paket langganan.</p>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('schools.register.submit') }}" class="space-y-8">
            @csrf

            {{-- STEP 1: School Info --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-brand-100 flex items-center justify-center text-brand-700 font-bold">1</div>
                    <h2 class="text-lg font-bold text-gray-800">Informasi Sekolah</h2>
                </div>
                <div class="grid md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Sekolah *</label>
                        <input type="text" name="school_name" value="{{ old('school_name') }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm transition"
                               placeholder="SMA Negeri 1 ...">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                        <input type="text" name="school_address" value="{{ old('school_address') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm transition"
                               placeholder="Jl. Pendidikan No. 1 ...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Telepon</label>
                        <input type="text" name="school_phone" value="{{ old('school_phone') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm transition"
                               placeholder="0812-xxxx-xxxx">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Sekolah *</label>
                        <input type="email" name="school_email" value="{{ old('school_email') }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm transition"
                               placeholder="info@sekolah.sch.id">
                    </div>
                </div>
            </div>

            {{-- STEP 2: Admin Account --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-accent-100 flex items-center justify-center text-accent-700 font-bold">2</div>
                    <h2 class="text-lg font-bold text-gray-800">Akun Admin Sekolah</h2>
                </div>
                <p class="text-sm text-gray-500 mb-5">Akun ini akan menjadi admin yang mengelola sekolah Anda di AcaHub.</p>
                <div class="grid md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Admin *</label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm transition"
                               placeholder="Nama lengkap">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Admin *</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm transition"
                               placeholder="admin@sekolah.sch.id">
                    </div>
                    <div></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password *</label>
                        <input type="password" name="admin_password" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm transition"
                               placeholder="Min. 8 karakter">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password *</label>
                        <input type="password" name="admin_password_confirmation" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm transition"
                               placeholder="Ulangi password">
                    </div>
                </div>
            </div>

            {{-- STEP 3: Choose Plan --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-700 font-bold">3</div>
                    <h2 class="text-lg font-bold text-gray-800">Pilih Paket & Jumlah Akun</h2>
                </div>

                <div class="grid md:grid-cols-{{ min(count($plans), 3) }} gap-4 mb-6">
                    @foreach($plans as $plan)
                    <label class="cursor-pointer">
                        <input type="radio" name="plan_id" value="{{ $plan->id }}" class="hidden peer"
                               {{ (old('plan_id', $selectedPlan?->id) == $plan->id) ? 'checked' : '' }}
                               data-price="{{ $plan->price_per_account }}"
                               data-min="{{ $plan->min_accounts }}"
                               data-max="{{ $plan->max_accounts ?? 500 }}"
                               onchange="updatePlanSelection(this)">
                        <div class="p-5 rounded-2xl border-2 border-gray-200 peer-checked:border-brand-500 peer-checked:bg-brand-50 transition-all">
                            <h3 class="font-bold text-gray-800">{{ $plan->name }}</h3>
                            <p class="text-2xl font-black text-brand-600 mt-2">Rp {{ number_format($plan->price_per_account, 0, ',', '.') }}<span class="text-sm font-normal text-gray-400">/akun</span></p>
                            <p class="text-xs text-gray-400 mt-1">Min. {{ $plan->min_accounts }} akun</p>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- Account count --}}
                <div class="flex items-center gap-4 mb-4">
                    <label class="text-sm font-semibold text-gray-700 whitespace-nowrap">Jumlah Akun:</label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="adjustRegAccounts(-10)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold transition text-lg">−</button>
                        <input type="number" name="total_accounts" id="reg-accounts"
                               value="{{ old('total_accounts', $selectedPlan?->min_accounts ?? $plans->first()->min_accounts ?? 10) }}"
                               class="w-24 text-center border border-gray-200 rounded-xl py-2.5 text-lg font-bold text-gray-800 focus:ring-2 focus:ring-brand-300"
                               oninput="calculateRegPrice()">
                        <button type="button" onclick="adjustRegAccounts(10)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold transition text-lg">+</button>
                    </div>
                </div>

                {{-- Price preview --}}
                <div class="bg-gradient-to-r from-brand-50 to-accent-50 rounded-2xl p-6 border border-brand-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Harga per akun</p>
                            <p class="text-lg font-bold text-gray-800" id="reg-price-per">Rp 0</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Total Pembayaran</p>
                            <p class="text-3xl font-black text-brand-600" id="reg-total">Rp 0</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full px-8 py-4 rounded-2xl bg-accent-500 text-white font-bold text-lg hover:bg-accent-600 shadow-xl shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5">
                Daftar & Lanjut ke Pembayaran
                <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
let regPricePerAccount = 0;
let regMinAccounts = 10;
let regMaxAccounts = 500;

// Initialize from pre-selected plan
document.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name="plan_id"]:checked');
    if (checked) updatePlanSelection(checked);
});

function updatePlanSelection(radio) {
    regPricePerAccount = parseFloat(radio.dataset.price);
    regMinAccounts = parseInt(radio.dataset.min);
    regMaxAccounts = parseInt(radio.dataset.max);

    const accountsInput = document.getElementById('reg-accounts');
    if (parseInt(accountsInput.value) < regMinAccounts) accountsInput.value = regMinAccounts;

    calculateRegPrice();
}

function adjustRegAccounts(delta) {
    const input = document.getElementById('reg-accounts');
    let val = parseInt(input.value) + delta;
    val = Math.max(regMinAccounts, Math.min(regMaxAccounts, val));
    input.value = val;
    calculateRegPrice();
}

function calculateRegPrice() {
    const accounts = parseInt(document.getElementById('reg-accounts').value) || regMinAccounts;
    const total = accounts * regPricePerAccount;

    document.getElementById('reg-price-per').textContent = 'Rp ' + regPricePerAccount.toLocaleString('id-ID');
    document.getElementById('reg-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endpush

@endsection
