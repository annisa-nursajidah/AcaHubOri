@extends('layouts.authenticated')
@section('content')
@php $title = 'Tahun Ajaran'; @endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Tahun Ajaran</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola periode akademik</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('academic-years.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Tahun Ajaran
    </a>
    @endif
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50/80">
            <tr>
                <th class="text-left px-6 py-4 font-semibold text-gray-600">Tahun / Semester</th>
                <th class="text-left px-6 py-4 font-semibold text-gray-600">Tanggal Mulai</th>
                <th class="text-left px-6 py-4 font-semibold text-gray-600">Tanggal Selesai</th>
                <th class="text-center px-6 py-4 font-semibold text-gray-600">Status</th>
                <th class="text-right px-6 py-4 font-semibold text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($years as $year)
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-6 py-4 font-medium text-gray-800">
                    {{ $year->tahun }} — {{ $year->semester }}
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $year->tanggal_mulai->format('d M Y') }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $year->tanggal_selesai->format('d M Y') }}</td>
                <td class="px-6 py-4 text-center">
                    @if($year->is_active)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Tidak Aktif</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        @if(auth()->user()->isAdmin())
                            @unless($year->is_active)
                            <form method="POST" action="{{ route('academic-years.activate', $year) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition">Aktifkan</button>
                            </form>
                            @endunless
                            <a href="{{ route('academic-years.edit', $year) }}" class="px-3 py-1.5 text-xs font-medium bg-brand-50 text-brand-700 rounded-lg hover:bg-brand-100 transition">Edit</a>
                            <form method="POST" action="{{ route('academic-years.destroy', $year) }}" onsubmit="return confirm('Hapus tahun ajaran ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">Hapus</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-400">Belum ada data tahun ajaran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $years->links() }}</div>
@endsection
