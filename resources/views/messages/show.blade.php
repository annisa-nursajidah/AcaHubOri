@extends('layouts.authenticated')
@section('content')
@php $title = $message->subject; @endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('messages.inbox') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">← Kembali ke Pesan</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        {{-- Header --}}
        <div class="flex items-start justify-between mb-6 pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-xl font-bold text-gray-800 mb-2">{{ $message->subject }}</h1>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center">
                        <span class="text-xs font-bold text-brand-700">{{ strtoupper(substr($message->sender->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $message->sender->name }}</p>
                        <p class="text-xs text-gray-400">Kepada: {{ $message->receiver->name }} · {{ $message->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('messages.destroy', $message) }}" onsubmit="return confirm('Hapus pesan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">Hapus</button>
            </form>
        </div>

        {{-- Body --}}
        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $message->body }}</div>
    </div>

    {{-- Reply shortcut --}}
    @if($message->sender_id !== auth()->id())
    <div class="mt-4">
        <a href="{{ route('messages.create', ['to' => $message->sender_id, 'subject' => 'Re: ' . $message->subject]) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
            Balas
        </a>
    </div>
    @endif
</div>
@endsection
