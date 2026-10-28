<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | STMC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('{{asset('images/bgSemenTonasa.png')}}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        /* Efek gelap transparan untuk background agar card login lebih menonjol */
        .bg-overlay {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(153, 27, 27, 0.6) 100%);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-overlay p-4">
    
    <div class="w-full max-w-4xl bg-white/95 backdrop-blur-xl rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/20 overflow-hidden flex flex-col lg:flex-row h-auto lg:h-[550px]">
        
        {{-- KIRI: Banner / Ilustrasi --}}
        <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-br from-red-800 to-red-950 p-10 flex-col justify-between relative overflow-hidden text-white">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8 bg-white/10 p-3 rounded-2xl w-max backdrop-blur-sm border border-white/10">
                    <img src="{{ asset('images/LogoStmc.png') }}" alt="STMC Logo" class="h-10 w-10 object-contain bg-white rounded-xl p-1">
                    <div>
                        <h2 class="font-black text-xl tracking-wide leading-none">STMC</h2>
                        <p class="text-[10px] uppercase tracking-widest text-red-200">Semen Tonasa Medical Centre</p>
                    </div>
                </div>
                <h1 class="text-3xl font-black mb-4 leading-tight">Layanan<br>Kesehatan<br>Terbaik.</h1>
                <p class="text-red-200 text-sm font-medium leading-relaxed">Sistem Informasi Medical Check-Up Terpadu PT. Semen Tonasa.</p>
            </div>
            <img src="{{ asset('images/ilustrasi_dokter.png')}}" alt="Ilustrasi" class="relative z-10 w-4/5 mx-auto mt-4 drop-shadow-2xl hover:scale-105 transition-transform duration-500">
        </div>

        {{-- KANAN: Form Login --}}
        <div class="w-full lg:w-7/12 p-8 sm:p-12 flex flex-col justify-center bg-white relative">
            
            {{-- Header Mobile --}}
            <div class="flex items-center justify-center gap-3 mb-8 lg:hidden bg-red-50 py-4 rounded-2xl border border-red-100">
                <img src="{{ asset('images/LogoStmc.png') }}" alt="STMC Logo" class="h-10 w-10 object-contain bg-white rounded-xl p-1 shadow-sm">
                <div>
                    <h2 class="font-black text-xl text-red-800 leading-none">STMC</h2>
                    <p class="text-[10px] uppercase tracking-widest text-red-500 font-bold">Semen Tonasa Medical Centre</p>
                </div>
            </div>
            
            <div class="mb-8 text-center lg:text-left">
                <h2 class="text-2xl sm:text-3xl font-black text-slate-800 mb-2">Selamat Datang! 👋</h2>
                <p class="text-sm font-medium text-slate-500">Silakan masuk menggunakan kredensial Anda.</p>
            </div>
            
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl flex items-center mb-6 text-sm font-medium shadow-sm">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="login_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-2"> No. SAP / NIK / Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input type="text" id="login_id" name="login_id" value="{{ old('login_id') }}" required autofocus
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all"
                            placeholder="Masukkan identitas Anda">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="text-xs font-bold text-slate-600 uppercase tracking-wide">Kata Sandi</label>
                    </div>
                    
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="block w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all"
                            placeholder="••••••••">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-red-600 focus:outline-none transition-colors">
                            <img id="eyeOpen" src="{{ asset('images/eye-open.png') }}" alt="Show" class="h-5 w-5 opacity-70">
                            <img id="eyeClosed" src="{{ asset('images/eye-closed.png') }}" alt="Hide" class="h-5 w-5 hidden opacity-70">
                        </button>
                    </div>
                    {{-- 3. Tombol Lupa Sandi (Ditambahkan di sini, di bawah kanan input) --}}
                    <div class="flex justify-end mt-2">
                        <a href="javascript:void(0)" onclick="tampilkanLupaSandi()" class="text-xs font-bold text-red-600 hover:text-red-800 transition-colors">
                            Lupa sandi?
                        </a>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center items-center py-4 px-4 border border-transparent rounded-2xl shadow-lg shadow-red-600/30 text-sm font-black text-white bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 hover:-translate-y-0.5 transition-all duration-300">
                        Masuk<svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
            
            <p class="text-center text-xs text-slate-400 font-medium mt-8">
                &copy; {{ date('Y') }} PT. Semen Tonasa Medical Centre.<br>Dilindungi oleh sistem keamanan.
            </p>
        </div>
    </div>

    <script>

        // Fungsi untuk pop-up Lupa Sandi yang lebih proporsional
        function tampilkanLupaSandi() {
            Swal.fire({
                icon: 'info',
                title: 'Lupa Kata Sandi?',
                text: 'Untuk alasan keamanan rekam medis, silakan hubungi Tim IT atau Superadmin STMC secara langsung untuk mereset kata sandi Anda.',
                confirmButtonColor: '#dc2626',
                width: '320px', // Membatasi lebar maksimal agar tidak penuh di layar HP
                padding: '1.5em', // Mengurangi jarak ruang kosong di dalam pop-up
                customClass: {
                    popup: 'rounded-2xl', // Lengkungan sudut yang tidak terlalu ekstrem
                    title: 'text-lg font-black text-slate-800', // Ukuran judul diperkecil
                    htmlContainer: 'text-xs text-slate-500 font-medium', // Ukuran teks penjelasan diperkecil
                    confirmButton: 'px-5 py-2 text-xs font-bold rounded-xl shadow-sm' // Memperkecil tombol OK
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeOpen.classList.add('hidden');
                        eyeClosed.classList.remove('hidden');
                    } else {
                        passwordInput.type = 'password';
                        eyeOpen.classList.remove('hidden');
                        eyeClosed.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>