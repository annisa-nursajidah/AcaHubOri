@extends('layouts.app')
@section('body')

{{-- ───────────── NAVBAR ───────────── --}}
<nav class="fixed top-0 w-full bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="/" class="text-2xl font-extrabold text-brand-500 tracking-tight">AcaHub</a>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="/" class="hover:text-brand-600 transition">Home</a>
            <a href="#plans" class="hover:text-brand-600 transition">Paket</a>
            <a href="#calculator" class="hover:text-brand-600 transition">Kalkulator</a>
            <a href="#faq" class="hover:text-brand-600 transition">FAQ</a>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-brand-600 transition">Sign In</a>
            <a href="{{ route('schools.register') }}" class="px-5 py-2.5 rounded-full bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40">Daftar Sekolah</a>
        </div>
    </div>
</nav>

{{-- ───────────── HERO ───────────── --}}
<section class="min-h-[70vh] flex items-center pt-24 pb-16 bg-gradient-to-br from-brand-50 via-white to-accent-50">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-100 text-brand-700 text-sm font-semibold mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            Solusi Akademik untuk Sekolah
        </div>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight tracking-tight max-w-4xl mx-auto">
            Pilih Paket yang <span class="text-brand-500">Tepat</span> untuk<br>
            <span class="text-accent-500">Sekolah Anda</span>
        </h1>
        <p class="mt-6 text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
            Bayar hanya sesuai jumlah akun yang dibutuhkan. Harga transparan, tanpa biaya tersembunyi.
        </p>
    </div>
</section>

