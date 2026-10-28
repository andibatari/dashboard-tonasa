<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold font-display text-slate-900 sm:text-4xl">Buat akun baru</h1>
        <p class="mt-3 text-base text-slate-500">Lengkapi data diri Anda untuk mulai memakai portal layanan.</p>
    </div>

    <div class="p-6 bg-white border shadow-xl rounded-3xl border-slate-200 shadow-slate-200/60 sm:p-8">
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Nama -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-900">Nama lengkap</label>
                <div class="relative mt-2">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                        placeholder="Nama Anda"
                        class="block w-full py-3 pl-12 pr-4 text-sm transition border rounded-xl border-slate-300 bg-slate-50 placeholder:text-slate-400 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-[#F5333F]" />
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-900">Alamat email</label>
                <div class="relative mt-2">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                        placeholder="nama@sig.id"
                        class="block w-full py-3 pl-12 pr-4 text-sm transition border rounded-xl border-slate-300 bg-slate-50 placeholder:text-slate-400 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-[#F5333F]" />
            </div>

            <!-- Password -->
            <div x-data="{ show: false }">
                <label for="password" class="block text-sm font-semibold text-slate-900">Kata sandi</label>
                <div class="relative mt-2">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                        class="block w-full py-3 pl-12 text-sm transition border pr-12 rounded-xl border-slate-300 bg-slate-50 placeholder:text-slate-400 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none">

                    <button type="button" @click="show = !show" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        class="absolute inset-y-0 right-0 flex items-center px-4 transition rounded-r-xl text-slate-400 hover:text-[#F5333F] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#F5333F]">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-[#F5333F]" />
            </div>

            <!-- Konfirmasi password -->
            <div x-data="{ show: false }">
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-900">Konfirmasi kata sandi</label>
                <div class="relative mt-2">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                    <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                        placeholder="Ulangi kata sandi"
                        class="block w-full py-3 pl-12 text-sm transition border pr-12 rounded-xl border-slate-300 bg-slate-50 placeholder:text-slate-400 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none">

                    <button type="button" @click="show = !show" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        class="absolute inset-y-0 right-0 flex items-center px-4 transition rounded-r-xl text-slate-400 hover:text-[#F5333F] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#F5333F]">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-[#F5333F]" />
            </div>

            <button type="submit"
                class="w-full px-4 py-3.5 mt-2 text-sm font-semibold text-white transition bg-[#F5333F] rounded-xl shadow-lg shadow-red-500/25 hover:bg-red-600 hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F5333F]">
                Daftar akun baru
            </button>
        </form>
    </div>

    @if (Route::has('login'))
        <p class="mt-8 text-sm text-center text-slate-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-[#F5333F] transition hover:text-red-700">Masuk di sini</a>
        </p>
    @endif
</x-guest-layout>
