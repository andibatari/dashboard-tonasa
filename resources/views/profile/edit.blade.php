<x-app-layout>
    <x-slot name="header">
        {{ __('Profil Akun') }}
    </x-slot>

    <div class="py-8">
        <div class="w-full">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- KOLOM KIRI: Informasi Utama & Foto Profil -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800 rounded-3xl shadow-sm overflow-hidden text-center p-5 sm:p-8">
                        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile-form">
                            @csrf
                            @method('patch')

                            <!-- Area Foto Profil Interaktif -->
                            <div class="relative w-32 h-32 mx-auto mb-6 group cursor-pointer" id="avatar-container" onclick="document.getElementById('avatar-input').click()">
                                @if(Auth::user()->avatar)
                                    <img id="avatar-preview" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Foto Profil" class="w-full h-full object-cover rounded-full shadow-md border-4 border-white dark:border-[#0d1117] transition-transform group-hover:scale-105">
                                @else
                                    <div id="avatar-initials" class="w-full h-full bg-[#F5333F] text-white flex items-center justify-center rounded-full text-4xl font-black shadow-md border-4 border-white dark:border-[#0d1117] transition-transform group-hover:scale-105">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                                
                                <!-- Overlay Hover Kamera -->
                                <div class="absolute inset-0 bg-black/50 rounded-full opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity backdrop-blur-sm">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                
                                <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*" 
                                    onchange="
                                        const file = this.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                // Sembunyikan inisial huruf N
                                                const initialDiv = document.getElementById('avatar-initials');
                                                if (initialDiv) initialDiv.style.display = 'none';
                                                
                                                // Buat atau ubah tag image
                                                let img = document.getElementById('avatar-preview');
                                                if (!img) {
                                                    img = document.createElement('img');
                                                    img.id = 'avatar-preview';
                                                    img.className = 'w-full h-full object-cover rounded-full shadow-md border-4 border-white dark:border-[#0d1117] transition-transform group-hover:scale-105';
                                                    document.getElementById('avatar-container').prepend(img);
                                                }
                                                img.src = e.target.result;
                                                
                                                // Munculkan tombol simpan
                                                document.getElementById('save-btn').classList.remove('hidden');
                                            }
                                            reader.readAsDataURL(file);
                                        }
                                    ">
                            </div>

                            <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $user->name }}</h3>
                            <p class="text-sm font-semibold text-[#F5333F] mt-1 mb-6">{{ $user->roles->first()->name ?? 'Karyawan' }}</p>

                            <!-- Input Nama & Email -->
                            <div class="space-y-4 text-left">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 text-sm font-semibold bg-gray-50 dark:bg-[#0d1117] border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-[#F5333F] focus:border-[#F5333F] text-gray-900 dark:text-white transition-colors" required>
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Alamat Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 text-sm font-semibold bg-gray-50 dark:bg-[#0d1117] border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-[#F5333F] focus:border-[#F5333F] text-gray-900 dark:text-white transition-colors" required>
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Tombol Simpan Profil Muncul Dinamis -->
                                <button id="save-btn" type="submit" class="w-full py-3 mt-4 text-sm font-black text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-xl hover:bg-[#F5333F] dark:hover:bg-[#F5333F] dark:hover:text-white transition-all shadow-md">
                                    Simpan Perubahan Profil
                                </button>
                                
                                @if (session('status') === 'profile-updated')
                                    <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-emerald-500 text-center mt-3">✓ Profil Berhasil Diperbarui</p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- KOLOM KANAN: Keamanan & Zona Bahaya -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Form Ganti Password -->
                    <div class="bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800 rounded-3xl shadow-sm p-5 sm:p-8">
                        <div class="mb-6 flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-50 text-amber-500 dark:bg-amber-500/10 dark:text-amber-400 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white">Keamanan Akun</h2>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1">Pastikan Anda menggunakan kata sandi panjang yang sulit ditebak.</p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                            @csrf
                            @method('put')

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kata Sandi Saat Ini</label>
                                <input type="password" name="current_password" class="w-full px-4 py-3 text-sm bg-gray-50 dark:bg-[#0d1117] border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-[#F5333F] focus:border-[#F5333F] text-gray-900 dark:text-white transition-colors" required>
                                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
                                    <input type="password" name="password" class="w-full px-4 py-3 text-sm bg-gray-50 dark:bg-[#0d1117] border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-[#F5333F] focus:border-[#F5333F] text-gray-900 dark:text-white transition-colors" required>
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Konfirmasi Sandi Baru</label>
                                    <input type="password" name="password_confirmation" class="w-full px-4 py-3 text-sm bg-gray-50 dark:bg-[#0d1117] border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-[#F5333F] focus:border-[#F5333F] text-gray-900 dark:text-white transition-colors" required>
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-4">
                                <button type="submit" class="px-6 py-2.5 text-sm font-black text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-xl hover:bg-amber-500 dark:hover:bg-amber-500 dark:hover:text-white transition-all shadow-md">
                                    Perbarui Sandi
                                </button>
                                @if (session('status') === 'password-updated')
                                    <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-emerald-500">✓ Sandi Aman</p>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Zona Bahaya (Hapus Akun) -->
                    <div class="bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/20 rounded-3xl p-8">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-black text-red-600 dark:text-red-400">Zona Bahaya</h3>
                                <p class="text-xs font-medium text-red-500/80 dark:text-red-400/80 mt-1 max-w-md">Menghapus akun akan menghilangkan seluruh data dan akses Anda ke dalam aplikasi secara permanen.</p>
                            </div>
                            
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="px-6 py-2.5 text-sm font-black text-red-600 bg-white dark:bg-[#1A1A1A] border border-red-200 dark:border-red-500/30 rounded-xl hover:bg-red-600 hover:text-white hover:border-red-600 transition-all shadow-sm whitespace-nowrap">
                                Hapus Akun Saya
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            
        </div>
    </div>

    <!-- Modal Hapus Akun Tetap Dipertahankan -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white dark:bg-[#1A1A1A]">
            @csrf
            @method('delete')
            <h2 class="text-lg font-black text-gray-900 dark:text-white mb-2">Apakah Anda yakin ingin menghapus akun?</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi Anda untuk mengonfirmasi.</p>
            <input type="password" name="password" class="w-full px-4 py-3 text-sm bg-gray-50 dark:bg-[#0d1117] border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white mb-2" placeholder="Kata Sandi Anda">
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mb-4" />
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm">Hapus Permanen</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>