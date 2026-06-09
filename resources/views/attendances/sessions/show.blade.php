@extends('layouts.authenticated')

@section('content')
<div class="mb-6 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('attendance-sessions.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors tooltip" title="Kembali ke Daftar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">QR Kelas: {{ $attendanceSession->subject->nama }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelas {{ $attendanceSession->classroom->name }} &bull; Sesi berjalan sejak {{ $attendanceSession->start_time->format('H:i') }}</p>
        </div>
    </div>
    
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg {{ $attendanceSession->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            <span class="w-2 h-2 rounded-full {{ $attendanceSession->status === 'active' ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
            {{ strtoupper($attendanceSession->status) }}
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- QR Code Section (Layarnya Besar) -->
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 sm:p-12 text-center relative overflow-hidden h-[600px] flex flex-col justify-center items-center">
            <!-- Background Decoration -->
            <div class="absolute -right-32 -bottom-32 w-96 h-96 rounded-full bg-brand-50 opacity-50"></div>
            <div class="absolute -left-32 -top-32 w-96 h-96 rounded-full bg-blue-50 opacity-50"></div>

            <div class="relative z-10 w-full max-w-sm mx-auto">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Scan untuk Presensi</h2>
                
                @if($attendanceSession->status === 'active')
                    <!-- Kotak QR Asli -->
                    <div class="bg-white p-6 rounded-3xl shadow-lg border-4 border-gray-100 mx-auto aspect-square flex items-center justify-center mb-8 relative">
                        <!-- Menggunakan API Terbuka untuk generate SVG/Image QR berdasarkan Token.
                             Dalam production idealnya pakai spatie/laravel-qrcode offline -->
                        <img id="qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=400x400&data={{ urlencode(route('student.attendance.scan', ['token' => $attendanceSession->qr_code_token])) }}" 
                             alt="QR Code" class="w-full h-full object-contain mix-blend-multiply">
                        <div class="absolute inset-0 border-8 border-brand-500 rounded-3xl opacity-20 pointer-events-none"></div>
                    </div>
                @else
                    <div class="bg-gray-100 p-6 rounded-3xl mx-auto aspect-square flex items-center justify-center mb-8 border-4 border-gray-200">
                        <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <p class="text-red-500 font-bold mb-8">SESI KELAS INI SUDAH DITUTUP</p>
                @endif
                
                @if($attendanceSession->status === 'active')
                <div class="flex items-center justify-center gap-3">
                    <form action="{{ route('attendance-sessions.refresh-qr', $attendanceSession) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors shadow-sm inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                            Refresh QR Code
                        </button>
                    </form>
                    
                    <form action="{{ route('attendance-sessions.close', $attendanceSession) }}" method="POST" onsubmit="return confirm('Tutup absen kelas ini sekarang? Siswa yang terlambat tidak bisa lagi scan.');">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-red-600 border border-red-600 hover:bg-red-700 rounded-xl transition-colors shadow-sm inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            Tutup Sesi Kelas
                        </button>
                    </form>
                </div>
                <!-- Mini instruksi -->
                <p class="text-xs text-gray-400 mt-4">* Refresh QR sesekali untuk merusak tangkappan layar siswa yang berniat titip absen.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Kolom Daftar Siswa -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col h-[600px] overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    Siswa Hadir: <span class="text-brand-600 ms-1">{{ $attendanceSession->attendances->count() }}</span>
                </h3>
            </div>
            
            <div class="p-5 overflow-y-auto flex-1 bg-gray-50/20">
                @if($attendanceSession->attendances->count() === 0)
                    <div class="h-full flex flex-col items-center justify-center text-center opacity-70">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.38.432.415.63.985.592 1.55l-.265 3.999c-.046.689.569 1.156 1.144.912l3.415-1.442a1.8 1.8 0 011.666.07A8.955 8.955 0 0012 20.25z"/></svg>
                        <p class="text-sm font-bold text-gray-500">Belum Ada Presensi</p>
                        <p class="text-xs text-gray-400 mt-1 max-w-[200px]">Daftar siswa yang telah menscan QR Code akan muncul di sini secara otomatis.</p>
                    </div>
                @else
                    <ul class="space-y-3">
                        @foreach($attendanceSession->attendances->sortByDesc('scanned_at') as $attendance)
                        <li class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-3 animate-[fade-in_0.3s_ease-out]">
                            <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-600 font-bold flex items-center justify-center shrink-0 uppercase text-sm border border-brand-100">
                                {{ substr($attendance->student->name, 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $attendance->student->name }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $attendance->status === 'present' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }} uppercase tracking-wider">
                                        {{ $attendance->status === 'present' ? 'Hadir' : 'Terlambat' }}
                                    </span>
                                    <span class="text-xs text-gray-400">&bull; {{ $attendance->scanned_at ? $attendance->scanned_at->format('H:i:s') : '-' }}</span>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            
            <div class="p-4 border-t border-gray-100 bg-white">
                <button onclick="window.location.reload();" class="w-full py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                    Segarkan Data Siswa Live
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
