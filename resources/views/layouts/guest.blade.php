<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PT Semen Tonasa') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/tonasa-logo.png') }}?v=2">
    
    <!-- Tipografi Modern & Bersih: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; letter-spacing: -0.02em; }

        .form-in { opacity: 0; transform: translateY(14px); animation: formIn .7s cubic-bezier(.2,.7,.2,1) .1s forwards; }
        @keyframes formIn { to { opacity: 1; transform: translateY(0); } }

        @media (prefers-reduced-motion: reduce) {
            .form-in { animation: none; opacity: 1; transform: none; }
        }
    </style>
</head>
<body class="antialiased text-slate-600 bg-white selection:bg-[#F5333F] selection:text-white">
    <div class="flex min-h-screen">

        <!-- ================= PANEL KIRI (DESKTOP) ================= -->
        <aside class="relative hidden overflow-hidden bg-center bg-cover lg:flex lg:w-[52%] bg-slate-900"
               style="background-image: url('{{ asset('images/Jembatan Soekarno Manado.jpg') }}');">

            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/55 to-black/40"></div>
            <div class="absolute -left-32 -top-32 w-[28rem] h-[28rem] rounded-full bg-[#F5333F]/25 blur-[110px] pointer-events-none"></div>

            <div class="relative z-10 flex flex-col justify-between w-full px-12 py-12 xl:px-20 xl:py-16">

                <div class="flex items-center">
                    <img src="{{ asset('images/tonasa-logo.png') }}" alt="Semen Tonasa" class="object-contain w-auto h-14 drop-shadow-lg">
                </div>

                <div class="max-w-lg">
                    <h2 class="text-4xl font-extrabold leading-[1.1] text-white font-display xl:text-5xl">
                        Semua operasional unit, <span class="text-[#FF5A64]">dalam satu pintu.</span>
                    </h2>
                    <p class="mt-5 text-lg leading-relaxed text-slate-200">
                        Pantau, evaluasi, dan kelola aktivitas antar unit Semen Tonasa dari satu platform terpadu.
                    </p>
                </div>

                <div class="pt-6 border-t border-white/20">
                    <p class="mt-1 text-xs text-slate-300">&copy; {{ date('Y') }} PT Semen Tonasa. Hak cipta dilindungi.</p>
                </div>
            </div>
        </aside>

        <!-- ================= PANEL KANAN (FORM) ================= -->
        <main class="relative flex flex-col w-full min-h-screen lg:w-[48%] bg-gradient-to-b from-white to-slate-50">

            <div class="flex items-center justify-between px-6 pt-6 sm:px-12 lg:px-16">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-medium transition rounded text-slate-500 hover:text-[#F5333F] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#F5333F]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke beranda
                </a>
            </div>

            <div class="flex items-center justify-center flex-1 px-6 py-10 sm:px-12 lg:px-16">
                <div class="w-full max-w-md form-in">
                    {{ $slot }}
                </div>
            </div>

            <p class="px-6 pb-6 text-xs text-center text-slate-400 lg:hidden">
                &copy; {{ date('Y') }} PT Semen Tonasa
            </p>
        </main>

    </div>
</body>
</html>
