<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wenshen Spa - Login</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    {{-- Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900">

    <div class="min-h-screen flex">
        
        {{-- 🖼️ LEFT SIDE: SPA IMAGE (Visible on large screens) --}}
        <div class="hidden lg:flex w-1/2 bg-cover bg-center relative" 
             style="background-image: url('https://images.unsplash.com/photo-1600334019640-eb1a9a57ccb7?q=80&w=2070&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-[#6B4E31]/40 mix-blend-multiply"></div>
            <div class="absolute bottom-10 left-10 text-white z-10">
                <h2 class="text-4xl font-serif font-bold mb-2 tracking-wide">Wenshen Beauty Spa</h2>
                <p class="text-sm font-light tracking-widest opacity-90 uppercase">Relaxation • Beauty • Wellness</p>
            </div>
        </div>

        {{-- 📝 RIGHT SIDE: LOGIN FORM --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-12 relative" style="background-color: #F9F3E3;">
            
            {{-- Decorative Top Right Circle --}}
            <div class="absolute top-0 right-0 w-32 h-32 bg-[#D4AF37]/10 rounded-bl-full"></div>

            <div class="w-full max-w-md bg-white p-10 rounded-2xl shadow-2xl border-t-4 border-[#D4AF37]">
                
                {{-- Logo / Header --}}
                <div class="text-center mb-8">
                    <div class="inline-flex justify-center items-center w-16 h-16 rounded-full bg-[#F9F3E3] mb-4 text-[#6B4E31]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-serif font-bold text-[#6B4E31] uppercase tracking-widest">Welcome Back</h2>
                    <p class="text-xs text-gray-400 mt-2 uppercase tracking-wide">Please sign in to your account</p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="email" class="block font-bold text-xs text-[#6B4E31] uppercase tracking-wider mb-2">Email Address</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                               class="w-full border-gray-200 bg-[#F9F3E3]/50 rounded-lg p-3 text-gray-700 focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition outline-none" 
                               placeholder="admin@wenshen.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block font-bold text-xs text-[#6B4E31] uppercase tracking-wider mb-2">Password</label>
                        <input id="password" type="password" name="password" required
                               class="w-full border-gray-200 bg-[#F9F3E3]/50 rounded-lg p-3 text-gray-700 focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition outline-none" 
                               placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="block mb-6">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#6B4E31] shadow-sm focus:ring-[#D4AF37]" name="remember">
                            <span class="ms-2 text-sm text-gray-500">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-[#6B4E31] to-[#8B6E4E] hover:from-[#5a422a] hover:to-[#7a5e3e] text-white font-bold py-4 rounded-lg shadow-lg hover:shadow-xl transition duration-300 uppercase tracking-[0.15em] text-xs">
                        Sign In
                    </button>
                    
                    {{-- Forgot Password Link --}}
                    @if (Route::has('password.request'))
                        <div class="mt-6 text-center">
                            <a class="text-xs text-[#D4AF37] hover:text-[#6B4E31] underline underline-offset-4 transition" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
            
            <p class="mt-8 text-xs text-[#6B4E31]/40 uppercase tracking-widest">© {{ date('Y') }} Wenshen Beauty Spa</p>
        </div>
    </div>
</body>
</html>