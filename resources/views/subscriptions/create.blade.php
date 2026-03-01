@extends('layouts.authenticated')
@section('content')

<div class="max-w-2xl">
    <a href="{{ route('subscriptions.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">← Kembali</a>
    <h1 class="text-2xl font-black text-gray-900 mt-1 mb-6">Buat Langganan</h1>

    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('subscriptions.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Sekolah *</label>
            <select name="school_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 text-sm">
                <option value="">-- Pilih Sekolah --</option>
                @foreach($schools as $school)
                <option value="{{ $school->id }}" {{ (old('school_id', $selectedSchool?->id) == $school->id) ? 'selected' : '' }}>{{ $school->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Paket *</label>
            <select name="plan_id" id="admin-plan" required onchange="adminUpdatePlan(this)"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 text-sm">
                <option value="">-- Pilih Paket --</option>
                @foreach($plans as $plan)
                <option value="{{ $plan->id }}" data-price="{{ $plan->price_per_account }}" data-min="{{ $plan->min_accounts }}" data-max="{{ $plan->max_accounts ?? '' }}"
                        {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                    {{ $plan->name }} — Rp {{ number_format($plan->price_per_account, 0, ',', '.') }}/akun
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Akun *</label>
            <input type="number" name="total_accounts" id="admin-accounts" value="{{ old('total_accounts', 10) }}" required oninput="adminCalc()"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 text-sm">
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-right">
            <span class="text-sm text-gray-500">Estimasi total:</span>
            <span class="text-lg font-black text-brand-600 ml-2" id="admin-total">Rp 0</span>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
            <textarea name="notes" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 text-sm">{{ old('notes') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-3 rounded-xl bg-brand-500 text-white font-semibold text-sm hover:bg-brand-600 shadow-md transition">Simpan</button>
            <a href="{{ route('subscriptions.index') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm hover:bg-gray-200 transition">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let adminPrice = 0;
function adminUpdatePlan(sel) {
    const opt = sel.options[sel.selectedIndex];
    adminPrice = parseFloat(opt.dataset.price || 0);
    const min = parseInt(opt.dataset.min || 1);
    const inp = document.getElementById('admin-accounts');
    if (parseInt(inp.value) < min) inp.value = min;
    adminCalc();
}
function adminCalc() {
    const total = parseInt(document.getElementById('admin-accounts').value || 0) * adminPrice;
    document.getElementById('admin-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('admin-plan');
    if (sel.value) adminUpdatePlan(sel);
});
</script>
@endpush

@endsection
