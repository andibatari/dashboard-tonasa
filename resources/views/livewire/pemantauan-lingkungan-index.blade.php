@section('title', 'Pemantauan Lingkungan')
<div>
    {{-- px-2 md:px-4: Padding kiri-kanan disusutkan untuk layar HP (px-2) agar tabel tidak terpotong --}}
    <div class="px-2 md:px-4 py-4 md:py-6 min-h-screen">

        {{-- HEADER TOMBOL --}}
        <div class="flex flex-col md:flex-row md:items-center justify-end mb-4 md:mb-6 gap-3">
            <div class="flex gap-2 w-full md:w-auto">
                <button wire:click="downloadExcel" class="flex-1 md:flex-none inline-flex items-center justify-center bg-white border border-emerald-500 text-emerald-600 font-bold py-2 px-3 rounded-lg hover:bg-emerald-50 text-xs shadow-sm transition-colors">
                    <i class="fas fa-file-excel mr-1.5"></i> Excel
                </button>
                <a href="{{ route('pemantauan.create') }}" class="flex-1 md:flex-none inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded-lg shadow-md text-xs transition-colors">
                    <i class="fas fa-plus mr-1.5"></i> Tambah Baru
                </a>
            </div>
        </div>

        {{-- Notifikasi Sukses --}}
        @if (session()->has('message'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center shadow-sm mb-4 text-xs font-bold animate-fade-in">
                <i class="fas fa-check-circle mr-2"></i> {{ session('message') }}
            </div>
        @endif

        {{-- KARTU UTAMA --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-3 md:p-5">
                
                {{-- 1. DASHBOARD MINI (Total, Aman, Bahaya) --}}
                <div class="grid grid-cols-3 gap-2 md:gap-4 mb-5">
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-2 md:p-3 text-center">
                        <p class="text-[8px] md:text-[10px] font-bold text-blue-600 uppercase mb-0.5">Total</p>
                        <h3 class="text-sm md:text-xl font-black text-slate-800">{{ $totalData }}</h3>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-2 md:p-3 text-center">
                        <p class="text-[8px] md:text-[10px] font-bold text-emerald-600 uppercase mb-0.5">Aman</p>
                        <h3 class="text-sm md:text-xl font-black text-slate-800">{{ $lokasiAman }}</h3>
                    </div>
                    <div class="bg-red-50 border border-red-100 rounded-xl p-2 md:p-3 text-center">
                        <p class="text-[8px] md:text-[10px] font-bold text-red-600 uppercase mb-0.5">Bahaya</p>
                        <h3 class="text-sm md:text-xl font-black text-slate-800">{{ $lokasiBahaya }}</h3>
                    </div>
                </div>

                {{-- 2. FILTER PENCARIAN DATA --}}
                <div class="bg-slate-50 rounded-xl p-3 md:p-4 border border-slate-100 mb-5">
                    
                    {{-- Baris Filter 1: Pencarian & Tanggal --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3 pb-3 border-b border-slate-200">
                        <div class="col-span-2">
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Cari Lokasi</label>
                            <input type="text" wire:model.live.debounce.500ms="searchQuery" placeholder="Nama lokasi..." class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Mulai Tgl</label>
                            <input type="date" wire:model.live="startDate" class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Sampai Tgl</label>
                            <input type="date" wire:model.live="endDate" class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 focus:border-red-500">
                        </div>
                    </div>

                    {{-- Baris Filter 2: Dropdown Departemen & Area --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Departemen</label>
                            <select wire:model.live="filterDepartemen" class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2">
                                <option value="">Semua</option>
                                @foreach ($departments as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Unit Kerja</label>
                            <select wire:model.live="filterUnitKerja" class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 disabled:bg-slate-100" @if(empty($filterDepartemen)) disabled @endif>
                                <option value="">Semua</option>
                                @foreach ($filteredUnits as $unit) <option value="{{ $unit->id }}">{{ $unit->nama_unit_kerja }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Area</label>
                            <select wire:model.live="filterArea" class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2">
                                <option value="">Semua</option>
                                @foreach ($uniqueAreas as $area) <option value="{{ $area }}">{{ $area }}</option> @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button wire:click="resetFilters" class="w-full bg-white border border-slate-300 text-xs font-bold py-1.5 px-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">Reset Filter</button>
                        </div>
                    </div>

                    {{-- Baris Filter 3: Filter Batas NAB --}}
                    <div class="grid grid-cols-4 gap-2 mt-3 pt-3 border-t border-slate-200"> 
                        @foreach (['Cahaya' => 'filterNabCahaya', 'Bising' => 'filterNabBising', 'Debu' => 'filterNabDebu', 'Suhu' => 'filterNabSuhuIsbb'] as $label => $model)
                        <div>
                            <label class="block text-[8px] font-bold text-slate-500 uppercase mb-1">{{ $label }} (NAB)</label>
                            <select wire:model.live="{{ $model }}" class="w-full rounded-lg border-slate-200 text-[10px] py-1 px-1.5">
                                <option value="">Semua</option>
                                <option value="below">Aman</option>
                                <option value="above">Bahaya</option>
                            </select>
                        </div>
                        @endforeach
                    </div>

                    {{-- KETERANGAN HINT DATA TERBARU DI WEB --}}
                    <div class="mt-4 pt-3 border-t border-slate-200 flex items-center gap-2 text-slate-500 text-[10px] md:text-xs font-medium italic">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span>Data yang ditampilkan secara default adalah data terbaru. Untuk melihat atau mencari data lampau, silakan gunakan filter rentang tanggal di atas.</span>
                    </div>
                </div>

                {{-- 3. TABEL DATA PEMANTAUAN --}}
                @if ($pemantauanLingkunganGrouped->count() > 0)
                    <div class="overflow-x-auto border border-slate-200 rounded-lg hide-scrollbar max-h-[500px]">
                        <table class="min-w-full divide-y divide-slate-200 bg-white text-left border-collapse whitespace-nowrap sticky-header">
                            
                            {{-- Header Tabel --}}
                            <thead class="bg-slate-100 sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th rowspan="2" class="px-3 py-2 text-[9px] font-bold text-slate-600 uppercase border-r border-slate-200">No</th>
                                    <th rowspan="2" class="px-3 py-2 text-[9px] font-bold text-slate-600 uppercase border-r border-slate-200">Lokasi & Waktu</th>
                                    <th colspan="9" class="px-3 py-1.5 text-[9px] font-bold text-slate-600 uppercase text-center border-r border-slate-200 bg-slate-200/50">Hasil Ukur Aktual</th>
                                    <th rowspan="2" class="px-3 py-2 text-[9px] font-bold text-slate-600 uppercase border-r border-slate-200">Kesimpulan</th>
                                    <th rowspan="2" class="px-3 py-2 text-[9px] font-bold text-slate-600 uppercase text-center">Aksi</th>
                                </tr>
                                <tr class="bg-slate-50">
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">Cahaya</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">Bising</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">Debu</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">S.Basah</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">S.Kering</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">S.Radiasi</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">ISBB In</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">ISBB Out</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">RH %</th>
                                </tr>
                            </thead>
                            
                            {{-- Isi Tabel (Di-group berdasarkan Area) --}}
                            <tbody class="divide-y divide-slate-100">
                                @php $globalIndex = ($paginator->currentPage() - 1) * $paginator->perPage(); @endphp
                                @foreach ($pemantauanLingkunganGrouped as $area => $lokasis)
                                    {{-- Baris Judul Area --}}
                                    <tr class="bg-slate-100/70 border-t border-slate-200">
                                        <td colspan="13" class="px-3 py-1.5 text-[10px] font-black text-slate-800 uppercase">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-1"></i> AREA: {{ $area }}
                                        </td>
                                    </tr>
                                    {{-- Baris Data Lokasi --}}
                                    @foreach ($lokasis as $data)
                                        <tr wire:key="row-{{ $data->id }}" class="hover:bg-blue-50/30">
                                            <td class="px-3 py-2 text-[10px] text-slate-500 border-r border-slate-100">{{ ++$globalIndex }}</td>
                                            <td class="px-3 py-2 border-r border-slate-100">
                                                <p class="text-[11px] font-bold text-slate-800">{{ $data->lokasi }}</p>
                                                <p class="text-[9px] text-slate-500">{{ \Carbon\Carbon::parse($data->tanggal_pemantauan)->format('d/m/y') }} | {{ $data->departemen->nama_departemen ?? '-' }}</p>
                                            </td>
                                            
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100 @if ($this->checkNabStatus($data, 'cahaya', 'nab_cahaya')) bg-red-50 text-red-700 font-bold @endif">{{ $data->data_pemantauan['cahaya'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100 @if ($this->checkNabStatus($data, 'bising', 'nab_bising')) bg-red-50 text-red-700 font-bold @endif">{{ $data->data_pemantauan['bising'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100 @if ($this->checkNabStatus($data, 'debu', 'nab_debu')) bg-red-50 text-red-700 font-bold @endif">{{ $data->data_pemantauan['debu'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100">{{ $data->data_pemantauan['suhu_basah'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100">{{ $data->data_pemantauan['suhu_kering'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100">{{ $data->data_pemantauan['suhu_radiasi'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100 @if ($this->checkNabStatus($data, 'isbb_indoor', 'nab_suhu')) bg-red-50 text-red-700 font-bold @endif">{{ $data->data_pemantauan['isbb_indoor'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100 @if ($this->checkNabStatus($data, 'isbb_outdoor', 'nab_suhu')) bg-red-50 text-red-700 font-bold @endif">{{ $data->data_pemantauan['isbb_outdoor'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100">{{ $data->data_pemantauan['rh'] ?? '-' }}</td>
                                            <td class="px-3 py-2 text-[9px] font-medium text-slate-600 border-r border-slate-100 max-w-[150px] truncate" title="{{ $data->kesimpulan }}">
                                                {{ $data->kesimpulan ?? '-' }}
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <button wire:click="edit({{ $data->id }})" class="p-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-600 hover:text-white mr-1 transition-colors"><i class="fas fa-pen text-[10px]"></i></button>
                                                <button onclick="confirm('Hapus data ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $data->id }})" class="p-1.5 bg-red-50 text-red-600 rounded hover:bg-red-600 hover:text-white transition-colors"><i class="fas fa-trash text-[10px]"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- TOMBOL PAGINATION DITAMPILKAN DI SINI --}}
                    @if ($paginator->hasPages()) 
                        <div class="mt-4">{{ $paginator->links() }}</div> 
                    @endif
                @else
                    <div class="bg-slate-50 py-8 text-center rounded-xl border border-slate-100 text-xs font-bold text-slate-500">Tidak ada data ditemukan.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ==========================================
         4. MODAL EDIT & TAMBAH LOKASI
         ========================================== --}}
    @if ($isAddingNewLocation || $isEditing)
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center z-[100] p-3 animate-fade-in">
        <div class="bg-white w-full max-w-xl mx-auto rounded-2xl shadow-2xl overflow-hidden"> 
            
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-base font-black text-slate-800 flex items-center">
                    <i class="fas {{ $isEditing ? 'fa-edit text-blue-500' : 'fa-plus-circle text-emerald-500' }} mr-2"></i> 
                    {{ $isEditing ? "Edit Lokasi Pengukuran" : 'Tambah Titik Lokasi Baru' }}
                </h3>
                <button wire:click="{{ $isEditing ? 'cancelEdit' : 'cancelAddLocation' }}" class="text-slate-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-5 max-h-[65vh] overflow-y-auto custom-scrollbar">
                <form wire:submit.prevent="{{ $isEditing ? 'update' : 'saveNewLocation' }}" class="space-y-5">
                    
                    <div class="grid grid-cols-2 gap-4"> 
                        @if($isEditing)
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Departemen</label>
                                <select wire:model.live="editingData.departemens_id" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-blue-500">
                                    <option value="">Pilih Departemen</option>
                                    @foreach ($departments as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Unit Kerja</label>
                                <select wire:model.defer="editingData.unit_kerjas_id" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-blue-500">
                                    @foreach ($availableUnitsEdit as $unit) <option value="{{ $unit['id'] }}">{{ $unit['nama_unit_kerja'] }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Nama Area / Gedung</label>
                                <input type="text" wire:model.defer="editingData.area" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-blue-500">
                            </div>
                            {{-- 🔥 PERBAIKAN: Input Tanggal Ditambahkan di sini --}}
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tanggal Pemantauan</label>
                                <input type="date" wire:model.defer="editingData.tanggal_pemantauan" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-blue-500">
                            </div>
                            {{-- 🔥 Kolom Titik Lokasi membentang penuh (col-span-2) agar layout grid rapi --}}
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Titik Lokasi (Ruangan)</label>
                                <input type="text" wire:model.defer="editingData.lokasi" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-blue-500">
                            </div>
                        @else
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Departemen</label>
                                <input type="text" value="{{ $departments->find($newLocationData['departemens_id'])->nama_departemen ?? '-' }}" disabled class="w-full bg-slate-50 text-slate-500 rounded-xl border-slate-200 text-xs p-2.5">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Unit Kerja</label>
                                <input type="text" value="{{ $unitKerjas->find($newLocationData['unit_kerjas_id'])->nama_unit_kerja ?? '-' }}" disabled class="w-full bg-slate-50 text-slate-500 rounded-xl border-slate-200 text-xs p-2.5">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Nama Area / Gedung</label>
                                <input type="text" value="{{ $newLocationData['area'] ?? '' }}" disabled class="w-full bg-slate-50 text-slate-500 rounded-xl border-slate-200 text-xs p-2.5">
                            </div>
                            {{-- 🔥 PERBAIKAN: Input Tanggal (Mode Tambah Anak Lokasi) - Bersifat read-only --}}
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tanggal Pemantauan</label>
                                <input type="date" value="{{ $newLocationData['tanggal_pemantauan'] ?? '' }}" disabled class="w-full bg-slate-50 text-slate-500 rounded-xl border-slate-200 text-xs p-2.5">
                            </div>
                            {{-- 🔥 Kolom Lokasi Baru membentang penuh (col-span-2) --}}
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-emerald-600 uppercase mb-1">Lokasi Baru *</label>
                                <input type="text" wire:model.defer="newLocationData.lokasi" class="w-full rounded-xl border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500 text-xs p-2.5 shadow-sm" placeholder="Ketik nama titik/ruangan">
                            </div>
                        @endif
                    </div>

                    <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-100">
                        <h4 class="text-[10px] font-bold text-amber-700 uppercase mb-3"><i class="fas fa-exclamation-triangle mr-1"></i> Batas NAB (Nilai Ambang Batas)</h4>
                        <div class="grid grid-cols-4 gap-3">
                            @foreach (['nab_cahaya' => 'Cahaya', 'nab_bising' => 'Bising', 'nab_debu' => 'Debu', 'nab_suhu' => 'Suhu'] as $key => $label)
                                <div>
                                    <label class="block text-[9px] font-bold text-amber-800 mb-1">{{ $label }}</label>
                                    <input type="{{ $key == 'nab_debu' ? 'text' : 'number' }}" step="0.01" wire:model.defer="{{ $isEditing ? 'editingData.'.$key : 'newLocationData.'.$key }}" class="w-full rounded-lg border-amber-200 text-xs p-2 focus:border-amber-500 focus:ring-amber-500 bg-white">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-bold text-slate-600 uppercase mb-2"><i class="fas fa-clipboard-list mr-1"></i> Hasil Ukur Aktual</h4>
                        <div class="grid grid-cols-3 md:grid-cols-5 gap-3">
                            @foreach (['cahaya'=>'Cahaya', 'bising'=>'Bising', 'debu'=>'Debu', 'suhu_basah'=>'S.Basah', 'suhu_kering'=>'S.Kering', 'suhu_radiasi'=>'S.Radiasi', 'isbb_indoor'=>'ISBB In', 'isbb_outdoor'=>'ISBB Out', 'rh'=>'RH%'] as $key => $label)
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-500 mb-1">{{ $label }}</label>
                                    <input type="number" step="0.01" wire:model.defer="{{ $isEditing ? 'editingData.data_pemantauan.'.$key : 'newLocationData.pemantauan.'.$key }}" class="w-full rounded-lg border-slate-200 text-xs p-2 focus:border-blue-500">
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Kesimpulan / Rekomendasi (Opsional)</label>
                            <textarea rows="2" wire:model.defer="{{ $isEditing ? 'editingData.kesimpulan' : 'newLocationData.kesimpulan' }}" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-blue-500 shadow-sm resize-none" placeholder="Tuliskan catatan atau rekomendasi di titik ini..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50 flex flex-col md:flex-row justify-between items-center gap-3">
                <div class="w-full md:w-auto">
                    @if($isEditing)
                        <button type="button" wire:click="startAddLocation" class="w-full md:w-auto px-4 py-2 bg-emerald-100 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-bold rounded-xl transition-colors text-left md:text-center">
                            <i class="fas fa-plus mr-1"></i> Tambah Lokasi di Area Ini
                        </button>
                    @endif
                </div>

                <div class="flex w-full md:w-auto gap-2">
                    <button type="button" wire:click="{{ $isEditing ? 'cancelEdit' : 'cancelAddLocation' }}" class="flex-1 md:flex-none px-5 py-2 border border-slate-300 text-xs font-bold rounded-xl text-slate-600 bg-white hover:bg-slate-100 transition-colors">Batal</button>
                    <button type="button" wire:click="{{ $isEditing ? 'update' : 'saveNewLocation' }}" class="flex-1 md:flex-none px-6 py-2 text-xs font-bold rounded-xl text-white bg-slate-800 hover:bg-slate-700 shadow-md transition-colors">Simpan Data</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <style>
        .hide-scrollbar::-webkit-scrollbar{display:none;}
        .animate-fade-in { animation: fadeIn 0.2s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</div>