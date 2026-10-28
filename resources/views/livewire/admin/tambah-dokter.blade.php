@section('title', 'Manajemen Dokter')

<div class="px-4 sm:px-6 py-6 min-h-screen bg-slate-50/50">
    
    {{-- KARTU FORM REGISTRASI/EDIT (LEBIH COMPACT) --}}
    <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/40 border border-slate-100 p-5 sm:p-7 md:p-8 max-w-5xl mx-auto mb-6 md:mb-8 relative overflow-hidden">
        
        {{-- Dekorasi Latar Belakang (Biru Samar) --}}
        <div class="absolute top-0 right-0 -mt-6 -mr-6 w-32 h-32 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-full blur-3xl opacity-70 pointer-events-none"></div>

        <h2 class="text-lg md:text-xl lg:text-2xl font-black text-slate-800 mb-5 md:mb-6 flex items-center gap-3 relative z-10">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                <i class="fas fa-user-md text-lg md:text-xl"></i>
            </div>
            <div>
                <span class="block leading-tight">{{ $isEditing ? 'Edit Profil Dokter' : 'Registrasi Dokter Baru' }}</span>
                <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-0.5 tracking-wide">Kelola data master tenaga medis STMC Health</p>
            </div>
        </h2>

        @if (session()->has('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl flex items-center shadow-sm mb-6 animate-fade-in font-bold text-xs md:text-sm">
                <div class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mr-2.5 shrink-0"><i class="fas fa-check text-xs"></i></div>
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}" class="relative z-10">
            
            {{-- Blok Input Utama --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 md:p-6 mb-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
                    
                    {{-- Nama Lengkap --}}
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-user text-slate-400 text-xs"></i></div>
                            <input type="text" wire:model="nama_lengkap" placeholder="Contoh: dr. Ahmad, Sp.PD" class="block w-full pl-10 pr-3 py-2.5 md:py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all shadow-sm" required>
                        </div>
                        @error('nama_lengkap') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- NIK --}}
                    <div>
                        <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">NIK (KTP)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-id-card text-slate-400 text-xs"></i></div>
                            <input type="number" wire:model="nik" placeholder="16 Digit NIK" class="block w-full pl-10 pr-3 py-2.5 text-xs md:text-sm font-medium font-mono rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all shadow-sm">
                        </div>
                        @error('nik') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- Spesialisasi --}}
                    <div>
                        <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Spesialisasi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-stethoscope text-slate-400 text-xs"></i></div>
                            <select wire:model="spesialisasi" class="block w-full pl-10 pr-8 py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all cursor-pointer shadow-sm appearance-none">
                                <option value="">Pilih Spesialisasi</option>
                                @foreach ($daftarSpesialisasi as $spes) <option value="{{ $spes }}">{{ $spes }}</option> @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"><i class="fas fa-chevron-down text-slate-400 text-[10px]"></i></div>
                        </div>
                        @error('spesialisasi') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                        <div class="relative">
                            <input type="date" wire:model="tanggal_lahir" class="block w-full px-3 py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all shadow-sm">
                        </div>
                    </div>
                    
                    {{-- Golongan Darah --}}
                    <div>
                        <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Gol. Darah</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-tint text-red-400 text-xs"></i></div>
                            <select wire:model="golongan_darah" class="block w-full pl-10 pr-8 py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all cursor-pointer shadow-sm appearance-none">
                                <option value="">Pilih</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"><i class="fas fa-chevron-down text-slate-400 text-[10px]"></i></div>
                        </div>
                    </div>

                    {{-- No Handphone --}}
                    <div>
                        <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">No Handphone</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-phone-alt text-slate-400 text-xs"></i></div>
                            <input type="tel" wire:model="no_hp" placeholder="08xx..." class="block w-full pl-10 pr-3 py-2.5 text-xs md:text-sm font-medium font-mono rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all shadow-sm">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Email Kontak <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-envelope text-slate-400 text-xs"></i></div>
                            <input type="email" wire:model="email" placeholder="dokter@klinik.com" class="block w-full pl-10 pr-3 py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all shadow-sm" required>
                        </div>
                        @error('email') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-2.5 md:gap-3">
                @if ($isEditing)
                    <button type="button" wire:click="cancelEdit" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-lg hover:bg-slate-50 transition-all text-xs shadow-sm">Batalkan Edit</button>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg shadow-md hover:-translate-y-0.5 transition-all text-xs">Update Profil</button>
                @else
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-lg shadow-md shadow-blue-500/20 hover:-translate-y-0.5 transition-all text-xs">
                        <i class="fas fa-save mr-1.5"></i> Simpan Dokter
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- KARTU TABEL DOKTER (LEBIH COMPACT) --}}
    <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/40 border border-slate-100 p-5 sm:p-7 md:p-8 max-w-5xl mx-auto overflow-hidden">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 border-b border-slate-100 pb-4">
            <div>
                <h4 class="text-base md:text-lg font-black text-slate-800 flex items-center"><i class="fas fa-clipboard-list text-blue-500 mr-2.5"></i> Direktori Dokter</h4>
                <p class="text-[10px] md:text-xs text-slate-500 font-medium mt-0.5">Daftar tenaga medis yang terdaftar di sistem MCU.</p>
            </div>
        </div>
        
        {{-- Tampilan Desktop (Tabel) --}}
        <div class="hidden md:block overflow-x-auto rounded-xl border border-slate-100 bg-white">
            <table class="min-w-full text-left">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest w-5/12">Profil Dokter</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest w-4/12">Informasi Kontak</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center w-3/12">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($this->dokterUsers as $dokter)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                {{-- Avatar Inisial Diperkecil --}}
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black text-base shadow-inner shrink-0 group-hover:bg-blue-100 transition-colors">
                                    {{ strtoupper(substr($dokter->nama_lengkap, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-black text-[13px] text-slate-800">{{ $dokter->nama_lengkap }}</p>
                                    <div class="flex items-center flex-wrap gap-1.5 mt-1">
                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[9px] font-bold border border-blue-100">{{ $dokter->spesialisasi }}</span>
                                        @if($dokter->nik)
                                            <span class="text-[9px] font-mono text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">NIK: {{ $dokter->nik }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="space-y-1">
                                <p class="text-[11px] font-medium text-slate-600 flex items-center"><span class="w-4 text-center inline-block mr-1"><i class="fas fa-envelope text-slate-400"></i></span> {{ $dokter->email }}</p>
                                <p class="text-[11px] font-medium text-slate-600 flex items-center"><span class="w-4 text-center inline-block mr-1"><i class="fas fa-phone-alt text-slate-400"></i></span> {{ $dokter->no_hp ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex justify-center gap-1.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit({{ $dokter->id }})" title="Edit Profil" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-blue-500 hover:text-white hover:border-blue-500 transition-all shadow-sm"><i class="fas fa-pen text-[10px]"></i></button>
                                <button wire:click="delete({{ $dokter->id }})" title="Hapus Dokter" onclick="return confirm('Hapus profil dokter ini dari sistem?')" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all shadow-sm"><i class="fas fa-trash text-[10px]"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-10 text-center">
                            <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-2"><i class="fas fa-user-slash text-xl"></i></div>
                            <p class="text-slate-500 font-bold text-[13px]">Belum ada dokter terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Tampilan Mobile (Cards Layout) --}}
        <div class="md:hidden space-y-3">
            @forelse ($this->dokterUsers as $dokter)
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-blue-400 to-indigo-500"></div>
                
                <div class="flex items-start gap-3 mb-4 pl-1">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black text-base shadow-inner shrink-0">
                        {{ strtoupper(substr($dokter->nama_lengkap, 0, 1)) }}
                    </div>
                    <div class="pr-2 flex-1">
                        <p class="font-black text-slate-800 text-[13px] leading-snug">{{ $dokter->nama_lengkap }}</p>
                        <div class="mt-1">
                            <span class="inline-block px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 text-[9px] font-bold border border-blue-100">{{ $dokter->spesialisasi }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="pl-1">
                    <div class="text-[10px] text-slate-600 font-medium space-y-1.5 mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        @if($dokter->nik)
                            <p class="flex items-center"><span class="w-4 inline-block"><i class="fas fa-id-card text-slate-400"></i></span> <span class="font-mono">{{ $dokter->nik }}</span></p>
                        @endif
                        <p class="flex items-center"><span class="w-4 inline-block"><i class="fas fa-envelope text-slate-400"></i></span> <span class="truncate">{{ $dokter->email }}</span></p>
                        <p class="flex items-center"><span class="w-4 inline-block"><i class="fas fa-phone-alt text-slate-400"></i></span> {{ $dokter->no_hp ?? '-' }}</p>
                    </div>
                    
                    <div class="flex gap-2">
                        <button wire:click="edit({{ $dokter->id }})" class="flex-1 bg-white border border-slate-200 py-2 rounded-lg text-[11px] font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm"><i class="fas fa-pen mr-1.5 text-blue-500"></i> Edit</button>
                        <button wire:click="delete({{ $dokter->id }})" onclick="return confirm('Hapus profil dokter ini?')" class="flex-1 bg-red-50 text-red-600 border border-red-100 py-2 rounded-lg text-[11px] font-bold hover:bg-red-100 transition-colors"><i class="fas fa-trash mr-1.5"></i> Hapus</button>
                    </div>
                </div>
            </div>
            @empty
                <div class="text-center py-8 text-slate-400 bg-slate-50 rounded-2xl border border-slate-100 border-dashed">
                    <i class="fas fa-user-md text-2xl mb-2 opacity-40"></i>
                    <p class="text-[11px] font-bold text-slate-500">Belum ada dokter.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-5 pt-3 border-t border-slate-100">
            {{ $this->dokterUsers->links() }}
        </div>
    </div>
</div>