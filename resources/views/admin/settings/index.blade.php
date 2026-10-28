<x-app-layout>
    <x-slot name="header">
        {{ __('Pengaturan Sistem') }}
    </x-slot>

    <!-- Melebarkan kanvas dari max-w-6xl menjadi max-w-7xl -->
    <div x-data="{ tab: '{{ request('tab', 'umum') }}' }" class="py-8">
        <div class="w-full">
            
            @if(session('success'))
                <div class="mb-6 px-4 py-3 text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 px-4 py-3 text-sm font-bold text-red-700 bg-red-50 border border-red-200 rounded-xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-8">
                
                <!-- KIRI: SIDEBAR MENU PENGATURAN -->
                <div class="w-full md:w-72 flex-shrink-0">
                    <div class="sticky top-24 space-y-1">
                        <h3 class="px-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Menu Konfigurasi</h3>
                        
                        <!-- Menu 1: Portal & Sistem -->
                        <button @click="tab = 'umum'" 
                                :class="tab === 'umum' ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200'" 
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Sistem & Portal
                        </button>
                        
                        <!-- Menu 2: Template Dokumen -->
                        <button @click="tab = 'template'" 
                                :class="tab === 'template' ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200'" 
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Template Dokumen
                        </button>

                        <!-- Menu 3: Manajemen Form -->
                        <button @click="tab = 'form'" 
                                :class="tab === 'form' ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200'" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all text-left group">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                Form Registrasi
                            </div>
                            <span class="px-1.5 py-0.5 text-[8px] font-black uppercase tracking-wider bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 rounded">NEW</span>
                        </button>

                        <!-- Menu 4: Tahapan KMST (Kontrol & Roadmap Inovasi) -->
                        <button @click="tab = 'tahapan'" 
                                :class="tab === 'tahapan' ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200'" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all text-left group">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                Tahapan KMST
                            </div>
                            <span class="px-1.5 py-0.5 text-[8px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 rounded">ROADMAP</span>
                        </button>
                    </div>
                </div>

                <!-- KANAN: AREA KONTEN DINAMIS -->
                <div class="flex-1 max-w-4xl">
                    
                    <!-- ============================================== -->
                    <!-- KONTEN 1: SISTEM & PORTAL -->
                    <!-- ============================================== -->
                    <div x-show="tab === 'umum'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="mb-6">
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white">Portal & Tampilan</h2>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Nyalakan atau matikan modul sistem secara seketika (Real-time).</p>
                        </div>

                        <div class="bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
                            <form action="{{ route('settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="form_type" value="tampilan">
                                @php
                                    $kmstSetting = \App\Models\Setting::where('key', 'kmst_event_active')->first();
                                    $isKmstOn = $kmstSetting ? ($kmstSetting->value === '1') : false;

                                    $bannerSetting = \App\Models\Setting::where('key', 'maintenance_banner')->first();
                                    $isBannerOn = $bannerSetting ? ($bannerSetting->value === '1') : false;
                                @endphp

                                <div class="space-y-4 p-6">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-slate-50/50 dark:bg-slate-800/20 border border-slate-100 dark:border-slate-800 rounded-xl hover:border-slate-200 dark:hover:border-slate-700 transition-colors">
                                        <div class="mb-4 sm:mb-0">
                                            <h4 class="text-base font-black text-slate-900 dark:text-white">Akses Portal Pendaftaran</h4>
                                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1 max-w-md">Buka atau tutup halaman form pendaftaran KMST Inovasi untuk seluruh karyawan Semen Tonasa.</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="kmst_event_active" value="1" class="sr-only peer" {{ $isKmstOn ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                        </label>
                                    </div>

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-slate-50/50 dark:bg-slate-800/20 border border-slate-100 dark:border-slate-800 rounded-xl hover:border-slate-200 dark:hover:border-slate-700 transition-colors">
                                        <div class="mb-4 sm:mb-0">
                                            <h4 class="text-base font-black text-slate-900 dark:text-white">Mode Pemeliharaan (Maintenance)</h4>
                                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1 max-w-md">Tampilkan spanduk peringatan merah di bagian atas aplikasi saat Anda sedang memperbaiki database.</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="maintenance_banner" value="1" class="sr-only peer" {{ $isBannerOn ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                        </label>
                                    </div>

                                    <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                                        <button type="submit" class="px-6 py-2.5 text-sm font-black text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-xl hover:bg-slate-800 dark:hover:bg-gray-200 shadow-md hover:-translate-y-0.5 transition-all">
                                            Simpan Tampilan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- KONTEN 2: TEMPLATE DOKUMEN -->
                    <!-- ============================================== -->
                    <div x-show="tab === 'template'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="mb-6">
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white">Master Template Makalah</h2>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Ganti file format Word/PDF yang akan diunduh oleh partisipan tanpa perlu mengakses server.</p>
                        </div>

                        <div class="bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden transition-colors">
                            <form action="{{ route('settings.update-templates') }}" method="POST" enctype="multipart/form-data" class="p-6">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    @php
                                        $templates = [
                                            'ss'  => ['label' => 'Sumbang Saran (SS)', 'path' => \App\Models\Setting::where('key', 'template_ss')->value('value')],
                                            'gkm' => ['label' => 'GKM', 'path' => \App\Models\Setting::where('key', 'template_gkm')->value('value')],
                                            'pkm' => ['label' => 'PKM', 'path' => \App\Models\Setting::where('key', 'template_pkm')->value('value')],
                                            'bi'  => ['label' => 'Breakthrough Innovation (BI)', 'path' => \App\Models\Setting::where('key', 'template_bi')->value('value')],
                                        ];
                                    @endphp

                                    @foreach($templates as $key => $data)
                                    <div class="p-5 border border-slate-200 dark:border-slate-700/60 rounded-xl bg-slate-50/50 dark:bg-slate-800/30 hover:border-red-300 dark:hover:border-red-500/50 transition-colors group relative">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-200">{{ $data['label'] }}</h4>
                                            
                                            @if($data['path'])
                                                <a href="{{ asset('storage/' . $data['path']) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-400 rounded-md hover:scale-105 transition-transform">
                                                    Unduh Cek
                                                </a>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-[10px] font-black uppercase tracking-widest text-slate-500 bg-slate-200 dark:bg-slate-700 dark:text-slate-400 rounded-md">
                                                    Kosong
                                                </span>
                                            @endif
                                        </div>

                                        <div class="relative w-full">
                                            <input type="file" name="template_{{ $key }}" id="file_{{ $key }}" class="hidden" onchange="document.getElementById('label_{{ $key }}').innerText = this.files[0].name" accept=".doc,.docx,.pdf">
                                            <label for="file_{{ $key }}" class="flex items-center justify-center w-full px-4 py-2.5 text-xs font-bold text-slate-600 bg-white border border-dashed border-slate-300 rounded-lg cursor-pointer hover:bg-red-50 hover:border-red-400 hover:text-red-600 dark:bg-slate-900/50 dark:border-slate-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:border-red-500 transition-all">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                <span id="label_{{ $key }}" class="truncate max-w-[180px]">Pilih file pengganti...</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                                    <button type="submit" class="px-6 py-2.5 text-sm font-black text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-xl hover:bg-slate-800 dark:hover:bg-gray-200 shadow-md hover:-translate-y-0.5 transition-all">
                                        Unggah & Timpa File
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- KONTEN 3: MANAJEMEN FORM -->
                    <!-- ============================================== -->
                    <div x-show="tab === 'form'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="mb-6">
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white">Aturan Form Registrasi</h2>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Atur pembatasan validasi untuk kolom-kolom pendaftaran (seperti maksimal anggota tim).</p>
                        </div>

                        <div class="bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden transition-colors">
                            <form action="{{ route('settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="form_type" value="aturan_form">
                                
                                @php
                                    $maxSS  = \App\Models\Setting::where('key', 'max_anggota_ss')->value('value') ?? 2;
                                    $maxGKM = \App\Models\Setting::where('key', 'max_anggota_gkm')->value('value') ?? 5;
                                @endphp

                                <div class="space-y-6 p-6">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        
                                        <!-- Input Batas SS -->
                                        <div>
                                            <label class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-2">Batas Maksimal Anggota (Tim SS)</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                </div>
                                                <input type="number" name="max_anggota_ss" value="{{ $maxSS }}" min="1" max="10" class="pl-10 w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 dark:bg-[#0d1117] dark:border-slate-700 dark:text-white font-bold" required>
                                            </div>
                                            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Termasuk ketua tim. Form akan menolak jika peserta memasukkan lebih dari jumlah ini.</p>
                                        </div>

                                        <!-- Input Batas GKM/PKM/BI -->
                                        <div>
                                            <label class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-2">Batas Maksimal Anggota (GKM/PKM/BI)</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                </div>
                                                <input type="number" name="max_anggota_gkm" value="{{ $maxGKM }}" min="1" max="15" class="pl-10 w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 dark:bg-[#0d1117] dark:border-slate-700 dark:text-white font-bold" required>
                                            </div>
                                            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Berlaku untuk 3 kategori tersebut (termasuk ketua tim).</p>
                                        </div>

                                    </div>

                                    <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                                        <button type="submit" class="px-6 py-2.5 text-sm font-black text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-xl hover:bg-slate-800 dark:hover:bg-gray-200 shadow-md hover:-translate-y-0.5 transition-all">
                                            Simpan Aturan Form
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- KONTEN 4: MANAJEMEN TAHAPAN KMST (BERSIH & AMAN) -->
                    <!-- ============================================== -->
                    <!-- Script Inisialisasi Data & Handler Tahapan KMST -->
                    <script>
                        window.__kmstInitialStages = {!! json_encode(array_values($tahapanList ?? []), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};

                        function tahapanManager() {
                            return {
                                stages: (Array.isArray(window.__kmstInitialStages) && window.__kmstInitialStages.length > 0)
                                    ? JSON.parse(JSON.stringify(window.__kmstInitialStages))
                                    : [],
                                addNewStage: function() {
                                    var nextNum = String(this.stages.length + 1);
                                    this.stages.push({
                                        id: Date.now(),
                                        nomor: nextNum,
                                        judul: 'Tahap Baru ' + nextNum,
                                        deskripsi: 'Deskripsi aktivitas pada tahapan kompetisi ini...',
                                        badge: 'Tahap Mendatang',
                                        info_bawah: 'Jadwal Segera Diumumkan',
                                        is_active: false,
                                        icon_type: 'number'
                                    });
                                    this.$nextTick(function() {
                                        var items = document.querySelectorAll('.tahapan-card-item');
                                        if (items.length > 0) {
                                            items[items.length - 1].scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        }
                                    });
                                },
                                removeStage: function(index) {
                                    if (this.stages.length <= 1) {
                                        alert('Minimal harus memiliki 1 tahapan kompetisi.');
                                        return;
                                    }
                                    if (confirm('Hapus tahapan ini dari alur kompetisi?')) {
                                        this.stages.splice(index, 1);
                                    }
                                },
                                setActive: function(index) {
                                    var targetIdx = index;
                                    this.stages.forEach(function(s, idx) {
                                        s.is_active = (idx === targetIdx);
                                        if (s.is_active && (!s.badge || s.badge === 'Tahap Mendatang' || s.badge === 'Tahap Berikutnya')) {
                                            s.badge = 'Tahap Aktif';
                                        }
                                    });
                                }
                            };
                        }

                        if (window.Alpine) {
                            window.Alpine.data('tahapanManager', tahapanManager);
                        } else {
                            document.addEventListener('alpine:init', function() {
                                window.Alpine.data('tahapanManager', tahapanManager);
                            });
                        }
                    </script>

                    <div x-show="tab === 'tahapan'" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         style="display: none;"
                         x-data="tahapanManager()">

                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-black text-gray-900 dark:text-white">Kontrol Tahapan KMST</h2>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                                    Aktifkan tahapan yang sedang berjalan (misal Tahap 2), ubah rincian, atau tambahkan tahapan baru pada portal publik.
                                </p>
                            </div>
                            <a href="{{ route('inovasi.portal') }}#tahapan" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-red-500 hover:text-[#F5333F] transition-all shadow-xs self-start sm:self-auto">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                <span>Lihat di Portal Publik</span>
                            </a>
                        </div>

                        <!-- Card 1: 1-Click Fast Stage Switcher (Aktivasi Cepat) -->
                        <div class="mb-8 p-6 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-850 to-slate-950 text-white shadow-lg border border-slate-800">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="text-xs font-black uppercase tracking-wider text-emerald-400">Peralihan Cepat (Quick Switch)</span>
                            </div>
                            <h3 class="text-lg font-black text-white">Tahapan Aktif Berjalan Saat Ini</h3>
                            <p class="text-xs text-slate-300 mt-1">Pilih tahapan yang sedang dibuka untuk partisipan, lalu klik tombol aktifkan di bawah:</p>

                            <form action="{{ route('settings.update') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="form_type" value="tahapan_kmst">
                                <input type="hidden" name="action" value="activate_stage">

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-3">
                                    @foreach(($tahapanList ?? []) as $t)
                                        <label class="cursor-pointer relative flex flex-col justify-between p-3.5 rounded-xl border transition-all {{ !empty($t['is_active']) ? 'bg-red-500/15 border-red-500 text-white shadow-md shadow-red-500/20' : 'bg-slate-800/60 border-slate-700/80 text-slate-300 hover:bg-slate-800 hover:border-slate-600' }}">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-black {{ !empty($t['is_active']) ? 'bg-red-500 text-white' : 'bg-slate-700 text-slate-300' }}">
                                                    {{ $t['nomor'] }}
                                                </span>
                                                <input type="radio" name="target_id" value="{{ $t['nomor'] }}" {{ !empty($t['is_active']) ? 'checked' : '' }} class="text-red-600 focus:ring-red-500">
                                            </div>
                                            <div>
                                                <span class="block text-xs font-bold leading-snug">{{ $t['judul'] }}</span>
                                                <span class="block text-[10px] mt-1 {{ !empty($t['is_active']) ? 'text-emerald-400 font-extrabold' : 'text-slate-400' }}">
                                                    {{ !empty($t['is_active']) ? '● SEDANG AKTIF' : ($t['badge'] ?? 'Tahap Mendatang') }}
                                                </span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="mt-4 flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 text-xs font-black text-white bg-gradient-to-r from-[#F5333F] to-rose-600 hover:from-red-600 hover:to-rose-700 rounded-xl shadow-md shadow-red-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                                        <span>Aktifkan Tahap Terpilih Sekarang</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Card 2: Form Lengkap (Edit Konten & Tambah Tahapan Baru) -->
                        <div class="bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Daftar &amp; Rincian Tahapan</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ubah teks kartu, nomor, badge, catatan bawah, atau tambah tahapan baru.</p>
                                </div>
                                <button type="button" @click="addNewStage()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-[#F5333F] bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 rounded-xl transition-colors self-start sm:self-auto cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                                    <span>Tambah Tahapan Baru</span>
                                </button>
                            </div>

                            <form action="{{ route('settings.update') }}" method="POST" @submit="$refs.tahapanInput.value = JSON.stringify(stages)">
                                @csrf
                                <input type="hidden" name="form_type" value="tahapan_kmst">
                                <input type="hidden" name="tahapan_data" :value="JSON.stringify(stages)" x-ref="tahapanInput">

                                <div class="space-y-4">
                                    <template x-for="(stage, index) in stages" :key="stage.id || index">
                                        <div class="tahapan-card-item p-4 sm:p-5 rounded-2xl border transition-all"
                                             :class="stage.is_active ? 'border-[#F5333F] bg-red-50/20 dark:bg-red-500/5 shadow-xs' : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/40'">
                                            
                                            <!-- Baris Header Kartu Editor -->
                                            <div class="flex items-center justify-between gap-3 mb-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl font-black text-sm"
                                                          :class="stage.is_active ? 'bg-[#F5333F] text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                                          x-text="stage.nomor || (index + 1)"></span>
                                                    <span class="text-sm font-black text-slate-900 dark:text-white" x-text="'Tahapan ' + (index + 1)"></span>
                                                    
                                                    <span x-show="stage.is_active" class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                                        Sedang Aktif
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <!-- Tombol Jadikan Aktif -->
                                                    <button type="button" @click="setActive(index)" 
                                                            :class="stage.is_active ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:text-emerald-600'"
                                                            class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition-colors cursor-pointer">
                                                        <span x-text="stage.is_active ? '✓ Tahap Ini Aktif' : 'Pilih Jadi Aktif'"></span>
                                                    </button>

                                                    <!-- Tombol Hapus -->
                                                    <button type="button" @click="removeStage(index)" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors cursor-pointer" title="Hapus tahapan">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Field Input Tahap -->
                                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
                                                <!-- Nomor / Urutan -->
                                                <div class="sm:col-span-2">
                                                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Nomor / Urut</label>
                                                    <input type="text" x-model="stage.nomor" class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-red-500 focus:border-red-500">
                                                </div>

                                                <!-- Judul Tahapan -->
                                                <div class="sm:col-span-5">
                                                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Judul Tahapan</label>
                                                    <input type="text" x-model="stage.judul" placeholder="Contoh: Pendaftaran & Upload" class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-red-500 focus:border-red-500">
                                                </div>

                                                <!-- Badge Status -->
                                                <div class="sm:col-span-3">
                                                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Badge Kategori</label>
                                                    <input type="text" x-model="stage.badge" placeholder="Contoh: Tahap Aktif" class="w-full px-3 py-2 text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-red-500 focus:border-red-500">
                                                </div>

                                                <!-- Ikon Tipe -->
                                                <div class="sm:col-span-2">
                                                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Tipe Ikon</label>
                                                    <select x-model="stage.icon_type" class="w-full px-2 py-2 text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-red-500 focus:border-red-500">
                                                        <option value="number">Angka</option>
                                                        <option value="trophy">Piala 🏆</option>
                                                    </select>
                                                </div>

                                                <!-- Deskripsi -->
                                                <div class="sm:col-span-7">
                                                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Deskripsi Singkat</label>
                                                    <textarea x-model="stage.deskripsi" rows="2" placeholder="Deskripsikan aktivitas tahapan..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-red-500 focus:border-red-500"></textarea>
                                                </div>

                                                <!-- Catatan / Info Bawah -->
                                                <div class="sm:col-span-5">
                                                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Catatan / Info Footer</label>
                                                    <input type="text" x-model="stage.info_bawah" placeholder="Contoh: Batas Akhir: 15 Des 2026" class="w-full px-3 py-2 text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-red-500 focus:border-red-500">
                                                    <p class="text-[10px] text-slate-400 mt-1">Muncul di garis bawah kartu tahapan.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                                    <button type="button" @click="addNewStage()" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                                        <span>+ Tambah Tahapan</span>
                                    </button>

                                    <button type="submit" class="px-6 py-2.5 text-sm font-black text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-xl hover:bg-slate-800 dark:hover:bg-gray-200 shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                                        Simpan Perubahan Tahapan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
