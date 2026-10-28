@section('title', 'Profil & Riwayat MCU Karyawan')

{{-- Pembungkus utama dengan max-w-7xl agar layout tidak melebar tak terbatas pada monitor besar --}}
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

    {{-- Layout grid utama yang membagi halaman menjadi 2 kolom utama pada layar laptop (lg:flex-row) --}}
    <div class="flex flex-col lg:flex-row gap-4"> 
        
        {{-- KOLOM KIRI (PROFIL & TANGGUNGAN): Ditetapkan lebar absolut 320px pada laptop agar proporsional --}}
        <div class="w-full lg:w-[320px] shrink-0 space-y-4">
            
            {{-- KARTU 1: IDENTITAS PASIEN AKTIF --}}
            <div class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-slate-100 text-center relative overflow-hidden">
                {{-- Aksen gradient dekoratif di bagian atas kartu, menggunakan absolute positioning --}}
                <div class="absolute top-0 left-0 w-full h-20 bg-gradient-to-br from-red-600 to-red-900 opacity-90 rounded-t-2xl"></div>
                
                <div class="relative mt-4 mb-3">
                    <div class="w-20 h-20 md:w-24 md:h-24 mx-auto bg-white rounded-full p-1 shadow-md ring-4 ring-white relative z-10 flex items-center justify-center overflow-hidden">
                        {{-- Memeriksa ketersediaan foto profil dari public atau Storage. Jika null, render icon placeholder --}}
                        @if($activeUser && $activeUser->foto_profil)
                            <img src="{{ asset('storage/' . $activeUser->foto_profil) }}" alt="Profil" class="w-full h-full object-cover rounded-full">
                        @else
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">
                                <i class="fas fa-user text-3xl"></i>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Menampilkan nama pengguna yang sedang di-select secara reaktif --}}
                <h2 class="text-lg font-black text-slate-800 leading-tight">{{ $activeUser ? ($activeUser->nama_lengkap ?? $activeUser->nama_karyawan) : 'Memuat...' }}</h2>
                <p class="text-[10px] font-bold text-red-600 mt-1 uppercase tracking-widest">{{ $activeUser && isset($activeUser->unitKerja) ? $activeUser->unitKerja->nama_unit_kerja : 'PASIEN UMUM / KELUARGA' }}</p>

                {{-- Blok informasi ID/NIK singkat --}}
                <div class="mt-4 flex flex-col gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100 text-left">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">ID / SAP</span>
                        <span class="text-xs font-mono font-black text-slate-700">{{ $activeUser->no_sap ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">NIK (KTP)</span>
                        <span class="text-xs font-mono font-black text-slate-700">{{ $activeUser->nik_pasien ?? $activeUser->nik_karyawan ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- KARTU 2: DAFTAR KELUARGA / TANGGUNGAN --}}
            <div class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-3 border-b border-slate-100 pb-3">
                    <h3 class="text-[10px] md:text-xs font-black text-slate-800 uppercase tracking-widest flex items-center">
                        <i class="fas fa-users text-slate-400 mr-2"></i> Data Keluarga
                    </h3>
                    <a href="{{ route('karyawan.add.keluarga', ['karyawan_id' => $karyawan->id]) }}" title="Tambah Anggota" class="w-6 h-6 bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white rounded flex items-center justify-center transition-colors">
                        <i class="fas fa-plus text-[10px]"></i>
                    </a>
                </div>
                
                {{-- 
                   Blok PHP in-line untuk mengevaluasi identitas entitas aktif.
                   Karyawan tidak memiliki kolom tipe_anggota (atau isinya 'Karyawan').
                   Peserta MCU (Keluarga) memiliki tipe_anggota spesifik ('Istri', 'Suami', dll).
                   Logika ini menjamin penyorotan tombol secara presisi tanpa variabel tambahan dari backend.
                --}}
                @php
                    $isKaryawanUtama = !isset($activeUser->tipe_anggota) || $activeUser->tipe_anggota === 'Karyawan';
                    $isIstri = isset($activeUser->tipe_anggota) && $activeUser->tipe_anggota === 'Istri';
                    $isSuami = isset($activeUser->tipe_anggota) && $activeUser->tipe_anggota === 'Suami';
                @endphp

                <div class="flex flex-col gap-2"> 
                    {{-- Tombol Navigasi Karyawan --}}
                    <button wire:click="selectKaryawan" class="w-full flex items-center justify-between p-2.5 rounded-xl border transition-all font-bold text-xs @if($isKaryawanUtama) border-red-500 bg-red-50 text-red-700 shadow-sm @else border-transparent bg-slate-50 hover:bg-slate-100 text-slate-600 @endif">
                        <div class="flex items-center"><i class="fas fa-user-tie w-5 text-left opacity-70"></i> Data Karyawan</div>
                        @if($isKaryawanUtama) <i class="fas fa-check-circle text-red-500"></i> @endif
                    </button>
                    
                    {{-- Tombol Navigasi Istri (Dirender jika data pesertaIstri eksis di database) --}}
                    @if ($pesertaIstri)
                    <button wire:click="selectIstri" class="w-full flex items-center justify-between p-2.5 rounded-xl border transition-all font-bold text-xs @if($isIstri) border-red-500 bg-red-50 text-red-700 shadow-sm @else border-transparent bg-slate-50 hover:bg-slate-100 text-slate-600 @endif">
                        <div class="flex items-center"><i class="fas fa-female w-5 text-left opacity-70"></i> Istri</div>
                        @if($isIstri) <i class="fas fa-check-circle text-red-500"></i> @endif
                    </button>
                    @endif

                    {{-- Tombol Navigasi Suami (Dirender jika data pesertaSuami eksis di database) --}}
                    @if ($pesertaSuami)
                    <button wire:click="selectSuami" class="w-full flex items-center justify-between p-2.5 rounded-xl border transition-all font-bold text-xs @if($isSuami) border-red-500 bg-red-50 text-red-700 shadow-sm @else border-transparent bg-slate-50 hover:bg-slate-100 text-slate-600 @endif">
                        <div class="flex items-center"><i class="fas fa-male w-5 text-left opacity-70"></i> Suami</div>
                        @if($isSuami) <i class="fas fa-check-circle text-red-500"></i> @endif
                    </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (KONTEN DINAMIS: PROFIL / RIWAYAT) --}}
        <div class="w-full flex-1"> 
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden min-h-[400px]">
                
                {{-- Segmented Tab Navigation. Menggunakan overflow-x-auto untuk dukungan scroll horizontal di mobile --}}
                <div class="flex p-1.5 m-3 md:m-4 bg-slate-100 rounded-xl overflow-x-auto hide-scrollbar w-max max-w-full">
                    <button wire:click="changeTab('data')" class="flex-none px-4 py-2 rounded-lg font-bold text-xs transition-all duration-200 @if($activeTab === 'data') bg-white text-slate-800 shadow-sm @else text-slate-500 hover:text-slate-700 @endif">
                        <i class="fas fa-id-card mr-1.5 opacity-70"></i> Data Lengkap
                    </button>
                    <button wire:click="changeTab('riwayat')" class="flex-none px-4 py-2 rounded-lg font-bold text-xs transition-all duration-200 @if($activeTab === 'riwayat') bg-white text-slate-800 shadow-sm @else text-slate-500 hover:text-slate-700 @endif">
                        <i class="fas fa-file-medical-alt mr-1.5 opacity-70"></i> Histori MCU
                    </button>
                </div>

                <div class="p-4 md:p-6 pt-0">
                    {{-- TAB 1: AREA DATA LENGKAP --}}
                    @if ($activeTab === 'data')
                        <div class="animate-fade-in">
                            @if ($activeUser)
                                {{-- Memuat partial template yang sesuai berdasarkan hasil komputasi tipe entitas --}}
                                @if ($isKaryawanUtama)
                                    @include('livewire.partials.user-data', ['user' => $activeUser, 'karyawan' => $karyawan])
                                @else
                                    @include('livewire.partials.keluarga-data-view', ['pesertaMcu' => $activeUser])
                                @endif
                            @else
                                <div class="py-10 text-center text-slate-400 text-xs font-medium bg-slate-50 rounded-xl border border-slate-100">Data profil tidak ditemukan.</div>
                            @endif
                        </div>
                    @endif
                    
                    {{-- TAB 2: AREA RIWAYAT KUNJUNGAN MCU --}}
                    @if ($activeTab === 'riwayat')
                        <div class="animate-fade-in">
                            @if ($activeUser)
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
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold border @if($riwayat->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200 @elseif($riwayat->status === 'Finished') bg-emerald-50 text-emerald-600 border-emerald-200 @else bg-slate-50 text-slate-600 border-slate-200 @endif">{{ $riwayat->status ?? '-' }}</span>
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
                            @else
                                <div class="py-8 text-center text-slate-400 text-xs font-medium bg-slate-50 rounded-xl border border-slate-100">Pilih anggota keluarga terlebih dahulu.</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Definisi CSS keyframes untuk efek masuk yang halus (fade-in) --}}
    <style>
        .animate-fade-in { animation: fadeIn 0.2s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>