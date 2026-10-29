<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold font-display text-slate-900 sm:text-4xl">Selamat Datang</h1>
        <p class="mt-3 text-base text-slate-500">Masuk dengan akun korporat Anda untuk membuka ruang kerja.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="p-6 bg-white border shadow-xl rounded-3xl border-slate-200 shadow-slate-200/60 sm:p-8">
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-900">Alamat email</label>
                <div class="relative mt-2">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        placeholder="nama@sig.id"
                        class="block w-full py-3 pl-12 pr-4 text-sm transition border rounded-xl border-slate-300 bg-slate-50 placeholder:text-slate-400 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-[#F5333F]" />
            </div>

            <!-- Password -->
            <div x-data="{ show: false }">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-semibold text-slate-900">Kata sandi</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold transition rounded text-slate-500 hover:text-[#F5333F] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F5333F]">Lupa sandi?</a>
                    @endif
                </div>

                <div class="relative mt-2">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                        placeholder="Masukkan kata sandi"
                        class="block w-full py-3 pl-12 text-sm transition border pr-12 rounded-xl border-slate-300 bg-slate-50 placeholder:text-slate-400 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none">

                    <button type="button" @click="show = !show" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        class="absolute inset-y-0 right-0 flex items-center px-4 transition rounded-r-xl text-slate-400 hover:text-[#F5333F] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#F5333F]">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-[#F5333F]" />
            </div>

            <!-- Remember -->
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-slate-300 text-[#F5333F] focus:ring-[#F5333F]">
                <label for="remember_me" class="ml-2.5 text-sm text-slate-600">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit"
                class="w-full px-4 py-3.5 text-sm font-semibold text-white transition bg-[#F5333F] rounded-xl shadow-lg shadow-red-500/25 hover:bg-red-600 hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F5333F]">
                Masuk ke dashboard
            </button>
        </form>

        <!-- Pemisah SSO Akun Korporat SIG -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="px-3 bg-white text-slate-400 font-semibold tracking-wider">Atau Masuk dengan Akun SIG</span>
            </div>
        </div>

        @if ($errors->has('oauth'))
            <div class="p-3 mb-4 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl">
                {{ $errors->first('oauth') }}
            </div>
        @endif

        <div class="space-y-3">
            <!-- Tombol Microsoft (Akun Resmi Korporat SIG / Azure AD) -->
            <a href="{{ route('oauth.redirect', 'microsoft') }}"
                class="flex items-center justify-center w-full px-4 py-3 text-sm font-semibold transition bg-white border rounded-xl border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 hover:shadow-sm">
                <svg class="w-5 h-5 mr-3 shrink-0" viewBox="0 0 21 21">
                    <rect x="1" y="1" width="9" height="9" fill="#f25022"/>
                    <rect x="11" y="1" width="9" height="9" fill="#7fba00"/>
                    <rect x="1" y="11" width="9" height="9" fill="#00a4ef"/>
                    <rect x="11" y="11" width="9" height="9" fill="#ffb900"/>
                </svg>
                <span>Masuk dengan Akun SIG (Microsoft)</span>
            </a>

            <!-- Tombol Google (Google Workspace) -->
            <a href="{{ route('oauth.redirect', 'google') }}"
                class="flex items-center justify-center w-full px-4 py-3 text-sm font-semibold transition bg-white border rounded-xl border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 hover:shadow-sm">
                <svg class="w-5 h-5 mr-3 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Masuk dengan Google</span>
            </a>
        </div>
    </div>

    @if (Route::has('register'))
        <p class="mt-8 text-sm text-center text-slate-500">
            Belum punya akun unit?
            <a href="{{ route('register') }}" class="font-semibold text-[#F5333F] transition hover:text-red-700">Daftar sekarang</a>
        </p>
    @endif
</x-guest-layout>
