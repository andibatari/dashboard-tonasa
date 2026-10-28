@php
    $maxSS  = \App\Models\Setting::where('key', 'max_anggota_ss')->value('value') ?? 2;
    $maxGKM = \App\Models\Setting::where('key', 'max_anggota_gkm')->value('value') ?? 5;
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Inovasi Semen Tonasa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kmst-gold.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .glass-nav { background: rgba(255, 255, 255, 0.88); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); }
        .hero-pattern { background-image: radial-gradient(#E2E8F0 1.2px, transparent 1.2px); background-size: 28px 28px; }
        
        @layer components {
            .form-input { 
                @apply w-full px-4 py-3 text-sm text-slate-800 bg-white border border-slate-300 rounded-2xl shadow-sm focus:outline-none focus:ring-4 focus:ring-[#F5333F]/15 focus:border-[#F5333F] transition-all duration-200; 
            }
            .form-label { 
                @apply block mb-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider; 
            }
        }

        @keyframes float-gentle {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(1.5deg); }
        }
        @keyframes float-gentle-rev {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(10px) rotate(-1.5deg); }
        }
        @keyframes shimmer-gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.05); opacity: 0.95; }
        }
        @keyframes radar-glow {
            0% { box-shadow: 0 0 0 0 rgba(245, 51, 63, 0.4); }
            70% { box-shadow: 0 0 0 12px rgba(245, 51, 63, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 51, 63, 0); }
        }

        .anim-float-gentle { animation: float-gentle 7s ease-in-out infinite; }
        .anim-float-gentle-rev { animation: float-gentle-rev 8s ease-in-out infinite; }
        .anim-shimmer-text { 
            background-size: 250% auto; 
            animation: shimmer-gradient 5s ease infinite; 
        }
        .anim-pulse-soft { animation: pulse-soft 4s ease-in-out infinite; }
        .anim-radar-glow { animation: radar-glow 2s infinite; }
    </style>
