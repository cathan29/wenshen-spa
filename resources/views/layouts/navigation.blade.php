<nav x-data="{ open: false }" class="bg-white border-b-2 border-[#D4AF37]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20"> 
            
            <div class="flex">
                {{-- 🌸 Logo Area --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-full bg-[#F9F3E3] flex items-center justify-center text-[#6B4E31] group-hover:bg-[#6B4E31] group-hover:text-[#D4AF37] transition duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-serif font-bold text-xl text-[#6B4E31] tracking-wider uppercase">Wenshen</span>
                            <span class="text-[10px] text-[#D4AF37] tracking-[0.2em] uppercase font-bold">Beauty Spa</span>
                        </div>
                    </a>
                </div>

                {{-- 🔗 Desktop Links --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    {{-- 1. Reception (Visible to everyone) --}}
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold uppercase tracking-widest transition duration-150 ease-in-out
                              {{ request()->routeIs('dashboard') ? 'border-[#6B4E31] text-[#6B4E31]' : 'border-transparent text-gray-400 hover:text-[#6B4E31] hover:border-[#D4AF37]' }}">
                        {{ __('Reception') }}
                    </a>

                    {{-- 🛑 Admin Links (Owner Only) --}}
                    @if(auth()->user()->role === 'admin')
                        {{-- 2. Owner's Office (Overview) --}}
                        <a href="{{ route('admin.dashboard') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold uppercase tracking-widest transition duration-150 ease-in-out
                                  {{ request()->routeIs('admin.dashboard') ? 'border-[#6B4E31] text-[#6B4E31]' : 'border-transparent text-gray-400 hover:text-[#6B4E31] hover:border-[#D4AF37]' }}">
                            {{ __('Owner\'s Office') }}
                        </a>

                        {{-- 3. Analytics (New Chart Page) --}}
                        <a href="{{ route('admin.reports') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold uppercase tracking-widest transition duration-150 ease-in-out
                                  {{ request()->routeIs('admin.reports') ? 'border-[#6B4E31] text-[#6B4E31]' : 'border-transparent text-gray-400 hover:text-[#6B4E31] hover:border-[#D4AF37]' }}">
                            {{ __('Analytics') }}
                        </a>
                        
                        {{-- 4. Services (Menu Editor) --}}
                        <a href="{{ route('services.index') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold uppercase tracking-widest transition duration-150 ease-in-out
                                  {{ request()->routeIs('services.*') ? 'border-[#6B4E31] text-[#6B4E31]' : 'border-transparent text-gray-400 hover:text-[#6B4E31] hover:border-[#D4AF37]' }}">
                            {{ __('Services') }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- 👤 Settings Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-xs uppercase font-bold tracking-widest text-[#6B4E31] bg-[#F9F3E3] hover:bg-[#6B4E31] hover:text-[#D4AF37] rounded-full transition duration-300 ease-in-out focus:outline-none">
                            <div class="mr-1">{{ Auth::user()->name }}</div>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-t-4 border-[#D4AF37]">
                            <x-dropdown-link :href="route('profile.edit')" class="text-[#6B4E31] hover:text-[#D4AF37] font-bold text-xs uppercase tracking-wider">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="text-red-400 hover:text-red-600 font-bold text-xs uppercase tracking-wider">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- 📱 Mobile Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-[#6B4E31] hover:text-[#D4AF37] hover:bg-[#F9F3E3] focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- 📱 Mobile Menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#fffbf2] border-t border-[#eaddc5]">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-[#6B4E31] hover:text-[#D4AF37]">
                {{ __('Reception') }}
            </x-responsive-nav-link>
            
            @if(auth()->user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-[#6B4E31] hover:text-[#D4AF37]">
                    {{ __('Owner\'s Office') }}
                </x-responsive-nav-link>
                
                {{-- Added Analytics for Mobile --}}
                <x-responsive-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')" class="text-[#6B4E31] hover:text-[#D4AF37]">
                    {{ __('Analytics') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')" class="text-[#6B4E31] hover:text-[#D4AF37]">
                    {{ __('Services') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-[#eaddc5]">
            <div class="px-4">
                <div class="font-bold text-base text-[#6B4E31]">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-[#D4AF37]">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-500 hover:text-[#6B4E31]">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-red-400">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>