<div class="space-y-6">
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
                <h3 class="text-base font-bold text-slate-800"><i class="fas fa-user-md text-blue-500 mr-2"></i>Dokter Pemeriksa Poli Mata</h3>
            </div>
            <div class="p-6">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Dokter</label>
                <select wire:model.defer="dokterId" class="block w-full md:w-1/2 rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500">
                    <option value="">-- Pilih Dokter --</option>
                    @foreach ($listDokter as $dokter)
                        <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap ?? $dokter->name }}</option>
                    @endforeach
                </select>
                @error('dokterId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- FORM PEMERIKSAAN MATA (100% SESUAI GAMBAR) --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800"><i class="fas fa-eye text-blue-500 mr-2"></i>Hasil Pemeriksaan Refraksi & Mata</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
                    
                    {{-- Baris 1: Visus --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2">Visus Kanan (VOD)</label>
                        <input type="text" wire:model.defer="dataMata.visus_kanan" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:border-blue-500">
                        @error('dataMata.visus_kanan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2">Visus Kiri (VOS)</label>
                        <input type="text" wire:model.defer="dataMata.visus_kiri" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:border-blue-500">
                        @error('dataMata.visus_kiri') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Baris 2: ADD & PD --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2">Addition (ADD)</label>
                        <input type="text" wire:model.defer="dataMata.add" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2">Pupillary Distance (PD)</label>
                        <input type="text" wire:model.defer="dataMata.pd" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:border-blue-500">
                    </div>

                </div>
            </div>
        </div>

        {{-- DIAGNOSA & KESIMPULAN --}}
        <div class="bg-amber-50 rounded-2xl border border-amber-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-amber-200/50 bg-amber-100/50">
                <h3 class="text-base font-bold text-amber-900">Kesimpulan Mata</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-amber-800 mb-2">Kesimpulan Pemeriksaan</label>
                    <textarea wire:model.defer="kesimpulan" rows="3" class="w-full rounded-xl border-amber-300 bg-white text-sm focus:border-amber-500 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-amber-800 mb-2">Saran & Keterangan</label>
                    <textarea wire:model.defer="keterangan" rows="3" class="w-full rounded-xl border-amber-300 bg-white text-sm focus:border-amber-500 resize-none"></textarea>
                </div>
            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="flex flex-col sm:flex-row justify-end gap-4 pt-4 border-t border-slate-200">
            
            {{-- Tombol Lihat PDF (Menggunakan helper asset agar URL menyertakan folder /storage/) --}}
            @if($mataResult && $mataResult->file_path)
                <a href="{{ asset('storage/' . $mataResult->file_path) }}" target="_blank"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-emerald-500 text-emerald-600 font-bold rounded-xl shadow-sm hover:bg-emerald-50 transition-all duration-200">
                    <i class="fas fa-file-pdf mr-2"></i> Lihat Laporan PDF
                </a>
            @endif
            
            <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                <span wire:loading.remove wire:target="simpanHasil"><i class="fas fa-save mr-2"></i> Simpan Pemeriksaan Mata</span>
                <span wire:loading wire:target="simpanHasil"><i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan Data...</span>
            </button>
        </div>
    </form>
</div>