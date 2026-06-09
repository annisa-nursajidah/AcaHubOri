@extends('layouts.authenticated')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('attendance-sessions.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
    </a>
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Buka Kelas / Sesi Absensi</h1>
        <p class="text-sm text-gray-500 mt-1">Buat kode QR agar siswa dapat melakukan tap-in absen mandiri.</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6 sm:p-8 max-w-2xl">
    <form action="{{ route('attendance-sessions.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="subject_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Mata Pelajaran <span class="text-red-500">*</span></label>
                <select name="subject_id" id="subject_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all">
                    <option value="" disabled selected>Pilih Mapel...</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->nama }}</option>
                    @endforeach
                </select>
                @error('subject_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="classroom_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Kelas yang Diajar <span class="text-red-500">*</span></label>
                <select name="classroom_id" id="classroom_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all">
                    <option value="" disabled selected>Pilih Kelas...</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}">{{ $classroom->nama }}</option>
                    @endforeach
                </select>
                @error('classroom_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="bg-brand-50/50 border border-brand-100 rounded-xl p-4 text-sm text-brand-800 flex items-start gap-3">
            <svg class="w-5 h-5 text-brand-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
            <p>Sesi absensi akan otomatis aktif segera setelah disimpan. Proyeksikan kode QR ke papan tulis dan minta siswa menggunakan scanner bawaan di aplikasi.</p>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
            <a href="{{ route('attendance-sessions.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-colors shadow-sm">Buka Kelas Sekarang</button>
        </div>
    </form>
</div>
@endsection
