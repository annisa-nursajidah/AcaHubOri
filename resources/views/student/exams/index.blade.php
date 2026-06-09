@extends('layouts.authenticated')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Computer Based Test (CBT)</h1>
    <p class="text-sm text-gray-500 mt-1">Daftar Penilaian Harian dan Ujian yang tersedia untuk Kelas Anda.</p>
</div>

@if(session('info'))
    <div class="mb-6 p-4 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 flex items-start gap-3 text-sm">
        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
        <p>{{ session('info') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($exams as $exam)
        @php
            $attempt = $attempts->get($exam->id);
            $isFinished = $attempt && in_array($attempt->status, ['submitted', 'time_up']);
            $isInProgress = $attempt && $attempt->status === 'in_progress';
        @endphp

        <div class="bg-white border rounded-2xl p-6 shadow-sm flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow
            {{ $isFinished ? 'border-gray-200' : ($isInProgress ? 'border-brand-200 ring-1 ring-brand-100' : 'border-gray-100' ) }}">
            
            <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full opacity-10 transition-transform duration-500 group-hover:scale-150
                {{ $isFinished ? 'bg-gray-400' : ($isInProgress ? 'bg-brand-500' : 'bg-blue-500') }}"></div>

            <div class="relative z-10 flex-1">
                <div class="flex items-start justify-between mb-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                        {{ $isFinished ? 'bg-gray-100 text-gray-600' : ($isInProgress ? 'bg-brand-50 text-brand-600' : 'bg-blue-50 text-blue-600') }}">
                        {{ $exam->subject->nama }}
                    </span>
                    
                    @if($isFinished)
                        <span class="text-xs font-bold text-gray-400 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Selesai
                        </span>
                    @elseif($isInProgress)
                        <span class="text-xs font-bold text-brand-500 animate-pulse flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Sedang Dikerjakan
                        </span>
                    @endif
                </div>

                <h3 class="font-bold text-lg text-gray-900 leading-tight mb-2">{{ $exam->title }}</h3>
                <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $exam->description ?? 'Tidak ada deskripsi ujian.' }}</p>

                <div class="mt-auto space-y-2 mb-5">
                    <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Durasi: {{ $exam->duration_minutes }} Menit
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        Guru: {{ $exam->teacher->name }}
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 relative z-10">
                @if($isFinished)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Nilai Akhir:</span>
                        <span class="text-lg font-black {{ $attempt->score >= 70 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $attempt->score ?? 0 }}
                        </span>
                    </div>
                @elseif($isInProgress)
                    <a href="{{ route('student.exams.take', [$exam, $attempt]) }}" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-brand-50 text-brand-600 text-sm font-bold rounded-xl hover:bg-brand-100 transition-colors">
                        Lanjutkan Ujian
                    </a>
                @else
                    <form action="{{ route('student.exams.start', $exam) }}" method="POST" onsubmit="return confirm('Waktu akan langsung berjalan setelah Anda memulai. Pastikan koneksi internet stabil. Yakin mulai sekarang?');">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-black transition-colors shadow-sm gap-2">
                            Mulai Kerjakan
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full bg-gray-50 border border-gray-100 rounded-2xl p-10 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-gray-100">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-gray-900 mb-1">Belum Ada Ujian</h3>
            <p class="text-sm text-gray-500">Saat ini tidak ada ujian CBT yang dijadwalkan untuk kelas Anda.</p>
        </div>
    @endforelse
</div>

@if($exams->hasPages())
    <div class="mt-8">
        {{ $exams->links() }}
    </div>
@endif
@endsection
