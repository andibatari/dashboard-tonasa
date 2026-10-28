{{-- 
  x-data="{ activeTab: 'laporan' }" adalah fitur dari Alpine.js.
  Fungsinya untuk menyimpan "state" (status) tab mana yang sedang dibuka.
  Kita pakai ini agar perpindahan tab terjadi sangat cepat di browser pengguna (client-side) tanpa perlu loading/refresh server.
--}}
<div class="px-3 md:px-8 py-6 md:py-8 min-h-screen" x-data="{ activeTab: 'laporan' }">
    
    {{-- Header & Tombol Simpan (Responsive: di HP atas-bawah, di Laptop sejajar) --}}
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">Pengaturan Sistem STMC</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Konfigurasi laporan PDF, antrean pendaftaran, dan keamanan sistem.</p>
        </div>
        
        {{-- Tombol dibuat w-full (lebar penuh) di HP agar mudah dipencet jari --}}
        <button wire:click="simpanPengaturan" class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-3 md:py-2.5 px-6 rounded-xl md:rounded-2xl shadow-lg shadow-red-500/30 transition-all transform active:scale-95 text-sm md:text-base whitespace-nowrap">
            <i class="fas fa-save mr-2"></i> Simpan Pengaturan
        </button>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-2xl shadow-sm text-emerald-800 font-bold text-sm animate-fade-in flex items-center">
            <i class="fas fa-check-circle mr-3 text-emerald-500 text-lg"></i> {{ session('success') }}
        </div>
    @endif

    {{-- 
      overflow-x-auto & hide-scrollbar: Digunakan agar jika layar HP terlalu kecil, 
      tombol tab ini tidak hancur bertumpuk, melainkan bisa di-geser (scroll) ke samping.
    --}}
    <div class="flex p-1.5 mb-6 md:mb-8 bg-slate-100 rounded-xl md:rounded-2xl w-full overflow-x-auto shadow-inner hide-scrollbar">
        <button @click="activeTab = 'laporan'" :class="activeTab === 'laporan' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-none px-4 md:px-6 py-2.5 text-xs md:text-sm font-bold rounded-lg md:rounded-xl transition-all duration-200 whitespace-nowrap flex items-center">
            <i class="fas fa-file-pdf mr-2" :class="activeTab === 'laporan' ? 'text-red-500' : ''"></i> Kop & Laporan
        </button>
        <button @click="activeTab = 'pendaftaran'" :class="activeTab === 'pendaftaran' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-none px-4 md:px-6 py-2.5 text-xs md:text-sm font-bold rounded-lg md:rounded-xl transition-all duration-200 whitespace-nowrap flex items-center">
            <i class="fas fa-qrcode mr-2" :class="activeTab === 'pendaftaran' ? 'text-red-500' : ''"></i> Pendaftaran QR
        </button>
        <button @click="activeTab = 'sistem'" :class="activeTab === 'sistem' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-none px-4 md:px-6 py-2.5 text-xs md:text-sm font-bold rounded-lg md:rounded-xl transition-all duration-200 whitespace-nowrap flex items-center">
            <i class="fas fa-shield-alt mr-2" :class="activeTab === 'sistem' ? 'text-red-500' : ''"></i> Keamanan & Backup
        </button>
    </div>

    {{-- KONTEN UTAMA DIBUNGKUS KOTAK PREMIUM --}}
    <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 min-h-[400px]">

        {{-- ===================================
             TAB 1: LAPORAN
             =================================== --}}
        <div x-show="activeTab === 'laporan'" class="p-5 md:p-8" x-transition.opacity.duration.300ms>
            <h3 class="text-base md:text-lg font-black text-slate-800 mb-5 md:mb-6 border-b border-slate-100 pb-3 flex items-center">
                <i class="fas fa-paint-roller text-slate-400 mr-2"></i> Kustomisasi Template PDF
            </h3>
            
            {{-- 
              [PENJELASAN UNTUK SEMHAS]
              grid-cols-1 md:grid-cols-2: Di layar HP (kecil) kolomnya 1 (atas-bawah). 
              Jika layarnya medium/laptop (md), otomatis berubah jadi 2 kolom sejajar.
            --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-8 mb-6 md:mb-8">
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Kepala Klinik</label>
                    {{-- 
                      wire:model.defer: Menyimpan ketikan user sementara di browser. 
                      Data baru dikirim ke server saat tombol 'Simpan' dipencet. Ini menghemat traffic internet/kinerja server.
                    --}}
                    <input type="text" wire:model.defer="nama_kepala_klinik" class="w-full border-slate-200 bg-slate-50 focus:bg-white rounded-xl focus:border-red-500 focus:ring-red-500 text-sm py-3 px-4 shadow-sm transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Teks Pengantar PDF</label>
                    <textarea wire:model.defer="teks_disclaimer" rows="3" class="w-full border-slate-200 bg-slate-50 focus:bg-white rounded-xl focus:border-red-500 focus:ring-red-500 text-sm p-4 shadow-sm transition-colors" placeholder="Contoh: Pada tanggal [TANGGAL], kami melakukan..."></textarea>
                    <p class="text-[10px] md:text-[11px] text-slate-500 font-medium mt-1.5 bg-slate-50 p-2 rounded-lg border border-slate-100">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i> Gunakan kata kunci <strong class="text-red-500 bg-red-50 px-1 rounded">[TANGGAL]</strong> agar sistem otomatis memasukkan tanggal pemeriksaan pasien.
                    </p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-8">
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 hover:shadow-md transition-shadow relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-white px-3 py-1 text-[10px] font-bold text-slate-400 rounded-bl-xl border-b border-l border-slate-200">KIRI PDF</div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-widest mb-3">Logo STMC</label>
                    <div class="bg-white p-4 rounded-xl border border-slate-200 mb-4 flex justify-center shadow-sm">
                        <img src="{{ asset($current_logo_stmc) }}" class="h-16 md:h-20 object-contain">
                    </div>
                    <input type="file" wire:model="logo_stmc" class="text-sm w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                </div>
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 hover:shadow-md transition-shadow relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-white px-3 py-1 text-[10px] font-bold text-slate-400 rounded-bl-xl border-b border-l border-slate-200">KANAN PDF</div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-widest mb-3">Logo Induk (Tonasa)</label>
                    <div class="bg-white p-4 rounded-xl border border-slate-200 mb-4 flex justify-center shadow-sm">
                        <img src="{{ asset($current_logo_tonasa) }}" class="h-16 md:h-20 object-contain">
                    </div>
                    <input type="file" wire:model="logo_tonasa" class="text-sm w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                </div>
            </div>
        </div>

        {{-- ===================================
             TAB 2: PENDAFTARAN
             =================================== --}}
        <div x-show="activeTab === 'pendaftaran'" style="display: none;" class="p-5 md:p-8" x-transition.opacity.duration.300ms>
            <h3 class="text-base md:text-lg font-black text-slate-800 mb-5 md:mb-6 border-b border-slate-100 pb-3 flex items-center">
                <i class="fas fa-cogs text-slate-400 mr-2"></i> Aturan Pendaftaran via QR Code
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 md:gap-6">
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Batas Kuota Harian</label>
                    <input type="number" wire:model.defer="kuota_harian" class="w-full border-slate-200 bg-slate-50 focus:bg-white rounded-xl focus:border-red-500 focus:ring-red-500 text-sm py-3 px-4 shadow-sm">
                </div>
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Jam Buka Registrasi</label>
                    <input type="time" wire:model.defer="jam_buka" class="w-full border-slate-200 bg-slate-50 focus:bg-white rounded-xl focus:border-red-500 focus:ring-red-500 text-sm py-3 px-4 shadow-sm">
                </div>
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Jam Tutup Registrasi</label>
                    <input type="time" wire:model.defer="jam_tutup" class="w-full border-slate-200 bg-slate-50 focus:bg-white rounded-xl focus:border-red-500 focus:ring-red-500 text-sm py-3 px-4 shadow-sm">
                </div>
            </div>
        </div>

        {{-- ===================================
             TAB 3: SISTEM
             =================================== --}}
        <div x-show="activeTab === 'sistem'" style="display: none;" class="p-5 md:p-8" x-transition.opacity.duration.300ms>
            <h3 class="text-base md:text-lg font-black text-slate-800 mb-5 md:mb-6 border-b border-slate-100 pb-3 flex items-center">
                <i class="fas fa-lock text-slate-400 mr-2"></i> Kontrol Keamanan Database
            </h3>
            <div class="flex flex-col md:flex-row gap-5 md:gap-8">
                
                {{-- Mode Pemeliharaan --}}
                <div class="bg-rose-50 border border-rose-200 p-5 md:p-6 rounded-2xl flex-1 relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 text-rose-100 opacity-50 transform group-hover:scale-110 transition-transform">
                        <i class="fas fa-power-off" style="font-size: 8rem;"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-black text-rose-800 text-sm md:text-base"><i class="fas fa-tools mr-2"></i> Mode Pemeliharaan</h4>
                            {{-- Toggle Switch (UI Checkbox yang dipercantik) --}}
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.defer="maintenance_mode" class="sr-only peer">
                                <div class="w-11 h-6 bg-rose-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                            </label>
                        </div>
                        <p class="text-xs text-rose-700 font-medium leading-relaxed pr-8">Jika aktif, pendaftaran QR pasien akan diblokir sementara. Gunakan saat mengupdate sistem.</p>
                    </div>
                </div>

                {{-- Backup Database --}}
                <div class="bg-blue-50 border border-blue-200 p-5 md:p-6 rounded-2xl flex-1 text-center md:text-left flex flex-col justify-between relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-blue-100 opacity-50 transform group-hover:scale-110 transition-transform">
                        <i class="fas fa-database" style="font-size: 8rem;"></i>
                    </div>
                    <div class="relative z-10">
                        <h4 class="font-black text-blue-800 text-sm md:text-base mb-2"><i class="fas fa-cloud-download-alt mr-2"></i> Backup Data (.sql)</h4>
                        <p class="text-xs text-blue-700 font-medium mb-5 md:pr-10 leading-relaxed">Unduh seluruh rekaman data rekam medis dan pengaturan ke komputer lokal Anda untuk pencegahan kehilangan data.</p>
                        <button wire:click="backupDatabase" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 md:py-2.5 px-6 rounded-xl text-sm transition-all shadow-lg shadow-blue-500/30">
                            Mulai Backup
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Kumpulan Style Khusus Halaman Ini --}}
    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        /* Menyembunyikan scrollbar di tab HP tapi tetap bisa digeser */
        .hide-scrollbar::-webkit-scrollbar { display: none; } 
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>