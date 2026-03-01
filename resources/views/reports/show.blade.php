@extends('layouts.authenticated')
@section('content')
@php $title = 'Rapor — ' . $student->user->name; @endphp

<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        @if(! auth()->user()->isStudent())
            <a href="{{ route('reports.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
        @endif
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Rapor — {{ $student->user->name }}</h1>
            <p class="text-sm text-gray-500">{{ $semester }} · {{ $tahunAjaran }}</p>
        </div>
    </div>

    {{-- Semester selector --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('report.show', $student->id) }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Semester</label>
                <select name="semester" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    <option value="Ganjil" {{ $semester === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ $semester === 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tahun Ajaran</label>
                <input type="text" name="tahun" value="{{ $tahunAjaran }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition w-32">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition">Tampilkan</button>
            <a href="{{ route('report.pdf', ['student' => $student->id, 'semester' => $semester, 'tahun' => $tahunAjaran]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Download PDF
            </a>
        </form>
    </div>

    {{-- Student info card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Nama</p>
                <p class="text-sm font-bold text-gray-800">{{ $student->user->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">NIS</p>
                <p class="text-sm font-bold text-gray-800">{{ $student->nis ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Kelas</p>
                <p class="text-sm font-bold text-gray-800">{{ $student->kelas ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Rata-rata</p>
                @php $avgColor = $overallAvg >= 75 ? 'text-green-600' : ($overallAvg >= 50 ? 'text-amber-600' : 'text-red-600'); @endphp
                <p class="text-lg font-black {{ $avgColor }}">{{ number_format($overallAvg, 1) }}</p>
            </div>
        </div>
    </div>

    {{-- Grades table --}}
    @if($subjectGrades->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            <p class="text-gray-500 font-medium">Belum ada data nilai untuk semester ini.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-semibold text-gray-600">#</th>
                            <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Mata Pelajaran</th>
                            <th class="text-center px-5 py-3.5 font-semibold text-gray-600">Tugas</th>
                            <th class="text-center px-5 py-3.5 font-semibold text-gray-600">UTS</th>
                            <th class="text-center px-5 py-3.5 font-semibold text-gray-600">UAS</th>
                            <th class="text-center px-5 py-3.5 font-semibold text-gray-600">Praktik</th>
                            <th class="text-center px-5 py-3.5 font-semibold text-gray-600">Rata-rata</th>
                            <th class="text-center px-5 py-3.5 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Guru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($subjectGrades as $i => $sg)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-5 py-3.5 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-gray-800">{{ $sg->subject->nama }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $sg->subject->kode }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-center text-gray-600">{{ $sg->tugas !== null ? number_format($sg->tugas, 1) : '-' }}</td>
                                <td class="px-5 py-3.5 text-center text-gray-600">{{ $sg->uts !== null ? number_format($sg->uts, 1) : '-' }}</td>
                                <td class="px-5 py-3.5 text-center text-gray-600">{{ $sg->uas !== null ? number_format($sg->uas, 1) : '-' }}</td>
                                <td class="px-5 py-3.5 text-center text-gray-600">{{ $sg->praktik !== null ? number_format($sg->praktik, 1) : '-' }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    @php $avgC = $sg->average >= 75 ? 'text-green-600' : ($sg->average >= 50 ? 'text-amber-600' : 'text-red-600'); @endphp
                                    <span class="font-bold {{ $avgC }}">{{ $sg->average }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @php $statusBadge = $sg->status === 'Tuntas' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'; @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">{{ $sg->status }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $sg->teacher }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="6" class="px-5 py-3.5 text-right text-gray-600">Rata-rata Keseluruhan</td>
                            <td class="px-5 py-3.5 text-center">
                                @php $footColor = $overallAvg >= 75 ? 'text-green-600' : ($overallAvg >= 50 ? 'text-amber-600' : 'text-red-600'); @endphp
                                <span class="font-black text-lg {{ $footColor }}">{{ number_format($overallAvg, 1) }}</span>
                            </td>
                            <td colspan="2" class="px-5 py-3.5">
                                @php $overallStatus = $overallAvg >= 75 ? 'Tuntas' : 'Belum Tuntas'; $overallBadge = $overallAvg >= 75 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'; @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $overallBadge }}">{{ $overallStatus }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Weight info --}}
        <div class="mt-4 p-4 rounded-xl bg-blue-50/50 border border-blue-100">
            <p class="text-xs text-blue-700">
                <strong>Bobot perhitungan:</strong> Tugas 25% · UTS 25% · UAS 35% · Praktik 15% · KKM: 75
            </p>
        </div>
    @endif
</div>
@endsection
