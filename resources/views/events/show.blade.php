@extends('layouts.authenticated')
@section('content')
@php $title = $event->judul; @endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('events.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">← Kembali ke Kalender</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="w-4 h-4 rounded-full" style="background-color: {{ $event->warna }}"></div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $event->judul }}</h1>
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold"
                          style="background-color: {{ $event->warna }}15; color: {{ $event->warna }}">{{ ucfirst($event->tipe) }}</span>
                </div>
            </div>
            @if(auth()->user()->isAdmin() || auth()->id() === $event->user_id)
            <div class="flex gap-2">
                <a href="{{ route('events.edit', $event) }}" class="px-3 py-1.5 text-xs font-medium bg-brand-50 text-brand-700 rounded-lg hover:bg-brand-100 transition">Edit</a>
                <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Hapus event ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">Hapus</button>
                </form>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-100">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mulai</p>
                <p class="text-sm font-medium text-gray-800">{{ $event->tanggal_mulai->format('d M Y — H:i') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Selesai</p>
                <p class="text-sm font-medium text-gray-800">{{ $event->tanggal_selesai->format('d M Y — H:i') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Dibuat oleh</p>
                <p class="text-sm font-medium text-gray-800">{{ $event->creator->name }}</p>
            </div>
        </div>

        @if($event->deskripsi)
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deskripsi</p>
            <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap">{{ $event->deskripsi }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
