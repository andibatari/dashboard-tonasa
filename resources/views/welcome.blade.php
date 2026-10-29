<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HCGRC | Portal Terpadu Human Capital & GRC</title>

    <link rel="icon" type="image/png" href="{{ asset('images/tonasa-logo.png') }}?v=2">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; letter-spacing: -0.02em; }

        /* Animasi: Hero muncul bertahap saat halaman dibuka */
        .hero-in { opacity: 0; transform: translateY(18px); animation: heroIn .9s cubic-bezier(.2,.7,.2,1) forwards; }
        .hero-in.d1 { animation-delay: .1s; }
        .hero-in.d2 { animation-delay: .25s; }
        .hero-in.d3 { animation-delay: .4s; }
        .hero-in.d4 { animation-delay: .55s; }
        @keyframes heroIn { to { opacity: 1; transform: translateY(0); } }

        .float-slow { animation: floatSlow 7s ease-in-out infinite; }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0) rotate(-2deg); }
            50% { transform: translateY(-14px) rotate(1deg); }
        }

        /* Garis aksen merah saat baris unit kerja di-hover */
        .unit-row::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
            background: #F5333F; transform: scaleY(0); transform-origin: top;
            transition: transform .35s ease;
        }
        .unit-row:hover::before, .unit-row:focus-within::before { transform: scaleY(1); }

        @media (prefers-reduced-motion: reduce) {
            .hero-in { animation: none; opacity: 1; transform: none; }
            .float-slow { animation: none; }
            html { scroll-behavior: auto; }
        }
    </style>
