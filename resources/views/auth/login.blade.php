@extends('layouts.app')
@section('body')

<div class="min-h-screen flex">
    {{-- ───── LEFT PANEL: Teal gradient + branding ───── --}}
    <div class="hidden lg:flex lg:w-5/12 relative bg-gradient-to-br from-brand-500 via-brand-600 to-brand-800 flex-col justify-between p-10 overflow-hidden">
        {{-- Decorative circles --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-white/5 rounded-full"></div>

        {{-- Logo --}}
        <a href="/" class="relative z-10 text-3xl font-extrabold text-white tracking-tight">AcaHub</a>

        {{-- Illustration area --}}
        <div class="relative z-10 flex-1 flex items-center justify-center">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 max-w-sm">
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white/20 rounded-lg h-20"></div>
                    <div class="bg-white/15 rounded-lg h-20 col-span-2"></div>
                    <div class="bg-white/15 rounded-lg h-16 col-span-2"></div>
                    <div class="bg-white/20 rounded-lg h-16"></div>
                    <div class="bg-white/10 rounded-lg h-14"></div>
                    <div class="bg-white/20 rounded-lg h-14"></div>
                    <div class="bg-white/15 rounded-lg h-14"></div>
                </div>
            </div>
        </div>

        {{-- Quote --}}
        <p class="relative z-10 text-white/80 text-sm italic">
            "Empowering education through seamless connection."
        </p>
    </div>

    {{-- ───── RIGHT PANEL: Login form ───── --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 bg-white">
        <div class="w-full max-w-md">
            {{-- Back link --}}
            <a href="/" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Back to Home
            </a>

            <h1 class="text-3xl font-extrabold text-gray-900">Welcome back!</h1>
            <p class="text-gray-500 mt-1">Please enter your details to sign in.</p>

            {{-- Session status --}}
            @if (session('status'))
                <div class="mt-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                @csrf

                {{-- Role selector --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">I am a:</label>
                    <div class="flex rounded-full border border-gray-200 p-1 bg-gray-50">
                        @foreach (['admin' => 'Admin', 'teacher' => 'Teacher', 'student' => 'Student/Parent'] as $value => $label)
                            <label class="flex-1 text-center cursor-pointer">
                                <input type="radio" name="role" value="{{ $value }}" class="peer hidden"
                                    {{ old('role', 'teacher') === $value ? 'checked' : '' }}>
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

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
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
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                            placeholder="Enter your password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm placeholder-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition pr-12">
                        <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember & Forgot --}}
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                        <span class="text-gray-600">Remember for 30 days</span>
                    </label>
                    <a href="#" class="text-brand-500 hover:text-brand-700 font-medium transition">Forgot password?</a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-3.5 rounded-xl bg-accent-500 text-white font-semibold text-sm
                           hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all hover:shadow-accent-500/40 hover:-translate-y-0.5 active:translate-y-0">
                    Sign In
                </button>
            </form>

            {{-- Footer --}}
            <p class="mt-8 text-center text-sm text-gray-500">
                Don't have an account?
                <a href="#" class="text-gray-700 font-semibold hover:text-brand-600 transition">Contact your school administrator.</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
@endpush

@endsection
