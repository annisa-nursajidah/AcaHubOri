@extends('layouts.authenticated')
@section('content')
@php $title = 'Absensi'; @endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Absensi</h1>
        <p class="text-sm text-gray-500 mt-0.5">Kelola kehadiran siswa</p>
    </div>
    <a href="{{ route('attendances.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Input Absensi
    </a>
</div>

{{-- Subject cards linking to reports --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @foreach($subjects as $subject)
        <a href="{{ route('attendances.show', $subject->id) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-brand-200 transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-gray-800 group-hover:text-brand-600 transition">{{ $subject->nama }}</p>
                    <p class="text-xs text-gray-400">{{ $subject->kode }}</p>
                </div>
            </div>
        </a>
    @endforeach
</div>
@endsection
