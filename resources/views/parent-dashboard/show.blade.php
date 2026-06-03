@extends('layouts.authenticated')

@section('content')
<div class="mb-6 flex items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <a href="{{ route('parent.dashboard') }}" class="p-2 bg-white border border-gray-200 text-gray-500 rounded-xl hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Profil & Rapor Siswa</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan akademik ananda <span class="font-bold text-gray-800">{{ $child->name }}</span></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Kolom Kiri: Profil Singkat -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 text-center">
            <div class="w-24 h-24 bg-brand-50 rounded-full border-4 border-white shadow-sm ring-1 ring-brand-100 flex items-center justify-center text-brand-600 font-bold text-3xl mx-auto mb-4 tracking-wider">
                {{ substr($child->name, 0, 1) }}
            </div>
            <h2 class="text-lg font-bold text-gray-900">{{ $child->name }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ $child->school->name ?? 'Sekolah Mitra AcaHub' }}</p>

            <div class="bg-gray-50 rounded-2xl p-4 text-left space-y-3 border border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Nomor Induk / NISN</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $child->studentProfile->nis ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Angkatan</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $child->studentProfile->batch_year ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Status Akademik</p>
                    <p class="text-sm font-semibold text-brand-600">{{ ucfirst($child->studentProfile->status ?? 'Active') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-brand-600 to-indigo-700 rounded-3xl border border-brand-800 shadow-lg p-6 text-white text-center">
            <h3 class="font-bold text-lg mb-2">Tagihan SPP Koperasi</h3>
            <p class="text-brand-100 text-sm mb-4">Fitur ini belum aktif. Modul pembayaran tagihan sedang dalam tahap pengembangan sistem.</p>
            <div class="inline-flex items-center justify-center px-4 py-2 bg-white/20 text-white text-xs font-bold rounded-full w-full opacity-60 cursor-not-allowed">
                Cek Pembayaran
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Rapor & Presensi -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Tab Navigasi Dummy -->
        <div class="flex gap-2 border-b border-gray-200">
            <button class="px-4 py-2.5 text-sm font-bold border-b-2 border-brand-500 text-brand-700">Rapor & Nilai</button>
            <button class="px-4 py-2.5 text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700">Kehadiran (Absensi)</button>
        </div>

        <!-- Kartu Rapor -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col items-center justify-center p-12 text-center opacity-80 min-h-[300px]">
            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            <h3 class="text-base font-bold text-gray-900 mb-2">Rapor Nilai Siswa</h3>
            <p class="text-sm text-gray-500 max-w-sm">Guru belum menerbitkan Nilai Semester untuk siswa ini. Seluruh rekapitulasi ujian CBT dan nilai harian akan dirangkum di sini saat Selesai.</p>
        </div>

    </div>
</div>
@endsection
