@extends('layouts.authenticated')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Portal Wali Murid</h1>
    <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ auth()->user()->name }}. Pantau perkembangan akademik anak Anda di bawah ini.</p>
</div>

@if($parent->children->count() === 0)
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 sm:p-12 text-center max-w-2xl mx-auto mt-8">
    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-sm ring-1 ring-gray-100">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <h2 class="text-xl font-bold text-gray-900 mb-2">Belum Memiliki Akses Penuh</h2>
    <p class="text-gray-500">Akun Anda belum ditautkan ke profil siswa manapun oleh pihak Sekolah. Silakan hubungi Admin Sekolah atau Wali Kelas anak Anda agar mereka dapat menautkan akun ini.</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($parent->children as $child)
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition-shadow">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-brand-50 rounded-full border border-brand-100 flex items-center justify-center text-brand-600 font-bold text-xl uppercase tracking-wider shrink-0">
                    {{ substr($child->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-gray-900 truncate group-hover:text-brand-600 transition-colors">{{ $child->name }}</h3>
                    <p class="text-sm text-gray-500 truncate">{{ $child->school->name ?? 'AcaHub Partner' }}</p>
                </div>
            </div>
            
            @if($child->studentProfile && $child->studentProfile->status === 'active')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg bg-green-50 text-green-700 border border-green-200 uppercase tracking-wider shrink-0">Aktif</span>
            @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg bg-gray-50 text-gray-600 border border-gray-200 uppercase tracking-wider shrink-0">Pasif</span>
            @endif
        </div>
        
        <div class="p-6 bg-gray-50/50 flex flex-col gap-3 flex-1 text-sm text-gray-600">
            <div class="flex justify-between items-center pb-2 border-b border-gray-100 border-dashed">
                <span class="font-medium text-gray-500">NIS / Nomor Induk</span>
                <span class="font-bold text-gray-900">{{ $child->studentProfile->nis ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-center pb-2 border-b border-gray-100 border-dashed">
                <span class="font-medium text-gray-500">Kelas / Rombel</span>
                <span class="font-bold text-gray-900">-</span>{{-- Akan dihubungkan --}}
            </div>
        </div>
        
        <div class="p-5 border-t border-gray-100 flex items-center justify-end">
             <a href="{{ route('parent.dashboard.show', $child->id) }}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 transition-colors gap-2 shadow-sm">
                Lihat Laporan Lengkap
                <svg class="w-4 h-4 mt-0.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
