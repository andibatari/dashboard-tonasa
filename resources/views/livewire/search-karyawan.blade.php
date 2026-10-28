<div>
    {{-- TABS & ACTIONS HEADER: Layout flex-col di HP dan flex-row di layar yang lebih besar --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 mb-5 border-b border-slate-100 pb-4">
        
        {{-- Segmented Control (Tabs) --}}
        <div class="flex p-1 bg-slate-100 rounded-xl md:rounded-2xl w-full xl:w-auto shrink-0 shadow-inner">
            <button wire:click="setActiveTab('ptst')" class="flex-1 xl:flex-none px-4 md:px-6 py-2 md:py-2.5 text-xs md:text-sm font-bold rounded-lg md:rounded-xl transition-all duration-200 {{ $activeTab === 'ptst' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                <i class="fas fa-id-badge md:mr-1"></i> <span class="hidden sm:inline">Karyawan</span> PTST
            </button>
            <button wire:click="setActiveTab('non-ptst')" class="flex-1 xl:flex-none px-4 md:px-6 py-2 md:py-2.5 text-xs md:text-sm font-bold rounded-lg md:rounded-xl transition-all duration-200 {{ $activeTab === 'non-ptst' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                <i class="fas fa-users md:mr-1"></i> <span class="hidden sm:inline">Peserta</span> Umum
            </button>
        </div>

        {{-- Action Buttons: Dirender menyesuaikan jenis Tab aktif --}}
        <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 w-full xl:w-auto">
            @if ($activeTab === 'ptst')
                {{-- Tombol import yang memicu klik pada input file hidden menggunakan JavaScript --}}
                <button onclick="document.getElementById('excel-upload-input').click()" class="col-span-2 sm:col-span-1 items-center justify-center bg-white border border-emerald-500 text-emerald-600 font-bold py-2 md:py-2.5 px-3 md:px-5 rounded-lg md:rounded-xl hover:bg-emerald-50 transition-all text-[10px] md:text-sm shadow-sm flex"> 
                    <i class="fas fa-file-excel mr-1.5"></i> Import Excel
                </button>
                {{-- Form submit otomatis via onchange event listener --}}
                <form id="excel-upload-form" action="{{ route('karyawan.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf <input type="file" name="file_karyawan" id="excel-upload-input" accept=".xlsx,.csv" onchange="this.form.submit()">
                </form>

                <a href="{{ route('karyawan.create') }}" class="flex items-center justify-center bg-red-600 text-white font-bold py-2 md:py-2.5 px-3 md:px-5 rounded-lg md:rounded-xl shadow-md hover:bg-red-700 hover:-translate-y-0.5 transition-all text-[10px] md:text-sm"> 
                    <i class="fas fa-user-plus mr-1.5"></i> Tambah Baru
                </a> 
                <a href="{{ route('karyawan.download') }}" class="flex items-center justify-center bg-slate-800 text-white font-bold py-2 md:py-2.5 px-3 md:px-5 rounded-lg md:rounded-xl shadow-md hover:bg-slate-700 hover:-translate-y-0.5 transition-all text-[10px] md:text-sm">
                    <i class="fas fa-download mr-1.5"></i> Ekspor Data
                </a>
            @else
                <button onclick="document.getElementById('excel-upload-input').click()" class="col-span-2 sm:col-span-1 items-center justify-center bg-white border border-emerald-500 text-emerald-600 font-bold py-2 md:py-2.5 px-3 md:px-5 rounded-lg md:rounded-xl hover:bg-emerald-50 transition-all text-[10px] md:text-sm shadow-sm flex"> 
                    <i class="fas fa-file-excel mr-1.5"></i> Import Excel
                </button>
                <form id="excel-upload-form" action="{{ route('peserta-mcu.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf <input type="file" name="file_peserta_mcu" id="excel-upload-input" accept=".xlsx,.csv" onchange="this.form.submit()">
                </form>
                <a href="{{ route('pasien.add.nonkaryawan') }}" class="flex items-center justify-center bg-red-600 text-white font-bold py-2 md:py-2.5 px-3 md:px-5 rounded-lg md:rounded-xl shadow-md hover:bg-red-700 hover:-translate-y-0.5 transition-all text-[10px] md:text-sm"> 
                    <i class="fas fa-user-plus mr-1.5"></i> Tambah Umum 
                </a> 
                <a href="{{ route('peserta.mcu.download') }}" class="flex items-center justify-center bg-slate-800 text-white font-bold py-2 md:py-2.5 px-3 md:px-5 rounded-lg md:rounded-xl shadow-md hover:bg-slate-700 hover:-translate-y-0.5 transition-all text-[10px] md:text-sm">
                    <i class="fas fa-download mr-1.5"></i> Ekspor Data
                </a>
            @endif
        </div>
    </div>

    {{-- SEARCH BAR --}}
    <div class="bg-slate-50 p-3 md:p-4 rounded-xl md:rounded-2xl border border-slate-100 mb-5">
        {{-- Ubah grid-cols menjadi 4 agar dropdown tampil sejajar rapi --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3"> 
            @if ($activeTab === 'ptst')
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                    <input type="text" wire:model.live.debounce.500ms="searchSap" placeholder="Cari by SAP..." class="w-full pl-8 pr-3 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg focus:border-red-500 focus:ring-red-500">
                </div>
                <div class="relative">
                    <i class="fas fa-font absolute left-3 top-3 text-slate-400 text-xs"></i>
                    <input type="text" wire:model.live.debounce.500ms="searchNama" placeholder="Cari by Nama..." class="w-full pl-8 pr-3 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg focus:border-red-500 focus:ring-red-500">
                </div>
                
                {{-- Dropdown Filter Departemen --}}
                <div class="relative">
                    <i class="fas fa-sitemap absolute left-3 top-3 text-slate-400 text-xs"></i>
                    <select wire:model.live="searchDepartemen" class="w-full pl-8 pr-3 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg focus:border-red-500 focus:ring-red-500 cursor-pointer appearance-none">
                        <option value="">Semua Departemen</option>
                        @foreach($listDepartemen as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Dropdown Filter Unit Kerja (Disabled jika dept belum dipilih) --}}
                <div class="relative">
                    <i class="fas fa-building absolute left-3 top-3 text-slate-400 text-xs"></i>
                    <select wire:model.live="searchUnitKerja" class="w-full pl-8 pr-3 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg focus:border-red-500 focus:ring-red-500 cursor-pointer appearance-none disabled:bg-slate-100 disabled:text-slate-400" @if(empty($listUnitKerja)) disabled @endif>
                        <option value="">Semua Unit Kerja</option>
                        @foreach($listUnitKerja as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->nama_unit_kerja }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="relative md:col-span-2">
                    <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                    <input type="text" wire:model.live.debounce.500ms="searchNamaPasien" placeholder="Cari by Nama Pasien..." class="w-full pl-8 pr-3 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg focus:border-red-500 focus:ring-red-500">
                </div>
                <div class="relative">
                    <i class="fas fa-id-card absolute left-3 top-3 text-slate-400 text-xs"></i>
                    <input type="text" wire:model.live.debounce.500ms="searchNik" placeholder="Cari by NIK..." class="w-full pl-8 pr-3 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg focus:border-red-500 focus:ring-red-500">
                </div>
            @endif
        </div>
    </div>
    
    {{-- 🌟 TAMBAHAN: DROPDOWN JUMLAH DATA PER HALAMAN --}}
    <div class="flex justify-between items-center mb-3 px-1">
        <div class="text-xs font-bold text-slate-500">
            Total: <span class="text-red-600">{{ $items->total() }}</span> Data
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-medium">Tampilkan</span>
            <select wire:model.live="perPage" class="text-xs font-medium bg-white border border-slate-200 rounded-lg focus:border-red-500 focus:ring-red-500 py-1.5 pl-3 pr-8 cursor-pointer shadow-sm appearance-none relative">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-xs text-slate-500 font-medium hidden sm:inline">Data</span>
        </div>
    </div>

    {{-- DATA TABEL KARYAWAN PTST --}}
    @if ($activeTab === 'ptst')
        <div class="overflow-x-auto border border-slate-100 rounded-xl hide-scrollbar">
            <table class="min-w-full bg-white text-left whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">No</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">SAP</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">Karyawan</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">Unit Kerja</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase hidden lg:table-cell">No KTP</th> 
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase hidden sm:table-cell">L/P</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase hidden md:table-cell">Jabatan</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($items as $karyawan)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 px-3 text-xs font-medium text-slate-500">{{ ($items->currentPage()-1) * $items->perPage() + $loop->iteration }}</td>
                            <td class="py-2.5 px-3 text-xs font-mono font-bold text-slate-600">{{ $karyawan->no_sap }}</td>
                            <td class="py-2.5 px-3 text-xs font-black text-slate-800">{{ $karyawan->nama_karyawan }}</td>
                            <td class="py-2.5 px-3">
                                <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold">{{ $karyawan->unitKerja->nama_unit_kerja ?? 'N/A' }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-xs text-slate-500 hidden lg:table-cell font-mono">{{ $karyawan->nik_karyawan }}</td>
                            <td class="py-2.5 px-3 text-[10px] font-bold text-slate-500 hidden sm:table-cell">{{ substr($karyawan->jenis_kelamin, 0, 1) }}</td>
                            <td class="py-2.5 px-3 text-xs text-slate-500 hidden md:table-cell">{{ $karyawan->jabatan ?? 'N/A' }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('karyawan.show', $karyawan->id) }}" class="w-6 h-6 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all" title="Detail"><i class="fas fa-eye text-[10px]"></i></a>
                                    <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="w-6 h-6 rounded bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-all" title="Edit"><i class="fas fa-pen text-[10px]"></i></a>
                                    <form action="{{ route('karyawan.destroy', $karyawan->id) }}" method="POST" onsubmit="return confirm('Hapus permanen data ini?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-6 h-6 rounded bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all" title="Hapus"><i class="fas fa-trash text-[10px]"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="py-8 text-center text-slate-400 text-xs font-bold">Data yang dicari tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 border-t border-slate-100 pt-3">{{ $items->links() }}</div>
    @endif

    {{-- TABLE PESERTA UMUM (NON-PTST) --}}
    @if ($activeTab === 'non-ptst')
        <div class="overflow-x-auto border border-slate-100 rounded-xl hide-scrollbar">
            <table class="min-w-full bg-white text-left whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">No</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">Nama Pasien</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">Perusahaan / Tipe</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase hidden sm:table-cell">No. KTP / SAP</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase hidden lg:table-cell">Tgl Lahir</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase hidden md:table-cell">Kontak</th>
                        <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($items as $pesertaMcu)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 px-3 text-xs font-medium text-slate-500">{{ ($items->currentPage()-1) * $items->perPage() + $loop->iteration }}</td>
                            <td class="py-2.5 px-3 text-xs font-black text-slate-800">{{ $pesertaMcu->nama_lengkap }}</td>
                            <td class="py-2.5 px-3">
                                <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ $pesertaMcu->perusahaan_asal ?? $pesertaMcu->tipe_anggota }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-[10px] text-slate-600 hidden sm:table-cell font-mono">
                                {{ $pesertaMcu->nik_pasien ?? '-' }} <br> 
                                <span class="text-slate-400">SAP: {{ $pesertaMcu->no_sap ?? '-' }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-xs text-slate-500 hidden lg:table-cell">{{ \Carbon\Carbon::parse($pesertaMcu->tanggal_lahir)->format('d M Y') }}</td>
                            <td class="py-2.5 px-3 text-xs text-slate-500 hidden md:table-cell">{{ $pesertaMcu->no_hp ?? '-' }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('pasien.show', ['pesertaMcu' => $pesertaMcu->id]) }}" class="w-6 h-6 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all" title="Detail"><i class="fas fa-eye text-[10px]"></i></a>
                                    <a href="{{ route('keluarga.edit', ['keluarga' => $pesertaMcu->id]) }}" class="w-6 h-6 rounded bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-all" title="Edit"><i class="fas fa-pen text-[10px]"></i></a>
                                    <form action="{{ route('keluarga.destroy', ['keluarga' => $pesertaMcu->id]) }}" method="POST" onsubmit="return confirm('Hapus permanen data ini?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-6 h-6 rounded bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all" title="Hapus"><i class="fas fa-trash text-[10px]"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-8 text-center text-slate-400 text-xs font-bold">Data yang Anda cari tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 border-t border-slate-100 pt-3">{{ $items->links() }}</div>
    @endif

    <style>.hide-scrollbar::-webkit-scrollbar { display: none; } .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }</style>
</div>