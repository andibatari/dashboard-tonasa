@section('title', 'Tambah Data Pemantauan')

<div class="px-3 md:px-6 py-4 md:py-6 min-h-screen">
    
    <div class="mb-4 md:mb-6">
        <a href="{{ route('pemantauan.index') }}" class="inline-flex items-center px-4 py-2 text-xs md:text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
    </div>

    <form wire:submit.prevent="simpanPemantauan" class="space-y-6 md:space-y-8">
        @if ($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r-lg shadow-sm mb-4 text-xs font-bold">
                <h5 class="mb-2 uppercase tracking-wider"><i class="fas fa-exclamation-circle text-rose-500 mr-1"></i> Data gagal disimpan karena:</h5>
                <ul class="list-disc pl-5 space-y-1 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- KARTU 1: SETUP LOKASI --}}
        {{-- PERBAIKAN CSS: "overflow-hidden" DIHAPUS agar dropdown tidak terpotong (squished) --}}
        <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center rounded-t-2xl md:rounded-t-[2rem]">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-sm"></i>
                </div>
                <h2 class="text-sm md:text-base font-black text-slate-800">1. Setup Lokasi & Batas NAB</h2>
            </div>

            <div class="p-5 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6 mb-6">
                    <div class="md:col-span-2 lg:col-span-1 relative z-20">
                        @livewire('searchable-departemen', ['initialDepartemenId' => $departemens_id, 'initialUnitKerjaId' => $unit_kerjas_id], key('departemen-unit-picker'))
                    </div>
                    
                    <div class="flex flex-col gap-5">
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Nama Area / Gedung <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="area" class="w-full rounded-xl border-slate-200 focus:border-blue-500 text-xs md:text-sm p-3 shadow-sm bg-slate-50 focus:bg-white transition-colors" placeholder="Cth: Gedung Diklat, Pabrik Tonasa 5...">
                        </div>
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Tanggal Pemantauan <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="tanggal_pemantauan" class="w-full rounded-xl border-slate-200 focus:border-blue-500 text-xs md:text-sm p-3 shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50/50 p-4 md:p-5 rounded-xl border border-amber-100">
                    <h4 class="text-[10px] md:text-xs font-black text-amber-800 uppercase tracking-widest mb-3 md:mb-4"><i class="fas fa-exclamation-triangle mr-1"></i> Standar Nilai Ambang Batas (NAB)</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach (['nabCahaya' => 'Cahaya', 'nabBising' => 'Bising', 'nabDebu' => 'Debu', 'nabSuhu' => 'Suhu'] as $model => $label)
                            <div>
                                <label class="block text-[9px] md:text-[10px] font-bold text-amber-700 uppercase mb-1.5">{{ $label }}</label>
                                <input type="{{ $model == 'nabDebu' ? 'text' : 'number' }}" step="0.01" wire:model="{{ $model }}" class="w-full rounded-lg border-amber-200 focus:border-amber-500 focus:ring-amber-500 text-xs md:text-sm p-2.5 bg-white shadow-sm">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU 2: INPUT TITIK PENGUKURAN --}}
        <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden p-5 md:p-8">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-6 md:mb-8 border-b border-slate-100 pb-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3 shrink-0">
                        <i class="fas fa-map-marker-alt text-sm"></i>
                    </div>
                    <h2 class="text-sm md:text-base font-black text-slate-800">2. Data Titik Pengukuran</h2>
                </div>
                <button type="button" wire:click="addLokasi" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white transition-colors font-bold text-[10px] md:text-xs rounded-xl border border-emerald-200 hover:border-emerald-600 shadow-sm">
                    <i class="fas fa-plus mr-1.5"></i> Tambah Titik Lokasi
                </button>
            </div>

            <div class="space-y-6 md:space-y-8">
                @foreach ($lokasiData as $index => $lokasiItem)
                <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 md:p-6 relative group transition-all hover:border-blue-200 hover:shadow-md">
                    @if(count($lokasiData) > 1)
                        <button type="button" wire:click="removeLokasi({{ $index }})" class="absolute -top-3 -right-3 w-8 h-8 bg-white border border-rose-200 text-rose-500 hover:bg-rose-500 hover:text-white rounded-full shadow-sm flex items-center justify-center transition-colors z-10" title="Hapus titik ini">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif

                    <div class="flex items-center gap-3 mb-5 md:mb-6">
                        <div class="w-6 h-6 md:w-8 md:h-8 bg-slate-800 text-white rounded-full flex items-center justify-center text-[10px] md:text-xs font-black shrink-0 shadow-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 max-w-md">
                            <input type="text" wire:model="lokasiData.{{ $index }}.lokasi" placeholder="Ketik nama spesifik ruangan/titik..." class="w-full border-0 border-b-2 border-slate-300 focus:border-blue-500 bg-transparent text-sm md:text-base font-bold text-slate-800 focus:ring-0 p-0 transition-colors pb-1" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-9 gap-3 md:gap-4 mb-4 md:mb-5">
                        @foreach (['cahaya'=>'Cahaya', 'bising'=>'Bising', 'debu'=>'Debu', 'suhu_basah'=>'S.Basah', 'suhu_kering'=>'S.Kering', 'suhu_radiasi'=>'S.Rad', 'isbb_indoor'=>'ISBB In', 'isbb_outdoor'=>'ISBB Out', 'rh'=>'RH%'] as $key => $label)
                            <div>
                                <label class="block text-[8px] md:text-[9px] font-bold text-slate-500 uppercase mb-1.5">{{ $label }}</label>
                                <input type="number" step="0.01" wire:model="lokasiData.{{ $index }}.pemantauan.{{ $key }}" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-blue-500 shadow-sm">
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-[9px] md:text-[10px] font-bold text-slate-500 uppercase mb-1.5">Kesimpulan Singkat / Rekomendasi (Opsional)</label>
                        <textarea rows="2" wire:model="lokasiData.{{ $index }}.kesimpulan" placeholder="Tuliskan catatan atau rekomendasi tindakan perbaikan di titik ini jika diperlukan..." class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-blue-500 shadow-sm resize-none"></textarea>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- FOOTER: TOMBOL SIMPAN --}}
        <div class="sticky bottom-6 z-40 bg-slate-900/90 backdrop-blur-md rounded-2xl p-4 md:p-5 flex flex-col md:flex-row justify-between items-center shadow-2xl border border-white/10 gap-3 md:gap-0">
            <div class="text-slate-300 text-[10px] md:text-xs font-medium text-center md:text-left">
                <i class="fas fa-shield-alt text-emerald-400 mr-1.5"></i> Pastikan semua data pengukuran telah terisi dengan benar.
            </div>
            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-red-600 text-white font-bold rounded-xl shadow-lg shadow-red-600/30 text-xs md:text-sm hover:bg-red-700 hover:-translate-y-0.5 transition-all">
                <span wire:loading.remove wire:target="simpanPemantauan"><i class="fas fa-cloud-upload-alt mr-2"></i> Simpan Laporan Pemantauan</span>
                <span wire:loading wire:target="simpanPemantauan"><i class="fas fa-spinner fa-spin mr-2"></i> Memproses Data...</span>
            </button>
        </div>
    </form>
</div>