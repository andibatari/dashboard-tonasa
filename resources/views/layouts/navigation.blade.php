<aside 
    :class="sidebarOpen ? 'translate-x-0 w-72' : '-translate-x-full w-72 lg:translate-x-0 lg:w-20'" 
    class="fixed lg:static inset-y-0 left-0 z-50 flex flex-col h-screen py-6 transition-all duration-300 ease-in-out bg-white dark:bg-[#0D0D0D] border-r border-gray-100 dark:border-gray-800/60 shadow-2xl dark:shadow-none"
>
    <!-- Header Sidebar -->
    <div :class="sidebarOpen || window.innerWidth < 1024 ? 'flex items-center justify-between px-5 mb-7' : 'flex flex-col items-center gap-3 px-2 mb-7'">
        {{-- Logo Semen Tonasa saat Sidebar Terbuka / Mobile --}}
        <a href="{{ route('dashboard') }}" x-show="sidebarOpen || window.innerWidth < 1024" class="flex items-center gap-3 group focus:outline-none select-none">
            <img src="{{ asset('images/tonasa-logo.png') }}" alt="PT Semen Tonasa" class="h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-105">
            <div class="flex flex-col">
                <span class="text-xs font-bold text-gray-900 dark:text-white leading-tight font-display tracking-wide">SEMEN TONASA</span>
                <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Portal Karyawan</span>
            </div>
        </a>

        {{-- Icon saat Sidebar Collapsed (Desktop) --}}
        <a href="{{ route('dashboard') }}" x-show="!sidebarOpen && window.innerWidth >= 1024" class="flex items-center justify-center p-1 group focus:outline-none select-none" title="PT Semen Tonasa">
            <img src="{{ asset('images/tonasa-logo.png') }}" alt="PT Semen Tonasa" class="h-8 w-8 object-contain transition-transform duration-200 group-hover:scale-110">
        </a>
        
        <!-- Tombol Tutup (Mobile) -->
        <button @click="sidebarOpen = false" class="p-1.5 text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white focus:outline-none lg:hidden transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        
        <!-- Tombol Toggle (Desktop) -->
        <button @click="sidebarOpen = !sidebarOpen" class="hidden p-1.5 text-gray-400 rounded-lg lg:block hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white focus:outline-none transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"></path></svg>
        </button>
    </div>

    <!-- Daftar Menu (Dengan Margin Kiri-Kanan 'Pill Design') -->
    <nav class="flex-1 px-3 space-y-1 overflow-y-auto scrollbar-hide">
        
        <!-- Beranda -->
        <a href="{{ route('dashboard') }}" class="relative flex items-center px-3 py-2.5 rounded-xl group transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-red-50 dark:bg-[#F5333F]/10 text-[#F5333F] font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/40 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-[#F5333F]' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white transition-colors' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path></svg>
            <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-3 text-[13.5px]">Beranda</span>
        </a>

        <!-- ========================================== -->
        <!-- MENU KHUSUS KARYAWAN UMUM (Role: User) -->
        <!-- ========================================== -->
        <div class="pt-5 pb-2 px-3">
            <span x-show="sidebarOpen || window.innerWidth < 1024" class="text-[10px] font-bold tracking-widest text-gray-400 dark:text-gray-500 uppercase">Layanan Karyawan</span>
        </div>

        @php
            // Mengecek langsung ke database apakah nilai toggle KMST bernilai '1'
            $settingKmst = \App\Models\Setting::where('key', 'kmst_event_active')->first();
            $isKmstActive = $settingKmst ? ($settingKmst->value === '1') : false;
        @endphp

        @if($isKmstActive)
            <a href="{{ route('inovasi.portal') }}" class="relative flex items-center px-3 py-2.5 rounded-xl group transition-all duration-200 {{ request()->routeIs('inovasi.*') ? 'bg-red-50 dark:bg-[#F5333F]/10 text-[#F5333F] font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/40 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('inovasi.*') ? 'text-[#F5333F]' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white transition-colors' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.509 1.333 1.509 2.316V18"></path></svg>
                <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-3 text-[13.5px] font-medium whitespace-nowrap">KMST Inovasi</span>
            </a>
        @endif


        <!-- ========================================== -->
        <!-- MENU KHUSUS INTERNAL HC & GRC (Developer/Admin) -->
        <!-- ========================================== -->
        @hasanyrole('Developer|Super Admin|Admin')
            <div class="pt-5 pb-2 px-3">
                <span x-show="sidebarOpen || window.innerWidth < 1024" class="text-[10px] font-bold tracking-widest text-[#F5333F] uppercase">Dept. HC & GRC</span>
            </div>

            <!-- 1. HC Planning & Development -->
            <div x-data="{ openHCPD: false }">
                <button @click="openHCPD = !openHCPD; if(!sidebarOpen && window.innerWidth >= 1024) sidebarOpen = true" class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/40 group transition-all duration-200 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"></path></svg>
                        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-3 text-[13.5px] font-medium whitespace-nowrap">HC Planning & Dev</span>
                    </div>
                    <svg x-show="sidebarOpen || window.innerWidth < 1024" :class="{'rotate-180': openHCPD}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openHCPD && (sidebarOpen || window.innerWidth < 1024)" x-transition.opacity class="mt-1 ml-5 pl-4 border-l-2 border-gray-100 dark:border-gray-800 space-y-1">
                    <a href="#" class="block px-3 py-2 text-[12.5px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40 rounded-lg transition-colors">OD & Career Mgt.</a>
                    <a href="#" class="block px-3 py-2 text-[12.5px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40 rounded-lg transition-colors">Performance & Talent</a>
                    <a href="#" class="block px-3 py-2 text-[12.5px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40 rounded-lg transition-colors">Learning & Development</a>
                </div>
            </div>

            <!-- 2. HC Operational -->
            <div x-data="{ openHCO: false }">
                <button @click="openHCO = !openHCO; if(!sidebarOpen && window.innerWidth >= 1024) sidebarOpen = true" class="flex items-center justify-between w-full px-3 py-2.5 mt-1 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/40 group transition-all duration-200 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path></svg>
                        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-3 text-[13.5px] font-medium whitespace-nowrap">HC Operational</span>
                    </div>
                    <svg x-show="sidebarOpen || window.innerWidth < 1024" :class="{'rotate-180': openHCO}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openHCO && (sidebarOpen || window.innerWidth < 1024)" x-transition.opacity class="mt-1 ml-5 pl-4 border-l-2 border-gray-100 dark:border-gray-800 space-y-1">
                    <a href="#" class="block px-3 py-2 text-[12.5px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40 rounded-lg transition-colors">PA & CompBen</a>
                    <a href="#" class="block px-3 py-2 text-[12.5px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40 rounded-lg transition-colors">Employee Facility</a>
                    <a href="#" class="block px-3 py-2 text-[12.5px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40 rounded-lg transition-colors">HC Partner & Culture</a>
                </div>
            </div>

            <!-- 3. Business & System Development -->
            <div x-data="{ openBusdev: {{ request()->routeIs('busdev.*') ? 'true' : 'false' }} }">
                <button @click="openBusdev = !openBusdev; if(!sidebarOpen && window.innerWidth >= 1024) sidebarOpen = true" class="flex items-center justify-between w-full px-3 py-2.5 mt-1 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/40 group transition-all duration-200 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.114 48.114 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"></path></svg>
                        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-3 text-[13.5px] font-medium whitespace-nowrap">Business & Sys. Dev</span>
                    </div>
                    <svg x-show="sidebarOpen || window.innerWidth < 1024" :class="{'rotate-180': openBusdev}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openBusdev && (sidebarOpen || window.innerWidth < 1024)" x-transition.opacity class="mt-1 ml-5 pl-4 border-l-2 border-gray-100 dark:border-gray-800 space-y-1">
                    <a href="{{ route('busdev.bpm-icofr.index') }}" class="block px-3 py-2 text-[12.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('busdev.bpm-icofr.*') ? 'bg-red-50 dark:bg-[#F5333F]/10 text-[#F5333F] font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40' }}">
                        Bus. Dev, Process & ICoFR
                    </a>
                    <a href="{{ route('busdev.system-development.index') }}" class="block px-3 py-2 text-[12.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('busdev.system-development.*') ? 'bg-red-50 dark:bg-[#F5333F]/10 text-[#F5333F] font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40' }}">
                        System Development
                    </a>
                    <a href="{{ route('busdev.innovation.index') }}" class="block px-3 py-2 text-[12.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('busdev.innovation.*') ? 'bg-red-50 dark:bg-[#F5333F]/10 text-[#F5333F] font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40' }}">
                        Innovation Management
                    </a>
                </div>
            </div>

            <!-- 4. GRC Management -->
            <div x-data="{ openGrc: false }">
                <button @click="openGrc = !openGrc; if(!sidebarOpen && window.innerWidth >= 1024) sidebarOpen = true" class="flex items-center justify-between w-full px-3 py-2.5 mt-1 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/40 group transition-all duration-200 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"></path></svg>
                        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-3 text-[13.5px] font-medium whitespace-nowrap">GRC Management</span>
                    </div>
                    <svg x-show="sidebarOpen || window.innerWidth < 1024" :class="{'rotate-180': openGrc}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openGrc && (sidebarOpen || window.innerWidth < 1024)" x-transition.opacity class="mt-1 ml-5 pl-4 border-l-2 border-gray-100 dark:border-gray-800 space-y-1">
                    <a href="#" class="block px-3 py-2 text-[12.5px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40 rounded-lg transition-colors">Risk Mgt & BCM</a>
                    <a href="#" class="block px-3 py-2 text-[12.5px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40 rounded-lg transition-colors">Gov, Compliance & ESG</a>
                </div>
            </div>

            <!-- 5. Health Service & Industrial Hygiene -->
            <div x-data="{ openHealth: false }">
                <button @click="openHealth = !openHealth; if(!sidebarOpen && window.innerWidth >= 1024) sidebarOpen = true" class="flex items-center justify-between w-full px-3 py-2.5 mt-1 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/40 group transition-all duration-200 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path></svg>
                        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-3 text-[13.5px] font-medium whitespace-nowrap">Health Service & Ind.</span>
                    </div>
                    <svg x-show="sidebarOpen || window.innerWidth < 1024" :class="{'rotate-180': openHealth}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openHealth && (sidebarOpen || window.innerWidth < 1024)" x-transition.opacity class="mt-1 ml-5 pl-4 border-l-2 border-gray-100 dark:border-gray-800 space-y-1">
                    <a href="#" class="block px-3 py-2 text-[12.5px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40 rounded-lg transition-colors">Med. Support & Hygiene</a>
                    <a href="#" class="block px-3 py-2 text-[12.5px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-800/40 rounded-lg transition-colors">Services & Nursing</a>
                </div>
            </div>
        @endhasanyrole

        <!-- ========================================== -->
        <!-- MENU LAYANAN KESEHATAN / STMC (Role: Admin STMC, Dokter STMC, Super Admin, Developer) -->
        <!-- ========================================== -->
        @hasanyrole('Developer|Super Admin|Admin|Admin STMC|Dokter STMC')
            <div class="pt-5 pb-2 px-3">
                <span x-show="sidebarOpen || window.innerWidth < 1024" class="text-[10px] font-bold tracking-widest text-[#F5333F] uppercase">Layanan Kesehatan (STMC)</span>
            </div>

            <!-- Dropdown Menu STMC -->
            <div x-data="{ openSTMC: {{ request()->routeIs('stmc.*') ? 'true' : 'false' }} }">
                <button @click="openSTMC = !openSTMC; if(!sidebarOpen && window.innerWidth >= 1024) sidebarOpen = true" class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/40 group transition-all duration-200 {{ request()->routeIs('stmc.*') ? 'text-[#F5333F] font-semibold bg-red-50/50 dark:bg-[#F5333F]/10' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }} focus:outline-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 {{ request()->routeIs('stmc.*') ? 'text-[#F5333F]' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white transition-colors' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-3 text-[13.5px] font-medium whitespace-nowrap">Klinik & MCU</span>
                    </div>
                    <svg x-show="sidebarOpen || window.innerWidth < 1024" :class="{'rotate-180': openSTMC}" class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openSTMC && (sidebarOpen || window.innerWidth < 1024)" x-transition class="pl-9 pr-2 py-1 space-y-1">
                    <a href="{{ route('stmc.jadwal.index') }}" class="block px-3 py-2 text-[12.5px] font-medium {{ request()->routeIs('stmc.jadwal.*') ? 'text-[#F5333F] font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }} hover:bg-gray-50 dark:hover:bg-gray-800/40 rounded-lg transition-colors">📅 Jadwal MCU</a>
                    <a href="{{ route('stmc.karyawan.index') }}" class="block px-3 py-2 text-[12.5px] font-medium {{ request()->routeIs('stmc.karyawan.*') ? 'text-[#F5333F] font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }} hover:bg-gray-50 dark:hover:bg-gray-800/40 rounded-lg transition-colors">👥 Data Karyawan & Pasien</a>
                    <a href="{{ route('stmc.scan.qr') }}" class="block px-3 py-2 text-[12.5px] font-medium {{ request()->routeIs('stmc.scan.qr') ? 'text-[#F5333F] font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }} hover:bg-gray-50 dark:hover:bg-gray-800/40 rounded-lg transition-colors">📷 Scan Kehadiran QR</a>
                    <a href="{{ route('stmc.laporan.pemeriksaan') }}" class="block px-3 py-2 text-[12.5px] font-medium {{ request()->routeIs('stmc.laporan.*') ? 'text-[#F5333F] font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }} hover:bg-gray-50 dark:hover:bg-gray-800/40 rounded-lg transition-colors">📊 Rekam Medis & Laporan</a>
                    <a href="{{ route('stmc.lingkungan.index') }}" class="block px-3 py-2 text-[12.5px] font-medium {{ request()->routeIs('stmc.lingkungan.*') ? 'text-[#F5333F] font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }} hover:bg-gray-50 dark:hover:bg-gray-800/40 rounded-lg transition-colors">🌱 Pemantauan Lingkungan</a>
                    <a href="{{ route('stmc.paket-poli') }}" class="block px-3 py-2 text-[12.5px] font-medium {{ request()->routeIs('stmc.paket-poli') ? 'text-[#F5333F] font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }} hover:bg-gray-50 dark:hover:bg-gray-800/40 rounded-lg transition-colors">🏥 Kelola Paket Poli</a>
                </div>
            </div>
        @endhasanyrole

    </nav>

    <!-- ========================================== -->
    <!-- PANEL PROFIL BAWAH DENGAN MENU DROP-UP -->
    <!-- ========================================== -->
    <div class="relative px-4 pb-4 mt-auto" x-data="{ openProfileMenu: false }">
        
        <!-- Garis pembatas halus -->
        <div class="h-px bg-gray-200 dark:bg-gray-800 mb-4 mx-2"></div>

        <!-- Menu Pop-up (Drop-up) -->
        <div x-show="openProfileMenu" 
            @click.away="openProfileMenu = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 scale-95"
            :class="(sidebarOpen || window.innerWidth < 1024) ? 'bottom-full left-3 right-3 mb-3 origin-bottom' : 'bottom-2 left-full ml-3 w-64 origin-bottom-left shadow-2xl'"
            class="absolute bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800 rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] dark:shadow-2xl overflow-hidden z-50 transform"
            style="display: none;">
            
            <div class="p-5 border-b border-gray-50 dark:border-gray-800/60 flex items-center gap-3 bg-gray-50/50 dark:bg-[#1A1A1A]/50">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-9 h-9 rounded-xl object-cover shadow-sm border border-gray-200 dark:border-gray-700 shrink-0">
                @else
                    <div class="flex items-center justify-center text-sm font-bold text-white rounded-xl shadow-sm w-9 h-9 bg-[#F5333F] shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex flex-col min-w-0">
                    <span class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</span>
                    <span class="text-xs font-semibold text-[#F5333F] dark:text-red-400 truncate">{{ Auth::user()->roles->first()->name ?? 'User' }}</span>
                </div>
            </div>

            <div class="p-2.5 space-y-1">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13.5px] font-medium text-gray-600 transition-colors rounded-xl dark:text-gray-400 hover:bg-red-50 hover:text-[#F5333F] dark:hover:bg-gray-800 dark:hover:text-white">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path></svg>
                    Profil
                </a>

                @hasanyrole('Developer|Super Admin|')
                <a href="{{ route('accounts.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13.5px] font-medium text-gray-600 transition-colors rounded-xl dark:text-gray-400 hover:bg-red-50 hover:text-[#F5333F] dark:hover:bg-gray-800 dark:hover:text-white">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path></svg>
                    Manajemen Akun
                </a>

                <!-- TAMBAHKAN MENU LOG AKTIVITAS INI -->
                <a href="{{ route('activity-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13.5px] font-medium text-gray-600 transition-colors rounded-xl dark:text-gray-400 hover:bg-red-50 hover:text-[#F5333F] dark:hover:bg-gray-800 dark:hover:text-white">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Log Aktivitas
                </a>
                @endhasanyrole

                <!-- DEV, SUPER ADMIN, DAN ADMIN BOLEH MELIHAT INI -->
                @hasanyrole('Developer|Super Admin|Admin')
                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13.5px] font-medium text-gray-600 transition-colors rounded-xl dark:text-gray-400 hover:bg-red-50 hover:text-[#F5333F] dark:hover:bg-gray-800 dark:hover:text-white">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281zM12 15a3 3 0 100-6 3 3 0 000 6z"></path></svg>
                    Pengaturan
                </a>
                @endhasanyrole

                <hr class="my-2 border-gray-100 dark:border-gray-800">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full gap-3 px-3 py-2.5 text-[13.5px] font-medium text-red-600 transition-colors rounded-xl hover:bg-red-50 dark:text-red-500 dark:hover:bg-red-500/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"></path></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        <!-- Tombol Pemicu Panel Profil -->
        <button @click="openProfileMenu = !openProfileMenu" 
            :class="(sidebarOpen || window.innerWidth < 1024) ? 'justify-between px-3' : 'justify-center px-1'"
            class="flex items-center w-full py-2.5 transition-all duration-200 rounded-2xl hover:bg-gray-50 dark:hover:bg-[#1A1A1A] focus:outline-none border border-transparent hover:border-gray-100 dark:hover:border-gray-800/60">
            <div class="flex items-center gap-3 overflow-hidden">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-9 h-9 rounded-xl object-cover shadow-sm border border-gray-200 dark:border-gray-700 shrink-0">
                @else
                    <div class="flex items-center justify-center text-sm font-bold text-white rounded-xl shadow-sm w-9 h-9 bg-[#F5333F] shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
                <div x-show="sidebarOpen || window.innerWidth < 1024" class="flex flex-col text-left truncate">
                    <span class="text-[13.5px] font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</span>
                    <!-- Label Role -->
                    <span class="inline-block mt-0.5 text-[11px] font-semibold text-gray-500 dark:text-gray-400 w-max">
                        {{ Auth::user()->roles->first()->name ?? 'User' }}
                    </span>
                </div>
            </div>
            <svg x-show="sidebarOpen || window.innerWidth < 1024" :class="{'rotate-180': openProfileMenu}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"></path></svg>
        </button>
    </div>

</aside>