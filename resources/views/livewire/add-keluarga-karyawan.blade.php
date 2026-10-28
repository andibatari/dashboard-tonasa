<form wire:submit.prevent="save" class="space-y-4 md:space-y-6">
    
    {{-- SECTION 1: Identitas & Pekerjaan --}}
    <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-id-card mr-1.5 text-slate-400"></i> Identitas</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3">
            <div class="col-span-2 md:col-span-1">
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" wire:model="nama_lengkap" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500" required>
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Tipe Pasien <span class="text-red-500">*</span></label>
                <select wire:model.live="tipe_anggota" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 cursor-pointer" required>
                    <option value="">Pilih</option>
                    @if ($karyawan) <option value="Istri">Istri</option><option value="Suami">Suami</option><option value="Anak">Anak</option> 
                    @else <option value="Non-Karyawan">Umum</option> @endif
                </select>
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">NIK (KTP) <span class="text-red-500">*</span></label>
                <input type="text" wire:model="nik_pasien" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500" required>
                @error('nik_pasien') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Agama</label>
                <select wire:model="agama" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white cursor-pointer">
                    <option value="">Pilih...</option><option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Buddha">Buddha</option><option value="Konghucu">Konghucu</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 mt-4 pt-3 border-t border-slate-200">
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Pendidikan Terakhir</label>
                <select wire:model="pendidikan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white cursor-pointer">
                    <option value="">Pilih Pendidikan</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA/SMK">SMA/SMK</option><option value="D1">D1</option><option value="D2">D2</option><option value="D3">D3</option><option value="D4">D4</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option>
                </select>
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Pekerjaan</label>
                <input type="text" wire:model="pekerjaan" placeholder="Contoh: Wiraswasta" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500">
            </div>
            <div class="col-span-2 md:col-span-1">
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Perusahaan / Instansi <span class="text-red-500">*</span></label>
                <input type="text" wire:model="perusahaan_asal" placeholder="Ketik Umum jika tidak ada" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500" required>
                @error('perusahaan_asal') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    
    {{-- SECTION 2: Kelahiran & Fisik --}}
    <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-heartbeat mr-1.5 text-slate-400"></i> Biometrik & Fisik</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3">
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Jenis Kelamin</label>
                <select wire:model="jenis_kelamin" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white cursor-pointer"><option value="">Pilih...</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select>
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Tempat Lahir</label>
                <input type="text" wire:model="tempat_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Tanggal Lahir</label>
                <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Umur</label>
                <input type="number" wire:model="umur" id="umur" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-slate-100 text-slate-500" readonly>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3 md:gap-4 mt-4 pt-3 border-t border-slate-200">
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Gol. Darah</label>
                <select wire:model="golongan_darah" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white cursor-pointer"><option value="">Pilih...</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option></select>
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Tinggi Badan (cm)</label>
                <input type="number" step="any" wire:model="tinggi_badan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Berat Badan (kg)</label>
                <input type="number" step="any" wire:model="berat_badan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
        </div>
    </div>

    {{-- SECTION 3: Kontak & Domisili --}}
    <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-map-marker-alt mr-1.5 text-slate-400"></i> Kontak & Domisili</h3>
        
        {{-- UBAH DISINI: grid-cols-1 menjadi grid-cols-2 untuk mobile --}}
        <div class="grid grid-cols-2 gap-3 md:gap-4 mb-3">
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">No Handphone</label>
                <input type="text" wire:model="no_hp" class="block w-full px-3 py-2 text-[10px] md:text-xs font-bold rounded-lg border border-slate-200 bg-white font-mono">
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Email</label>
                <input type="email" wire:model="email" class="block w-full px-3 py-2 text-[10px] md:text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
        </div>

        {{-- UBAH DISINI: Pengaturan col-span agar pas di layar HP --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 mt-4 pt-3 border-t border-slate-200">
            <div class="col-span-2 md:col-span-1">
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Provinsi</label>
                <select wire:model.live="provinsi_id" class="block w-full px-3 py-2 text-[10px] md:text-xs font-bold rounded-lg border border-slate-200 bg-white cursor-pointer">
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinsis as $provinsi)
                        <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1">
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Kabupaten/Kota</label>
                <input type="text" wire:model="nama_kabupaten" class="block w-full px-3 py-2 text-[10px] md:text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
            <div class="col-span-1">
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Kecamatan</label>
                <input type="text" wire:model="nama_kecamatan" class="block w-full px-3 py-2 text-[10px] md:text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
            <div class="col-span-2 md:col-span-3">
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Alamat Lengkap</label>
                <input type="text" wire:model="alamat" placeholder="Jalan, RT/RW, Perumahan" class="block w-full px-3 py-2 text-[10px] md:text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
        </div>
    </div>

    {{-- SECTION 4: Akun Akses (Hanya Muncul Jika Perlu Password) --}}
    @if ($tipe_anggota == 'Non-Karyawan' || in_array($tipe_anggota, ['Istri', 'Suami']))
    <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-lock mr-1.5 text-slate-400"></i> Akun Akses</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Buat Password Login</label>
                <div class="relative">
                    <input type="password" id="pass_add_kel" wire:model="password" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm transition-all">
                    <button type="button" onclick="togglePasswordVisibility('pass_add_kel', 'eyeOpen_ak1', 'eyeClosed_ak1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-600 focus:outline-none">
                        <img id="eyeOpen_ak1" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                        <img id="eyeClosed_ak1" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                    </button>
                </div>
                @error('password') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" id="pass_add_kel_conf" wire:model="password_confirmation" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm transition-all">
                    <button type="button" onclick="togglePasswordVisibility('pass_add_kel_conf', 'eyeOpen_ak2', 'eyeClosed_ak2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-600 focus:outline-none">
                        <img id="eyeOpen_ak2" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                        <img id="eyeClosed_ak2" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- Tombol Aksi --}}
    <div class="flex justify-end pt-2">
        <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow-md hover:-translate-y-0.5 transition-all text-xs active:scale-95">
            <span wire:loading.remove><i class="fas fa-save mr-1.5"></i> Registrasi Pasien</span>
            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-1.5"></i> Menyimpan...</span>
        </button>
    </div>
</form>

{{-- Pastikan kamu juga menaruh script JS SweetAlert pembaca URL Redirect yang kita bahas sebelumnya di bagian paling bawah halaman (luar form) --}}