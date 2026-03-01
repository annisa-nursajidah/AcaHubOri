@extends('layouts.authenticated')
@section('content')
@php $title = 'Pesan Masuk'; @endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pesan</h1>
        <p class="text-sm text-gray-500 mt-1">Kotak masuk pesan Anda</p>
    </div>
    <a href="{{ route('messages.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
        Tulis Pesan
    </a>
</div>

{{-- Tab navigation --}}
<div class="flex gap-1 bg-gray-100 p-1 rounded-xl mb-6 max-w-xs">
    <a href="{{ route('messages.inbox') }}" class="flex-1 px-4 py-2 text-center rounded-lg text-sm font-medium transition bg-white text-gray-800 shadow-sm">Masuk</a>
    <a href="{{ route('messages.sent') }}" class="flex-1 px-4 py-2 text-center rounded-lg text-sm font-medium transition text-gray-500 hover:text-gray-700">Terkirim</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="divide-y divide-gray-50">
        @forelse($messages as $msg)
        <a href="{{ route('messages.show', $msg) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/80 transition {{ is_null($msg->read_at) ? 'bg-brand-50/30' : '' }}">
            <div class="w-10 h-10 rounded-full {{ is_null($msg->read_at) ? 'bg-brand-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold {{ is_null($msg->read_at) ? 'text-brand-700' : 'text-gray-500' }}">{{ strtoupper(substr($msg->sender->name, 0, 1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <p class="text-sm font-semibold text-gray-800 truncate {{ is_null($msg->read_at) ? 'font-bold' : '' }}">{{ $msg->sender->name }}</p>
                    @if(is_null($msg->read_at))
                    <span class="w-2 h-2 rounded-full bg-brand-500 flex-shrink-0"></span>
                    @endif
                </div>
                <p class="text-sm text-gray-700 truncate {{ is_null($msg->read_at) ? 'font-semibold' : '' }}">{{ $msg->subject }}</p>
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ Str::limit($msg->body, 80) }}</p>
            </div>
            <p class="text-xs text-gray-400 flex-shrink-0">{{ $msg->created_at->diffForHumans() }}</p>
        </a>
        @empty
        <div class="px-6 py-12 text-center text-gray-400">Tidak ada pesan masuk.</div>
        @endforelse
    </div>
</div>
<div class="mt-4">{{ $messages->links() }}</div>
@endsection
