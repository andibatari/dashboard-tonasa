<x-app-layout>
    <x-slot name="header">
        <div class="min-w-0">
            <div class="text-xs sm:text-sm md:text-base font-bold leading-tight text-slate-900 dark:text-white uppercase tracking-wider truncate">
                System Development &bull; Monitoring System
            </div>
            <p class="hidden sm:block text-[11px] text-slate-500 dark:text-slate-400 truncate">
                Dasbor pemantauan sertifikasi terintegrasi dan tindak lanjut temuan audit (SPPT SNI) berbasis integrasi Spreadsheet.
            </p>
        </div>
    </x-slot>

    {{-- Pesan Flash Notifikasi --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 mb-4 text-sm font-medium text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-2xl dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 dark:hover:text-emerald-200 text-lg">&times;</button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 mb-4 text-sm font-medium text-red-800 bg-red-50 border border-red-200 rounded-2xl dark:bg-red-950/40 dark:border-red-800 dark:text-red-300">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path></svg>
                <span>{{ $errors->first() }}</span>
            </div>
            <button @click="show = false" class="text-red-600 hover:text-red-800 dark:hover:text-red-200 text-lg">&times;</button>
        </div>
    @endif

    {{-- Container Alpine.js Utama untuk Interaktivitas Dasbor & Spreadsheet --}}
    <div x-data="systemDevelopmentApp(@js($certificates), @js($auditFindings), @js($auditCategoryStats))" x-init="initDashboard()" class="space-y-6">

        {{-- ==================================================================== --}}
        {{-- SWITCHER SUB-MENU UTAMA: 1. MONITORING SERTIFIKAT | 2. TEMUAN AUDIT  --}}
        {{-- ==================================================================== --}}
        <div class="bg-white dark:bg-[#1A1A1A] p-2 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar w-full sm:w-auto">
                <button type="button" @click="activeModule = 'certificates'; $nextTick(() => updateCertCharts())" 
                    :class="activeModule === 'certificates' 
                        ? 'bg-[#F5333F] text-white shadow-sm font-bold' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-semibold'"
                    class="px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2.5 transition-all whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"></path></svg>
                    <span>Monitoring Sertifikat</span>
                    <span class="px-2 py-0.5 text-[11px] rounded-full font-mono" :class="activeModule === 'certificates' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'">
                        {{ $totalCertificates }}
                    </span>
                </button>

                <button type="button" @click="activeModule = 'audit'; $nextTick(() => updateAuditCharts())" 
                    :class="activeModule === 'audit' 
                        ? 'bg-[#F5333F] text-white shadow-sm font-bold' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-semibold'"
                    class="px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2.5 transition-all whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path></svg>
                    <span>Temuan Audit (SPPT SNI)</span>
                    <span class="px-2 py-0.5 text-[11px] rounded-full font-mono" :class="activeModule === 'audit' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'">
                        {{ $totalAudit }}
                    </span>
                </button>
            </div>

            {{-- Action Buttons Sesuai Sub-Menu yang Aktif --}}
            <div class="flex flex-wrap items-center gap-2">
                <template x-if="activeModule === 'certificates'">
                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        <button type="button" @click="$dispatch('open-sync-modal')" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white transition bg-emerald-600 rounded-xl shadow-sm hover:bg-emerald-700 shadow-emerald-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                            Tarik Spreadsheet
                        </button>
                        <button type="button" @click="$dispatch('open-upload-modal')" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 transition bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700">
                            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path></svg>
                            Import
                        </button>
                        <a href="{{ route('busdev.system-development.export-excel') }}" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 transition bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path></svg>
                            Export
                        </a>
                        <button type="button" @click="$dispatch('open-add-modal')" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white transition bg-[#F5333F] rounded-xl shadow-sm hover:bg-red-600 shadow-red-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                            Tambah
                        </button>
                    </div>
                </template>

                <template x-if="activeModule === 'audit'">
                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        <button type="button" @click="$dispatch('open-sync-audit-modal')" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white transition bg-emerald-600 rounded-xl shadow-sm hover:bg-emerald-700 shadow-emerald-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                            Tarik Spreadsheet Temuan
                        </button>
                        <button type="button" @click="$dispatch('open-add-audit-modal')" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white transition bg-[#F5333F] rounded-xl shadow-sm hover:bg-red-600 shadow-red-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                            Tambah Temuan
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- MODUL 1: MONITORING SERTIFIKAT                                       --}}
        {{-- ==================================================================== --}}
        <div x-show="activeModule === 'certificates'" class="space-y-5">
            
            {{-- Info Status Tautan Spreadsheet Tersimpan Sertifikat --}}
            @if(!empty($savedSheetsUrl))
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl text-xs text-slate-600 dark:text-slate-400">
                    <div class="flex items-center gap-2 truncate">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 shrink-0">
                            ✓ Tersimpan
                        </span>
                        <span class="font-medium text-slate-700 dark:text-slate-300 truncate">
                            Spreadsheet Sertifikat: <span class="text-slate-500 dark:text-slate-400 font-mono">{{ Str::limit($savedSheetsUrl, 65) }}</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-right shrink-0">
                        <form action="{{ route('busdev.system-development.sync-sheets') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="sheets_url" value="{{ $savedSheetsUrl }}">
                            <button type="submit" class="font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:underline">
                                Sinkronkan Sekarang &rarr;
                            </button>
                        </form>
                        <a href="{{ $savedSheetsUrl }}" target="_blank" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            Buka Sheet &nearr;
                        </a>
                    </div>
                </div>
            @endif

            {{-- Navigasi Tab Internal Sertifikat --}}
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800">
                <div class="flex gap-4">
                    <button type="button" @click="certTab = 'dashboard'; $nextTick(() => updateCertCharts())" 
                        :class="certTab === 'dashboard' ? 'border-[#F5333F] text-[#F5333F] dark:border-red-500 dark:text-red-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white'"
                        class="py-3 px-1 border-b-2 text-xs sm:text-sm flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"></path></svg>
                        Dasbor Monitoring Sertifikat
                    </button>
                    <button type="button" @click="certTab = 'spreadsheet'" 
                        :class="certTab === 'spreadsheet' ? 'border-[#F5333F] text-[#F5333F] dark:border-red-500 dark:text-red-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white'"
                        class="py-3 px-1 border-b-2 text-xs sm:text-sm flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125"></path></svg>
                        Data Spreadsheet Sertifikat
                        <span class="px-2 py-0.5 text-xs bg-slate-100 dark:bg-slate-800 rounded-full font-mono text-slate-700 dark:text-slate-300" x-text="certificates.length"></span>
                    </button>
                </div>
            </div>

            {{-- Tab Sertifikat 1: Dasbor Monitoring --}}
            <div x-show="certTab === 'dashboard'" class="space-y-5">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    
                    {{-- Kolom Kiri: Filter & Quick Links --}}
                    <div class="lg:col-span-3 space-y-4">
                        <div class="p-5 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xs font-extrabold tracking-wider uppercase text-slate-900 dark:text-white">
                                    Filter Sertifikat
                                </h3>
                                <button type="button" @click="resetFilters()" class="text-xs text-[#F5333F] hover:underline font-semibold">
                                    Reset Filter
                                </button>
                            </div>

                            <div class="space-y-3">
                                {{-- Nama Sertifikat --}}
                                <div class="relative" x-data="{ openName: false }">
                                    <label class="block mb-1 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                        Nama Sertifikat
                                    </label>
                                    <button type="button" @click="openName = !openName; openStatus = false; openDate = false" 
                                        class="w-full flex items-center justify-between px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium transition shadow-sm">
                                        <span class="truncate" x-text="selectedName || 'Semua Nama Sertifikat'">Semua Nama Sertifikat</span>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': openName}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                    <div x-show="openName" @click.away="openName = false" 
                                        class="absolute left-0 right-0 z-30 mt-1.5 p-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-56 overflow-y-auto space-y-1">
                                        <button type="button" @click="selectedName = ''; openName = false; updateCertCharts()" 
                                            :class="!selectedName ? 'bg-red-50 text-[#F5333F] dark:bg-red-500/10 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
                                            class="w-full text-left px-2.5 py-1.5 text-xs rounded-lg transition">
                                            &bull; Semua Sertifikat
                                        </button>
                                        @foreach($uniqueNames as $n)
                                            <button type="button" @click="selectedName = '{{ addslashes($n) }}'; openName = false; updateCertCharts()" 
                                                :class="selectedName === '{{ addslashes($n) }}' ? 'bg-red-50 text-[#F5333F] dark:bg-red-500/10 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
                                                class="w-full text-left px-2.5 py-1.5 text-xs rounded-lg transition truncate">
                                                {{ $n }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Status Validitas --}}
                                <div class="relative" x-data="{ openStatus: false }">
                                    <label class="block mb-1 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                        Status Validitas
                                    </label>
                                    <button type="button" @click="openStatus = !openStatus; openName = false; openDate = false" 
                                        class="w-full flex items-center justify-between px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium transition shadow-sm">
                                        <span class="truncate" x-text="selectedStatuses.length === 3 ? 'Status Validitas (Semua)' : (selectedStatuses.length === 0 ? 'Pilih Status' : 'Status (' + selectedStatuses.length + ' dipilih)')">
                                            Status Validitas (Semua)
                                        </span>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': openStatus}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                    <div x-show="openStatus" @click.away="openStatus = false" 
                                        class="absolute left-0 right-0 z-30 mt-1.5 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl space-y-2.5">
                                        <label class="flex items-center gap-2.5 text-xs font-bold text-slate-900 dark:text-white cursor-pointer pb-1.5 border-b border-slate-100 dark:border-slate-700">
                                            <input type="checkbox" :checked="selectedStatuses.length === 3" @change="toggleAllStatuses()" class="rounded border-slate-300 dark:border-slate-600 text-[#F5333F] focus:ring-red-500">
                                            <span>Pilih Semua Status</span>
                                        </label>
                                        <label class="flex items-center gap-2.5 text-xs text-slate-700 dark:text-slate-300 cursor-pointer hover:text-slate-900 dark:hover:text-white">
                                            <input type="checkbox" value="Renewal" x-model="selectedStatuses" @change="updateCertCharts()" class="rounded border-slate-300 dark:border-slate-600 text-amber-500 focus:ring-amber-500">
                                            <span class="inline-flex items-center gap-1.5 font-medium">
                                                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Renewal
                                            </span>
                                        </label>
                                        <label class="flex items-center gap-2.5 text-xs text-slate-700 dark:text-slate-300 cursor-pointer hover:text-slate-900 dark:hover:text-white">
                                            <input type="checkbox" value="Expired" x-model="selectedStatuses" @change="updateCertCharts()" class="rounded border-slate-300 dark:border-slate-600 text-red-500 focus:ring-red-500">
                                            <span class="inline-flex items-center gap-1.5 font-medium">
                                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Expired
                                            </span>
                                        </label>
                                        <label class="flex items-center gap-2.5 text-xs text-slate-700 dark:text-slate-300 cursor-pointer hover:text-slate-900 dark:hover:text-white">
                                            <input type="checkbox" value="Aktif" x-model="selectedStatuses" @change="updateCertCharts()" class="rounded border-slate-300 dark:border-slate-600 text-emerald-500 focus:ring-emerald-500">
                                            <span class="inline-flex items-center gap-1.5 font-medium">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aktif
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Tanggal Expired --}}
                                <div class="relative" x-data="{ openDate: false, dateSearch: '' }">
                                    <label class="block mb-1 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                        Tanggal Expired
                                    </label>
                                    <button type="button" @click="openDate = !openDate; openName = false; openStatus = false" 
                                        class="w-full flex items-center justify-between px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium transition shadow-sm">
                                        <span class="truncate" x-text="selectedDates.length === 0 ? 'Tanggal Expired (Semua)' : 'Expired (' + selectedDates.length + ' dipilih)'">
                                            Tanggal Expired (Semua)
                                        </span>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': openDate}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                    <div x-show="openDate" @click.away="openDate = false" 
                                        class="absolute left-0 right-0 z-30 mt-1.5 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl space-y-2">
                                        <input type="text" x-model="dateSearch" placeholder="Cari tanggal..." 
                                            class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-white">
                                        <div class="max-h-44 overflow-y-auto space-y-1.5 pt-1">
                                            <template x-for="d in filteredDateOptions(dateSearch)" :key="d">
                                                <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                                                    <input type="checkbox" :value="d" x-model="selectedDates" @change="updateCertCharts()" class="rounded border-slate-300 dark:border-slate-600 text-[#F5333F] focus:ring-red-500">
                                                    <span x-text="d"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Pintas File Sertifikat (Mobile Friendly 4-col) --}}
                        <div class="p-5 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm space-y-4">
                            <h3 class="text-xs font-extrabold tracking-wider uppercase text-slate-900 dark:text-white">
                                Akses Cepat File
                            </h3>
                            
                            {{-- ISO --}}
                            <div>
                                <span class="block text-[11px] font-bold text-blue-600 dark:text-blue-400 mb-1.5 uppercase">ISO Integrasi</span>
                                <div class="grid grid-cols-4 sm:grid-cols-5 gap-1.5">
                                    @foreach($buttonGroups['FILE SERTIFIKAT ISO INTERGRASI']['items'] as $btn)
                                        <a href="{{ $btn->link_sertifikat ?: '#' }}" target="_blank" 
                                            class="flex items-center justify-center py-1.5 text-[11px] font-bold rounded-lg transition shadow-sm bg-blue-600 hover:bg-blue-700 text-white" 
                                            title="{{ $btn->kode }} - {{ $btn->jenis_sertifikat }}">
                                            {{ $btn->kode_display }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            {{-- SNI --}}
                            <div>
                                <span class="block text-[11px] font-bold text-lime-600 dark:text-lime-400 mb-1.5 uppercase">SNI Produk</span>
                                <div class="grid grid-cols-4 sm:grid-cols-5 gap-1.5">
                                    @foreach($buttonGroups['FILE SERTIFIKAT SNI']['items'] as $btn)
                                        <a href="{{ $btn->link_sertifikat ?: '#' }}" target="_blank" 
                                            class="flex items-center justify-center py-1.5 text-[11px] font-extrabold rounded-lg transition shadow-sm bg-lime-400 hover:bg-lime-500 text-slate-900" 
                                            title="{{ $btn->kode }} - {{ $btn->jenis_sertifikat }}">
                                            {{ $btn->kode_display }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            {{-- TKDN --}}
                            <div>
                                <span class="block text-[11px] font-bold text-red-600 dark:text-red-400 mb-1.5 uppercase">TKDN</span>
                                <div class="grid grid-cols-4 sm:grid-cols-5 gap-1.5">
                                    @foreach($buttonGroups['FILE SERTIFIKAT TKDN']['items'] as $btn)
                                        <a href="{{ $btn->link_sertifikat ?: '#' }}" target="_blank" 
                                            class="flex items-center justify-center py-1.5 text-[11px] font-bold rounded-lg transition shadow-sm bg-red-600 hover:bg-red-700 text-white" 
                                            title="{{ $btn->kode }} - {{ $btn->jenis_sertifikat }}">
                                            {{ $btn->kode_display }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Green Label --}}
                            <div>
                                <span class="block text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mb-1.5 uppercase">Green Label</span>
                                <div class="grid grid-cols-4 sm:grid-cols-5 gap-1.5">
                                    @foreach($buttonGroups['FILE SERTIFIKAT GREEN LABEL']['items'] as $btn)
                                        <a href="{{ $btn->link_sertifikat ?: '#' }}" target="_blank" 
                                            class="flex items-center justify-center py-1.5 text-[11px] font-bold rounded-lg transition shadow-sm bg-emerald-600 hover:bg-emerald-700 text-white" 
                                            title="{{ $btn->kode }} - {{ $btn->jenis_sertifikat }}">
                                            {{ $btn->kode_display }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: 4 Grafik Sertifikat & Tabel Ringkasan --}}
                    <div class="lg:col-span-9 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">
                            
                            {{-- Speedometer Total --}}
                            <div class="lg:col-span-5 p-5 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col items-center justify-center relative overflow-hidden">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center mb-1">
                                    JUMLAH SERTIFIKAT
                                </h4>
                                <div class="relative w-full h-44 flex items-center justify-center mt-2">
                                    <canvas id="gaugeCertificatesChart"></canvas>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pt-8 pointer-events-none">
                                        <span class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight" x-text="totalFilteredCertCount">
                                            {{ $totalCertificates }}
                                        </span>
                                        <span class="text-[10px] uppercase font-bold text-emerald-600 dark:text-lime-400 mt-0.5">Total Sertifikat</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Bar Jenis --}}
                            <div class="lg:col-span-7 p-5 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center mb-2">
                                    JENIS SERTIFIKAT
                                </h4>
                                <div class="relative w-full h-44">
                                    <canvas id="jenisBarChart"></canvas>
                                </div>
                            </div>

                            {{-- Donut Status --}}
                            <div class="lg:col-span-5 p-5 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col items-center justify-center">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center mb-2">
                                    STATUS VALIDITAS
                                </h4>
                                <div class="relative w-full h-48 flex items-center justify-center">
                                    <canvas id="statusDonutChart"></canvas>
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none pr-16 sm:pr-20">
                                        <span class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400" x-text="activeCertCount">
                                            {{ $activeCount }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Bar Lembaga Penerbit --}}
                            <div class="lg:col-span-7 p-5 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center mb-2">
                                    LEMBAGA PENERBIT
                                </h4>
                                <div class="relative w-full h-48">
                                    <canvas id="lembagaBarChart"></canvas>
                                </div>
                            </div>

                        </div>

                        {{-- Tabel Sertifikat --}}
                        <div class="bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-900/40">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Pencarian:</span>
                                    <div class="relative w-64">
                                        <input type="text" x-model="searchKeyword" @input="updateCertCharts()" placeholder="Cari kode, nomor, atau nama..." 
                                            class="w-full px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#F5333F]">
                                        <button x-show="searchKeyword" @click="searchKeyword = ''; updateCertCharts()" class="absolute right-2.5 top-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs">&times;</button>
                                    </div>
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    Menampilkan <span class="font-bold text-[#F5333F] dark:text-red-400" x-text="filteredCertificates.length"></span> dari <span x-text="certificates.length"></span> sertifikat
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-200">
                                    <thead>
                                        <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200/80 dark:border-slate-800 uppercase tracking-wider text-[11px]">
                                            <th class="py-3 px-4 cursor-pointer select-none" @click="sortCertBy('kode')">
                                                <div class="flex items-center gap-1">
                                                    <span>Kode</span>
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg>
                                                </div>
                                            </th>
                                            <th class="py-3 px-4">Jenis Sertifikat</th>
                                            <th class="py-3 px-4">Nomor Sertifikat</th>
                                            <th class="py-3 px-4">Tanggal Expired</th>
                                            <th class="py-3 px-4">Tanggal Terbit</th>
                                            <th class="py-3 px-4 text-center">Status Validitas</th>
                                            <th class="py-3 px-4 text-center">Hari Tersisa</th>
                                            <th class="py-3 px-4 text-center">File Sertifikat</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                                        <template x-for="item in paginatedCertificates" :key="item.id">
                                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                                <td class="py-3 px-4 font-bold whitespace-nowrap">
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-mono text-[11px]" x-text="item.kode"></span>
                                                </td>
                                                <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white whitespace-nowrap" x-text="item.jenis_sertifikat"></td>
                                                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 font-mono whitespace-nowrap" x-text="item.nomor_sertifikat || '-'"></td>
                                                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 whitespace-nowrap" x-text="formatDate(item.tanggal_expired)"></td>
                                                <td class="py-3 px-4 text-slate-500 dark:text-slate-400 whitespace-nowrap" x-text="formatDate(item.tanggal_terbit)"></td>
                                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                                    <span :class="{
                                                        'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800': item.status_validitas === 'Aktif',
                                                        'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800': item.status_validitas === 'Renewal',
                                                        'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/40 dark:text-red-400 dark:border-red-800': item.status_validitas === 'Expired',
                                                    }" class="px-2.5 py-1 text-[11px] font-bold rounded-full">
                                                        <span x-text="item.status_validitas"></span>
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-center font-bold whitespace-nowrap">
                                                    <span :class="item.hari_tersisa < 0 ? 'text-red-600 bg-red-50 dark:bg-red-950/60 dark:text-red-400 px-2 py-0.5 rounded font-mono' : (item.hari_tersisa <= 60 ? 'text-amber-600 bg-amber-50 dark:bg-amber-950/60 dark:text-amber-400 px-2 py-0.5 rounded font-mono' : 'text-slate-700 dark:text-slate-300 font-mono')" x-text="item.hari_tersisa !== null ? item.hari_tersisa : '-'"></span>
                                                </td>
                                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                                    <template x-if="item.link_sertifikat">
                                                        <a :href="item.link_sertifikat" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 rounded-lg transition-colors">
                                                            Buka File &nearr;
                                                        </a>
                                                    </template>
                                                    <template x-if="!item.link_sertifikat">
                                                        <span class="text-slate-400">-</span>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="filteredCertificates.length === 0">
                                            <tr>
                                                <td colspan="8" class="py-10 text-center text-slate-500 text-xs">
                                                    Tidak ada data sertifikat yang cocok dengan kriteria filter.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            <div class="p-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 bg-slate-50/40 dark:bg-slate-900/30">
                                <div>
                                    Halaman <span class="font-bold text-slate-900 dark:text-white" x-text="certPage"></span> dari <span class="font-bold text-slate-900 dark:text-white" x-text="certTotalPages"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="certPage = Math.max(1, certPage - 1)" :disabled="certPage === 1" 
                                        class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 disabled:opacity-40">
                                        &larr; Prev
                                    </button>
                                    <button type="button" @click="certPage = Math.min(certTotalPages, certPage + 1)" :disabled="certPage === certTotalPages" 
                                        class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 disabled:opacity-40">
                                        Next &rarr;
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Tab Sertifikat 2: Data Spreadsheet & Input --}}
            <div x-show="certTab === 'spreadsheet'" class="space-y-4" style="display: none;">
                <div class="p-4 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="$dispatch('open-add-modal')" 
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-white bg-[#F5333F] rounded-xl shadow-sm hover:bg-red-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                            Tambah Baris Sertifikat
                        </button>
                        <button type="button" @click="$dispatch('open-sync-modal')" 
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl hover:bg-emerald-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                            Sinkronkan Google Sheets
                        </button>
                    </div>

                    <div class="relative w-full md:w-72">
                        <input type="text" x-model="sheetSearch" placeholder="Cari dalam spreadsheet..." 
                            class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-[#F5333F] text-slate-900 dark:text-white">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs whitespace-nowrap divide-y divide-slate-200 dark:divide-slate-800 font-sans">
                            <thead class="bg-slate-50 dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 font-bold uppercase text-[10.5px]">
                                <tr>
                                    <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800 text-center">Kode</th>
                                    <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800">Jenis Sertifikat</th>
                                    <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800">Nama :</th>
                                    <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800">Nomor Sertifikat</th>
                                    <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800">Nama Produk</th>
                                    <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800">Lembaga Penerbit</th>
                                    <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800">Tanggal Terbit</th>
                                    <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800">Tanggal Expired</th>
                                    <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800">Link Sertifikat</th>
                                    <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800 text-center">Status</th>
                                    <th class="py-3 px-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                                <template x-for="item in filteredSheetData(sheetSearch)" :key="item.id">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                        <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800 font-bold font-mono text-center" x-text="item.kode"></td>
                                        <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800 font-medium text-slate-900 dark:text-white" x-text="item.jenis_sertifikat"></td>
                                        <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800 font-semibold" x-text="item.kategori_singkat || '-'"></td>
                                        <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800 font-mono text-xs" x-text="item.nomor_sertifikat || '-'"></td>
                                        <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800" x-text="item.nama_produk || '-'"></td>
                                        <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800" x-text="item.lembaga_penerbit || '-'"></td>
                                        <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800" x-text="formatDate(item.tanggal_terbit)"></td>
                                        <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800" x-text="formatDate(item.tanggal_expired)"></td>
                                        <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800">
                                            <template x-if="item.link_sertifikat">
                                                <a :href="item.link_sertifikat" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 font-medium">
                                                    <span class="truncate max-w-[140px]" x-text="item.link_sertifikat"></span>
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            </template>
                                            <template x-if="!item.link_sertifikat"><span class="text-slate-400">-</span></template>
                                        </td>
                                        <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800 text-center">
                                            <span :class="{
                                                'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800': item.status_validitas === 'Aktif',
                                                'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-800': item.status_validitas === 'Renewal',
                                                'bg-red-50 text-red-800 dark:bg-red-950/50 dark:text-red-400 border border-red-200 dark:border-red-800': item.status_validitas === 'Expired',
                                            }" class="px-2.5 py-0.5 font-bold rounded-full text-[11px]" x-text="item.status_validitas"></span>
                                        </td>
                                        <td class="py-2.5 px-3.5 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" @click="editCertItem(item)" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold text-xs">
                                                    Edit
                                                </button>
                                                <span class="text-slate-300 dark:text-slate-700">|</span>
                                                <form :action="'{{ url('busdev/system-development/certificate') }}/' + item.id" method="POST" onsubmit="return confirm('Hapus data sertifikat ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-semibold text-xs">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================================================================== --}}
        {{-- MODUL 2: MONITORING TINDAK LANJUT TEMUAN AUDIT (SPPT SNI)             --}}
        {{-- ==================================================================== --}}
        <div x-show="activeModule === 'audit'" class="space-y-5" style="display: none;">
            
            {{-- Info Status Tautan Spreadsheet Tersimpan Audit --}}
            @if(!empty($savedAuditSheetsUrl))
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl text-xs text-slate-600 dark:text-slate-400">
                    <div class="flex items-center gap-2 truncate">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 shrink-0">
                            ✓ Tersimpan
                        </span>
                        <span class="font-medium text-slate-700 dark:text-slate-300 truncate">
                            Spreadsheet Temuan Audit: <span class="text-slate-500 dark:text-slate-400 font-mono">{{ Str::limit($savedAuditSheetsUrl, 65) }}</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-right shrink-0">
                        <form action="{{ route('busdev.system-development.sync-audit-sheets') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="sheets_url" value="{{ $savedAuditSheetsUrl }}">
                            <button type="submit" class="font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:underline">
                                Sinkronkan Sekarang &rarr;
                            </button>
                        </form>
                        <a href="{{ $savedAuditSheetsUrl }}" target="_blank" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            Buka Sheet &nearr;
                        </a>
                    </div>
                </div>
            @endif

            {{-- 3 KARTU STATISTIK KPI UTAMA --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Card 1: Total Temuan --}}
                <div class="p-4 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Temuan Audit</span>
                        <div class="text-2xl font-black text-slate-900 dark:text-white mt-1" x-text="auditFindings.length">
                            {{ $totalAudit }}
                        </div>
                        <span class="text-[11px] text-slate-400">Asesmen SPPT SNI</span>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                    </div>
                </div>

                {{-- Card 2: Belum Lengkap --}}
                <div class="p-4 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Belum Lengkap</span>
                        <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1" x-text="auditBelumLengkapCount">
                            {{ $auditBelumLengkap }}
                        </div>
                        <span class="text-[11px] text-rose-500 dark:text-rose-400 font-semibold" x-text="auditFindings.length ? Math.round((auditBelumLengkapCount / auditFindings.length) * 100) + '% dari total' : '0%'">
                            90% dari total
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path></svg>
                    </div>
                </div>

                {{-- Card 3: Lengkap --}}
                <div class="p-4 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Lengkap (Closed)</span>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1" x-text="auditLengkapCount">
                            {{ $auditLengkap }}
                        </div>
                        <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold" x-text="auditFindings.length ? Math.round((auditLengkapCount / auditFindings.length) * 100) + '% selesai' : '0%'">
                            10% selesai
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                {{-- Card 4: Kategori Breakdown --}}
                <div class="p-4 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Komposisi Kategori</span>
                        <div class="flex items-center gap-2 mt-1 text-xs font-bold">
                            <span class="text-red-600 dark:text-red-400">4 Major</span>
                            <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                            <span class="text-amber-500">3 Minor</span>
                            <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                            <span class="text-emerald-500">3 Obs</span>
                        </div>
                        <span class="text-[11px] text-slate-400">Kategori Temuan</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"></path></svg>
                    </div>
                </div>

            </div>

            {{-- 3 GRAFIK UTAMA TEMUAN AUDIT (RESPONSIVE & SESUAI SCREENSHOT) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                
                {{-- Grafik 1: Persentase by Category (Donut) --}}
                <div class="p-5 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col items-center">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 text-center mb-1">
                        Persentase by Category
                    </h4>
                    <p class="text-[11px] text-slate-400 mb-2">Major, Minor, Observation</p>
                    <div class="relative w-full h-56 flex items-center justify-center">
                        <canvas id="auditCategoryDonutChart"></canvas>
                    </div>
                </div>

                {{-- Grafik 2: Persentase Realisasi (Donut) --}}
                <div class="p-5 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col items-center">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 text-center mb-1">
                        Persentase Realisasi
                    </h4>
                    <p class="text-[11px] text-slate-400 mb-2">Belum Lengkap vs Lengkap</p>
                    <div class="relative w-full h-56 flex items-center justify-center">
                        <canvas id="auditRealisasiDonutChart"></canvas>
                    </div>
                </div>

                {{-- Grafik 3: Realisasi per Category (Horizontal Stacked Bar) --}}
                <div class="p-5 bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 text-center mb-1">
                        Realisasi per Category
                    </h4>
                    <p class="text-[11px] text-slate-400 text-center mb-2">Perbandingan Status per Kategori</p>
                    <div class="relative w-full h-56">
                        <canvas id="auditStackedBarChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- TABEL RINCIAN TINDAK LANJUT TEMUAN AUDIT DENGAN FILTER --}}
            <div class="bg-white dark:bg-[#1A1A1A] border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                
                {{-- Toolbar & Filter Tabel Temuan Audit --}}
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-900/40">
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Filter Kategori --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Kategori:</span>
                            <select x-model="auditCategoryFilter" @change="updateAuditFilter()" 
                                class="px-2.5 py-1.5 text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white">
                                <option value="all">Semua Kategori</option>
                                <option value="Major">Major</option>
                                <option value="Minor">Minor</option>
                                <option value="Observation">Observation</option>
                            </select>
                        </div>

                        {{-- Filter Status Lengkap --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Status:</span>
                            <select x-model="auditStatusFilter" @change="updateAuditFilter()" 
                                class="px-2.5 py-1.5 text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white">
                                <option value="all">Semua Status</option>
                                <option value="Belum Lengkap">Belum Lengkap</option>
                                <option value="Lengkap">Lengkap</option>
                            </select>
                        </div>

                        {{-- Search Keyword --}}
                        <div class="relative w-full sm:w-64">
                            <input type="text" x-model="auditSearchKeyword" @input="updateAuditFilter()" placeholder="Cari ketidaksesuaian, PIC, atau tindakan..." 
                                class="w-full pl-8 pr-3 py-1.5 text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#F5333F]">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        Menampilkan <span class="font-bold text-[#F5333F] dark:text-red-400" x-text="filteredAuditFindings.length"></span> dari <span x-text="auditFindings.length"></span> baris temuan
                    </div>
                </div>

                {{-- Tabel Data Temuan Audit --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs whitespace-nowrap divide-y divide-slate-200 dark:divide-slate-800 font-sans">
                        <thead class="bg-slate-50 dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 font-bold uppercase text-[10.5px]">
                            <tr>
                                <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-800 text-center w-12">NO</th>
                                <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800 text-center">KATEGORI</th>
                                <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-800 text-center">JENIS</th>
                                <th class="py-3 px-4 border-r border-slate-200 dark:border-slate-800 min-w-[260px] max-w-sm whitespace-normal">DESKRIPSI KETIDAKSESUAIAN</th>
                                <th class="py-3 px-4 border-r border-slate-200 dark:border-slate-800 min-w-[220px] max-w-sm whitespace-normal">ANALISA AKAR PENYEBAB</th>
                                <th class="py-3 px-4 border-r border-slate-200 dark:border-slate-800 min-w-[220px] max-w-sm whitespace-normal">TINDAKAN KOREKSI</th>
                                <th class="py-3 px-4 border-r border-slate-200 dark:border-slate-800 min-w-[220px] max-w-sm whitespace-normal">TINDAKAN KOREKTIF</th>
                                <th class="py-3 px-4 border-r border-slate-200 dark:border-slate-800 min-w-[200px] max-w-xs whitespace-normal">DESKRIPSI BUKTI DOKUMEN</th>
                                <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800 text-center">EVIDENCE</th>
                                <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800">PIC</th>
                                <th class="py-3 px-3.5 border-r border-slate-200 dark:border-slate-800 text-center">STATUS LENGKAP</th>
                                <th class="py-3 px-3 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                            <template x-for="item in filteredAuditFindings" :key="item.id">
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-2.5 px-3 border-r border-slate-200 dark:border-slate-800 font-bold font-mono text-center text-slate-900 dark:text-white" x-text="item.no"></td>
                                    
                                    {{-- Kategori Badge --}}
                                    <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800 text-center">
                                        <span :class="{
                                            'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/40 dark:text-red-400 dark:border-red-800': item.category === 'Major',
                                            'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800': item.category === 'Minor',
                                            'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800': item.category === 'Observation',
                                        }" class="px-2.5 py-0.5 font-extrabold rounded-full text-[11px]" x-text="item.category"></span>
                                    </td>

                                    <td class="py-2.5 px-3 border-r border-slate-200 dark:border-slate-800 font-semibold text-center text-slate-600 dark:text-slate-400" x-text="item.jenis || '-'"></td>
                                    
                                    <td class="py-2.5 px-4 border-r border-slate-200 dark:border-slate-800 font-medium text-slate-900 dark:text-white min-w-[260px] max-w-sm whitespace-normal leading-relaxed" x-text="item.description"></td>
                                    <td class="py-2.5 px-4 border-r border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 min-w-[220px] max-w-sm whitespace-normal leading-relaxed" x-text="item.root_cause || '-'"></td>
                                    <td class="py-2.5 px-4 border-r border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 min-w-[220px] max-w-sm whitespace-normal leading-relaxed" x-text="item.correction || '-'"></td>
                                    <td class="py-2.5 px-4 border-r border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 min-w-[220px] max-w-sm whitespace-normal leading-relaxed" x-text="item.corrective_action || '-'"></td>
                                    <td class="py-2.5 px-4 border-r border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 min-w-[200px] max-w-xs whitespace-normal text-xs" x-text="item.file_evidence_desc || '-'"></td>

                                    {{-- Evidence Link --}}
                                    <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800 text-center">
                                        <template x-if="item.file_evidence">
                                            <a :href="item.file_evidence" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 rounded-lg transition">
                                                <span>Buka</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            </a>
                                        </template>
                                        <template x-if="!item.file_evidence">
                                            <span class="text-slate-400">-</span>
                                        </template>
                                    </td>

                                    <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800 font-medium text-slate-700 dark:text-slate-300" x-text="item.pic || '-'"></td>

                                    {{-- Status Lengkap Badge --}}
                                    <td class="py-2.5 px-3.5 border-r border-slate-200 dark:border-slate-800 text-center">
                                        <span :class="{
                                            'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800': item.status_lengkap === 'Lengkap',
                                            'bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800': item.status_lengkap === 'Belum Lengkap',
                                        }" class="px-2.5 py-0.5 font-bold rounded-full text-[11px]" x-text="item.status_lengkap"></span>
                                    </td>

                                    {{-- Aksi Edit / Hapus --}}
                                    <td class="py-2.5 px-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="editAuditItem(item)" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold text-xs">
                                                Edit
                                            </button>
                                            <span class="text-slate-300 dark:text-slate-700">|</span>
                                            <form :action="'{{ url('busdev/system-development/audit-finding') }}/' + item.id" method="POST" onsubmit="return confirm('Hapus data temuan audit ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-semibold text-xs">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredAuditFindings.length === 0">
                                <tr>
                                    <td colspan="12" class="py-10 text-center text-slate-500 text-xs">
                                        Tidak ada data temuan audit yang sesuai dengan kriteria filter.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL : SINKRONISASI GOOGLE SHEETS TEMUAN AUDIT                      --}}
        {{-- ==================================================================== --}}
        <div x-data="{ show: false }" @open-sync-audit-modal.window="show = true" x-show="show" class="relative z-50" style="display: none;" role="dialog" aria-modal="true">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div x-show="show" @click.outside="show = false" class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-slate-900">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="p-2 text-emerald-600 bg-emerald-50 rounded-xl dark:bg-emerald-950/40 dark:text-emerald-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Tarik Temuan Audit dari Sheets</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Sinkronkan temuan audit (SPPT SNI) langsung dari Google Spreadsheet.</p>
                            </div>
                        </div>
                        <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
                    </div>

                    <form action="{{ route('busdev.system-development.sync-audit-sheets') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Tautan Google Spreadsheet Temuan Audit</label>
                            <input type="url" name="sheets_url" required value="{{ $savedAuditSheetsUrl }}" 
                                placeholder="https://docs.google.com/spreadsheets/d/..."
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:border-[#F5333F] focus:outline-none dark:text-white text-slate-900">
                            <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                                Pastikan share link disetel ke <b>"Anyone with the link can view"</b> (Siapa saja dengan link dapat melihat).
                            </p>
                        </div>

                        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-800 dark:text-emerald-300">
                            <b>Kolom yang dibaca:</b> <i>NO, KATEGORI, JENIS, DESKRIPSI KETIDAKSESUAIAN, ANALISA AKAR PENYEBAB, TINDAKAN KOREKSI, TINDAKAN KOREKTIF, DESKRIPSI BUKTI, EVIDENCE, PIC, STATUS LENGKAP</i>.
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="show = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                            <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm">Mulai Sinkronisasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL : TAMBAH / EDIT TEMUAN AUDIT MANUAL                            --}}
        {{-- ==================================================================== --}}
        <div x-data="{ show: false, isEdit: false, form: { id: null, category: 'Major', jenis: 'SNI', description: '', root_cause: '', correction: '', corrective_action: '', file_evidence_desc: '', file_evidence: '', audit_review: '', plan_date: '', real_date: '', explanation: '', pic: '', status_lengkap: 'Belum Lengkap' } }" 
            @open-add-audit-modal.window="show = true; isEdit = false; form = { id: null, category: 'Major', jenis: 'SNI', description: '', root_cause: '', correction: '', corrective_action: '', file_evidence_desc: '', file_evidence: '', audit_review: '', plan_date: '', real_date: '', explanation: '', pic: '', status_lengkap: 'Belum Lengkap' }"
            @open-edit-audit-modal.window="show = true; isEdit = true; form = { ...$event.detail }"
            x-show="show" class="relative z-50" style="display: none;" role="dialog" aria-modal="true">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div x-show="show" @click.outside="show = false" class="w-full max-w-2xl overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-slate-900 max-h-[90vh] flex flex-col">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="isEdit ? 'Ubah Temuan Audit' : 'Tambah Temuan Audit Baru'"></h3>
                        <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
                    </div>

                    <form :action="isEdit ? '{{ url('busdev/system-development/audit-finding') }}/' + form.id : '{{ route('busdev.system-development.store-audit-finding') }}'" method="POST" class="p-6 overflow-y-auto space-y-4">
                        @csrf
                        <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Kategori Temuan *</label>
                                <select name="category" x-model="form.category" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                                    <option value="Major">Major</option>
                                    <option value="Minor">Minor</option>
                                    <option value="Observation">Observation</option>
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Jenis Asesmen *</label>
                                <input type="text" name="jenis" x-model="form.jenis" placeholder="Contoh: SNI, IH" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi Ketidaksesuaian *</label>
                                <textarea name="description" x-model="form.description" rows="2" required placeholder="Uraian temuan asesmen..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900"></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Analisa Akar Penyebab</label>
                                <textarea name="root_cause" x-model="form.root_cause" rows="2" placeholder="Penyebab mendasar masalah terjadi..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900"></textarea>
                            </div>

                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Tindakan Koreksi</label>
                                <textarea name="correction" x-model="form.correction" rows="2" placeholder="Koreksi langsung..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900"></textarea>
                            </div>

                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Tindakan Korektif</label>
                                <textarea name="corrective_action" x-model="form.corrective_action" rows="2" placeholder="Tindakan pencegahan berulang..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900"></textarea>
                            </div>

                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi Bukti Dokumen</label>
                                <input type="text" name="file_evidence_desc" x-model="form.file_evidence_desc" placeholder="Contoh: Salinan sertifikat atau foto" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>

                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Link Bukti / Evidence (Drive / URL)</label>
                                <input type="url" name="file_evidence" x-model="form.file_evidence" placeholder="https://drive.google.com/..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>

                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">PIC</label>
                                <input type="text" name="pic" x-model="form.pic" placeholder="Contoh: Lab / QC, HRD" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>

                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Status Lengkap *</label>
                                <select name="status_lengkap" x-model="form.status_lengkap" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                                    <option value="Belum Lengkap">Belum Lengkap</option>
                                    <option value="Lengkap">Lengkap</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="show = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                            <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-[#F5333F] hover:bg-red-600 rounded-xl shadow-sm">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL : SINKRONISASI GOOGLE SHEETS SERTIFIKAT                        --}}
        {{-- ==================================================================== --}}
        <div x-data="{ show: false }" @open-sync-modal.window="show = true" x-show="show" class="relative z-50" style="display: none;" role="dialog" aria-modal="true">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div x-show="show" @click.outside="show = false" class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-slate-900">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="p-2 text-emerald-600 bg-emerald-50 rounded-xl dark:bg-emerald-950/40 dark:text-emerald-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Tarik Data dari Google Sheets</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Sinkronkan sertifikat & status validitas langsung dari cloud sheet.</p>
                            </div>
                        </div>
                        <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
                    </div>

                    <form action="{{ route('busdev.system-development.sync-sheets') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Tautan / URL Google Sheets</label>
                            <input type="url" name="sheets_url" required value="{{ $savedSheetsUrl }}" 
                                placeholder="https://docs.google.com/spreadsheets/d/..."
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:border-[#F5333F] focus:outline-none dark:text-white text-slate-900">
                            <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                                Link ini <b>otomatis tersimpan</b> di database. Pastikan pengaturan akses spreadsheet di Google Sheets sudah disetel ke <b>"Anyone with the link can view"</b>.
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="show = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                            <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm">Mulai Sinkronisasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL : UPLOAD FILE EXCEL / CSV SERTIFIKAT                           --}}
        {{-- ==================================================================== --}}
        <div x-data="{ show: false, fileName: '' }" @open-upload-modal.window="show = true" x-show="show" class="relative z-50" style="display: none;" role="dialog" aria-modal="true">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div x-show="show" @click.outside="show = false" class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-slate-900">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="p-2 text-blue-600 bg-blue-50 rounded-xl dark:bg-blue-950/40 dark:text-blue-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Upload File Excel / CSV</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui data melalui dokumen spreadsheet lokal (.xlsx, .xls, .csv).</p>
                            </div>
                        </div>
                        <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
                    </div>

                    <form action="{{ route('busdev.system-development.import-excel') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label for="excel-input" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl cursor-pointer hover:border-[#F5333F] hover:bg-red-50/20 transition">
                                <svg class="w-8 h-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300" x-text="fileName || 'Pilih File Dokumen Excel (.xlsx / .csv)'"></span>
                                <span class="text-[10px] text-slate-500 mt-1">Ukuran maksimal 20 MB</span>
                            </label>
                            <input id="excel-input" type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="hidden" @change="fileName = $event.target.files[0]?.name || ''">
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                            <a href="{{ route('busdev.system-development.export-excel') }}" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:underline">
                                &darr; Unduh Format Contoh
                            </a>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="show = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                                <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-[#F5333F] hover:bg-red-600 rounded-xl shadow-sm">Import File</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL : TAMBAH / EDIT SERTIFIKAT MANUAL                              --}}
        {{-- ==================================================================== --}}
        <div x-data="{ show: false, isEdit: false, form: { id: null, kode: '', jenis_sertifikat: '', kategori_singkat: '', nomor_sertifikat: '', nama_produk: '', lembaga_penerbit: '', tanggal_terbit: '', tanggal_expired: '', link_sertifikat: '', pic_email: '', status_validitas: 'Aktif', notifikasi: true } }" 
            @open-add-modal.window="show = true; isEdit = false; form = { id: null, kode: '', jenis_sertifikat: '', kategori_singkat: '', nomor_sertifikat: '', nama_produk: '', lembaga_penerbit: '', tanggal_terbit: '', tanggal_expired: '', link_sertifikat: '', pic_email: '', status_validitas: 'Aktif', notifikasi: true }"
            @open-edit-modal.window="show = true; isEdit = true; form = { ...$event.detail }"
            x-show="show" class="relative z-50" style="display: none;" role="dialog" aria-modal="true">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div x-show="show" @click.outside="show = false" class="w-full max-w-2xl overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-slate-900 max-h-[90vh] flex flex-col">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="isEdit ? 'Ubah Data Sertifikat' : 'Tambah Sertifikat Baru'"></h3>
                        <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
                    </div>

                    <form :action="isEdit ? '{{ url('busdev/system-development/certificate') }}/' + form.id : '{{ route('busdev.system-development.store') }}'" method="POST" class="p-6 overflow-y-auto space-y-4">
                        @csrf
                        <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Kode *</label>
                                <input type="text" name="kode" x-model="form.kode" required placeholder="Contoh: S41" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Kategori Singkat (Nama)</label>
                                <input type="text" name="kategori_singkat" x-model="form.kategori_singkat" placeholder="ISO, SNI, TKDN, Green Label" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Jenis Sertifikat *</label>
                                <input type="text" name="jenis_sertifikat" x-model="form.jenis_sertifikat" required placeholder="Contoh: ISO 9001:2015" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Nomor Sertifikat</label>
                                <input type="text" name="nomor_sertifikat" x-model="form.nomor_sertifikat" placeholder="Contoh: QSC.00753" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Lembaga Penerbit</label>
                                <input type="text" name="lembaga_penerbit" x-model="form.lembaga_penerbit" placeholder="PT. Sucofindo, B4T, GPCI, KAN" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Produk / Layanan</label>
                                <input type="text" name="nama_produk" x-model="form.nama_produk" placeholder="Contoh: Manajemen Mutu" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" x-model="form.tanggal_terbit" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Tanggal Expired</label>
                                <input type="date" name="tanggal_expired" x-model="form.tanggal_expired" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Tautan File Sertifikat (Google Drive / URL)</label>
                                <input type="url" name="link_sertifikat" x-model="form.link_sertifikat" placeholder="https://drive.google.com/... atau https://s.id/..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">Status Validitas</label>
                                <select name="status_validitas" x-model="form.status_validitas" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Renewal">Renewal</option>
                                    <option value="Expired">Expired</option>
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-slate-700 dark:text-slate-300">PIC Email</label>
                                <input type="email" name="pic_email" x-model="form.pic_email" placeholder="pic@tonasa.co.id" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white text-slate-900">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="show = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                            <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-[#F5333F] hover:bg-red-600 rounded-xl shadow-sm">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Script Chart.js & Logika Interaktif Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function systemDevelopmentApp(initialCertificates, initialAuditFindings, initialAuditCatStats) {
            return {
                activeModule: '{{ session('active_module', request('module', 'certificates')) }}', // 'certificates' | 'audit'
                certTab: 'dashboard',         // 'dashboard' | 'spreadsheet'

                // Data Sertifikat
                certificates: initialCertificates || [],
                selectedName: '',
                selectedStatuses: ['Renewal', 'Expired', 'Aktif'],
                selectedDates: [],
                searchKeyword: '',
                sheetSearch: '',
                certPage: 1,
                certPageSize: 10,
                certSortCol: 'kode',
                certSortDir: 'asc',

                // Chart Instances Sertifikat
                gaugeChart: null,
                statusChart: null,
                jenisChart: null,
                lembagaChart: null,

                // Data Temuan Audit
                auditFindings: initialAuditFindings || [],
                auditCatStats: initialAuditCatStats || {},
                auditCategoryFilter: 'all',
                auditStatusFilter: 'all',
                auditSearchKeyword: '',

                // Chart Instances Temuan Audit
                auditCatChart: null,
                auditRealChart: null,
                auditStackedChart: null,

                get isDarkMode() {
                    return document.documentElement.classList.contains('dark');
                },

                initDashboard() {
                    this.$nextTick(() => {
                        this.renderCertCharts();
                        this.renderAuditCharts();
                    });
                },

                // ==========================================
                // HELPER TANGGAL & FORMAT
                // ==========================================
                formatDate(dateStr) {
                    if (!dateStr) return '-';
                    try {
                        const d = new Date(dateStr);
                        if (isNaN(d.getTime())) return dateStr;
                        return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
                    } catch (e) {
                        return dateStr;
                    }
                },

                get allDates() {
                    const dates = this.certificates
                        .filter(c => c.tanggal_expired)
                        .map(c => this.formatDate(c.tanggal_expired));
                    return [...new Set(dates)];
                },

                filteredDateOptions(search) {
                    if (!search) return this.allDates;
                    return this.allDates.filter(d => d.toLowerCase().includes(search.toLowerCase()));
                },

                toggleAllStatuses() {
                    if (this.selectedStatuses.length === 3) {
                        this.selectedStatuses = [];
                    } else {
                        this.selectedStatuses = ['Renewal', 'Expired', 'Aktif'];
                    }
                    this.updateCertCharts();
                },

                resetFilters() {
                    this.selectedName = '';
                    this.selectedStatuses = ['Renewal', 'Expired', 'Aktif'];
                    this.selectedDates = [];
                    this.searchKeyword = '';
                    this.updateCertCharts();
                },

                // ==========================================
                // FILTER DATA SERTIFIKAT
                // ==========================================
                get filteredCertificates() {
                    return this.certificates.filter(c => {
                        if (this.selectedName && c.jenis_sertifikat !== this.selectedName) return false;
                        if (this.selectedStatuses.length > 0 && !this.selectedStatuses.includes(c.status_validitas)) return false;
                        if (this.selectedDates.length > 0) {
                            const d = this.formatDate(c.tanggal_expired);
                            if (!this.selectedDates.includes(d)) return false;
                        }
                        if (this.searchKeyword) {
                            const kw = this.searchKeyword.toLowerCase();
                            const match = (c.kode && c.kode.toLowerCase().includes(kw)) ||
                                          (c.jenis_sertifikat && c.jenis_sertifikat.toLowerCase().includes(kw)) ||
                                          (c.nomor_sertifikat && c.nomor_sertifikat.toLowerCase().includes(kw)) ||
                                          (c.lembaga_penerbit && c.lembaga_penerbit.toLowerCase().includes(kw)) ||
                                          (c.nama_produk && c.nama_produk.toLowerCase().includes(kw));
                            if (!match) return false;
                        }
                        return true;
                    }).sort((a, b) => {
                        let valA = a[this.certSortCol] || '';
                        let valB = b[this.certSortCol] || '';
                        if (this.certSortCol === 'kode') {
                            const numA = parseInt(valA.replace(/\D/g, '') || '0', 10);
                            const numB = parseInt(valB.replace(/\D/g, '') || '0', 10);
                            return this.certSortDir === 'asc' ? numA - numB : numB - numA;
                        }
                        if (valA < valB) return this.certSortDir === 'asc' ? -1 : 1;
                        if (valA > valB) return this.certSortDir === 'asc' ? 1 : -1;
                        return 0;
                    });
                },

                get totalFilteredCertCount() {
                    return this.filteredCertificates.length;
                },

                get activeCertCount() {
                    return this.filteredCertificates.filter(c => c.status_validitas === 'Aktif').length;
                },

                get paginatedCertificates() {
                    const start = (this.certPage - 1) * this.certPageSize;
                    return this.filteredCertificates.slice(start, start + this.certPageSize);
                },

                get certTotalPages() {
                    return Math.max(1, Math.ceil(this.filteredCertificates.length / this.certPageSize));
                },

                sortCertBy(col) {
                    if (this.certSortCol === col) {
                        this.certSortDir = this.certSortDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.certSortCol = col;
                        this.certSortDir = 'asc';
                    }
                },

                filteredSheetData(search) {
                    if (!search) return this.certificates;
                    const kw = search.toLowerCase();
                    return this.certificates.filter(c => {
                        return Object.values(c).some(val => val && val.toString().toLowerCase().includes(kw));
                    });
                },

                editCertItem(item) {
                    window.dispatchEvent(new CustomEvent('open-edit-modal', { detail: item }));
                },

                // ==========================================
                // RENDER GRAFIK SERTIFIKAT
                // ==========================================
                renderCertCharts() {
                    this.renderGaugeChart();
                    this.renderStatusChart();
                    this.renderJenisChart();
                    this.renderLembagaChart();
                },

                updateCertCharts() {
                    this.certPage = 1;
                    this.$nextTick(() => {
                        const filtered = this.filteredCertificates;
                        
                        if (this.gaugeChart) {
                            const total = filtered.length;
                            const maxScale = Math.max(50, Math.ceil(total / 10) * 10);
                            this.gaugeChart.data.datasets[0].data = [total, Math.max(0, maxScale - total)];
                            this.gaugeChart.update();
                        }

                        if (this.statusChart) {
                            const aktif = filtered.filter(c => c.status_validitas === 'Aktif').length;
                            const renewal = filtered.filter(c => c.status_validitas === 'Renewal').length;
                            const expired = filtered.filter(c => c.status_validitas === 'Expired').length;
                            this.statusChart.data.datasets[0].data = [aktif, renewal, expired];
                            this.statusChart.update();
                        }

                        if (this.jenisChart) {
                            const jenisMap = {};
                            filtered.forEach(c => {
                                const k = (c.kategori_singkat || c.jenis_sertifikat || 'Lainnya').toUpperCase();
                                jenisMap[k] = (jenisMap[k] || 0) + 1;
                            });
                            const sortedJenis = Object.entries(jenisMap).sort((a, b) => b[1] - a[1]);
                            this.jenisChart.data.labels = sortedJenis.map(e => e[0]);
                            this.jenisChart.data.datasets[0].data = sortedJenis.map(e => e[1]);
                            this.jenisChart.update();
                        }

                        if (this.lembagaChart) {
                            const lembagaMap = {};
                            filtered.forEach(c => {
                                const l = c.lembaga_penerbit || 'Lainnya';
                                lembagaMap[l] = (lembagaMap[l] || 0) + 1;
                            });
                            const sortedLembaga = Object.entries(lembagaMap).sort((a, b) => b[1] - a[1]);
                            this.lembagaChart.data.labels = sortedLembaga.map(e => e[0]);
                            this.lembagaChart.data.datasets[0].data = sortedLembaga.map(e => e[1]);
                            this.lembagaChart.update();
                        }
                    });
                },

                renderGaugeChart() {
                    const ctx = document.getElementById('gaugeCertificatesChart');
                    if (!ctx) return;
                    const total = this.filteredCertificates.length;
                    const isDark = this.isDarkMode;
                    
                    if (this.gaugeChart) this.gaugeChart.destroy();
                    this.gaugeChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Total Sertifikat', 'Sisa'],
                            datasets: [{
                                data: [total, Math.max(0, 50 - total)],
                                backgroundColor: ['#10b981', isDark ? '#27272a' : '#e2e8f0'],
                                borderWidth: 0,
                                hoverOffset: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            circumference: 180,
                            rotation: 270,
                            cutout: '80%',
                            plugins: { legend: { display: false }, tooltip: { enabled: false } }
                        }
                    });
                },

                renderStatusChart() {
                    const ctx = document.getElementById('statusDonutChart');
                    if (!ctx) return;
                    const aktif = this.filteredCertificates.filter(c => c.status_validitas === 'Aktif').length;
                    const renewal = this.filteredCertificates.filter(c => c.status_validitas === 'Renewal').length;
                    const expired = this.filteredCertificates.filter(c => c.status_validitas === 'Expired').length;
                    const isDark = this.isDarkMode;

                    if (this.statusChart) this.statusChart.destroy();
                    this.statusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['1. Aktif', '2. Renewal', '3. Expired'],
                            datasets: [{
                                data: [aktif, renewal, expired],
                                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                                borderWidth: 2,
                                borderColor: isDark ? '#1A1A1A' : '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '72%',
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        color: isDark ? '#cbd5e1' : '#475569',
                                        boxWidth: 10,
                                        font: { size: 11, weight: 'bold' },
                                        padding: 12
                                    }
                                }
                            }
                        }
                    });
                },

                renderJenisChart() {
                    const ctx = document.getElementById('jenisBarChart');
                    if (!ctx) return;
                    
                    const jenisMap = {};
                    this.filteredCertificates.forEach(c => {
                        const k = (c.kategori_singkat || c.jenis_sertifikat || 'Lainnya').toUpperCase();
                        jenisMap[k] = (jenisMap[k] || 0) + 1;
                    });
                    const sortedJenis = Object.entries(jenisMap).sort((a, b) => b[1] - a[1]);
                    const isDark = this.isDarkMode;

                    if (this.jenisChart) this.jenisChart.destroy();
                    this.jenisChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: sortedJenis.map(e => e[0]),
                            datasets: [{
                                label: 'Jumlah',
                                data: sortedJenis.map(e => e[1]),
                                backgroundColor: '#10b981',
                                borderRadius: 6,
                                maxBarThickness: 36
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: {
                                    ticks: { color: isDark ? '#cbd5e1' : '#64748b', font: { size: 10, weight: 'bold' } },
                                    grid: { display: false }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: { color: isDark ? '#64748b' : '#94a3b8', stepSize: 2 },
                                    grid: { color: isDark ? '#262626' : '#f1f5f9' }
                                }
                            }
                        }
                    });
                },

                renderLembagaChart() {
                    const ctx = document.getElementById('lembagaBarChart');
                    if (!ctx) return;

                    const lembagaMap = {};
                    this.filteredCertificates.forEach(c => {
                        const l = c.lembaga_penerbit || 'Lainnya';
                        lembagaMap[l] = (lembagaMap[l] || 0) + 1;
                    });
                    const sortedLembaga = Object.entries(lembagaMap).sort((a, b) => b[1] - a[1]);
                    const isDark = this.isDarkMode;

                    if (this.lembagaChart) this.lembagaChart.destroy();
                    this.lembagaChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: sortedLembaga.map(e => e[0]),
                            datasets: [{
                                label: 'Jumlah',
                                data: sortedLembaga.map(e => e[1]),
                                backgroundColor: '#0284c7',
                                borderRadius: 6,
                                maxBarThickness: 24
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: { color: isDark ? '#64748b' : '#94a3b8', stepSize: 2 },
                                    grid: { color: isDark ? '#262626' : '#f1f5f9' }
                                }
                            }
                        }
                    });
                },

                // ==========================================
                // TEMUAN AUDIT SPPT SNI LOGIKA & GRAFIK
                // ==========================================
                get filteredAuditFindings() {
                    return this.auditFindings.filter(item => {
                        if (this.auditCategoryFilter !== 'all' && item.category !== this.auditCategoryFilter) return false;
                        if (this.auditStatusFilter !== 'all' && item.status_lengkap !== this.auditStatusFilter) return false;
                        if (this.auditSearchKeyword) {
                            const kw = this.auditSearchKeyword.toLowerCase();
                            const match = (item.description && item.description.toLowerCase().includes(kw)) ||
                                          (item.root_cause && item.root_cause.toLowerCase().includes(kw)) ||
                                          (item.correction && item.correction.toLowerCase().includes(kw)) ||
                                          (item.corrective_action && item.corrective_action.toLowerCase().includes(kw)) ||
                                          (item.pic && item.pic.toLowerCase().includes(kw)) ||
                                          (item.category && item.category.toLowerCase().includes(kw));
                            if (!match) return false;
                        }
                        return true;
                    });
                },

                get auditBelumLengkapCount() {
                    return this.auditFindings.filter(f => f.status_lengkap === 'Belum Lengkap').length;
                },

                get auditLengkapCount() {
                    return this.auditFindings.filter(f => f.status_lengkap === 'Lengkap').length;
                },

                editAuditItem(item) {
                    window.dispatchEvent(new CustomEvent('open-edit-audit-modal', { detail: item }));
                },

                renderAuditCharts() {
                    this.renderAuditCategoryDonut();
                    this.renderAuditRealisasiDonut();
                    this.renderAuditStackedBar();
                },

                updateAuditFilter() {
                    this.$nextTick(() => {
                        this.updateAuditCharts();
                    });
                },

                updateAuditCharts() {
                    const isDark = this.isDarkMode;
                    const filtered = this.filteredAuditFindings;

                    // 1. Update Kategori Donut
                    if (this.auditCatChart) {
                        const major = filtered.filter(f => f.category === 'Major').length;
                        const minor = filtered.filter(f => f.category === 'Minor').length;
                        const obs = filtered.filter(f => f.category === 'Observation').length;
                        this.auditCatChart.data.datasets[0].data = [major, minor, obs];
                        this.auditCatChart.update();
                    }

                    // 2. Update Realisasi Donut
                    if (this.auditRealChart) {
                        const belumLengkap = filtered.filter(f => f.status_lengkap === 'Belum Lengkap').length;
                        const lengkap = filtered.filter(f => f.status_lengkap === 'Lengkap').length;
                        this.auditRealChart.data.datasets[0].data = [belumLengkap, lengkap];
                        this.auditRealChart.update();
                    }

                    // 3. Update Stacked Bar
                    if (this.auditStackedChart) {
                        const categories = ['Major', 'Observation', 'Minor'];
                        const dataBelum = categories.map(cat => filtered.filter(f => f.category === cat && f.status_lengkap === 'Belum Lengkap').length);
                        const dataLengkap = categories.map(cat => filtered.filter(f => f.category === cat && f.status_lengkap === 'Lengkap').length);

                        this.auditStackedChart.data.datasets[0].data = dataBelum;
                        this.auditStackedChart.data.datasets[1].data = dataLengkap;
                        this.auditStackedChart.update();
                    }
                },

                renderAuditCategoryDonut() {
                    const ctx = document.getElementById('auditCategoryDonutChart');
                    if (!ctx) return;
                    const isDark = this.isDarkMode;

                    const major = this.auditFindings.filter(f => f.category === 'Major').length;
                    const minor = this.auditFindings.filter(f => f.category === 'Minor').length;
                    const obs = this.auditFindings.filter(f => f.category === 'Observation').length;

                    if (this.auditCatChart) this.auditCatChart.destroy();
                    this.auditCatChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Major (40%)', 'Minor (30%)', 'Observation (30%)'],
                            datasets: [{
                                data: [major, minor, obs],
                                backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                                borderWidth: 2,
                                borderColor: isDark ? '#1A1A1A' : '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: isDark ? '#cbd5e1' : '#475569',
                                        boxWidth: 12,
                                        font: { size: 11, weight: 'bold' },
                                        padding: 12
                                    }
                                }
                            }
                        }
                    });
                },

                renderAuditRealisasiDonut() {
                    const ctx = document.getElementById('auditRealisasiDonutChart');
                    if (!ctx) return;
                    const isDark = this.isDarkMode;

                    const belum = this.auditFindings.filter(f => f.status_lengkap === 'Belum Lengkap').length;
                    const lengkap = this.auditFindings.filter(f => f.status_lengkap === 'Lengkap').length;

                    if (this.auditRealChart) this.auditRealChart.destroy();
                    this.auditRealChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Belum Lengkap (90%)', 'Lengkap (10%)'],
                            datasets: [{
                                data: [belum, lengkap],
                                backgroundColor: ['#ef4444', '#10b981'],
                                borderWidth: 2,
                                borderColor: isDark ? '#1A1A1A' : '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: isDark ? '#cbd5e1' : '#475569',
                                        boxWidth: 12,
                                        font: { size: 11, weight: 'bold' },
                                        padding: 12
                                    }
                                }
                            }
                        }
                    });
                },

                renderAuditStackedBar() {
                    const ctx = document.getElementById('auditStackedBarChart');
                    if (!ctx) return;
                    const isDark = this.isDarkMode;

                    const categories = ['Major', 'Observation', 'Minor'];
                    const dataBelum = categories.map(cat => this.auditFindings.filter(f => f.category === cat && f.status_lengkap === 'Belum Lengkap').length);
                    const dataLengkap = categories.map(cat => this.auditFindings.filter(f => f.category === cat && f.status_lengkap === 'Lengkap').length);

                    if (this.auditStackedChart) this.auditStackedChart.destroy();
                    this.auditStackedChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: categories,
                            datasets: [
                                {
                                    label: 'Belum Lengkap',
                                    data: dataBelum,
                                    backgroundColor: '#ef4444',
                                    borderRadius: 4
                                },
                                {
                                    label: 'Lengkap',
                                    data: dataLengkap,
                                    backgroundColor: '#10b981',
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    stacked: true,
                                    beginAtZero: true,
                                    ticks: { color: isDark ? '#64748b' : '#94a3b8', stepSize: 1 },
                                    grid: { color: isDark ? '#262626' : '#f1f5f9' }
                                },
                                y: {
                                    stacked: true,
                                    ticks: { color: isDark ? '#cbd5e1' : '#475569', font: { size: 11, weight: 'bold' } },
                                    grid: { display: false }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: isDark ? '#cbd5e1' : '#475569',
                                        boxWidth: 12,
                                        font: { size: 11, weight: 'bold' },
                                        padding: 10
                                    }
                                }
                            }
                        }
                    });
                }

            };
        }
    </script>
</x-app-layout>
