<form wire:submit.prevent="save">
    <div class="space-y-4 md:space-y-6"> 

        {{-- 1. PENCARIAN PASIEN --}}
        <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100 relative shadow-sm">
            <h4 class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-widest mb-3"><i class="fas fa-search mr-1.5"></i> Langkah 1: Pilih Pasien</h4>
            
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user-circle text-slate-400"></i>
                </div>
                {{-- Event input debounce mengatur penundaan query pencarian untuk mengurangi beban server selama pengetikan cepat --}}
                <input type="text" id="karyawan_search" wire:model.live.debounce.300ms="search" 
                    class="block w-full pl-9 pr-3 py-2.5 text-xs font-bold rounded-lg border border-slate-200 bg-white shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-slate-400 transition-colors"
                    placeholder="Ketik Nama, NIK, atau No. SAP..." autocomplete="off">
                @error('karyawan_id') <p class="mt-1 text-[10px] font-bold text-red-500"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror

                {{-- Kontainer absolut digunakan agar hasil pencarian dropdown mengambang dan tidak menggeser elemen HTML di bawahnya --}}
                @if (!empty($results))
                    <div class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl max-h-48 overflow-y-auto divide-y divide-slate-50">
                        @foreach ($results as $item)
                            <div wire:click="selectPatient({{ $item['id'] }}, '{{ $item['search_type'] }}')"
                                class="px-4 py-2 cursor-pointer hover:bg-slate-50 transition-colors duration-150 group">
                                <p class="font-bold text-xs text-slate-800 group-hover:text-red-600">{{ $item['search_name'] }}</p>
                                <p class="text-[10px] font-medium text-slate-500 mt-0.5">
                                    NIK: <span class="font-mono">{{ $item['search_nik'] }}</span> | SAP: <span class="font-mono">{{ $item['no_sap'] ?? '-' }}</span>
                                    @if ($item['search_type'] == 'peserta_mcu')
                                        <span class="ml-1 inline-flex px-1.5 py-0.5 rounded text-[8px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-widest">Umum</span>
                                    @else
                                        <span class="ml-1 inline-flex px-1.5 py-0.5 rounded text-[8px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-widest">Karyawan</span>
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. DETAIL PASIEN --}}
        {{-- Hanya di-render di DOM ketika property $selectedKaryawan tidak null (terpilih melalui interaksi) --}}
        @if ($selectedKaryawan)
            <div class="bg-white p-4 md:p-6 rounded-xl border border-slate-200 shadow-sm animate-fade-in">
                <h4 class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-widest mb-3"><i class="fas fa-id-card mr-1.5"></i> Profil Pasien</h4>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 md:gap-3"> 
                    {{-- Parsing tipe object model karyawan / peserta umum ke array seragam --}}
                    @php
                        $details = [];
                        if ($patientType === 'karyawan') {
                            $details = [
                                'NO SAP' => ['value' => $selectedKaryawan->no_sap, 'icon' => 'fa-hashtag'],
                                'NAMA' => ['value' => $selectedKaryawan->nama_karyawan, 'icon' => 'fa-user'],
                                'UNIT KERJA' => ['value' => $selectedKaryawan->unitKerja->nama_unit_kerja ?? 'N/A', 'icon' => 'fa-building'],
                                'JABATAN' => ['value' => $selectedKaryawan->jabatan ?? 'N/A', 'icon' => 'fa-briefcase'],
                            ];
                        } elseif ($patientType === 'peserta_mcu') {
                             $details = [
                                'NAMA' => ['value' => $selectedKaryawan->nama_lengkap, 'icon' => 'fa-user'],
                                'NIK' => ['value' => $selectedKaryawan->nik_pasien ?? 'N/A', 'icon' => 'fa-id-badge'],
                                'PERUSAHAAN' => ['value' => $selectedKaryawan->perusahaan_asal ?? 'N/A', 'icon' => 'fa-building'],
                                'TIPE' => ['value' => $selectedKaryawan->tipe_anggota ?? 'N/A', 'icon' => 'fa-users'],
                            ];
                        }
                    @endphp

                    {{-- Render grid statis berbasis array mapping --}}
                    @foreach ($details as $label => $data)
                        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 flex items-start gap-2 overflow-hidden"> 
                            <div class="bg-white p-1.5 rounded text-slate-400 shadow-sm shrink-0"><i class="fas {{ $data['icon'] }} text-[10px] w-3 text-center"></i></div>
                            <div class="min-w-0">
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">{{ $label }}</p>
                                <p class="font-black text-[10px] md:text-xs text-slate-800 truncate" title="{{ $data['value'] ?? '-' }}">{{ $data['value'] ?? '-' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 3. FORM INPUT JADWAL UTAMA --}}
        <div class="bg-white p-4 md:p-6 rounded-xl border border-slate-200 shadow-sm">
            <h4 class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-widest mb-3"><i class="fas fa-calendar-check mr-1.5"></i> Langkah 2: Jadwal & Layanan</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3"> 
                <div>
                    <label for="tanggal_mcu" class="block text-[10px] font-bold text-slate-700 mb-1">Tanggal MCU <span class="text-red-500">*</span></label>
                    {{-- 
                      UBAH DI SINI: wire:model diubah menggunakan modifier '.live'
                      Tujuannya agar setiap kali ada perubahan tanggal di kalender, Livewire langsung mendeteksi 
                      dan mengeksekusi fungsi updatedTanggalMcu() di backend untuk mencari dokter praktik secara real-time.
                    --}}
                    <input type="date" name="tanggal_mcu" id="tanggal_mcu" wire:model.live="tanggal_mcu" required
                        class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors shadow-sm">
                    @error('tanggal_mcu') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="dokter_id" class="block text-[10px] font-bold text-slate-700 mb-1">Dokter P. Jawab <span class="text-red-500">*</span></label>
                    {{-- Dropdown dokter tetap menggunakan wire:model standar agar Admin bisa merubah pilihan otomatis sistem jika dibutuhkan --}}
                    <select id="dokter_id" wire:model="dokter_id" required
                        class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer shadow-sm">
                        <option value="">-- Pilih Dokter --</option>
                        @foreach ($daftarDokter as $dokter)
                            <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap }} ({{ $dokter->spesialisasi }})</option>
                        @endforeach
                    </select>
                    @error('dokter_id') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="paket_mcus_id" class="block text-[10px] font-bold text-slate-700 mb-1">Paket MCU <span class="text-red-500">*</span></label>
                    <select id="paket_mcus_id" wire:model="paket_mcus_id" required
                        class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer shadow-sm">
                        <option value="">-- Pilih Paket --</option>
                        @foreach ($daftarPaket as $paket)
                            <option value="{{ $paket->id }}">{{ $paket->nama_paket }}</option>
                        @endforeach
                    </select>
                    @error('paket_mcus_id') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Mengoptimalkan rendering UI state saat interaksi klik --}}
        <div class="flex justify-end pt-2"> 
            <button type="submit" wire:loading.attr="disabled"
                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg shadow-md hover:-translate-y-0.5 transition-all text-xs active:scale-95">
                <span wire:loading.remove><i class="fas fa-save mr-1.5"></i> {{ $isEditMode ? 'Simpan Perubahan' : 'Buat Jadwal MCU' }}</span>
                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-1.5"></i> Memproses...</span>
            </button>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.2s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</form>