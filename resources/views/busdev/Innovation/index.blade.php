<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 min-w-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="truncate">
                    <span class="sm:hidden">KMST Inovasi</span>
                    <span class="hidden sm:inline">Innovation Management &bull; KMST 2026/2027</span>
                </span>
            </div>
            
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1.5 px-3 py-1 bg-white dark:bg-slate-800 rounded-xl shadow-xs border border-slate-200 dark:border-slate-700 text-[10px] sm:text-xs font-bold flex-shrink-0">
                    <span class="relative flex w-2 h-2">
                        @if($kmstActive)
                            <span class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-emerald-400 animate-ping"></span>
                        @endif
                        <span class="relative inline-flex w-2 h-2 rounded-full {{ $kmstActive ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    </span>
                    <span class="{{ $kmstActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $kmstActive ? 'PORTAL BUKA' : 'PORTAL TUTUP' }}
                    </span>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $card = 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm transition-all duration-300';
    @endphp

    <div class="py-4 sm:py-6 space-y-6" x-data="{ mainTab: '{{ request('tab', $currentTab ?? 'monitor') }}', selectedReg: null }">

        {{-- ==================================================================== --}}
        {{-- HEADER TABS NAVIGATION: MONITOR & ANALITIK vs TABEL PENDAFTAR        --}}
        {{-- ==================================================================== --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-2 bg-slate-100/80 dark:bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
            
            {{-- Tab Switcher Pills --}}
            <div class="flex items-center gap-1.5 p-1 bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200/60 dark:border-slate-800">
                
                {{-- Tab 1: Monitoring & Statistik --}}
                <button type="button" 
                        @click="mainTab = 'monitor'; $nextTick(() => { window.dispatchEvent(new Event('resize')); })" 
                        :class="mainTab === 'monitor' ? 'bg-[#F5333F] text-white shadow-md shadow-red-500/20 font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 font-bold'"
                        class="flex items-center gap-2 px-4 py-2 text-xs rounded-lg transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"></path></svg>
                    <span>Monitoring &amp; Analitik</span>
                    <span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase tracking-wider" :class="mainTab === 'monitor' ? 'bg-white/20 text-white' : 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400'">
                        LIVE
                    </span>
                </button>

                {{-- Tab 2: Tabel Pendaftar (KMST) --}}
                <button type="button" 
                        @click="mainTab = 'pendaftar'" 
                        :class="mainTab === 'pendaftar' ? 'bg-[#F5333F] text-white shadow-md shadow-red-500/20 font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 font-bold'"
                        class="flex items-center gap-2 px-4 py-2 text-xs rounded-lg transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"></path></svg>
                    <span>Tabel Pendaftar</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-black" :class="mainTab === 'pendaftar' ? 'bg-white text-slate-900' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'">
                        {{ $stats['total'] }} Tim
                    </span>
                </button>

            </div>

            {{-- Quick Utility Links --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('inovasi.portal') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:border-red-500 hover:text-[#F5333F] rounded-xl border border-slate-200 dark:border-slate-800 transition-colors shadow-xs">
                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    <span>Portal Publik</span>
                </a>

                <a href="{{ route('settings.index', ['tab' => 'tahapan']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:border-red-500 hover:text-[#F5333F] rounded-xl border border-slate-200 dark:border-slate-800 transition-colors shadow-xs">
                    <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span>Kontrol Tahapan</span>
                </a>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- TAB 1: MONITORING & ANALITIK (KPI Cards, Charts, & Quick Summary)   --}}
        {{-- ==================================================================== --}}
        <div x-show="mainTab === 'monitor'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="space-y-6">

            {{-- 1. EXECUTIVE HERO BANNER INOVASI --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-850 to-slate-950 text-white shadow-xl border border-slate-800 p-5 sm:p-7 group">
                <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-[#F5333F]/20 blur-3xl pointer-events-none"></div>
                <div class="absolute right-1/3 -bottom-20 w-72 h-72 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-2 max-w-3xl">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-full bg-red-500/20 text-red-300 border border-red-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-ping"></span>
                                Konvensi Mutu Semen Tonasa (KMST) 2026/2027
                            </span>
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-white/10 text-slate-300">
                                Terbuka 5 Kategori Resmi
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
                            Pusat Manajemen &amp; Evaluasi Karya Inovasi
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-medium">
                            Pantau pendaftaran karya inovasi insan PT Semen Tonasa, verifikasi bidang benefit efisiensi, dan analisis statistik secara seketika (*real-time*).
                        </p>
                    </div>

                    {{-- Hero Actions --}}
                    <div class="flex flex-wrap items-center gap-2.5 flex-shrink-0">
                        <button type="button" 
                                @click="mainTab = 'pendaftar'"
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-black text-slate-900 bg-white hover:bg-red-50 hover:text-[#F5333F] rounded-xl transition-all shadow-md hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                            <svg class="w-4 h-4 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"></path></svg>
                            <span>Buka Tabel Pendaftar ({{ $stats['total'] }})</span>
                        </button>
                        <a href="{{ route('settings.index', ['tab' => 'tahapan']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-black text-white bg-gradient-to-r from-[#F5333F] to-rose-600 hover:from-red-600 hover:to-rose-700 rounded-xl transition-all shadow-md shadow-red-500/25 hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <span>Kontrol Tahapan KMST</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- 2. STATUS ROADMAP / TAHAPAN BERJALAN SAAT INI --}}
            @php
                $activeStage = collect($tahapanList ?? [])->firstWhere('is_active', true) ?? ($tahapanList[0] ?? null);
            @endphp
            @if($activeStage)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <div class="flex items-center gap-3.5">
                    <span class="flex items-center justify-center w-11 h-11 rounded-2xl bg-gradient-to-tr from-[#F5333F] to-rose-500 text-white font-black text-base shadow-md shadow-red-500/20 flex-shrink-0">
                        @if(($activeStage['icon_type'] ?? '') === 'trophy') 🏆 @else {{ $activeStage['nomor'] ?? '1' }} @endif
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Tahapan Kompetisi KMST Aktif:</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ $activeStage['badge'] ?? 'Tahap Aktif' }}
                            </span>
                        </div>
                        <h4 class="text-base font-black text-slate-900 dark:text-white mt-0.5">
                            Tahap {{ $activeStage['nomor'] ?? '1' }}: {{ $activeStage['judul'] ?? 'Pendaftaran & Upload' }}
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                            {{ $activeStage['deskripsi'] ?? '' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('settings.index', ['tab' => 'tahapan']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-black text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-red-50 hover:text-[#F5333F] dark:hover:bg-slate-700 rounded-xl transition-all shadow-xs">
                        <svg class="w-4 h-4 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path></svg>
                        <span>Ubah / Beralih Tahapan &rarr;</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- 3. KPI METRIC CARDS (6 Kategori Resmi Proporsional) --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                
                {{-- Card Total --}}
                <div class="{{ $card }} p-4 sm:p-5 relative overflow-hidden group hover:border-[#F5333F]/60 hover:-translate-y-1.5 border-l-4 border-l-[#F5333F] cursor-pointer"
                     @click="mainTab = 'pendaftar'">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black tracking-wider uppercase text-slate-400">Total Makalah</span>
                        <span class="p-2 rounded-xl bg-red-50 text-[#F5333F] dark:bg-red-500/10 group-hover:rotate-6 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </span>
                    </div>
                    <div class="mt-2.5">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $stats['total'] }}</span>
                        <span class="block text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">100% Seluruh Kategori</span>
                    </div>
                </div>

                {{-- Card SS Office --}}
                <div class="{{ $card }} p-4 sm:p-5 relative overflow-hidden group hover:border-amber-400 hover:-translate-y-1.5 border-l-4 border-l-amber-500 cursor-pointer"
                     @click="window.location.href='{{ route('busdev.innovation.index', ['tab' => 'pendaftar', 'kategori' => 'SS (Office)']) }}'">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black tracking-wider uppercase text-amber-600 dark:text-amber-400">SS (Office)</span>
                        <span class="p-2 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 group-hover:rotate-6 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </span>
                    </div>
                    <div class="mt-2.5">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $stats['ss_office'] }}</span>
                        <span class="block text-[11px] font-bold text-amber-600 dark:text-amber-400 mt-0.5">
                            {{ $stats['total'] > 0 ? round(($stats['ss_office'] / $stats['total']) * 100) : 0 }}% Porsi
                        </span>
                    </div>
                </div>

                {{-- Card SS Plant --}}
                <div class="{{ $card }} p-4 sm:p-5 relative overflow-hidden group hover:border-emerald-400 hover:-translate-y-1.5 border-l-4 border-l-emerald-500 cursor-pointer"
                     @click="window.location.href='{{ route('busdev.innovation.index', ['tab' => 'pendaftar', 'kategori' => 'SS (Plant)']) }}'">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black tracking-wider uppercase text-emerald-600 dark:text-emerald-400">SS (Plant)</span>
                        <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 group-hover:rotate-6 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path></svg>
                        </span>
                    </div>
                    <div class="mt-2.5">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $stats['ss_plant'] }}</span>
                        <span class="block text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                            {{ $stats['total'] > 0 ? round(($stats['ss_plant'] / $stats['total']) * 100) : 0 }}% Porsi
                        </span>
                    </div>
                </div>

                {{-- Card GKM --}}
                <div class="{{ $card }} p-4 sm:p-5 relative overflow-hidden group hover:border-blue-400 hover:-translate-y-1.5 border-l-4 border-l-blue-500 cursor-pointer"
                     @click="window.location.href='{{ route('busdev.innovation.index', ['tab' => 'pendaftar', 'kategori' => 'GKM']) }}'">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black tracking-wider uppercase text-blue-600 dark:text-blue-400">GKM</span>
                        <span class="p-2 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 group-hover:rotate-6 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>
                        </span>
                    </div>
                    <div class="mt-2.5">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $stats['gkm'] }}</span>
                        <span class="block text-[11px] font-bold text-blue-600 dark:text-blue-400 mt-0.5">
                            {{ $stats['total'] > 0 ? round(($stats['gkm'] / $stats['total']) * 100) : 0 }}% Porsi
                        </span>
                    </div>
                </div>

                {{-- Card PKM --}}
                <div class="{{ $card }} p-4 sm:p-5 relative overflow-hidden group hover:border-purple-400 hover:-translate-y-1.5 border-l-4 border-l-purple-500 cursor-pointer"
                     @click="window.location.href='{{ route('busdev.innovation.index', ['tab' => 'pendaftar', 'kategori' => 'PKM']) }}'">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black tracking-wider uppercase text-purple-600 dark:text-purple-400">PKM</span>
                        <span class="p-2 rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/10 group-hover:rotate-6 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path></svg>
                        </span>
                    </div>
                    <div class="mt-2.5">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $stats['pkm'] }}</span>
                        <span class="block text-[11px] font-bold text-purple-600 dark:text-purple-400 mt-0.5">
                            {{ $stats['total'] > 0 ? round(($stats['pkm'] / $stats['total']) * 100) : 0 }}% Porsi
                        </span>
                    </div>
                </div>

                {{-- Card BI (Breakthrough) --}}
                <div class="{{ $card }} p-4 sm:p-5 relative overflow-hidden group hover:border-rose-400 hover:-translate-y-1.5 border-l-4 border-l-rose-500 cursor-pointer"
                     @click="window.location.href='{{ route('busdev.innovation.index', ['tab' => 'pendaftar', 'kategori' => 'BI']) }}'">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black tracking-wider uppercase text-rose-600 dark:text-rose-400">Breakthrough (BI)</span>
                        <span class="p-2 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 group-hover:rotate-6 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </span>
                    </div>
                    <div class="mt-2.5">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $stats['bi'] }}</span>
                        <span class="block text-[11px] font-bold text-rose-600 dark:text-rose-400 mt-0.5">
                            {{ $stats['total'] > 0 ? round(($stats['bi'] / $stats['total']) * 100) : 0 }}% Porsi
                        </span>
                    </div>
                </div>

            </div>

            {{-- 4. INTERACTIVE ANALYTICS & CHARTS SECTION (Grid 2 Kolom Lega & Sinkron 100%) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {{-- Grafik 1: Sebaran 5 Kategori (Donut Chart - 5 Cols) --}}
                <div class="{{ $card }} p-5 sm:p-6 lg:col-span-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#F5333F]"></span>
                                Sebaran 5 Kategori Inovasi
                            </h3>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                KMST 2026/2027
                            </span>
                        </div>

                        <div class="relative flex justify-center items-center h-60 my-2">
                            <canvas id="categoryChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['total'] }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Karya Inovasi</span>
                            </div>
                        </div>
                    </div>

                    {{-- Category Legend Summary --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-4 mt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                            <span class="truncate text-slate-600 dark:text-slate-400">SS Office: <strong>{{ $stats['ss_office'] }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="truncate text-slate-600 dark:text-slate-400">SS Plant: <strong>{{ $stats['ss_plant'] }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                            <span class="truncate text-slate-600 dark:text-slate-400">GKM: <strong>{{ $stats['gkm'] }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shrink-0"></span>
                            <span class="truncate text-slate-600 dark:text-slate-400">PKM: <strong>{{ $stats['pkm'] }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
                            <span class="truncate text-slate-600 dark:text-slate-400">BI: <strong>{{ $stats['bi'] }}</strong></span>
                        </div>
                    </div>
                </div>

                {{-- Grafik 2: Tren & Komparasi Multi-Tahun (Bar Chart - 7 Cols - 100% SINKRON) --}}
                <div class="{{ $card }} p-5 sm:p-6 lg:col-span-7 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                    Komparasi &amp; Pertumbuhan Karya Inovasi Per Tahun
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Analisis keterlibatan insan inovator multi-periode (SS Plant, SS Office, GKM, PKM, BI).</p>
                            </div>
                            <span class="px-2.5 py-1 text-[10px] font-bold text-slate-600 bg-slate-100 dark:bg-slate-800 dark:text-slate-400 rounded-lg">
                                Historis Sinkron
                            </span>
                        </div>

                        <div class="relative w-full h-60 my-2">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>

                    <div class="pt-4 mt-2 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Data otomatis tersinkronisasi dengan 5 kategori resmi.</span>
                        </span>
                        <button type="button" @click="mainTab = 'pendaftar'" class="font-bold text-[#F5333F] hover:underline flex items-center gap-1 cursor-pointer">
                            <span>Lihat Rincian Data di Tabel &rarr;</span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- 5. RECENT SUBMISSIONS PREVIEW (Pendaftaran Terbaru) --}}
            <div class="{{ $card }} p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <span>⚡</span>
                            Pendaftaran Terbaru (5 Entri Terakhir)
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Makalah inovasi yang baru saja dikirim oleh karyawan.</p>
                    </div>
                    <button type="button" 
                            @click="mainTab = 'pendaftar'" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-[#F5333F] bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 rounded-xl transition-colors self-start sm:self-auto cursor-pointer">
                        <span>Lihat Seluruh Tabel ({{ $stats['total'] }}) &rarr;</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse(($recentRegistrations ?? []) as $rec)
                        @php
                            $pesertaRec = (array) $rec->data_peserta;
                            $ketuaRec   = $pesertaRec['ketua'] ?? ($pesertaRec['ketua_nama'] ?? []);
                            $namaKetuaRec = is_array($ketuaRec) ? ($ketuaRec['nama'] ?? '-') : ($ketuaRec ?? '-');
                            $deptKetuaRec = is_array($ketuaRec) ? ($ketuaRec['dept'] ?? '-') : '-';
                            $katColor = match($rec->kategori) {
                                'SS (Office)', 'SS_Office' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'SS (Plant)', 'SS_Plant'   => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'GKM'                      => 'bg-blue-100 text-blue-800 border-blue-200',
                                'PKM'                      => 'bg-purple-100 text-purple-800 border-purple-200',
                                'BI'                       => 'bg-rose-100 text-rose-800 border-rose-200',
                                default                    => 'bg-slate-100 text-slate-800 border-slate-200',
                            };
                        @endphp
                        <div class="p-4 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 hover:border-red-300 dark:hover:border-slate-700 transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded-md border {{ $katColor }}">
                                        {{ $rec->kategori }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        {{ $rec->created_at ? $rec->created_at->format('d M Y') : '-' }}
                                    </span>
                                </div>
                                <h4 class="text-sm font-black text-slate-900 dark:text-white line-clamp-1">
                                    {{ $rec->nama_tim }}
                                </h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">
                                    Ketua: <strong class="text-slate-700 dark:text-slate-200">{{ $namaKetuaRec }}</strong>
                                </p>
                                <p class="text-[11px] text-slate-400 truncate">
                                    {{ $deptKetuaRec }}
                                </p>
                            </div>

                            <div class="pt-3 mt-3 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between">
                                @if($rec->file_makalah)
                                    <a href="{{ route('busdev.innovation.makalah', $rec->id) }}" target="_blank" class="text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:text-[#F5333F] flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span>Buka Makalah</span>
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Tanpa Berkas</span>
                                @endif

                                <button type="button" 
                                        @click="mainTab = 'pendaftar'" 
                                        class="text-[11px] font-bold text-[#F5333F] hover:underline">
                                    Buka di Tabel &rarr;
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-6 text-slate-400 text-xs">
                            Belum ada berkas pendaftaran inovasi yang masuk.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ==================================================================== --}}
        {{-- TAB 2: TABEL & DATABASE PENDAFTAR (Full-Width, Search & Export)     --}}
        {{-- ==================================================================== --}}
        <div x-show="mainTab === 'pendaftar'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display: none;"
             class="space-y-6">

            <div class="{{ $card }} overflow-hidden">
                
                {{-- Header Tabel & Tombol Unduh Pintar Sesuai Filter --}}
                <div class="p-5 sm:p-6 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-red-100 text-[#F5333F] dark:bg-red-500/10">
                                Database Pendaftar
                            </span>
                            <span class="text-xs font-bold text-slate-400 font-mono">
                                Total: {{ $registrations->total() }} Tim Terdaftar
                            </span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mt-1">
                            Daftar Lengkap Pendaftar Konvensi Mutu Semen Tonasa
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Filter berdasarkan kategori, cari nama tim/peserta, dan unduh data sesuai tahun yang Anda pilih.
                        </p>
                    </div>

                    {{-- Tombol Unduh Data Sesuai Filter Aktif (Best Practice) --}}
                    <div class="flex items-center gap-2.5 flex-shrink-0">
                        <a href="{{ route('busdev.innovation.export', ['kategori' => $activeTab, 'tahun' => $activeTahun]) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-black text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path></svg>
                            <span>
                                @if($activeTahun !== 'Semua')
                                    Unduh Data (Tahun {{ $activeTahun }}) .CSV
                                @else
                                    Unduh Semua Data (Seluruh Tahun) .CSV
                                @endif
                            </span>
                        </a>
                    </div>
                </div>

                {{-- Folder Navigation Sub-tabs (5 Kategori Resmi) --}}
                <div class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 px-3 sm:px-6 pt-3 flex items-center gap-2 overflow-x-auto hide-scrollbar">
                    
                    @php
                        $tabs = [
                            ['id' => 'Semua', 'label' => 'Semua Karya', 'count' => $stats['total'], 'color' => 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'],
                            ['id' => 'SS (Office)', 'label' => 'SS (Office)', 'count' => $stats['ss_office'], 'color' => 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200'],
                            ['id' => 'SS (Plant)', 'label' => 'SS (Plant)', 'count' => $stats['ss_plant'], 'color' => 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200'],
                            ['id' => 'GKM', 'label' => 'GKM', 'count' => $stats['gkm'], 'color' => 'bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-200'],
                            ['id' => 'PKM', 'label' => 'PKM', 'count' => $stats['pkm'], 'color' => 'bg-purple-100 dark:bg-purple-900/60 text-purple-800 dark:text-purple-200'],
                            ['id' => 'BI', 'label' => 'Breakthrough (BI)', 'count' => $stats['bi'], 'color' => 'bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-200'],
                        ];
                    @endphp

                    @foreach($tabs as $t)
                        @php
                            $isActive = ($activeTab === $t['id']);
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['tab' => 'pendaftar', 'kategori' => $t['id'], 'page' => 1]) }}"
                           class="relative flex items-center gap-2 px-4 py-3 text-xs font-bold rounded-t-2xl transition-all whitespace-nowrap {{ $isActive ? 'bg-white dark:bg-slate-900 text-[#F5333F] border-t-2 border-[#F5333F] shadow-sm font-extrabold' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/60 dark:hover:bg-slate-800/40' }}">
                            <span>{{ $t['label'] }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono {{ $t['color'] }}">
                                {{ $t['count'] }}
                            </span>
                        </a>
                    @endforeach
                </div>

                {{-- Filter & Search Toolbar --}}
                <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                    <form method="GET" action="{{ route('busdev.innovation.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                        <input type="hidden" name="tab" value="pendaftar">
                        <input type="hidden" name="kategori" value="{{ $activeTab }}">
                        
                        {{-- Search Input --}}
                        <div class="relative flex-1 max-w-md">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tim, nama peserta, nomor SAP..."
                                   class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white dark:focus:bg-slate-900 focus:border-[#F5333F] focus:ring-2 focus:ring-red-100 focus:outline-none dark:text-white transition-all">
                        </div>

                        {{-- Dropdown Filters --}}
                        <div class="flex items-center gap-2">
                            {{-- Filter Tahun --}}
                            <select name="tahun" onchange="this.form.submit()" class="px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:border-[#F5333F]">
                                <option value="Semua">Semua Tahun</option>
                                @foreach($availableYears as $thn)
                                    <option value="{{ $thn }}" {{ $activeTahun == $thn ? 'selected' : '' }}>Tahun {{ $thn }}</option>
                                @endforeach
                            </select>

                            {{-- Filter Urutan --}}
                            <select name="sort" onchange="this.form.submit()" class="px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:border-[#F5333F]">
                                <option value="terbaru" {{ $activeSort === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                                <option value="terlama" {{ $activeSort === 'terlama' ? 'selected' : '' }}>Terlama</option>
                            </select>

                            <button type="submit" class="px-3.5 py-2 text-xs font-bold text-white bg-slate-900 dark:bg-slate-800 hover:bg-[#F5333F] dark:hover:bg-[#F5333F] rounded-xl transition-colors cursor-pointer">
                                Filter
                            </button>

                            @if(request()->hasAny(['search', 'tahun', 'sort']) && (request('search') || request('tahun') !== 'Semua' || request('sort') !== 'terbaru'))
                                <a href="{{ route('busdev.innovation.index', ['tab' => 'pendaftar', 'kategori' => $activeTab]) }}" class="p-2 text-slate-400 hover:text-red-500 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Reset Filter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Full-Width Table View (Mewah, Rapi, & Bersih) --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/50 text-[10px] font-black uppercase text-slate-400 border-b border-slate-200 dark:border-slate-800 tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4 text-center w-12">No.</th>
                                <th class="py-3.5 px-4">Nama Tim &amp; Terdaftar</th>
                                <th class="py-3.5 px-4 text-center">Kategori</th>
                                <th class="py-3.5 px-4">Ketua Tim &amp; Unit Kerja</th>
                                <th class="py-3.5 px-4 text-center">Anggota</th>
                                <th class="py-3.5 px-4">Bidang Manfaat</th>
                                <th class="py-3.5 px-4 text-center">Makalah</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @forelse($registrations as $index => $reg)
                                @php
                                    $peserta = (array) $reg->data_peserta;
                                    $ketua   = $peserta['ketua'] ?? ($peserta['ketua_nama'] ?? []);
                                    $namaKetua = is_array($ketua) ? ($ketua['nama'] ?? '-') : ($ketua ?? '-');
                                    $sapKetua  = is_array($ketua) ? ($ketua['sap'] ?? '-') : '-';
                                    $deptKetua = is_array($ketua) ? ($ketua['dept'] ?? '-') : '-';
                                    $ukKetua   = is_array($ketua) ? ($ketua['uk'] ?? '-') : '-';
                                    
                                    // Hitung anggota
                                    $allMembers = [];
                                    foreach ($peserta as $roleKey => $item) {
                                        if (is_array($item) && !empty($item['nama'])) {
                                            $badgeClass = match($roleKey) {
                                                'fasilitator' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'ketua'       => 'bg-red-50 text-red-700 border-red-200 font-bold',
                                                default       => 'bg-slate-50 text-slate-600 border-slate-200',
                                            };
                                            $allMembers[] = [
                                                'peran'       => ucfirst($roleKey),
                                                'peran_badge' => $badgeClass,
                                                'nama'        => $item['nama'] ?? '-',
                                                'sap'         => $item['sap'] ?? '-',
                                                'dept'        => $item['dept'] ?? '-',
                                                'uk'          => $item['uk'] ?? '-',
                                                'hp'          => $item['hp'] ?? '-',
                                            ];
                                        }
                                    }

                                    $ext = strtolower(pathinfo($reg->file_makalah ?? '', PATHINFO_EXTENSION) ?: 'docx');
                                    $katClean = str_replace(['/', '\\'], '-', $reg->kategori);
                                    $timClean = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $reg->nama_tim);
                                    $thn      = $reg->created_at ? $reg->created_at->format('Y') : date('Y');
                                    $customTitle = "{$katClean}_{$timClean}_{$thn}.{$ext}";

                                    $modalData = [
                                        'id'           => $reg->id,
                                        'kategori'     => $reg->kategori,
                                        'nama_tim'     => $reg->nama_tim,
                                        'user_name'    => $reg->user?->name ?? 'User #' . $reg->user_id,
                                        'user_email'   => $reg->user?->email ?? '-',
                                        'created_at'   => $reg->created_at ? $reg->created_at->format('d M Y, H:i') : '-',
                                        'benefit'      => is_array($reg->bidang_benefit) ? $reg->bidang_benefit : [],
                                        'file_url'     => $reg->file_makalah ? route('busdev.innovation.makalah', $reg->id) : null,
                                        'download_url' => $reg->file_makalah ? route('busdev.innovation.makalah.download', $reg->id) : null,
                                        'filename'     => $customTitle,
                                        'members'      => $allMembers,
                                    ];

                                    $catBadge = match($reg->kategori) {
                                        'SS (Office)', 'SS_Office' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
                                        'SS (Plant)', 'SS_Plant'   => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
                                        'GKM'                      => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
                                        'PKM'                      => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800',
                                        'BI'                       => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
                                        default                    => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                                    
                                    {{-- No Urut --}}
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-400">
                                        {{ ($registrations->currentPage() - 1) * $registrations->perPage() + $loop->iteration }}
                                    </td>

                                    {{-- Nama Tim & Waktu Daftar --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-black text-slate-900 dark:text-white group-hover:text-[#F5333F] transition-colors">
                                            {{ $reg->nama_tim }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-medium mt-0.5 flex items-center gap-1.5">
                                            <span>📅</span>
                                            <span>Terdaftar: {{ $reg->created_at ? $reg->created_at->format('d M Y, H:i') : '-' }}</span>
                                        </div>
                                    </td>

                                    {{-- Kategori Resmi --}}
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-black uppercase rounded-lg border {{ $catBadge }}">
                                            {{ $reg->kategori }}
                                        </span>
                                    </td>

                                    {{-- Ketua Tim & Unit Kerja --}}
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                                {{ strtoupper(substr($namaKetua, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 dark:text-white truncate max-w-xs">
                                                    {{ $namaKetua }}
                                                </div>
                                                <div class="text-[10px] text-slate-400 truncate max-w-xs">
                                                    {{ $deptKetua !== '-' ? $deptKetua : ($reg->user?->name ?? 'User') }}
                                                    @if($ukKetua && $ukKetua !== '-') &bull; {{ $ukKetua }} @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Anggota Tim Count --}}
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            <span>👥</span>
                                            <span>{{ count($allMembers) }} Org</span>
                                        </span>
                                    </td>

                                    {{-- Bidang Manfaat --}}
                                    <td class="py-3.5 px-4">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @if(!empty($reg->bidang_benefit) && is_array($reg->bidang_benefit))
                                                @foreach(array_slice($reg->bidang_benefit, 0, 2) as $b)
                                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
                                                        {{ $b }}
                                                    </span>
                                                @endforeach
                                                @if(count($reg->bidang_benefit) > 2)
                                                    <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                                        +{{ count($reg->bidang_benefit) - 2 }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Makalah File Link (Buka di Tab Chrome Baru) --}}
                                    <td class="py-3.5 px-4 text-center">
                                        @if($reg->file_makalah)
                                            <a href="{{ route('busdev.innovation.makalah', $reg->id) }}" target="_blank" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-[#F5333F] bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 rounded-lg transition-colors cursor-pointer"
                                               title="Buka Makalah di Tab Chrome Baru: {{ $customTitle }}">
                                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                <span>Makalah</span>
                                            </a>
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">Kosong</span>
                                        @endif
                                    </td>

                                    {{-- Aksi Buka Detail --}}
                                    <td class="py-3.5 px-4 text-center">
                                        <button type="button" 
                                                @click='selectedReg = @json($modalData)'
                                                class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg transition-colors cursor-pointer">
                                            <span>Detail Tim</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path></svg>
                                        </button>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12 text-slate-400">
                                        <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                                        <p class="font-bold text-slate-700 dark:text-slate-300">Belum ada karya terdaftar</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Tidak ditemukan pendaftaran pada kategori atau kata kunci pencarian ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        Menampilkan <strong>{{ $registrations->firstItem() ?? 0 }}</strong> sampai <strong>{{ $registrations->lastItem() ?? 0 }}</strong> dari <strong>{{ $registrations->total() }}</strong> pendaftar
                    </div>
                    <div>
                        {{ $registrations->appends(['tab' => 'pendaftar', 'kategori' => $activeTab, 'tahun' => $activeTahun, 'search' => request('search'), 'sort' => $activeSort])->links() }}
                    </div>
                </div>

            </div>

        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL DETAIL TIM INOVATOR (LIGHTBOX POPUP)                           --}}
        {{-- ==================================================================== --}}
        <div x-show="selectedReg" 
             x-transition.opacity 
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-xs"
             style="display: none;">
            
            <div @click.away="selectedReg = null" 
                 class="relative w-full max-w-3xl max-h-[90vh] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col">
                
                <template x-if="selectedReg">
                    <div class="flex flex-col h-full overflow-hidden">
                        
                        {{-- Header Modal --}}
                        <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/60 flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2.5 py-0.5 text-[10px] font-black uppercase rounded-lg bg-red-100 text-[#F5333F] dark:bg-red-500/10" x-text="selectedReg.kategori"></span>
                                    <span class="text-xs text-slate-400" x-text="'Terdaftar: ' + selectedReg.created_at"></span>
                                </div>
                                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white" x-text="selectedReg.nama_tim"></h3>
                            </div>
                            <button type="button" @click="selectedReg = null" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        {{-- Body Modal (Scrollable) --}}
                        <div class="p-5 sm:p-6 overflow-y-auto space-y-6 flex-1">
                            
                            {{-- Info Akun & Benefit --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                
                                {{-- Akun Pendaftar --}}
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-1.5">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Akun Pengirim Berkas</span>
                                    <p class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                        <span>👤</span>
                                        <span x-text="selectedReg.user_name"></span>
                                    </p>
                                    <p class="text-xs text-slate-500 font-mono" x-text="selectedReg.user_email"></p>
                                </div>

                                {{-- Bidang Manfaat (Benefit) --}}
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-1.5">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Bidang Manfaat (Benefit)</span>
                                    <div class="flex flex-wrap gap-1.5 pt-1">
                                        <template x-for="b in selectedReg.benefit" :key="b">
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800" x-text="b"></span>
                                        </template>
                                        <template x-if="!selectedReg.benefit || selectedReg.benefit.length === 0">
                                            <span class="text-xs text-slate-400 italic">Tidak ada bidang manfaat terpilih</span>
                                        </template>
                                    </div>
                                </div>

                            </div>

                            {{-- Personel Tim --}}
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>
                                        Susunan Personel Tim
                                    </h4>
                                    <span class="text-xs font-bold text-slate-400">Total: <strong class="text-slate-700 dark:text-slate-200" x-text="selectedReg.members.length + ' Orang'"></strong></span>
                                </div>

                                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs text-left">
                                            <thead class="bg-slate-50 dark:bg-slate-900/60 text-[10px] font-black uppercase text-slate-400 border-b border-slate-200 dark:border-slate-800 tracking-wider">
                                                <tr>
                                                    <th class="px-4 py-3">Peran Tim</th>
                                                    <th class="px-4 py-3">Nama Lengkap</th>
                                                    <th class="px-4 py-3">No. SAP</th>
                                                    <th class="px-4 py-3">Departemen</th>
                                                    <th class="px-4 py-3">Unit Kerja</th>
                                                    <th class="px-4 py-3">Kontak / HP</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                                <template x-for="m in selectedReg.members" :key="m.peran + m.sap + m.nama">
                                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-md border" :class="m.peran_badge" x-text="m.peran"></span>
                                                        </td>
                                                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-white whitespace-nowrap" x-text="m.nama"></td>
                                                        <td class="px-4 py-3 font-mono text-slate-500 dark:text-slate-400 whitespace-nowrap" x-text="m.sap"></td>
                                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300" x-text="m.dept"></td>
                                                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400" x-text="m.uk"></td>
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            <template x-if="m.hp && m.hp !== '-'">
                                                                <a :href="'https://wa.me/' + m.hp.replace(/[^0-9]/g, '')" target="_blank" class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                                                    <span>💬</span>
                                                                    <span x-text="m.hp"></span>
                                                                </a>
                                                            </template>
                                                            <template x-if="!m.hp || m.hp === '-'">
                                                                <span class="text-slate-400">-</span>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Footer Modal --}}
                        <div class="p-4 px-6 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-900/40">
                            <div class="flex flex-col">
                                <span class="text-xs text-slate-400">ID Registrasi: #<strong x-text="selectedReg.id"></strong></span>
                                <template x-if="selectedReg.filename">
                                    <span class="text-[10px] text-slate-500 font-mono truncate max-w-xs" x-text="selectedReg.filename"></span>
                                </template>
                            </div>
                            <div class="flex items-center gap-2">
                                <template x-if="selectedReg.file_url">
                                    <a :href="selectedReg.file_url" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer">
                                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span>Buka di Tab Baru</span>
                                    </a>
                                </template>
                                <template x-if="selectedReg.download_url">
                                    <a :href="selectedReg.download_url" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-[#F5333F] hover:bg-red-700 rounded-xl transition-colors shadow-xs cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path></svg>
                                        <span>Unduh Makalah</span>
                                    </a>
                                </template>
                                <button type="button" @click="selectedReg = null" class="px-4 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors cursor-pointer">
                                    Tutup
                                </button>
                            </div>
                        </div>

                    </div>
                </template>

            </div>
        </div>

    </div>

    {{-- Chart.js Scripts (Sinkron 100% dengan Kategori Resmi) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94a3b8' : '#475569'; 
            const gridColor = isDark ? '#334155' : '#f1f5f9';

            // 1. Chart Donat (5 Kategori Resmi)
            const catCanvas = document.getElementById('categoryChart');
            if (catCanvas) {
                const ctxCat = catCanvas.getContext('2d');
                window.categoryChartInstance = new Chart(ctxCat, {
                    type: 'doughnut',
                    data: {
                        labels: ['SS (Office)', 'SS (Plant)', 'GKM', 'PKM', 'BI'],
                        datasets: [{
                            data: [
                                {{ $stats['ss_office'] }}, 
                                {{ $stats['ss_plant'] }}, 
                                {{ $stats['gkm'] }}, 
                                {{ $stats['pkm'] }}, 
                                {{ $stats['bi'] }}
                            ],
                            backgroundColor: ['#F59E0B', '#10B981', '#3B82F6', '#A855F7', '#F43F5E'],
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            // 2. Chart Batang Bertumpuk (Tren Multi-Tahun 5 Kategori - 100% SINKRON)
            const trendCanvas = document.getElementById('trendChart');
            if (trendCanvas) {
                const ctxTrend = trendCanvas.getContext('2d');
                window.trendChartInstance = new Chart(ctxTrend, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($trendChart['labels']) !!},
                        datasets: [
                            { label: 'SS (Office)', data: {!! json_encode($trendChart['ss_office']) !!}, backgroundColor: '#F59E0B', borderRadius: 4 },
                            { label: 'SS (Plant)', data: {!! json_encode($trendChart['ss_plant']) !!}, backgroundColor: '#10B981', borderRadius: 4 },
                            { label: 'GKM', data: {!! json_encode($trendChart['gkm']) !!}, backgroundColor: '#3B82F6', borderRadius: 4 },
                            { label: 'PKM', data: {!! json_encode($trendChart['pkm']) !!}, backgroundColor: '#A855F7', borderRadius: 4 },
                            { label: 'BI', data: {!! json_encode($trendChart['bi']) !!}, backgroundColor: '#F43F5E', borderRadius: 4 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { 
                                position: 'bottom', 
                                labels: { usePointStyle: true, boxWidth: 8, color: textColor, font: { size: 11, weight: 'bold' } } 
                            },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            y: { stacked: true, beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } } },
                            x: { stacked: true, grid: { display: false }, ticks: { color: textColor, font: { size: 11, weight: 'bold' } } }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
