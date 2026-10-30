<x-app-layout>
    <x-slot name="header">
        @if($viewMode === 'employee')

                <!-- 1. HERO EXECUTIVE SECTION (BERSIH, LAPANG & MODERN ALA BUMN FLAGSHIP) -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-10 shadow-xs transition-all">
                    
                    <!-- Header Atas: Kategori, Sambutan & Selengkapnya -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pb-8 border-b border-slate-100 dark:border-slate-800">
                        
                        <!-- Kolom Kiri: Eyebrow + Judul Utama -->
                        <div class="lg:col-span-7 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#F5333F] dark:text-red-400">
                                    PORTAL KARYAWAN SEMEN TONASA (2026)
                                </span>
                                <span class="text-slate-300 dark:text-slate-700">•</span>
                                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                </span>
                            </div>

                            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white font-headline leading-tight">
                                Selamat Datang, {{ $user->name }}
                            </h1>

                            <div class="flex flex-wrap items-center gap-2.5 pt-1 text-xs text-slate-600 dark:text-slate-400">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 font-bold text-slate-800 dark:text-slate-200">
                                    <svg class="w-3.5 h-3.5 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    {{ $user->unit?->name_unit ?? 'Unit Kerja Semen Tonasa' }}
                                </span>
                                @if(isset($karyawan) && $karyawan && $karyawan->no_sap)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 font-mono font-semibold text-slate-700 dark:text-slate-300">
                                        SAP: {{ $karyawan->no_sap }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 font-semibold border border-emerald-200/60">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Status Akun Aktif
                                </span>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Deskripsi Ringkas + Tombol Pill Minimalis -->
                        <div class="lg:col-span-5 flex flex-col sm:flex-row lg:flex-col justify-between items-start lg:items-end gap-5">
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed text-left lg:text-right">
                                Ruang kerja mandiri digital insan PT Semen Tonasa. Akses pendaftaran inovasi mutu (KMST), pantau layanan kesehatan klinik (STMC), dan kelola data profil Anda.
                            </p>

                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2.5 px-6 py-2.5 rounded-full border border-slate-300 dark:border-slate-700 hover:border-slate-900 dark:hover:border-white text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-slate-900 transition-all duration-300 shadow-2xs group">
                                <span>Kelola Profil &amp; Akun</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>

                    </div>

                    <!-- 2. METRIK INDIKATOR UTAMA (TYPOGRAPHY BESAR & LAPANG SEPERTI PERTAMINA FLAGSHIP) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8">
                        
                        <!-- Metrik 1: Layanan Kesehatan STMC -->
                        <div class="space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">
                                LAYANAN KESEHATAN (STMC)
                            </span>
                            <div class="text-2xl sm:text-3xl font-headline font-black text-slate-900 dark:text-white tracking-tight">
                                @if($userMcu)
                                    {{ ucfirst($userMcu->status ?? 'Terjadwal') }}
                                @else
                                    Siap Layanan
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                @if($userMcu)
                                    Jadwal MCU: {{ \Carbon\Carbon::parse($userMcu->tanggal_mcu)->translatedFormat('d F Y') }}
                                @else
                                    Klinik Semen Tonasa Biringere siaga 24 jam untuk keluarga &amp; karyawan.
                                @endif
                            </p>
                        </div>

                        <!-- Metrik 2: Program Inovasi KMST -->
                        <div class="space-y-2 md:border-l md:border-slate-100 dark:md:border-slate-800 md:pl-8">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">
                                INOVASI MUTU (KMST 2026/2027)
                            </span>
                            <div class="text-2xl sm:text-3xl font-headline font-black text-slate-900 dark:text-white tracking-tight">
                                @if($userKmstRegistration)
                                    {{ $userKmstRegistration->kategori }}
                                @else
                                    5 Kategori
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                @if($userKmstRegistration)
                                    Terdaftar di Tim: <strong class="text-slate-800 dark:text-slate-200">{{ $userKmstRegistration->nama_tim }}</strong>
                                @else
                                    Terbuka SS (Office &amp; Plant), GKM, PKM, dan Breakthrough (BI).
                                @endif
                            </p>
                        </div>

                        <!-- Metrik 3: Kepatuhan & Budaya Organisasi -->
                        <div class="space-y-2 md:border-l md:border-slate-100 dark:md:border-slate-800 md:pl-8">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">
                                UNIT KERJA &amp; DEPARTEMEN
                            </span>
                            <div class="text-2xl sm:text-3xl font-headline font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                                <span>{{ $user->unit?->code_unit ?? 'PTST' }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-md bg-red-50 text-[#F5333F] font-bold border border-red-200/60 font-sans">Tonasa</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                {{ $user->unit?->department?->name_department ?? 'PT Semen Tonasa • Holding SIG' }}
                            </p>
                        </div>

                    </div>

                </div>

                <!-- 3. DUA MODUL UTAMA (KMST INOVASI & LAYANAN KESEHATAN STMC) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                    
                    <!-- KARTU 1: PROGRAM INOVASI KMST -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 flex flex-col justify-between shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-[#F5333F] dark:bg-red-500/10 dark:text-red-400 text-xs font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.509 1.333 1.509 2.316V18"></path></svg>
                                    KMST Inovasi 2026/2027
                                </span>
                                @if($userKmstRegistration)
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Sudah Mendaftar
                                    </span>
                                @else
                                    <span class="text-xs font-medium text-slate-400">Pendaftaran Terbuka</span>
                                @endif
                            </div>

                            <h3 class="text-xl font-headline font-bold text-slate-900 dark:text-white">
                                Konvensi Mutu Semen Tonasa (KMST)
                            </h3>

                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                Kirimkan gagasan dan karya inovasi tim Anda untuk efisiensi operasional dan keandalan pabrik. Unduh template makalah resmi:
                            </p>

                            <!-- Unduh Template Singkat & Bersih -->
                            <div class="flex flex-wrap gap-2 pt-1">
                                <a href="{{ route('inovasi.download-template', ['kategori' => 'SS_Office']) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold border border-slate-200/70 dark:border-slate-700 transition">
                                    📄 SS (Office)
                                </a>
                                <a href="{{ route('inovasi.download-template', ['kategori' => 'SS_Plant']) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold border border-slate-200/70 dark:border-slate-700 transition">
                                    📄 SS (Plant)
                                </a>
                                <a href="{{ route('inovasi.download-template', ['kategori' => 'GKM']) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold border border-slate-200/70 dark:border-slate-700 transition">
                                    📄 GKM
                                </a>
                                <a href="{{ route('inovasi.download-template', ['kategori' => 'PKM']) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold border border-slate-200/70 dark:border-slate-700 transition">
                                    📄 PKM
                                </a>
                                <a href="{{ route('inovasi.download-template', ['kategori' => 'BI']) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold border border-slate-200/70 dark:border-slate-700 transition">
                                    📄 BI
                                </a>
                            </div>
                        </div>

                        <!-- Aksi Tombol KMST -->
                        <div class="pt-6 mt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                            <a href="{{ route('inovasi.portal') }}" class="text-xs font-bold text-[#F5333F] hover:text-red-700 transition flex items-center gap-1.5">
                                <span>Buka Halaman Inovasi</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            <a href="{{ route('inovasi.portal') }}#daftar" class="px-4 py-2 rounded-xl bg-[#F5333F] hover:bg-red-600 text-white text-xs font-bold transition shadow-sm">
                                Formulir Pendaftaran
                            </a>
                        </div>
                    </div>

                    <!-- KARTU 2: LAYANAN KESEHATAN KLINIK STMC -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 flex flex-col justify-between shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 text-xs font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    Layanan Kesehatan Tonasa
                                </span>
                                <span class="text-xs font-medium text-slate-400">Klinik Biringere 24 Jam</span>
                            </div>

                            <h3 class="text-xl font-headline font-bold text-slate-900 dark:text-white">
                                Semen Tonasa Medical Centre (STMC)
                            </h3>

                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                Fasilitas pemantauan kesehatan berkala (MCU), jadwal praktik dokter, konsultasi rawat jalan, dan aplikasi mobile STMC Health.
                            </p>

                            <!-- Status MCU Mini Box -->
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <div class="space-y-0.5">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Jadwal MCU Tahunan Anda</span>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                        @if($userMcu)
                                            Tanggal: {{ \Carbon\Carbon::parse($userMcu->tanggal_mcu)->translatedFormat('d F Y') }} (Poli Umum)
                                        @else
                                            Belum ada jadwal MCU aktif periode ini
                                        @endif
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-[#F5333F] bg-white dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                                    STMC Mobile Ready
                                </span>
                            </div>
                        </div>

                        <!-- Aksi Tombol STMC -->
                        <div class="pt-6 mt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                Butuh darurat? Ext: <strong>1234</strong>
                            </span>
                            <span class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold">
                                Biringere • Pangkep
                            </span>
                        </div>
                    </div>

                </div>

                <!-- 4. FOOTNOTE BANTUAN & INFORMASI RESMI -->
                <div class="p-6 rounded-3xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span>Butuh bantuan terkait akun atau sistem informasi? Hubungi Helpdesk Dept. HC &amp; Services Semen Tonasa.</span>
                    </div>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Senin - Jumat (08.00 - 16.30 WITA)</span>
                </div>

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
