@extends('layouts.authenticated')
@section('content')
@php $title = 'Riwayat Kehadiran'; @endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">Riwayat Kehadiran</h1>
        <p class="text-sm text-gray-500 mt-0.5">Data kehadiran Anda di semua mata pelajaran</p>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        @php
            $pct = $total > 0 ? round(($summary['hadir'] / $total) * 100) : 0;
            $cards = [
                ['label' => 'Hadir', 'val' => $summary['hadir'], 'emoji' => '✅', 'bg' => 'bg-green-50 border-green-100', 'text' => 'text-green-700'],
                ['label' => 'Izin',  'val' => $summary['izin'],  'emoji' => '📝', 'bg' => 'bg-blue-50 border-blue-100',  'text' => 'text-blue-700'],
                ['label' => 'Sakit', 'val' => $summary['sakit'], 'emoji' => '🏥', 'bg' => 'bg-amber-50 border-amber-100','text' => 'text-amber-700'],
                ['label' => 'Alpa',  'val' => $summary['alpa'],  'emoji' => '❌', 'bg' => 'bg-red-50 border-red-100',    'text' => 'text-red-700'],
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
                            'hadir'  => 'bg-green-50 text-green-700',
                            'izin'   => 'bg-blue-50 text-blue-700',
                            'sakit'  => 'bg-amber-50 text-amber-700',
                            'alpa'   => 'bg-red-50 text-red-700',
                            default  => 'bg-gray-50 text-gray-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3 text-gray-600">{{ $r->tanggal->format('d M Y') }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $r->subject->nama }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">{{ ucfirst($r->status) }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $r->teacherProfile?->user?->name ?? '-' }}</td>
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
