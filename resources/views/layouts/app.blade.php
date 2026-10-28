<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PT Semen Tonasa') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/tonasa-logo.png') }}?v=2">
    
    <!-- Tipografi Modern & Bersih: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Skrip untuk Mencegah Layar Berkedip Putih saat Dark Mode -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="font-sans antialiased text-gray-700 bg-gray-50 dark:text-gray-300 dark:bg-gray-900 transition-colors duration-300 overflow-x-hidden selection:bg-[#F5333F]/20 selection:text-[#F5333F]">
    
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" @resize.window="sidebarOpen = window.innerWidth >= 1024">
        
        <!-- Sidebar Navigation Drawer -->
        @include('layouts.navigation')

        <!-- Overlay Mobile Backdrop (z-40) -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 backdrop-blur-xs lg:hidden" style="display: none;"></div>

        <!-- Area Konten Utama -->
        <div class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden z-10 w-full">
            
            @isset($header)
                <header class="sticky top-0 z-20 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700/80 shadow-xs transition-colors duration-300">
                    <div class="flex items-center justify-between px-3 py-2.5 sm:px-6 sm:py-3.5 w-full">
                        
                        <!-- Sisi Kiri: Tombol Hamburger & Slot Header (Auto-truncate & Fluid) -->
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 mr-2 sm:mr-4">
                            <button @click="sidebarOpen = true" aria-label="Buka Menu Sidebar" class="p-1.5 sm:p-2 -ml-1 sm:-ml-2 text-gray-600 dark:text-gray-300 rounded-lg lg:hidden hover:bg-gray-100 dark:hover:bg-gray-700/60 focus:outline-none flex-shrink-0 transition-colors">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>
                            <div class="min-w-0 flex-1 header-slot-wrapper">
                                <div class="text-xs sm:text-sm md:text-base lg:text-lg font-extrabold text-gray-900 dark:text-white uppercase tracking-tight truncate leading-tight">
                                    {{ $header }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sisi Kanan: Theme Toggle & Profil Dropdown (Flex-Shrink-0 agar tidak pernah terpotong di mode HP) -->
                        <div class="flex items-center gap-1 sm:gap-3 flex-shrink-0">
                            
                            <!-- Tombol Dark/Light Mode -->
                            <button onclick="
                                if(document.documentElement.classList.contains('dark')) {
                                    document.documentElement.classList.remove('dark');
                                    localStorage.theme = 'light';
                                } else {
                                    document.documentElement.classList.add('dark');
                                    localStorage.theme = 'dark';
                                }
                            " class="p-1.5 sm:p-2 text-gray-500 dark:text-gray-400 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors flex-shrink-0" aria-label="Ganti Tema">
                                <!-- Ikon Matahari (Dark Mode) -->
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <!-- Ikon Bulan (Light Mode) -->
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            </button>

                            <!-- Menu Profile Dropdown -->
                            <div class="relative flex-shrink-0" x-data="{ openProfile: false }">
                                <button @click="openProfile = !openProfile" @click.away="openProfile = false" class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm font-bold text-gray-900 dark:text-white transition hover:text-[#F5333F] focus:outline-none p-0.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700/50 flex-shrink-0">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-7 h-7 sm:w-8 sm:h-8 rounded-full object-cover shadow-xs border border-gray-200 dark:border-gray-700 flex-shrink-0 ring-1 ring-black/5 dark:ring-white/10">
                                    @else
                                        <div class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#F5333F] text-white text-[11px] sm:text-xs font-bold shadow-xs flex-shrink-0">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="hidden sm:inline-block truncate max-w-[120px]">{{ Auth::user()->name }}</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <div x-show="openProfile" x-transition.opacity class="absolute right-0 w-48 mt-2 origin-top-right bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl outline-none z-50 py-1.5" style="display: none;">
                                    <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 sm:hidden">
                                        <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-[10px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs sm:text-sm text-gray-700 dark:text-gray-300 transition hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white font-semibold">👤 Profil Saya</a>
                                    <hr class="my-1 border-gray-100 dark:border-gray-700">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-xs sm:text-sm text-left text-[#F5333F] transition hover:bg-red-50 dark:hover:bg-gray-700 font-semibold">🚪 Log Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            @endisset

            <main class="p-3 sm:p-5 md:p-6 w-full flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
