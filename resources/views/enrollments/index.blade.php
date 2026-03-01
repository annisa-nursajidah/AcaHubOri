@extends('layouts.authenticated')
@section('content')
@php $title = 'Pendaftaran Siswa'; @endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pendaftaran Siswa</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola penempatan siswa ke kelas</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('enrollments.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Daftarkan Siswa
    </a>
    @endif
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Kelas</label>
            <select name="classroom_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm">
                <option value="">Semua Kelas</option>
                @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun Ajaran</label>
            <select name="academic_year_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm">
                <option value="">Semua</option>
                @foreach($years as $y)
                    <option value="{{ $y->id }}" {{ request('academic_year_id') == $y->id ? 'selected' : '' }}>{{ $y->full_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600 transition">Filter</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50/80">
            <tr>
                <th class="text-left px-6 py-4 font-semibold text-gray-600">Siswa</th>
                <th class="text-left px-6 py-4 font-semibold text-gray-600">Kelas</th>
                <th class="text-left px-6 py-4 font-semibold text-gray-600">Tahun Ajaran</th>
                <th class="text-center px-6 py-4 font-semibold text-gray-600">Status</th>
                <th class="text-right px-6 py-4 font-semibold text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($enrollments as $e)
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-6 py-4 font-medium text-gray-800">{{ $e->studentProfile->user->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $e->classroom->nama }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $e->academicYear->full_name }}</td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $e->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($e->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    @if(auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('enrollments.destroy', $e) }}" onsubmit="return confirm('Hapus pendaftaran ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">Belum ada data pendaftaran.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $enrollments->appends(request()->query())->links() }}</div>
@endsection
