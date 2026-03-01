@extends('layouts.app')
@section('body')

{{-- ───────────── NAVBAR ───────────── --}}
<nav class="fixed top-0 w-full bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        {{-- Logo --}}
        <a href="/" class="text-2xl font-extrabold text-brand-500 tracking-tight">AcaHub</a>

        {{-- Nav links --}}
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="#features" class="hover:text-brand-600 transition">Features</a>
            <a href="{{ route('pricing') }}" class="hover:text-brand-600 transition">For Schools</a>
            <a href="#resources" class="hover:text-brand-600 transition">Resources</a>
            <a href="{{ route('pricing') }}" class="hover:text-brand-600 transition">Pricing</a>
        </div>

        {{-- Auth buttons --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-brand-600 transition">Sign In</a>
            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40">Start Free Trial</a>
        </div>
    </div>
</nav>

{{-- ───────────── HERO ───────────── --}}
<section class="min-h-screen flex items-center pt-16">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
        {{-- Left text --}}
        <div>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-black leading-[1.08] tracking-tight">
                Education<br>
                <span class="text-brand-500">Connect</span><br>
                Simply.
            </h1>
            <p class="mt-6 text-lg text-gray-500 max-w-md leading-relaxed">
                AcaHub bridges the gap between teachers, students, and parents with real-time grading, easy reporting, and seamless communication.
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-accent-500 text-white font-semibold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5">
                    Get Started Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
                <a href="#" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full border-2 border-brand-500 text-brand-600 font-semibold hover:bg-brand-50 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Watch How It Works
                </a>
            </div>
        </div>

        {{-- Right illustration --}}
        <div class="hidden md:flex justify-center">
            <div class="relative">
                {{-- Decorative blobs --}}
                <div class="absolute -top-8 -left-8 w-72 h-72 bg-brand-200/40 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-8 -right-8 w-72 h-72 bg-accent-200/40 rounded-full blur-3xl"></div>
                {{-- Card grid --}}
                <div class="relative grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition">
                        <div class="w-10 h-10 rounded-lg bg-brand-100 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-800">Real-time Grades</h3>
                        <p class="text-sm text-gray-500 mt-1">Instant grade updates for students & parents</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mt-8 hover:shadow-2xl transition">
                        <div class="w-10 h-10 rounded-lg bg-accent-100 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-800">Role-Based Access</h3>
                        <p class="text-sm text-gray-500 mt-1">Admin, teacher & student views</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-800">Easy Reports</h3>
                        <p class="text-sm text-gray-500 mt-1">Semester reports in one click</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mt-8 hover:shadow-2xl transition">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-800">Communication</h3>
                        <p class="text-sm text-gray-500 mt-1">Connect teachers & parents</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ───────────── FOOTER (minimal) ───────────── --}}
<footer class="py-8 text-center text-sm text-gray-400">
    &copy; {{ date('Y') }} AcaHub. Supporting SDG 4 — Quality Education.
</footer>

@endsection
