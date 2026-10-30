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
            50% { transform: translateY(-6px) rotate(0.8deg); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(6px) rotate(-0.8deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 0.9; transform: scale(1.03); }
        }
        .anim-float { animation: float-slow 7s ease-in-out infinite; }
        .anim-float-rev { animation: float-reverse 8s ease-in-out infinite; }
        .anim-pulse-glow { animation: pulse-glow 5s ease-in-out infinite; }
    </style>

    <div class="py-4 sm:py-8">
        <div class="w-full px-2 sm:px-6 lg:px-8 space-y-5 sm:space-y-6">

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
            {{-- A. TAMPILAN KHUSUS KARYAWAN UMUM (BALANCED & HARMONIOUS) --}}
            {{-- ######################################################## --}}
            @if($viewMode === 'employee')

                <!-- 1. EXECUTIVE WELCOME BANNER (BERWIBAWA, ELEGAN & MODERN) -->
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white shadow-xl border border-slate-800/80 group">
                    <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-[#F5333F]/15 blur-3xl pointer-events-none anim-float"></div>
                    <div class="absolute right-1/3 -bottom-24 w-72 h-72 rounded-full bg-red-600/10 blur-3xl pointer-events-none anim-float-rev"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(#ffffff0a_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

                    <div class="relative p-5 sm:p-7 flex flex-col md:flex-row md:items-center justify-between gap-5 z-10">
                        <div class="flex items-center gap-4 sm:gap-5">
                            
                            <!-- Avatar Squircle -->
                            <div class="relative flex-shrink-0">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-white/20 shadow-xl transition-transform duration-300 group-hover:scale-105">
                                @else
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-800 via-slate-900 to-slate-800 text-white font-headline font-black text-2xl flex items-center justify-center ring-2 ring-white/20 shadow-xl border border-white/10">
                                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-rose-200">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                                <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-slate-950 flex items-center justify-center shadow-sm" title="Online & Terverifikasi">
                                    <span class="w-1 h-1 rounded-full bg-white animate-ping"></span>
                                </span>
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-widest rounded-full bg-red-500/15 text-red-400 border border-red-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F5333F] animate-pulse"></span>
                                        Portal Karyawan Semen Tonasa
                                    </span>
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-white/10 text-slate-300 border border-white/10">
                                        {{ $user->roles->first()->name ?? 'Karyawan' }}
                                    </span>
                                    @if(isset($karyawan) && $karyawan && $karyawan->no_sap)
                                        <span class="px-2.5 py-0.5 text-[10px] font-mono font-semibold rounded-full bg-white/5 text-slate-300 border border-white/10">
                                            SAP: {{ $karyawan->no_sap }}
                                        </span>
                                    @endif
                                </div>
                                <h2 class="text-xl sm:text-2xl font-headline font-black tracking-tight text-white flex items-center gap-2">
                                    Selamat Datang, {{ $user->name }}!
                                </h2>
                                <p class="text-xs text-slate-300 flex flex-wrap items-center gap-x-3 gap-y-1">
                                    <span class="inline-flex items-center gap-1.5 text-slate-200 font-medium">
                                        <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $user->unit?->name_unit ?? 'Unit of Business & System Development' }}
                                    </span>
                                    <span class="text-slate-600">•</span>
                                    <span class="inline-flex items-center gap-1.5 text-slate-400">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $user->email }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Right Side: Kalender & Aksi Cepat Akun -->
                        <div class="flex items-start md:items-end justify-between md:flex-col gap-3 border-t md:border-t-0 border-white/10 pt-3 md:pt-0">
                            <div class="text-left md:text-right">
                                <span class="text-[10px] font-medium text-slate-400 block uppercase tracking-wider">Kalender Kerja:</span>
                                <span class="text-xs sm:text-sm font-headline font-bold text-white flex items-center gap-1.5 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('profile.edit') }}" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/15 transition-all shadow-xs flex items-center gap-1.5 hover:scale-[1.02]">
                                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span>Kelola Profil</span>
                                </a>
                                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-300 text-xs font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Aktif</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. QUICK STAT CARDS (4 KOLOM SIMETRIS, LAPANG & MODERN) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- KARTU 1: STATUS KMST INOVASI -->
                    <div class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Inovasi Mutu (KMST)</span>
                            <span class="w-8 h-8 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </span>
                        </div>
                        <div class="mt-3">
                            <div class="text-xl sm:text-2xl font-headline font-black text-slate-900 dark:text-white">
                                @if($userKmstRegistration)
                                    {{ $userKmstRegistration->kategori }}
                                @else
                                    KMST 2026/27
                                @endif
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block mt-0.5 truncate">
                                @if($userKmstRegistration)
                                    Terdaftar di Tim: <strong>{{ $userKmstRegistration->nama_tim }}</strong>
                                @else
                                    Pendaftaran Terbuka
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- KARTU 2: LAYANAN KESEHATAN (STMC) -->
                    <div class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Layanan Kesehatan (STMC)</span>
                            <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </span>
                        </div>
                        <div class="mt-3">
                            <div class="text-xl sm:text-2xl font-headline font-black text-slate-900 dark:text-white">
                                @if(isset($userMcu) && $userMcu)
                                    {{ ucfirst($userMcu->status ?? 'Terjadwal') }}
                                @else
                                    Siap Layanan
                                @endif
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block mt-0.5 truncate">
                                @if(isset($userMcu) && $userMcu)
                                    MCU: {{ \Carbon\Carbon::parse($userMcu->tanggal_mcu)->translatedFormat('d F Y') }}
                                @else
                                    Klinik Biringere 24 Jam
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- KARTU 3: UNIT KERJA & DEPT -->
                    <div class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Unit Kerja</span>
                            <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </span>
                        </div>
                        <div class="mt-3">
                            <div class="text-xl sm:text-2xl font-headline font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                BUSDEV
                                <span class="text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-red-100 text-[#F5333F] dark:bg-red-900/40 dark:text-red-300">Tonasa</span>
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block mt-0.5 truncate">
                                PT Semen Tonasa • Holding SIG
                            </span>
                        </div>
                    </div>

                    <!-- KARTU 4: TEMPLATE RESMI KMST -->
                    <div class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Template Makalah</span>
                            <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                        </div>
                        <div class="mt-3">
                            <div class="text-xl sm:text-2xl font-headline font-black text-slate-900 dark:text-white">
                                5 Kategori
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block mt-0.5 truncate">
                                Format .docx Resmi KMST
                            </span>
                        </div>
                    </div>

                </div>

                <!-- 3. PROGRAM UNGGULAN INOVASI: KMST 2026/2027 + UNDUH 5 TEMPLATE RESMI -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 space-y-6">
                    
                    <!-- Header Kartu KMST & Tombol Aksi -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 pb-5 border-b border-slate-100 dark:border-slate-800">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-red-50 text-[#F5333F] dark:bg-red-500/10 border border-red-200/60 dark:border-red-800/40">
                                    PROGRAM UNGGULAN INOVASI
                                </span>
                                <span class="text-xs text-slate-400 font-semibold">• KMST 2026/2027</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-headline font-bold text-slate-900 dark:text-white">
                                Konvensi Mutu Semen Tonasa (KMST) 2026 – 2027
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 max-w-3xl leading-relaxed">
                                Salurkan gagasan dan inovasi terbaik Anda untuk mewujudkan efisiensi biaya, produktivitas optimal, dan keselamatan kerja di PT Semen Tonasa. Terbuka dalam 5 Kategori Resmi: SS (Office), SS (Plant), GKM, PKM, dan BI.
                            </p>
                        </div>

                        <!-- Tombol Aksi KMST -->
                        <div class="flex items-center gap-2.5 flex-shrink-0">
                            <a href="{{ route('inovasi.portal') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                                <span>Buka Portal Inovasi</span>
                                <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            <a href="{{ route('inovasi.portal') }}#daftar" class="px-5 py-2.5 rounded-xl bg-[#F5333F] hover:bg-red-600 text-white text-xs font-bold transition shadow-sm hover:scale-[1.02] active:scale-[0.98] flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                                <span>Formulir Pendaftaran</span>
                            </a>
                        </div>
                    </div>

                    <!-- Status Pendaftaran Karyawan Terkini -->
                    @if($userKmstRegistration)
                        <div class="p-4 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-300 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <span class="font-bold text-emerald-900 dark:text-emerald-200 block">Status Pendaftaran: Anda Sudah Terdaftar di KMST!</span>
                                    <span class="text-emerald-700 dark:text-emerald-300">Tim: <strong class="text-slate-900 dark:text-white">{{ $userKmstRegistration->nama_tim }}</strong> • Kategori: <strong class="px-1.5 py-0.5 rounded bg-emerald-200/60 dark:bg-emerald-900/60">{{ $userKmstRegistration->kategori }}</strong></span>
                                </div>
                            </div>
                            <a href="{{ route('inovasi.portal') }}" class="font-bold text-emerald-800 dark:text-emerald-300 hover:underline flex items-center gap-1">
                                <span>Lihat Rincian Tim</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-600 dark:text-slate-300">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                                <span>Pendaftaran KMST Inovasi Terbuka untuk seluruh insan PT Semen Tonasa. Bentuk tim atau daftarkan ide individu Anda.</span>
                            </div>
                            <span class="font-bold text-[#F5333F]">Periode 2026/2027</span>
                        </div>
                    @endif

                    <!-- Bagian Unduh 5 Template Makalah Resmi -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Template Resmi Makalah (Format .docx Resmi):
                            </span>
                            <a href="{{ route('inovasi.portal') }}#panduan" class="text-xs font-bold text-[#F5333F] hover:underline flex items-center gap-1">
                                <span>Panduan Format KMST</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>

                        <!-- Grid 5 Kolom Tombol Unduh Template -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                            
                            <!-- 1. SS OFFICE -->
                            <a href="{{ route('inovasi.download-template', ['kategori' => 'SS_Office']) }}" class="group p-3 rounded-2xl bg-slate-50 hover:bg-white dark:bg-slate-800/50 dark:hover:bg-slate-800 border border-slate-200/80 hover:border-red-300 dark:border-slate-800 dark:hover:border-red-900/50 shadow-2xs hover:shadow-md transition-all hover:-translate-y-0.5 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-[#F5333F] transition-colors">SS (Office)</span>
                                    <span class="text-[10px] text-slate-400 block">Format .docx</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#F5333F] transition-colors shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            </a>

                            <!-- 2. SS PLANT -->
                            <a href="{{ route('inovasi.download-template', ['kategori' => 'SS_Plant']) }}" class="group p-3 rounded-2xl bg-slate-50 hover:bg-white dark:bg-slate-800/50 dark:hover:bg-slate-800 border border-slate-200/80 hover:border-red-300 dark:border-slate-800 dark:hover:border-red-900/50 shadow-2xs hover:shadow-md transition-all hover:-translate-y-0.5 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-[#F5333F] transition-colors">SS (Plant)</span>
                                    <span class="text-[10px] text-slate-400 block">Format .docx</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#F5333F] transition-colors shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            </a>

                            <!-- 3. GKM -->
                            <a href="{{ route('inovasi.download-template', ['kategori' => 'GKM']) }}" class="group p-3 rounded-2xl bg-slate-50 hover:bg-white dark:bg-slate-800/50 dark:hover:bg-slate-800 border border-slate-200/80 hover:border-red-300 dark:border-slate-800 dark:hover:border-red-900/50 shadow-2xs hover:shadow-md transition-all hover:-translate-y-0.5 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-[#F5333F] transition-colors">GKM</span>
                                    <span class="text-[10px] text-slate-400 block">Gugus Mutu</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#F5333F] transition-colors shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            </a>

                            <!-- 4. PKM -->
                            <a href="{{ route('inovasi.download-template', ['kategori' => 'PKM']) }}" class="group p-3 rounded-2xl bg-slate-50 hover:bg-white dark:bg-slate-800/50 dark:hover:bg-slate-800 border border-slate-200/80 hover:border-red-300 dark:border-slate-800 dark:hover:border-red-900/50 shadow-2xs hover:shadow-md transition-all hover:-translate-y-0.5 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-[#F5333F] transition-colors">PKM</span>
                                    <span class="text-[10px] text-slate-400 block">Proyek Mutu</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#F5333F] transition-colors shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            </a>

                            <!-- 5. BI (BREAKTHROUGH) -->
                            <a href="{{ route('inovasi.download-template', ['kategori' => 'BI']) }}" class="group p-3 rounded-2xl bg-slate-50 hover:bg-white dark:bg-slate-800/50 dark:hover:bg-slate-800 border border-slate-200/80 hover:border-red-300 dark:border-slate-800 dark:hover:border-red-900/50 shadow-2xs hover:shadow-md transition-all hover:-translate-y-0.5 flex items-center gap-2.5 col-span-2 sm:col-span-1">
                                <span class="w-8 h-8 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-[#F5333F] transition-colors">BI</span>
                                    <span class="text-[10px] text-slate-400 block">Breakthrough</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#F5333F] transition-colors shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            </a>

                        </div>
                    </div>

                </div>

                <!-- 4. HALL OF FAME: GALERI PRESTASI JUARA INOVASI SEMEN TONASA (MEWAH & SEJAJAR 4 KOLOM) -->
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded-full bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-xs">
                                    <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 5.6L20 10l-4.2 3.8 1.2 5.8-5-3-5 3 1.2-5.8L4 10l5.6-2.4L12 2z"/></svg>
                                    Hall of Fame Inovasi
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">• Prestasi Bergengsi Nasional &amp; Internasional</span>
                            </div>
                            <h3 class="text-lg sm:text-xl font-headline font-black text-slate-900 dark:text-white mt-1">
                                Galeri Prestasi Juara Inovasi PT Semen Tonasa
                            </h3>
                        </div>
                        <a href="{{ route('inovasi.portal') }}#galeri" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 border border-slate-200/80 dark:border-slate-700 shadow-2xs transition-all hover:scale-[1.02] active:scale-[0.98] self-start sm:self-auto">
                            <span>Jelajahi Portal Inovasi</span>
                            <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>

                    <!-- 4 Kartu Prestasi Berkelas (Grid 4 Kolom di Layar Lebar) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        
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
                                    <span class="font-extrabold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-slate-700/60">💰 Rp 2.4 M</span>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 2: GKM (ICQCC Internasional) -->
                        <div class="group relative flex flex-col justify-between bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                            <div>
                                <div class="relative aspect-[16/10] overflow-hidden bg-slate-950">
                                    <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Platinum Award ICQCC" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                                    <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg backdrop-blur-md bg-slate-950/75 border border-white/15 text-white uppercase tracking-wider shadow-md">
                                        <svg class="w-3 h-3 text-cyan-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 5.6L20 10l-4.2 3.8 1.2 5.8-5-3-5 3 1.2-5.8L4 10l5.6-2.4L12 2z"/></svg>
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
                                        <span class="text-[10px] text-slate-400 font-medium">Internasional</span>
                                    </div>
                                    <h4 class="text-sm font-headline font-bold text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors line-clamp-2 leading-snug">
                                        AI Predictive Maintenance Kiln 4
                                    </h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                        Penerapan machine learning deteksi dini anomali vibrasi motor raw mill dan kiln pabrik.
                                    </p>
                                </div>
                            </div>
                            <div class="p-4 pt-0">
                                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                                    <span class="text-slate-500 dark:text-slate-400 truncate max-w-[120px] font-medium">Tim GKM Digital</span>
                                    <span class="font-extrabold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-slate-700/60">⏱️ Zero Stop</span>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 3: PKM (Inovasi Holding SIG) -->
                        <div class="group relative flex flex-col justify-between bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                            <div>
                                <div class="relative aspect-[16/10] overflow-hidden bg-slate-950">
                                    <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Gold Award SIG Group" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                                    <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg backdrop-blur-md bg-slate-950/75 border border-white/15 text-white uppercase tracking-wider shadow-md">
                                        <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 5.6L20 10l-4.2 3.8 1.2 5.8-5-3-5 3 1.2-5.8L4 10l5.6-2.4L12 2z"/></svg>
                                        Gold Award SIG
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
                                        Digital Blending Limestone IoT
                                    </h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                        Sensor optik dan tracking armada tambang untuk konsistensi komposisi feed kiln.
                                    </p>
                                </div>
                            </div>
                            <div class="p-4 pt-0">
                                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                                    <span class="text-slate-500 dark:text-slate-400 truncate max-w-[120px] font-medium">Tim PKM Tambang</span>
                                    <span class="font-extrabold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-slate-700/60">🎯 Akurasi 99%</span>
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
                                        Pabrik Tonasa
                                    </span>
                                </div>
                                <div class="p-4 space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="px-2 py-0.5 text-[9px] font-black rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 uppercase border border-slate-200/60 dark:border-slate-700/60">
                                            SS (Plant)
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-medium">Internal Tonasa</span>
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
                                    <span class="font-extrabold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-slate-700/60">⏱️ Zero Delay</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 5. DUA MODUL LAYANAN MANDIRI TERPADU (GRID 2 KOLOM SEIMBANG 50:50) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    
                    <!-- KARTU KIRI: LAYANAN KESEHATAN KLINIK STMC -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-7 flex flex-col justify-between shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 space-y-5">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 text-xs font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    Layanan Kesehatan Tonasa
                                </span>
                                <span class="text-xs font-medium text-slate-400">Klinik Biringere 24 Jam</span>
                            </div>

                            <div>
                                <h3 class="text-lg sm:text-xl font-headline font-bold text-slate-900 dark:text-white">
                                    Semen Tonasa Medical Centre (STMC)
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    Fasilitas pemantauan kesehatan berkala (MCU), jadwal praktik dokter, konsultasi rawat jalan, dan aplikasi mobile STMC Health.
                                </p>
                            </div>

                            <!-- Status MCU Karyawan Safe Check -->
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <div class="space-y-0.5">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Jadwal MCU Tahunan Anda</span>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                        @if(isset($userMcu) && $userMcu)
                                            Tanggal: {{ \Carbon\Carbon::parse($userMcu->tanggal_mcu)->translatedFormat('d F Y') }} (Poli Umum)
                                        @else
                                            Belum ada jadwal MCU aktif periode ini
                                        @endif
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-[#F5333F] bg-white dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                                    STMC Mobile Ready
                                </span>
                            </div>
                        </div>

                        <!-- Footer STMC -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                            <span>Butuh darurat medis? Ext: <strong class="text-slate-900 dark:text-white">1234</strong></span>
                            <span class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                Biringere • Pangkep
                            </span>
                        </div>
                    </div>

                    <!-- KARTU KANAN: TATA KELOLA ICOFR & BUDAYA KERJA AKHLAK -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-7 flex flex-col justify-between shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 space-y-5">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-xs font-bold">
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    Tata Kelola &amp; Kepatuhan
                                </span>
                                <span class="text-xs font-medium text-slate-400">Standar SIG &amp; BUMN</span>
                            </div>

                            <div>
                                <h3 class="text-lg sm:text-xl font-headline font-bold text-slate-900 dark:text-white">
                                    Pengendalian Internal (ICoFR) &amp; Nilai AKHLAK
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    Penerapan matriks risiko &amp; kontrol (RCM) serta penanaman 6 nilai inti insan Semen Tonasa dalam setiap proses bisnis operasional.
                                </p>
                            </div>

                            <!-- 6 Nilai AKHLAK Pills -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 space-y-2">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Pilar Budaya Kerja BUMN (AKHLAK):</span>
                                <div class="flex flex-wrap gap-1.5 text-xs font-bold">
                                    <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 shadow-2xs">Amanah</span>
                                    <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 shadow-2xs">Kompeten</span>
                                    <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 shadow-2xs">Harmonis</span>
                                    <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 shadow-2xs">Loyal</span>
                                    <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 shadow-2xs">Adaptif</span>
                                    <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 shadow-2xs">Kolaboratif</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Helpdesk -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                            <span>Helpdesk: <strong class="text-slate-900 dark:text-white">hc.service@sementonasa.co.id</strong></span>
                            <span class="text-slate-500">Ext: <strong>5678</strong></span>
                        </div>
                    </div>

                </div>

                <!-- 6. FOOTNOTE RESMI DENGAN INFORMASI OPERASIONAL -->
                <div class="p-5 rounded-3xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span>Butuh bantuan terkait akun atau sistem informasi? Hubungi Helpdesk Dept. HC &amp; Services PT Semen Tonasa.</span>
                    </div>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Senin - Jumat (08.00 - 16.30 WITA)</span>
                </div>

{{-- ######################################################## --}}
            {{-- B. TAMPILAN OPERASIONAL STAF (ROLE-BASED & BEST PRACTICE)--}}
            {{-- ######################################################## --}}
            @else

                {{-- ======================================================== --}}
                {{-- TAB SWITCHER KHUSUS DEVELOPER & SUPER ADMIN              --}}
                {{-- ======================================================== --}}
                @if($isDeveloper || $isSuperAdmin)
                    <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs">
                        <div class="flex items-center gap-1.5 overflow-x-auto py-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-2">Divisi:</span>
                            <a href="{{ route('dashboard', ['view' => 'staff', 'dept' => 'overview']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $staffDept === 'overview' ? 'bg-[#F5333F] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                🏢 Semua Divisi (Overview)
                            </a>
                            <a href="{{ route('dashboard', ['view' => 'staff', 'dept' => 'busdev']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $staffDept === 'busdev' ? 'bg-[#F5333F] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                📊 Unit Busdev &amp; Inovasi
                            </a>
                            <a href="{{ route('dashboard', ['view' => 'staff', 'dept' => 'stmc']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $staffDept === 'stmc' ? 'bg-[#F5333F] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                🏥 Unit STMC Health
                            </a>
                        </div>

                        <a href="{{ route('dashboard', ['view' => 'employee']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition">
                            <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <span>Pratinjau Portal Karyawan</span>
                        </a>
                    </div>
                @endif


                {{-- ######################################################## --}}
                {{-- KONDISI 1: DASHBOARD KHUSUS ADMIN STMC                   --}}
                {{-- ######################################################## --}}
                @if($staffDept === 'stmc')

                    <!-- 1. HEADER OPERASIONAL STMC HEALTH -->
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-850 to-slate-950 text-white shadow-xl border border-slate-800 p-5 sm:p-7 group">
                        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-blue-600/20 blur-3xl pointer-events-none anim-float"></div>
                        <div class="absolute right-1/4 -bottom-16 w-64 h-64 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none anim-float-rev"></div>

                        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
                            <div class="flex items-center gap-4 sm:gap-5">
                                <!-- STMC Department Badge -->
                                <div class="relative flex-shrink-0">
                                    <div class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-blue-700 via-indigo-600 to-sky-500 p-0.5 ring-4 ring-white/10 shadow-2xl flex items-center justify-center overflow-hidden select-none">
                                        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/25 pointer-events-none"></div>
                                        <div class="relative z-10 flex flex-col items-center justify-center text-center leading-none">
                                            <svg class="w-5 h-5 text-white mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                            <span class="text-xs font-black tracking-wider text-white uppercase font-mono">STMC</span>
                                        </div>
                                    </div>
                                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-slate-900 flex items-center justify-center shadow-md" title="Klinik Siaga">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                    </span>
                                </div>

                                <div class="min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                            Semen Tonasa Medical Centre
                                        </span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-white/10 text-slate-300">
                                            {{ $user->roles->first()->name ?? 'Admin STMC' }}
                                        </span>
                                    </div>
                                    <h2 class="text-lg sm:text-2xl font-black text-white truncate">
                                        Dashboard Operasional Semen Tonasa Medical Centre (STMC)
                                    </h2>
                                    <p class="text-xs text-slate-300">
                                        Pusat pengelolaan Medical Check Up (MCU), jadwal praktik dokter klinik Biringere 24 jam &amp; rekam medis kesehatan kerja.
                                    </p>
                                </div>
                            </div>

                            <!-- Tombol Aksi Cepat STMC -->
                            <div class="flex items-start md:items-end justify-between md:flex-col gap-2.5 border-t md:border-t-0 border-white/10 pt-3 md:pt-0 flex-shrink-0">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('jadwal-mcu.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-[#F5333F] hover:bg-red-600 text-white shadow-sm transition-all hover:scale-[1.02]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                                        <span>+ Buat Jadwal MCU</span>
                                    </a>
                                    <a href="{{ route('scan.qr') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/15 transition-all">
                                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        <span>Scan QR</span>
                                    </a>
                                </div>
                                <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. 4 KPI CARDS UTAMA OPERASIONAL STMC -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        <!-- KPI 1: TOTAL PASIEN & KARYAWAN -->
                        <a href="{{ route('karyawan.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-blue-400 dark:hover:border-blue-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Data Pasien &amp; Karyawan</span>
                                <span class="p-2 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $stmcStats['total_pasien'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">Karyawan: {{ $stmcStats['total_karyawan'] ?? 0 }} • Keluarga Terdaftar</span>
                            </div>
                        </a>

                        <!-- KPI 2: JADWAL MCU AKTIF -->
                        <a href="{{ route('jadwal.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-red-400 dark:hover:border-red-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jadwal MCU</span>
                                <span class="p-2 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $stmcStats['jadwal_aktif'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">Sesi Aktif • Total: {{ $stmcStats['total_jadwal'] ?? 0 }} Terjadwal</span>
                            </div>
                        </a>

                        <!-- KPI 3: TIM DOKTER KLINIK -->
                        <a href="{{ route('admin.tambah-dokter') }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-emerald-400 dark:hover:border-emerald-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dokter &amp; Tenaga Medis</span>
                                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $stmcStats['total_dokter'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">Dokter Spesialis &amp; Umum Siaga 24 Jam</span>
                            </div>
                        </a>

                        <!-- KPI 4: PEMANTAUAN LINGKUNGAN PABRIK -->
                        <a href="{{ route('pemantauan.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-purple-400 dark:hover:border-purple-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pemantauan Lingkungan</span>
                                <span class="p-2 rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $stmcStats['total_pemantauan'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">Titik Uji Lingkungan Fisik &amp; K3 Tonasa</span>
                            </div>
                        </a>

                    </div>

                    <!-- 3. PINTASAN CEPAT MODUL KERJA STMC HEALTH -->
                    <div class="p-6 sm:p-8 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                Pintasan Cepat Modul Kerja STMC Health
                            </h3>
                            <span class="text-xs text-slate-400 font-medium">Akses Lengkap Fitur Medis</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                            
                            <!-- 1. JADWAL MCU -->
                            <a href="{{ route('jadwal.index') }}" class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-red-50 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800 transition-all hover:-translate-y-0.5 group">
                                <span class="p-2.5 rounded-xl bg-red-100 text-[#F5333F] dark:bg-red-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-[#F5333F] transition-colors">Jadwal &amp; Kuota MCU</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate block">Atur agenda MCU tahunan karyawan &amp; keluarga</span>
                                </div>
                            </a>

                            <!-- 2. DATA KARYAWAN & PASIEN -->
                            <a href="{{ route('karyawan.index') }}" class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-blue-50 dark:hover:bg-blue-950/20 border border-slate-100 dark:border-slate-800 transition-all hover:-translate-y-0.5 group">
                                <span class="p-2.5 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-blue-600 transition-colors">Data Karyawan &amp; Pasien</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate block">Basis data rekam medis, NIK, dan No. SAP</span>
                                </div>
                            </a>

                            <!-- 3. SCAN QR -->
                            <a href="{{ route('scan.qr') }}" class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 border border-slate-100 dark:border-slate-800 transition-all hover:-translate-y-0.5 group">
                                <span class="p-2.5 rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-emerald-600 transition-colors">Scan Kehadiran QR</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate block">Check-in cepat peserta MCU via QR scanner</span>
                                </div>
                            </a>

                            <!-- 4. JADWAL DOKTER & POLI -->
                            <a href="{{ route('admin.jadwal-dokter') }}" class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-purple-50 dark:hover:bg-purple-950/20 border border-slate-100 dark:border-slate-800 transition-all hover:-translate-y-0.5 group">
                                <span class="p-2.5 rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-purple-600 transition-colors">Jadwal Praktik Dokter</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate block">Kalender shift jaga dokter klinik Tonasa</span>
                                </div>
                            </a>

                            <!-- 5. KELOLA PAKET POLI -->
                            <a href="{{ route('paket-poli') }}" class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-amber-50 dark:hover:bg-amber-950/20 border border-slate-100 dark:border-slate-800 transition-all hover:-translate-y-0.5 group">
                                <span class="p-2.5 rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-amber-600 transition-colors">Kelola Paket Poli</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate block">Paket laboratorium, fisik, dan penunjang MCU</span>
                                </div>
                            </a>

                            <!-- 6. REKAM MEDIS & LAPORAN -->
                            <a href="{{ route('admin.laporan.pemeriksaan') }}" class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-rose-50 dark:hover:bg-rose-950/20 border border-slate-100 dark:border-slate-800 transition-all hover:-translate-y-0.5 group">
                                <span class="p-2.5 rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-rose-600 transition-colors">Rekam Medis &amp; Laporan</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate block">Export PDF hasil MCU &amp; resume fit-to-work</span>
                                </div>
                            </a>

                        </div>
                    </div>

                    <!-- 4. TABEL AGENDA MCU TERKINI -->
                    <div class="p-6 sm:p-8 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
                                    Agenda &amp; Jadwal MCU Terdekat
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">Daftar pemeriksaan medical check-up terkini karyawan Semen Tonasa.</p>
                            </div>
                            <a href="{{ route('jadwal.index') }}" class="text-xs font-bold text-[#F5333F] hover:underline flex items-center gap-1">
                                <span>Lihat Semua Jadwal</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>

                        @if(isset($recentMcuSchedules) && $recentMcuSchedules->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider font-extrabold">
                                            <th class="py-3 px-3">Nama Pasien</th>
                                            <th class="py-3 px-3">No. SAP / NIK</th>
                                            <th class="py-3 px-3">Tanggal MCU</th>
                                            <th class="py-3 px-3">Dokter Pemeriksa</th>
                                            <th class="py-3 px-3">Status</th>
                                            <th class="py-3 px-3 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        @foreach($recentMcuSchedules as $mcu)
                                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                                <td class="py-3 px-3 font-bold text-slate-900 dark:text-white">
                                                    {{ $mcu->nama_pasien ?? ($mcu->karyawan->nama_karyawan ?? 'Pasien') }}
                                                </td>
                                                <td class="py-3 px-3 font-mono text-slate-500">
                                                    {{ $mcu->no_sap ?? ($mcu->nik_pasien ?? '-') }}
                                                </td>
                                                <td class="py-3 px-3 text-slate-600 dark:text-slate-300">
                                                    {{ \Carbon\Carbon::parse($mcu->tanggal_mcu)->translatedFormat('d M Y') }}
                                                </td>
                                                <td class="py-3 px-3 text-slate-600 dark:text-slate-300">
                                                    {{ $mcu->dokter->nama_dokter ?? 'Dokter Klinik' }}
                                                </td>
                                                <td class="py-3 px-3">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $mcu->status === 'selesai' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' }}">
                                                        {{ $mcu->status ?? 'Terjadwal' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-3 text-right">
                                                    <a href="{{ route('jadwal.show', $mcu->id) }}" class="font-bold text-[#F5333F] hover:underline">
                                                        Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-8 text-center rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800 space-y-3">
                                <span class="flex items-center justify-center w-12 h-12 mx-auto rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/10">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Belum Ada Sesi MCU Aktif Hari Ini</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Gunakan tombol buat jadwal untuk menambahkan peserta MCU baru.</p>
                                </div>
                                <a href="{{ route('jadwal-mcu.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#F5333F] hover:bg-red-600 text-white text-xs font-bold transition shadow-xs">
                                    <span>+ Buat Jadwal MCU Sekarang</span>
                                </a>
                            </div>
                        @endif
                    </div>


                {{-- ######################################################## --}}
                {{-- KONDISI 2: DASHBOARD KHUSUS ADMIN BUSDEV                 --}}
                {{-- ######################################################## --}}
                @elseif($staffDept === 'busdev')

                    <!-- 1. HEADER ADMIN BUSDEV -->
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-850 to-slate-950 text-white shadow-xl border border-slate-800 p-5 sm:p-7 group">
                        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-[#F5333F]/20 blur-3xl pointer-events-none anim-float"></div>
                        <div class="absolute right-1/4 -bottom-16 w-64 h-64 rounded-full bg-amber-500/10 blur-3xl pointer-events-none anim-float-rev"></div>

                        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
                            <div class="flex items-center gap-4 sm:gap-5">
                                <div class="relative flex-shrink-0">
                                    <div class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#990000] via-[#D31027] to-[#F5333F] p-0.5 ring-4 ring-white/10 shadow-2xl flex items-center justify-center overflow-hidden select-none">
                                        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/25 pointer-events-none"></div>
                                        <div class="relative z-10 flex flex-col items-center justify-center text-center leading-none">
                                            <span class="text-[8px] font-black tracking-widest text-red-100 uppercase font-mono">UNIT</span>
                                            <span class="text-xl sm:text-2xl font-black text-white tracking-tighter">BUSDEV</span>
                                        </div>
                                    </div>
                                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-slate-900 flex items-center justify-center shadow-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                    </span>
                                </div>

                                <div class="min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-red-500/20 text-red-300 border border-red-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                            Business &amp; System Development
                                        </span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-white/10 text-slate-300">
                                            {{ $user->roles->first()->name ?? 'Admin Busdev' }}
                                        </span>
                                    </div>
                                    <h2 class="text-lg sm:text-2xl font-black text-white truncate">
                                        Dashboard Operasional Business &amp; System Development
                                    </h2>
                                    <p class="text-xs text-slate-300">
                                        Pemantauan matriks ICoFR RCM, pendaftar inovasi mutu KMST, audit SPPT SNI &amp; kepatuhan tata kelola.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start md:items-end justify-between md:flex-col gap-2.5 border-t md:border-t-0 border-white/10 pt-3 md:pt-0 flex-shrink-0">
                                <a href="{{ route('dashboard', ['view' => 'employee']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/15 transition-all shadow-xs hover:scale-[1.02]">
                                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Beralih ke Portal Karyawan</span>
                                </a>
                                <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. 4 KPI CARDS BUSDEV -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        <!-- KPI 1: ICOFR -->
                        <a href="{{ route('busdev.bpm-icofr.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-red-400 dark:hover:border-red-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">ICoFR RCM</span>
                                <span class="p-2 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $busdevStats['total_controls'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">Total Kontrol di {{ $busdevStats['total_cycles'] ?? 0 }} Siklus Bisnis</span>
                            </div>
                        </a>

                        <!-- KPI 2: KMST INOVASI -->
                        <a href="{{ route('busdev.innovation.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-rose-400 dark:hover:border-rose-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendaftar KMST</span>
                                <span class="p-2 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $busdevStats['total_kmst'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">SS: {{ $busdevStats['kmst_ss_count'] ?? 0 }} • GKM: {{ $busdevStats['kmst_gkm_count'] ?? 0 }} • PKM: {{ $busdevStats['kmst_pkm_count'] ?? 0 }} • BI: {{ $busdevStats['kmst_bi_count'] ?? 0 }}</span>
                            </div>
                        </a>

                        <!-- KPI 3: AUDIT SPPT SNI -->
                        <a href="{{ route('busdev.system-development.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-blue-400 dark:hover:border-blue-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Audit SPPT SNI</span>
                                <span class="p-2 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $busdevStats['total_audits'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">Temuan Audit • {{ $busdevStats['total_certs'] ?? 0 }} Sertifikat</span>
                            </div>
                        </a>

                        <!-- KPI 4: DEPARTEMEN SISTEM -->
                        <div class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Departemen Terkelola</span>
                                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $busdevStats['total_depts'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">Unit &amp; Departemen PT Semen Tonasa</span>
                            </div>
                        </div>

                    </div>

                    <!-- 3. PINTASAN CEPAT MODUL KERJA BUSDEV -->
                    <div class="p-6 sm:p-8 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#F5333F]"></span>
                            Pintasan Cepat Modul Kerja Busdev
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('busdev.bpm-icofr.index') }}" class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-red-50 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800 transition-all hover:-translate-y-0.5 group">
                                <span class="p-2.5 rounded-xl bg-red-100 text-[#F5333F] dark:bg-red-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div>
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-[#F5333F] transition-colors">ICoFR Process &amp; RCM</span>
                                    <span class="text-[11px] text-slate-500">Matriks risiko, CSA, sinkronisasi spreadsheet</span>
                                </div>
                            </a>

                            <a href="{{ route('busdev.system-development.index') }}" class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-blue-50 dark:hover:bg-blue-950/20 border border-slate-100 dark:border-slate-800 transition-all hover:-translate-y-0.5 group">
                                <span class="p-2.5 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </span>
                                <div>
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-blue-600 transition-colors">System Development</span>
                                    <span class="text-[11px] text-slate-500">Sertifikasi SPPT SNI &amp; Temuan Audit</span>
                                </div>
                            </a>

                            <a href="{{ route('busdev.innovation.index') }}" class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-rose-50 dark:hover:bg-rose-950/20 border border-slate-100 dark:border-slate-800 transition-all hover:-translate-y-0.5 group">
                                <span class="p-2.5 rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </span>
                                <div>
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-rose-600 transition-colors">Innovation Management</span>
                                    <span class="text-[11px] text-slate-500">Monitoring &amp; reviu makalah tim inovasi</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- 4. TABEL PENDAFTAR KMST TERBARU -->
                    <div class="p-6 sm:p-8 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
                                    Pendaftaran Tim Inovasi KMST Terkini
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">Pantau karya inovasi yang baru saja didaftarkan oleh karyawan.</p>
                            </div>
                            <a href="{{ route('busdev.innovation.index') }}" class="text-xs font-bold text-[#F5333F] hover:underline flex items-center gap-1">
                                <span>Kelola Semua Pendaftar</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>

                        @if(isset($recentKmstRegistrations) && $recentKmstRegistrations->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider font-extrabold">
                                            <th class="py-3 px-3">Nama Tim</th>
                                            <th class="py-3 px-3">Kategori</th>
                                            <th class="py-3 px-3">Judul Inovasi</th>
                                            <th class="py-3 px-3">Tanggal Daftar</th>
                                            <th class="py-3 px-3 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        @foreach($recentKmstRegistrations as $reg)
                                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                                <td class="py-3 px-3 font-bold text-slate-900 dark:text-white">
                                                    {{ $reg->nama_tim }}
                                                </td>
                                                <td class="py-3 px-3">
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-red-50 text-[#F5333F] dark:bg-red-500/10">
                                                        {{ $reg->kategori }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-3 text-slate-600 dark:text-slate-300 max-w-xs truncate">
                                                    {{ $reg->judul ?? 'Inovasi Efisiensi Pabrik' }}
                                                </td>
                                                <td class="py-3 px-3 text-slate-500">
                                                    {{ \Carbon\Carbon::parse($reg->created_at)->translatedFormat('d M Y') }}
                                                </td>
                                                <td class="py-3 px-3 text-right">
                                                    <a href="{{ route('busdev.innovation.index') }}" class="font-bold text-[#F5333F] hover:underline">
                                                        Reviu
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-8 text-center rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800">
                                <p class="text-xs text-slate-400">Belum ada tim inovasi yang mendaftar pada periode ini.</p>
                            </div>
                        @endif
                    </div>


                {{-- ######################################################## --}}
                {{-- KONDISI 3: DASHBOARD MASTER SUPER ADMIN / DEVELOPER      --}}
                {{-- ######################################################## --}}
                @else

                    <!-- 1. HEADER EXECUTIVE OVERVIEW DEPT. HC & SERVICES -->
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-850 to-slate-950 text-white shadow-xl border border-slate-800 p-5 sm:p-7 group">
                        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-[#F5333F]/20 blur-3xl pointer-events-none anim-float"></div>
                        <div class="absolute right-1/4 -bottom-16 w-64 h-64 rounded-full bg-blue-500/15 blur-3xl pointer-events-none anim-float-rev"></div>

                        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
                            <div class="flex items-center gap-4 sm:gap-5">
                                <div class="relative flex-shrink-0">
                                    <div class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#990000] via-[#D31027] to-[#F5333F] p-0.5 ring-4 ring-white/10 shadow-2xl flex items-center justify-center overflow-hidden select-none">
                                        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/25 pointer-events-none"></div>
                                        <div class="relative z-10 flex flex-col items-center justify-center text-center leading-none">
                                            <span class="text-[7.5px] font-black tracking-widest text-red-100 uppercase font-mono">MASTER</span>
                                            <span class="text-xl sm:text-2xl font-black text-white tracking-tighter">HC</span>
                                        </div>
                                    </div>
                                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-slate-900 flex items-center justify-center shadow-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                    </span>
                                </div>

                                <div class="min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-red-500/20 text-red-300 border border-red-500/30">
                                            Dept. HC &amp; Services • Pengawasan Terpadu
                                        </span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-white/10 text-slate-300">
                                            {{ $user->roles->first()->name ?? 'Super Admin' }}
                                        </span>
                                    </div>
                                    <h2 class="text-lg sm:text-2xl font-black text-white truncate">
                                        Dashboard Eksekutif Dept. HC &amp; Services
                                    </h2>
                                    <p class="text-xs text-slate-300">
                                        Pemantauan lintas divisi: Tata Kelola ICoFR, Inovasi KMST, Layanan Medis STMC &amp; Manajemen Pengguna.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start md:items-end justify-between md:flex-col gap-2.5 border-t md:border-t-0 border-white/10 pt-3 md:pt-0 flex-shrink-0">
                                <a href="{{ route('accounts.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/15 transition-all shadow-xs hover:scale-[1.02]">
                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <span>Kelola Akun &amp; Hak Akses</span>
                                </a>
                                <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. 4 KPI CARDS GABUNGAN (MASTER OVERVIEW) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        <!-- KPI 1: BUSDEV ICOFR -->
                        <a href="{{ route('dashboard', ['view' => 'staff', 'dept' => 'busdev']) }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-red-400 dark:hover:border-red-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">ICoFR RCM (Busdev)</span>
                                <span class="p-2 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $staffStats['total_controls'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">Total Kontrol Pengendalian Internal</span>
                            </div>
                        </a>

                        <!-- KPI 2: PENDAFTAR KMST -->
                        <a href="{{ route('dashboard', ['view' => 'staff', 'dept' => 'busdev']) }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-rose-400 dark:hover:border-rose-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Inovasi KMST</span>
                                <span class="p-2 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $staffStats['total_kmst'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">Tim Karya Inovasi Mutu Terdaftar</span>
                            </div>
                        </a>

                        <!-- KPI 3: PASIEN & MCU STMC -->
                        <a href="{{ route('dashboard', ['view' => 'staff', 'dept' => 'stmc']) }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-blue-400 dark:hover:border-blue-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Layanan Medis STMC</span>
                                <span class="p-2 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $staffStats['total_pasien'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">{{ $staffStats['total_jadwal'] ?? 0 }} Sesi MCU • {{ $staffStats['total_dokter'] ?? 0 }} Dokter</span>
                            </div>
                        </a>

                        <!-- KPI 4: AKUN PENGGUNA TERDAFTAR -->
                        <a href="{{ route('accounts.index') }}" class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-emerald-400 dark:hover:border-emerald-800 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Akun Terdaftar</span>
                                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $staffStats['total_users'] ?? 0 }}</span>
                                <span class="text-xs text-slate-400 block mt-1">Pengguna di 5 Role Terstandarisasi</span>
                            </div>
                        </a>

                    </div>

                    <!-- 3. DUA MODUL KERJA UTAMA BERDAMPINGAN (BUSDEV & STMC) -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        
                        <!-- KOLOM KIRI: MODUL BUSINESS & SYSTEM DEVELOPMENT -->
                        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#F5333F]"></span>
                                    Divisi Business &amp; Sys. Dev
                                </h3>
                                <a href="{{ route('dashboard', ['view' => 'staff', 'dept' => 'busdev']) }}" class="text-xs font-bold text-[#F5333F] hover:underline">
                                    Buka Detail Busdev &rarr;
                                </a>
                            </div>

                            <div class="space-y-2.5">
                                <a href="{{ route('busdev.bpm-icofr.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="p-2 rounded-xl bg-red-100 text-[#F5333F] dark:bg-red-500/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </span>
                                        <div>
                                            <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-[#F5333F] transition-colors">ICoFR Process &amp; RCM</span>
                                            <span class="text-[11px] text-slate-400">Pengendalian internal &amp; siklus bisnis</span>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                </a>

                                <a href="{{ route('busdev.system-development.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="p-2 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </span>
                                        <div>
                                            <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-blue-600 transition-colors">System Development</span>
                                            <span class="text-[11px] text-slate-400">Audit SPPT SNI &amp; Sertifikat produk</span>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                </a>

                                <a href="{{ route('busdev.innovation.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="p-2 rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        </span>
                                        <div>
                                            <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-rose-600 transition-colors">Innovation Management</span>
                                            <span class="text-[11px] text-slate-400">Reviu pendaftaran karya mutu KMST</span>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: MODUL SEMEN TONASA MEDICAL CENTRE (STMC) -->
                        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                    Divisi STMC Medical Health
                                </h3>
                                <a href="{{ route('dashboard', ['view' => 'staff', 'dept' => 'stmc']) }}" class="text-xs font-bold text-blue-600 hover:underline">
                                    Buka Detail STMC &rarr;
                                </a>
                            </div>

                            <div class="space-y-2.5">
                                <a href="{{ route('jadwal.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="p-2 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </span>
                                        <div>
                                            <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-blue-600 transition-colors">Jadwal MCU &amp; Kuota</span>
                                            <span class="text-[11px] text-slate-400">Jadwal pemeriksaan karyawan berkala</span>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                </a>

                                <a href="{{ route('karyawan.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="p-2 rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        </span>
                                        <div>
                                            <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-emerald-600 transition-colors">Data Karyawan &amp; Pasien</span>
                                            <span class="text-[11px] text-slate-400">Rekam medis &amp; data keluarga</span>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                </a>

                                <a href="{{ route('scan.qr') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="p-2 rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-500/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </span>
                                        <div>
                                            <span class="text-xs font-bold text-slate-900 dark:text-white block group-hover:text-purple-600 transition-colors">Scan Kehadiran QR</span>
                                            <span class="text-[11px] text-slate-400">Verifikasi kehadiran pasien MCU</span>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>

                    </div>

                @endif

            @endif

        </div>
    </div>
</x-app-layout>
