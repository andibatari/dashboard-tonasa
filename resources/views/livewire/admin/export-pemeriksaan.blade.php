<div class="px-3 md:px-6 py-4 md:py-6 min-h-screen" x-data="{ activeTab: 'rekapitulasi' }">
    @section('title', 'Pusat Laporan & Ekspor Data')
    
    {{-- TOMBOL NAVIGASI TAB (RESPONSIVE) --}}
    <div class="max-w-7xl mx-auto mb-4 md:mb-6 flex flex-col sm:flex-row gap-2 bg-white p-2 md:p-3 rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-slate-100">
        <button @click="activeTab = 'rekapitulasi'" 
            :class="activeTab === 'rekapitulasi' ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-slate-500 hover:bg-slate-50 font-medium'"
            class="flex-1 py-3.5 md:py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center text-sm md:text-base">
            <i class="fa-solid fa-file-excel mr-2 text-lg md:text-base" :class="activeTab === 'rekapitulasi' ? 'text-emerald-500' : 'text-slate-400'"></i>
            Panel Rekapitulasi (Excel)
        </button>
        
        <button @click="activeTab = 'laporan_pdf'" 
            :class="activeTab === 'laporan_pdf' ? 'bg-red-50 text-red-700 font-bold shadow-sm' : 'text-slate-500 hover:bg-slate-50 font-medium'"
            class="flex-1 py-3.5 md:py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center text-sm md:text-base">
            <i class="fa-solid fa-file-pdf mr-2 text-lg md:text-base" :class="activeTab === 'laporan_pdf' ? 'text-red-500' : 'text-slate-400'"></i>
            Daftar Laporan Medis (PDF)
        </button>
    </div>

    {{-- KONTEN TAB --}}
    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden max-w-7xl mx-auto min-h-[500px] md:min-h-[600px]">
        
        {{-- ==========================================
             TAB 1: PANEL EKSPOR REKAPITULASI (EXCEL)
             ========================================== --}}
        <div x-show="activeTab === 'rekapitulasi'" x-transition.opacity.duration.300ms>
            <div class="px-5 md:px-8 py-5 md:py-6 border-b border-slate-100 bg-slate-50">
                <h2 class="text-lg md:text-xl font-black text-slate-800 tracking-tight">Eksport Data Rekapitulasi (.xlsx)</h2>
                <p class="text-[11px] md:text-xs text-slate-500 mt-1 leading-relaxed">Gunakan filter di bawah ini untuk menarik data massal ke format Excel.</p>
            </div>

            <div class="p-5 md:p-8">
                {{-- Form Grid Responsive --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-5 mb-6 md:mb-8">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Mulai Tanggal</label>
                        <input type="date" wire:model.live="date_start" class="w-full border border-slate-200 bg-white rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Sampai Tanggal</label>
                        <input type="date" wire:model.live="date_end" class="w-full border border-slate-200 bg-white rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Filter Departemen</label>
                        <select wire:model.live="departemens_id" class="w-full border border-slate-200 bg-white rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors cursor-pointer">
                            <option value="">Semua Departemen</option>
                            @foreach($listDepartemen as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Kategori Peserta</label>
                        <select wire:model.live="tipe_anggota" class="w-full border border-slate-200 bg-white rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach($listKategori as $kat) 
                                <option value="{{ $kat }}">{{ $kat }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Status Akhir MCU</label>
                        <select wire:model.live="status_kehadiran" class="w-full border border-slate-200 bg-white rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors cursor-pointer">
                            <option value="">Semua Status</option>
                            @foreach($statusLabels as $value => $label) <option value="{{ $value }}">{{ $label }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Filter Poli</label>
                        <select wire:model.live="poli_id" class="w-full border border-slate-200 bg-white rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors cursor-pointer">
                            <option value="">Semua Poli (Paket Komplit)</option>
                            @foreach($listPoli as $key => $label) 
                                <option value="{{ $key }}">{{ $label }}</option> 
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Info Notice Responsive --}}
                <div class="flex items-start bg-amber-50 border border-amber-100 p-3 md:p-4 rounded-2xl mb-6 md:mb-8">
                    <i class="fa-solid fa-circle-info text-amber-500 mt-1 mr-3 text-base md:text-lg"></i>
                    <p class="text-[11px] md:text-xs font-medium text-amber-800 leading-relaxed">
                        <strong>Penting:</strong> Memilih "Filter Departemen" secara otomatis memfilter data <strong class="font-black">Khusus Karyawan PTST</strong>. Kosongkan pilihan ini jika Anda ingin mengekspor data keluarga atau pasien umum (Non-PTST).
                    </p>
                </div>

                {{-- Big Result Card Responsive --}}
                <div class="bg-slate-800 rounded-3xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between text-white shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-48 h-48 md:w-64 md:h-64 bg-white opacity-5 rounded-full -mr-16 -mt-16 md:-mr-20 md:-mt-20 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 md:w-40 md:h-40 bg-emerald-500 opacity-20 rounded-full blur-2xl md:blur-3xl -ml-8 -mb-8 md:-ml-10 md:-mb-10 pointer-events-none"></div>
                    
                    <div class="mb-5 md:mb-0 relative z-10 text-center md:text-left w-full">
                        <p class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-widest mb-2">Total Data Terekapitulasi</p>
                        <h3 class="text-5xl lg:text-6xl font-black text-white flex flex-col md:flex-row items-center md:items-baseline justify-center md:justify-start">
                            {{ number_format($total_preview) }} 
                            <span class="text-sm md:text-lg font-bold text-slate-400 mt-1 md:mt-0 md:ml-3">Dokumen MCU</span>
                        </h3>
                    </div>
                    
                    <div class="relative z-10 w-full md:w-auto mt-2 md:mt-0">
                        <button wire:click="exportExcel" @if($total_preview == 0) disabled @endif
                            class="w-full md:w-auto disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-500 hover:bg-emerald-400 text-emerald-950 font-black py-4 px-6 md:px-8 rounded-2xl flex items-center justify-center shadow-[0_10px_20px_rgba(16,185,129,0.3)] hover:shadow-[0_15px_30px_rgba(16,185,129,0.4)] transition-all text-sm tracking-wide">
                            <i class="fa-solid fa-download mr-3 text-lg md:text-xl"></i> Download Laporan (.xlsx)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==========================================
             TAB 2: DAFTAR LAPORAN MEDIS PASIEN (PDF)
             ========================================== --}}
        <div x-show="activeTab === 'laporan_pdf'" style="display: none;" x-transition.opacity.duration.300ms>
            <div class="px-5 md:px-8 py-5 md:py-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <div>
                    <h2 class="text-lg md:text-xl font-black text-slate-800 tracking-tight">Daftar Laporan Medis</h2>
                    <p class="text-[11px] md:text-xs text-slate-500 mt-1 leading-relaxed">Cari dan unduh dokumen hasil Medical Check Up pasien dalam format PDF.</p>
                </div>
            </div>
            
            <div class="p-5 md:p-8">
                {{-- Baris Filter & Pencarian Tabel --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
                    <div class="sm:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                        <input type="text" wire:model.live.debounce.500ms="searchTable" placeholder="Cari Nama / SAP..." 
                            class="w-full pl-11 border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm py-3 md:py-2.5 text-sm">
                    </div>
                    <div>
                        <select wire:model.live="tableDept" class="w-full border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm py-3 md:py-2.5 text-sm">
                            <option value="">Semua Departemen</option>
                            @foreach($listDepartemen as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="tableUnit" 
                            class="w-full border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm py-3 md:py-2.5 text-sm transition-colors {{ !$tableDept ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white' }}" 
                            {{ !$tableDept ? 'disabled' : '' }}>
                            
                            <option value="">Semua Unit Kerja</option>
                            @isset($listUnitKerja)
                                @foreach($listUnitKerja as $unit) 
                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit_kerja }}</option> 
                                @endforeach
                            @endisset
                        </select>
                    </div>
                </div>

                {{-- Tabel Data (Desktop View) --}}
                <div class="hidden md:block overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                                <th class="p-4 font-bold border-b border-slate-200">No</th>
                                <th class="p-4 font-bold border-b border-slate-200">Tgl MCU</th>
                                <th class="p-4 font-bold border-b border-slate-200 whitespace-nowrap">No. SAP / NIK</th>
                                <th class="p-4 font-bold border-b border-slate-200">Nama Pasien</th>
                                <th class="p-4 font-bold border-b border-slate-200">Departemen</th>
                                <th class="p-4 font-bold border-b border-slate-200">Status</th>
                                <th class="p-4 font-bold border-b border-slate-200 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($jadwalTable as $index => $jadwal)
                                <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100">
                                    <td class="p-4 text-slate-500">{{ $jadwalTable->firstItem() + $index }}</td>
                                    <td class="p-4 font-medium text-slate-800 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->tanggal_mcu)->format('d/m/Y') }}</td>
                                    <td class="p-4 text-slate-600">{{ $jadwal->karyawan->no_sap ?? $jadwal->pesertaMcu->nik_pasien ?? '-' }}</td>
                                    <td class="p-4 font-bold text-slate-800">{{ $jadwal->karyawan->nama_karyawan ?? $jadwal->pesertaMcu->nama_lengkap ?? $jadwal->nama_pasien }}</td>
                                    <td class="p-4 text-slate-600">{{ $jadwal->karyawan->departemen->nama_departemen ?? 'Non-PTST' }}</td>
                                    <td class="p-4">
                                        @if($jadwal->status == 'Finished')
                                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase">Selesai</span>
                                        @else
                                            <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase">Proses</span>
                                        @endif
                                    </td>
                                    <td class="p-4 flex items-center justify-center gap-2">
                                        <a href="{{ route('qr-patient-detail', ['jadwal' => $jadwal->id]) }}" 
                                            class="bg-blue-50 hover:bg-blue-500 text-blue-600 hover:text-white border border-blue-200 py-1.5 px-3 rounded-lg text-xs font-bold transition-colors whitespace-nowrap">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('download.mcu.summary', $jadwal->id) }}" target="_blank"
                                            class="bg-red-50 border border-red-200 text-red-600 hover:bg-red-600 hover:text-white hover:border-red-600 py-1.5 px-3 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center whitespace-nowrap">
                                            <i class="fa-solid fa-file-pdf mr-1.5"></i> Unduh PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-500 italic">Tidak ada data pasien yang sesuai pencarian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Tampilan Card (Mobile View) - INI YANG MEMBUAT KEREN DI HP --}}
                <div class="md:hidden space-y-4">
                    @forelse($jadwalTable as $jadwal)
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 relative shadow-sm">
                            {{-- Badge Status --}}
                            <div class="absolute top-4 right-4">
                                @if($jadwal->status == 'Finished')
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider">Selesai</span>
                                @else
                                    <span class="bg-amber-100 text-amber-700 px-2 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider">Proses</span>
                                @endif
                            </div>

                            <p class="text-[11px] text-slate-500 font-bold mb-1.5 uppercase tracking-wider"><i class="fas fa-calendar-alt text-slate-400 mr-1"></i> {{ \Carbon\Carbon::parse($jadwal->tanggal_mcu)->format('d M Y') }}</p>
                            <h4 class="font-black text-slate-800 text-base mb-1 pr-16 leading-tight">{{ $jadwal->karyawan->nama_karyawan ?? $jadwal->pesertaMcu->nama_lengkap ?? $jadwal->nama_pasien }}</h4>
                            <p class="text-[13px] font-bold text-blue-600 mb-3"><i class="fas fa-id-card text-blue-400 mr-1"></i> SAP/NIK: {{ $jadwal->karyawan->no_sap ?? $jadwal->pesertaMcu->nik_pasien ?? '-' }}</p>

                            <div class="flex items-center gap-2 mb-5">
                                <span class="bg-white border border-slate-200 text-slate-600 px-2.5 py-1 rounded-lg text-[11px] font-bold">
                                    <i class="fas fa-building text-slate-400 mr-1"></i> {{ $jadwal->karyawan->departemen->nama_departemen ?? 'Non-PTST' }}
                                </span>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('qr-patient-detail', ['jadwal' => $jadwal->id]) }}" class="flex-1 text-center bg-white text-blue-600 border-2 border-blue-100 py-2.5 rounded-xl text-xs font-black hover:bg-blue-50 transition-colors">
                                    Lihat Detail
                                </a>
                                <a href="{{ route('download.mcu.summary', $jadwal->id) }}" target="_blank" class="flex-1 flex justify-center items-center bg-red-600 text-white shadow-lg shadow-red-500/30 py-2.5 rounded-xl text-xs font-black hover:bg-red-700 transition-all active:scale-95">
                                    <i class="fa-solid fa-file-pdf mr-1.5"></i> Unduh
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 py-8 bg-slate-50 rounded-2xl border border-slate-100 text-sm font-medium">Tidak ada data pasien yang sesuai.</div>
                    @endforelse
                </div>
                
                {{-- Pagination --}}
                <div class="mt-6 border-t border-slate-100 pt-4">
                    {{ $jadwalTable->links() }}
                </div>

            </div>
        </div>

    </div>
</div>