<div>
    {{-- Prevent default form submission HTML, diganti dengan memanggil fungsi Livewire saveKaryawan --}}
    <form wire:submit.prevent="saveKaryawan" class="space-y-4 md:space-y-6">
        
        {{-- SECTION 1: Informasi Dasar --}}
        {{-- Padding disusutkan menjadi p-4 md:p-6 agar lebih hemat ruang --}}
        <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4 flex items-center"><i class="fas fa-id-badge mr-2 text-slate-400"></i> Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                
                {{-- Looping array of fields untuk menyingkat kode penulisan input yang berulang --}}
                @foreach([
                    ['model' => 'no_sap', 'label' => 'No SAP', 'req' => true],
                    ['model' => 'nik_karyawan', 'label' => 'NIK Karyawan', 'req' => true],
                    ['model' => 'nama_karyawan', 'label' => 'Nama Lengkap', 'req' => true],
                    ['model' => 'pekerjaan', 'label' => 'Pekerjaan', 'req' => false],
                ] as $field)
                <div>
                    <label for="{{ $field['model'] }}" class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">{{ $field['label'] }} @if($field['req'])<span class="text-red-500">*</span>@endif</label>
                    <input type="text" wire:model.live="{{ $field['model'] }}" id="{{ $field['model'] }}" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors shadow-sm" @if($field['req']) required @endif>
                    @error($field['model']) <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endforeach
            </div>
        </div>

        {{-- SECTION 2: Demografi --}}
        <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4 flex items-center"><i class="fas fa-birthday-cake mr-2 text-slate-400"></i> Data Pribadi & Fisik</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3 md:mb-4">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Tempat Lahir</label>
                    <input type="text" wire:model.live="tempat_lahir" id="tempat_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 shadow-sm">
                    @error('tempat_lahir') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Tanggal Lahir</label>
                    <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 shadow-sm">
                </div>
                <div>
                    {{-- Input umur dibuat readonly karena kalkulasi logic dilakukan di controller berdasarkan field tanggal_lahir --}}
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Umur</label>
                    <input type="text" wire:model="umur" id="umur" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Jenis Kelamin</label>
                    <select wire:model.live="jenis_kelamin" id="jenis_kelamin" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 cursor-pointer shadow-sm">
                        <option value="">Pilih</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Gol. Darah</label>
                    <select wire:model.live="golongan_darah" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 cursor-pointer shadow-sm">
                        <option value="">Pilih</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Agama</label>
                    <select wire:model.live="agama" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 cursor-pointer shadow-sm">
                        <option value="">Pilih</option><option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Buddha">Buddha</option><option value="Konghucu">Konghucu</option>
                    </select>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Sts. Pernikahan</label>
                    <select wire:model.live="status_pernikahan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 cursor-pointer shadow-sm">
                        <option value="">Pilih Status</option><option value="Menikah">Menikah</option><option value="Belum Menikah">Belum Menikah</option><option value="Cerai Hidup">Cerai Hidup</option><option value="Cerai Mati">Cerai Mati</option>
                    </select>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Hubungan</label>
                    <input type="text" wire:model.live="hubungan" id="hubungan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                </div>
            </div>
        </div>

        {{-- SECTION 3: Organisasi & Alamat --}}
        <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4 flex items-center"><i class="fas fa-building mr-2 text-slate-400"></i> Organisasi & Lokasi</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-4">
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Kebangsaan</label>
                    <select wire:model.live="kebangsaan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white cursor-pointer shadow-sm"><option value="">Pilih</option><option value="WNI">WNI</option><option value="WNA">WNA</option></select>
                </div>
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Jabatan</label>
                    <select wire:model.live="jabatan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white cursor-pointer shadow-sm"><option value="">Pilih Jabatan</option><option value="Senior Manager">Senior Manager</option><option value="Manager">Manager</option><option value="Associate">Associate</option><option value="Supervisor">Supervisor</option><option value="Staff">Staff</option></select>
                </div>
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Eselon</label>
                    <select wire:model.live="eselon" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white cursor-pointer shadow-sm"><option value="">Pilih</option><option value="I">I</option><option value="II">II</option><option value="III">III</option><option value="IV">IV</option><option value="V">V</option></select>
                </div>
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Pendidikan</label>
                    <select wire:model.live="pendidikan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white cursor-pointer shadow-sm"><option value="">Pilih</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA/SMK">SMA/SMK</option><option value="D1">D1</option><option value="D2">D2</option><option value="D3">D3</option><option value="D4">D4</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option></select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4 pt-4 border-t border-slate-200">
                <div class="col-span-1">
                    {{-- Menyisipkan komponen terpisah untuk pemilihan departemen yang relasional --}}
                    @livewire('searchable-departemen', ['initialDepartemenId' => $departemens_id, 'initialUnitKerjaId' => $unit_kerjas_id])
                </div>
                <div class="col-span-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Alamat Domisili</label>
                    <div class="grid grid-cols-3 gap-2">
                        <select wire:model.live="provinsi_id" class="block w-full px-2 py-2 text-[10px] font-bold rounded-lg border border-slate-200 bg-white cursor-pointer shadow-sm"><option value="">Provinsi</option> @foreach($provinsis as $provinsi) <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option> @endforeach </select>
                        <input type="text" wire:model.live="nama_kabupaten" placeholder="Kabupaten" class="block w-full px-2 py-2 text-[10px] font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                        <input type="text" wire:model.live="nama_kecamatan" placeholder="Kecamatan" class="block w-full px-2 py-2 text-[10px] font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 4: Kontak --}}
        <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4 flex items-center"><i class="fas fa-address-book mr-2 text-slate-400"></i> Kontak & Keluarga</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3 md:mb-4">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Email Akses</label>
                    <input type="email" wire:model.live="email" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="pass_create_kar" wire:model.live="password" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm transition-all">
                        <button type="button" onclick="togglePasswordVisibility('pass_create_kar', 'eyeOpen_ck', 'eyeClosed_ck')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-600 focus:outline-none">
                            <img id="eyeOpen_ck" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                            <img id="eyeClosed_ck" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                        </button>
                    </div>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">No Handphone</label>
                    <input type="text" wire:model.live="no_hp" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Tinggi Badan (cm)</label>
                    <input type="number" step="any" wire:model.live="tinggi_badan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4">
                <div class="md:col-span-2">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Alamat Detail</label>
                    <input type="text" wire:model.live="alamat" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Nama Pasangan</label>
                    <input type="text" wire:model.live="suami_istri" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Berat Badan (kg)</label>
                    <input type="number" step="any" wire:model.live="berat_badan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                </div>
            </div>
        </div>

        {{-- Tombol submit memanfaatkan w-full pada layar mobile agar target area ketuk (tap) menjadi optimal --}}
        <div class="flex justify-end pt-2">
            <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg shadow hover:shadow-lg transition-all text-xs md:text-sm active:scale-95">
                <span wire:loading.remove><i class="fas fa-save mr-2"></i> Simpan Data Karyawan</span>
                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>

{{-- 
    Catatan:
    - Banyak bagian kode yang menggunakan loop @foreach untuk menghindari penulisan kode yang berulang (DRY principle).
    - Kelas CSS disesuaikan agar tampilan tetap rapi dan responsif di berbagai ukuran layar.
    - Validasi error ditampilkan langsung di bawah input terkait untuk memberikan feedback yang jelas kepada pengguna.
--}}