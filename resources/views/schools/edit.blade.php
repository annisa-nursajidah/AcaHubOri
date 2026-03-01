@extends('layouts.authenticated')
@section('content')

<div class="max-w-2xl">
    <h1 class="text-2xl font-black text-gray-900 mb-6">Edit Sekolah</h1>

    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('schools.update', $school) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Sekolah *</label>
            <input type="text" name="name" value="{{ old('name', $school->name) }}" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
            <input type="text" name="address" value="{{ old('address', $school->address) }}"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $school->phone) }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email *</label>
                <input type="email" name="email" value="{{ old('email', $school->email) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm">
            </div>
        </div>
        <div class="flex items-center gap-3">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ $school->is_active ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-brand-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
            </label>
            <span class="text-sm font-medium text-gray-700">Sekolah Aktif</span>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-3 rounded-xl bg-brand-500 text-white font-semibold text-sm hover:bg-brand-600 shadow-md transition">Perbarui</button>
            <a href="{{ route('schools.show', $school) }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm hover:bg-gray-200 transition">Batal</a>
        </div>
    </form>
</div>

@endsection
