<div class="space-y-6">
    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center shadow-sm">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center shadow-sm">
            <i class="fas fa-exclamation-circle text-xl mr-3"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif
    
    <form wire:submit.prevent="simpanHasil" class="space-y-6">
        
        {{-- DOKTER PEMERIKSA --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800"><i class="fas fa-user-md text-blue-500 mr-2"></i>Dokter Pemeriksa</h3>
            </div>
            <div class="p-6">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Dokter</label>
                <select wire:model="dokterId" class="block w-full md:w-1/2 rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors">
                    <option value="">-- Pilih Dokter Gigi --</option>
                    @isset($listDokter)
                        @foreach ($listDokter as $dokter)
                            <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap ?? $dokter->name }}</option>
                        @endforeach
                    @endisset
                </select>
                @error('dokterId') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- 1. EKSTRA ORAL --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800">1. Pemeriksaan Ekstra Oral</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kelenjar Submandibular</label>
                    <input type="text" wire:model.defer="dataForm.ekstraOral.kelenjar_submandibular" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors" placeholder="Contoh: NORMAL">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kelenjar Leher</label>
                    <input type="text" wire:model.defer="dataForm.ekstraOral.kelenjar_leher" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors" placeholder="Contoh: NORMAL">
                </div>
            </div>
        </div>

        {{-- 2. INTRA ORAL --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800">2. Pemeriksaan Intra Oral</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    {{-- 🌟 PERBAIKAN: Form diubah agar sama persis dengan form Intra Oral di gambar PDF STMC --}}
                    
                    {{-- Baris 1 Kiri --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mukosa Pipi</label>
                        <select wire:model.defer="dataForm.intraOral.mukosa_pipi" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors cursor-pointer">
                            <option value="NORMAL">NORMAL</option>
                            <option value="TIDAK NORMAL">TIDAK NORMAL</option>
                        </select>
                    </div>
                    
                    {{-- Baris 1 Kanan --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ginggiva RA</label>
                        <select wire:model.defer="dataForm.intraOral.ginggiva_ra" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors cursor-pointer">
                            <option value="TIDAK ADA">TIDAK ADA</option>
                            <option value="ADA">ADA</option>
                        </select>
                    </div>

                    {{-- Baris 2 Kiri --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Palatum</label>
                        <select wire:model.defer="dataForm.intraOral.palatum" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors cursor-pointer">
                            <option value="SEDANG">SEDANG</option>
                            <option value="DALAM">DALAM</option>
                            <option value="RENDAH">RENDAH</option>
                        </select>
                    </div>

                    {{-- Baris 2 Kanan --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ginggiva RB</label>
                        <select wire:model.defer="dataForm.intraOral.ginggiva_rb" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors cursor-pointer">
                            <option value="TIDAK ADA">TIDAK ADA</option>
                            <option value="ADA">ADA</option>
                        </select>
                    </div>

                    {{-- Baris 3 Kiri --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mukosa Mulut</label>
                        <select wire:model.defer="dataForm.intraOral.mukosa_mulut" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors cursor-pointer">
                            <option value="NORMAL">NORMAL</option>
                            <option value="TIDAK NORMAL">TIDAK NORMAL</option>
                        </select>
                    </div>

                    {{-- Baris 3 Kanan --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Karang Gigi</label>
                        <input type="text" wire:model.defer="dataForm.intraOral.karang_gigi" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors" placeholder="(Kosongkan jika tidak ada)">
                    </div>

                    {{-- Baris 4 Kiri --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Lidah</label>
                        <select wire:model.defer="dataForm.intraOral.lidah" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors cursor-pointer">
                            <option value="NORMAL">NORMAL</option>
                            <option value="KOTOR">KOTOR</option>
                        </select>
                    </div>

                    {{-- Baris 4 Kanan --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pocket</label>
                        <select wire:model.defer="dataForm.intraOral.pocket" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors cursor-pointer">
                            <option value="TIDAK ADA">TIDAK ADA</option>
                            <option value="ADA">ADA</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- PETA GIGI --}}
        <div class="bg-blue-50 rounded-2xl border border-blue-100 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-blue-200 bg-blue-100/50 flex flex-col sm:flex-row justify-between sm:items-center">
                <h3 class="text-base font-bold text-blue-900">2.1. Peta Gigi Interaktif</h3>
                <p class="text-xs font-semibold text-blue-600 bg-blue-100 px-3 py-1 rounded-full mt-2 sm:mt-0">Klik pada gigi untuk mengubah status</p>
            </div>
            
            <div class="p-6 flex flex-col items-center">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-6 w-full max-w-4xl overflow-x-auto">
                    <table class="w-full border-collapse min-w-[600px]">
                        <thead class="text-center font-bold text-slate-400">
                            <tr>
                                @for ($i = 8; $i >= 1; $i--) <td class="py-2 px-1 text-xs">{{ $i }}</td> @endfor
                                <td class="w-2"></td> {{-- Spacer Garis Tengah --}}
                                @for ($i = 1; $i <= 8; $i++) <td class="py-2 px-1 text-xs">{{ $i }}</td> @endfor
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- Kuadran 1 (Atas Kanan) --}}
                                @for ($i = 8; $i >= 1; $i--)
                                    @php $gigiId = '1' . $i; $status = $gigiKlinis[$gigiId] ?? 'Normal'; @endphp
                                    <td class="p-1">
                                        <div id="gigi-{{ $gigiId }}" wire:click.prevent="toggleGigiKlinis('{{ $gigiId }}')" 
                                            class="relative w-full h-12 flex items-center justify-center border-2 rounded-lg transition duration-200 cursor-pointer shadow-sm
                                                @if($status === 'Caries') bg-red-600 border-red-700 text-white 
                                                @elseif($status === 'Missing') bg-slate-500 border-slate-600 text-white 
                                                @elseif($status === 'Tambal') bg-emerald-500 border-emerald-600 text-white
                                                @else bg-slate-50 border-slate-300 hover:bg-blue-100 hover:border-blue-400 text-slate-700 @endif">
                                            
                                            <span class="text-[10px] font-bold z-10 opacity-50 absolute top-1 left-1.5">{{ $gigiId }}</span>
                                            
                                            @if($status === 'Caries') <span class="text-xl font-black z-20">O</span>
                                            @elseif($status === 'Missing') <span class="text-xl font-black z-20">X</span>
                                            @elseif($status === 'Tambal') <span class="text-lg font-black z-20">T</span>
                                            @endif
                                        </div>
                                    </td>
                                @endfor
                                
                                <td class="w-2 bg-blue-900 rounded-full"></td> {{-- Garis Tengah Vertikal (Salib) --}}
                                
                                {{-- Kuadran 2 (Atas Kiri) --}}
                                @for ($i = 1; $i <= 8; $i++)
                                    @php $gigiId = '2' . $i; $status = $gigiKlinis[$gigiId] ?? 'Normal'; @endphp
                                    <td class="p-1">
                                        <div id="gigi-{{ $gigiId }}" wire:click.prevent="toggleGigiKlinis('{{ $gigiId }}')" 
                                            class="relative w-full h-12 flex items-center justify-center border-2 rounded-lg transition duration-200 cursor-pointer shadow-sm
                                                @if($status === 'Caries') bg-red-600 border-red-700 text-white 
                                                @elseif($status === 'Missing') bg-slate-500 border-slate-600 text-white 
                                                @elseif($status === 'Tambal') bg-emerald-500 border-emerald-600 text-white
                                                @else bg-slate-50 border-slate-300 hover:bg-blue-100 hover:border-blue-400 text-slate-700 @endif">
                                            
                                            <span class="text-[10px] font-bold z-10 opacity-50 absolute top-1 right-1.5">{{ $gigiId }}</span>
                                            
                                            @if($status === 'Caries') <span class="text-xl font-black z-20">O</span>
                                            @elseif($status === 'Missing') <span class="text-xl font-black z-20">X</span>
                                            @elseif($status === 'Tambal') <span class="text-lg font-black z-20">T</span>
                                            @endif
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                            
                            {{-- Garis Pembatas Horizontal (Salib) --}}
                            <tr><td colspan="17" class="py-2"><div class="h-1.5 w-full bg-blue-900 rounded-full"></div></td></tr>
                            
                            <tr>
                                {{-- Kuadran 4 (Bawah Kanan) --}}
                                @for ($i = 8; $i >= 1; $i--)
                                    @php $gigiId = '4' . $i; $status = $gigiKlinis[$gigiId] ?? 'Normal'; @endphp
                                    <td class="p-1">
                                        <div id="gigi-{{ $gigiId }}" wire:click.prevent="toggleGigiKlinis('{{ $gigiId }}')" 
                                            class="relative w-full h-12 flex items-center justify-center border-2 rounded-lg transition duration-200 cursor-pointer shadow-sm
                                                @if($status === 'Caries') bg-red-600 border-red-700 text-white 
                                                @elseif($status === 'Missing') bg-slate-500 border-slate-600 text-white 
                                                @elseif($status === 'Tambal') bg-emerald-500 border-emerald-600 text-white
                                                @else bg-slate-50 border-slate-300 hover:bg-blue-100 hover:border-blue-400 text-slate-700 @endif">
                                            
                                            <span class="text-[10px] font-bold z-10 opacity-50 absolute bottom-1 left-1.5">{{ $gigiId }}</span>
                                            
                                            @if($status === 'Caries') <span class="text-xl font-black z-20">O</span>
                                            @elseif($status === 'Missing') <span class="text-xl font-black z-20">X</span>
                                            @elseif($status === 'Tambal') <span class="text-lg font-black z-20">T</span>
                                            @endif
                                        </div>
                                    </td>
                                @endfor
                                
                                <td class="w-2 bg-blue-900 rounded-full"></td> {{-- Garis Tengah Vertikal --}}
                                
                                {{-- Kuadran 3 (Bawah Kiri) --}}
                                @for ($i = 1; $i <= 8; $i++)
                                    @php $gigiId = '3' . $i; $status = $gigiKlinis[$gigiId] ?? 'Normal'; @endphp
                                    <td class="p-1">
                                        <div id="gigi-{{ $gigiId }}" wire:click.prevent="toggleGigiKlinis('{{ $gigiId }}')" 
                                            class="relative w-full h-12 flex items-center justify-center border-2 rounded-lg transition duration-200 cursor-pointer shadow-sm
                                                @if($status === 'Caries') bg-red-600 border-red-700 text-white 
                                                @elseif($status === 'Missing') bg-slate-500 border-slate-600 text-white 
                                                @elseif($status === 'Tambal') bg-emerald-500 border-emerald-600 text-white
                                                @else bg-slate-50 border-slate-300 hover:bg-blue-100 hover:border-blue-400 text-slate-700 @endif">
                                            
                                            <span class="text-[10px] font-bold z-10 opacity-50 absolute bottom-1 right-1.5">{{ $gigiId }}</span>
                                            
                                            @if($status === 'Caries') <span class="text-xl font-black z-20">O</span>
                                            @elseif($status === 'Missing') <span class="text-xl font-black z-20">X</span>
                                            @elseif($status === 'Tambal') <span class="text-lg font-black z-20">T</span>
                                            @endif
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap justify-center gap-4 text-xs font-bold">
                    <div class="flex items-center bg-white px-3 py-2 rounded-xl shadow-sm border border-slate-200">
                        <span class="w-5 h-5 bg-slate-100 border border-slate-300 rounded mr-2"></span> <span class="text-slate-600">Sehat (Normal)</span>
                    </div>
                    <div class="flex items-center bg-white px-3 py-2 rounded-xl shadow-sm border border-slate-200">
                        <span class="flex items-center justify-center w-5 h-5 bg-red-600 border border-red-700 text-white rounded mr-2 text-[12px]">O</span> <span class="text-red-700">Berlubang (Caries)</span>
                    </div>
                    <div class="flex items-center bg-white px-3 py-2 rounded-xl shadow-sm border border-slate-200">
                        <span class="flex items-center justify-center w-5 h-5 bg-slate-500 border border-slate-600 text-white rounded mr-2 text-[12px]">X</span> <span class="text-slate-700">Hilang / Cabut</span>
                    </div>
                    <div class="flex items-center bg-white px-3 py-2 rounded-xl shadow-sm border border-slate-200">
                        <span class="flex items-center justify-center w-5 h-5 bg-emerald-500 border border-emerald-600 text-white rounded mr-2 text-[12px]">T</span> <span class="text-emerald-700">Ditambal</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3 & 4. KESIMPULAN --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-base font-bold text-slate-800">3. Keterangan Hasil Pemeriksaan</h3>
                </div>
                <div class="p-6">
                    <textarea wire:model.defer="keterangan" rows="3" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors placeholder-slate-300 resize-none" placeholder="Contoh: 1 GIGI HILANG"></textarea>
                </div>
            </div>

            <div class="bg-amber-50 rounded-2xl border border-amber-200 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-amber-200/50 bg-amber-100/50">
                    <h3 class="text-base font-bold text-amber-900">4. Kesimpulan</h3>
                </div>
                <div class="p-6">
                    <input type="text" wire:model.defer="kesimpulan" class="block w-full rounded-xl border-amber-300 bg-white text-sm font-bold text-amber-900 focus:border-amber-500 focus:ring-amber-500 transition-colors placeholder-amber-200" placeholder="Contoh: OHIS BAIK">
                </div>
            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="flex flex-col sm:flex-row justify-end gap-4 pt-4 border-t border-slate-200">
            @if ($poliGigiResult && $poliGigiResult->file_path)
                <a href="{{ asset('storage/' . $poliGigiResult->file_path) }}" target="_blank"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-emerald-500 text-emerald-600 font-bold rounded-xl shadow-sm hover:bg-emerald-50 transition-all duration-200">
                    <i class="fas fa-file-pdf mr-2"></i> Laporan PDF
                </a>
            @endif
            
            <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                <span wire:loading.remove wire:target="simpanHasil"><i class="fas fa-save mr-2"></i> Simpan Form Gigi</span>
                <span wire:loading wire:target="simpanHasil"><i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>