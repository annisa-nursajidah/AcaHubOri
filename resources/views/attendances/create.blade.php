@extends('layouts.authenticated')
@section('content')
@php $title = 'Input Absensi'; @endphp

<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('attendances.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <h1 class="text-2xl font-extrabold text-gray-900">Input Absensi</h1>
    </div>

    {{-- Select subject & date --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('attendances.create') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Mata Pelajaran</label>
                <select name="subject_id" required class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    <option value="">Pilih Mapel</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ $subjectId == $s->id ? 'selected' : '' }}>{{ $s->nama }} ({{ $s->kode }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition">Tampilkan</button>
        </form>
    </div>

    @if($subjectId)
        <form method="POST" action="{{ route('attendances.store') }}">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $subjectId }}">
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-semibold text-gray-600">#</th>
                            <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Siswa</th>
                            <th class="text-center px-5 py-3.5 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($students as $i => $student)
                            @php $current = $existing[$student->id] ?? 'hadir'; @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-[10px]">
                                            {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $student->user->name }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $student->nis ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex justify-center gap-1">
                                        @foreach(['hadir' => '✅', 'izin' => '📝', 'sakit' => '🏥', 'alpa' => '❌'] as $val => $emoji)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="attendance[{{ $student->id }}][status]" value="{{ $val }}"
                                                    {{ $current === $val ? 'checked' : '' }}
                                                    class="peer sr-only">
                                                <span class="inline-flex w-10 h-10 items-center justify-center rounded-xl border-2 border-gray-200 text-base peer-checked:border-brand-500 peer-checked:bg-brand-50 transition" title="{{ ucfirst($val) }}">
                                                    {{ $emoji }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <input type="text" name="attendance[{{ $student->id }}][keterangan]" placeholder="Opsional..."
                                        class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-brand-500/30 focus:border-brand-500 transition">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <button type="submit" class="px-6 py-3 rounded-xl bg-accent-500 text-white font-semibold text-sm hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all">Simpan Absensi</button>
                <a href="{{ route('attendances.index') }}" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">Batal</a>
            </div>
        </form>
    @endif
</div>
@endsection
