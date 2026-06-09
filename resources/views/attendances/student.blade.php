@extends('layouts.authenticated')
@section('content')
@php $title = 'Riwayat Kehadiran'; @endphp

<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Riwayat Kehadiran</h1>
            <p class="text-sm text-gray-500 mt-0.5">Data kehadiran Anda di semua mata pelajaran</p>
        </div>
        <a href="{{ route('student.attendance.scan') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 shadow-lg shadow-brand-500/25 transition-all hover:shadow-brand-500/40 hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z"/>
            </svg>
            Scan QR Absensi
        </a>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        @php
            $hadirCount = $summary['present'] + $summary['late'];
            $pct = $total > 0 ? round(($hadirCount / $total) * 100) : 0;
            $cards = [
                ['label' => 'Hadir',   'val' => $summary['present'],  'emoji' => '✅', 'bg' => 'bg-green-50 border-green-100', 'text' => 'text-green-700'],
                ['label' => 'Terlambat','val' => $summary['late'],    'emoji' => '⏰', 'bg' => 'bg-yellow-50 border-yellow-100','text' => 'text-yellow-700'],
                ['label' => 'Sakit',   'val' => $summary['sick'],     'emoji' => '🏥', 'bg' => 'bg-amber-50 border-amber-100', 'text' => 'text-amber-700'],
                ['label' => 'Izin',    'val' => $summary['excused'],  'emoji' => '📝', 'bg' => 'bg-blue-50 border-blue-100',   'text' => 'text-blue-700'],
                ['label' => 'Alpa',    'val' => $summary['absent'],   'emoji' => '❌', 'bg' => 'bg-red-50 border-red-100',     'text' => 'text-red-700'],
            ];
        @endphp
        @foreach($cards as $c)
            <div class="rounded-2xl border {{ $c['bg'] }} p-4 text-center">
                <p class="text-2xl mb-1">{{ $c['emoji'] }}</p>
                <p class="text-2xl font-black {{ $c['text'] }}">{{ $c['val'] }}</p>
                <p class="text-xs text-gray-500">{{ $c['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Attendance percentage bar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-bold text-gray-700">Persentase Kehadiran</span>
            <span class="text-sm font-black {{ $pct >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $pct }}%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
            <div class="h-3 rounded-full transition-all duration-500 {{ $pct >= 75 ? 'bg-gradient-to-r from-green-400 to-green-500' : 'bg-gradient-to-r from-red-400 to-red-500' }}" style="width: {{ $pct }}%"></div>
        </div>
    </div>

    {{-- History table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100">
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Tanggal</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Mata Pelajaran</th>
                    <th class="text-center px-5 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Guru</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($records as $r)
                    @php
                        $statusBadge = match($r->status) {
                            'present' => 'bg-green-50 text-green-700',
                            'late'    => 'bg-yellow-50 text-yellow-700',
                            'excused' => 'bg-blue-50 text-blue-700',
                            'sick'    => 'bg-amber-50 text-amber-700',
                            'absent'  => 'bg-red-50 text-red-700',
                            default   => 'bg-gray-50 text-gray-700',
                        };
                        $statusLabel = match($r->status) {
                            'present' => 'Hadir',
                            'late'    => 'Terlambat',
                            'excused' => 'Izin',
                            'sick'    => 'Sakit',
                            'absent'  => 'Alpa',
                            default   => ucfirst($r->status),
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3 text-gray-600">
                            {{ \Carbon\Carbon::parse($r->date)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $r->session?->subject?->nama ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            {{ $r->session?->teacher?->name ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">Belum ada data kehadiran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->hasPages())
        <div class="mt-4">{{ $records->links() }}</div>
    @endif
</div>
@endsection
