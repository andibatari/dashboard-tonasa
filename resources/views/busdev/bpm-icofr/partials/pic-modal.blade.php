{{-- ==================================================================== --}}
{{-- MODAL : MANAJEMEN MASTER PIC PELAKSANA KONTROL & SINKRONISASI SPREADSHEET --}}
{{-- ==================================================================== --}}
@php
    $dbDepartments = $departments ?? \App\Models\Department::orderBy('name_dept')->get();
    $dbUnits = $allUnits ?? \App\Models\Unit::with('department')->orderBy('name_unit')->get();
@endphp
<div x-data="{
        showPicModal: false,
        picTab: 'list', // 'list' | 'form' | 'guide'
        picSearch: '',
        picUnitFilter: '',
        editingPic: null,
        picForm: {
            id: '',
            name: '',
            position: '',
            unitSelect: '',
            customUnit: '',
            deptSelect: '',
            customDept: '',
            phone: '',
            notes: ''
        },
        picsList: @js($icofrPics),
        dbDepartments: @js($dbDepartments->pluck('name_dept')->values()),
        dbUnits: @js($dbUnits->map(fn($u) => ['id' => $u->id, 'name' => $u->name_unit, 'dept' => $u->department?->name_dept])->values()),
        
        get availableUnits() {
            if (!this.picForm.deptSelect || this.picForm.deptSelect === '__OTHER__') {
                return this.dbUnits;
            }
            const filtered = this.dbUnits.filter(u => u.dept && u.dept.toLowerCase() === this.picForm.deptSelect.toLowerCase());
            return filtered.length > 0 ? filtered : this.dbUnits;
        },

        get filteredPics() {
            return this.picsList.filter(p => {
                const matchSearch = !this.picSearch || 
                    (p.name && p.name.toLowerCase().includes(this.picSearch.toLowerCase())) ||
                    (p.position && p.position.toLowerCase().includes(this.picSearch.toLowerCase())) ||
                    (p.unit && p.unit.toLowerCase().includes(this.picSearch.toLowerCase())) ||
                    (p.department && p.department.toLowerCase().includes(this.picSearch.toLowerCase()));
                const matchUnit = !this.picUnitFilter || p.unit === this.picUnitFilter;
                return matchSearch && matchUnit;
            });
        },

        get uniqueUnits() {
            return [...new Set(this.picsList.map(p => p.unit).filter(Boolean))].sort();
        },

        onUnitSelectChange(val) {
            if (val === '__OTHER__') {
                this.picForm.customUnit = '';
            } else {
                this.picForm.customUnit = '';
                // Auto-fill/select Department dari database jika unit terhubung
                const foundUnit = this.dbUnits.find(u => u.name === val);
                if (foundUnit && foundUnit.dept) {
                    const matchDept = this.dbDepartments.find(d => d.toLowerCase() === foundUnit.dept.toLowerCase());
                    if (matchDept) {
                        this.picForm.deptSelect = matchDept;
                        this.picForm.customDept = '';
                    } else {
                        this.picForm.deptSelect = '__OTHER__';
                        this.picForm.customDept = foundUnit.dept;
                    }
                }
            }
        },

        onDeptSelectChange(val) {
            if (val === '__OTHER__') {
                this.picForm.customDept = '';
            } else {
                this.picForm.customDept = '';
                // Jika unit terpilih tidak sesuai dengan departemen yang dipilih, reset pilihan unit
                if (this.picForm.unitSelect && this.picForm.unitSelect !== '__OTHER__') {
                    const currentUnit = this.dbUnits.find(u => u.name === this.picForm.unitSelect);
                    if (currentUnit && currentUnit.dept && currentUnit.dept.toLowerCase() !== val.toLowerCase()) {
                        this.picForm.unitSelect = '';
                    }
                }
            }
        },

        openAddPic() {
            this.editingPic = null;
            this.picForm = { 
                id: '', 
                name: '', 
                position: '', 
                unitSelect: '', 
                customUnit: '', 
                deptSelect: '', 
                customDept: '', 
                phone: '', 
                notes: '' 
            };
            this.picTab = 'form';
        },

        openEditPic(pic) {
            this.editingPic = pic;
            
            let uSelect = '';
            let cUnit = '';
            if (pic.unit) {
                const exists = this.dbUnits.some(u => u.name.toLowerCase() === pic.unit.toLowerCase());
                if (exists) {
                    uSelect = this.dbUnits.find(u => u.name.toLowerCase() === pic.unit.toLowerCase()).name;
                } else {
                    uSelect = '__OTHER__';
                    cUnit = pic.unit;
                }
            }

            let dSelect = '';
            let cDept = '';
            if (pic.department) {
                const exists = this.dbDepartments.some(d => d.toLowerCase() === pic.department.toLowerCase());
                if (exists) {
                    dSelect = this.dbDepartments.find(d => d.toLowerCase() === pic.department.toLowerCase());
                } else {
                    dSelect = '__OTHER__';
                    cDept = pic.department;
                }
            }

            this.picForm = {
                id: pic.id,
                name: pic.name || '',
                position: pic.position || '',
                unitSelect: uSelect,
                customUnit: cUnit,
                deptSelect: dSelect,
                customDept: cDept,
                phone: pic.phone || '',
                notes: pic.notes || ''
            };
            this.picTab = 'form';
        },

        deletePic(id, position) {
            if (!confirm('Hapus PIC ' + position + '?')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url('/busdev/bpm-icofr/pics') }}/' + id;
            form.innerHTML = `
                <input type='hidden' name='_token' value='{{ csrf_token() }}'>
                <input type='hidden' name='_method' value='DELETE'>
            `;
            document.body.appendChild(form);
            form.submit();
        }
     }"
     @open-pic-modal.window="showPicModal = true; picTab = 'list'"
     @keydown.escape.window="showPicModal = false"
     x-show="showPicModal" 
     class="relative z-50" 
     style="display: none;" 
     role="dialog" 
     aria-modal="true">

    {{-- Backdrop --}}
    <div x-show="showPicModal" 
         x-transition.opacity 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div x-show="showPicModal" 
             @click.outside="showPicModal = false" 
             class="w-full max-w-4xl overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-[#161a23] border border-slate-200 dark:border-slate-800 flex flex-col max-h-[92vh]">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-2xl bg-red-500/10 text-[#F5333F] dark:bg-red-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            Master PIC Pelaksana Kontrol ICoFR
                            <span class="px-2 py-0.5 text-[11px] font-black rounded-full bg-red-100 text-[#F5333F] dark:bg-red-950/60 dark:text-red-400 font-mono" x-text="picsList.length + ' Total'"></span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Kelola relasi berjenjang Unit &bull; Jabatan &bull; Nama Pelaksana Kontrol untuk website &amp; Google Spreadsheet.
                        </p>
                    </div>
                </div>
                <button type="button" @click="showPicModal = false" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Nav Tabs --}}
            <div class="flex items-center justify-between px-5 pt-3 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-[#161a23]">
                <div class="flex items-center gap-2">
                    <button type="button" @click="picTab = 'list'" 
                            :class="picTab === 'list' ? 'border-[#F5333F] text-[#F5333F] font-black' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 font-semibold'"
                            class="pb-2.5 px-3 border-b-2 text-xs flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span>Daftar PIC (<span x-text="filteredPics.length"></span>)</span>
                    </button>
                    <button type="button" @click="openAddPic()" 
                            :class="picTab === 'form' ? 'border-[#F5333F] text-[#F5333F] font-black' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 font-semibold'"
                            class="pb-2.5 px-3 border-b-2 text-xs flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span x-text="editingPic ? 'Edit PIC' : 'Tambah PIC Baru'"></span>
                    </button>
                    <button type="button" @click="picTab = 'guide'" 
                            :class="picTab === 'guide' ? 'border-[#F5333F] text-[#F5333F] font-black' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 font-semibold'"
                            class="pb-2.5 px-3 border-b-2 text-xs flex items-center gap-2 transition">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Rumus &amp; Script Spreadsheet</span>
                    </button>
                </div>

                <div class="pb-2">
                    <form method="POST" action="{{ route('busdev.bpm-icofr.pics.push-sheets') }}" onsubmit="return confirm('Kirim dan buat Sheet Master_PIC di Google Spreadsheet?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Push ke Google Sheets</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Tab 1: Daftar PIC Table --}}
            <div x-show="picTab === 'list'" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3">
                {{-- Toolbar Filter --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-slate-50 dark:bg-slate-800/40 p-3 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" x-model="picSearch" placeholder="Cari nama, jabatan, unit, atau departemen..."
                               class="w-full pl-9 pr-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:border-[#F5333F] focus:outline-none dark:text-white">
                    </div>
                    <div class="flex items-center gap-2">
                        <select x-model="picUnitFilter" class="px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:border-[#F5333F]">
                            <option value="">Semua Unit</option>
                            <template x-for="u in uniqueUnits" :key="u">
                                <option :value="u" x-text="u"></option>
                            </template>
                        </select>
                        <button type="button" @click="openAddPic()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-white bg-[#F5333F] hover:bg-red-600 rounded-xl transition shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah</span>
                        </button>
                    </div>
                </div>

                {{-- Table View --}}
                <div class="overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900/80 text-[10px] font-black uppercase text-slate-500 border-b border-slate-200 dark:border-slate-800 tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No.</th>
                                <th class="px-4 py-3">Unit Pelaksana</th>
                                <th class="px-4 py-3">Jabatan Pelaksana</th>
                                <th class="px-4 py-3">Nama Pelaksana</th>
                                <th class="px-4 py-3">Departemen</th>
                                <th class="px-4 py-3 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <template x-for="(pic, idx) in filteredPics" :key="pic.id">
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-4 py-3 text-center font-mono text-slate-400" x-text="idx + 1"></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-0.5 text-[11px] font-bold rounded-md bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 dark:border-blue-900" x-text="pic.unit"></span>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200" x-text="pic.position"></td>
                                    <td class="px-4 py-3">
                                        <template x-if="pic.name">
                                            <span class="font-bold text-slate-900 dark:text-white" x-text="pic.name"></span>
                                        </template>
                                        <template x-if="!pic.name">
                                            <span class="text-slate-400 italic">(Belum ada nama pejabat)</span>
                                        </template>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 text-[11px]" x-text="pic.department || '-'"></td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" @click="openEditPic(pic)" class="p-1.5 text-slate-400 hover:text-[#F5333F] rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button type="button" @click="deletePic(pic.id, pic.position)" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/30 transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredPics.length === 0">
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 italic">
                                        Tidak ada data PIC yang sesuai dengan pencarian.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 2: Form Tambah/Edit PIC --}}
            <div x-show="picTab === 'form'" class="flex-1 overflow-y-auto p-5 space-y-4">
                <form method="POST" :action="editingPic ? '{{ url('/busdev/bpm-icofr/pics') }}/' + editingPic.id : '{{ route('busdev.bpm-icofr.pics.store') }}'">
                    @csrf
                    <template x-if="editingPic">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="bg-slate-50 dark:bg-slate-800/40 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 mb-4">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300" x-text="editingPic ? 'Edit Data PIC Pelaksana' : 'Form Tambah PIC Pelaksana Kontrol Baru'"></h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">PIC yang didaftarkan di sini akan otomatis muncul pada dropdown berjenjang saat menambah atau mengedit kontrol ICoFR.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Departemen / Direktorat --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                <span>Departemen / Direktorat</span>
                                <span class="text-[10px] text-slate-400 font-normal">Database Terhubung</span>
                            </label>
                            <select x-model="picForm.deptSelect" 
                                    @change="onDeptSelectChange($event.target.value)"
                                    class="w-full px-3.5 py-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:border-[#F5333F] focus:outline-none dark:text-white">
                                <option value="">-- Pilih Departemen --</option>
                                <template x-for="dept in dbDepartments" :key="dept">
                                    <option :value="dept" x-text="dept"></option>
                                </template>
                                <option value="__OTHER__">&#10133; Lainnya... (Ketik Departemen Baru)</option>
                            </select>

                            {{-- Input Manual jika memilih Lainnya --}}
                            <div x-show="picForm.deptSelect === '__OTHER__'" x-transition class="mt-2 space-y-1">
                                <input type="text" 
                                       x-model="picForm.customDept" 
                                       placeholder="Ketik nama departemen / direktorat baru..." 
                                       class="w-full px-3.5 py-2 text-xs bg-amber-50/50 dark:bg-slate-900 border border-amber-300 dark:border-amber-600 rounded-xl focus:border-[#F5333F] focus:outline-none dark:text-white placeholder-slate-400">
                                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium block">
                                    &bull; Mode ketik manual departemen baru
                                </span>
                            </div>

                            <input type="hidden" name="department" :value="picForm.deptSelect === '__OTHER__' ? picForm.customDept : picForm.deptSelect">
                        </div>

                        {{-- Unit Pelaksana Kontrol --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                <span>Unit Pelaksana Kontrol <span class="text-red-500">*</span></span>
                                <span class="text-[10px] text-slate-400 font-normal">Database Terhubung</span>
                            </label>
                            <select x-model="picForm.unitSelect" 
                                    @change="onUnitSelectChange($event.target.value)"
                                    class="w-full px-3.5 py-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:border-[#F5333F] focus:outline-none dark:text-white"
                                    :required="picForm.unitSelect !== '__OTHER__'">
                                <option value="">-- Pilih Unit Pelaksana --</option>
                                <template x-for="u in availableUnits" :key="u.id">
                                    <option :value="u.name" x-text="u.name + (u.dept && !picForm.deptSelect ? ' (' + u.dept + ')' : '')"></option>
                                </template>
                                <option value="__OTHER__">&#10133; Lainnya... (Ketik Unit Baru)</option>
                            </select>

                            {{-- Input Manual jika memilih Lainnya --}}
                            <div x-show="picForm.unitSelect === '__OTHER__'" x-transition class="mt-2 space-y-1">
                                <input type="text" 
                                       x-model="picForm.customUnit" 
                                       placeholder="Ketik nama unit pelaksana baru..." 
                                       :required="picForm.unitSelect === '__OTHER__'"
                                       class="w-full px-3.5 py-2 text-xs bg-amber-50/50 dark:bg-slate-900 border border-amber-300 dark:border-amber-600 rounded-xl focus:border-[#F5333F] focus:outline-none dark:text-white placeholder-slate-400">
                                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium block">
                                    &bull; Mode ketik manual unit baru
                                </span>
                            </div>

                            <input type="hidden" name="unit" :value="picForm.unitSelect === '__OTHER__' ? picForm.customUnit : picForm.unitSelect" required>
                        </div>

                        {{-- Jabatan Pelaksana Kontrol --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Jabatan Pelaksana Kontrol <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="position" x-model="picForm.position" required placeholder="Contoh: Treasury Officer / SM of Finance"
                                   class="w-full px-3.5 py-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:border-[#F5333F] focus:outline-none dark:text-white">
                        </div>

                        {{-- Nama Pelaksana Kontrol --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Nama Pelaksana Kontrol (Pejabat Aktif)
                            </label>
                            <input type="text" name="name" x-model="picForm.name" placeholder="Contoh: Muh. Rifqi Sanuddin, SE."
                                   class="w-full px-3.5 py-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:border-[#F5333F] focus:outline-none dark:text-white">
                        </div>

                        {{-- Catatan / WhatsApp --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Catatan / Kontak
                            </label>
                            <input type="text" name="notes" x-model="picForm.notes" placeholder="Catatan tambahan atau no kontak (opsional)"
                                   class="w-full px-3.5 py-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:border-[#F5333F] focus:outline-none dark:text-white">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800 mt-5">
                        <button type="button" @click="picTab = 'list'" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-[#F5333F] hover:bg-red-600 rounded-xl shadow-md shadow-red-500/20 transition">
                            <span x-text="editingPic ? 'Simpan Perubahan' : 'Tambahkan PIC'"></span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tab 3: Panduan & Script Apps Script Spreadsheet --}}
            <div x-show="picTab === 'guide'" class="flex-1 overflow-y-auto p-5 space-y-4 text-xs leading-relaxed">
                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800">
                    <h4 class="font-extrabold text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Solusi Dropdown Berjenjang Otomatis di Google Spreadsheet
                    </h4>
                    <p class="text-emerald-700 dark:text-emerald-400 mt-1">
                        Di Google Spreadsheet, untuk membuat relasi bertingkat (<strong>Pilih Unit di Kolom F</strong> &rarr; <strong>Muncul Jabatan terkait di Kolom E</strong> &rarr; <strong>Nama Pejabat otomatis terisi di Kolom D</strong>), gunakan fitur <strong>Google Apps Script `onEdit(e)`</strong> di bawah ini.
                    </p>
                </div>

                <div class="space-y-3">
                    <h5 class="font-black text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">Langkah Cepat Pemasangan:</h5>
                    <ol class="list-decimal list-inside space-y-2 text-slate-600 dark:text-slate-400">
                        <li>Buka Google Spreadsheet Anda: <a href="https://docs.google.com/spreadsheets/d/1JKyajr5QjfZXSboWKFZhA1k_1nZZZsUrqZvOOpOS_Ds/edit" target="_blank" class="text-[#F5333F] font-bold underline">Link Spreadsheet ICoFR</a></li>
                        <li>Klik menu <strong>Extensions (Ekstensi)</strong> &rarr; <strong>Apps Script</strong>.</li>
                        <li>Salin kode skrip di bawah ini dan tempelkan ke dalam file <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded font-mono text-[#F5333F]">Code.gs</code>:</li>
                    </ol>

                    <div class="relative">
                        <pre class="p-4 bg-slate-900 text-slate-200 rounded-2xl font-mono text-[11px] overflow-x-auto select-all leading-normal" id="apps-script-code">
/**
 * ==============================================================================
 * GOOGLE APPS SCRIPT: INTEGRASI KONTROL ICOFR &amp; MASTER PIC PELAKSANA KONTROL
 * PT SEMEN TONASA (VERSI TERPADU: DROPDOWN BERJENJANG UNIT &amp; JABATAN OTOMATIS)
 * ==============================================================================
 */

function doPost(e) {
  try {
    var contents = JSON.parse(e.postData.contents);
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var action = contents.action || &#039;sync_controls&#039;;
    var data = contents.data;
    
    if (!data || !data.length) {
      return ContentService.createTextOutput(JSON.stringify({
        status: &#039;error&#039;, 
        message: &#039;Data kosong. Tidak ada data yang dikirim.&#039;
      })).setMimeType(ContentService.MimeType.JSON);
    }

    // =========================================================================
    // AKSI 1: SINKRONISASI SHEET &quot;Master_PIC&quot; (DARI TOMBOL PUSH MASTER PIC DI WEB)
    // =========================================================================
    if (action === &#039;sync_pics&#039;) {
      var picSheet = ss.getSheetByName(&quot;Master_PIC&quot;);
      if (!picSheet) {
        picSheet = ss.insertSheet(&quot;Master_PIC&quot;);
      }
      
      picSheet.clear();
      
      // 1. Tulis Header Resmi
      var headers = [[&quot;No&quot;, &quot;Nama Pelaksana Kontrol&quot;, &quot;Jabatan Pelaksana Kontrol&quot;, &quot;Unit Pelaksana Kontrol&quot;, &quot;Departemen&quot;]];
      picSheet.getRange(1, 1, 1, 5).setValues(headers)
        .setFontWeight(&quot;bold&quot;)
        .setBackground(&quot;#003366&quot;)
        .setFontColor(&quot;#FFFFFF&quot;)
        .setHorizontalAlignment(&quot;center&quot;);
      
      picSheet.setFrozenRows(1);
      
      // 2. Tulis Seluruh Data PIC
      var picRows = [];
      for (var p = 0; p &lt; data.length; p++) {
        var it = data[p];
        picRows.push([
          it.no || (p + 1),
          it.name || &quot;&quot;,
          it.position || &quot;&quot;,
          it.unit || &quot;&quot;,
          it.department || &quot;&quot;
        ]);
      }
      
      if (picRows.length &gt; 0) {
        var dataRange = picSheet.getRange(2, 1, picRows.length, 5);
        dataRange.setValues(picRows);
        dataRange.setBorder(true, true, true, true, true, true, &quot;#D1D5DB&quot;, SpreadsheetApp.BorderStyle.SOLID);
      }
      
      // 3. Rapikan Lebar Kolom
      for (var col = 1; col &lt;= 5; col++) {
        picSheet.autoResizeColumn(col);
      }
      
      // 4. OTOMATIS PASANG DROPDOWN UNIT &amp; JABATAN PADA SELURUH SHEET KONTROL
      try {
        pasangSemuaDropdown(ss);
      } catch (ddErr) {
        Logger.log(&quot;Info dropdown: &quot; + ddErr);
      }
      
      return ContentService.createTextOutput(JSON.stringify({
        status: &#039;success&#039;,
        message: &#039;Berhasil membuat Sheet Master_PIC dengan &#039; + picRows.length + &#039; PIC, dan seluruh baris Kolom Unit &amp; Jabatan telah dipasangi Dropdown otomatis!&#039;
      })).setMimeType(ContentService.MimeType.JSON);
    }

    // =========================================================================
    // AKSI 2: SINKRONISASI KONTROL &amp; HASIL CSA KE SHEET AKTIF
    // =========================================================================
    var sheet = ss.getActiveSheet();
    var lastRow = Math.max(sheet.getLastRow(), 1);
    var lastCol = Math.max(sheet.getLastColumn(), 26);
    
    // 1. Deteksi Baris Header Inti
    var headerRowIndex = 2; // Default baris ke-2
    var scanRows = Math.min(lastRow, 10);
    var sampleGrid = sheet.getRange(1, 1, scanRows, lastCol).getValues();
    
    for (var r = 0; r &lt; scanRows; r++) {
      var rowStr = sampleGrid[r].join(&quot; &quot;).toLowerCase();
      if (rowStr.indexOf(&quot;(4)&quot;) !== -1 || rowStr.indexOf(&quot;ref. control&quot;) !== -1 || rowStr.indexOf(&quot;ref control&quot;) !== -1) {
        headerRowIndex = r + 1;
        break;
      }
    }
    
    // 2. Gabungkan SEMUA teks header dari Baris 1 s/d headerRowIndex untuk setiap kolom
    var headerGrid = sheet.getRange(1, 1, headerRowIndex, lastCol).getValues();
    var combinedHeaders = [];
    for (var c = 0; c &lt; lastCol; c++) {
      var text = &quot;&quot;;
      for (var r = 0; r &lt; headerRowIndex; r++) {
        if (headerGrid[r][c] !== undefined &amp;&amp; headerGrid[r][c] !== null) {
          text += &quot; &quot; + headerGrid[r][c].toString();
        }
      }
      combinedHeaders[c] = text.toLowerCase().replace(/\s+/g, &quot; &quot;).trim();
    }
    
    // Helper pencarian kolom berdasarkan kata kunci
    function findColumn(keywords) {
      for (var c = 0; c &lt; combinedHeaders.length; c++) {
        var h = combinedHeaders[c];
        for (var k = 0; k &lt; keywords.length; k++) {
          if (h.indexOf(keywords[k].toLowerCase()) !== -1) {
            return c + 1; // 1-indexed
          }
        }
      }
      return null;
    }
    
    // 3. Mapping Kolom Lengkap
    var colMap = {};
    colMap[&#039;siklus&#039;]                   = findColumn([&#039;(1)&#039;, &#039;siklus&#039;]);
    colMap[&#039;proses&#039;]                   = findColumn([&#039;(2)&#039;, &#039;proses&#039;]);
    colMap[&#039;sub_proses&#039;]               = findColumn([&#039;(3)&#039;, &#039;sub proses&#039;]);
    
    // 3 Kolom PIC Pelaksana Kontrol
    colMap[&#039;nama_pelaksana&#039;]           = findColumn([&#039;nama pelaksana&#039;, &#039;nama pic&#039;]);
    colMap[&#039;jabatan_pelaksana&#039;]        = findColumn([&#039;jabatan pelaksana&#039;, &#039;jabatan pic&#039;]);
    colMap[&#039;unit_pelaksana&#039;]           = findColumn([&#039;unit pelaksana&#039;]);
    
    // Kompatibilitas mundur
    colMap[&#039;nama_pic&#039;]                 = colMap[&#039;nama_pelaksana&#039;] || findColumn([&#039;nama pelaksana&#039;, &#039;nama pic&#039;]);
    colMap[&#039;pelaksana_kontrol&#039;]        = colMap[&#039;unit_pelaksana&#039;] || findColumn([&#039;pelaksana kontrol&#039;, &#039;unit&#039;]);
    
    colMap[&#039;ref_risk&#039;]                 = findColumn([&#039;ref. risiko&#039;, &#039;ref risiko&#039;, &#039;ref. risk&#039;, &#039;ref risk&#039;, &#039;kode risiko&#039;]);
    colMap[&#039;deskripsi_risiko&#039;]         = findColumn([&#039;deskripsi risiko&#039;, &#039;uraian risiko&#039;, &#039;deskripsi risk&#039;]);
    colMap[&#039;nilai_risiko&#039;]             = findColumn([&#039;nilai risiko&#039;, &#039;risk value&#039;, &#039;tingkat risiko&#039;]);
    colMap[&#039;risiko_kecurangan&#039;]        = findColumn([&#039;risiko kecurangan&#039;, &#039;kecurangan&#039;, &#039;fraud&#039;]);
    
    colMap[&#039;ref_control&#039;]              = findColumn([&#039;(4)&#039;, &#039;ref. control&#039;, &#039;no. ref. control&#039;, &#039;ref control&#039;]);
    
    colMap[&#039;deskripsi_kontrol&#039;]        = null;
    for (var c = 0; c &lt; combinedHeaders.length; c++) {
      var h = combinedHeaders[c];
      if (h.indexOf(&#039;(5)&#039;) !== -1 || (h.indexOf(&#039;deskripsi&#039;) !== -1 &amp;&amp; h.indexOf(&#039;risiko&#039;) === -1 &amp;&amp; h.indexOf(&#039;risk&#039;) === -1)) {
        colMap[&#039;deskripsi_kontrol&#039;] = c + 1;
        break;
      }
    }
    
    colMap[&#039;key_non_key&#039;]              = findColumn([&#039;key/non key&#039;, &#039;key / non key&#039;, &#039;key/non&#039;, &#039;key non key&#039;, &#039;tipe kontrol&#039;]);
    colMap[&#039;frekuensi&#039;]                = findColumn([&#039;(7)&#039;, &#039;frekuensi&#039;]);
    colMap[&#039;dokumen&#039;]                  = findColumn([&#039;(10)&#039;, &#039;dokumen pendukung&#039;, &#039;dokumen&#039;]);
    colMap[&#039;obyektif&#039;]                 = findColumn([&#039;(11)&#039;, &#039;obyektif csa&#039;, &#039;obyektif&#039;]);
    colMap[&#039;prosedur&#039;]                 = findColumn([&#039;(12)&#039;, &#039;prosedur csa&#039;, &#039;prosedur&#039;]);
    colMap[&#039;hasil_csa&#039;]                = findColumn([&#039;(13)&#039;, &#039;hasil csa&#039;]);
    colMap[&#039;keterangan_tidak_efektif&#039;] = findColumn([&#039;(14)&#039;, &#039;keterangan tidak&#039;]);
    colMap[&#039;root_cause&#039;]               = findColumn([&#039;(15)&#039;, &#039;root cause&#039;]);
    colMap[&#039;kertas_kerja&#039;]             = findColumn([&#039;(16)&#039;, &#039;kertas kerja&#039;]);
    colMap[&#039;remediasi&#039;]                = findColumn([&#039;(17)&#039;, &#039;rencana remediasi&#039;, &#039;remediasi&#039;]);
    
    var ctrlCol = colMap[&#039;ref_control&#039;] || 10;
    
    // 4. Bangun indeks baris berdasarkan Kode Kontrol
    var rowLookup = {};
    if (lastRow &gt; headerRowIndex) {
      var ctrlValues = sheet.getRange(headerRowIndex + 1, ctrlCol, lastRow - headerRowIndex, 1).getValues();
      for (var i = 0; i &lt; ctrlValues.length; i++) {
        var code = (ctrlValues[i][0] || &#039;&#039;).toString().trim();
        if (code) {
          rowLookup[code] = headerRowIndex + 1 + i;
        }
      }
    }
    
    var sampleFormatRow = lastRow &gt; headerRowIndex ? Math.max(headerRowIndex + 1, lastRow) : headerRowIndex + 1;
    
    function safeSetValue(cell, val) {
      if (val === undefined || val === null || val === &#039;&#039;) return;
      var finalVal = val;
      var rule = null;
      try { rule = cell.getDataValidation(); } catch (e) {}
      
      if (rule) {
        try {
          var criteria = rule.getCriteriaValues();
          if (criteria &amp;&amp; criteria.length &gt; 0 &amp;&amp; Array.isArray(criteria[0])) {
            var list = criteria[0];
            var valClean = val.toString().toLowerCase().replace(/[^a-z0-9]/g, &#039;&#039;);
            for (var i = 0; i &lt; list.length; i++) {
              if (list[i]) {
                var itemClean = list[i].toString().toLowerCase().replace(/[^a-z0-9]/g, &#039;&#039;);
                if (itemClean === valClean) {
                  finalVal = list[i];
                  break;
                }
              }
            }
          }
        } catch (e) {}
      }
      
      try {
        cell.setValue(finalVal);
      } catch (err) {
        try {
          if (rule) {
            cell.setDataValidation(rule.copy().setAllowInvalid(true).build());
          } else {
            cell.clearDataValidations();
          }
          cell.setValue(finalVal);
        } catch (err2) {
          try {
            cell.clearDataValidations();
            cell.setValue(finalVal);
          } catch (err3) {}
        }
      }
    }
    
    var updated = 0;
    var added = 0;
    
    // 5. Update atau Insert Baris Kontrol
    data.forEach(function(item) {
      if (!item.ref_control) return;
      var code = item.ref_control.toString().trim();
      var targetRow = rowLookup[code];
      
      if (!targetRow) {
        targetRow = sheet.getLastRow() + 1;
        rowLookup[code] = targetRow;
        added++;
        
        if (sampleFormatRow &lt;= sheet.getLastRow() &amp;&amp; sampleFormatRow &gt; headerRowIndex) {
          var srcRange = sheet.getRange(sampleFormatRow, 1, 1, lastCol);
          var dstRange = sheet.getRange(targetRow, 1, 1, lastCol);
          srcRange.copyTo(dstRange, SpreadsheetApp.CopyPasteType.PASTE_FORMAT, false);
          srcRange.copyTo(dstRange, SpreadsheetApp.CopyPasteType.PASTE_DATA_VALIDATION, false);
        }
      } else {
        updated++;
      }
      
      for (var key in colMap) {
        var colIndex = colMap[key];
        if (colIndex &amp;&amp; item[key] !== undefined &amp;&amp; item[key] !== null &amp;&amp; item[key] !== &#039;&#039;) {
          var cell = sheet.getRange(targetRow, colIndex);
          safeSetValue(cell, item[key]);
        }
      }
      
      // Pasang validasi dropdown Key/Non Key &amp; CSA jika belum ada
      if (colMap[&#039;key_non_key&#039;]) {
        var cellKey = sheet.getRange(targetRow, colMap[&#039;key_non_key&#039;]);
        if (!cellKey.getDataValidation()) {
          var ruleKey = SpreadsheetApp.newDataValidation()
            .requireValueInList([&#039;Key&#039;, &#039;Non Key&#039;], true)
            .setAllowInvalid(true)
            .build();
          cellKey.setDataValidation(ruleKey);
        }
      }
      
      if (colMap[&#039;hasil_csa&#039;]) {
        var cellCsa = sheet.getRange(targetRow, colMap[&#039;hasil_csa&#039;]);
        if (!cellCsa.getDataValidation()) {
          var ruleCsa = SpreadsheetApp.newDataValidation()
            .requireValueInList([&#039;Efektif&#039;, &#039;Tidak Efektif&#039;, &#039;Tidak Ada Transaksi&#039;, &#039;Sedang Berjalan&#039;, &#039;Belum DInilai&#039;, &#039;Belum Dinilai&#039;], true)
            .setAllowInvalid(true)
            .build();
          cellCsa.setDataValidation(ruleCsa);
        }
      }
    });
    
    // 6. Urutkan berdasarkan Siklus, Proses, Sub Proses, Ref Control (A-Z)
    try {
      var currentLastRow = sheet.getLastRow();
      if (currentLastRow &gt; headerRowIndex) {
        var numDataRows = currentLastRow - headerRowIndex;
        var dataRange = sheet.getRange(headerRowIndex + 1, 1, numDataRows, lastCol);
        
        var sortSpecs = [];
        if (colMap[&#039;siklus&#039;])       sortSpecs.push({column: colMap[&#039;siklus&#039;], ascending: true});
        if (colMap[&#039;proses&#039;])       sortSpecs.push({column: colMap[&#039;proses&#039;], ascending: true});
        if (colMap[&#039;sub_proses&#039;])   sortSpecs.push({column: colMap[&#039;sub_proses&#039;], ascending: true});
        if (colMap[&#039;ref_control&#039;])  sortSpecs.push({column: colMap[&#039;ref_control&#039;], ascending: true});
        
        if (sortSpecs.length &gt; 0) {
          dataRange.sort(sortSpecs);
        }
      }
    } catch (sortErr) {
      Logger.log(&quot;Info urutan data: &quot; + sortErr);
    }

    // 7. Otomatis pasang/perbarui Dropdown Unit &amp; Jabatan
    try {
      pasangSemuaDropdown(ss);
    } catch (ddErr2) {
      Logger.log(&quot;Info dropdown: &quot; + ddErr2);
    }

    return ContentService.createTextOutput(JSON.stringify({
      status: &#039;success&#039;,
      message: &#039;Berhasil sinkronisasi ke Spreadsheet: &#039; + updated + &#039; kontrol diperbarui, &#039; + added + &#039; kontrol baru ditambahkan serta seluruh baris telah diurutkan berdasarkan abjad A-Z!&#039;
    })).setMimeType(ContentService.MimeType.JSON);
    
  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({
      status: &#039;error&#039;,
      message: err.toString()
    })).setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * ==============================================================================
 * MENU TAMBAHAN GOOGLE SHEETS (DITAMPILKAN SAAT SPREADSHEET DIBUKA)
 * ==============================================================================
 */
function onOpen() {
  var ui = SpreadsheetApp.getUi();
  ui.createMenu(&quot;⚡ ICoFR Semen Tonasa&quot;)
    .addItem(&quot;🔄 Pasang Dropdown Unit &amp; Jabatan Sekarang&quot;, &quot;pasangSemuaDropdown&quot;)
    .addToUi();
}

/**
 * ==============================================================================
 * FUNGSI UTAMA: MEMASANG DROPDOWN UNIT &amp; JABATAN PADA SELURUH BARIS KONTROL
 * ==============================================================================
 * Fungsi ini dapat dijalankan langsung dengan menekan tombol Run (Jalankan) di Apps Script,
 * atau melalui menu &quot;⚡ ICoFR Semen Tonasa&quot; &gt; &quot;Pasang Dropdown Unit &amp; Jabatan Sekarang&quot;.
 */
function pasangSemuaDropdown(ss) {
  if (!ss) ss = SpreadsheetApp.getActiveSpreadsheet();
  var masterSheet = ss.getSheetByName(&quot;Master_PIC&quot;);
  if (!masterSheet) {
    Logger.log(&quot;Sheet Master_PIC belum ada.&quot;);
    return;
  }
  
  var masterData = masterSheet.getDataRange().getValues(); // [No, Nama, Jabatan, Unit, Dept]
  if (masterData.length &lt;= 1) return;
  
  var uniqueUnits = [];
  var allPositions = [];
  var unitToPositions = {};
  
  for (var i = 1; i &lt; masterData.length; i++) {
    var name = String(masterData[i][1] || &quot;&quot;).trim();
    var pos  = String(masterData[i][2] || &quot;&quot;).trim();
    var unit = String(masterData[i][3] || &quot;&quot;).trim();
    
    if (unit &amp;&amp; uniqueUnits.indexOf(unit) === -1) {
      uniqueUnits.push(unit);
    }
    if (pos &amp;&amp; allPositions.indexOf(pos) === -1) {
      allPositions.push(pos);
    }
    if (unit &amp;&amp; pos) {
      if (!unitToPositions[unit]) unitToPositions[unit] = [];
      if (unitToPositions[unit].indexOf(pos) === -1) {
        unitToPositions[unit].push(pos);
      }
    }
  }
  
  uniqueUnits.sort();
  allPositions.sort();
  
  // Rule dropdown Unit (Menampilkan panah chip dropdown di sel)
  var unitRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(uniqueUnits, true)
    .setAllowInvalid(true)
    .build();
    
  // Rule dropdown seluruh Jabatan
  var allPosRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(allPositions, true)
    .setAllowInvalid(true)
    .build();
    
  var sheets = ss.getSheets();
  for (var s = 0; s &lt; sheets.length; s++) {
    var sh = sheets[s];
    var sName = sh.getName();
    if (sName === &quot;Master_PIC&quot; || sName === &quot;Dropdownlist&quot;) continue;
    
    var lastRow = Math.max(sh.getLastRow(), 20);
    if (lastRow &lt; 3) continue;
    
    var cols = getPicColumns(sh);
    var numRows = lastRow - 2;
    
    // 1. PASANG DROPDOWN UNIT PADA SEMUA BARIS (KOLOM F)
    sh.getRange(3, cols.colUnit, numRows, 1).setDataValidation(unitRule);
    
    // 2. PASANG DROPDOWN JABATAN PADA KOLOM E (SESUAIKAN DENGAN UNIT JIKA SUDAH ADA)
    var currentUnitValues = sh.getRange(3, cols.colUnit, numRows, 1).getValues();
    for (var r = 0; r &lt; numRows; r++) {
      var rowNum = 3 + r;
      var curUnit = String(currentUnitValues[r][0] || &quot;&quot;).trim();
      var cellJabatan = sh.getRange(rowNum, cols.colJabatan);
      
      if (curUnit &amp;&amp; unitToPositions[curUnit] &amp;&amp; unitToPositions[curUnit].length &gt; 0) {
        var filteredRule = SpreadsheetApp.newDataValidation()
          .requireValueInList(unitToPositions[curUnit], true)
          .setAllowInvalid(true)
          .build();
        cellJabatan.setDataValidation(filteredRule);
      } else {
        cellJabatan.setDataValidation(allPosRule);
      }
    }
  }
  
  Logger.log(&quot;Berhasil memasang dropdown Unit dan Jabatan!&quot;);
}

/**
 * Helper mendeteksi nomor kolom PIC Pelaksana Kontrol (Unit, Jabatan, Nama)
 */
function getPicColumns(sheet) {
  var lastCol = Math.max(sheet.getLastColumn(), 26);
  var scanRows = Math.min(sheet.getLastRow(), 3);
  var headerGrid = sheet.getRange(1, 1, scanRows, lastCol).getValues();
  var cols = { colUnit: 6, colJabatan: 5, colNama: 4 }; // Default F, E, D
  
  for (var c = 0; c &lt; lastCol; c++) {
    var txt = &quot;&quot;;
    for (var r = 0; r &lt; headerGrid.length; r++) {
      if (headerGrid[r][c] !== undefined &amp;&amp; headerGrid[r][c] !== null) {
        txt += &quot; &quot; + headerGrid[r][c].toString();
      }
    }
    txt = txt.toLowerCase().trim();
    if (txt.indexOf(&quot;unit pelaksana&quot;) !== -1) {
      cols.colUnit = c + 1;
    } else if (txt.indexOf(&quot;jabatan pelaksana&quot;) !== -1) {
      cols.colJabatan = c + 1;
    } else if (txt.indexOf(&quot;nama pelaksana&quot;) !== -1 || txt.indexOf(&quot;nama pic&quot;) !== -1) {
      cols.colNama = c + 1;
    }
  }
  return cols;
}

/**
 * ==============================================================================
 * SISTEM DROPDOWN BERJENJANG OTOMATIS SAAT USER MENGEDIT SPREADSHEET (onEdit)
 * ==============================================================================
 * Memilih Unit di Kolom F -&gt; Filter Jabatan di Kolom E -&gt; Auto-fill Nama di Kolom D
 * Memilih Jabatan di Kolom E -&gt; Auto-fill Unit di Kolom F &amp; Nama di Kolom D
 */
function onEdit(e) {
  if (!e) return;
  var range = e.range;
  var sheet = range.getSheet();
  var sheetName = sheet.getName();
  
  if (sheetName === &quot;Master_PIC&quot; || sheetName === &quot;Dropdownlist&quot;) return;
  
  var row = range.getRow();
  var col = range.getColumn();
  if (row &lt;= 2) return; // Abaikan baris header
  
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var masterSheet = ss.getSheetByName(&quot;Master_PIC&quot;);
  if (!masterSheet) return;
  
  var masterData = masterSheet.getDataRange().getValues(); // [No, Nama, Jabatan, Unit, Dept]
  var cols = getPicColumns(sheet);
  
  // 1. KETIKA USER MEMILIH &quot;UNIT PELAKSANA&quot; (KOLOM F)
  if (col === cols.colUnit) {
    var selectedUnit = String(e.value || &quot;&quot;).trim();
    var cellJabatan = sheet.getRange(row, cols.colJabatan);
    var cellNama = sheet.getRange(row, cols.colNama);
    
    if (!selectedUnit) {
      // Jika dikosongkan, beri seluruh jabatan sebagai pilihan
      var allPositions = [];
      for (var a = 1; a &lt; masterData.length; a++) {
        var aPos = String(masterData[a][2] || &quot;&quot;).trim();
        if (aPos &amp;&amp; allPositions.indexOf(aPos) === -1) allPositions.push(aPos);
      }
      allPositions.sort();
      var defaultRule = SpreadsheetApp.newDataValidation()
        .requireValueInList(allPositions, true)
        .setAllowInvalid(true)
        .build();
      cellJabatan.setDataValidation(defaultRule).setValue(&quot;&quot;);
      cellNama.setValue(&quot;&quot;);
      return;
    }
    
    // Cari seluruh jabatan yang berada di bawah Unit yang dipilih
    var matchingPositions = [];
    for (var i = 1; i &lt; masterData.length; i++) {
      var pUnit = String(masterData[i][3] || &quot;&quot;).trim();
      var pPos  = String(masterData[i][2] || &quot;&quot;).trim();
      if (pUnit.toLowerCase() === selectedUnit.toLowerCase() &amp;&amp; pPos !== &quot;&quot;) {
        if (matchingPositions.indexOf(pPos) === -1) {
          matchingPositions.push(pPos);
        }
      }
    }
    
    // Pasang daftar validasi dropdown Jabatan
    if (matchingPositions.length &gt; 0) {
      var rule = SpreadsheetApp.newDataValidation()
        .requireValueInList(matchingPositions, true)
        .setAllowInvalid(true)
        .build();
      cellJabatan.setDataValidation(rule);
      
      // Jika hanya ada 1 jabatan (misal: Unit of Warehouse -&gt; SM of Warehouse), langsung pilihkan otomatis
      if (matchingPositions.length === 1) {
        cellJabatan.setValue(matchingPositions[0]);
        for (var j = 1; j &lt; masterData.length; j++) {
          if (String(masterData[j][2]).trim().toLowerCase() === matchingPositions[0].toLowerCase()) {
            cellNama.setValue(masterData[j][1] || &quot;&quot;);
            break;
          }
        }
      } else {
        // Cek apakah jabatan yang sedang terisi masih valid untuk unit baru ini
        var curJab = String(cellJabatan.getValue() || &quot;&quot;).trim();
        var isValid = matchingPositions.some(function(mp) {
          return mp.toLowerCase() === curJab.toLowerCase();
        });
        if (!isValid) {
          cellJabatan.setValue(&quot;&quot;);
          cellNama.setValue(&quot;&quot;);
        }
      }
    } else {
      cellJabatan.clearDataValidations();
    }
  }
  
  // 2. KETIKA USER MEMILIH &quot;JABATAN PELAKSANA&quot; (KOLOM E)
  if (col === cols.colJabatan) {
    var selectedJabatan = String(e.value || &quot;&quot;).trim();
    var cellUnit = sheet.getRange(row, cols.colUnit);
    var currentUnit = String(cellUnit.getValue() || &quot;&quot;).trim();
    var cellNama = sheet.getRange(row, cols.colNama);
    
    if (selectedJabatan) {
      for (var k = 1; k &lt; masterData.length; k++) {
        var pName = String(masterData[k][1] || &quot;&quot;).trim();
        var pPos  = String(masterData[k][2] || &quot;&quot;).trim();
        var pUnit = String(masterData[k][3] || &quot;&quot;).trim();
        
        if (pPos.toLowerCase() === selectedJabatan.toLowerCase()) {
          // Jika unit kosong, otomatis isi Unit-nya juga
          if (!currentUnit &amp;&amp; pUnit) {
            cellUnit.setValue(pUnit);
          }
          // Otomatis isi Nama Pelaksana Kontrol
          if (pName) {
            cellNama.setValue(pName);
          }
          break;
        }
      }
    }
  }
}

</pre>
                    </div>

                    <p class="text-slate-500 text-[11px]">
                        Setelah menempelkan kode di atas, klik tombol <strong>Simpan (Save icon)</strong> di Google Apps Script. Dropdown berjenjang akan langsung aktif otomatis saat Anda memilih unit di kolom F!
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between text-xs">
                <span class="text-slate-400">Tekan <kbd class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">ESC</kbd> untuk menutup</span>
                <button type="button" @click="showPicModal = false" class="px-4 py-2 font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

