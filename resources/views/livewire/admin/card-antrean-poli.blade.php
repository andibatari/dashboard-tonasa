<div> {{-- ROOT ELEMENT LIVEWIRE WAJIB DI SINI --}}
    <div wire:poll.3s class="flex overflow-x-auto gap-5 pb-6 pt-2 snap-x hide-scrollbar custom-scrollbar-horizontal">
        @forelse ($polis as $poli)
            @if ($poli->jadwalPoli->count() > 0)
                {{-- Card Poli Modern --}}
                <div class="flex-none w-72 bg-white border border-slate-100 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] snap-start overflow-hidden flex flex-col relative group hover:-translate-y-1 transition-transform duration-300">
                    
                    {{-- Header Gradasi Red-Crimson --}}
                    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white px-5 py-4 flex justify-between items-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-5 rounded-full -mr-10 -mt-10"></div>
                        <h3 class="font-black text-sm uppercase tracking-wider truncate z-10">{{ $poli->nama_poli }}</h3>
                        <div class="bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xs font-black px-3 py-1 rounded-full shadow-sm z-10">
                            {{ $poli->jadwalPoli->count() }} Antre
                        </div>
                    </div>
                    
                    {{-- Daftar Pasien Antre --}}
                    <div class="p-3 max-h-52 overflow-y-auto custom-scrollbar bg-slate-50/50 flex-1 space-y-2">
                        @foreach ($poli->jadwalPoli as $index => $antrean)
                            @php
                                $jadwal = $antrean->jadwalMcu;
                                $namaPasien = $jadwal->karyawan->nama_karyawan ?? $jadwal->pesertaMcu->nama_lengkap ?? $jadwal->nama_pasien ?? '-';
                                $isCalling = $antrean->status === 'Calling';
                            @endphp
                            
                            <a href="{{ route('qr-patient-detail', $jadwal->id) }}?tab=poli-{{ $poli->id }}" 
                               class="flex items-center p-2.5 rounded-2xl border shadow-sm transition-all duration-200 group/item block cursor-pointer 
                               {{ $isCalling ? 'bg-amber-50 border-amber-200 hover:bg-amber-100' : 'bg-white border-slate-100 hover:border-red-200 hover:shadow-md' }}">
                                
                                {{-- Nomor Urut Melingkar --}}
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-black mr-3 shadow-inner transition-colors 
                                    {{ $isCalling ? 'bg-amber-400 text-white shadow-amber-500/50' : 'bg-slate-100 text-slate-500 group-hover/item:bg-red-100 group-hover/item:text-red-600' }}">
                                    {{ $index + 1 }}
                                </div>
                                
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-slate-800 truncate group-hover/item:text-red-700 transition-colors {{ $isCalling ? 'text-amber-900' : '' }}">{{ $namaPasien }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate mt-0.5">ID: {{ $jadwal->no_sap ?? $jadwal->nik_pasien ?? 'Umum' }}</p>
                                </div>

                                @if($isCalling)
                                    <div class="flex-shrink-0 ml-2 relative flex items-center justify-center w-6 h-6" title="Sedang diperiksa di ruangan">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500 shadow-sm"></span>
                                    </div>
                                @else
                                    <i class="fas fa-chevron-right text-[10px] text-slate-300 opacity-0 group-hover/item:opacity-100 transition-opacity ml-2"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <div class="w-full p-6 bg-slate-50 text-slate-400 text-sm font-bold text-center rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center">
                <i class="fas fa-bed text-3xl mb-3 text-slate-300"></i> Tidak ada antrean terdaftar di poli manapun.
            </div>
        @endforelse

        @if ($polis->sum(fn($p) => $p->jadwalPoli->count()) === 0 && $polis->count() > 0)
            <div class="w-full p-8 bg-emerald-50 text-emerald-600 text-sm font-bold text-center rounded-3xl border border-emerald-100 shadow-sm flex flex-col items-center justify-center">
                <i class="fas fa-clipboard-check text-4xl mb-3 text-emerald-400"></i>
                <p>Ruang tunggu poli kosong, semua pasien telah tertangani.</p>
            </div>
        @endif
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar-horizontal::-webkit-scrollbar { height: 6px; }
        .custom-scrollbar-horizontal::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar-horizontal::-webkit-scrollbar-track { background: transparent; }
    </style>
</div>