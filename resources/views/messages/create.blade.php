@extends('layouts.authenticated')
@section('content')
@php $title = 'Tulis Pesan'; @endphp

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tulis Pesan Baru</h1>

    <form method="POST" action="{{ route('messages.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Kepada</label>
            <select name="receiver_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition @error('receiver_id') border-red-300 @enderror">
                <option value="">— Pilih Penerima —</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('receiver_id', request('to')) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                @endforeach
            </select>
            @error('receiver_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Subjek</label>
            <input type="text" name="subject" value="{{ old('subject', request('subject')) }}"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition @error('subject') border-red-300 @enderror">
            @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Pesan</label>
            <textarea name="body" rows="8"
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition resize-none @error('body') border-red-300 @enderror">{{ old('body') }}</textarea>
            @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">Kirim Pesan</button>
            <a href="{{ route('messages.inbox') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