{{-- ───────────── PRICING CARDS ───────────── --}}
<section id="plans" class="py-20 -mt-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-{{ min(count($plans), 3) }} gap-8 max-w-5xl mx-auto">
            @foreach($plans as $index => $plan)
            @php
                $isPopular = $index === 1 && count($plans) >= 2;
                $gradients = [
                    'from-gray-50 to-white border-gray-200',
                    'from-brand-500 to-brand-700 border-brand-400',
                    'from-gray-50 to-white border-gray-200',
                ];
                $gradient = $gradients[$index % 3] ?? $gradients[0];
            @endphp
            <div class="relative group">
                @if($isPopular)
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-10">
                    <span class="px-4 py-1.5 rounded-full bg-accent-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg">Paling Populer</span>
                </div>
                @endif
                <div class="h-full rounded-3xl border-2 bg-gradient-to-b {{ $gradient }} p-8 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 {{ $isPopular ? 'shadow-xl scale-105 ring-2 ring-brand-300/50' : 'shadow-lg' }}">
                    <h3 class="text-xl font-bold {{ $isPopular ? 'text-white' : 'text-gray-800' }}">{{ $plan->name }}</h3>
                    <p class="mt-2 text-sm {{ $isPopular ? 'text-brand-100' : 'text-gray-500' }}">{{ $plan->description ?? 'Paket ' . $plan->name }}</p>

                    <div class="mt-6 flex items-baseline gap-1">
                        <span class="text-4xl font-black {{ $isPopular ? 'text-white' : 'text-gray-900' }}">Rp {{ number_format($plan->price_per_account, 0, ',', '.') }}</span>
                        <span class="text-sm {{ $isPopular ? 'text-brand-200' : 'text-gray-500' }}">/akun</span>
                    </div>
                    <p class="mt-1 text-sm {{ $isPopular ? 'text-brand-200' : 'text-gray-400' }}">
                        Min. {{ $plan->min_accounts }} akun
                        @if($plan->max_accounts) · Maks. {{ $plan->max_accounts }} akun @endif
                    </p>

                    @if($plan->features && is_array($plan->features))
                    <ul class="mt-6 space-y-3">
                        @foreach($plan->features as $feature)
                        <li class="flex items-start gap-2 text-sm {{ $isPopular ? 'text-brand-100' : 'text-gray-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ $isPopular ? 'text-accent-300' : 'text-green-500' }}" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <a href="{{ route('schools.register', ['plan' => $plan->id]) }}"
                       class="mt-8 block w-full text-center px-6 py-3.5 rounded-2xl font-semibold text-sm transition-all duration-200
                              {{ $isPopular
                                 ? 'bg-white text-brand-700 hover:bg-brand-50 shadow-lg'
                                 : 'bg-brand-500 text-white hover:bg-brand-600 shadow-md shadow-brand-500/25' }}">
                        Pilih Paket Ini
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ───────────── INTERACTIVE CALCULATOR ───────────── --}}
<section id="calculator" class="py-20 bg-gray-50">
    <div class="max-w-3xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-gray-900">Kalkulator Harga</h2>
            <p class="mt-3 text-gray-500">Hitung estimasi biaya sesuai kebutuhan sekolah Anda</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            {{-- Plan selector --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Paket</label>
                <div class="grid grid-cols-{{ min(count($plans), 3) }} gap-3">
                    @foreach($plans as $index => $plan)
                    <button onclick="selectPlan({{ $plan->id }}, {{ $plan->price_per_account }}, {{ $plan->min_accounts }}, {{ $plan->max_accounts ?? 'null' }})"
                            id="plan-btn-{{ $plan->id }}"
                            class="plan-btn px-4 py-3 rounded-xl border-2 text-sm font-semibold transition-all
                                   {{ $index === 0 ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-gray-200 text-gray-600 hover:border-brand-300' }}">
                        {{ $plan->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Account slider --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-700">Jumlah Akun</label>
                    <div class="flex items-center gap-2">
                        <button onclick="adjustAccounts(-10)" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold transition">−</button>
                        <input type="number" id="calc-accounts" value="{{ $plans->first()->min_accounts ?? 10 }}"
                               class="w-24 text-center border border-gray-200 rounded-xl py-2 text-lg font-bold text-gray-800 focus:ring-2 focus:ring-brand-300 focus:border-brand-400"
                               oninput="calculatePrice()">
                        <button onclick="adjustAccounts(10)" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold transition">+</button>
                    </div>
                </div>
                <input type="range" id="calc-slider" min="{{ $plans->first()->min_accounts ?? 10 }}" max="500" value="{{ $plans->first()->min_accounts ?? 10 }}"
                       class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-brand-500"
                       oninput="document.getElementById('calc-accounts').value=this.value; calculatePrice()">
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                    <span id="calc-min-label">{{ $plans->first()->min_accounts ?? 10 }}</span>
                    <span id="calc-max-label">500</span>
                </div>
            </div>

            {{-- Result --}}
            <div class="bg-gradient-to-r from-brand-50 to-accent-50 rounded-2xl p-6 border border-brand-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Harga per akun</p>
                        <p class="text-lg font-bold text-gray-800" id="calc-price-per">Rp {{ number_format($plans->first()->price_per_account ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Estimasi Total</p>
                        <p class="text-3xl font-black text-brand-600" id="calc-total">
                            Rp {{ number_format(($plans->first()->price_per_account ?? 0) * ($plans->first()->min_accounts ?? 10), 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">per {{ ($plans->first()->duration_days ?? 365) }} hari</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('schools.register') }}" id="calc-cta"
               class="mt-6 block w-full text-center px-6 py-4 rounded-2xl bg-accent-500 text-white font-bold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5">
                Daftar Sekarang — <span id="calc-cta-accounts">{{ $plans->first()->min_accounts ?? 10 }}</span> Akun
            </a>
        </div>
    </div>
</section>

{{-- ───────────── FAQ ───────────── --}}
<section id="faq" class="py-20">
    <div class="max-w-3xl mx-auto px-6">
        <h2 class="text-3xl font-black text-gray-900 text-center mb-12">Pertanyaan Umum</h2>
        <div class="space-y-4">
            @php
            $faqs = [
                ['Apa itu "akun"?', 'Satu akun = satu user (guru atau siswa) yang bisa mengakses platform AcaHub. Akun school admin tidak dihitung dari kuota.'],
                ['Bagaimana cara membayar?', 'Setelah mendaftar, Anda akan diarahkan ke halaman pembayaran. Kami menerima transfer bank, e-wallet (GoPay, OVO, etc), dan QRIS.'],
                ['Bisakah menambah akun di tengah jalan?', 'Ya! Anda bisa membeli kuota tambahan kapan saja dari dashboard admin sekolah.'],
                ['Apa yang terjadi jika langganan habis?', 'Akun yang sudah ada tetap bisa diakses, namun Anda tidak bisa membuat akun baru hingga memperpanjang langganan.'],
                ['Apakah data aman?', 'Ya, setiap sekolah memiliki data yang terisolasi. Data sekolah A tidak bisa diakses oleh sekolah B.'],
            ];
            @endphp
            @foreach($faqs as [$q, $a])
            <details class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <summary class="flex items-center justify-between px-6 py-5 cursor-pointer">
                    <span class="text-sm font-semibold text-gray-800">{{ $q }}</span>
                    <svg class="w-5 h-5 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </summary>
                <div class="px-6 pb-5 text-sm text-gray-500 leading-relaxed">{{ $a }}</div>
            </details>
            @endforeach
        </div>
    </div>
</section>

{{-- ───────────── FOOTER ───────────── --}}
<footer class="py-8 text-center text-sm text-gray-400 border-t border-gray-100">
    &copy; {{ date('Y') }} AcaHub. Supporting SDG 4 — Quality Education.
</footer>

@push('scripts')
<script>
let currentPricePerAccount = {{ $plans->first()->price_per_account ?? 0 }};
let currentMinAccounts = {{ $plans->first()->min_accounts ?? 10 }};
let currentMaxAccounts = {{ $plans->first()->max_accounts ?? 500 }};

function selectPlan(planId, price, min, max) {
    currentPricePerAccount = price;
    currentMinAccounts = min;
    currentMaxAccounts = max || 500;

    // Update button styles
    document.querySelectorAll('.plan-btn').forEach(btn => {
        btn.classList.remove('border-brand-500', 'bg-brand-50', 'text-brand-700');
        btn.classList.add('border-gray-200', 'text-gray-600');
    });
    const activeBtn = document.getElementById('plan-btn-' + planId);
    activeBtn.classList.remove('border-gray-200', 'text-gray-600');
    activeBtn.classList.add('border-brand-500', 'bg-brand-50', 'text-brand-700');

    // Update slider range
    const slider = document.getElementById('calc-slider');
    slider.min = min;
    slider.max = currentMaxAccounts;
    document.getElementById('calc-min-label').textContent = min;
    document.getElementById('calc-max-label').textContent = currentMaxAccounts;

    // Update accounts input
    const accountsInput = document.getElementById('calc-accounts');
    if (parseInt(accountsInput.value) < min) accountsInput.value = min;
    slider.value = accountsInput.value;

    calculatePrice();
}

function adjustAccounts(delta) {
    const input = document.getElementById('calc-accounts');
    let val = parseInt(input.value) + delta;
    val = Math.max(currentMinAccounts, Math.min(currentMaxAccounts, val));
    input.value = val;
    document.getElementById('calc-slider').value = val;
    calculatePrice();
}

function calculatePrice() {
    const accounts = parseInt(document.getElementById('calc-accounts').value) || currentMinAccounts;
    const total = accounts * currentPricePerAccount;

    document.getElementById('calc-price-per').textContent = 'Rp ' + currentPricePerAccount.toLocaleString('id-ID');
    document.getElementById('calc-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('calc-cta-accounts').textContent = accounts;
    document.getElementById('calc-slider').value = accounts;
}
</script>
@endpush

@endsection