</head>
<body class="antialiased text-slate-600 bg-slate-50 selection:bg-[#F5333F] selection:text-white">

    <!-- ================= NAVIGASI (LANDING PAGE HEADER) ================= -->
    <nav id="navbar" class="fixed inset-x-0 top-0 z-50 transition-all duration-300 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs"
         x-data="{ mobileNav: false }">
        <div class="relative flex items-center justify-end h-16 sm:h-20 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <!-- Menu Navigasi Desktop (Presisi di Tengah Halaman / Center of Screen) -->
            <div class="hidden lg:flex absolute left-1/2 -translate-x-1/2 items-center p-1 rounded-full bg-slate-100/90 border border-slate-200/70 backdrop-blur-md shadow-xs space-x-1">
                <a href="#" class="px-4 py-1.5 text-xs font-bold text-slate-700 hover:text-[#F5333F] hover:bg-white rounded-full transition-all duration-200 shadow-none hover:shadow-xs">
                    Beranda
                </a>
                <a href="#unit" class="px-4 py-1.5 text-xs font-bold text-slate-700 hover:text-[#F5333F] hover:bg-white rounded-full transition-all duration-200 shadow-none hover:shadow-xs">
                    Ekosistem Unit Kerja
                </a>
                <a href="#showcase" class="px-4 py-1.5 text-xs font-bold text-slate-700 hover:text-[#F5333F] hover:bg-white rounded-full transition-all duration-200 shadow-none hover:shadow-xs">
                    Kebanggaan Kita
                </a>
                <a href="#kontak" class="px-4 py-1.5 text-xs font-bold text-[#F5333F] bg-white rounded-full shadow-xs hover:bg-red-50 transition-all duration-200">
                    Hubungi Kami
                </a>
            </div>

            <!-- Bagian Kanan Header: Tombol Akses Portal -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2 text-xs sm:text-sm font-bold text-white transition bg-gradient-to-r from-[#F5333F] to-rose-600 rounded-full shadow-md shadow-red-500/25 hover:from-red-600 hover:to-rose-700 hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-500">
                            <span>Dashboard Saya</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:text-[#F5333F] transition-colors rounded-full hover:bg-slate-50">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-5 py-2 text-xs sm:text-sm font-bold text-white transition bg-gradient-to-r from-[#F5333F] to-rose-600 rounded-full shadow-md shadow-red-500/25 hover:from-red-600 hover:to-rose-700 hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-500">
                                <span>Daftar</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                        @endif
                    @endauth
                @endif

                <!-- Tombol Menu Mobile (Hamburger) -->
                <button @click="mobileNav = !mobileNav" class="lg:hidden p-2 text-slate-700 hover:text-[#F5333F] focus:outline-none rounded-xl hover:bg-slate-100" aria-label="Toggle navigation">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path x-show="!mobileNav" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileNav" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menu Drawer Mobile -->
        <div x-show="mobileNav" 
             x-transition 
             class="lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-2 shadow-2xl">
            <a href="#" @click="mobileNav = false" class="block px-4 py-2.5 text-sm font-bold text-slate-800 rounded-xl hover:bg-slate-50">Beranda</a>
            <a href="#unit" @click="mobileNav = false" class="block px-4 py-2.5 text-sm font-bold text-slate-800 rounded-xl hover:bg-slate-50">Ekosistem Unit Kerja</a>
            <a href="#showcase" @click="mobileNav = false" class="block px-4 py-2.5 text-sm font-bold text-slate-800 rounded-xl hover:bg-slate-50">Kebanggaan Kita</a>
            <a href="#kontak" @click="mobileNav = false" class="block px-4 py-2.5 text-sm font-bold text-[#F5333F] rounded-xl bg-red-50">Hubungi Kami</a>
        </div>
    </nav>

    <!-- ================= HERO ================= -->
    <header class="relative flex items-center min-h-screen overflow-hidden bg-center bg-cover bg-slate-900 pt-20"
            style="background-image: url('{{ asset('images/Jembatan Soekarno Manado.jpg') }}');">

        <!-- Overlay: gelap di kiri untuk keterbacaan teks, terbuka di kanan agar foto tetap terlihat -->
        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/55 to-black/20"></div>
        <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-slate-50 to-transparent"></div>

        <!-- Kemasan PCC 3D -->
        <div class="absolute hidden lg:block right-10 xl:right-24 bottom-24 w-[22rem] xl:w-[28rem] pointer-events-none float-slow">
            <img src="{{ asset('images/PCC-768x761.png') }}" alt="Semen Tonasa PCC" class="w-full h-auto drop-shadow-[0_30px_40px_rgba(0,0,0,.55)]">
        </div>

        <div class="relative z-10 w-full px-4 pt-20 pb-28 sm:pb-32 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <!-- Logo Tonasa di Bagian Hero (Sesuai Arahan Gambar: Berada di Atas Headline) -->
                <div class="mb-8 hero-in">
                    <div class="inline-flex items-center gap-4 px-5 py-3.5 rounded-2xl bg-black/50 backdrop-blur-xl border border-white/20 shadow-2xl group hover:border-red-500/50 transition-all duration-300">
                        <img src="{{ asset('images/tonasa-logo.png') }}" alt="PT Semen Tonasa" class="object-contain w-auto h-12 sm:h-14 drop-shadow-[0_4px_14px_rgba(0,0,0,0.7)] group-hover:scale-105 transition-transform duration-300">
                        <span class="w-px h-10 bg-white/20"></span>
                        <div class="flex flex-col">
                            <span class="text-sm sm:text-base font-extrabold tracking-wider uppercase text-white font-display">PT Semen Tonasa</span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-[#F5333F] animate-pulse"></span>
                                <span class="text-[10px] sm:text-[11px] font-bold text-red-400 tracking-widest uppercase">PORTAL TERPADU</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Headline Utama: Title Case Sesuai Kaidah Bahasa -->
                <h1 class="text-4xl font-extrabold leading-[1.08] text-white font-display sm:text-5xl lg:text-6xl hero-in d1">
                    Semua Operasional Unit, <span class="text-[#FF5A64]">dalam Satu Pintu.</span>
                </h1>

                <!-- Deskripsi Hero: Sentence Case -->
                <p class="max-w-xl mt-6 text-base leading-relaxed text-slate-200 sm:text-lg hero-in d2">
                    Pantau, evaluasi, dan kelola aktivitas antar unit dari satu platform terpadu, agar setiap keputusan didukung data yang sama.
                </p>

                <!-- Tombol CTA Hero: Title Case -->
                <div class="flex flex-col gap-3 mt-10 sm:flex-row hero-in d3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white transition bg-[#F5333F] rounded-full shadow-xl shadow-red-900/40 hover:bg-red-600 hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            Buka Ruang Kerja
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white transition bg-[#F5333F] rounded-full shadow-xl shadow-red-900/40 hover:bg-red-600 hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            Masuk ke Portal
                        </a>
                        <a href="#unit" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white transition border rounded-full border-white/40 bg-white/10 backdrop-blur hover:bg-white/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            Lihat Struktur Unit
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- ================= EKOSISTEM UNIT KERJA ================= -->
        @php
            $hcUnits = [
                [
                    'name'        => 'HC Planning & Development',
                    'abbr'        => 'HCPD',
                    'desc'        => 'Perencanaan strategi SDM, rekrutmen talenta, evaluasi kinerja karyawan, serta pemetaan kompetensi organisasi.',
                    'url'         => 'https://hcpd.sementonasa.co.id',
                    'has_website' => true,
                    'status_label'=> 'Website Aktif',
                    'btn_text'    => 'Buka Website',
                    'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                ],
                [
                    'name'        => 'HC Operational',
                    'abbr'        => 'HCO',
                    'desc'        => 'Pengelolaan administrasi kepegawaian, kompensasi, benefit, absensi terpadu, dan layanan operasional harian SDM.',
                    'url'         => '#',
                    'has_website' => false,
                    'status_label'=> 'Portal Layanan',
                    'btn_text'    => 'Kunjungi',
                    'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
                ],
                [
                    'name'        => 'Business & System Development',
                    'abbr'        => 'BUSDEV',
                    'desc'        => 'Pusat transformasi digital, pengelolaan inovasi karyawan (KMST), pemetaan proses bisnis, dan implementasi ICoFR.',
                    'url'         => route('busdev.bpm-icofr.index'),
                    'has_website' => true,
                    'status_label'=> 'Inovasi & ICoFR',
                    'btn_text'    => 'Buka Sistem',
                    'logo_img'    => asset('images/Logo Staff.png'),
                    'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                ],
                [
                    'name'        => 'GRC Management',
                    'abbr'        => 'GRC',
                    'desc'        => 'Pemantauan tata kelola perusahaan (GCG), manajemen risiko korporat, dan kepatuhan terhadap regulasi eksternal maupun internal.',
                    'url'         => 'https://grc.sementonasa.co.id',
                    'has_website' => true,
                    'status_label'=> 'Website Aktif',
                    'btn_text'    => 'Buka Website',
                    'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                ],
                [
                    'name'        => 'Health Service & Industrial Relations',
                    'abbr'        => 'HSIR',
                    'desc'        => 'Penyediaan layanan kesehatan kerja, asuransi, keselamatan kerja (K3), serta pengelolaan hubungan industrial yang harmonis.',
                    'url'         => '#',
                    'has_website' => false,
                    'status_label'=> 'Kesehatan & K3',
                    'btn_text'    => 'Kunjungi',
                    'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                ],
            ];
        @endphp

        <section id="unit" class="relative px-4 pt-12 pb-16 sm:pb-20 lg:pb-24 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">

                {{-- Sisi Kiri: Informasi Departemen --}}
                <div class="lg:col-span-4">
                    <div class="lg:sticky lg:top-28 space-y-6">
                        <div>
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-extrabold bg-red-50 text-[#F5333F] border border-red-200/80 tracking-wider uppercase shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-[#F5333F] animate-ping"></span>
                                Ekosistem Unit Kerja
                            </span>
                            <h2 class="mt-4 text-3xl font-extrabold leading-tight font-display text-slate-900 sm:text-4xl">
                                Departemen Human Capital &amp; GRC
                            </h2>
                            <div class="w-16 h-1 mt-5 rounded-full bg-[#F5333F]"></div>
                            <p class="mt-5 text-base leading-relaxed text-slate-600">
                                Lima unit strategis yang menggerakkan transformasi digital, pengembangan talenta SDM, inovasi perusahaan, dan kepatuhan tata kelola Semen Tonasa.
                            </p>
                        </div>

                        {{-- Kartu Info Panduan Navigasi Portal --}}
                        <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-lg shadow-slate-100 text-xs text-slate-600 space-y-3">
                            <div class="flex items-center gap-2.5 font-bold text-slate-900 text-sm">
                                <span class="flex items-center justify-center w-7 h-7 rounded-xl bg-red-50 text-[#F5333F] border border-red-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </span>
                                <span>Akses Portal Terintegrasi</span>
                            </div>
                            <p class="leading-relaxed text-slate-500">
                                Klik pada masing-masing kartu unit kerja untuk membuka situs web mandiri, sistem informasi, atau portal layanan terkait.
                            </p>
                            <div class="pt-2 flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-500 border-t border-slate-100">
                                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Website Mandiri</span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#F5333F] animate-pulse"></span> Ruang Kerja Sistem</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sisi Kanan: Daftar Tombol Unit Interaktif --}}
                <div class="lg:col-span-8">
                    <div class="overflow-hidden bg-white border divide-y shadow-2xl rounded-3xl border-slate-200/80 divide-slate-100 shadow-slate-200/50">
                        @foreach($hcUnits as $u)
                            <a href="{{ $u['url'] }}" 
                                @if(str_starts_with($u['url'], 'http')) target="_blank" rel="noopener noreferrer" @endif
                                class="group relative block p-6 sm:p-7 transition-all duration-300 unit-row hover:bg-gradient-to-r hover:from-red-50/50 hover:via-white hover:to-slate-50/80 focus:outline-none focus:ring-2 focus:ring-[#F5333F]/20">
                                
                                <div class="flex items-start sm:items-center gap-4 sm:gap-6">
                                    {{-- Icon Squircle dengan Hover Glow --}}
                                    <div class="flex items-center justify-center flex-none w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-red-50 to-red-100/60 text-[#F5333F] border border-red-100 group-hover:from-[#F5333F] group-hover:to-red-600 group-hover:text-white group-hover:scale-105 group-hover:shadow-lg group-hover:shadow-red-500/25 transition-all duration-300">
                                        <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            {!! $u['icon'] !!}
                                        </svg>
                                    </div>

                                    {{-- Konten Utama --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                            <h3 class="text-base sm:text-lg font-bold font-display text-slate-900 group-hover:text-[#F5333F] transition-colors duration-200">
                                                {{ $u['name'] }}
                                            </h3>
                                            <span class="px-2 py-0.5 text-[10px] font-black rounded-md bg-slate-100 text-slate-600 font-mono border border-slate-200/70">
                                                {{ $u['abbr'] }}
                                            </span>

                                            @if($u['has_website'])
                                                @if($u['abbr'] === 'BUSDEV')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-[#F5333F] border border-red-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F5333F] animate-pulse"></span>
                                                        {{ $u['status_label'] }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        {{ $u['status_label'] }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>

                                        <p class="text-xs sm:text-sm leading-relaxed text-slate-500 group-hover:text-slate-700 transition-colors">
                                            {{ $u['desc'] }}
                                        </p>
                                    </div>
                                    
                                    {{-- Tombol Aksi Kanan (Action Button Pill) --}}
                                    <div class="flex-none flex items-center self-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 text-xs font-bold rounded-xl text-slate-700 bg-slate-100/90 border border-slate-200 group-hover:bg-[#F5333F] group-hover:text-white group-hover:border-[#F5333F] group-hover:shadow-md group-hover:shadow-red-500/25 transition-all duration-300">
                                            <span class="hidden sm:inline">
                                                {{ $u['btn_text'] }}
                                            </span>
                                            <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                @if(str_starts_with($u['url'], 'http'))
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                                @endif
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= SHOWCASE (KEBANGGAAN KITA) ================= -->
        <section id="showcase" class="relative py-16 sm:py-20 lg:py-24 bg-gradient-to-b from-white via-slate-50/80 to-slate-100/50 text-slate-800 overflow-hidden border-t border-slate-200/60">
            <!-- Background Decorative Ambient Lighting -->
            <div class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-red-500/5 rounded-full blur-[150px] pointer-events-none"></div>
            <div class="absolute bottom-0 left-10 w-[500px] h-[500px] bg-slate-300/30 rounded-full blur-[140px] pointer-events-none"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:24px_24px] opacity-60 pointer-events-none"></div>

            <div class="relative z-10 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Section Header: Title Case Headline & Sentence Case Description -->
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-12">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-50 border border-red-200/80 text-[#F5333F] text-xs font-bold tracking-widest uppercase mb-4 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#F5333F] animate-pulse"></span>
                        Karya Nyata • Mahakarya Tonasa
                    </div>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 font-display tracking-tight leading-tight">
                        Membangun Negeri <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F5333F] via-rose-500 to-amber-500">Bersama</span>
                    </h2>
                    <p class="mt-4 text-base sm:text-lg text-slate-600 leading-relaxed">
                        Produk Semen Tonasa berkualitas tinggi menjadi fondasi kokoh mega proyek infrastruktur strategis nasional—dari bentang jembatan, tol, bendungan hingga pembangunan IKN.
                    </p>
                </div>

                <!-- Bento Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 auto-rows-[260px] sm:auto-rows-[280px]">

                    <!-- 1. Jembatan Soekarno Manado (Hero Card - Col Span 7, Row Span 2) -->
                    <figure class="relative overflow-hidden group rounded-[32px] border border-slate-200/80 bg-slate-900 shadow-xl shadow-slate-200/60 hover:shadow-2xl hover:border-red-500/40 ring-1 ring-slate-900/5 transition-all duration-500 md:col-span-2 lg:col-span-7 lg:row-span-2 flex flex-col justify-between p-6 sm:p-9 min-h-[400px] lg:min-h-[580px]">
                        <img src="{{ asset('images/Jembatan Soekarno Manado.jpg') }}" alt="Jembatan Soekarno Manado" class="absolute inset-0 object-cover w-full h-full transition duration-700 ease-out group-hover:scale-105 group-hover:brightness-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/45 to-black/20"></div>

                        <!-- Top Tag -->
                        <div class="relative z-10 flex items-center justify-between">
                            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-black/60 backdrop-blur-md border border-white/20 text-white shadow-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                Infrastruktur Ikonik
                            </span>
                            <span class="px-3.5 py-1 text-xs font-semibold text-slate-200 bg-white/15 backdrop-blur-md rounded-full border border-white/20 shadow-sm">
                                Sulawesi Utara • Bentang 1.127 M
                            </span>
                        </div>

                        <!-- Content Bottom -->
                        <figcaption class="relative z-10 max-w-xl">
                            <span class="text-xs font-bold uppercase tracking-wider text-red-400">Urat Nadi Trans-Sulawesi</span>
                            <h3 class="mt-1.5 text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white font-display">Jembatan Soekarno, Manado</h3>
                            <p class="mt-2.5 text-sm sm:text-base leading-relaxed text-slate-200">
                                Berdiri kokoh dengan mutu Semen Tonasa, menjadi urat nadi perhubungan logistik dan ikon pariwisata di Kawasan Timur Indonesia.
                            </p>
                            <div class="mt-5 flex flex-wrap gap-2.5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md rounded-xl border border-white/10">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    100% Pasokan Tonasa
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md rounded-xl border border-white/10">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Formula Tahan Sulfat Air Laut
                                </span>
                            </div>
                        </figcaption>
                    </figure>

                    <!-- 2. Portland Composite Cement (PCC) (Col Span 5, Row Span 1) -->
                    <figure class="relative overflow-hidden group rounded-[32px] border border-slate-200/90 bg-gradient-to-br from-white via-red-50/30 to-slate-50 p-6 sm:p-7 shadow-lg shadow-slate-200/50 hover:shadow-2xl hover:border-red-500/40 transition-all duration-500 lg:col-span-5 flex flex-col justify-between">
                        <!-- Red subtle glow behind sack -->
                        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-red-500/15 rounded-full blur-3xl group-hover:bg-red-500/25 transition-all duration-500"></div>

                        <!-- 3D floating image of PCC sack -->
                        <div class="absolute right-2 sm:right-5 bottom-2 sm:bottom-3 w-36 sm:w-44 h-auto pointer-events-none transition-transform duration-500 group-hover:scale-110 group-hover:-translate-y-2 group-hover:rotate-1">
                            <img src="{{ asset('images/PCC-768x761.png') }}" alt="Semen Tonasa PCC" class="w-full h-auto drop-shadow-[0_20px_25px_rgba(0,0,0,0.18)]">
                        </div>

                        <div class="relative z-10 flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-200/80">
                                Produk Unggulan
                            </span>
                            <span class="px-2.5 py-0.5 text-[11px] font-mono text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200">
                                SNI 7064:2014
                            </span>
                        </div>

                        <figcaption class="relative z-10 max-w-[60%] sm:max-w-[62%] mt-4">
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 font-display">Portland Composite Cement</h3>
                            <p class="mt-1.5 text-xs sm:text-sm text-slate-600 leading-snug">
                                Kuat tekan tinggi, kedap air, dan ramah lingkungan untuk seluruh jenis konstruksi modern.
                            </p>
                            <div class="mt-3 flex gap-2">
                                <span class="px-2 py-0.5 text-[10px] font-semibold text-slate-500 bg-white/80 rounded-md border border-slate-200">Kemasan 40kg &amp; 50kg</span>
                            </div>
                        </figcaption>
                    </figure>

                    <!-- 3. Distribusi Terintegrasi (Col Span 5, Row Span 1) -->
                    <figure class="relative overflow-hidden group rounded-[28px] border border-slate-200/80 bg-slate-900 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:border-red-500/40 transition-all duration-500 lg:col-span-5 flex flex-col justify-between p-6 sm:p-7">
                        <img src="{{ asset('images/IMG_4084.jpg') }}" alt="Proses distribusi semen terintegrasi" class="absolute inset-0 object-cover w-full h-full transition duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-black/20"></div>

                        <div class="relative z-10 flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-black/60 text-white border border-white/20 backdrop-blur-md">
                                Jaringan Logistik
                            </span>
                            <span class="text-xs text-white/80 bg-black/40 px-2.5 py-0.5 rounded-full backdrop-blur-sm">
                                11 Unit Pengantongan
                            </span>
                        </div>

                        <figcaption class="relative z-10">
                            <h3 class="text-xl font-bold text-white font-display">Distribusi Terintegrasi</h3>
                            <p class="mt-1 text-xs sm:text-sm text-slate-300">
                                Didukung pelabuhan khusus Biringkassi dan armada kapal semen curah andal ke pelosok Nusantara.
                            </p>
                        </figcaption>
                    </figure>

                    <!-- 4. Tol Layang Manado (Col Span 6) -->
                    <figure class="relative overflow-hidden group rounded-[28px] border border-slate-200/80 bg-slate-900 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:border-red-500/40 transition-all duration-500 md:col-span-1 lg:col-span-6 flex flex-col justify-between p-6 sm:p-7">
                        <img src="{{ asset('images/Tol Layang Manado.jpg') }}" alt="Tol Layang Manado" class="absolute inset-0 object-cover w-full h-full transition duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                        <div class="relative z-10 flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-black/60 text-white border border-white/20 backdrop-blur-md">
                                Konektivitas Trans-Sulawesi
                            </span>
                            <span class="text-xs text-white/80 bg-black/40 px-2.5 py-0.5 rounded-full backdrop-blur-sm">
                                Jalan Bebas Hambatan
                            </span>
                        </div>

                        <figcaption class="relative z-10">
                            <h3 class="text-xl font-bold text-white font-display">Tol Layang Manado - Bitung</h3>
                            <p class="mt-1 text-xs sm:text-sm text-slate-300">
                                Menghubungkan pusat ekonomi Manado dan Pelabuhan Internasional Bitung dengan mutu perkerasan teruji.
                            </p>
                        </figcaption>
                    </figure>

                    <!-- 5. Bendungan Balambano Sorowako (Col Span 6) -->
                    <figure class="relative overflow-hidden group rounded-[28px] border border-slate-200/80 bg-slate-900 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:border-red-500/40 transition-all duration-500 md:col-span-1 lg:col-span-6 flex flex-col justify-between p-6 sm:p-7">
                        <img src="{{ asset('images/Bendungan Balambano Sorowako.jpg') }}" alt="Bendungan Balambano Sorowako" class="absolute inset-0 object-cover w-full h-full transition duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                        <div class="relative z-10 flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-black/60 text-white border border-white/20 backdrop-blur-md">
                                Energi &amp; Konstruksi Masif
                            </span>
                            <span class="text-xs text-white/80 bg-black/40 px-2.5 py-0.5 rounded-full backdrop-blur-sm">
                                PLTA 135 MW
                            </span>
                        </div>

                        <figcaption class="relative z-10">
                            <h3 class="text-xl font-bold text-white font-display">Bendungan Balambano</h3>
                            <p class="mt-1 text-xs sm:text-sm text-slate-300">
                                Struktur RCC (Roller Compacted Concrete) berstandar internasional untuk pasokan listrik industri strategis.
                            </p>
                        </figcaption>
                    </figure>

                </div>

                @php
                    // Usia PT Semen Tonasa (didirikan resmi pada 2 November 1968).
                    // Dihitung dinamis dan otomatis bertambah 1 tahun tepat setiap tanggal 02 November.
                    $tonasaFoundation = \Carbon\Carbon::create(1968, 11, 2, 0, 0, 0);
                    $tonasaYears = (int) $tonasaFoundation->diffInYears(now());
                @endphp
                <!-- Sleek Trust & Quality Metrics Capsules (3 Elevated Cards) -->
                <div class="mt-10 sm:mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Capsule 1: Usia Dinamis -->
                    <div class="p-7 rounded-3xl bg-white border border-slate-200/80 shadow-lg shadow-slate-200/40 hover:shadow-xl hover:border-red-500/30 transition-all duration-300 flex flex-col justify-between group">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-red-50 text-[#F5333F] border border-red-200/60">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                HUT 02 November
                            </span>
                            <span class="text-[11px] font-mono text-slate-400">Est. 1968</span>
                        </div>
                        <div>
                            <div class="text-4xl sm:text-5xl font-black text-slate-900 font-display tracking-tight group-hover:text-[#F5333F] transition-colors">
                                {{ $tonasaYears }}+ <span class="text-xl sm:text-2xl font-bold text-[#F5333F]">Tahun</span>
                            </div>
                            <h4 class="mt-2 text-base font-bold text-slate-800">Konsistensi Mutu Membangun Negeri</h4>
                            <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                                Diresmikan 2 November 1968. Terus berinovasi menopang kemajuan infrastruktur nasional Indonesia Timur.
                            </p>
                        </div>
                    </div>

                    <!-- Capsule 2: Sertifikasi & Akreditasi -->
                    <div class="p-7 rounded-3xl bg-white border border-slate-200/80 shadow-lg shadow-slate-200/40 hover:shadow-xl hover:border-red-500/30 transition-all duration-300 flex flex-col justify-between group">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Mutu Terakreditasi
                            </span>
                            <span class="text-[11px] font-mono text-slate-400">ISO &amp; SNI</span>
                        </div>
                        <div>
                            <div class="text-4xl sm:text-5xl font-black text-[#F5333F] font-display tracking-tight">
                                100% <span class="text-xl sm:text-2xl font-bold text-slate-800">Teruji</span>
                            </div>
                            <h4 class="mt-2 text-base font-bold text-slate-800">Standar Mutu Nasional &amp; Global</h4>
                            <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                                Memenuhi SNI 7064:2014 &amp; ISO 9001 untuk ketahanan struktural beton performa tinggi dan ramah lingkungan.
                            </p>
                        </div>
                    </div>

                    <!-- Capsule 3: Jaringan Logistik -->
                    <div class="p-7 rounded-3xl bg-white border border-slate-200/80 shadow-lg shadow-slate-200/40 hover:shadow-xl hover:border-red-500/30 transition-all duration-300 flex flex-col justify-between group">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-blue-50 text-blue-700 border border-blue-200/60">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                                Jalur Maritim
                            </span>
                            <span class="text-[11px] font-mono text-slate-400">Nusantara</span>
                        </div>
                        <div>
                            <div class="text-4xl sm:text-5xl font-black text-slate-900 font-display tracking-tight group-hover:text-[#F5333F] transition-colors">
                                11 <span class="text-xl sm:text-2xl font-bold text-slate-500">Unit Packing</span>
                            </div>
                            <h4 class="mt-2 text-base font-bold text-slate-800">Distribusi Terintegrasi Nusantara</h4>
                            <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                                Didukung Pelabuhan Khusus Biringkassi dan armada kapal curah menghubungkan pasokan ke Ibu Kota Nusantara (IKN).
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- ================= HUBUNGI KAMI ================= -->
        <section id="kontak" class="py-16 sm:py-20 lg:py-24 bg-slate-50 relative overflow-hidden border-t border-slate-200/80">
            <!-- Decorative Accent -->
            <div class="absolute top-1/2 left-0 w-96 h-96 bg-red-500/5 rounded-full blur-[140px] pointer-events-none"></div>

            <div class="relative z-10 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Section Header: Title Case Headline & Sentence Case Description -->
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-12">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-50 border border-red-200/80 text-[#F5333F] text-xs font-bold tracking-widest uppercase mb-4 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#F5333F] animate-pulse"></span>
                        Komunikasi &amp; Layanan Resmi
                    </div>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 font-display tracking-tight leading-tight">
                        Hubungi <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F5333F] to-rose-600">Kami</span>
                    </h2>
                    <p class="mt-4 text-base sm:text-lg text-slate-600 leading-relaxed">
                        Pusat informasi resmi operasional, layanan terpadu unit kerja, dan komunikasi korporat PT Semen Tonasa.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Kolom Kiri: Informasi Kantor & Layanan -->
                    <div class="lg:col-span-5 space-y-4">
                        
                        <!-- Kantor Pusat & Pabrik Pangkep -->
                        <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/80 shadow-md shadow-slate-200/30 hover:border-red-500/30 transition-all">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-2xl bg-red-50 text-[#F5333F] flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-900 font-display">Kantor Pusat &amp; Pabrik</h4>
                                    <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                                        Desa Biringere, Kec. Bungoro, Kab. Pangkajene &amp; Kepulauan (Pangkep), Sulawesi Selatan 90615
                                    </p>
                                    <div class="mt-3 text-xs text-slate-700 font-medium space-y-1">
                                        <p><span class="text-slate-400">Telepon:</span> - </p>
                                        <p><span class="text-slate-400">Email:</span> - </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kantor Perwakilan Makassar -->
                        <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/80 shadow-md shadow-slate-200/30 hover:border-red-500/30 transition-all">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-900 font-display">Kantor Perwakilan Makassar</h4>
                                    <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                                        Jl. Khairil Anwar No. 1, Makassar, Sulawesi Selatan 90113
                                    </p>
                                    <div class="mt-3 text-xs text-slate-700 font-medium">
                                        <p><span class="text-slate-400">Telepon:</span> - </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Helpdesk Portal & Unit Kerja -->
                        <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/80 shadow-md shadow-slate-200/30 hover:border-red-500/30 transition-all">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-2xl bg-red-50 text-[#F5333F] flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-900 font-display">Helpdesk Portal Karyawan</h4>
                                    <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                                        Dukungan teknis akun korporat dan operasional sistem informasi antar unit.
                                    </p>
                                    <div class="mt-3 text-xs text-slate-700 font-medium space-y-1">
                                        <p><span class="text-slate-400">Jam Layanan:</span> Senin - Jumat (08.00 - 16.30 WITA)</p>
                                        <p><span class="text-slate-400">Helpdesk:</span> - </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Kolom Kanan: Form Kirim Pesan Cepat -->
                    <div class="lg:col-span-7" x-data="{ sent: false }">
                        <div class="p-8 sm:p-10 rounded-3xl bg-white border border-slate-200/80 shadow-xl shadow-slate-200/40">
                            <h3 class="text-2xl font-extrabold text-slate-900 font-display">Kirim Pesan atau Pertanyaan</h3>
                            <p class="mt-2 text-sm text-slate-500">
                                Isi formulir di bawah ini untuk terhubung langsung dengan tim humas atau helpdesk layanan terpadu.
                            </p>

                            <div x-show="sent" 
                                x-transition 
                                class="mt-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>Terima kasih! Pesan Anda telah terkirim ke tim layanan PT Semen Tonasa. Kami akan segera menindaklanjutinya.</span>
                            </div>

                            <form x-show="!sent" @submit.prevent="sent = true" class="mt-6 space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                                        <input type="text" required placeholder="Nama Anda" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-900 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                                        <input type="email" required placeholder="nama@instansi.com" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-900 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none transition">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori Keperluan</label>
                                    <select class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none transition">
                                        <option>Pertanyaan Umum &amp; Informasi Perusahaan</option>
                                        <option>Layanan &amp; Portal Karyawan</option>
                                        <option>Kemitraan, Penjualan &amp; Proyek</option>
                                        <option>Bantuan Teknis Sistem / IT Helpdesk</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pesan atau Pertanyaan</label>
                                    <textarea rows="4" required placeholder="Tuliskan pesan atau kebutuhan informasi Anda di sini..." class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-900 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none transition"></textarea>
                                </div>

                                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 text-sm font-bold text-white transition bg-[#F5333F] rounded-xl shadow-lg shadow-red-500/25 hover:bg-red-600 hover:-translate-y-0.5 focus:outline-none">
                                    Kirim Pesan Sekarang
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-black border-t border-white/10">
        <div class="flex flex-col items-center justify-between gap-6 px-4 py-8 mx-auto sm:flex-row max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center">
                <img src="{{ asset('images/kokoh-kuat-terpercaya-white.png') }}" alt="Semen Tonasa - Kokoh, Kuat &amp; Terpercaya" class="h-10 sm:h-12 w-auto object-contain">
            </div>
            <div class="text-center sm:text-right">
                <p class="text-xs text-slate-400 sm:text-sm">&copy; {{ date('Y') }} PT Semen Tonasa. Hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        // Navbar shadow menjadi lebih tegas setelah halaman di-scroll
        const nav = document.getElementById('navbar');
        const onScroll = () => {
            const scrolled = window.scrollY > 30;
            nav.classList.toggle('shadow-md', scrolled);
            nav.classList.toggle('shadow-xs', !scrolled);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    </script>
</body>
</html>