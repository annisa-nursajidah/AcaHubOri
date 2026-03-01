@extends('layouts.authenticated')
@section('content')

<div class="max-w-2xl">
    <h1 class="text-2xl font-black text-gray-900 mb-6">Tambah Sekolah</h1>

    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('schools.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Sekolah *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
            <input type="text" name="address" value="{{ old('address') }}"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 text-sm">
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-3 rounded-xl bg-brand-500 text-white font-semibold text-sm hover:bg-brand-600 shadow-md transition">Simpan</button>
            <a href="{{ route('schools.index') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm hover:bg-gray-200 transition">Batal</a>
        </div>
    </form>
</div>

@endsection
