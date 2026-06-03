@extends('layouts.authenticated')

@section('content')
<div class="mb-6 flex items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <a href="{{ route('parents.index') }}" class="p-2 bg-white border border-gray-200 text-gray-500 rounded-xl hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Daftarkan Wali Murid</h1>
            <p class="text-sm text-gray-500 mt-1">Buat akun untuk portal dan tautkan ke akun Siswa tercatat.</p>
        </div>
    </div>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <form action="{{ route('parents.store') }}" method="POST">
            @csrf

            <!-- IDENTITAS ORANG TUA -->
            <div class="mb-8 p-5 bg-gray-50 rounded-2xl border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    Informasi Akun Wali Murid
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap Wali Murid <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 rounded-xl border-gray-200 bg-white focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors shadow-sm @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Cth: Budi Santoso">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Login <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 rounded-xl border-gray-200 bg-white focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors shadow-sm @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="budi@example.com">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password Portal <span class="text-red-500">*</span></label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 rounded-xl border-gray-200 bg-white focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors shadow-sm @error('password') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-3 rounded-xl border-gray-200 bg-white focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors shadow-sm">
                    </div>
                </div>
            </div>

            <!-- PENAUTAN SISWA -->
            <div class="mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-2">Tautkan ke Siswa</h2>
                <p class="text-sm text-gray-500 mb-4">Pilih satu atau lebih siswa yang merupakan anak dari wali murid ini. Wali murid hanya dapat melihat rapor dan kehadiran untuk siswa yang ditauntukan padanya.</p>
                
                <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden max-h-80 overflow-y-auto">
                    @if($students->count() === 0)
                        <div class="p-6 text-center text-sm text-gray-500">
                            Belum ada siswa yang mendaftar di sekolah ini.
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:gap-px bg-gray-200">
                            @foreach($students as $student)
                            <label class="flex items-start gap-4 p-4 bg-white hover:bg-brand-50 cursor-pointer transition-colors w-full group">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" 
                                        {{ (is_array(old('student_ids')) && in_array($student->id, old('student_ids'))) ? 'checked' : '' }}
                                        class="w-5 h-5 text-brand-600 bg-gray-100 border-gray-300 rounded focus:ring-brand-500 focus:ring-2 cursor-pointer">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate group-hover:text-brand-700 transition-colors">{{ $student->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $student->email }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    @endif
                </div>
                @error('student_ids') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
                @error('student_ids.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('parents.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
                    Simpan & Daftarkan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
