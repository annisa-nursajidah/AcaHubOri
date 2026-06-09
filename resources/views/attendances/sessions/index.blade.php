@extends('layouts.authenticated')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">CBT & Absensi Kelas</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar sesi absensi QR Hari Ini.</p>
    </div>
    <a href="{{ route('attendance-sessions.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition-colors gap-2 shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Buka Kelas (Sesi QR Baru)
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($sessions as $session)
        <div class="bg-white rounded-2xl border {{ $session->status === 'active' ? 'border-brand-200 ring-1 ring-brand-100' : 'border-gray-100' }} p-6 shadow-sm flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-gray-100 text-gray-600">
                    {{ $session->classroom->name }}
                </span>
                
                @if($session->status === 'active')
                    <span class="text-xs font-bold text-green-500 animate-pulse flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        Sesi Dibuka
                    </span>
                @else
                    <span class="text-xs font-bold text-gray-400 flex items-center gap-1">
                        Sesi Ditutup
                    </span>
                @endif
            </div>

            <h3 class="font-bold text-lg text-gray-900 leading-tight mb-2">{{ $session->subject->nama }}</h3>
            
            <div class="mt-auto space-y-2 mb-5">
                <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mulai: {{ $session->start_time->format('H:i') }}
                </div>
                @if($session->end_time)
                <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tutup: {{ $session->end_time->format('H:i') }}
                </div>
                @endif
            </div>

            <div class="pt-4 border-t border-gray-100">
                <a href="{{ route('attendance-sessions.show', $session) }}" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-50 text-brand-600 text-sm font-bold rounded-xl hover:bg-gray-100 transition-colors">
                    Lihat Proyektor QR
                </a>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-gray-50 border border-gray-100 rounded-2xl p-10 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-gray-100">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-gray-900 mb-1">Belum Ada Kelas yang Terbuka</h3>
            <p class="text-sm text-gray-500">Mulai memanggil siswa bằng membuka Sesi Absensi baru untuk pelajaran Anda saat ini.</p>
        </div>
    @endforelse
</div>
@endsection
