@extends('layouts.app')

@section('title', 'Kelola Profil Admin')

@section('content')
{{-- 
  [TIPS SEMHAS]
  Class px-4 md:px-6 lg:px-8 adalah teknik "Mobile-First Design" dari Tailwind.
  Artinya: Di layar HP (default) padding kiri-kanan hanya 4. 
  Saat layar agak besar (md/tablet), padding naik jadi 6.
  Saat layar besar (lg/laptop), padding naik jadi 8. Ini membuat layout sangat adaptif.
--}}
<div class="max-w-5xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-8">
    
    {{-- Header Halaman --}}
    <div class="mb-6 md:mb-8">
        <h1 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">Pengaturan Profil</h1>
        <p class="text-xs md:text-sm text-slate-500 mt-1">Kelola informasi pribadi, email, dan keamanan kata sandi akun Anda.</p>
    </div>

    {{-- Pesan Sukses --}}
    @if (session('success'))
        <div class="mb-6 flex items-center bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-2xl shadow-sm animate-fade-in" role="alert">
            <i class="fas fa-check-circle text-emerald-500 text-xl mr-3"></i>
            <div>
                <p class="text-sm font-bold text-emerald-800">Berhasil!</p>
                <p class="text-xs md:text-sm text-emerald-600">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    
    {{-- Pesan Error --}}
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-2xl shadow-sm animate-fade-in" role="alert">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle text-rose-500 text-lg mr-2"></i>
                <p class="text-sm font-bold text-rose-800">Gagal menyimpan perubahan!</p>
            </div>
            <ul class="text-xs md:text-sm text-rose-600 list-disc list-inside ml-6 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card Profil Utama --}}
    <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
        
        {{-- 
          [TIPS SEMHAS - PERTANYAAN DOSEN]
          Dosen: "Kenapa form ini bisa upload gambar sedangkan form lain tidak?"
          Jawaban: "Karena pada tag form ini saya menambahkan atribut 'enctype=multipart/form-data'. 
          Tanpa atribut ini, HTML hanya akan mengirimkan nama filenya saja (teks), bukan file fisik gambarnya ke server Laravel."
        --}}
        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            
            {{-- 
              [TIPS SEMHAS]
              Dosen: "Apa itu @csrf dan @method('PUT')?"
              Jawaban: "@csrf adalah token keamanan wajib dari Laravel untuk mencegah serangan Cross-Site Request Forgery (Hacker memalsukan request). 
              Lalu @method('PUT') digunakan karena form HTML murni hanya mendukung GET dan POST. 
              Sesuai standar RESTful API, untuk meng-update data kita menggunakan metode PUT/PATCH, sehingga Laravel me-routing POST menjadi PUT."
            --}}
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-12">
                
                {{-- =====================================
                     KIRI: Bagian Foto Profil (Col-Span 4)
                     ===================================== --}}
                {{-- Di HP, border bawah (border-b). Di Laptop, border kanan (lg:border-r) --}}
                <div class="lg:col-span-4 bg-slate-50/50 border-b lg:border-b-0 lg:border-r border-slate-100 p-6 md:p-8 flex flex-col items-center justify-center text-center">
                    <h2 class="text-[11px] md:text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 md:mb-6">Foto Profil</h2>
                    
                    <div class="relative inline-block mb-4 group">
                        {{-- Logic Preview Foto --}}
                        @php
                            $imageUrl = null;
                            if (isset($admin) && $admin->foto_profil) {
                                if (str_starts_with($admin->foto_profil, 'admin_photos/')) {
                                    $imageUrl = asset($admin->foto_profil) . '?t=' . now()->timestamp;
                                } else {
                                    $imageUrl = asset('storage/' . $admin->foto_profil) . '?t=' . now()->timestamp;
                                }
                            } else {
                                $imageUrl = 'https://ui-avatars.com/api/?name=' . urlencode($admin->nama_lengkap ?? 'Admin') . '&background=fecaca&color=b91c1c&size=200';
                            }
                        @endphp
                        
                        <div class="w-28 h-28 md:w-36 md:h-36 rounded-full p-1 bg-white border-2 border-slate-200 shadow-md relative overflow-hidden">
                            <img src="{{ $imageUrl }}" 
                                alt="Foto Profil" 
                                id="profileImagePreview"
                                class="w-full h-full object-cover rounded-full transition-opacity duration-300">
                        </div>

                        {{-- Tombol Edit Mengambang (Hidden File Input) --}}
                        <label for="foto_profil" class="absolute bottom-1 right-1 md:bottom-2 md:right-2 bg-red-600 hover:bg-red-700 text-white p-2 md:p-2.5 rounded-full shadow-lg cursor-pointer transition-all duration-200 hover:scale-110 border-2 border-white ring-2 ring-transparent focus-within:ring-red-500">
                            <i class="fas fa-camera text-xs md:text-sm"></i>
                        </label>
                        <input type="file" name="foto_profil" id="foto_profil" class="hidden" accept="image/jpeg,image/png,image/jpg">
                    </div>
                    
                    <h3 class="text-base md:text-lg font-black text-slate-800 leading-tight px-4">{{ $admin->nama_lengkap ?? 'Administrator' }}</h3>
                    <p class="text-[11px] md:text-xs text-slate-500 mt-1 font-medium">Admin STMC</p>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 mt-4 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                        JPG, JPEG, PNG (Maks 2MB)
                    </p>
                    
                    @error('foto_profil')
                        <p class="text-rose-500 text-[10px] md:text-xs font-bold mt-3 bg-rose-50 px-3 py-1 rounded border border-rose-100">{{ $message }}</p>
                    @enderror
                </div>

                {{-- =====================================
                     KANAN: Form Detail Akun (Col-Span 8)
                     ===================================== --}}
                {{-- Padding dikecilkan di HP (p-5), dilonggarkan di laptop (lg:p-10) --}}
                <div class="lg:col-span-8 p-5 md:p-8 lg:p-10">
                    
                    {{-- Section 1: Informasi Dasar --}}
                    <div class="mb-8 md:mb-10">
                        <div class="flex items-center mb-5 md:mb-6">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center mr-3 border border-red-100">
                                <i class="fas fa-id-badge text-red-600 text-sm"></i>
                            </div>
                            <h2 class="text-base md:text-lg font-black text-slate-800">Informasi Dasar</h2>
                        </div>

                        <div class="space-y-4 md:space-y-5">
                            <div>
                                <label for="nama_lengkap" class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 md:mb-2">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-slate-400"></i>
                                    </div>
                                    {{-- 
                                      [TIPS SEMHAS]
                                      Fungsi old('nama_lengkap', $admin->nama_lengkap) artinya:
                                      Sistem akan mencoba mengambil data yang baru diketik (old) jika terjadi error validasi.
                                      Jika tidak ada error, maka ia akan menampilkan data asli dari database ($admin).
                                    --}}
                                    <input type="text" name="nama_lengkap" id="nama_lengkap" 
                                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all shadow-sm"
                                        value="{{ old('nama_lengkap', $admin->nama_lengkap) }}" required>
                                </div>
                                @error('nama_lengkap') <p class="text-rose-500 text-[10px] md:text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 md:mb-2">Alamat Email (Username)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-slate-400"></i>
                                    </div>
                                    <input type="email" name="email" id="email" 
                                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all shadow-sm"
                                        value="{{ old('email', $admin->email) }}" required>
                                </div>
                                @error('email') <p class="text-rose-500 text-[10px] md:text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-100 my-6 md:my-8">

                    {{-- Section 2: Keamanan Akun --}}
                    <div>
                        <div class="flex items-center mb-4 md:mb-6">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center mr-3 border border-slate-200">
                                <i class="fas fa-lock text-slate-600 text-sm"></i>
                            </div>
                            <h2 class="text-base md:text-lg font-black text-slate-800">Keamanan Akun</h2>
                        </div>
                        
                        <p class="text-[11px] md:text-xs text-slate-500 mb-5 font-medium leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <i class="fas fa-info-circle text-blue-400 mr-1"></i> Biarkan kosong jika Anda tidak ingin mengubah kata sandi saat ini.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5">
                            {{-- Input Kata Sandi Baru --}}
                            <div>
                                <label for="password" class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 md:mb-2">Kata Sandi Baru</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-key text-slate-400"></i>
                                    </div>
                                    {{-- Ditambahkan pr-12 agar teks tidak tertimpa icon mata --}}
                                    <input type="password" name="password" id="password" placeholder="••••••••"
                                        class="block w-full pl-11 pr-12 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm font-bold focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all shadow-sm">
                                    
                                    {{-- Tombol Mata (Buka/Tutup) --}}
                                    <button type="button" onclick="togglePasswordVisibility('password', 'eyeOpenPass1', 'eyeClosedPass1')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-red-600 focus:outline-none transition-colors">
                                        <img id="eyeOpenPass1" src="{{ asset('images/eye-open.png') }}" alt="Show" class="h-5 w-5 opacity-70">
                                        <img id="eyeClosedPass1" src="{{ asset('images/eye-closed.png') }}" alt="Hide" class="h-5 w-5 hidden opacity-70">
                                    </button>
                                </div>
                                @error('password') <p class="text-rose-500 text-[10px] md:text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>

                            {{-- Input Konfirmasi Kata Sandi --}}
                            <div>
                                <label for="password_confirmation" class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 md:mb-2">Konfirmasi Sandi Baru</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-check-double text-slate-400"></i>
                                    </div>
                                    {{-- Ditambahkan pr-12 agar teks tidak tertimpa icon mata --}}
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••"
                                        class="block w-full pl-11 pr-12 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm font-bold focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all shadow-sm">
                                    
                                    {{-- Tombol Mata (Buka/Tutup) --}}
                                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eyeOpenPass2', 'eyeClosedPass2')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-red-600 focus:outline-none transition-colors">
                                        <img id="eyeOpenPass2" src="{{ asset('images/eye-open.png') }}" alt="Show" class="h-5 w-5 opacity-70">
                                        <img id="eyeClosedPass2" src="{{ asset('images/eye-closed.png') }}" alt="Hide" class="h-5 w-5 hidden opacity-70">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi (W-full di HP) --}}
                    <div class="mt-8 md:mt-10 flex justify-end">
                        <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 md:px-8 py-3.5 md:py-3.5 bg-red-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-red-500/30 hover:bg-red-700 transition-all duration-200 transform active:scale-95">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan Profil
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
    /* [TIPS SEMHAS - JAVASCRIPT]
       Dosen: "Bagaimana cara kerja fitur preview gambar ini sebelum datanya dikirim ke server?"
       Jawaban: "Saya menggunakan fungsi native JS bernama 'FileReader()'. 
       Saat admin memilih file di komputer/HP, FileReader akan membaca data file tersebut dan mengubahnya menjadi format 'Data URL' (Base64 string). 
       String Base64 ini kemudian saya pasang secara dinamis ke dalam atribut 'src' pada tag <img> menggunakan javascript, sehingga gambarnya langsung muncul di layar tanpa membebani server."
    */
    document.getElementById('foto_profil').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            // Validasi ukuran (2MB = 2097152 bytes)
            if(file.size > 2097152){
                alert('Ukuran foto terlalu besar. Maksimal 2MB.');
                this.value = ""; 
                return;
            }

            const reader = new FileReader();
            const preview = document.getElementById('profileImagePreview');
            
            // Animasi halus saat gambar sedang diproses
            preview.style.opacity = '0.3';
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                setTimeout(() => {
                    preview.style.opacity = '1';
                }, 150); // Delay 150ms agar animasi transisinya terlihat premium
            }
            reader.readAsDataURL(file);
        }
    });

    // Fungsi Dinamis Buka/Tutup Password
    function togglePasswordVisibility(inputId, eyeOpenId, eyeClosedId) {
        const passwordInput = document.getElementById(inputId);
        const eyeOpen = document.getElementById(eyeOpenId);
        const eyeClosed = document.getElementById(eyeClosedId);

        if (passwordInput.type === 'password') {
            // Ubah ke Text (Terlihat)
            passwordInput.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            // Ubah ke Password (Tersembunyi)
            passwordInput.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
</script>
@endsection