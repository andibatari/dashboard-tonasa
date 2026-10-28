<div class="w-full max-w-7xl mx-auto px-3 md:px-0">

    {{-- Header navigasi menggunakan flexbox. flex-col pada mobile agar bertumpuk, flex-row pada layar >= sm agar sejajar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 md:mb-6 gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight">Profil Pasien</h1>
            <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-0.5">Pusat data identitas dan histori medical check-up.</p>
        </div>
        {{-- Tombol kembali. Menggunakan w-full di mobile agar tap-area maksimal, dan w-auto di desktop --}}
        <a href="{{ route('karyawan.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center bg-white border border-slate-200 text-slate-600 font-bold py-2 md:py-2.5 px-4 rounded-xl hover:bg-slate-50 transition-colors text-xs shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="flex flex-col lg:flex-row gap-4 md:gap-6">
        
        {{-- Kolom Kiri: Ringkasan Identitas --}}
        <div class="w-full lg:w-[320px] xl:w-[350px] shrink-0 space-y-4 md:space-y-6">
            
            {{-- KARTU PROFIL UTAMA --}}
            <div class="bg-white p-5 md:p-6 rounded-2xl md:rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-20 md:h-24 bg-gradient-to-br from-blue-600 to-indigo-800 opacity-90 rounded-t-2xl md:rounded-t-[2rem]"></div>
                
                <div class="relative mt-4 md:mt-6 mb-3 md:mb-4">
                    {{-- 🌟 Menambahkan deteksi dan render foto dari driver public --}}
                    <div class="w-20 h-20 md:w-24 md:h-24 mx-auto bg-white rounded-full p-1 shadow-lg ring-4 ring-white flex items-center justify-center overflow-hidden">
                        @if($pesertaMcu && $pesertaMcu->foto_profil)
                            <img src="{{ asset('storage/' . $pesertaMcu->foto_profil) }}" alt="Profil" class="w-full h-full object-cover rounded-full">
                        @else
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300 rounded-full">
                                <i class="fas fa-user text-3xl md:text-4xl"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <h2 class="text-lg md:text-xl font-black text-slate-800 leading-tight">{{ $pesertaMcu->nama_lengkap }}</h2>
                <p class="text-[10px] md:text-xs font-bold text-blue-600 mt-1 uppercase tracking-widest">{{ $pesertaMcu->tipe_anggota ?? 'Pasien Umum' }}</p>
                <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-0.5">{{ $pesertaMcu->perusahaan_asal ?? 'Umum' }}</p>

                <div class="mt-4 md:mt-5 flex flex-col gap-2 bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100 text-left">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] md:text-xs font-bold text-slate-400">NIK (KTP)</span>
                        <span class="text-xs md:text-sm font-mono font-black text-slate-700">{{ $pesertaMcu->nik_pasien ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] md:text-xs font-bold text-slate-400">SAP / NIP</span>
                        <span class="text-xs md:text-sm font-mono font-black text-slate-700">{{ $pesertaMcu->no_sap ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- KARTU REFERENSI KARYAWAN (Hanya dirender apabila pasien ini diregistrasi melalui akun karyawan) --}}
            @if ($pesertaMcu->karyawan_id)
            <div class="bg-white p-5 md:p-6 rounded-2xl md:rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
                <h3 class="text-[11px] md:text-sm font-black text-slate-800 uppercase tracking-widest mb-3 md:mb-4 border-b border-slate-100 pb-2 md:pb-3 flex items-center">
                    <i class="fas fa-user-tie text-slate-400 mr-2"></i> Relasi Karyawan
                </h3>
                <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100">
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase">Nama Karyawan</p>
                    <p class="font-bold text-slate-800 text-xs md:text-sm mb-2 md:mb-3">{{ $pesertaMcu->karyawan->nama_karyawan ?? 'N/A' }}</p>
                    
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase">Departemen</p>
                    <p class="font-bold text-slate-800 text-xs md:text-sm mb-3">{{ $pesertaMcu->karyawan->unitKerja->nama_unit_kerja ?? 'N/A' }}</p>
                    
                    <a href="{{ route('karyawan.show', ['karyawan' => $pesertaMcu->karyawan_id]) }}" class="block w-full py-2 md:py-2.5 text-center text-[10px] md:text-xs font-bold text-white bg-slate-800 rounded-lg hover:bg-slate-700 transition-colors mt-2">Buka Profil Utama</a>
                </div>
            </div>
            @endif
        </div>

        {{-- Kolom Kanan: TABS NAVIGASI & KONTEN MULTI-VIEW --}}
        <div class="w-full flex-1">
            <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden min-h-[400px]">
                
                {{-- Menyembunyikan scrollbar native webkit untuk tampilan antarmuka yang bersih pada perangkat HP --}}
                <div class="flex p-1.5 md:p-2 m-3 md:m-4 bg-slate-100 rounded-xl md:rounded-2xl overflow-x-auto hide-scrollbar">
                    <button wire:click="changeTab('data')" class="flex-none px-4 md:px-6 py-2 md:py-2.5 rounded-lg md:rounded-xl font-bold text-xs md:text-sm transition-all duration-200 @if($activeTab === 'data') bg-white text-slate-800 shadow-sm @else text-slate-500 hover:text-slate-700 @endif">
                        <i class="fas fa-id-card mr-1.5 opacity-70"></i> Data Pasien
                    </button>
                    <button wire:click="changeTab('riwayat')" class="flex-none px-4 md:px-6 py-2 md:py-2.5 rounded-lg md:rounded-xl font-bold text-xs md:text-sm transition-all duration-200 @if($activeTab === 'riwayat') bg-white text-slate-800 shadow-sm @else text-slate-500 hover:text-slate-700 @endif">
                        <i class="fas fa-notes-medical mr-1.5 opacity-70"></i> Riwayat MCU
                    </button>
                </div>

                <div class="p-4 md:p-8 pt-0">
                    @if ($activeTab === 'data')
                        @if ($pesertaMcu)
                            <div class="animate-fade-in">
                                {{-- Memanggil sub-view (blade partials) yang memuat blok HTML demografi data --}}
                                @include('livewire.partials.keluarga-data-view', ['pesertaMcu' => $pesertaMcu])
                            </div>
                        @endif
                    @endif
                    
                    @if ($activeTab === 'riwayat')
                        <div class="animate-fade-in">
                            <div class="mb-4 md:mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3 md:pb-4">
                                <h3 class="font-black text-sm md:text-lg text-slate-800">Kunjungan Medical Check-Up</h3>
                                <div class="flex items-center gap-2">
                                    <label class="text-[10px] md:text-xs font-bold text-slate-500 uppercase">Tahun:</label>
                                    {{-- Filterisasi data berbasis rentang waktu tahun (men-trigger lifecycle hook di Livewire saat berubah) --}}
                                    <select wire:model.live="selectedYear" class="block rounded-lg md:rounded-xl border border-slate-200 bg-white shadow-sm text-xs font-bold p-1.5 md:p-2 focus:border-red-500 cursor-pointer">
                                        <option value="">Semua</option>
                                        @for ($year = date('Y'); $year >= 2020; $year--) <option value="{{ $year }}">{{ $year }}</option> @endfor
                                    </select>
                                </div>
                            </div>

                            {{-- Desktop Table --}}
                            <div class="hidden md:block overflow-x-auto border border-slate-100 rounded-xl">
                                <table class="min-w-full text-xs bg-white border-collapse text-left">
                                    <thead class="bg-slate-50 border-b border-slate-100">
                                        <tr>
                                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tgl Periksa</th>
                                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Dokter PIC</th>
                                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @forelse ($filteredRecords as $jadwalMcu)
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-3 px-3 font-bold text-slate-700">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_mcu)->format('d/m/Y') }}</td>
                                            <td class="py-3 px-3 font-medium text-slate-600 truncate">{{ $jadwalMcu->dokter->nama_lengkap ?? '-' }}</td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold border @if($jadwalMcu->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200 @elseif($jadwalMcu->status === 'Finished') bg-emerald-50 text-emerald-600 border-emerald-200 @else bg-slate-50 text-slate-600 border-slate-200 @endif">{{ $jadwalMcu->status ?? '-' }}</span>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <a href="{{ route('qr-patient-detail', $jadwalMcu->id) }}" class="text-[10px] font-bold text-blue-600 hover:text-white bg-blue-50 hover:bg-blue-600 px-2 py-1 rounded transition-colors">Buka Berkas</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="py-6 text-center text-slate-400 italic">Belum ada data kunjungan medis.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- Mobile Card untuk layout yang optimal di layar beresolusi sempit --}}
                            <div class="md:hidden space-y-3">
                                @forelse($filteredRecords as $riwayat)
                                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-3.5">
                                        <div class="flex justify-between items-start mb-2 border-b border-slate-100 pb-2">
                                            <div>
                                                <span class="text-[9px] font-black uppercase text-slate-400">Tgl Periksa</span>
                                                <p class="font-bold text-slate-800 text-xs mt-0.5">{{ \Carbon\Carbon::parse($riwayat->tanggal_mcu)->format('d/m/Y') }}</p>
                                            </div>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold border">{{ $riwayat->status ?? '-' }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="text-[9px] font-black uppercase text-slate-400">Dokter</span>
                                            <p class="font-medium text-slate-600 text-[11px] mt-0.5 truncate">{{ $riwayat->dokter->nama_lengkap ?? '-' }}</p>
                                        </div>
                                        <a href="{{ route('qr-patient-detail', $riwayat->id) }}" class="block w-full py-1.5 text-center text-[10px] font-bold text-slate-700 bg-slate-100 rounded hover:bg-slate-200 transition-colors">Buka Berkas Laboratorium</a>
                                    </div>
                                @empty
                                    <div class="py-8 bg-slate-50 text-center rounded-xl border border-slate-100">
                                        <p class="text-xs font-bold text-slate-400">Tidak ada riwayat untuk tahun ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .animate-fade-in { animation: fadeIn 0.2s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>