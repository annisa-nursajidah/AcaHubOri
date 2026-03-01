@extends('layouts.authenticated')
@section('content')
@php $title = 'Absensi — ' . $subject->nama; @endphp

<div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('attendances.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $subject->nama }}</h1>
            <p class="text-sm text-gray-500">Rekap absensi bulan ini</p>
        </div>
    </div>

    {{-- Month selector --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('attendances.show', $subject->id) }}" class="flex gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                <input type="month" name="month" value="{{ $month }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition">Tampilkan</button>
        </form>
    </div>

    @if($dates->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-gray-400">Belum ada data absensi untuk bulan ini.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 sticky left-0 bg-gray-50/80 z-10">Siswa</th>
                            @foreach($dates as $date)
                                <th class="text-center px-2 py-3 font-medium text-gray-500 text-xs min-w-[44px]">{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                            @endforeach
                            <th class="text-center px-3 py-3 font-semibold text-gray-600">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($students as $student)
                            @php
                                $row = $grid[$student->id] ?? [];
                                $totalDates = count($dates);
                                $hadir = collect($row)->filter(fn($s) => $s === 'hadir')->count();
                                $pct = $totalDates > 0 ? round(($hadir / $totalDates) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-2.5 sticky left-0 bg-white z-10">
                                    <p class="font-medium text-gray-800 text-xs">{{ $student->user->name }}</p>
                                </td>
                                @foreach($dates as $date)
                                    @php
                                        $status = $row[$date] ?? null;
                                        $dot = match($status) {
                                            'hadir' => 'bg-green-500',
                                            'izin'  => 'bg-blue-500',
                                            'sakit' => 'bg-amber-500',
                                            'alpa'  => 'bg-red-500',
                                            default => 'bg-gray-200',
                                        };
                                        $label = $status ? ucfirst($status) : '-';
                                    @endphp
                                    <td class="text-center px-2 py-2.5" title="{{ $label }}">
                                        <span class="inline-block w-3 h-3 rounded-full {{ $dot }}"></span>
                                    </td>
                                @endforeach
                                <td class="text-center px-3 py-2.5">
                                    <span class="text-xs font-bold {{ $pct >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $pct }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Legend --}}
        <div class="mt-4 flex items-center gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span> Hadir</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Izin</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Sakit</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500"></span> Alpa</span>
        </div>
    @endif
</div>
@endsection
