@extends('layouts.authenticated')
@section('content')
@php $title = 'Daftarkan Siswa'; @endphp

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Daftarkan Siswa ke Kelas</h1>

    <form method="POST" action="{{ route('enrollments.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Siswa</label>
            <select name="student_profile_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition @error('student_profile_id') border-red-300 @enderror">
                <option value="">— Pilih Siswa —</option>
                @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ old('student_profile_id') == $s->id ? 'selected' : '' }}>{{ $s->user->name }} ({{ $s->nis ?? '-' }})</option>
                @endforeach
            </select>
            @error('student_profile_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
            <select name="classroom_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition @error('classroom_id') border-red-300 @enderror">
                <option value="">— Pilih Kelas —</option>
                @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ old('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }} @if($c->academicYear) ({{ $c->academicYear->full_name }}) @endif</option>
                @endforeach
            </select>
            @error('classroom_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran</label>
            <select name="academic_year_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition @error('academic_year_id') border-red-300 @enderror">
                <option value="">— Pilih Tahun Ajaran —</option>
                @foreach($years as $y)
                    <option value="{{ $y->id }}" {{ old('academic_year_id') == $y->id ? 'selected' : '' }}>{{ $y->full_name }}</option>
                @endforeach
            </select>
            @error('academic_year_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">Daftarkan</button>
            <a href="{{ route('enrollments.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
