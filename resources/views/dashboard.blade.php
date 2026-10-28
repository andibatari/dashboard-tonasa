<x-app-layout>
    <x-slot name="header">
        @if($viewMode === 'employee')
            <span class="sm:hidden">Portal Karyawan</span>
            <span class="hidden sm:inline">Beranda Portal Karyawan</span>
        @else
            <span class="sm:hidden">Dashboard HC &amp; GRC</span>
            <span class="hidden sm:inline">Dashboard Internal Dept. HC &amp; GRC</span>
        @endif
    </x-slot>

    {{-- ======================================================== --}}
    {{-- CSS ANIMASI UNIK KUSTOM                                  --}}
    {{-- ======================================================== --}}
    <style>
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-7px) rotate(1deg); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(7px) rotate(-1deg); }
        }
        @keyframes shimmer-slow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 0.9; transform: scale(1.04); }
        }
        @keyframes radar-ping {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 51, 63, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(245, 51, 63, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 51, 63, 0); }
        }

        .anim-float { animation: float-slow 6s ease-in-out infinite; }
        .anim-float-rev { animation: float-reverse 7s ease-in-out infinite; }
        .anim-shimmer { background-size: 250% 250%; animation: shimmer-slow 8s ease infinite; }
        .anim-pulse-glow { animation: pulse-glow 5s ease-in-out infinite; }
        .anim-radar { animation: radar-ping 2.5s infinite; }
        
        .glass-card {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>

    <div class="py-4 sm:py-8">
        <div class="w-full px-2 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

            {{-- ======================================================== --}}
            {{-- ALERT NOTIFIKASI SUCCESS / ERROR                         --}}
            {{-- ======================================================== --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center justify-between p-4 mb-4 text-sm font-semibold rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/60 dark:text-emerald-300">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="p-1 text-emerald-600 hover:text-emerald-900 dark:hover:text-emerald-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center justify-between p-4 mb-4 text-sm font-semibold rounded-2xl bg-red-50 text-red-800 border border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800/50 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-red-100 text-[#F5333F] dark:bg-red-900/60 dark:text-red-300">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="p-1 text-red-600 hover:text-red-900 dark:hover:text-red-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- VIEW SWITCHER (HANYA MUNCUL DI PRATINJAU KARYAWAN UMUM)  --}}
            {{-- ======================================================== --}}
            @if($isStaff && $viewMode === 'employee')
                <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs transition-all hover:border-slate-300">
                    <div class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 dark:bg-red-500/10 text-[#F5333F] anim-pulse-glow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </span>
                        <span>Mode Pratinjau Aktif: <strong class="text-slate-900 dark:text-white uppercase font-bold">Portal Karyawan Umum</strong></span>
                    </div>

                    <a href="{{ route('dashboard', ['view' => 'staff']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl transition-all shadow-xs hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path></svg>
                        Kembali ke Dashboard Internal HC
                    </a>
                </div>
            @endif


            {{-- ######################################################## --}}
            {{-- A. TAMPILAN KHUSUS KARYAWAN UMUM (EMPLOYEE PORTAL)       --}}
            {{-- ######################################################## --}}
            @if($viewMode === 'employee')

                <!-- 1. COMPACT EXECUTIVE HERO BANNER SAMBUTAN KARYAWAN -->
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white shadow-xl border border-slate-800/80 group">
                    <!-- Subtle Ambient Backlights (SIG Brand Harmony) -->
                    <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-[#F5333F]/15 blur-3xl pointer-events-none anim-float"></div>
                    <div class="absolute right-1/3 -bottom-24 w-72 h-72 rounded-full bg-red-600/10 blur-3xl pointer-events-none anim-float-rev"></div>
                    
                    <!-- Micro Dot Grid Overlay for Depth -->
                    <div class="absolute inset-0 bg-[radial-gradient(#ffffff0a_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

                    <div class="relative p-5 sm:p-6 lg:p-7 flex flex-col md:flex-row md:items-center justify-between gap-5 z-10">
                        <div class="flex items-center gap-4 sm:gap-5">
                            
                            <!-- Avatar dengan Squircle Double-Ring Glass -->
                            <div class="relative flex-shrink-0">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover ring-2 ring-white/20 shadow-xl transition-transform duration-300 group-hover:scale-105">
                                @else
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-slate-800 via-slate-900 to-slate-800 text-white font-headline font-black text-xl sm:text-2xl flex items-center justify-center ring-2 ring-white/20 shadow-xl transition-transform duration-300 group-hover:scale-105 border border-white/10">
                                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-rose-200">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                                <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-slate-950 flex items-center justify-center shadow-sm" title="Online & Terhubung">
                                    <span class="w-1 h-1 rounded-full bg-white animate-ping"></span>
                                </span>
                            </div>

                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-widest rounded-full bg-red-500/10 text-red-400 border border-red-500/25 shadow-xs backdrop-blur-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F5333F] animate-pulse"></span>
                                        Portal Karyawan Semen Tonasa
                                    </span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-white/5 text-slate-300 border border-white/10 backdrop-blur-sm">
                                        {{ $user->roles->first()->name ?? 'Karyawan' }}
                                    </span>
                                    <span class="hidden sm:inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full bg-white/5 text-slate-400 border border-white/5">
                                        Dept. HC & GRC • PT Semen Tonasa
                                    </span>
                                </div>
                                <h2 class="text-lg sm:text-2xl font-headline font-black tracking-tight text-white flex items-center gap-2">
                                    Selamat Datang, {{ $user->name }}!
                                </h2>
                                <p class="text-xs text-slate-300 flex flex-wrap items-center gap-x-3 gap-y-1 pt-0.5">
                                    <span class="inline-flex items-center gap-1.5 text-slate-200 font-medium">
                                        <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $user->unit?->name_unit ?? 'Unit Kerja Umum' }}
                                    </span>
                                    <span class="text-slate-600">•</span>
                                    <span class="inline-flex items-center gap-1.5 text-slate-400">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $user->email }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Right Side: Mini Status & Live Date Card -->
                        <div class="flex items-center md:items-end justify-between md:flex-col gap-2.5 border-t md:border-t-0 border-white/10 pt-3 md:pt-0">
                            <div class="text-left md:text-right">
                                <span class="text-[10px] font-medium text-slate-400 block uppercase tracking-wider">Kalender Kerja:</span>
                                <span class="text-xs sm:text-sm font-headline font-bold text-white flex items-center gap-1.5 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 px-2.5 py-1 rounded-xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-300 text-[10px] font-semibold backdrop-blur-sm">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span>Layanan Aktif &amp; Terverifikasi</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- STRUKTUR DUA KOLOM ASIMETRIS UTAMA (8 COLS + 4 COLS)     --}}
                {{-- ======================================================== --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    {{-- #################################################### --}}
                    {{-- KOLOM KIRI (MAIN TRACK - 8 COLS): KONTEN UTAMA INOVASI --}}
                    {{-- #################################################### --}}
                    <div class="lg:col-span-8 space-y-6">

                        <!-- 2. KARTU UTAMA: PROGRAM UNGGULAN INOVASI (KMST 2026 - 2027) -->
                        <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-md transition-all duration-300 group">
                            <!-- Accent Line At Top -->
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#F5333F] to-transparent"></div>

                            <div class="p-5 sm:p-7 space-y-5">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                    
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <span class="flex items-center justify-center w-10 h-10 rounded-2xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 dark:text-red-400 shadow-xs group-hover:scale-105 transition-transform border border-red-100 dark:border-red-900/30 flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.509 1.333 1.509 2.316V18"></path></svg>
                                            </span>
                                            <div>
                                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#F5333F] flex items-center gap-1.5">
                                                    <span>Program Unggulan Inovasi</span>
                                                    <span class="w-1 h-1 rounded-full bg-[#F5333F]"></span>
                                                    <span class="text-slate-400 dark:text-slate-500 font-bold">KMST 2026/2027</span>
                                                </span>
                                                <h3 class="text-lg sm:text-xl font-headline font-black text-slate-900 dark:text-white mt-0.5">
                                                    Konvensi Mutu Semen Tonasa (KMST) 2026 - 2027
                                                </h3>
                                            </div>
                                        </div>

                                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed pt-1">
                                            Salurkan gagasan dan inovasi terbaik Anda untuk mewujudkan efisiensi biaya, produktivitas optimal, dan keselamatan kerja di PT Semen Tonasa. Terbuka dalam <strong>5 Kategori Resmi</strong>:
                                        </p>
                                    </div>

                                    <!-- Tombol Aksi Utama KMST -->
                                    <div class="flex flex-row sm:flex-col gap-2.5 flex-shrink-0 w-full sm:w-auto">
                                        <a href="{{ route('inovasi.portal') }}" class="group/btn relative flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-3 text-xs sm:text-sm font-headline font-bold text-white transition-all duration-300 rounded-2xl bg-gradient-to-r from-[#F5333F] via-[#E02430] to-[#C81E2B] shadow-md shadow-red-500/20 hover:shadow-lg hover:shadow-red-500/30 hover:-translate-y-0.5 active:translate-y-0">
                                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            <span>Buka Portal Inovasi</span>
                                            <svg class="w-3.5 h-3.5 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                        </a>

                                        <a href="{{ route('inovasi.portal') }}#daftar" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-xs font-bold rounded-xl text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700/80 border border-slate-200/70 dark:border-slate-700/70 transition hover:-translate-y-0.5 active:translate-y-0">
                                            <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                                            Formulir Pendaftaran
                                        </a>
                                    </div>

                                </div>

                                <!-- 5 KATEGORI KMST RESMI DENGAN DESAIN UNIFIED & ELEGAN -->
                                <div class="flex flex-wrap items-center gap-2 pt-1">
                                    <!-- 1. SS OFFICE -->
                                    <div class="group/pill flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/60 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/70 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-all hover:border-slate-300 dark:hover:border-slate-600 shadow-2xs">
                                        <span class="px-1.5 py-0.5 text-[9px] font-mono font-black rounded-md bg-slate-200/80 dark:bg-slate-700 text-slate-700 dark:text-slate-300">01</span>
                                        <span>SS (Office)</span>
                                    </div>

                                    <!-- 2. SS PLANT -->
                                    <div class="group/pill flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/60 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/70 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-all hover:border-slate-300 dark:hover:border-slate-600 shadow-2xs">
                                        <span class="px-1.5 py-0.5 text-[9px] font-mono font-black rounded-md bg-slate-200/80 dark:bg-slate-700 text-slate-700 dark:text-slate-300">02</span>
                                        <span>SS (Plant)</span>
                                    </div>

                                    <!-- 3. GKM -->
                                    <div class="group/pill flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/60 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/70 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-all hover:border-slate-300 dark:hover:border-slate-600 shadow-2xs">
                                        <span class="px-1.5 py-0.5 text-[9px] font-mono font-black rounded-md bg-slate-200/80 dark:bg-slate-700 text-slate-700 dark:text-slate-300">03</span>
                                        <span>GKM (Gugus Mutu)</span>
                                    </div>

                                    <!-- 4. PKM -->
                                    <div class="group/pill flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/60 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/70 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-all hover:border-slate-300 dark:hover:border-slate-600 shadow-2xs">
                                        <span class="px-1.5 py-0.5 text-[9px] font-mono font-black rounded-md bg-slate-200/80 dark:bg-slate-700 text-slate-700 dark:text-slate-300">04</span>
                                        <span>PKM (Proyek Mutu)</span>
                                    </div>

                                    <!-- 5. BI -->
                                    <div class="group/pill flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/60 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/70 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-all hover:border-slate-300 dark:hover:border-slate-600 shadow-2xs">
                                        <span class="px-1.5 py-0.5 text-[9px] font-mono font-black rounded-md bg-red-100 dark:bg-red-950/60 text-[#F5333F] dark:text-red-300">05</span>
                                        <span>BI (Breakthrough)</span>
                                    </div>
                                </div>

                                {{-- Status Partisipasi Karyawan --}}
                                @if($userKmstRegistration)
                                    <div class="p-3.5 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 flex items-start gap-3 text-emerald-900 dark:text-emerald-200 shadow-xs">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/60 dark:text-emerald-300 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div class="text-xs space-y-0.5">
                                            <p class="font-headline font-bold text-xs sm:text-sm text-emerald-800 dark:text-emerald-300">Status Pendaftaran: Anda Sudah Terdaftar di KMST!</p>
                                            <p class="text-emerald-700 dark:text-emerald-300">Tim: <strong class="text-emerald-950 dark:text-white">{{ $userKmstRegistration->nama_tim }}</strong> • Kategori: <strong class="px-1.5 py-0.5 rounded-md bg-emerald-200/60 dark:bg-emerald-900/60 text-emerald-900 dark:text-emerald-200">{{ $userKmstRegistration->kategori }}</strong></p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2.5 text-xs pt-1">
                                        @if($isKmstActive)
                                            <span class="inline-flex items-center gap-2 px-3 py-1 font-bold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-2xs">
                                                <span class="relative flex h-2 w-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                                </span>
                                                Pendaftaran Sedang Dibuka
                                            </span>
                                            <span class="text-slate-500 dark:text-slate-400 text-xs">Siapkan tim inovasi Anda dan unduh template resmi di bawah.</span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 font-bold rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Pendaftaran Sedang Ditutup
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- 3. RESOURCE HUB: PUSAT UNDUHAN TEMPLATE RESMI (5 KATEGORI) -->
                        <div class="p-5 sm:p-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-3.5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Template Resmi Makalah (5 Kategori):
                                </span>
                                <a href="{{ route('inovasi.portal') }}" class="text-xs font-bold text-[#F5333F] hover:underline flex items-center gap-1 self-start sm:self-auto">
                                    <span>Panduan &amp; Format KMST</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                                <!-- 1. SS Office -->
                                <a href="{{ route('inovasi.download-template', ['kategori' => 'SS_Office']) }}" class="group flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50/80 hover:bg-white dark:bg-slate-800/40 dark:hover:bg-slate-800 border border-slate-200/80 hover:border-red-300 dark:border-slate-800 dark:hover:border-red-900/40 shadow-2xs hover:shadow-sm transition-all hover:-translate-y-0.5">
                                    <span class="w-7 h-7 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 dark:text-red-400 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-[#F5333F] transition-colors">SS (Office)</span>
                                        <span class="text-[9px] text-slate-400 block truncate font-medium">Format .docx</span>
                                    </div>
                                    <svg class="w-3 h-3 text-slate-300 group-hover:text-[#F5333F] transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                </a>

                                <!-- 2. SS Plant -->
                                <a href="{{ route('inovasi.download-template', ['kategori' => 'SS_Plant']) }}" class="group flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50/80 hover:bg-white dark:bg-slate-800/40 dark:hover:bg-slate-800 border border-slate-200/80 hover:border-red-300 dark:border-slate-800 dark:hover:border-red-900/40 shadow-2xs hover:shadow-sm transition-all hover:-translate-y-0.5">
                                    <span class="w-7 h-7 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 dark:text-red-400 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-[#F5333F] transition-colors">SS (Plant)</span>
                                        <span class="text-[9px] text-slate-400 block truncate font-medium">Format .docx</span>
                                    </div>
                                    <svg class="w-3 h-3 text-slate-300 group-hover:text-[#F5333F] transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                </a>

                                <!-- 3. GKM -->
                                <a href="{{ route('inovasi.download-template', ['kategori' => 'GKM']) }}" class="group flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50/80 hover:bg-white dark:bg-slate-800/40 dark:hover:bg-slate-800 border border-slate-200/80 hover:border-red-300 dark:border-slate-800 dark:hover:border-red-900/40 shadow-2xs hover:shadow-sm transition-all hover:-translate-y-0.5">
                                    <span class="w-7 h-7 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 dark:text-red-400 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-[#F5333F] transition-colors">GKM</span>
                                        <span class="text-[9px] text-slate-400 block truncate font-medium">Gugus Mutu</span>
                                    </div>
                                    <svg class="w-3 h-3 text-slate-300 group-hover:text-[#F5333F] transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                </a>

                                <!-- 4. PKM -->
                                <a href="{{ route('inovasi.download-template', ['kategori' => 'PKM']) }}" class="group flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50/80 hover:bg-white dark:bg-slate-800/40 dark:hover:bg-slate-800 border border-slate-200/80 hover:border-red-300 dark:border-slate-800 dark:hover:border-red-900/40 shadow-2xs hover:shadow-sm transition-all hover:-translate-y-0.5">
                                    <span class="w-7 h-7 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 dark:text-red-400 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-[#F5333F] transition-colors">PKM</span>
                                        <span class="text-[9px] text-slate-400 block truncate font-medium">Proyek Mutu</span>
                                    </div>
                                    <svg class="w-3 h-3 text-slate-300 group-hover:text-[#F5333F] transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                </a>

                                <!-- 5. BI -->
                                <a href="{{ route('inovasi.download-template', ['kategori' => 'BI']) }}" class="group flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50/80 hover:bg-white dark:bg-slate-800/40 dark:hover:bg-slate-800 border border-slate-200/80 hover:border-red-300 dark:border-slate-800 dark:hover:border-red-900/40 shadow-2xs hover:shadow-sm transition-all hover:-translate-y-0.5 col-span-2 sm:col-span-1">
                                    <span class="w-7 h-7 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 dark:text-red-400 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-[#F5333F] transition-colors">BI</span>
                                        <span class="text-[9px] text-slate-400 block truncate font-medium">Breakthrough</span>
                                    </div>
                                    <svg class="w-3 h-3 text-slate-300 group-hover:text-[#F5333F] transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- 4. HALL OF FAME: GALERI PRESTASI JUARA INOVASI (2X2 GRID ELEGANT SHOWCASE) -->
                        <div class="space-y-3.5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded-full bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-xs">
                                            <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 5.6L20 10l-4.2 3.8 1.2 5.8-5-3-5 3 1.2-5.8L4 10l5.6-2.4L12 2z"/></svg>
                                            Hall of Fame Inovasi
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">• Prestasi Nasional &amp; Internasional</span>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-headline font-black text-slate-900 dark:text-white mt-1">
                                        Galeri Prestasi Juara Inovasi Semen Tonasa
                                    </h3>
                                </div>
                                <a href="{{ route('inovasi.portal') }}#galeri" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold rounded-xl text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 border border-slate-200/80 dark:border-slate-700 shadow-2xs transition-all hover:scale-[1.02] active:scale-[0.98] self-start sm:self-auto">
                                    <span>Jelajahi Portal Inovasi</span>
                                    <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>

                            <!-- 4 Kartu Galeri Foto Juara (2x2 Grid Layout Sempurna untuk 8 Kolom) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                
                                <!-- CARD 1: BI (TKMPN Bali) -->
                                <div class="group relative flex flex-col justify-between bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                                    <div>
                                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-950">
                                            <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Juara 1 TKMPN Bali" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                                            <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg backdrop-blur-md bg-slate-950/75 border border-white/15 text-white uppercase tracking-wider shadow-md">
                                                <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 5.6L20 10l-4.2 3.8 1.2 5.8-5-3-5 3 1.2-5.8L4 10l5.6-2.4L12 2z"/></svg>
                                                Juara 1 TKMPN
                                            </span>
                                            <span class="absolute bottom-2.5 right-3 text-[10px] font-bold text-slate-300 bg-slate-900/80 backdrop-blur-sm px-2 py-0.5 rounded border border-white/10">
                                                Pentas Nasional
                                            </span>
                                        </div>
                                        <div class="p-4 space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="px-2 py-0.5 text-[9px] font-black rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 uppercase border border-slate-200/60 dark:border-slate-700/60">
                                                    BI (Breakthrough)
                                                </span>
                                                <span class="text-[10px] text-slate-400 font-medium">Tingkat Nasional</span>
                                            </div>
                                            <h4 class="text-sm font-headline font-bold text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors line-clamp-2 leading-snug">
                                                Optimalisasi Raw Mill &amp; Green Clinker
                                            </h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                                Inovasi pembakaran alternatif ramah lingkungan dan reduksi emisi thermal pabrik.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="p-4 pt-0">
                                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                                            <span class="text-slate-500 dark:text-slate-400 truncate max-w-[120px] font-medium">Tim Breakthrough</span>
                                            <span class="font-extrabold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-slate-700/60">💰 Rp 2.4 Miliar</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- CARD 2: GKM (ICQCC Internasional) -->
                                <div class="group relative flex flex-col justify-between bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                                    <div>
                                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-950">
                                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Platinum Award ICQCC" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                                            <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg backdrop-blur-md bg-slate-950/75 border border-white/15 text-white uppercase tracking-wider shadow-md">
                                                <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 5.6L20 10l-4.2 3.8 1.2 5.8-5-3-5 3 1.2-5.8L4 10l5.6-2.4L12 2z"/></svg>
                                                Platinum ICQCC
                                            </span>
                                            <span class="absolute bottom-2.5 right-3 text-[10px] font-bold text-slate-300 bg-slate-900/80 backdrop-blur-sm px-2 py-0.5 rounded border border-white/10">
                                                Pentas Dunia
                                            </span>
                                        </div>
                                        <div class="p-4 space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="px-2 py-0.5 text-[9px] font-black rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 uppercase border border-slate-200/60 dark:border-slate-700/60">
                                                    GKM (Gugus Mutu)
                                                </span>
                                                <span class="text-[10px] text-slate-400 font-medium">Tingkat Dunia</span>
                                            </div>
                                            <h4 class="text-sm font-headline font-bold text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors line-clamp-2 leading-snug">
                                                Gugus Mutu Klinker Tonasa Preheater
                                            </h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                                Peningkatan keandalan siklon kalsiner dan kontrol kestabilan klinker ekspor.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="p-4 pt-0">
                                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                                            <span class="text-slate-500 dark:text-slate-400 truncate max-w-[120px] font-medium">GKM Klinker Unggul</span>
                                            <span class="font-extrabold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-slate-700/60">⚡ -48 Jam Stop</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- CARD 3: PKM (Temu Karya SIG) -->
                                <div class="group relative flex flex-col justify-between bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                                    <div>
                                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-950">
                                            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Inovasi Efisiensi Energi" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                                            <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg backdrop-blur-md bg-slate-950/75 border border-white/15 text-white uppercase tracking-wider shadow-md">
                                                <svg class="w-3 h-3 text-amber-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 5.6L20 10l-4.2 3.8 1.2 5.8-5-3-5 3 1.2-5.8L4 10l5.6-2.4L12 2z"/></svg>
                                                Gold Winner SIG
                                            </span>
                                            <span class="absolute bottom-2.5 right-3 text-[10px] font-bold text-slate-300 bg-slate-900/80 backdrop-blur-sm px-2 py-0.5 rounded border border-white/10">
                                                Holding SIG
                                            </span>
                                        </div>
                                        <div class="p-4 space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="px-2 py-0.5 text-[9px] font-black rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 uppercase border border-slate-200/60 dark:border-slate-700/60">
                                                    PKM (Proyek Mutu)
                                                </span>
                                                <span class="text-[10px] text-slate-400 font-medium">Grup Semen</span>
                                            </div>
                                            <h4 class="text-sm font-headline font-bold text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors line-clamp-2 leading-snug">
                                                Digital Blending Limestone IoT Tambang
                                            </h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                                Sensor optik dan tracking armada tambang untuk konsistensi komposisi feed kiln.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="p-4 pt-0">
                                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                                            <span class="text-slate-500 dark:text-slate-400 truncate max-w-[120px] font-medium">Tim PKM Tambang</span>
                                            <span class="font-extrabold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-slate-700/60">🎯 Akurasi 99.2%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- CARD 4: SS PLANT (Best SS Tonasa) -->
                                <div class="group relative flex flex-col justify-between bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                                    <div>
                                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-950">
                                            <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Best SS Plant Tonasa" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                                            <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg backdrop-blur-md bg-slate-950/75 border border-white/15 text-white uppercase tracking-wider shadow-md">
                                                <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 5.6L20 10l-4.2 3.8 1.2 5.8-5-3-5 3 1.2-5.8L4 10l5.6-2.4L12 2z"/></svg>
                                                Best SS Plant
                                            </span>
                                            <span class="absolute bottom-2.5 right-3 text-[10px] font-bold text-slate-300 bg-slate-900/80 backdrop-blur-sm px-2 py-0.5 rounded border border-white/10">
                                                Internal Tonasa
                                            </span>
                                        </div>
                                        <div class="p-4 space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="px-2 py-0.5 text-[9px] font-black rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 uppercase border border-slate-200/60 dark:border-slate-700/60">
                                                    SS (Plant)
                                                </span>
                                                <span class="text-[10px] text-slate-400 font-medium">Pabrik Tonasa</span>
                                            </div>
                                            <h4 class="text-sm font-headline font-bold text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors line-clamp-2 leading-snug">
                                                Quick-Release Silo Nozzle Pack
                                            </h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                                Rancang bangun nozzle fleksibel mempercepat proses loading armada curah dan packing.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="p-4 pt-0">
                                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                                            <span class="text-slate-500 dark:text-slate-400 truncate max-w-[120px] font-medium">Inovator Distribusi</span>
                                            <span class="font-extrabold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-slate-700/60">⏱️ Zero Bottleneck</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- 5. KATALOG MENU LAYANAN KARYAWAN (BENTO GRID 2X2 TERPADU) -->
                        <div class="space-y-3.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-headline font-black text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#F5333F]"></span>
                                    Katalog Layanan &amp; Informasi Karyawan
                                </h3>
                                <span class="text-xs text-slate-400 font-medium">Akses Cepat Fasilitas Insan Tonasa</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                
                                <!-- CARD 1: KMST INOVASI -->
                                <div class="flex flex-col justify-between p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-1 transition-all duration-300 group">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="flex items-center justify-center w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200 group-hover:bg-red-50 group-hover:text-[#F5333F] dark:group-hover:bg-red-500/10 dark:group-hover:text-red-400 transition-all duration-300 shadow-2xs">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                            </span>
                                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700/60">
                                                5 Kategori
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm sm:text-base font-headline font-bold text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors">Inovasi KMST</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">SS (Office), SS (Plant), GKM, PKM, dan BI. Pendaftaran tim, template makalah, dan galeri pemenang.</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('inovasi.portal') }}" class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-[#F5333F] group-hover:translate-x-1 transition-transform">
                                        <span>Buka Portal Inovasi</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>

                                <!-- CARD 2: PENGENDALIAN & TATA KELOLA (ICoFR) -->
                                <div class="flex flex-col justify-between p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-1 transition-all duration-300 group">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="flex items-center justify-center w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200 group-hover:bg-red-50 group-hover:text-[#F5333F] dark:group-hover:bg-red-500/10 dark:group-hover:text-red-400 transition-all duration-300 shadow-2xs">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                            </span>
                                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700/60">
                                                Tata Kelola
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm sm:text-base font-headline font-bold text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors">Tata Kelola &amp; Kepatuhan</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">Pengendalian internal proses bisnis (ICoFR), manajemen risiko terpadu, dan kepatuhan insan Semen Tonasa.</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300 group-hover:translate-x-1 transition-transform">
                                        <span>Kepatuhan SIG &amp; BUMN</span>
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>

                                <!-- CARD 3: LAYANAN KESEHATAN & KLINIK -->
                                <div class="flex flex-col justify-between p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-1 transition-all duration-300 group">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="flex items-center justify-center w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200 group-hover:bg-red-50 group-hover:text-[#F5333F] dark:group-hover:bg-red-500/10 dark:group-hover:text-red-400 transition-all duration-300 shadow-2xs">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                            </span>
                                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700/60">
                                                Fasilitas
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm sm:text-base font-headline font-bold text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors">Kesehatan &amp; Klinik</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">Pelayanan kesehatan kerja, jadwal dokter klinik Tonasa, medical check-up (MCU), dan fasilitas hygiene pabrik.</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300 group-hover:translate-x-1 transition-transform">
                                        <span>Klinik Biringere 24 Jam</span>
                                        <svg class="w-4 h-4 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                </div>

                                <!-- CARD 4: BUDAYA AKHLAK & TALENTA -->
                                <div class="flex flex-col justify-between p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-1 transition-all duration-300 group">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="flex items-center justify-center w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200 group-hover:bg-red-50 group-hover:text-[#F5333F] dark:group-hover:bg-red-500/10 dark:group-hover:text-red-400 transition-all duration-300 shadow-2xs">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            </span>
                                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700/60">
                                                Budaya
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm sm:text-base font-headline font-bold text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors">Budaya AKHLAK</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">Nilai inti BUMN (Amanah, Kompeten, Harmonis, Loyal, Adaptif, Kolaboratif) insan Semen Tonasa.</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300 group-hover:translate-x-1 transition-transform">
                                        <span>Pilar Budaya Kerja</span>
                                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- #################################################### --}}
                    {{-- KOLOM KANAN (SIDEBAR INTELLIGENCE - 4 COLS): WIDGETS --}}
                    {{-- #################################################### --}}
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">

                        <!-- WIDGET 1: KARTU IDENTITAS KARYAWAN (EXECUTIVE DIGITAL PASS) -->
                        <div class="p-5 sm:p-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-slate-800 via-[#F5333F] to-slate-800"></div>
                            
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5 mb-4">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Kartu Identitas Karyawan
                                </span>
                                <span class="text-[9px] font-mono font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                                    SIG • TONASA
                                </span>
                            </div>

                            <div class="flex items-center gap-3.5">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 dark:ring-slate-800 shadow-xs flex-shrink-0">
                                @else
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-800 text-white font-headline font-black text-lg flex items-center justify-center ring-2 ring-slate-100 dark:ring-slate-800 shadow-xs flex-shrink-0 border border-white/10">
                                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-rose-200">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h4 class="text-sm font-headline font-black text-slate-900 dark:text-white truncate">
                                        {{ $user->name }}
                                    </h4>
                                    <span class="text-[11px] font-bold text-[#F5333F] block truncate mt-0.5">
                                        {{ $user->roles->first()->name ?? 'Karyawan' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block truncate">
                                        {{ $user->unit?->name_unit ?? 'Unit Kerja Umum' }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-[11px]">
                                <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                    <span class="text-[10px] text-slate-400 block">Status:</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 mt-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                    <span class="text-[10px] text-slate-400 block">Departemen:</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 truncate block mt-0.5">
                                        {{ $user->unit?->department?->name_department ?? 'PT Semen Tonasa' }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="mt-3.5 w-full py-2 px-3 rounded-xl text-center text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 transition block border border-slate-200/60 dark:border-slate-700/60">
                                Kelola Profil &amp; Akun
                            </a>
                        </div>

                        <!-- WIDGET 2: BANNER SIAP JADI BINTANG INOVASI (QUICK CTA) -->
                        <div class="p-5 rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white border border-slate-800 shadow-md relative overflow-hidden group">
                            <div class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-[#F5333F]/20 blur-2xl pointer-events-none"></div>
                            
                            <div class="relative z-10 space-y-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-red-500/20 text-[#F5333F] border border-red-500/30 flex-shrink-0 text-sm">
                                        🌟
                                    </span>
                                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-red-400">
                                        Pentas Mutu 2026/2027
                                    </span>
                                </div>

                                <h4 class="text-sm font-headline font-black text-white leading-snug">
                                    Siap Menjadi Bintang Inovasi Berikutnya?
                                </h4>

                                <p class="text-xs text-slate-400 leading-relaxed">
                                    Daftarkan ide tim Anda sekarang dan raih penghargaan bergengsi tingkat nasional hingga global.
                                </p>

                                <a href="{{ route('inovasi.portal') }}#daftar" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 text-xs font-headline font-bold text-white rounded-xl bg-gradient-to-r from-[#F5333F] to-rose-600 hover:from-rose-600 hover:to-[#F5333F] shadow-sm shadow-red-500/25 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                    <span>Daftar Tim KMST Sekarang</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- WIDGET 3: BULETIN INFORMASI & PENGUMUMAN RESMI (AGENDA FEED) -->
                        <div class="p-5 sm:p-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-3.5">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                                <h3 class="text-xs font-headline font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-red-50 text-[#F5333F] dark:bg-red-500/10">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                    </span>
                                    Pengumuman Terkini
                                </h3>
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">Update 2026</span>
                            </div>

                            <div class="space-y-2.5">
                                <!-- Feed Item 1: KMST -->
                                <div class="p-3 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800 space-y-1.5 hover:bg-white dark:hover:bg-slate-800/80 transition-all shadow-2xs">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="px-2 py-0.5 text-[9px] font-black rounded-md bg-[#F5333F] text-white">KMST 2026</span>
                                        <span class="text-[10px] text-slate-400">Periode Baru</span>
                                    </div>
                                    <h4 class="text-xs font-headline font-bold text-slate-900 dark:text-white leading-snug">
                                        Pendaftaran Karya Inovasi Mutu Terbuka 5 Kategori
                                    </h4>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Kategori SS (Office &amp; Plant), GKM, PKM, dan BI resmi dibuka. Manfaatkan template makalah resmi.
                                    </p>
                                </div>

                                <!-- Feed Item 2: ICoFR -->
                                <div class="p-3 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800 space-y-1.5 hover:bg-white dark:hover:bg-slate-800/80 transition-all shadow-2xs">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="px-2 py-0.5 text-[9px] font-black rounded-md bg-slate-900 text-white dark:bg-slate-700">ICoFR</span>
                                        <span class="text-[10px] text-slate-400">Kepatuhan</span>
                                    </div>
                                    <h4 class="text-xs font-headline font-bold text-slate-900 dark:text-white leading-snug">
                                        Implementasi Pengendalian Internal Berjalan Efektif
                                    </h4>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Matriks Risiko &amp; Kontrol (RCM) dan evaluasi CSA Triwulan disinkronkan secara real-time.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- WIDGET 4: PUSAT BANTUAN & FAST CONCIERGE HC & GRC -->
                        <div class="p-5 sm:p-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-3.5">
                            <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                                <span class="flex items-center justify-center w-7 h-7 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                <div>
                                    <h3 class="text-xs font-headline font-bold text-slate-900 dark:text-white">Pusat Bantuan HC &amp; GRC</h3>
                                    <span class="text-[10px] text-slate-400 block">Layanan Konsultasi &amp; Helpdesk</span>
                                </div>
                            </div>

                            <div class="space-y-2 text-xs">
                                <a href="tel:1234" class="flex items-center gap-2.5 p-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors group">
                                    <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 group-hover:text-[#F5333F] transition-colors flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </span>
                                    <span class="truncate">Ext. Telepon: <strong class="text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors">1234 / 5678</strong></span>
                                </a>
                                
                                <a href="mailto:hc.service@sementonasa.co.id" class="flex items-center gap-2.5 p-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors group">
                                    <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 group-hover:text-[#F5333F] transition-colors flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </span>
                                    <span class="truncate">Email: <strong class="text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors">hc.service@sementonasa.co.id</strong></span>
                                </a>

                                <div class="flex items-center gap-2.5 p-2 rounded-xl text-slate-700 dark:text-slate-300">
                                    <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </span>
                                    <span class="text-[11px] truncate">Gedung Kantor Pusat PT Semen Tonasa</span>
                                </div>
                            </div>
                        </div>

                        <!-- WIDGET 5: NILAI INTI BUDAYA KERJA AKHLAK -->
                        <div class="p-4 sm:p-5 bg-slate-50/80 dark:bg-slate-800/40 rounded-3xl border border-slate-200/80 dark:border-slate-800 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Pilar Budaya Kerja BUMN
                                </span>
                                <span class="text-[10px] font-bold text-[#F5333F]">AKHLAK</span>
                            </div>
                            <div class="flex flex-wrap gap-1 text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                <span class="px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700">Amanah</span>
                                <span class="px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700">Kompeten</span>
                                <span class="px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700">Harmonis</span>
                                <span class="px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700">Loyal</span>
                                <span class="px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700">Adaptif</span>
                                <span class="px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700">Kolaboratif</span>
                            </div>
                        </div>

                    </div>

                </div>

            {{-- ######################################################## --}}
            {{-- B. TAMPILAN KHUSUS STAF DEPT. HC & GRC (OPERATIONAL)     --}}
            {{-- ######################################################## --}}
            @else

                <!-- HEADER ADMIN / STAF HC (EXECUTIVE PROFILE BANNER) -->
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-850 to-slate-950 text-white shadow-xl border border-slate-800 p-4 sm:p-6 lg:p-7 group">
                    <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-[#F5333F]/20 blur-3xl pointer-events-none anim-float"></div>
                    <div class="absolute right-1/4 -bottom-16 w-64 h-64 rounded-full bg-amber-500/10 blur-3xl pointer-events-none anim-float-rev"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6">
                        <div class="flex items-center gap-3.5 sm:gap-5">
                            <!-- Unique Dept. HC & GRC Department Avatar Badge -->
                            <div class="relative flex-shrink-0">
                                <div class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#990000] via-[#D31027] to-[#F5333F] p-0.5 ring-4 ring-white/10 shadow-2xl shadow-red-950/60 flex items-center justify-center overflow-hidden group-hover:scale-105 transition-all duration-300 select-none">
                                    <!-- Ambient Glass Glow & Diagonal Sheen -->
                                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/25 pointer-events-none"></div>
                                    <div class="absolute -top-6 -right-6 w-12 h-12 bg-white/20 rounded-full blur-md pointer-events-none"></div>

                                    <!-- Department HC & GRC Stylized Insignia -->
                                    <div class="relative z-10 flex flex-col items-center justify-center text-center leading-none">
                                        <div class="flex items-center justify-center gap-0.5 mb-0.5">
                                            <svg class="w-3 h-3 text-amber-300 drop-shadow" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l2.4 5.6L20 10l-4.2 3.8 1.2 5.8-5-3-5 3 1.2-5.8L4 10l5.6-2.4L12 2z"/>
                                            </svg>
                                            <span class="text-[7.5px] font-black tracking-widest text-red-100 uppercase font-mono">DEPT</span>
                                        </div>
                                        <span class="text-xl sm:text-2xl font-black text-white tracking-tighter drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)]">
                                            HC
                                        </span>
                                    </div>
                                </div>
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 sm:w-4.5 sm:h-4.5 rounded-full bg-emerald-500 border-2 border-slate-900 flex items-center justify-center shadow-md" title="HC Staff Aktif">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                </span>
                            </div>

                            <div class="min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[9px] sm:text-[10px] font-black uppercase tracking-wider rounded-md bg-red-500/20 text-red-300 border border-red-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        Dept. HC &amp; GRC
                                    </span>
                                    <span class="px-2 py-0.5 text-[9px] sm:text-[10px] font-bold rounded-md bg-white/10 text-slate-300">
                                        {{ $user->roles->first()->name ?? 'Administrator' }}
                                    </span>
                                </div>
                                <h2 class="text-base sm:text-xl md:text-2xl font-black text-white truncate">
                                    Dashboard Operasional Dept. HC &amp; GRC
                                </h2>
                                <p class="text-xs text-slate-300 line-clamp-1 sm:line-clamp-none">
                                    Pemantauan matriks ICoFR, pendaftar inovasi KMST, audit SPPT SNI, dan tata kelola akun.
                                </p>
                            </div>
                        </div>

                        <!-- Tombol Beralih Mode & Status Operasional -->
                        <div class="flex items-center justify-between md:flex-col md:items-end gap-2 pt-3 md:pt-0 border-t md:border-t-0 border-white/10 flex-shrink-0">
                            <a href="{{ route('dashboard', ['view' => 'employee']) }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/15 transition-all shadow-sm hover:scale-[1.02] active:scale-[0.98]">
                                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>Beralih ke Portal Karyawan</span>
                            </a>
                            <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">
                                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 4 KPI CARDS UTAMA HC & GRC -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    
                    <!-- KPI 1: ICOFR -->
                    <a href="{{ route('busdev.bpm-icofr.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-red-400 dark:hover:border-red-800 hover:-translate-y-1.5 transition-all duration-300 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">ICoFR RCM</span>
                            <span class="p-2 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 group-hover:rotate-6 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                        </div>
                        <div class="mt-3">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $staffStats['total_controls'] ?? 0 }}</span>
                            <span class="text-xs text-slate-400 block mt-1">Total Kontrol di {{ $staffStats['total_cycles'] ?? 0 }} Siklus Bisnis</span>
                        </div>
                    </a>

                    <!-- KPI 2: KMST INOVASI -->
                    <a href="{{ route('busdev.innovation.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-rose-400 dark:hover:border-rose-800 hover:-translate-y-1.5 transition-all duration-300 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendaftar KMST</span>
                            <span class="p-2 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 group-hover:rotate-6 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </span>
                        </div>
                        <div class="mt-3">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $staffStats['total_kmst'] ?? 0 }}</span>
                            <span class="text-xs text-slate-400 block mt-1">SS: {{ $staffStats['kmst_ss_count'] ?? 0 }} • GKM: {{ $staffStats['kmst_gkm_count'] ?? 0 }} • PKM: {{ ($staffStats['kmst_pkm_count'] ?? 0) }} • BI: {{ ($staffStats['kmst_bi_count'] ?? 0) }}</span>
                        </div>
                    </a>

                    <!-- KPI 3: AUDIT SNI -->
                    <a href="{{ route('busdev.system-development.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-blue-400 dark:hover:border-blue-800 hover:-translate-y-1.5 transition-all duration-300 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Audit SPPT SNI</span>
                            <span class="p-2 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 group-hover:rotate-6 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                        </div>
                        <div class="mt-3">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $staffStats['total_audits'] ?? 0 }}</span>
                            <span class="text-xs text-slate-400 block mt-1">Temuan Audit • {{ $staffStats['total_certs'] ?? 0 }} Sertifikat</span>
                        </div>
                    </a>

                    <!-- KPI 4: AKUN PENGGUNA -->
                    <a href="{{ route('accounts.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-emerald-400 dark:hover:border-emerald-800 hover:-translate-y-1.5 transition-all duration-300 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Akun Terdaftar</span>
                            <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 group-hover:rotate-6 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </span>
                        </div>
                        <div class="mt-3">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $staffStats['total_users'] ?? 0 }}</span>
                            <span class="text-xs text-slate-400 block mt-1">Pengguna di {{ $staffStats['total_depts'] ?? 0 }} Departemen</span>
                        </div>
                    </a>

                </div>

                <!-- PINTASAN MODUL KERJA INTERNAL HC & GRC -->
                <div class="p-4 sm:p-6 lg:p-8 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">Pintasan Cepat Modul Kerja</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <a href="{{ route('busdev.bpm-icofr.index') }}" class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-red-50 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800 transition hover:-translate-y-0.5">
                            <span class="p-2 rounded-xl bg-red-100 text-[#F5333F] dark:bg-red-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">ICoFR Process &amp; RCM</span>
                                <span class="text-[11px] text-slate-500">Matriks risiko, CSA, sinkronisasi spreadsheet</span>
                            </div>
                        </a>

                        <a href="{{ route('busdev.system-development.index') }}" class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-blue-50 dark:hover:bg-blue-950/20 border border-slate-100 dark:border-slate-800 transition hover:-translate-y-0.5">
                            <span class="p-2 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </span>
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">System Development</span>
                                <span class="text-[11px] text-slate-500">Sertifikasi SPPT SNI &amp; Temuan Audit</span>
                            </div>
                        </a>

                        <a href="{{ route('busdev.innovation.index') }}" class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-rose-50 dark:hover:bg-rose-950/20 border border-slate-100 dark:border-slate-800 transition hover:-translate-y-0.5">
                            <span class="p-2 rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </span>
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Innovation Management</span>
                                <span class="text-[11px] text-slate-500">Monitoring &amp; reviu makalah tim inovasi</span>
                            </div>
                        </a>
                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
