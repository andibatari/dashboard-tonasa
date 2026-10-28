<div> 
    @section('title', 'Manajemen Admin & Sistem')

    <div class="px-4 sm:px-6 py-6 min-h-screen bg-slate-50/50">
        
        {{-- KARTU FORM REGISTRASI/EDIT (COMPACT & ELEGAN) --}}
        <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/40 border border-slate-100 p-5 sm:p-7 md:p-8 max-w-5xl mx-auto mb-6 md:mb-8 relative overflow-hidden">
            
            {{-- Dekorasi Latar Belakang (Biru Samar) --}}
            <div class="absolute top-0 right-0 -mt-6 -mr-6 w-32 h-32 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-full blur-3xl opacity-70 pointer-events-none"></div>

            <h2 class="text-lg md:text-xl lg:text-2xl font-black text-slate-800 mb-5 md:mb-6 flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-slate-700 to-slate-900 text-white rounded-xl flex items-center justify-center shadow-md shadow-slate-500/20 shrink-0">
                    <i class="fas fa-shield-alt text-lg md:text-xl"></i>
                </div>
                <div>
                    <span class="block leading-tight">{{ $isEditing ? 'Edit Akun Administrator' : 'Registrasi Akun Sistem' }}</span>
                    <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-0.5 tracking-wide">Kelola hak akses dan pengguna sistem STMC</p>
                </div>
            </h2>

            @if (session()->has('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl flex items-center shadow-sm mb-6 animate-fade-in font-bold text-xs md:text-sm">
                    <div class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mr-2.5 shrink-0"><i class="fas fa-check text-xs"></i></div>
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-xl flex items-center shadow-sm mb-6 animate-fade-in font-bold text-xs md:text-sm">
                    <div class="w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center mr-2.5 shrink-0"><i class="fas fa-exclamation text-xs"></i></div>
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}" class="relative z-10">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 md:p-6 mb-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
                        
                        {{-- 🌟 1. DROPDOWN SUMBER PERSONEL --}}
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Sumber Data Personel</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-database text-slate-400 text-xs"></i></div>
                                <select wire:model.live="tipe_personel" class="block w-full pl-10 pr-8 py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all cursor-pointer shadow-sm appearance-none" {{ $isEditing ? 'disabled' : '' }}>
                                    <option value="manual">Admin Luar (Ketik Manual)</option>
                                    <option value="karyawan">Karyawan PT. Semen Tonasa</option>
                                    <option value="dokter">Dokter Pemeriksa (Klinik)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"><i class="fas fa-chevron-down text-slate-400 text-[10px]"></i></div>
                            </div>
                        </div>

                        {{-- 🌟 2. DROPDOWN HAK AKSES/ROLE --}}
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Hak Akses Sistem (Role)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-user-shield text-slate-400 text-xs"></i></div>
                                <select wire:model.live="role" class="block w-full pl-10 pr-8 py-2.5 text-xs md:text-sm font-bold rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 text-blue-700 transition-all cursor-pointer shadow-sm appearance-none">
                                    <option value="superadmin">Superadmin (Akses Penuh)</option>
                                    <option value="admin">Administrator Biasa</option>
                                    <option value="dokter">Dokter</option>
                                    <option value="karyawan">Karyawan</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"><i class="fas fa-chevron-down text-slate-400 text-[10px]"></i></div>
                            </div>
                        </div>

                        {{-- 🌟 3. BLOK PENCARIAN/INPUT (Dinamis) --}}
                        <div class="sm:col-span-2 relative">
                            <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                {{ $tipe_personel === 'manual' ? 'Nama Lengkap' : 'Pencarian & Penautan Akun' }}
                            </label>
                            
                            @if ($tipe_personel === 'dokter')
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-user-md text-slate-400 text-xs"></i></div>
                                    <select wire:model.live="selectedDokterId" class="block w-full pl-10 pr-8 py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:border-blue-500 cursor-pointer shadow-sm appearance-none" {{ $isEditing ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Dokter Terdaftar --</option>
                                        @foreach ($listDokter as $id => $nama) <option value="{{ $id }}">{{ $nama }}</option> @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"><i class="fas fa-chevron-down text-slate-400 text-[10px]"></i></div>
                                </div>
                            @elseif ($tipe_personel === 'karyawan')
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-search text-slate-400 text-xs"></i></div>
                                    <input wire:key="input-search-karyawan" type="text" wire:model.live.debounce.500ms="searchQuery" placeholder="Cari by NIK, SAP, atau Nama..." class="block w-full pl-10 pr-3 py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all shadow-sm" {{ $isEditing ? 'disabled' : '' }}>
                                </div>
                                
                                @if (!empty($searchQuery) && count($searchedKaryawans) > 0 && !$karyawanFound)
                                    <div wire:key="dropdown-search-karyawan" class="absolute z-[100] w-full bg-white border border-slate-200 mt-1.5 rounded-xl shadow-xl max-h-60 overflow-y-auto divide-y divide-slate-50">
                                        @foreach ($searchedKaryawans as $k)
                                            <p wire:key="item-karyawan-{{ $k->id }}" wire:click="selectKaryawan({{ $k->id }})" class="p-3.5 cursor-pointer hover:bg-blue-50 hover:text-blue-700 text-xs md:text-sm font-bold text-slate-700 transition-colors">
                                                <span class="font-mono text-[11px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 mr-2">{{ $k->no_sap }}</span> {{ $k->nama_karyawan }}
                                            </p>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-user text-slate-400 text-xs"></i></div>
                                    <input type="text" wire:model="nama_lengkap" placeholder="Ketik nama admin baru..." class="block w-full pl-10 pr-3 py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all shadow-sm">
                                </div>
                            @endif

                            @if ($karyawanFound)
                                 <p class="text-[10px] md:text-[11px] font-bold text-emerald-600 mt-1.5 bg-emerald-50 p-1.5 rounded inline-block"><i class="fas fa-check-circle mr-1"></i> Ditautkan: {{ $nama_lengkap }}</p>
                            @endif
                            @error('nama_lengkap') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 4. KOLOM SISA --}}
                        <div class="sm:col-span-1">
                            <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">No. SAP / ID</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-id-badge text-slate-400 text-xs"></i></div>
                                <input type="text" wire:model="no_sap" class="block w-full pl-10 pr-3 py-2.5 text-xs md:text-sm font-medium font-mono rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all shadow-sm {{ ($tipe_personel !== 'manual' && $karyawanFound) ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-transparent' : 'bg-slate-50/50 hover:bg-white focus:bg-white' }}" {{ ($tipe_personel !== 'manual' && $karyawanFound) ? 'disabled' : '' }}>
                            </div>
                            @error('no_sap') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="sm:col-span-1">
                            <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Email Login</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-envelope text-slate-400 text-xs"></i></div>
                                <input type="email" wire:model="email" class="block w-full pl-10 pr-3 py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all shadow-sm {{ ($tipe_personel !== 'manual' && $karyawanFound) ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-transparent' : 'bg-slate-50/50 hover:bg-white focus:bg-white' }}" {{ ($tipe_personel !== 'manual' && $karyawanFound) ? 'disabled' : '' }}>
                            </div>
                            @error('email') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                        </div>

                        @if (!$isEditing)
                            <div class="sm:col-span-2 lg:col-span-4 border-t border-slate-200 pt-4 mt-2">
                                <label class="block text-[10px] md:text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Buat Password Default <span class="text-red-500">*</span></label>
                                <div class="relative w-full md:w-1/2 lg:w-1/3">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-lock text-slate-400 text-xs"></i></div>
                                    <input type="password" id="pass_add_admin" wire:model="password" placeholder="Minimal 6 karakter" class="block w-full pl-10 pr-10 py-2.5 text-xs md:text-sm font-medium rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all shadow-sm">
                                    <button type="button" onclick="togglePasswordVisibility('pass_add_admin', 'eyeOpen_aa', 'eyeClosed_aa')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 focus:outline-none">
                                        <img id="eyeOpen_aa" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                                        <img id="eyeClosed_aa" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                                    </button>
                                </div>
                                @error('password') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tombol Responsif --}}
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2.5 md:gap-3">
                    @if ($isEditing)
                        <button type="button" wire:click="cancelEdit" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-lg hover:bg-slate-50 transition-all text-xs shadow-sm">Batal Edit</button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg shadow-md hover:-translate-y-0.5 transition-all text-xs">Update Akun</button>
                    @else
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-lg shadow-md shadow-blue-500/20 hover:-translate-y-0.5 transition-all text-xs">
                            <i class="fas fa-save mr-1.5"></i> Daftarkan Akun
                        </button>
                    @endif
                </div>
            </form>
        </div>

        {{-- KARTU TABEL ADMIN --}}
        <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/40 border border-slate-100 p-5 sm:p-7 md:p-8 max-w-5xl mx-auto overflow-hidden">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 border-b border-slate-100 pb-4">
                <div>
                    <h4 class="text-base md:text-lg font-black text-slate-800 flex items-center"><i class="fas fa-database text-blue-500 mr-2.5"></i> Database Akun Sistem</h4>
                    <p class="text-[10px] md:text-xs text-slate-500 font-medium mt-0.5">Seluruh pengguna yang memiliki akses ke dalam dashboard admin.</p>
                </div>
            </div>
            
            {{-- Tampilan Desktop (Tabel) --}}
            <div class="hidden md:block overflow-x-auto rounded-xl border border-slate-100 bg-white">
                <table class="min-w-full text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest w-5/12">Identitas Personel</th>
                            <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest w-4/12">Login & Akses</th>
                            <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center w-3/12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($this->adminUsers as $admin)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    {{-- Avatar Inisial Berwarna sesuai Role --}}
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-base shadow-inner shrink-0 transition-colors
                                        {{ $admin->role === 'superadmin' ? 'bg-purple-50 text-purple-600 group-hover:bg-purple-100' : 
                                          ($admin->role === 'admin' ? 'bg-rose-50 text-rose-600 group-hover:bg-rose-100' : 
                                          ($admin->role === 'dokter' ? 'bg-blue-50 text-blue-600 group-hover:bg-blue-100' : 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100')) }}">
                                        {{ strtoupper(substr($admin->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-[13px] text-slate-800">{{ $admin->nama_lengkap }}</p>
                                        <div class="mt-1">
                                            <span class="text-[9px] font-mono text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200 shadow-sm">SAP: {{ $admin->no_sap ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="text-[11px] font-medium text-slate-600 flex items-center mb-1.5"><span class="w-4 text-center inline-block mr-1"><i class="fas fa-envelope text-slate-400"></i></span> {{ $admin->email }}</p>
                                
                                {{-- Badge Role --}}
                                <span class="px-2 py-0.5 rounded uppercase border text-[9px] font-bold shadow-sm
                                    {{ $admin->role === 'superadmin' ? 'bg-purple-50 text-purple-600 border-purple-200' : 
                                      ($admin->role === 'admin' ? 'bg-rose-50 text-rose-600 border-rose-200' : 
                                      ($admin->role === 'dokter' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200')) }}">
                                    {{ $admin->role }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex justify-center gap-1.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                    <button wire:click="editPassword({{ $admin->id }})" title="Ganti Password" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-amber-500 hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all shadow-sm"><i class="fas fa-key text-[10px]"></i></button>
                                    <button wire:click="edit({{ $admin->id }})" title="Edit Profil" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-blue-500 hover:text-white hover:border-blue-500 transition-all shadow-sm"><i class="fas fa-pen text-[10px]"></i></button>
                                    <button wire:click="delete({{ $admin->id }})" title="Hapus Akun" onclick="return confirm('Hapus akun admin ini?')" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all shadow-sm"><i class="fas fa-trash text-[10px]"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center">
                                <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-2"><i class="fas fa-user-slash text-xl"></i></div>
                                <p class="text-slate-500 font-bold text-[13px]">Belum ada data akun.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tampilan Mobile (Cards Layout) --}}
            <div class="md:hidden space-y-3">
                @forelse ($this->adminUsers as $admin)
                <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm relative overflow-hidden">
                    
                    {{-- Aksen Garis Kiri Sesuai Role --}}
                    <div class="absolute top-0 left-0 w-1 h-full 
                        {{ $admin->role === 'superadmin' ? 'bg-gradient-to-b from-purple-400 to-purple-600' : 
                          ($admin->role === 'admin' ? 'bg-gradient-to-b from-rose-400 to-rose-600' : 
                          ($admin->role === 'dokter' ? 'bg-gradient-to-b from-blue-400 to-blue-600' : 'bg-gradient-to-b from-emerald-400 to-emerald-600')) }}">
                    </div>
                    
                    <div class="flex items-start gap-3 mb-4 pl-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-base shadow-inner shrink-0
                            {{ $admin->role === 'superadmin' ? 'bg-purple-50 text-purple-600' : 
                              ($admin->role === 'admin' ? 'bg-rose-50 text-rose-600' : 
                              ($admin->role === 'dokter' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600')) }}">
                            {{ strtoupper(substr($admin->nama_lengkap, 0, 1)) }}
                        </div>
                        <div class="pr-2 flex-1">
                            <p class="font-black text-slate-800 text-[13px] leading-snug">{{ $admin->nama_lengkap }}</p>
                            <div class="mt-1">
                                <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold uppercase border shadow-sm
                                    {{ $admin->role === 'superadmin' ? 'bg-purple-50 text-purple-600 border-purple-200' : 
                                      ($admin->role === 'admin' ? 'bg-rose-50 text-rose-600 border-rose-200' : 
                                      ($admin->role === 'dokter' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200')) }}">
                                    {{ $admin->role }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pl-1">
                        <div class="text-[10px] text-slate-600 font-medium space-y-1.5 mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <p class="flex items-center"><span class="w-4 inline-block"><i class="fas fa-id-card text-slate-400"></i></span> SAP: <span class="font-mono ml-1 bg-white px-1 border border-slate-200 rounded">{{ $admin->no_sap ?? '-' }}</span></p>
                            <p class="flex items-center"><span class="w-4 inline-block"><i class="fas fa-envelope text-slate-400"></i></span> <span class="truncate">{{ $admin->email }}</span></p>
                        </div>
                        
                        <div class="flex gap-2">
                            <button wire:click="editPassword({{ $admin->id }})" class="flex-1 bg-amber-50 border border-amber-100 py-2 rounded-lg text-[11px] font-bold text-amber-600 hover:bg-amber-100 transition-colors shadow-sm"><i class="fas fa-key mr-1"></i> Pass</button>
                            <button wire:click="edit({{ $admin->id }})" class="flex-1 bg-white border border-slate-200 py-2 rounded-lg text-[11px] font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm"><i class="fas fa-pen mr-1"></i> Edit</button>
                            <button wire:click="delete({{ $admin->id }})" onclick="return confirm('Hapus profil admin ini?')" class="flex-1 bg-red-50 text-red-600 border border-red-100 py-2 rounded-lg text-[11px] font-bold hover:bg-red-100 transition-colors shadow-sm"><i class="fas fa-trash mr-1"></i> Hapus</button>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="text-center py-8 text-slate-400 bg-slate-50 rounded-2xl border border-slate-100 border-dashed">
                        <i class="fas fa-user-slash text-2xl mb-2 opacity-40"></i>
                        <p class="text-[11px] font-bold text-slate-500">Tidak ada akun.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-5 pt-3 border-t border-slate-100">
                {{ $this->adminUsers->links() }}
            </div>
        </div>

        {{-- Modal Ganti Password Modern --}}
        @if ($editPasswordId)
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[100] animate-fade-in">
                <div class="bg-white rounded-[2rem] shadow-2xl max-w-sm w-full p-6 text-center relative overflow-hidden">
                    <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-100 shadow-sm"><i class="fas fa-key text-xl"></i></div>
                    <h3 class="text-xl font-black text-slate-800 mb-1">Ganti Password</h3>
                    <p class="text-[11px] text-slate-500 font-medium mb-6">Ubah kata sandi keamanan untuk admin ini.</p>
                    
                    <form wire:submit.prevent="updatePassword" class="text-left">
                        <div class="mb-5">
                            <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-widest mb-1.5">Password Baru</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i class="fas fa-lock text-slate-400 text-xs"></i></div>
                                <input type="password" id="pass_edit_admin" wire:model="newPassword" class="block w-full pl-10 pr-10 py-3 text-sm font-bold rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 shadow-sm transition-all">
                                <button type="button" onclick="togglePasswordVisibility('pass_edit_admin', 'eyeOpen_ea', 'eyeClosed_ea')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-amber-600 focus:outline-none">
                                    <img id="eyeOpen_ea" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                                    <img id="eyeClosed_ea" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                                </button>
                            </div>
                            @error('newPassword') <p class="mt-1 text-[10px] font-bold text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <button type="submit" class="w-full py-3 rounded-xl font-bold text-white bg-slate-800 hover:bg-slate-900 shadow-md hover:-translate-y-0.5 transition-all text-xs">Simpan Password</button>
                            <button type="button" wire:click="$set('editPasswordId', null)" class="w-full py-3 rounded-xl font-bold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-xs">Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.2s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</div>