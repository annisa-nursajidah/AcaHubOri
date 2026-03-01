@extends('layouts.app')
@section('body')

<div class="min-h-screen flex">
    {{-- ───── LEFT PANEL: Teal gradient + branding ───── --}}
    <div class="hidden lg:flex lg:w-5/12 relative bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 flex-col justify-between p-10 overflow-hidden">
        {{-- Decorative circles --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-white/5 rounded-full"></div>

        {{-- Logo --}}
        <a href="/" class="relative z-10 text-3xl font-extrabold text-white tracking-tight">AcaHub</a>

        {{-- Illustration area --}}
        <div class="relative z-10 flex-1 flex items-center justify-center">
            <div class="text-center text-white/90 space-y-4">
                <div class="w-24 h-24 mx-auto rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                    <svg class="w-12 h-12 text-white/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                </div>
                <h2 class="text-xl font-bold">Join AcaHub</h2>
                <p class="text-white/60 text-sm max-w-xs mx-auto">Create your account and start connecting with your school ecosystem today.</p>
            </div>
        </div>

        {{-- Quote --}}
        <p class="relative z-10 text-white/80 text-sm italic">
            "Quality education is the foundation of a better future."
        </p>
    </div>

    {{-- ───── RIGHT PANEL: Register form ───── --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 bg-white">
        <div class="w-full max-w-md">
            {{-- Back link --}}
            <a href="/" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Back to Home
            </a>

            <h1 class="text-3xl font-extrabold text-gray-900">Create an account</h1>
            <p class="text-gray-500 mt-1">Get started with AcaHub today.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                @csrf

                {{-- Role selector --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">I am a:</label>
                    <div class="flex rounded-full border border-gray-200 p-1 bg-gray-50">
                        @foreach (['admin' => 'Admin', 'teacher' => 'Teacher', 'student' => 'Student/Parent'] as $value => $label)
                            <label class="flex-1 text-center cursor-pointer">
                                <input type="radio" name="role" value="{{ $value }}" class="peer hidden"
                                    {{ old('role', 'student') === $value ? 'checked' : '' }}>
                                <span class="block py-2.5 rounded-full text-sm font-medium transition-all
                                    peer-checked:bg-accent-500 peer-checked:text-white peer-checked:shadow-md
                                    text-gray-500 hover:text-gray-700">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                        placeholder="Your full name"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        placeholder="yourname@school.edu"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" required
                        placeholder="Create a password"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        placeholder="Confirm your password"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-3.5 rounded-xl bg-accent-500 text-white font-semibold text-sm
                           hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5 active:translate-y-0">
                    Create Account
                </button>
            </form>

            {{-- Footer --}}
            <p class="mt-8 text-center text-sm text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-brand-600 font-semibold hover:text-brand-700 transition">Sign in</a>
            </p>
        </div>
    </div>
</div>

@endsection
