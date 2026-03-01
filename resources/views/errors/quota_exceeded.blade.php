<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ditutup - {{ $school->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 selection:bg-brand-500/30">
    <!-- Branding Top -->
    <div class="fixed top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-red-500 via-rose-500 to-red-500"></div>
    
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-8 sm:p-10 border border-gray-100 text-center">
        <!-- Logo Area -->
        <div class="mb-6 flex justify-center">
            <div class="w-20 h-20 bg-red-50 rounded-2xl flex items-center justify-center rotate-3 relative shadow-inner">
                <div class="absolute inset-0 bg-red-400 opacity-20 blur-xl rounded-full"></div>
                <svg class="w-10 h-10 text-red-500 relative z-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
        
        <h2 class="text-2xl font-black text-gray-900 mb-2">Mohon Maaf, Pendaftaran Ditutup</h2>
        <p class="text-gray-500 mb-8 leading-relaxed">Kuota pendaftaran siswa baru untuk <strong>{{ $school->name }}</strong> saat ini telah penuh.</p>
        
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-8 text-left rounded-r-xl">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-amber-800 font-medium">
                        Silakan hubungi pihak sekolah atau coba kembali nanti saat kuota pendaftaran telah dibuka kembali.
                    </p>
                </div>
            </div>
        </div>

        <a href="{{ url('/') }}" class="w-full inline-flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-semibold text-gray-700 bg-gray-100 border border-gray-200 hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 transition-all">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
