@extends('layouts.authenticated')
@section('content')
@php $title = 'Notifikasi'; @endphp

<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-1">{{ auth()->user()->unreadNotifications->count() }} belum dibaca</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
        <form method="POST" action="{{ route('notifications.markAllRead') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-brand-50 text-brand-700 rounded-xl text-sm font-medium hover:bg-brand-100 transition">
                Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-50">
            @forelse($notifications as $n)
            <a href="{{ route('notifications.read', $n->id) }}"
               class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/80 transition {{ is_null($n->read_at) ? 'bg-brand-50/30' : '' }}">
                <div class="w-10 h-10 rounded-full {{ is_null($n->read_at) ? 'bg-brand-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                    @if(str_contains($n->type, 'Grade'))
                        <svg class="w-5 h-5 {{ is_null($n->read_at) ? 'text-brand-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75Z"/></svg>
                    @elseif(str_contains($n->type, 'Announcement'))
                        <svg class="w-5 h-5 {{ is_null($n->read_at) ? 'text-brand-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                    @else
                        <svg class="w-5 h-5 {{ is_null($n->read_at) ? 'text-brand-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm {{ is_null($n->read_at) ? 'font-semibold text-gray-800' : 'text-gray-600' }} truncate">
                        {{ $n->data['message'] ?? 'Notifikasi baru' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if(is_null($n->read_at))
                <span class="w-2 h-2 rounded-full bg-brand-500 flex-shrink-0"></span>
                @endif
            </a>
            @empty
            <div class="px-6 py-12 text-center text-gray-400">Tidak ada notifikasi.</div>
            @endforelse
        </div>
    </div>
    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection
