<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen Akun') }}
    </x-slot>

    <!-- Pembungkus Alpine.js untuk mengontrol Modal -->
    <div class="py-10" x-data="{ showAddModal: false, showEditModal: false, editFormAction: '', editData: { name: '', email: '', role: '' } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Notifikasi Sukses / Error -->
            @if(session('success'))
                <div class="mb-4 px-4 py-3 text-sm font-bold text-emerald-700 bg-emerald-100 border-l-4 border-emerald-500 rounded-r-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 px-4 py-3 text-sm font-bold text-red-700 bg-red-100 border-l-4 border-red-500 rounded-r-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Header Aksi -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Pengguna Sistem</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola hak akses dan penempatan unit seluruh karyawan.</p>
                </div>
                
                <!-- Tombol Tambah Akun (Kini terhubung dengan Alpine.js) -->
                <button @click="showAddModal = true" class="flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-[#F5333F] rounded-xl shadow-lg hover:bg-red-700 transition-all focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Akun Baru
                </button>
            </div>

            <!-- Tabel Data -->
            <div class="bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800 text-[11px] font-bold text-gray-500 uppercase tracking-widest">
                                <th class="px-6 py-4">Informasi Karyawan</th>
                                <th class="px-6 py-4">Role Sistem</th>
                                <th class="px-6 py-4">Unit Kerja</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60">
                            
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-9 h-9 rounded-xl object-cover shadow-sm border border-gray-200 dark:border-gray-700 shrink-0">
                                        @else
                                            <div class="flex items-center justify-center text-sm font-bold text-white rounded-xl shadow-sm w-9 h-9 bg-[#F5333F] shrink-0">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    @php
                                        $roleName = $user->roles->first()->name ?? 'User';
                                        $roleColors = [
                                            'Developer' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 border-purple-200 dark:border-purple-800/30',
                                            'Super Admin' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border-blue-200 dark:border-blue-800/30',
                                            'Admin Busdev' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border-amber-200 dark:border-amber-800/30',
                                            'Admin STMC' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/30',
                                            'User' => 'bg-slate-100 text-slate-700 dark:bg-slate-500/10 dark:text-slate-400 border-slate-200 dark:border-slate-800/30',
                                        ];
                                        $color = $roleColors[$roleName] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400 border-gray-200 dark:border-gray-800/30';
                                    @endphp
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg border {{ $color }}">
                                        {{ $roleName }}
                                    </span>
                                </td>

                                <!-- Unit Kerja -->
                                <td class="px-6 py-4">
                                    @if($user->unit)
                                        <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $user->unit->name_unit }}</p>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ $user->unit->code_unit }}</p>
                                    @else
                                        <span class="text-xs italic text-gray-400">Tidak ada unit</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                        <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Aktif</span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-right">
                                    <!-- Tombol Edit -->
                                    <button @click="
                                                showEditModal = true; 
                                                editFormAction = '{{ route('accounts.update', $user->id) }}'; 
                                                editData = {{ Illuminate\Support\Js::from(['name' => $user->name, 'email' => $user->email, 'role' => $roleName, 'unit_id' => $user->unit_id]) }};
                                            " 
                                            class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Edit Akun">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    <!-- Tombol Hapus (Berfungsi & Dilindungi Form) -->
                                    @if($roleName !== 'Developer')
                                    <form action="{{ route('accounts.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }} secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-[#F5333F] transition-colors" title="Hapus Akun">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL POP-UP TAMBAH PENGGUNA (ALPINE.JS) -->
        <!-- ========================================== -->
        <div x-show="showAddModal" class="relative z-50" style="display: none;">
            
            <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
                <div x-show="showAddModal" 
                     @click.away="showAddModal = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="w-full max-w-lg overflow-hidden bg-white rounded-2xl shadow-2xl dark:bg-gray-900 transition-colors">
                    
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Registrasi Akun Internal</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sandi bawaan: <span class="font-mono font-bold text-[#F5333F]">Tonasa2026!</span></p>
                        </div>
                        <button type="button" @click="showAddModal = false" class="text-gray-400 hover:text-red-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Form Tambah -->
                    <form action="{{ route('accounts.store') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        
                        <div>
                            <label class="block mb-1.5 text-sm font-bold text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                            <input type="text" name="name" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-[#F5333F] focus:border-[#F5333F]" required>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-bold text-gray-700 dark:text-gray-300">Email Korporat</label>
                            <input type="email" name="email" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-[#F5333F] focus:border-[#F5333F]" required>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-bold text-gray-700 dark:text-gray-300">Hak Akses (Role)</label>
                            <select name="role" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-[#F5333F] focus:border-[#F5333F]" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-bold text-gray-700 dark:text-gray-300">Penempatan Unit (Seksi)</label>
                            <select name="unit_id" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-[#F5333F] focus:border-[#F5333F]">
                                <option value="">-- Pilih Unit Kerja (Opsional) --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name_unit }} ({{ $unit->code_unit }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-100 dark:border-gray-800">
                            <button type="button" @click="showAddModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-[#F5333F] rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-500/30">
                                Simpan Akun Baru
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL POP-UP EDIT PENGGUNA (ALPINE.JS) -->
        <!-- ========================================== -->
        <div x-show="showEditModal" class="relative z-50" style="display: none;">
            
            <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
                <div x-show="showEditModal" 
                    @click.away="showEditModal = false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="w-full max-w-lg overflow-hidden bg-white rounded-2xl shadow-2xl dark:bg-gray-900 transition-colors">
                    
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Data Akun</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Perbarui informasi dan hak akses karyawan.</p>
                        </div>
                        <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-blue-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Form Edit -->
                    <form x-bind:action="editFormAction" method="POST" class="p-6 space-y-5">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block mb-1.5 text-sm font-bold text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                            <input type="text" name="name" x-model="editData.name" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-bold text-gray-700 dark:text-gray-300">Email Korporat</label>
                            <input type="email" name="email" x-model="editData.email" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-bold text-gray-700 dark:text-gray-300">Hak Akses (Role)</label>
                            <select name="role" x-model="editData.role" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                                <template x-if="editData.role === 'Developer'">
                                    <!-- Jika yang diedit adalah Developer, hanya muncul opsi Developer -->
                                    <option value="Developer">Developer</option>
                                </template>
                                <template x-if="editData.role !== 'Developer'">
                                    <!-- Opsi untuk akun normal -->
                                    <optgroup label="Pilihan Role">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </optgroup>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-bold text-gray-700 dark:text-gray-300">Penempatan Unit (Seksi)</label>
                            <select name="unit_id" x-model="editData.unit_id" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Unit Kerja --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name_unit }} ({{ $unit->code_unit }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-100 dark:border-gray-800">
                            <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/30">
                                Perbarui Data
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>