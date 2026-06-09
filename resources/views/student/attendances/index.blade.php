@extends('layouts.authenticated')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Riwayat Absensi</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar kehadiran Anda yang tercatat melalui scanner QR pada tahun ajaran ini.</p>
    </div>
    <a href="{{ route('student.attendance.scan') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition-colors gap-2 shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5z"/></svg>
        Buka Kamera Scanner
    </a>
</div>

<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    @if($attendances->count() === 0)
        <div class="p-12 text-center flex flex-col items-center justify-center opacity-70">
            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="text-sm font-bold text-gray-900 mb-1">Riwayat Kosong</h3>
            <p class="text-sm text-gray-500 max-w-sm">Anda belum memiliki catatan presensi yang dipindai melalui sistem biometrik/QR.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50/50 text-gray-500 uppercase font-semibold text-xs border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Tanggal Sesi</th>
                        <th class="px-6 py-4">Mata Pelajaran</th>
                        <th class="px-6 py-4">Waktu Scan</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($attendances as $row)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 border-l-4 border-transparent">
                                {{ $row->session->date->translatedFormat('l, d F Y') }}
                            </td>
                            <td class="px-6 py-4 border-l-4 border-transparent">
                                <div class="font-bold text-gray-900 mb-0.5">{{ $row->session->subject->nama }}</div>
                                <div class="text-xs text-gray-500">Guru: {{ $row->session->teacher->name }}</div>
                            </td>
                            <td class="px-6 py-4 border-l-4 border-transparent text-gray-600 font-medium">
                                {{ $row->scanned_at ? $row->scanned_at->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 border-l-4 border-transparent">
                                @if($row->status === 'present')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-lg bg-green-50 text-green-700 border border-green-200 uppercase tracking-wider">
                                        Hadir
                                    </span>
                                @elseif($row->status === 'late')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-200 uppercase tracking-wider">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-lg bg-red-50 text-red-700 border border-red-200 uppercase tracking-wider">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($attendances->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $attendances->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
