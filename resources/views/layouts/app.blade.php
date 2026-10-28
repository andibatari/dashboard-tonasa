<!DOCTYPE html>
<html lang="id" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'STMC Health Portal')</title>
    
    {{-- 1. Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{-- 2. FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    {{-- 3. FullCalendar (untuk tampilan jadwal dokter) --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    {{-- 3. Tailwind CSS CDN & Config --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                            950: '#450a0a',
                        }
                    }
                }
            }
        }
    </script>

    {{-- 4. Pusher Script --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = true;
        var pusher = new Pusher('c04c6e0bc13266555594', { cluster: 'ap1' });
        var channel = pusher.subscribe('mcu-channel');
        
        channel.bind('StatusPoliUpdatedEvent', function(data) {
            console.log("Status Poli Berubah, me-refresh halaman...");
            if (typeof window.Livewire !== 'undefined') {
                window.Livewire.dispatch('$refresh');
            } else {
                window.location.reload();
            }
        });
    </script>

    {{-- 5. Custom CSS --}}
    <style>
        body { background-color: #f8fafc; }
        
        /* Sidebar Base Style */
        .sidebar {
            position: fixed;
            top: 0; bottom: 0; left: 0;
            z-index: 40; 
            width: 17rem;
            transform: translateX(-100%); 
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 24px rgba(0,0,0,0.1);
        }
        .sidebar.sidebar-open { transform: translateX(0%); }
        
        @media (min-width: 1024px) { 
            .sidebar { transform: translateX(0%); }
        }

        /* Main Content Area */
        .main-content-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: 0;
            min-width: 0;
            width: 100%;
        }
        @media (min-width: 1024px) { 
            .main-content-area { margin-left: 17rem; }
        }

        /* Overlay Mobile */
        .sidebar-overlay {
            position: fixed; inset: 0;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 35; 
            display: none; 
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .sidebar-overlay.show { display: block; opacity: 1; }
        @media (min-width: 1024px) { .sidebar-overlay { display: none !important; } }
        
        body.sidebar-mobile-open { overflow: hidden; }

        /* Styling Menu Aktif (Glassmorphism & Neon effect) */
        .sidebar-link {
            transition: all 0.2s ease-in-out;
        }
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-left: 4px solid #fca5a5;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
            font-weight: 600;
        }
        .sidebar-link:not(.active) { margin-left: 0.5rem; border-left: 4px solid transparent; }
        .sidebar-link:hover:not(.active) { background: rgba(255, 255, 255, 0.05); transform: translateX(4px); }
        
        /* Submenu Styling */
        .sub-menu a { transition: all 0.2s; position: relative; }
        .sub-menu a::before {
            content: ''; position: absolute; left: -12px; top: 50%; width: 6px; height: 6px;
            background-color: rgba(255,255,255,0.3); border-radius: 50%; transform: translateY(-50%);
        }
        .sub-menu a:hover { color: #fff !important; transform: translateX(4px); }
        .sub-menu a.active { color: #fff !important; font-weight: 600; }
        .sub-menu a.active::before { background-color: #fca5a5; box-shadow: 0 0 8px #fca5a5; }

        /* Scrollbar Premium */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
        .sidebar-nav-container::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.2); }
        .sidebar-nav-container::-webkit-scrollbar-track { background: transparent; }
    </style>
    @livewireStyles
    @stack('head')
</head>

<body class="text-slate-800 selection:bg-brand-500 selection:text-white">
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="flex min-h-screen">
        {{-- SIDEBAR: Gradient Super Premium --}}
        <aside id="sidebar" class="bg-gradient-to-b from-brand-900 via-brand-800 to-brand-950 text-slate-100 flex flex-col sidebar">
            
            {{-- LOGO AREA --}}
            <div class="px-6 py-5 flex items-center border-b border-white/10 bg-black/10 backdrop-blur-sm">
                <div class="bg-white p-1 rounded-xl shadow-lg mr-3">
                    <img src="{{asset('images/LogoStmc.png')}}" alt="STMC Logo" class="h-10 w-10 object-contain">
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-xl tracking-wide text-white">STMC</span>
                    <span class="text-[10px] font-medium text-brand-200 tracking-wider uppercase">Semen Tonasa Medical Centre</span>
                </div>
            </div>
            
            {{-- NAVIGATION --}}
            <div class="mt-6 sidebar-nav-container flex-grow h-full overflow-y-auto px-3 pb-6">
                <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-brand-300/60 mb-2">Menu Utama</p>
                <nav>
                    <ul class="space-y-1.5">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="sidebar-link group flex items-center p-3.5 text-sm text-brand-100 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-th-large w-6 text-center text-lg opacity-70 group-hover:opacity-100 transition-opacity"></i>
                                <span class="ml-3">Dashboard</span>
                            </a>
                        </li>
                        
                        {{-- MANAJEMEN PASIEN --}}
                        @if(in_array(Auth::guard('admin_users')->user()->role ?? '', ['superadmin', 'admin']))
                        <li>
                            <button id="toggleKaryawan" class="w-full text-left sidebar-link group flex items-center justify-between p-3.5 text-sm text-brand-100 rounded-xl {{ request()->routeIs('karyawan.*') ? 'active' : '' }}">
                                <span class="flex items-center">
                                    <i class="fas fa-users w-6 text-center text-lg opacity-70 group-hover:opacity-100"></i>
                                    <span class="ml-3">Manajemen Pasien</span>
                                </span>
                                <i id="arrowKaryawan" class="fas fa-chevron-down text-[10px] transition-transform duration-300"></i>
                            </button>
                            <ul id="submenuKaryawan" class="sub-menu pl-11 pr-2 mt-1 space-y-1 {{ request()->routeIs('karyawan.*') ? 'block' : 'hidden' }}">
                                <li>
                                    <a href="{{ route('karyawan.index') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('karyawan.index') ? 'active' : '' }}">
                                        Daftar Pasien
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        
                        {{-- MANAJEMEN JADWAL --}}
                        <li>
                            <button id="toggleJadwal" class="w-full text-left sidebar-link group flex items-center justify-between p-3.5 text-sm text-brand-100 rounded-xl {{ request()->routeIs('jadwal.*') || request()->routeIs('scan.qr') ? 'active' : '' }}">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar-alt w-6 text-center text-lg opacity-70 group-hover:opacity-100"></i>
                                    <span class="ml-3">Manajemen Jadwal</span>
                                </span>
                                <i id="arrowJadwal" class="fas fa-chevron-down text-[10px] transition-transform duration-300"></i>
                            </button>
                            <ul id="submenuJadwal" class="sub-menu pl-11 pr-2 mt-1 space-y-1 {{ request()->routeIs('jadwal.*') || request()->routeIs('scan.qr') ? 'block' : 'hidden' }}">
                                <li>
                                    <a href="{{ route('jadwal.index') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('jadwal.index') ? 'active' : '' }}">Daftar Jadwal MCU</a>
                                </li>
                                <li>
                                    <a href="{{ route('scan.qr') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('scan.qr') ? 'active' : '' }}">Registrasi via QR</a>
                                </li>
                            </ul>
                        </li>
                        
                        {{-- MANAJEMEN LINGKUNGAN --}}
                        <li>
                            <button id="toggleLingkungan" class="w-full text-left sidebar-link group flex items-center justify-between p-3.5 text-sm text-brand-100 rounded-xl {{ request()->routeIs('pemantauan.*') ? 'active' : '' }}">
                                <span class="flex items-center">
                                    <i class="fas fa-leaf w-6 text-center text-lg opacity-70 group-hover:opacity-100"></i>
                                    <span class="ml-3">Lingkungan Kerja</span>
                                </span>
                                <i id="arrowLingkungan" class="fas fa-chevron-down text-[10px] transition-transform duration-300"></i>
                            </button>
                            <ul id="submenuLingkungan" class="sub-menu pl-11 pr-2 mt-1 space-y-1 {{ request()->routeIs('pemantauan.*') ? 'block' : 'hidden' }}">
                                <li>
                                    <a href="{{ route('pemantauan.index') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('pemantauan.index') ? 'active' : '' }}">Monitoring Area</a>
                                </li>
                            </ul>
                        </li>

                        {{-- NOTIFIKASI --}}
                        @if(in_array(Auth::guard('admin_users')->user()->role ?? '', ['superadmin', 'admin']))
                        <div class="pt-4 pb-1"><div class="h-px bg-white/10 w-full"></div></div>
                        <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-brand-300/60 mb-2">Sistem</p>
                        
                        <li>
                            <button id="toggleNotifikasi" class="w-full text-left sidebar-link group flex items-center justify-between p-3.5 text-sm text-brand-100 rounded-xl {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                                <span class="flex items-center">
                                    <i class="fas fa-bell w-6 text-center text-lg opacity-70 group-hover:opacity-100"></i>
                                    <span class="ml-3">Pusat Notifikasi</span>
                                </span>
                                <i id="arrowNotifikasi" class="fas fa-chevron-down text-[10px] transition-transform duration-300"></i>
                            </button>
                            <ul id="submenuNotifikasi" class="sub-menu pl-11 pr-2 mt-1 space-y-1 {{ request()->routeIs('notifications.*') ? 'block' : 'hidden' }}">
                                <li>
                                    <a href="{{ route('notifications.dashboard') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('notifications.dashboard') ? 'active' : '' }}">Dashboard Notif</a>
                                </li>
                                <li>
                                    <a href="{{ route('notifications.history') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('notifications.history') ? 'active' : '' }}">Riwayat Pengiriman</a>
                                </li>
                            </ul>
                        </li>
                        
                        {{-- AUDIT TRAIL & KEAMANAN --}}
                        <li>
                            <a href="{{ route('audit.trail') }}" class="sidebar-link group flex items-center p-3.5 text-sm text-brand-100 rounded-xl {{ request()->routeIs('audit.trail') ? 'active' : '' }}">
                                <i class="fas fa-shield-alt w-6 text-center text-lg opacity-70 group-hover:opacity-100 transition-opacity"></i>
                                <span class="ml-3">Audit & Keamanan</span>
                            </a>
                        </li>
                        {{-- MANAJEMEN LAYANAN --}}
                        <li>
                            <button id="toggleAdmin" class="w-full text-left sidebar-link group flex items-center justify-between p-3.5 text-sm text-brand-100 rounded-xl {{ request()->routeIs(['admin.create', 'admin.tambah-dokter', 'paket-poli', 'admin.laporan.pemeriksaan']) ? 'active' : '' }}">
                                <span class="flex items-center">
                                    <i class="fas fa-cogs w-6 text-center text-lg opacity-70 group-hover:opacity-100"></i>
                                    <span class="ml-3">Manajemen Layanan</span>
                                </span>
                                <i id="arrowAdmin" class="fas fa-chevron-down text-[10px] transition-transform duration-300"></i>
                            </button>
                            <ul id="submenuAdmin" class="sub-menu pl-11 pr-2 mt-1 space-y-1 {{ request()->routeIs(['admin.create', 'admin.tambah-dokter', 'paket-poli', 'admin.laporan.pemeriksaan']) ? 'block' : 'hidden' }}">
                                <li><a href="{{ route('admin.create') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('admin.create') ? 'active' : '' }}">Kelola Admin</a></li>
                                <li><a href="{{ route('admin.tambah-dokter') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('admin.tambah-dokter') ? 'active' : '' }}">Kelola Dokter</a></li>
                                <li><a href="{{ route('paket-poli') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('paket-poli') ? 'active' : '' }}">Paket & Poli MCU</a></li>
                                <li><a href="{{ route('admin.jadwal-dokter') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('admin.jadwal-dokter') ? 'active' : '' }}">Jadwal Dokter</a></li>
                                <li><a href="{{ route('admin.laporan.pemeriksaan') }}" class="block py-2 text-sm text-brand-200 {{ request()->routeIs('admin.laporan.pemeriksaan') ? 'active' : '' }}">Rekap & Ekspor</a></li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>

            {{-- LOGOUT BUTTON --}}
            <div class="p-4 bg-black/10 backdrop-blur-md border-t border-white/10 mt-auto">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center bg-white/10 hover:bg-brand-500 text-white font-bold py-3 border border-white/20 rounded-xl shadow-lg transition-all duration-300 group">
                        <i class="fas fa-sign-out-alt mr-2 group-hover:-translate-x-1 transition-transform"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <div id="mainContentArea" class="main-content-area">
            
            {{-- FLOATING HEADER (GLASSMORPHISM) --}}
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md shadow-[0_4px_20px_rgba(0,0,0,0.03)] border-b border-slate-200 px-4 md:px-8 py-3 lg:py-4 flex items-center justify-between transition-all">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="text-slate-500 hover:text-brand-600 lg:hidden p-2 -ml-2 rounded-lg hover:bg-slate-100 transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="text-lg md:text-xl font-black text-slate-800 tracking-tight ml-2 lg:ml-0">
                        @yield('title', 'Dashboard')
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 md:space-x-5">
                    
                    {{-- DROPDOWN NOTIFIKASI --}}
                    <livewire:notification-header />

                    <div class="h-6 w-px bg-slate-200 hidden md:block"></div>

                    {{-- DROPDOWN PROFIL ADMIN --}}
                    <div class="relative">
                        <button id="profileDropdownButton" class="flex items-center gap-3 focus:outline-none p-1.5 pr-3 rounded-full hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all">
                            @php
                                $user = Auth::guard('admin_users')->user();
                                $photoUrl = null;
                                if (isset($user) && $user->foto_profil) {
                                    if (str_starts_with($user->foto_profil, 'admin_photos/')) {
                                        $photoUrl = asset($user->foto_profil) . '?t=' . now()->timestamp;
                                    } else {
                                        $photoUrl = asset('storage/' . $user->foto_profil) . '?t=' . now()->timestamp;
                                    }
                                } else {
                                    $photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap ?? 'Admin') . '&color=FFFFFF&background=ef4444&size=100&bold=true';
                                }
                            @endphp
                            <div class="relative">
                                <img src="{{ $photoUrl }}" alt="Admin Profile" class="h-9 w-9 md:h-10 md:w-10 rounded-full object-cover border-2 border-white shadow-sm"> 
                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                            </div>
                            <div class="hidden lg:flex flex-col items-start"> 
                                <span class="font-bold text-sm text-slate-800 truncate max-w-[130px]">{{ Auth::guard('admin_users')->user()->nama_lengkap ?? 'Administrator' }}</span>
                                <span class="text-slate-400 text-[11px] font-medium tracking-wide">{{ Auth::guard('admin_users')->user()->role ?? 'Admin' }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 hidden lg:block ml-1"></i>
                        </button>
                        
                        <div id="profileDropdownMenu" class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] overflow-hidden z-50 border border-slate-100 hidden transform origin-top-right transition-all">
                            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 lg:hidden">
                                <p class="font-bold text-sm text-slate-800 truncate">{{ Auth::guard('admin_users')->user()->nama_lengkap ?? 'Administrator' }}</p>
                                <p class="text-slate-500 text-xs">{{ Auth::guard('admin_users')->user()->email ?? 'admin@stmc.com' }}</p>
                            </div>
                            <a href="{{ route('admin.profile.edit') }}" class="flex items-center px-5 py-3.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">
                                <i class="fas fa-user-circle w-5 text-center mr-2 text-slate-400"></i> Profil Saya
                            </a>
                            <a href="{{ route('admin.settings') }}" class="flex items-center px-5 py-3.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">
                                <i class="fas fa-cog w-5 text-center mr-2 text-slate-400"></i> Pengaturan Sistem
                            </a>
                            <div class="border-t border-slate-100">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-5 py-3.5 text-sm font-bold text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-5 text-center mr-2"></i> Keluar 
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- BODY CONTENT --}}
            <main class="p-4 md:p-6 lg:p-8 flex-1 overflow-y-auto">
                {{-- Global Alerts --}}
                @if (session('error'))
                    <div class="flex items-center p-4 mb-6 text-sm text-red-800 border border-red-200 rounded-2xl bg-red-50 shadow-sm animate-fade-in-down" role="alert">
                        <i class="fas fa-exclamation-triangle text-lg mr-3 text-red-500"></i>
                        <div><span class="font-bold uppercase tracking-wide text-xs mr-2 bg-red-200 px-2 py-0.5 rounded text-red-800">Error</span> {{ session('error') }}</div>
                    </div>
                @endif
                @if (session('success'))
                    <div class="flex items-center p-4 mb-6 text-sm text-emerald-800 border border-emerald-200 rounded-2xl bg-emerald-50 shadow-sm animate-fade-in-down" role="alert">
                        <i class="fas fa-check-circle text-lg mr-3 text-emerald-500"></i>
                        <div><span class="font-bold uppercase tracking-wide text-xs mr-2 bg-emerald-200 px-2 py-0.5 rounded text-emerald-800">Sukses</span> {{ session('success') }}</div>
                    </div>
                @endif

                {{-- SLOT / CONTENT INJECTION --}}
                @yield('content')
                @isset($slot)
                    {{ $slot }}
                @endisset
            </main>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Accordion Logic
            const menuConfigs = [
                { toggle: 'toggleKaryawan', submenu: 'submenuKaryawan', arrow: 'arrowKaryawan', routeActive: {{ request()->routeIs('karyawan.*') ? 'true' : 'false' }} },
                { toggle: 'toggleJadwal', submenu: 'submenuJadwal', arrow: 'arrowJadwal', routeActive: {{ request()->routeIs('jadwal.*') || request()->routeIs('scan.qr') ? 'true' : 'false' }} },
                { toggle: 'toggleAdmin', submenu: 'submenuAdmin', arrow: 'arrowAdmin', routeActive: {{ request()->routeIs(['admin.create', 'admin.tambah-dokter', 'paket-poli', 'admin.laporan.pemeriksaan']) ? 'true' : 'false' }} },
                { toggle: 'toggleLingkungan', submenu: 'submenuLingkungan', arrow: 'arrowLingkungan', routeActive: {{ request()->routeIs('pemantauan.*') ? 'true' : 'false' }} },
                { toggle: 'toggleNotifikasi', submenu: 'submenuNotifikasi', arrow: 'arrowNotifikasi', routeActive: {{ request()->routeIs('notifications.*') ? 'true' : 'false' }} }
            ];

            function toggleSubmenu(submenu, arrow, forceOpen = false) {
                if (!submenu || !arrow) return;
                const isCurrentlyOpen = submenu.style.display === 'block';

                if (forceOpen || !isCurrentlyOpen) {
                    menuConfigs.forEach(config => {
                        const otherSubmenu = document.getElementById(config.submenu);
                        const otherArrow = document.getElementById(config.arrow);
                        if (otherSubmenu && otherSubmenu !== submenu) {
                            otherSubmenu.style.display = 'none';
                            otherArrow && (otherArrow.style.transform = 'rotate(0deg)');
                        }
                    });
                    submenu.style.display = 'block'; 
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    submenu.style.display = 'none'; 
                    arrow.style.transform = 'rotate(0deg)';
                }
            }
            
            menuConfigs.forEach(config => {
                const toggleBtn = document.getElementById(config.toggle);
                const submenu = document.getElementById(config.submenu);
                const arrow = document.getElementById(config.arrow);

                if (toggleBtn) {
                    if (config.routeActive === 'true' && submenu) {
                        submenu.style.display = 'block';
                        arrow && (arrow.style.transform = 'rotate(180deg)');
                    } else if(submenu) {
                        submenu.style.display = 'none';
                    }
                    toggleBtn.addEventListener('click', () => toggleSubmenu(submenu, arrow));
                }
            });

            // Dropdown Logic (Header)
            const setupDropdown = (btnId, menuId) => {
                const btn = document.getElementById(btnId);
                const menu = document.getElementById(menuId);
                if (btn && menu) {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        // Hide all other dropdowns
                        document.querySelectorAll('[id$="DropdownMenu"]').forEach(m => {
                            if(m !== menu) m.classList.add('hidden');
                        });
                        menu.classList.toggle('hidden');
                    });
                }
            };
            setupDropdown('notificationDropdownButton', 'notificationDropdownMenu');
            setupDropdown('profileDropdownButton', 'profileDropdownMenu');

            window.addEventListener('click', () => {
                document.querySelectorAll('[id$="DropdownMenu"]').forEach(m => m.classList.add('hidden'));
            });

            // Mobile Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            const toggleMobileSidebar = () => {
                const isOpen = sidebar.classList.toggle('sidebar-open');
                sidebarOverlay.classList.toggle('show');
                document.body.classList.toggle('sidebar-mobile-open', isOpen);
            };

            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', toggleMobileSidebar);
                sidebarOverlay.addEventListener('click', toggleMobileSidebar);
            }

            // Reset on desktop resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) { 
                    sidebar.classList.remove('sidebar-open');
                    sidebarOverlay.classList.remove('show');
                    document.body.classList.remove('sidebar-mobile-open');
                }
            });
        });

        // Livewire Handlers
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('view-merged-pdf', (event) => { 
                const data = event[0]; 
                if (data.jadwalId) {
                    const url = `${window.location.origin}/admin/download-mcu-summary/${data.jadwalId}`;
                    const newWindow = window.open(url, '_blank'); 
                    if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                        alert('Pop-up blocker terdeteksi. Izinkan pop-up untuk mencetak hasil.');
                    }
                }
            });
        });

        function togglePasswordVisibility(inputId, eyeOpenId, eyeClosedId) {
            const passwordInput = document.getElementById(inputId);
            const eyeOpen = document.getElementById(eyeOpenId);
            const eyeClosed = document.getElementById(eyeClosedId);

            if (passwordInput && eyeOpen && eyeClosed) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                } else {
                    passwordInput.type = 'password';
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                }
            }
        }
    </script>
    @livewireScripts
    @stack('scripts') 
</body>
</html>