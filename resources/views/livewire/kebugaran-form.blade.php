<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="px-4 py-4 md:px-6 md:py-5 border-b border-slate-100 bg-red-50/50 flex items-center justify-between">
        <h3 class="text-base md:text-lg font-black text-red-800"><i class="fas fa-heartbeat text-red-500 mr-2"></i> Indeks Kebugaran Pasien</h3>
    </div>

    <div class="p-4 md:p-8">
        @if (session()->has('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 md:px-5 md:py-4 rounded-xl flex items-center shadow-sm mb-5 md:mb-6">
                <i class="fas fa-check-circle text-lg md:text-xl mr-3"></i><span class="font-medium text-sm md:text-base">{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 md:px-5 md:py-4 rounded-xl flex items-center shadow-sm mb-5 md:mb-6">
                <i class="fas fa-exclamation-triangle text-lg md:text-xl mr-3"></i><span class="font-medium text-sm md:text-base">{{ session('error') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="calculateAndSaveKebugaran">
            
            {{-- DATA DASAR --}}
            <div class="bg-slate-50 border border-slate-100 p-4 md:p-5 rounded-2xl mb-6 md:mb-8 flex flex-col md:flex-row gap-4 md:gap-6">
                <div class="flex-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Usia Pasien (Tahun)</label>
                    <input type="text" value="{{ $umur }}" readonly class="block w-full rounded-xl border-slate-200 bg-slate-100/50 font-bold text-slate-600 text-sm shadow-sm cursor-not-allowed">
                </div>
                <div class="flex-1">
                    <label class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Berat Badan (Kg)</label>
                    <input type="text" value="{{ $bb }}" readonly class="block w-full rounded-xl border-slate-200 bg-slate-100/50 font-bold text-slate-600 text-sm shadow-sm cursor-not-allowed">
                </div>
            </div>

            {{-- INPUT INDIKATOR --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Durasi Uji (Menit)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-stopwatch text-slate-400"></i></div>
                        <input type="number" wire:model.defer="durasi_menit" min="1" required placeholder="Contoh: 6"
                            class="block w-full pl-10 rounded-xl border-slate-300 bg-white text-sm font-bold focus:border-red-500 focus:ring-red-500 transition-colors shadow-sm py-2.5 md:py-3">
                    </div>
                    @error('durasi_menit') <p class="mt-1 text-[10px] md:text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Beban Latihan (Watt)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-dumbbell text-slate-400"></i></div>
                        <input type="number" wire:model.defer="beban_latihan" required placeholder="Contoh: 125"
                            class="block w-full pl-10 rounded-xl border-slate-300 bg-white text-sm font-bold focus:border-red-500 focus:ring-red-500 transition-colors shadow-sm py-2.5 md:py-3">
                    </div>
                    @error('beban_latihan') <p class="mt-1 text-[10px] md:text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Denyut Nadi (bpm)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-heart text-red-400"></i></div>
                        <input type="number" wire:model.defer="denyut_nadi" min="1" required placeholder="Contoh: 120"
                            class="block w-full pl-10 rounded-xl border-slate-300 bg-white text-sm font-bold focus:border-red-500 focus:ring-red-500 transition-colors shadow-sm py-2.5 md:py-3">
                    </div>
                    @error('denyut_nadi') <p class="mt-1 text-[10px] md:text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 md:px-8 py-3 md:py-3.5 bg-red-600 text-white rounded-xl font-black tracking-wide shadow-lg shadow-red-600/30 hover:bg-red-700 hover:-translate-y-0.5 transition-all duration-200 text-sm md:text-base" wire:loading.attr="disabled" wire:target="calculateAndSaveKebugaran">
                <span wire:loading.remove wire:target="calculateAndSaveKebugaran"><i class="fas fa-calculator mr-2"></i> Hitung & Simpan Kebugaran</span>
                <span wire:loading wire:target="calculateAndSaveKebugaran"><i class="fas fa-circle-notch fa-spin mr-2"></i> Kalkulasi Data Sistem...</span>
            </button>
        </form>

        {{-- HASIL PERHITUNGAN (BANNER) --}}
        @if($hasilKebugaran !== null)
            <div class="mt-8 md:mt-10 bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-5 md:p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 md:w-40 md:h-40 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 md:w-32 md:h-32 bg-red-500/20 rounded-full blur-2xl"></div>
                
                <h4 class="text-[10px] md:text-sm font-black text-slate-400 tracking-widest uppercase mb-4 md:mb-6 relative z-10 text-center lg:text-left">Summary Kebutuhan VO2 Max</h4>
                
                <div class="flex flex-col lg:flex-row items-center justify-between gap-6 md:gap-8 relative z-10">
                    
                    {{-- Boks Angka dan Kategori --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-8 w-full lg:w-auto">
                        <div class="text-center">
                            <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase mb-1">Skor VO2 Max</p>
                            <p class="text-4xl md:text-5xl font-black text-white">{{ number_format($hasilKebugaran, 2) }}</p>
                        </div>
                        
                        {{-- Garis Pemisah (Vertikal di PC, Horizontal di Mobile) --}}
                        <div class="h-16 w-px bg-slate-700 hidden sm:block"></div>
                        <div class="w-full h-px bg-slate-700 block sm:hidden my-2"></div>
                        
                        <div class="text-center sm:text-left">
                            <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase mb-1">Kategori Klinis</p>
                            <span class="inline-block px-4 py-1.5 md:px-5 md:py-2 rounded-xl text-sm md:text-lg font-black tracking-wide
                                @if($keterangan === 'Good') bg-emerald-500/20 text-emerald-400 border border-emerald-500/50
                                @elseif($keterangan === 'Average') bg-amber-500/20 text-amber-400 border border-amber-500/50
                                @else bg-red-500/20 text-red-400 border border-red-500/50 @endif">
                                {{ $keterangan }}
                            </span>
                        </div>
                    </div>

                    {{-- Tombol PDF --}}
                    @if($kebugaranDataId && isset($kebugaranResult->file_path))
                        <div class="w-full lg:w-auto mt-2 lg:mt-0">
                            <a href="{{ asset('storage/' . $kebugaranResult->file_path) }}" target="_blank"
                                class="flex items-center justify-center px-5 py-3 md:px-6 md:py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl text-sm md:text-base font-bold transition-all backdrop-blur-sm group w-full">
                                <i class="fas fa-file-pdf text-base md:text-lg mr-2 md:mr-3 group-hover:text-red-400 transition-colors"></i> 
                                Akses Laporan PDF 
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        @endif
    </div>
</div>