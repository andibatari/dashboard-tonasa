{{-- Pembungkus data keluarga dengan gap yang dirapatkan untuk efisiensi ruang layar --}}
<div class="space-y-4 animate-fade-in">
    
    {{-- 1. IDENTITAS DEMOGRAFI --}}
    <div>
        <h3 class="text-[10px] md:text-xs font-black text-slate-800 uppercase tracking-widest mb-3 flex items-center border-b border-slate-100 pb-2">
            <div class="w-5 h-5 rounded bg-blue-50 text-blue-500 flex items-center justify-center mr-1.5"><i class="fas fa-id-card text-[10px]"></i></div> 
            Identitas Pasien
        </h3>
        
        {{-- Penggunaan CSS Grid: 2 kolom seragam di HP, menyesuaikan hingga 3 kolom di Desktop --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 hover:border-blue-200 transition-colors">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tipe Pasien</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $pesertaMcu->tipe_anggota ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 hover:border-blue-200 transition-colors">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">NIK (KTP)</span>
                <p class="text-xs md:text-sm font-black text-slate-800 font-mono truncate">{{ $pesertaMcu->nik_pasien ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 hover:border-blue-200 transition-colors">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pekerjaan</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $pesertaMcu->pekerjaan ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 hover:border-blue-200 transition-colors">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Agama</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $pesertaMcu->agama ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 hover:border-blue-200 transition-colors">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pendidikan</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $pesertaMcu->pendidikan ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- 2. FISIK & KELAHIRAN --}}
    <div>
        <h3 class="text-[10px] md:text-xs font-black text-slate-800 uppercase tracking-widest mb-3 flex items-center border-b border-slate-100 pb-2">
            <div class="w-5 h-5 rounded bg-emerald-50 text-emerald-500 flex items-center justify-center mr-1.5"><i class="fas fa-heartbeat text-[10px]"></i></div> 
            Biometrik & Kelahiran
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tgl Lahir</span>
                {{-- Parser Carbon mengubah data tanggal murni menjadi format string yang familiar di telinga manusia --}}
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $pesertaMcu->tanggal_lahir ? \Carbon\Carbon::parse($pesertaMcu->tanggal_lahir)->format('d M Y') : 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tempat Lahir</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $pesertaMcu->tempat_lahir ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Umur</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $pesertaMcu->umur ?? 'N/A' }} Thn</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Gender</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $pesertaMcu->jenis_kelamin ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tinggi</span>
                <p class="text-xs md:text-sm font-black text-slate-800">{{ $pesertaMcu->tinggi_badan ?? '-' }} cm</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Berat</span>
                <p class="text-xs md:text-sm font-black text-slate-800">{{ $pesertaMcu->berat_badan ?? '-' }} kg</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Gol. Darah</span>
                <p class="text-xs md:text-sm font-black text-red-600">{{ $pesertaMcu->golongan_darah ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- 3. ALAMAT & KONTAK --}}
    <div>
        <h3 class="text-[10px] md:text-xs font-black text-slate-800 uppercase tracking-widest mb-3 flex items-center border-b border-slate-100 pb-2">
            <div class="w-5 h-5 rounded bg-amber-50 text-amber-500 flex items-center justify-center mr-1.5"><i class="fas fa-map-marker-alt text-[10px]"></i></div> 
            Domisili & Kontak
        </h3>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Telepon</span>
                <p class="text-xs md:text-sm font-black text-slate-800 font-mono truncate">{{ $pesertaMcu->no_hp ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Provinsi</span>
                <p class="text-xs md:text-sm font-black text-slate-800 font-mono truncate">{{ $pesertaMcu->provinsi->nama_provinsi ?? '' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kecamatan</span>
                <p class="text-xs md:text-sm font-black text-slate-800 font-mono truncate">{{ $pesertaMcu->nama_kecamatan ?? '' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kabupaten</span>
                <p class="text-xs md:text-sm font-black text-slate-800 font-mono truncate">{{ $pesertaMcu->nama_kabupaten ?? '' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 col-span-2 lg:col-span-1">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email Akses</span>
                <p class="text-xs md:text-sm font-black text-slate-800 truncate">{{ $pesertaMcu->email ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 col-span-2">
                <span class="block text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat Lengkap</span>
                <p class="text-[11px] md:text-xs font-black text-slate-800 leading-tight">{{ $pesertaMcu->alamat ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    
    {{-- Aksi Edit Spesifik: Mendapatkan parameter rute keluarga.edit dengan melempar parameter current $pesertaMcu->id --}}
    <div class="pt-3 border-t border-slate-100 flex justify-end">
        <a href="{{ route('keluarga.edit', ['keluarga' => $pesertaMcu->id]) }}" class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg shadow-sm transition-all text-xs">
            <i class="fas fa-user-edit mr-1.5"></i> Edit Pasien
        </a>
    </div>
</div>