</head>
<body class="text-slate-800 bg-slate-50 antialiased selection:bg-[#F5333F] selection:text-white">

    <!-- ================= NAVBAR ================= -->
    <nav class="fixed top-0 z-50 w-full border-b glass-nav border-slate-200/70 shadow-sm transition-all duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <a href="{{ route('inovasi.portal') }}" class="flex items-center gap-3 sm:gap-3.5 group focus:outline-none">
                    <!-- Logo Emas Bulat Semen Tonasa (Gambar 2: Tajam & Resolusi Tinggi) -->
                    <img src="{{ asset('images/logo-kmst-gold.png') }}" alt="Logo Semen Tonasa KMST" class="h-10 sm:h-12 md:h-14 w-auto object-contain drop-shadow-md group-hover:scale-105 transition-transform duration-300">
                    
                    <!-- Teks Stylized INNOVATION (Gambar 3: Menggantikan INOVASI SEMEN TONASA) -->
                    <img src="{{ asset('images/text-innovation.png') }}" alt="INNOVATION" class="h-5 sm:h-6 md:h-7 w-auto object-contain group-hover:opacity-95 transition-opacity">
                </a>

                <div class="flex items-center space-x-3 sm:space-x-6 text-sm font-bold text-slate-600">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-slate-600 hover:text-[#F5333F] hover:bg-red-50/60 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Ke Beranda</span>
                    </a>
                    <a href="#galeri" class="hidden md:inline-block px-3.5 py-2 rounded-xl hover:text-[#F5333F] hover:bg-red-50/60 transition-colors">Galeri Juara</a>
                    <a href="#tahapan" class="hidden lg:inline-block px-3.5 py-2 rounded-xl hover:text-[#F5333F] hover:bg-red-50/60 transition-colors">Tahapan KMST</a>
                    <a href="#download" class="hidden sm:inline-block px-3.5 py-2 rounded-xl hover:text-[#F5333F] hover:bg-red-50/60 transition-colors">Unduh Template</a>
                    <a href="#daftar" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs sm:text-sm text-white bg-gradient-to-r from-slate-900 to-slate-800 hover:from-[#F5333F] hover:to-rose-600 rounded-2xl shadow-lg shadow-slate-900/10 hover:shadow-red-500/30 transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Registrasi KMST</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ================= HERO SECTION DENGAN ANIMASI GLOW & ORB ================= -->
    <div class="relative pt-24 sm:pt-36 pb-16 sm:pb-28 bg-white hero-pattern border-b border-slate-100 overflow-hidden">
        <!-- Glowing Floating Blobs -->
        <div class="absolute -top-20 -right-20 w-96 h-96 rounded-full bg-[#F5333F]/15 blur-3xl pointer-events-none anim-float-gentle"></div>
        <div class="absolute -bottom-24 -left-20 w-80 h-80 rounded-full bg-amber-500/15 blur-3xl pointer-events-none anim-float-gentle-rev"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-rose-500/10 blur-3xl pointer-events-none anim-pulse-soft"></div>

        <div class="relative mx-auto text-center max-w-5xl px-4 z-10">
            <!-- Pulsing Badge Event -->
            <div class="inline-flex items-center gap-2.5 px-4 py-1.5 mb-8 text-xs font-black tracking-widest text-[#F5333F] bg-red-50 border border-red-200/80 rounded-full uppercase shadow-sm">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#F5333F]"></span>
                </span>
                <span>Konvensi Mutu Semen Tonasa 2026 - 2027</span>
            </div>

            <!-- Headline with Gradient Text Animation (Modern, Seimbang & Bebas Kata Menggantung) -->
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-black tracking-tight text-slate-900 leading-[1.18] max-w-4xl mx-auto text-balance">
                Wujudkan <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F5333F] via-rose-600 to-amber-500 anim-shimmer-text">Inovasi Terbaik</span> Anda
            </h1>

            <p class="max-w-3xl mx-auto mt-6 text-base sm:text-xl text-slate-600 leading-relaxed font-medium">
                Pintu gerbang pendaftaran karya inovasi insan Semen Tonasa. Terbuka untuk 5 kategori: 
                <strong class="text-slate-900 font-extrabold">SS (Office)</strong>, 
                <strong class="text-slate-900 font-extrabold">SS (Plant)</strong>, 
                <strong class="text-slate-900 font-extrabold">GKM</strong>, 
                <strong class="text-slate-900 font-extrabold">PKM</strong>, dan 
                <strong class="text-slate-900 font-extrabold">BI</strong>.
            </p>

            <!-- Quick Action Buttons -->
            <div class="mt-10 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                <a href="#daftar" class="inline-flex items-center gap-2.5 px-8 py-4 text-sm sm:text-base font-extrabold text-white transition-all rounded-2xl bg-gradient-to-r from-[#F5333F] via-rose-600 to-[#F5333F] bg-[length:200%_auto] hover:bg-right shadow-xl shadow-red-500/30 hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    <span>Daftarkan Tim Inovasi</span>
                </a>
                <a href="#download" class="inline-flex items-center gap-2 px-6 py-4 text-sm sm:text-base font-bold text-slate-700 bg-white border border-slate-200 rounded-2xl shadow-sm hover:border-[#F5333F] hover:text-[#F5333F] hover:shadow-md transition-all hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Unduh 5 Template Resmi</span>
                </a>
            </div>

            <!-- Stats Highlight Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 pt-10 border-t border-slate-100 max-w-4xl mx-auto">
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 block">5</span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 block">Kategori Resmi</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100">
                    <span class="text-2xl sm:text-3xl font-black text-[#F5333F] block">100+</span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 block">Inovasi Dihasilkan</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100">
                    <span class="text-2xl sm:text-3xl font-black text-emerald-600 block">TKMPN</span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 block">Pentas Nasional</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100">
                    <span class="text-2xl sm:text-3xl font-black text-amber-600 block">ICQCC</span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 block">Pentas Dunia</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= TAHAPAN / MILESTONE KMST (DINAMIS DARI KONTROL SISTEM) ================= -->
    <div id="tahapan" class="py-16 sm:py-24 bg-slate-50/80 border-b border-slate-200/80 relative overflow-hidden">
        <div class="max-w-7xl px-4 mx-auto sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-14 sm:mb-20">
                <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#F5333F] bg-red-50 px-3.5 py-1.5 rounded-full border border-red-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-[#F5333F] animate-ping"></span>
                    Roadmap &amp; Timeline Kompetisi
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-3">{{ count($tahapanList ?? []) }} Tahapan Konvensi Mutu Semen Tonasa</h2>
                <p class="text-sm sm:text-base text-slate-500 mt-2">Ikuti alur tahapan resmi KMST dari pendaftaran hingga penganugerahan juara dan tiket delegasi nasional/internasional.</p>
            </div>

            <!-- Grid Tahapan Dinamis Terhubung -->
            @php
                $tahapCount = count($tahapanList ?? []);
                $gridColsClass = $tahapCount <= 3 ? 'md:grid-cols-3' : ($tahapCount === 4 ? 'md:grid-cols-2 lg:grid-cols-4' : 'md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-' . min($tahapCount, 5));
            @endphp
            <div class="relative grid grid-cols-1 {{ $gridColsClass }} gap-6">
                @foreach(($tahapanList ?? []) as $index => $t)
                    @php
                        $isActive = !empty($t['is_active']);
                        $isTrophy = ($t['icon_type'] ?? '') === 'trophy' || str_contains(strtolower($t['judul'] ?? ''), 'penganugerahan') || str_contains(strtolower($t['badge'] ?? ''), 'awarding');
                    @endphp

                    @if($isActive)
                        <!-- Kartu Tahap AKTIF (Highlighted dengan Radar Glow) -->
                        <div class="group relative p-6 sm:p-7 rounded-3xl bg-white border-2 border-[#F5333F] shadow-xl shadow-red-500/10 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#F5333F] to-rose-500 text-white font-black text-lg shadow-lg shadow-red-500/30 anim-radar-glow">
                                        @if($isTrophy) 🏆 @else {{ $t['nomor'] ?? ($index + 1) }} @endif
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        {{ !empty($t['badge']) ? $t['badge'] : 'Tahap Aktif' }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900 mt-2 group-hover:text-[#F5333F] transition-colors">
                                    {{ $t['judul'] }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                    {{ $t['deskripsi'] }}
                                </p>
                            </div>
                            @if(!empty($t['info_bawah']))
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-[11px] font-bold text-[#F5333F]">
                                    <span>{{ $t['info_bawah'] }}</span>
                                </div>
                            @endif
                        </div>
                    @elseif($isTrophy)
                        <!-- Kartu Tahap Trophy / Awarding -->
                        <div class="group relative p-6 sm:p-7 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-lg hover:border-amber-300 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 font-black text-lg border border-amber-200 group-hover:scale-105 transition-transform">
                                        🏆
                                    </span>
                                    @if(!empty($t['badge']))
                                        <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-amber-100 text-amber-800">
                                            {{ $t['badge'] }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-black text-slate-900 mt-2 group-hover:text-amber-600 transition-colors">
                                    {{ $t['judul'] }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                    {{ $t['deskripsi'] }}
                                </p>
                            </div>
                            @if(!empty($t['info_bawah']))
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-[11px] font-bold text-amber-600">
                                    <span>{{ $t['info_bawah'] }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Kartu Tahap Normal -->
                        <div class="group relative p-6 sm:p-7 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-lg hover:border-slate-300 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 font-black text-lg group-hover:bg-slate-200 transition-colors">
                                        {{ $t['nomor'] ?? ($index + 1) }}
                                    </span>
                                    @if(!empty($t['badge']))
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-500">
                                            {{ $t['badge'] }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-black text-slate-900 mt-2 group-hover:text-slate-900 transition-colors">
                                    {{ $t['judul'] }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                    {{ $t['deskripsi'] }}
                                </p>
                            </div>
                            @if(!empty($t['info_bawah']))
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-[11px] font-semibold text-slate-400">
                                    <span>{{ $t['info_bawah'] }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- ================= GALERI PRESTASI JUARA INOVASI DENGAN FILTER & MODAL ================= -->
    <div id="galeri" class="py-16 sm:py-24 bg-white border-b border-slate-100" 
        x-data="{ 
            activeCat: 'all', 
            selectedChampion: null,
            champions: [
                {
                    id: 1,
                    kategori: 'BI',
                    kategoriLabel: 'BI (Breakthrough)',
                    badge: '🏆 Juara 1 & Grand Champion TKMPN',
                    badgeColor: 'amber',
                    title: 'Optimalisasi Raw Mill & Green Clinker',
                    team: 'Tim Breakthrough Semen Tonasa',
                    impact: '💰 Efisiensi Rp 2.4 Miliar/Tahun',
                    image: 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                    shortDesc: 'Inovasi pembakaran klinker alternatif ramah lingkungan dan reduksi emisi thermal pabrik.',
                    fullDesc: 'Melalui modifikasi jalur pneumatic feed dan substitusi bahan bakar alternatif (AFR) pada proses pembakaran kiln, tim berhasil meningkatkan efisiensi energi termal sekaligus memangkas biaya operasional klinker secara signifikan dengan nol dampak negatif pada mutu produk semen.',
                    event: 'Temu Karya Mutu & Produktivitas Nasional (TKMPN) Bali'
                },
                {
                    id: 2,
                    kategori: 'GKM',
                    kategoriLabel: 'GKM (Gugus Kendali Mutu)',
                    badge: '🌍 Platinum Award ICQCC Internasional',
                    badgeColor: 'emerald',
                    title: 'Reduksi Thermal Loss Kalsiner Preheater',
                    team: 'GKM Klinker Unggul Pabrik 4/5',
                    impact: '⚡ Reduksi Stop Kiln 48 Jam',
                    image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                    shortDesc: 'Peningkatan keandalan siklon kalsiner dan kontrol kestabilan klinker ekspor.',
                    fullDesc: 'Inovasi rekayasa aerodinamika pada siklon tingkat 4 dan 5 preheater yang meminimalisir coating dan sumbatan raw meal. Berhasil menyabet predikat Platinum Award pada ajang konvensi mutu internasional International Convention on Quality Control Circles (ICQCC).',
                    event: 'International Convention on Quality Control Circles (ICQCC)'
                },
                {
                    id: 3,
                    kategori: 'PKM',
                    kategoriLabel: 'PKM (Proyek Kendali Mutu)',
                    badge: '⚡ Gold Winner Temu Karya SIG',
                    badgeColor: 'blue',
                    title: 'Digital Blending Limestone IoT Tambang',
                    team: 'Tim PKM Penambangan & Bahan Baku',
                    impact: '🎯 Akurasi Blending 99.2%',
                    image: 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                    shortDesc: 'Sensor optik dan tracking armada armada tambang untuk konsistensi mutu kiln.',
                    fullDesc: 'Integrasi sistem analitik kadar kalsium karbonat real-time dengan pemantauan lokasi armada truk tambang. Memastikan proporsi komposisi bahan baku limestone selalu presisi sebelum masuk hopper utama penumpukan.',
                    event: 'InnoFest Holding Semen Indonesia (SIG Group)'
                },
                {
                    id: 4,
                    kategori: 'SS',
                    kategoriLabel: 'SS (Plant)',
                    badge: '💡 Best SS Plant Semen Tonasa',
                    badgeColor: 'emerald',
                    title: 'Quick-Release Silo Nozzle Pack Pabrik 5',
                    team: 'Inovator Unit Packing & Distribusi',
                    impact: '⏱️ Nol Downtime Distribusi',
                    image: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                    shortDesc: 'Rancang bangun nozzle fleksibel mempercepat proses loading armada curah.',
                    fullDesc: 'Desain sambungan modular nozzle silo semen yang dapat diganti dalam waktu kurang dari 15 menit tanpa mematikan blower transport semen, melenyapkan antrean truk curah pada jam puncak distribusi pabrik.',
                    event: 'Konvensi Mutu Internal Semen Tonasa'
                },
                {
                    id: 5,
                    kategori: 'SS',
                    kategoriLabel: 'SS (Office)',
                    badge: '⭐ Gold Award SS Office Tonasa',
                    badgeColor: 'amber',
                    title: 'Otomasi Rekonsiliasi Faktur Logistik',
                    team: 'Inovasi Akuntansi & Pengadaan',
                    impact: '🚀 Waktu Proses -75%',
                    image: 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                    shortDesc: 'Digitalisasi pencocokan dokumen Delivery Order dan Invoice SAP terintegrasi.',
                    fullDesc: 'Pengembangan script otomatisasi verifikasi surat jalan dan faktur tagihan logistik distribusi yang terhubung langsung ke SAP ERP Semen Tonasa, memotong siklus verifikasi dari 4 hari menjadi hanya beberapa jam kerja.',
                    event: 'Konvensi Inovasi Manajemen Semen Tonasa'
                }
            ]
        }">
        <div class="max-w-7xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14">
                <span class="text-xs font-black uppercase tracking-wider text-amber-600 bg-amber-50 px-3.5 py-1.5 rounded-full border border-amber-200 shadow-sm">
                    🏆 Hall of Fame Inovasi
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-3">Galeri Prestasi Juara Inovasi</h2>
                <p class="mt-2 text-sm sm:text-base text-slate-500">Jejak langkah para inovator Semen Tonasa yang telah menorehkan prestasi gemilang di tingkat holding, nasional, dan panggung dunia.</p>

                <!-- Filter Kategori Tabs -->
                <div class="flex flex-wrap items-center justify-center gap-2 mt-8">
                    <button type="button" @click="activeCat = 'all'" :class="activeCat === 'all' ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                        Semua Kategori (5)
                    </button>
                    <button type="button" @click="activeCat = 'SS'" :class="activeCat === 'SS' ? 'bg-[#F5333F] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                        SS (Office &amp; Plant)
                    </button>
                    <button type="button" @click="activeCat = 'GKM'" :class="activeCat === 'GKM' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                        GKM (Gugus Mutu)
                    </button>
                    <button type="button" @click="activeCat = 'PKM'" :class="activeCat === 'PKM' ? 'bg-purple-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                        PKM (Proyek Mutu)
                    </button>
                    <button type="button" @click="activeCat = 'BI'" :class="activeCat === 'BI' ? 'bg-rose-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                        BI (Breakthrough)
                    </button>
                </div>
            </div>

            <!-- Grid Foto Juara -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="champ in champions" :key="champ.id">
                    <div x-show="activeCat === 'all' || activeCat === champ.kategori" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        @click="selectedChampion = champ"
                        class="group relative flex flex-col justify-between overflow-hidden rounded-3xl bg-white border border-slate-200 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 cursor-pointer">
                        
                        <!-- Gambar dengan Overlay Gradient -->
                        <div class="relative aspect-video overflow-hidden bg-slate-900">
                            <img :src="champ.image" :alt="champ.title" class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110 opacity-90 group-hover:opacity-100">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/30 to-transparent"></div>
                            
                            <!-- Badge Prestasi -->
                            <span class="absolute top-3 left-3 inline-block px-3 py-1 text-[10px] font-black rounded-full bg-amber-500 text-slate-950 uppercase tracking-wider shadow-md" x-text="champ.badge"></span>
                            
                            <!-- Kategori Pill -->
                            <span class="absolute bottom-3 right-3 px-2.5 py-0.5 text-[10px] font-black rounded-lg bg-black/60 backdrop-blur-md text-white border border-white/20 uppercase" x-text="champ.kategoriLabel"></span>
                        </div>

                        <!-- Info Card -->
                        <div class="p-5 space-y-2">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-400">
                                <span x-text="champ.team"></span>
                                <span class="text-emerald-600 font-extrabold" x-text="champ.impact"></span>
                            </div>
                            <h4 class="text-base font-black text-slate-900 group-hover:text-[#F5333F] transition-colors line-clamp-1" x-text="champ.title"></h4>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed" x-text="champ.shortDesc"></p>
                        </div>

                        <!-- Footer Action -->
                        <div class="px-5 pb-4 pt-0">
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-[#F5333F] group-hover:translate-x-1 transition-transform">
                                <span>Lihat Detail Karya</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
        </div>

        <!-- ================= MODAL DETAIL JUARA (LIGHTBOX) ================= -->
        <div x-show="selectedChampion" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="selectedChampion = null"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" 
            style="display: none;">
            
            <div @click.away="selectedChampion = null" 
                x-show="selectedChampion"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
                
                <template x-if="selectedChampion">
                    <div>
                        <!-- Foto Modal -->
                        <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                            <img :src="selectedChampion.image" :alt="selectedChampion.title" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                            
                            <button type="button" @click="selectedChampion = null" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-black/60 text-white flex items-center justify-center hover:bg-[#F5333F] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>

                            <div class="absolute bottom-4 left-6 right-6">
                                <span class="px-3 py-1 text-xs font-black rounded-full bg-amber-500 text-slate-950 uppercase tracking-wider inline-block mb-2" x-text="selectedChampion.badge"></span>
                                <h3 class="text-xl sm:text-2xl font-black text-white" x-text="selectedChampion.title"></h3>
                            </div>
                        </div>

                        <!-- Detail Konten -->
                        <div class="p-6 sm:p-8 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-100">
                                <div>
                                    <span class="text-xs text-slate-400 block font-semibold">Tim Inovator:</span>
                                    <strong class="text-sm font-extrabold text-slate-800" x-text="selectedChampion.team"></strong>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-400 block font-semibold">Kategori:</span>
                                    <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-lg bg-red-50 text-[#F5333F]" x-text="selectedChampion.kategoriLabel"></span>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-400 block font-semibold">Nilai Manfaat:</span>
                                    <strong class="text-sm font-black text-emerald-600" x-text="selectedChampion.impact"></strong>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-400">Ringkasan &amp; Dampak Solusi</h4>
                                <p class="text-sm text-slate-600 leading-relaxed" x-text="selectedChampion.fullDesc"></p>
                            </div>

                            <div class="pt-2">
                                <span class="text-xs text-slate-400 block font-semibold">Ajang Penghargaan:</span>
                                <p class="text-xs font-bold text-slate-700 mt-0.5 flex items-center gap-1.5">
                                    <span>🏅</span>
                                    <span x-text="selectedChampion.event"></span>
                                </p>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="button" @click="selectedChampion = null" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-[#F5333F] text-white text-xs font-bold transition-colors">
                                    Tutup Pratinjau
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>

    </div>

    <!-- ================= DOWNLOAD SECTION (5 KATEGORI LENGKAP) ================= -->
    <div id="download" class="py-16 sm:py-24 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-7xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
                <span class="text-xs font-black uppercase tracking-wider text-[#F5333F] bg-red-50 px-3 py-1 rounded-full border border-red-100">Pusat Unduhan</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">Unduh Template Resmi Makalah (5 Kategori)</h2>
                <p class="mt-2 text-sm sm:text-base text-slate-500">Gunakan format makalah resmi KMST 2026/2027 sesuai kategori yang Anda ikuti.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @php
                    $kategoriTemplates = [
                        ['code' => 'SS_Office', 'label' => 'SS (Office)', 'desc' => 'Sumbang Saran Kantor', 'badge' => 'SS', 'color' => 'amber'],
                        ['code' => 'SS_Plant',  'label' => 'SS (Plant)',  'desc' => 'Sumbang Saran Pabrik', 'badge' => 'SS', 'color' => 'emerald'],
                        ['code' => 'GKM',       'label' => 'GKM',         'desc' => 'Gugus Kendali Mutu',   'badge' => 'GKM', 'color' => 'blue'],
                        ['code' => 'PKM',       'label' => 'PKM',         'desc' => 'Proyek Kendali Mutu',  'badge' => 'PKM', 'color' => 'purple'],
                        ['code' => 'BI',        'label' => 'BI',          'desc' => 'Breakthrough Inovasi', 'badge' => 'BI', 'color' => 'rose'],
                    ];
                @endphp
                @foreach($kategoriTemplates as $kt)
                <a href="{{ route('inovasi.download-template', ['kategori' => $kt['code']]) }}" 
                    class="group relative flex flex-col items-center justify-between p-6 transition-all duration-300 bg-white border border-slate-200/80 rounded-3xl hover:border-[#F5333F] hover:shadow-xl hover:-translate-y-2 cursor-pointer overflow-hidden text-center">
                    
                    <span class="px-2 py-0.5 text-[9px] font-black rounded-md uppercase tracking-wider bg-slate-100 text-slate-600 group-hover:bg-[#F5333F] group-hover:text-white transition-colors">
                        Format .docx
                    </span>

                    <div class="w-14 h-14 my-4 rounded-2xl bg-red-50 text-[#F5333F] flex items-center justify-center group-hover:bg-[#F5333F] group-hover:text-white transition-all shadow-sm z-10 group-hover:rotate-6 group-hover:scale-110 duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-slate-900 tracking-tight group-hover:text-[#F5333F] transition-colors">{{ $kt['label'] }}</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-tight">{{ $kt['desc'] }}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 w-full flex items-center justify-center gap-1 text-[11px] font-bold text-[#F5333F] group-hover:underline">
                        <span>Unduh File</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ================= FORM REGISTRASI DENGAN VALIDASI & LIVE SELEKSI ================= -->
    <div id="daftar" class="py-16 sm:py-24 bg-white">
        <div class="max-w-7xl px-4 mx-auto sm:px-6 lg:px-8">
            
            <div class="p-6 sm:p-10 lg:p-14 bg-white border border-slate-200 shadow-xl rounded-3xl relative overflow-hidden">
                <!-- Background Glow -->
                <div class="absolute top-0 right-0 w-80 h-80 bg-gradient-to-br from-red-100/60 to-transparent rounded-bl-full pointer-events-none -z-0"></div>

                <div class="mb-10 sm:mb-14 text-center relative z-10">
                    <span class="text-xs font-black uppercase tracking-wider text-[#F5333F] bg-red-50 px-3 py-1 rounded-full border border-red-200">Formulir KMST 2026/2027</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-2">Pendaftaran Tim &amp; Pengunggahan Makalah</h2>
                    <p class="mt-2 text-sm sm:text-base text-slate-500">Lengkapi data susunan tim dan unggah berkas makalah resmi (Maks. 30MB).</p>
                </div>

                @if(session('success'))
                    <div class="p-5 mb-10 text-emerald-800 border bg-emerald-50 border-emerald-200 rounded-2xl flex items-center gap-4 shadow-sm">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="font-extrabold text-lg">Pendaftaran Sukses!</p>
                            <p class="text-sm mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (isset($errors) && $errors->any())
                    <div class="p-6 mb-10 text-red-800 bg-red-50 border border-red-200 rounded-2xl shadow-sm">
                        <p class="font-extrabold mb-3 text-base flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Mohon periksa kembali isian formulir:
                        </p>
                        <ul class="pl-6 text-sm list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Persiapan Data Dropdown Alpine -->
                @php
                    $deptUnitMap = [];
                    if(isset($departments)) {
                        foreach($departments as $dept) {
                            $deptUnitMap[$dept->name_dept] = $dept->units->pluck('name_unit')->toArray();
                        }
                    }
                @endphp

                <form action="{{ route('inovasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-12 sm:space-y-16 relative z-10" 
                    x-data="{ 
                        kategori: '',
                        
                        // Tarik data batasan dari database
                        batasSS: {{ $maxSS }},
                        batasGKM: {{ $maxGKM }},
                        
                        // State penyimpanan
                        d: { fasilitator: '', ketua: '', sekretaris: '' },
                        u: { fasilitator: '', ketua: '', sekretaris: '' },
                        deptUnits: {{ json_encode($deptUnitMap) }},
                        
                        // Array untuk menampung baris anggota ekstra
                        anggotaTambahan: [], 
                        
                        getUnits(role) { return this.deptUnits[this.d[role]] || []; },
                        
                        get isSS() {
                            return this.kategori.includes('SS');
                        },

                        // Menghitung berapa banyak sisa kursi anggota yang bisa ditambahkan
                        get sisaKursi() {
                            if (this.kategori === '') return 0;
                            
                            let batasMaksimal = this.isSS ? this.batasSS : this.batasGKM;
                            
                            // Kurangi jatah kursi dengan anggota wajib
                            let jumlahWajib = this.isSS ? 1 : 3; // SS: Ketua (1); GKM, PKM, BI: Fasilitator, Ketua, Sekretaris (3)
                            
                            let sisa = batasMaksimal - jumlahWajib - this.anggotaTambahan.length;
                            return sisa > 0 ? sisa : 0;
                        },

                        tambahAnggota() {
                            if (this.sisaKursi > 0) {
                                this.anggotaTambahan.push(Date.now());
                            } else {
                                alert('Batas maksimal anggota tim telah tercapai sesuai aturan sistem.');
                            }
                        },

                        hapusAnggota(id) {
                            this.anggotaTambahan = this.anggotaTambahan.filter(item => item !== id);
                        }
                    }">
                    @csrf

                    <!-- BLOK 1: KATEGORI & TIM -->
                    <div>
                        <div class="flex items-center gap-3 pb-4 mb-6 sm:mb-8 border-b border-slate-100">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center bg-slate-900 text-white rounded-2xl font-black text-sm sm:text-base shadow-sm">1</div>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900">Informasi Utama Kategori &amp; Tim</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="min-w-0">
                                <label class="form-label">Pilih Kategori Inovasi <span class="text-[#F5333F]">*</span></label>
                                <select x-model="kategori" name="kategori" class="form-input cursor-pointer font-bold" @change="anggotaTambahan = []" required>
                                    <option value="">-- Pilih Salah Satu Kategori --</option>
                                    <option value="SS (Office)">Sumbang Saran - SS (Office)</option>
                                    <option value="SS (Plant)">Sumbang Saran - SS (Plant)</option>
                                    <option value="GKM">Gugus Kendali Mutu (GKM)</option>
                                    <option value="PKM">Proyek Kendali Mutu (PKM)</option>
                                    <option value="BI">Breakthrough Innovation (BI)</option>
                                </select>
                            </div>
                            <div class="min-w-0">
                                <label class="form-label">Nama Tim Inovasi <span class="text-[#F5333F]">*</span></label>
                                <input type="text" name="nama_tim" class="form-input font-bold" placeholder="Contoh: Tim Breakthrough Klinker" required>
                            </div>
                        </div>
                    </div>

                    <!-- Placeholder Belum Memilih Kategori -->
                    <div x-show="kategori === ''" class="py-16 sm:py-20 text-center border-2 border-dashed rounded-3xl border-slate-200 bg-slate-50/60">
                        <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 border border-slate-200 text-slate-400">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-800">Pilih Kategori Inovasi di Atas</h4>
                        <p class="text-xs text-slate-400 mt-1">Struktur form dan susunan tim akan menyesuaikan secara dinamis.</p>
                    </div>

                    <!-- BLOK 2: DATA PESERTA -->
                    <div x-show="kategori !== ''" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="space-y-8 sm:space-y-10">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center bg-[#F5333F] text-white rounded-2xl font-black text-sm sm:text-base shadow-sm shadow-red-500/20">2</div>
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900">Susunan Peserta <span x-text="kategori" class="text-[#F5333F] ml-1"></span></h3>
                            </div>
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                                Sisa Kuota Tambahan: <strong class="text-[#F5333F]" x-text="sisaKursi"></strong>
                            </span>
                        </div>

                        <!-- Opsi Dropdown Departemen (Reusable) -->
                        @php
                            ob_start();
                        @endphp
                            <option value="">-- Pilih Departemen --</option>
                            @if(isset($departments)) @foreach($departments as $dept) <option value="{{ $dept->name_dept }}">{{ $dept->name_dept }}</option> @endforeach @endif
                            <option value="Lainnya" class="font-bold text-red-600">Lainnya (Ketik Manual)...</option>
                        @php $dropdownDept = ob_get_clean(); @endphp

                        <!-- FASILITATOR (Untuk GKM, PKM, BI) -->
                        <div x-show="!isSS" class="p-6 sm:p-8 border border-slate-200 rounded-3xl bg-slate-50/50 transition-all hover:border-slate-300">
                            <h4 class="mb-6 text-sm font-bold tracking-widest text-slate-500 uppercase">1. Fasilitator</h4>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                                <div class="min-w-0"><label class="form-label">Nama Lengkap *</label><input type="text" name="peserta[fasilitator][nama]" class="form-input" :required="!isSS"></div>
                                <div class="min-w-0"><label class="form-label">No. SAP *</label><input type="text" name="peserta[fasilitator][sap]" class="form-input" :required="!isSS"></div>
                                <div class="min-w-0"><label class="form-label">Departemen *</label>
                                    <select x-model="d.fasilitator" @change="u.fasilitator = ''" name="peserta[fasilitator][dept]" class="form-input cursor-pointer" :required="!isSS">{!! $dropdownDept !!}</select>
                                    <input x-show="d.fasilitator === 'Lainnya'" type="text" name="peserta[fasilitator][dept_lainnya]" placeholder="Ketik Departemen..." class="form-input mt-3" :required="d.fasilitator === 'Lainnya'">
                                </div>
                                <div class="min-w-0"><label class="form-label">Unit Kerja *</label>
                                    <select x-model="u.fasilitator" name="peserta[fasilitator][unit]" class="form-input cursor-pointer" :required="!isSS">
                                        <option value="">-- Pilih Unit Kerja --</option>
                                        <template x-for="unitName in getUnits('fasilitator')" :key="unitName"><option :value="unitName" x-text="unitName"></option></template>
                                        <option value="Lainnya" class="font-bold text-red-600">Lainnya (Ketik Manual)...</option>
                                    </select>
                                    <input x-show="u.fasilitator === 'Lainnya'" type="text" name="peserta[fasilitator][unit_lainnya]" placeholder="Ketik Unit Kerja..." class="form-input mt-3" :required="u.fasilitator === 'Lainnya'">
                                </div>
                            </div>
                        </div>

                        <!-- KETUA TIM (Semua Kategori: SS, GKM, PKM, BI) -->
                        <div class="p-6 sm:p-8 bg-white border border-slate-200 shadow-md rounded-3xl relative overflow-hidden transition-all hover:shadow-lg">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-amber-500"></div>
                            <h4 class="mb-6 text-sm font-bold tracking-widest text-amber-600 uppercase" x-text="isSS ? '1. Ketua Tim' : '2. Ketua Tim'"></h4>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-5">
                                <div class="min-w-0"><label class="form-label">Nama Lengkap *</label><input type="text" name="peserta[ketua][nama]" class="form-input font-semibold" required></div>
                                <div class="min-w-0"><label class="form-label">No. SAP *</label><input type="text" name="peserta[ketua][sap]" class="form-input" required></div>
                                <div class="min-w-0"><label class="form-label">Departemen *</label>
                                    <select x-model="d.ketua" @change="u.ketua = ''" name="peserta[ketua][dept]" class="form-input cursor-pointer" required>{!! $dropdownDept !!}</select>
                                    <input x-show="d.ketua === 'Lainnya'" type="text" name="peserta[ketua][dept_lainnya]" placeholder="Ketik Departemen..." class="form-input mt-3" :required="d.ketua === 'Lainnya'">
                                </div>
                                <div class="min-w-0"><label class="form-label">Unit Kerja *</label>
                                    <select x-model="u.ketua" name="peserta[ketua][unit]" class="form-input cursor-pointer" required>
                                        <option value="">-- Pilih Unit Kerja --</option>
                                        <template x-for="unitName in getUnits('ketua')" :key="unitName"><option :value="unitName" x-text="unitName"></option></template>
                                        <option value="Lainnya" class="font-bold text-red-600">Lainnya (Ketik Manual)...</option>
                                    </select>
                                    <input x-show="u.ketua === 'Lainnya'" type="text" name="peserta[ketua][unit_lainnya]" placeholder="Ketik Unit Kerja..." class="form-input mt-3" :required="u.ketua === 'Lainnya'">
                                </div>
                                <div class="min-w-0"><label class="form-label">No. HP *</label><input type="text" name="peserta[ketua][hp]" class="form-input" required></div>
                            </div>
                        </div>

                        <!-- SEKRETARIS (Untuk GKM, PKM, BI) -->
                        <div x-show="!isSS" class="p-6 sm:p-8 border border-slate-200 rounded-3xl bg-slate-50/50 transition-all hover:border-slate-300">
                            <h4 class="mb-6 text-sm font-bold tracking-widest text-slate-500 uppercase">3. Sekretaris</h4>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                                <div class="min-w-0"><label class="form-label">Nama Lengkap *</label><input type="text" name="peserta[sekretaris][nama]" class="form-input" :required="!isSS"></div>
                                <div class="min-w-0"><label class="form-label">No. SAP *</label><input type="text" name="peserta[sekretaris][sap]" class="form-input" :required="!isSS"></div>
                                <div class="min-w-0"><label class="form-label">Departemen *</label>
                                    <select x-model="d.sekretaris" @change="u.sekretaris = ''" name="peserta[sekretaris][dept]" class="form-input cursor-pointer" :required="!isSS">{!! $dropdownDept !!}</select>
                                    <input x-show="d.sekretaris === 'Lainnya'" type="text" name="peserta[sekretaris][dept_lainnya]" class="form-input mt-3" :required="d.sekretaris === 'Lainnya'">
                                </div>
                                <div class="min-w-0"><label class="form-label">Unit Kerja *</label>
                                    <select x-model="u.sekretaris" name="peserta[sekretaris][unit]" class="form-input cursor-pointer" :required="!isSS">
                                        <option value="">-- Pilih Unit Kerja --</option>
                                        <template x-for="unitName in getUnits('sekretaris')" :key="unitName"><option :value="unitName" x-text="unitName"></option></template>
                                        <option value="Lainnya" class="font-bold text-red-600">Lainnya (Ketik Manual)...</option>
                                    </select>
                                    <input x-show="u.sekretaris === 'Lainnya'" type="text" name="peserta[sekretaris][unit_lainnya]" class="form-input mt-3" :required="u.sekretaris === 'Lainnya'">
                                </div>
                            </div>
                        </div>

                        <!-- ANGGOTA DINAMIS -->
                        <template x-for="(id, index) in anggotaTambahan" :key="id">
                            <div x-data="{ deptAnggota: '', unitAnggota: '' }" class="p-6 sm:p-8 border border-slate-200 rounded-3xl bg-slate-50/50 mb-6 shadow-sm">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-3">
                                    <h4 class="text-sm font-bold tracking-widest text-slate-500 uppercase">
                                        <span x-text="(isSS ? 1 : 3) + index + 1"></span>. Anggota Tambahan
                                    </h4>
                                    <button type="button" @click="hapusAnggota(id)" class="text-xs text-red-500 font-bold hover:underline px-3.5 py-1.5 rounded-xl hover:bg-red-50 transition-colors border border-red-200">
                                        Hapus Baris Anggota
                                    </button>
                                </div>
                                    <button type="button" @click="hapusAnggota(id)" class="text-xs text-red-500 font-bold hover:underline px-3.5 py-1.5 rounded-xl hover:bg-red-50 transition-colors border border-red-200">
                                        Hapus Baris Anggota
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                                    <div class="min-w-0"><label class="form-label">Nama Lengkap *</label><input type="text" :name="'peserta[anggota_' + id + '][nama]'" class="form-input" required></div>
                                    <div class="min-w-0"><label class="form-label">No. SAP *</label><input type="text" :name="'peserta[anggota_' + id + '][sap]'" class="form-input" required></div>
                                    <div class="min-w-0"><label class="form-label">Departemen *</label>
                                        <select x-model="deptAnggota" @change="unitAnggota = ''" :name="'peserta[anggota_' + id + '][dept]'" class="form-input cursor-pointer" required>{!! $dropdownDept !!}</select>
                                        <input x-show="deptAnggota === 'Lainnya'" type="text" :name="'peserta[anggota_' + id + '][dept_lainnya]'" placeholder="Ketik Departemen..." class="form-input mt-3" :required="deptAnggota === 'Lainnya'">
                                    </div>
                                    <div class="min-w-0"><label class="form-label">Unit Kerja *</label>
                                        <select x-model="unitAnggota" :name="'peserta[anggota_' + id + '][unit]'" class="form-input cursor-pointer" required>
                                            <option value="">-- Pilih Unit Kerja --</option>
                                            <template x-for="unitName in (deptUnits[deptAnggota] || [])" :key="unitName"><option :value="unitName" x-text="unitName"></option></template>
                                            <option value="Lainnya" class="font-bold text-red-600">Lainnya (Ketik Manual)...</option>
                                        </select>
                                        <input x-show="unitAnggota === 'Lainnya'" type="text" :name="'peserta[anggota_' + id + '][unit_lainnya]'" placeholder="Ketik Unit Kerja..." class="form-input mt-3" :required="unitAnggota === 'Lainnya'">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Tombol Tambah Anggota -->
                        <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <button type="button" @click="tambahAnggota()" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 text-xs sm:text-sm font-extrabold text-slate-800 transition-all bg-white border-2 border-dashed border-slate-300 rounded-2xl hover:border-[#F5333F] hover:text-[#F5333F] hover:bg-red-50/50 shadow-sm">
                                <svg class="w-4 h-4 text-[#F5333F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tambah Anggota Tim (Tersisa: <strong x-text="sisaKursi"></strong>)</span>
                            </button>
                        </div>
                    </div>

                    <!-- BLOK 3: BENEFIT & UPLOAD DOKUMEN -->
                    <div x-show="kategori !== ''" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="space-y-10">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mt-12 sm:mt-16">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center bg-slate-900 text-white rounded-2xl font-black text-sm sm:text-base shadow-sm">3</div>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900">Bidang Benefit &amp; Unggah Makalah</h3>
                        </div>

                        <div>
                            <label class="form-label mb-4">Pilih Bidang Manfaat Inovasi (Minimal 1) <span class="text-[#F5333F]">*</span></label>
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                                @foreach(['Cost Benefit', 'Clinker Production', 'Cement Production', 'PROPER', 'K3'] as $benefit)
                                    <label class="relative flex items-center gap-3 p-4 bg-slate-50/60 border border-slate-200 rounded-2xl cursor-pointer hover:border-[#F5333F] hover:bg-red-50/20 transition-all select-none">
                                        <input type="checkbox" name="bidang_benefit[]" value="{{ $benefit }}" class="w-4 h-4 text-[#F5333F] border-slate-300 rounded focus:ring-[#F5333F]">
                                        <span class="text-xs font-bold text-slate-700">{{ $benefit }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div x-data="{ fileName: '', fileSize: '', isDragging: false }" class="p-8 sm:p-12 text-center border-2 border-dashed rounded-3xl transition-all"
                            :class="isDragging ? 'border-[#F5333F] bg-red-50/20' : (fileName ? 'border-emerald-500 bg-emerald-50/20' : 'border-slate-300 bg-slate-50/60 hover:border-[#F5333F] hover:bg-red-50/10')"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="isDragging = false">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm"
                                :class="fileName ? 'bg-emerald-100 text-emerald-600' : 'bg-red-50 text-[#F5333F]'">
                                <template x-if="!fileName">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                </template>
                                <template x-if="fileName">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </template>
                            </div>
                            <label class="block text-lg sm:text-xl font-black text-slate-900 mb-2">Unggah Dokumen Makalah Final <span class="text-[#F5333F]">*</span></label>
                            
                            <!-- State Saat File Terpilih -->
                            <div x-show="fileName" class="mb-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-emerald-300 shadow-sm text-xs font-bold text-emerald-800">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="truncate max-w-xs" x-text="fileName"></span>
                                <span class="text-slate-400">•</span>
                                <span x-text="fileSize"></span>
                            </div>

                            <p class="mb-6 text-xs sm:text-sm text-slate-500 leading-relaxed max-w-xl mx-auto">
                                Ukuran maksimal <strong class="text-slate-900">30 MB</strong>. Format: PDF, DOC, DOCX.<br>
                                Rekomendasi penamaan berkas: <span class="font-mono bg-white px-2 py-0.5 rounded border text-[#F5333F] font-bold">KATEGORI_NAMA TIM.pdf</span>
                            </p>
                            
                            <input type="file" name="file_makalah" accept=".pdf,.doc,.docx" 
                                @change="if ($event.target.files.length > 0) { fileName = $event.target.files[0].name; fileSize = ($event.target.files[0].size / (1024*1024)).toFixed(2) + ' MB'; }"
                                class="block w-full max-w-md mx-auto text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-[#F5333F] file:text-white hover:file:bg-red-700 transition-colors cursor-pointer focus:outline-none" required>
                        </div>
                    </div>

                    <!-- TOMBOL SUBMIT -->
                    <div x-show="kategori !== ''" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="pt-8 border-t border-slate-100">
                        <button type="submit" class="w-full py-5 text-base sm:text-lg font-black tracking-wider text-white uppercase transition-all duration-300 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 hover:from-[#F5333F] hover:to-rose-600 shadow-xl hover:shadow-red-500/30 transform hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-red-500/20 active:scale-[0.99]">
                            Kirim Formulir Pendaftaran KMST
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- ================= FOOTER ================= -->
    <footer class="py-12 bg-slate-900 text-slate-400 text-center text-xs border-t border-slate-800">
        <div class="max-w-7xl px-4 mx-auto sm:px-6 lg:px-8 space-y-3">
            <p class="font-bold text-slate-300 text-sm">Konvensi Mutu Semen Tonasa (KMST)</p>
            <p>Department of Human Capital &amp; GRC • Business &amp; System Development</p>
            <p class="text-slate-500 text-[11px]">&copy; {{ date('Y') }} PT Semen Tonasa. Hak Cipta Dilindungi Undang-Undang.</p>
        </div>
    </footer>

</body>
</html>
