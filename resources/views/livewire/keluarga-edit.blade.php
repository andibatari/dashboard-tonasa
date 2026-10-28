{{-- Container pembungkus luar dengan padding minimal (px-3 py-4) khusus layar ponsel untuk menghemat ruang --}}
<div class="px-3 md:px-6 py-4 min-h-screen">
    
    {{-- Tombol Navigasi Kembali menggunakan history browser --}}
    <div class="mb-4">
        <a href="javascript:history.back()" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-800 hover:-translate-x-1 transition-all duration-200">
            <i class="fas fa-arrow-left mr-1.5"></i> Kembali
        </a>
    </div>

    {{-- KARTU UTAMA: Jarak padding dikurangi menjadi p-4 pada mobile dan p-8 pada desktop agar komponen form lebih rapat --}}
    <div class="bg-white rounded-xl md:rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 p-4 md:p-8 max-w-7xl mx-auto">
        
        {{-- Bagian Judul Modul / Header Form --}}
        <div class="mb-5 border-b border-slate-100 pb-4">
            <h1 class="text-lg md:text-2xl font-black text-slate-800 flex items-center leading-tight">
                <div class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center mr-2.5 shrink-0">
                    <i class="fas fa-user-edit text-sm"></i>
                </div>
                Edit Data Pasien
            </h1>
            <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-1 ml-11">Perbarui informasi identitas, demografi, dan kontak pasien secara berkala.</p>
        </div>

        {{-- Form Utama: wire:submit.prevent mematikan fungsi reload halaman bawaan browser dan mengalihkannya ke method updateKeluarga di Livewire --}}
        {{-- Spasi vertikal antar grup form dirapatkan menggunakan space-y-4 --}}
        <form wire:submit.prevent="updateKeluarga" class="space-y-4">
            
            {{-- ==========================================
                 SECTION 1: IDENTITAS PASIEN
                 ========================================== --}}
            <div class="bg-slate-50 p-3 md:p-5 rounded-xl border border-slate-100 shadow-inner">
                <h3 class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-id-card mr-1.5 text-slate-400"></i> 1. Identitas Pasien</h3>
                
                {{-- Grid responsif: 1 kolom di HP, 2 kolom di tablet (md), 3 kolom di desktop (lg) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div ">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Nama Lengkap</label>
                        <input type="text" wire:model.live="nama_lengkap" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-0 shadow-sm transition-colors" required>
                        @error('nama_lengkap') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div >
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">NIK (KTP)</label>
                        <input type="text" wire:model.live="nik_pasien" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-0 shadow-sm transition-colors" required>
                        @error('nik_pasien') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Perusahaan</label>
                        <input type="text" wire:model.live="perusahaan_asal" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-0 font-mono shadow-sm transition-colors">
                        @error('perusahaan') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Pekerjaan</label>
                        <input type="text" wire:model.live="pekerjaan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-0 shadow-sm transition-colors">
                        @error('pekerjaan') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Agama</label>
                        <select wire:model.live="agama" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm cursor-pointer">
                            <option value="">Pilih</option><option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Buddha">Buddha</option><option value="Konghucu">Konghucu</option>
                        </select>
                        @error('agama') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Pendidikan</label>
                        <select wire:model.live="pendidikan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm cursor-pointer">
                            <option value="">Pilih</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA/SMK">SMA/SMK</option><option value="D3">D3</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option>
                        </select>
                        @error('pendidikan') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 SECTION 2: BIOMETRIK & KELAHIRAN
                 ========================================== --}}
            <div class="bg-slate-50 p-3 md:p-5 rounded-xl border border-slate-100 shadow-inner">
                <h3 class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-heartbeat mr-1.5 text-slate-400"></i> 2. Biometrik & Kelahiran</h3>
                
                {{-- Memaksa grid-cols-2 sejak di HP agar parameter biometrik berjejer hemat tempat vertikal --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Tanggal Lahir</label>
                        <input type="date" wire:model.live="tanggal_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Tempat Lahir</label>
                        <input type="text" wire:model.live="tempat_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                    </div>
                    <div>
                        {{-- Input Umur ditandai dengan properti readonly karena nilainya dikalkulasi secara otomatis oleh sistem backend --}}
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Umur</label>
                        <input type="text" wire:model="umur" class="block w-full px-3 py-2 text-xs font-black rounded-lg border border-slate-200 bg-slate-200 text-slate-600 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Jenis Kelamin</label>
                        <select wire:model.live="jenis_kelamin" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm cursor-pointer">
                            <option value="">Pilih</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Tinggi Badan (cm)</label>
                        <input type="number" step="any" wire:model.live="tinggi_badan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Berat Badan (kg)</label>
                        <input type="number" step="any" wire:model.live="berat_badan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Gol. Darah</label>
                        <select wire:model.live="golongan_darah" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm cursor-pointer">
                            <option value="">Pilih</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 SECTION 3: DOMISILI & KONTAK
                 ========================================== --}}
            <div class="bg-slate-50 p-3 md:p-5 rounded-xl border border-slate-100 shadow-inner">
                <h3 class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-map-marker-alt mr-1.5 text-slate-400"></i> 3. Domisili & Kontak</h3>
                
                {{-- Kombinasi grid bertahap: split 2 kolom di mobile, 3 kolom di desktop --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Provinsi</label>
                        <select wire:model.live="provinsi_id" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm cursor-pointer">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsis as $provinsi)
                                <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Kabupaten/Kota</label>
                        <input type="text" wire:model.live="nama_kabupaten" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Kecamatan</label>
                        <input type="text" wire:model.live="nama_kecamatan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Nomor Handphone</label>
                        <input type="text" wire:model.live="no_hp" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 font-mono shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Email Akses</label>
                        <input type="email" wire:model.live="email" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Alamat Lengkap Domisili</label>
                        {{-- Mengurangi ukuran tinggi area text (rows="2") agar form lebih padat --}}
                        <textarea wire:model.live="alamat" rows="2" class="block w-full rounded-lg border border-slate-200 bg-white text-xs focus:border-red-500 p-3 shadow-sm resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 SECTION 4: KEAMANAN AKUN LOGIN
                 ========================================== --}}
            <div class="bg-slate-50 p-3 md:p-5 rounded-xl border border-slate-100 shadow-inner">
                <h3 class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-widest mb-1 flex items-center"><i class="fas fa-lock mr-1.5 text-slate-400"></i> 4. Keamanan Akun Login</h3>
                <p class="text-[10px] text-slate-400 font-medium mb-3">Biarkan kolom kosong jika Anda tidak berencana mengubah kata sandi.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="pass_edit_kel" wire:model.live="password" placeholder="••••••••" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm transition-all">
                            <button type="button" onclick="togglePasswordVisibility('pass_edit_kel', 'eyeOpen_ekel1', 'eyeClosed_ekel1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-600 focus:outline-none">
                                <img id="eyeOpen_ekel1" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                                <img id="eyeClosed_ekel1" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                            </button>
                        </div>
                        @error('password') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" id="pass_edit_kel_conf" wire:model.live="password_confirmation" placeholder="••••••••" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm transition-all">
                            <button type="button" onclick="togglePasswordVisibility('pass_edit_kel_conf', 'eyeOpen_ekel2', 'eyeClosed_ekel2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-600 focus:outline-none">
                                <img id="eyeOpen_ekel2" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                                <img id="eyeClosed_ekel2" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                            </button>
                        </div>
                        @error('password_confirmation') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- TOMBOL AKSI SUBMIT --}}
            {{-- Menggunakan w-full pada mobile agar memudahkan interaksi ketuk jari --}}
            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow hover:shadow-lg hover:-translate-y-0.5 transition-all text-xs md:text-sm active:scale-95">
                    {{-- wire:loading men-toggle teks tombol secara otomatis saat proses sinkronisasi database sedang berlangsung --}}
                    <span wire:loading.remove><i class="fas fa-save mr-1.5"></i> Simpan Perubahan</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-1.5"></i> Memperbarui...</span>
                </button>
            </div>
        </form>
    </div>
</div>