@extends('layouts.authenticated')
@section('content')
@php $title = 'Kelas'; @endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Kelas</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola kelas dan wali kelas</p>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isSchoolAdmin())
    <a href="{{ route('classrooms.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Kelas
    </a>
    @endif
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($classrooms as $classroom)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center">
                <span class="text-lg font-bold text-brand-600">{{ $classroom->tingkat }}</span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-accent-50 text-accent-700">
                {{ $classroom->enrollments_count }} siswa
            </span>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $classroom->nama }}</h3>
        <p class="text-sm text-gray-500 mb-1">Tingkat {{ $classroom->tingkat }}</p>
        @if($classroom->waliKelas)
            <p class="text-sm text-gray-500">Wali Kelas: <span class="font-medium text-gray-700">{{ $classroom->waliKelas->user->name }}</span></p>
        @endif
        @if($classroom->academicYear)
            <p class="text-xs text-gray-400 mt-2">{{ $classroom->academicYear->full_name }}</p>
        @endif
        <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-50">
            <a href="{{ route('classrooms.show', $classroom) }}" class="px-3 py-1.5 text-xs font-medium bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition">Detail</a>
            @if(auth()->user()->isAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('classrooms.edit', $classroom) }}" class="px-3 py-1.5 text-xs font-medium bg-brand-50 text-brand-700 rounded-lg hover:bg-brand-100 transition">Edit</a>
            <form method="POST" action="{{ route('classrooms.destroy', $classroom) }}" onsubmit="return confirm('Hapus kelas ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">Hapus</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 text-gray-400">Belum ada data kelas.</div>
    @endforelse
</div>

<div class="mt-4">{{ $classrooms->links() }}</div>
@endsection
