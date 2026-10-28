@section('title', 'Edit Data Karyawan')

<div>
    <div class="mb-4 md:mb-6">
        <a href="javascript:history.back()" class="inline-flex items-center px-3 py-1.5 text-xs md:text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-1.5"></i> Kembali
        </a>
    </div>

    {{-- KARTU UTAMA FORM --}}
    <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden p-4 md:p-8 max-w-7xl mx-auto">
        
        <div class="mb-5 md:mb-8 border-b border-slate-100 pb-4 md:pb-5 flex items-center">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 shrink-0">
                <i class="fas fa-user-edit text-sm md:text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg md:text-2xl font-black text-slate-800 leading-tight">Edit Data Karyawan</h1>
                <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-0.5">Perbarui informasi profil dan domisili.</p>
            </div>
        </div>

        <form wire:submit.prevent="updateKaryawan" class="space-y-4 md:space-y-6">
            
            {{-- SECTION 1: Informasi Dasar --}}
            <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
                <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4 flex items-center"><i class="fas fa-id-badge mr-2 text-slate-400"></i> Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                    @foreach([
                        ['model' => 'no_sap', 'label' => 'No SAP', 'req' => true],
                        ['model' => 'nik_karyawan', 'label' => 'NIK Karyawan', 'req' => true],
                        ['model' => 'nama_karyawan', 'label' => 'Nama Lengkap', 'req' => true],
                        ['model' => 'pekerjaan', 'label' => 'Pekerjaan', 'req' => false],
                    ] as $field)
                    <div>
                        <label for="{{ $field['model'] }}" class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">{{ $field['label'] }} @if($field['req'])<span class="text-red-500">*</span>@endif</label>
                        <input type="text" wire:model.live="{{ $field['model'] }}" id="{{ $field['model'] }}" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors shadow-sm" @if($field['req']) required @endif>
                        @error($field['model']) <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- SECTION 2: Data Pribadi & Fisik --}}
            <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
                <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4 flex items-center"><i class="fas fa-birthday-cake mr-2 text-slate-400"></i> Data Pribadi & Fisik</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3 md:mb-4">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Tempat Lahir</label>
                        <input type="text" wire:model.live="tempat_lahir" id="tempat_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        @error('tempat_lahir') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Tanggal Lahir</label>
                        <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Umur</label>
                        <input type="text" wire:model="umur" id="umur" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Jenis Kelamin</label>
                        <select wire:model.live="jenis_kelamin" id="jenis_kelamin" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-blue-500 cursor-pointer shadow-sm">
                            <option value="">Pilih</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Gol. Darah</label>
                        <select wire:model.live="golongan_darah" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer shadow-sm">
                            <option value="">Pilih</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Agama</label>
                        <select wire:model.live="agama" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer shadow-sm">
                            <option value="">Pilih</option><option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Buddha">Buddha</option><option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Sts. Pernikahan</label>
                        <select wire:model.live="status_pernikahan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer shadow-sm">
                            <option value="">Pilih Status</option><option value="Menikah">Menikah</option><option value="Belum Menikah">Belum Menikah</option><option value="Cerai Hidup">Cerai Hidup</option><option value="Cerai Mati">Cerai Mati</option>
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Hubungan</label>
                        <input type="text" wire:model.live="hubungan" id="hubungan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm">
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
                        @livewire('searchable-departemen', ['initialDepartemenId' => $departemens_id, 'initialUnitKerjaId' => $unit_kerjas_id])
                        @error('departemens_id') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        @error('unit_kerjas_id') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Alamat Domisili</label>
                        <div class="grid grid-cols-3 gap-2">
                            <select wire:model.live="provinsi_id" class="block w-full px-2 py-2 text-[10px] font-bold rounded-lg border border-slate-200 bg-white cursor-pointer shadow-sm"><option value="">Provinsi</option> @foreach($provinsis as $provinsi) <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option> @endforeach </select>
                            <input type="text" wire:model.live="nama_kabupaten" placeholder="Kabupaten" class="block w-full px-2 py-2 text-[10px] font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm">
                            <input type="text" wire:model.live="nama_kecamatan" placeholder="Kecamatan" class="block w-full px-2 py-2 text-[10px] font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 4: Kontak & Keluarga --}}
            <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
                <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4 flex items-center"><i class="fas fa-address-book mr-2 text-slate-400"></i> Kontak & Keluarga</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3 md:mb-4">
                    <div class="col-span-2 md:col-span-2">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Email Akses</label>
                        <input type="email" wire:model.live="email" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm">
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">No Handphone</label>
                        <input type="text" wire:model.live="no_hp" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm">
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Tinggi Badan (cm)</label>
                        <input type="number" step="any" wire:model.live="tinggi_badan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Alamat Detail</label>
                        <input type="text" wire:model.live="alamat" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Nama Pasangan</label>
                        <input type="text" wire:model.live="suami_istri" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Berat Badan (kg)</label>
                        <input type="number" step="any" wire:model.live="berat_badan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm">
                    </div>
                </div>
            </div>

            {{-- SECTION 5: Keamanan Akun Login --}}
            <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
                <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-1 flex items-center">
                    <i class="fas fa-lock mr-2 text-slate-400"></i> Keamanan Akun Login
                </h3>
                <p class="text-[10px] text-slate-400 mb-4 font-medium">Biarkan kolom kosong jika Anda tidak berencana mengubah kata sandi.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="pass_edit_kar" wire:model.live="password" placeholder="••••••••" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm transition-all">
                            <button type="button" onclick="togglePasswordVisibility('pass_edit_kar', 'eyeOpen_ek', 'eyeClosed_ek')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 focus:outline-none">
                                <img id="eyeOpen_ek" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                                <img id="eyeClosed_ek" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                            </button>
                        </div>
                        @error('password') <span class="text-red-500 text-[10px] font-bold mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" id="pass_edit_kar_confirm" wire:model.live="password_confirmation" placeholder="••••••••" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm transition-all">
                            <button type="button" onclick="togglePasswordVisibility('pass_edit_kar_confirm', 'eyeOpen_ek_conf', 'eyeClosed_ek_conf')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 focus:outline-none">
                                <img id="eyeOpen_ek_conf" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                                <img id="eyeClosed_ek_conf" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="flex justify-end pt-2">
                <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-8 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg shadow-md hover:-translate-y-0.5 transition-all text-xs md:text-sm active:scale-95">
                    <span wire:loading.remove><i class="fas fa-save mr-1.5"></i> Simpan Perubahan</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-1.5"></i> Memperbarui...</span>
                </button>
            </div>
        </form>
    </div>
</div>