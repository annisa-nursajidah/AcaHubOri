<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — AcaHub</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:'#f0fdfa',100:'#ccfbf1',200:'#99f6e4',300:'#5eead4',400:'#2dd4bf',
                            500:'#0891b2',600:'#0e7490',700:'#155e75',800:'#164e63',900:'#134152',
                        },
                        accent: {
                            50:'#fff7ed',100:'#ffedd5',200:'#fed7aa',300:'#fdba74',400:'#fb923c',
                            500:'#f97316',600:'#ea580c',700:'#c2410c',800:'#9a3412',900:'#7c2d12',
                        },
                    },
                },
            },
        }
    </script>
    <style>body{font-family:'Inter',sans-serif}</style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

<div class="min-h-screen flex">
    {{-- ───── SIDEBAR ───── --}}
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-100 transform -translate-x-full lg:translate-x-0 transition-transform duration-200">
        {{-- Logo --}}
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
            <a href="{{ route('dashboard') }}" class="text-2xl font-extrabold text-brand-500 tracking-tight">AcaHub</a>
        </div>

        {{-- Nav --}}
        <nav class="p-4 space-y-1">
            @php $route = Route::currentRouteName(); @endphp

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ $route === 'dashboard' ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Dashboard
            </a>

            @if(!auth()->user()->isAdmin())
            <a href="{{ route('grades.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ str_starts_with($route, 'grades') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                Nilai / Grades
            </a>

            <a href="{{ route('subjects.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ str_starts_with($route, 'subjects') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                Mata Pelajaran
            </a>

            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ str_starts_with($route ?? '', 'report') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                Rapor
            </a>
            @endif

            <a href="{{ route('announcements.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ str_starts_with($route ?? '', 'announcement') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                Pengumuman
            </a>

            @if(!auth()->user()->isAdmin())
            <a href="{{ route('attendances.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ str_starts_with($route ?? '', 'attendance') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/></svg>
                Absensi
            </a>

            <a href="{{ route('classrooms.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ str_starts_with($route ?? '', 'classrooms') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                Kelas
            </a>

            <a href="{{ route('enrollments.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ str_starts_with($route ?? '', 'enrollments') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                Pendaftaran
            </a>
            @endif

            <a href="{{ route('messages.inbox') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ str_starts_with($route ?? '', 'messages') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                Pesan
                @php $unreadCount = auth()->user()->unreadMessagesCount(); @endphp
                @if($unreadCount > 0)
                <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500 text-white">{{ $unreadCount }}</span>
                @endif
            </a>

            <a href="{{ route('events.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                      {{ str_starts_with($route ?? '', 'events') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                Kalender
            </a>

            @if(auth()->user()->isAdmin() || auth()->user()->isSchoolAdmin())
            <div class="pt-3 mt-3 border-t border-gray-100">
                <p class="px-4 pb-2 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Manajemen</p>

                <a href="{{ route('academic-years.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                          {{ str_starts_with($route ?? '', 'academic-years') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/></svg>
                    Tahun Ajaran
                </a>

                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                          {{ str_starts_with($route, 'users') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                    Kelola Users
                </a>

                @if(auth()->user()->isAdmin())
                <p class="px-4 pb-2 mt-4 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Sistem AcaHub</p>
                <a href="{{ route('schools.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                          {{ str_starts_with($route ?? '', 'schools') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z"/></svg>
                    Sekolah Terdaftar
                </a>

                <a href="{{ route('subscriptions.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                          {{ str_starts_with($route ?? '', 'subscriptions') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>
                    Data Langganan
                </a>
                @endif
            </div>
            @endif
        </nav>

        {{-- User info at bottom --}}
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-100">
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.show') }}" class="w-9 h-9 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-sm hover:ring-2 hover:ring-brand-300 transition">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </a>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ───── MAIN CONTENT ───── --}}
    <div class="flex-1 lg:ml-64">
        {{-- Top bar (mobile hamburger + page title) --}}
        <header class="sticky top-0 z-30 h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center px-6 gap-4">
            <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="lg:hidden text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
            <h2 class="text-lg font-bold text-gray-800 flex-1">{{ $title ?? 'Dashboard' }}</h2>

            {{-- Notification bell --}}
            <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition" title="Notifikasi">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                @php $notifCount = auth()->user()->unreadNotifications->count(); @endphp
                @if($notifCount > 0)
                <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                @endif
            </a>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-6 mt-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Page content --}}
        <main class="p-6">
            @yield('content')
        </main>
    </div>
</div>

{{-- Overlay for mobile sidebar --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-30 hidden lg:hidden" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')"></div>

@stack('scripts')
</body>
</html>
