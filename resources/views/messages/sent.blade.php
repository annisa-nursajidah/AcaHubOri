@extends('layouts.authenticated')
@section('content')
@php $title = 'Pesan Terkirim'; @endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pesan</h1>
        <p class="text-sm text-gray-500 mt-1">Pesan yang telah Anda kirim</p>
    </div>
    <a href="{{ route('messages.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
        Tulis Pesan
    </a>
</div>

{{-- Tab navigation --}}
<div class="flex gap-1 bg-gray-100 p-1 rounded-xl mb-6 max-w-xs">
    <a href="{{ route('messages.inbox') }}" class="flex-1 px-4 py-2 text-center rounded-lg text-sm font-medium transition text-gray-500 hover:text-gray-700">Masuk</a>
    <a href="{{ route('messages.sent') }}" class="flex-1 px-4 py-2 text-center rounded-lg text-sm font-medium transition bg-white text-gray-800 shadow-sm">Terkirim</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="divide-y divide-gray-50">
        @forelse($messages as $msg)
        <a href="{{ route('messages.show', $msg) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/80 transition">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold text-gray-500">{{ strtoupper(substr($msg->receiver->name, 0, 1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">Ke: {{ $msg->receiver->name }}</p>
                <p class="text-sm text-gray-600 truncate">{{ $msg->subject }}</p>
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ Str::limit($msg->body, 80) }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</p>
                @if($msg->read_at)
                <p class="text-xs text-green-500 mt-1">✓ Dibaca</p>
                @else
                <p class="text-xs text-gray-400 mt-1">Belum dibaca</p>
                @endif
            </div>
        </a>
        @empty
        <div class="px-6 py-12 text-center text-gray-400">Tidak ada pesan terkirim.</div>
        @endforelse
    </div>
</div>
<div class="mt-4">{{ $messages->links() }}</div>
@endsection
