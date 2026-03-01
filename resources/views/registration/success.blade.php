<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - {{ $school->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 selection:bg-brand-500/30">
    <!-- Branding Top -->
    <div class="fixed top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-brand-500 via-accent-500 to-brand-500"></div>
    
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-8 sm:p-10 border border-gray-100 text-center">
        <!-- Logo Area -->
        <div class="mb-6 flex justify-center">
            <div class="w-20 h-20 bg-green-50 rounded-2xl flex items-center justify-center rotate-3 relative shadow-inner">
                <div class="absolute inset-0 bg-green-400 opacity-20 blur-xl rounded-full"></div>
                <svg class="w-10 h-10 text-green-500 relative z-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        
        <h2 class="text-2xl font-black text-gray-900 mb-2">Pendaftaran Berhasil!</h2>
        <p class="text-gray-500 mb-8 leading-relaxed">Terima kasih telah mendaftar di <strong>{{ $school->name }}</strong>. Akun Anda berhasil dibuat dengan status <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded ml-1">Pending</span>.</p>
        
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8 text-left rounded-r-xl">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Anda belum bisa login. Harap menunggu konfirmasi penerimaan / <i>approval</i> dari pihak Tata Usaha atau Admin Sekolah.
                    </p>
                </div>
            </div>
        </div>

        <a href="{{ url('/') }}" class="w-full inline-flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:ring-gray-200 transition-all">
            Kembali ke Beranda AcaHub
        </a>
    </div>
</body>
</html>
