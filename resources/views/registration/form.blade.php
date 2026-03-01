<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Siswa Baru - {{ $school->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 selection:bg-brand-500/30">
    <!-- Branding Top -->
    <div class="fixed top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-brand-500 via-accent-500 to-brand-500"></div>
    
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-8 sm:p-10 border border-gray-100">
        <!-- Logo Area -->
        <div class="mb-8 text-center">
            <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center -rotate-3 mb-4 mx-auto shadow-inner">
                <svg class="w-8 h-8 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Formulir Pendaftaran</h2>
            <p class="text-sm text-gray-500 mt-1">Calon Siswa Baru <span class="font-semibold text-brand-600">{{ $school->name }}</span></p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 text-red-700 p-4 rounded-xl text-sm mb-6 border border-red-100 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z"/></svg>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('registration.submit', $school->id) }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    </div>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-400"
                           placeholder="John Doe">
                </div>
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Aktif</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-400"
                           placeholder="johndoe@example.com">
                </div>
                @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                    </div>
                    <input id="password" type="password" name="password" required
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-400"
                           placeholder="••••••••">
                </div>
                @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                    </div>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-400"
                           placeholder="••••••••">
                </div>
                @error('password_confirmation')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2 border-t border-gray-100">
                <label for="invite_code" class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center justify-between mt-3">
                    <span>Kode Undangan</span>
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-brand-50 text-brand-600 border border-brand-100 uppercase tracking-wider">Opsional</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
                    </div>
                    <input id="invite_code" type="text" name="invite_code" value="{{ old('invite_code') }}"
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-mono uppercase tracking-widest placeholder:normal-case placeholder:tracking-normal placeholder:text-gray-400"
                           placeholder="Contoh: SMPIT99">
                </div>
                <p class="mt-2 text-[11px] text-gray-500 font-medium">✨ Masukkan kode undangan dari Admin Sekolah agar akun Anda dapat langsung disetujui tanpa perlu menunggu.</p>
                @error('invite_code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 px-4 rounded-xl text-sm font-semibold text-white bg-brand-500 hover:bg-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-500/30 transition-all relative group overflow-hidden">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        Daftarkan Saya
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </span>
                    <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                </button>
            </div>
            
            <div class="text-center mt-6">
                <p class="text-sm text-gray-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-accent-600 hover:text-accent-500 transition-colors">Masuk di sini</a></p>
            </div>
        </form>
    </div>
</body>
</html>
