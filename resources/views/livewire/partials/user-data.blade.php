<div class="space-y-4 md:space-y-6 animate-fade-in">
    
    {{-- 1. IDENTITAS & PEKERJAAN --}}
    <div>
        <h3 class="text-[11px] md:text-sm font-black text-slate-800 uppercase tracking-widest mb-3 md:mb-4 flex items-center border-b border-slate-100 pb-2">
            <div class="w-5 h-5 md:w-6 md:h-6 rounded bg-blue-50 text-blue-500 flex items-center justify-center mr-2"><i class="fas fa-id-badge text-[10px] md:text-xs"></i></div> 
            Identitas & Pekerjaan
        </h3>
        
        {{-- Implementasi CSS Grid untuk menyusun tata letak blok informasi. Kolom bertambah sesuai ukuran layar --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
            
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tipe Anggota</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->tipe_anggota ?? 'Karyawan' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">NIK (KTP)</span>
                {{-- Prioritas fallback nilai: Mengecek properti nik_karyawan, jika null menggunakan nik_pasien --}}
                <p class="text-xs md:text-sm font-black text-slate-800 font-mono truncate">{{ $user->nik_karyawan ?? $user->nik_pasien ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pekerjaan</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->pekerjaan ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pendidikan</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->pendidikan ?? 'N/A' }}</p>
            </div>

            {{-- Blok Kondisional: Hanya merender informasi instansi jika subjek merupakan karyawan aktif perusahaan --}}
            @if(($user->tipe_anggota ?? 'Karyawan') === 'Karyawan')
                <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                    <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jabatan</span>
                    <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->jabatan ?? 'N/A' }}</p>
                </div>
                <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                    <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Eselon</span>
                    <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->eselon ?? 'N/A' }}</p>
                </div>
                <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100 col-span-2 lg:col-span-1">
                    <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Departemen</span>
                    <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->departemen->nama_departemen ?? 'N/A' }}</p>
                </div>
                <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100 col-span-2 lg:col-span-1">
                    <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Unit Kerja</span>
                    <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->unitKerja->nama_unit_kerja ?? 'N/A' }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- 2. DEMOGRAFI FISIK & SOSIAL --}}
    <div>
        <h3 class="text-[11px] md:text-sm font-black text-slate-800 uppercase tracking-widest mb-3 md:mb-4 flex items-center border-b border-slate-100 pb-2">
            <div class="w-5 h-5 md:w-6 md:h-6 rounded bg-emerald-50 text-emerald-500 flex items-center justify-center mr-2"><i class="fas fa-heartbeat text-[10px] md:text-xs"></i></div> 
            Demografi & Fisik
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-4">
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tgl Lahir</span>
                {{-- Parsing tanggal mentah ke format pembacaan manusia melalui utility class milik Carbon --}}
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d/m/Y') : 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tpt Lahir</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->tempat_lahir ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Umur</span>
                <p class="text-xs md:text-sm font-black text-slate-800">{{ $user->umur ?? 'N/A' }} Thn</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Gender</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->jenis_kelamin ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tinggi</span>
                <p class="text-xs md:text-sm font-black text-slate-800">{{ $user->tinggi_badan ?? '-' }} cm</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Berat</span>
                <p class="text-xs md:text-sm font-black text-slate-800">{{ $user->berat_badan ?? '-' }} kg</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Gol. Darah</span>
                <p class="text-xs md:text-sm font-black text-red-600">{{ $user->golongan_darah ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sts Nikah</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->status_pernikahan ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- 3. ALAMAT & KONTAK --}}
    <div>
        <h3 class="text-[11px] md:text-sm font-black text-slate-800 uppercase tracking-widest mb-3 md:mb-4 flex items-center border-b border-slate-100 pb-2">
            <div class="w-5 h-5 md:w-6 md:h-6 rounded bg-amber-50 text-amber-500 flex items-center justify-center mr-2"><i class="fas fa-map-marker-alt text-[10px] md:text-xs"></i></div> 
            Kontak & Domisili
        </h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-3 md:mb-4">
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Telepon</span>
                <p class="text-xs md:text-sm font-black text-slate-800 font-mono truncate">{{ $user->no_hp ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Provinsi</span>
                <p class="text-xs md:text-sm font-black text-slate-800 font-mono truncate">{{ $user->provinsi->nama_provinsi ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kecamatan</span>
                <p class="text-xs md:text-sm font-black text-slate-800 font-mono truncate">{{ $user->nama_kecamatan ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kabupaten</span>
                <p class="text-xs md:text-sm font-black text-slate-800 font-mono truncate">{{ $user->nama_kabupaten ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100 col-span-2 lg:col-span-1">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email Akses</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $user->email ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100 col-span-2">
                <span class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat Lengkap</span>
                <p class="text-xs md:text-sm font-black text-slate-800">{{ $user->alamat ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    
    {{-- Aksi Edit: Routing menuju controller terkait yang disesuaikan berdasarkan entitas asal --}}
    <div class="pt-4 border-t border-slate-100 flex justify-end">
        @if (isset($karyawan) && $user->id === $karyawan->id)
            <a href="{{ route('karyawan.edit', ['karyawan' => $karyawan->id]) }}" class="w-full sm:w-auto text-center px-6 py-3 md:py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg md:rounded-xl shadow-md transition-all text-xs">
                <i class="fas fa-user-edit mr-1.5"></i> Edit Karyawan
            </a>
        @else
            <a href="{{ route('keluarga.edit', ['keluarga' => $user->id]) }}" class="w-full sm:w-auto text-center px-6 py-3 md:py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg md:rounded-xl shadow-md transition-all text-xs">
                <i class="fas fa-user-edit mr-1.5"></i> Edit Anggota Keluarga
            </a>
        @endif
    </div>
</div>