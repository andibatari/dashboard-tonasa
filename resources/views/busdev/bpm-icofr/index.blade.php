<x-app-layout>
    <x-slot name="header">
        Implementasi ICoFR {{ $selectedYear }}
    </x-slot>

    @php
        // ---------- Peran & periode ----------
        $isAdmin      = auth()->user()->hasAnyRole(['Developer', 'Super Admin', 'Admin']);
        $selectedYear = request('tahun', date('Y'));
        $periode      = $tw ?? 'TW3';                // dikirim controller (?tw=TW1..TW4)

        // ---------- Tab yang boleh dilihat ----------
        $tabs = [];
        if ($isAdmin) {
            $tabs['dashboard'] = 'Dasbor pemantauan';
            $tabs['master']    = 'Master dokumen (RCM)';
        }

        // ---------- Angka ringkasan dashboard ----------
        $summary       = collect($summaryData);
        $totalControls = $summary->sum('total_controls');
        $totEfektif    = $summary->sum('efektif');
        $totTidak      = $summary->sum('tidak_efektif');
        $totNA         = $summary->sum('tidak_ada');
        $totProg       = $summary->sum('in_progress');
        $totDinilai    = $totEfektif + $totTidak + $totNA;
        $totBelum      = max($totalControls - $totDinilai - $totProg, 0);
        // % CSA = key control yang sudah punya status CSA / jumlah key control (sama dengan slide pimpinan)
        $keyAllCount   = $summary->sum('key_controls');
        $keyAllDinilai = $summary->sum('key_dinilai');
        $grandProgress = $keyAllCount > 0 ? round(($keyAllDinilai / $keyAllCount) * 100) : 0;
        $binary        = $totEfektif + $totTidak;
        $skorEfektif   = $binary > 0 ? round(($totEfektif / $binary) * 100) : null;

        // ---------- Data donut (SVG) ----------
        $circ  = 2 * pi() * 52;
        $cum   = 0;
        $donut = [];
        foreach ([
            ['Efektif', $totEfektif, '#10b981'],
            ['Tidak efektif', $totTidak, '#ef4444'],
            ['Tidak ada transaksi', $totNA, '#94a3b8'],
            ['Sedang berjalan', $totProg, '#f59e0b'],
        ] as [$label, $val, $color]) {
            $frac    = $totalControls > 0 ? $val / $totalControls : 0;
            $donut[] = ['label' => $label, 'val' => $val, 'color' => $color, 'dash' => $frac * $circ, 'off' => $cum * $circ];
            $cum    += $frac;
        }

        // ---------- Pencarian siklus ----------
        $cycleIndex = collect($cycles)->map(fn($c) => Str::lower($c->code . ' ' . $c->name))->values();

        // ---------- Gaya & ikon yang dipakai berulang ----------
        $card = 'bg-white border border-slate-200/80 shadow-sm rounded-2xl dark:bg-[#1A1A1A] dark:border-slate-800';
        $inp  = 'block w-full px-4 py-2.5 text-sm transition bg-slate-50 border border-slate-200 rounded-xl placeholder:text-slate-400 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-100 focus:outline-none dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:focus:ring-red-500/20';
        $lbl  = 'block mb-1.5 text-sm font-semibold text-slate-700 dark:text-slate-300';
        $btnGhost   = 'px-5 py-2.5 text-sm font-semibold text-slate-700 transition bg-white border border-slate-200 rounded-xl hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700';
        $btnPrimary = 'px-5 py-2.5 text-sm font-semibold text-white transition bg-[#F5333F] rounded-xl shadow-lg shadow-red-500/25 hover:bg-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F5333F]';
        $mt = 'x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 sm:scale-100" x-transition:leave-end="opacity-0 sm:scale-95"';

        $P = [
            'download' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
            'upload'   => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
            'plus'     => 'M12 4v16m8-8H4',
            'search'   => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
            'check'    => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'alert'    => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'warn'     => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'clip'     => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'bolt'     => 'M13 10V3L4 14h7v7l9-11h-7z',
            'pencil'   => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'trash'    => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
            'x'        => 'M6 18L18 6M6 6l12 12',
            'eye'      => 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
            'doc'      => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'minus'    => 'M20 12H4',
            'folder'   => 'M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z',
        ];
        $ic = fn($d, $cls = 'w-5 h-5') => '<svg class="' . $cls . '" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . $d . '"/></svg>';

        // Angka 0 ditampilkan redup agar tabel tidak ramai
        $cell = fn($v, $cls) => $v > 0
            ? '<span class="font-semibold ' . $cls . '">' . (int) $v . '</span>'
            : '<span class="text-slate-300 dark:text-slate-600">–</span>';

        // Sel persentase dengan bar mini; null = belum ada sumber data
        $pctCell = function ($v) {
            if ($v === null) {
                return '<span class="text-slate-300 dark:text-slate-600" title="Belum terhubung ke data">–</span>';
            }
            $bar = $v == 100 ? 'bg-emerald-500' : ($v > 0 ? 'bg-amber-500' : 'bg-slate-300');
            $txt = $v == 100 ? 'text-emerald-600' : ($v > 0 ? 'text-amber-600' : 'text-slate-400');
            return '<div class="flex items-center justify-center gap-2"><div class="w-14 h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800"><div class="h-full rounded-full ' . $bar . '" style="width: ' . (int) $v . '%"></div></div><span class="w-9 text-xs font-bold text-right ' . $txt . '">' . (int) $v . '%</span></div>';
        };

    @endphp

    <div class="py-4 sm:py-8"
        @keydown.escape.window="closeAll()"
        x-data="{
            activeTab: 'dashboard',
            allowedTabs: @js(array_keys($tabs)),
            showAddProcessModal: false,
            showAddCycleModal: false,
            showEditCycleModal: false, 
            cycleForm: { code: '', name: '', url: '' }, 

            openEditCycle(data) {
                this.cycleForm = data;
                this.showEditCycleModal = true;
            },

            showUploadBpmModal: false,
            showImportModal: false,
            showSyncSheetsModal: false,
            showPicModal: false,
            syncMode: 'pull',
            showScriptGuide: false,
            copiedScript: false,

            bpmData: { id: '', name: '' },
            
            cycleSearch: '',
            cycleIndex: @js($cycleIndex),

            get cyclesShown() { return this.cycleIndex.filter(t => t.includes(this.cycleSearch.toLowerCase())).length },
            
            openBpm(d) { this.bpmData = d; this.showUploadBpmModal = true },

            closeAll() { 
                this.showAddProcessModal = false; 
                this.showAddCycleModal = false; 
                this.showUploadBpmModal = false;
                this.showImportModal = false;
                this.showSyncSheetsModal = false;
                this.showPicModal = false;
                this.showEditCycleModal = false;
            }
        }"
        x-init="
            try { const t = localStorage.getItem('icofr_tab'); if (t && allowedTabs.includes(t)) activeTab = t } catch (e) {}
            $watch('activeTab', v => { try { localStorage.setItem('icofr_tab', v) } catch (e) {} })
        ">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- ============ Notifikasi ============ --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-transition.opacity role="status"
                    class="flex items-start gap-3 px-4 py-3 mb-6 text-sm font-medium border rounded-xl text-emerald-800 bg-emerald-50 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-800/40">
                    {!! $ic($P['check'], 'w-5 h-5 mt-0.5 flex-none') !!}
                    <p class="flex-1">{{ session('success') }}</p>
                    <button type="button" @click="show = false" aria-label="Tutup" class="opacity-60 hover:opacity-100">{!! $ic($P['x'], 'w-4 h-4') !!}</button>
                </div>
            @endif
            @if(session('import_warnings'))
                <div role="status" class="px-4 py-3 mb-6 text-sm border rounded-xl text-amber-800 bg-amber-50 border-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-800/40">
                    <p class="font-semibold">Catatan import (perlu dicek):</p>
                    <ul class="pl-4 mt-1 space-y-0.5 list-disc">
                        @foreach(session('import_warnings') as $w)
                            <li>{{ $w }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if($errors->any())
                <div role="alert" class="flex items-start gap-3 px-4 py-3 mb-6 text-sm border rounded-xl text-red-800 bg-red-50 border-red-200 dark:bg-red-500/10 dark:text-red-300 dark:border-red-800/40">
                    {!! $ic($P['alert'], 'w-5 h-5 mt-0.5 flex-none') !!}
                    <ul class="pl-4 space-y-0.5 list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ============ Tab + Export ============ --}}
            <div class="flex flex-col items-start justify-between gap-3 mb-8 lg:flex-row lg:items-center">
                <div class="inline-flex max-w-full p-1 overflow-x-auto rounded-xl bg-slate-100 dark:bg-slate-800 hide-scrollbar" role="tablist">
                    @foreach($tabs as $key => $label)
                        <button type="button" role="tab" @click="activeTab = '{{ $key }}'" :aria-selected="activeTab === '{{ $key }}'"
                                :class="activeTab === '{{ $key }}' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'"
                                class="flex items-center gap-2 px-4 py-2 text-sm font-semibold transition rounded-lg whitespace-nowrap focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#F5333F]">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                @if($isAdmin)
                    <script>
                    window.certificateManager = function() {
                        return {
                            showCertModal: false,
                            showOfficialsModal: false,
                            officialsTab: 'gm',
                            tingkat: 'GM',
                            triwulan: '4',
                            tahun: '{{ date('Y') }}',
                            unitPejabat: 'Dept. of Communication, Legal & GA',
                            jabatanPejabat: 'GM of Communication, Legal & GA',
                            namaPejabat: 'Muhammad Mursham, ST., M.S.M',

                            deptGmMap: @json($deptGmMap),

                            dirbidList: @json($directors),

                            init() {
                                this.setTingkat('GM');
                            },

                            setTingkat(t) {
                                this.tingkat = t;
                                if (t === 'DIRBID') {
                                    this.onDirbidChange(1);
                                } else {
                                    const def = this.deptGmMap.find(d => d.dept.includes('Communication')) || this.deptGmMap[0];
                                    if (def) {
                                        this.unitPejabat = def.dept;
                                        this.jabatanPejabat = def.jabatan;
                                        this.namaPejabat = def.nama || 'Muhammad Mursham, ST., M.S.M';
                                    }
                                }
                            },

                            onDeptChange(dept) {
                                this.unitPejabat = dept;
                                const match = this.deptGmMap.find(item => item.dept === dept);
                                if (match) {
                                    this.jabatanPejabat = match.jabatan;
                                    if (match.nama) {
                                        this.namaPejabat = match.nama;
                                    }
                                }
                            },

                            onGmJabatanChange(jab) {
                                this.jabatanPejabat = jab;
                                const match = this.deptGmMap.find(item => item.jabatan === jab);
                                if (match) {
                                    this.unitPejabat = match.dept;
                                    if (match.nama) {
                                        this.namaPejabat = match.nama;
                                    }
                                }
                            },

                            onDirbidChange(idx) {
                                const item = this.dirbidList[idx];
                                if (item) {
                                    this.jabatanPejabat = item.jabatan;
                                    this.unitPejabat = item.direktorat;
                                    this.namaPejabat = item.nama;
                                }
                            }
                        };
                    };
                    </script>

                    <div x-data="certificateManager()" class="flex flex-col sm:flex-row items-center gap-3">

                        <!-- Tombol Kelola Pejabat & Direksi -->
                        <button @click="showOfficialsModal = true" type="button" class="w-full sm:w-auto flex items-center justify-center px-4 py-2.5 text-sm font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 dark:bg-blue-500/10 dark:border-blue-500/20 dark:text-blue-400 dark:hover:bg-blue-500/20 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Kelola Pejabat &amp; Direksi
                        </button>

                        <!-- Tombol Unduh Sertifikasi -->
                        <button @click="showCertModal = true" type="button" class="w-full sm:w-auto flex items-center justify-center px-4 py-2.5 text-sm font-bold text-[#F5333F] bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400 dark:hover:bg-red-500/20 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Unduh Sertifikasi Berjenjang
                        </button>

                        <!-- ================= MODAL 1: POP-UP SERTIFIKASI ================= -->
                        <div x-show="showCertModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4" x-cloak style="display: none;">
                            <div x-show="showCertModal" 
                                @click.away="showCertModal = false"
                                x-transition:enter="transition ease-out duration-300 transform"
                                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                class="w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                                
                                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-800/50">
                                    <div class="flex items-center gap-3">
                                        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-red-100 text-[#F5333F] dark:bg-red-500/15 dark:text-red-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </span>
                                        <div>
                                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Sertifikasi Berjenjang ICoFR</h3>
                                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Unduh dokumen Word resmi (.docx) untuk GM & DIRBID</p>
                                        </div>
                                    </div>
                                    <button @click="showCertModal = false" type="button" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <form action="{{ route('busdev.bpm-icofr.download-cert') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
                                    @csrf
                                    
                                    {{-- Tingkat Penandatangan (GM vs DIRBID) --}}
                                    <div>
                                        <label class="block mb-2 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">
                                            Tingkat Penandatangan <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label @click="setTingkat('GM')"
                                                   :class="tingkat === 'GM' 
                                                       ? 'border-[#F5333F] bg-red-50/60 text-[#F5333F] dark:bg-red-500/10 dark:border-[#F5333F]' 
                                                       : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-slate-300'"
                                                   class="relative flex flex-col items-center justify-center p-3.5 border-2 rounded-2xl cursor-pointer transition-all">
                                                <input type="radio" name="tingkat" value="GM" x-model="tingkat" class="sr-only" required>
                                                <span class="text-sm font-extrabold">General Manager</span>
                                                <span class="text-[11px] opacity-75">Tingkat Departemen</span>
                                            </label>

                                            <label @click="setTingkat('DIRBID')"
                                                   :class="tingkat === 'DIRBID' 
                                                       ? 'border-[#F5333F] bg-red-50/60 text-[#F5333F] dark:bg-red-500/10 dark:border-[#F5333F]' 
                                                       : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-slate-300'"
                                                   class="relative flex flex-col items-center justify-center p-3.5 border-2 rounded-2xl cursor-pointer transition-all">
                                                <input type="radio" name="tingkat" value="DIRBID" x-model="tingkat" class="sr-only" required>
                                                <span class="text-sm font-extrabold">Direktur Bidang</span>
                                                <span class="text-[11px] opacity-75">Tingkat Direksi</span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Periode & Tahun --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block mb-1.5 text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Periode Triwulan</label>
                                            <select name="triwulan" x-model="triwulan" class="w-full px-3.5 py-2.5 text-sm font-medium bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#F5333F]/20 focus:border-[#F5333F]" required>
                                                <option value="1">Triwulan 1 (TW1)</option>
                                                <option value="2">Triwulan 2 (TW2)</option>
                                                <option value="3">Triwulan 3 (TW3)</option>
                                                <option value="4">Triwulan 4 (TW4)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block mb-1.5 text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Tahun</label>
                                            <input type="number" name="tahun" x-model="tahun" class="w-full px-3.5 py-2.5 text-sm font-medium bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#F5333F]/20 focus:border-[#F5333F]" required>
                                        </div>
                                    </div>

                                    {{-- Identitas Pejabat Dinamis --}}
                                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3.5">
                                        
                                        {{-- 1. Departemen / Direktorat Dropdown --}}
                                        <div>
                                            <label class="flex items-center justify-between mb-1.5 text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                                                <span x-text="tingkat === 'GM' ? 'Departemen / Direktorat (Departemen)' : 'Departemen / Direktorat (Direktorat)'"></span>
                                                <span class="text-[10px] font-normal text-slate-400 capitalize" x-text="tingkat === 'GM' ? 'dari Database' : 'Pilihan Direksi'"></span>
                                            </label>

                                            {{-- Dropdown Departemen untuk GM (Diambil dari database) --}}
                                            <select name="unit_pejabat" 
                                                    x-show="tingkat === 'GM'" 
                                                    :disabled="tingkat !== 'GM'"
                                                    x-model="unitPejabat" 
                                                    @change="onDeptChange($event.target.value)" 
                                                    class="w-full px-3.5 py-2.5 text-sm font-medium bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#F5333F]/20 focus:border-[#F5333F]" 
                                                    required>
                                                @foreach($picDepartments as $dept)
                                                    <option value="{{ $dept->name_dept }}">{{ $dept->name_dept }}</option>
                                                @endforeach
                                            </select>

                                            {{-- Dropdown Direktorat untuk DIRBID (3 Direktorat Resmi) --}}
                                            <select name="unit_pejabat" 
                                                    x-show="tingkat === 'DIRBID'" 
                                                    :disabled="tingkat !== 'DIRBID'"
                                                    x-model="unitPejabat" 
                                                    @change="onDirbidChange($event.target.selectedIndex)" 
                                                    class="w-full px-3.5 py-2.5 text-sm font-medium bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#F5333F]/20 focus:border-[#F5333F]" 
                                                    required>
                                                @foreach($directors as $dir)
                                                    <option value="{{ $dir['direktorat'] }}">{{ $dir['direktorat'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- 2. Jabatan Dropdown --}}
                                        <div>
                                            <label class="block mb-1.5 text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                                                Jabatan Penandatangan
                                            </label>

                                            {{-- Dropdown Jabatan untuk GM --}}
                                            <select name="jabatan_pejabat" 
                                                    x-show="tingkat === 'GM'" 
                                                    :disabled="tingkat !== 'GM'"
                                                    x-model="jabatanPejabat" 
                                                    @change="onGmJabatanChange($event.target.value)"
                                                    class="w-full px-3.5 py-2.5 text-sm font-medium bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#F5333F]/20 focus:border-[#F5333F]" 
                                                    required>
                                                @foreach($picDepartments as $dept)
                                                    @php $gmOpt = $dept->leader_title ?: ('GM of ' . preg_replace('/^(Department|Dept\.)\s+of\s+/i', '', $dept->name_dept)); @endphp
                                                    <option value="{{ $gmOpt }}">{{ $gmOpt }}</option>
                                                @endforeach
                                                <option value="General Manager">General Manager</option>
                                            </select>

                                            {{-- Dropdown Jabatan untuk DIRBID (3 Jabatan Direktur Resmi) --}}
                                            <select name="jabatan_pejabat" 
                                                    x-show="tingkat === 'DIRBID'" 
                                                    :disabled="tingkat !== 'DIRBID'"
                                                    x-model="jabatanPejabat" 
                                                    @change="onDirbidChange($event.target.selectedIndex)"
                                                    class="w-full px-3.5 py-2.5 text-sm font-medium bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#F5333F]/20 focus:border-[#F5333F]" 
                                                    required>
                                                @foreach($directors as $dir)
                                                    <option value="{{ $dir['jabatan'] }}">{{ $dir['jabatan'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- 3. Nama Pejabat Input (Auto-fill tapi bisa diedit bebas) --}}
                                        <div>
                                            <label class="block mb-1.5 text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                                                Nama Lengkap Pejabat
                                            </label>
                                            <input type="text" 
                                                   name="nama_pejabat" 
                                                   x-model="namaPejabat" 
                                                   list="pejabat-suggestions"
                                                   placeholder="Contoh: Endah Sulistyo Haryani, SE., Akt., MSi." 
                                                   class="w-full px-3.5 py-2.5 text-sm font-medium bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#F5333F]/20 focus:border-[#F5333F]" 
                                                   required>
                                            
                                            <datalist id="pejabat-suggestions">
                                                @foreach($picDepartments as $dept)
                                                    @if(!empty($dept->leader_name))
                                                        <option value="{{ $dept->leader_name }}">
                                                    @endif
                                                @endforeach
                                                @foreach($directors as $dir)
                                                    @if(!empty($dir['nama']))
                                                        <option value="{{ $dir['nama'] }}">
                                                    @endif
                                                @endforeach
                                            </datalist>
                                        </div>
                                    </div>

                                    <div class="pt-3">
                                        <button type="submit" class="w-full py-3.5 text-sm font-extrabold tracking-wider text-white uppercase transition-all shadow-lg rounded-xl bg-gradient-to-r from-red-600 to-[#F5333F] hover:from-red-700 hover:to-red-600 shadow-red-500/25 active:scale-[0.99] flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Generate Dokumen Word (.docx)
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- ================= MODAL 2: KELOLA PEJABAT & DIREKSI ================= -->
                        <div x-show="showOfficialsModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4" x-cloak style="display: none;">
                            <div x-show="showOfficialsModal" 
                                @click.away="showOfficialsModal = false"
                                x-transition:enter="transition ease-out duration-300 transform"
                                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                class="w-full max-w-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                                
                                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-800/50">
                                    <div class="flex items-center gap-3">
                                        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        </span>
                                        <div>
                                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Kelola Master Pejabat &amp; Direksi</h3>
                                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Ubah nama pejabat jika terjadi mutasi/pergantian pengurus</p>
                                        </div>
                                    </div>
                                    <button @click="showOfficialsModal = false" type="button" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <form action="{{ route('busdev.bpm-icofr.update-officials') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                                    @csrf

                                    {{-- Tab Selector --}}
                                    <div class="flex items-center gap-2 px-6 pt-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/20">
                                        <button type="button" @click="officialsTab = 'gm'" 
                                                :class="officialsTab === 'gm' ? 'border-[#F5333F] text-[#F5333F] font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                                                class="pb-3 text-xs uppercase tracking-wider border-b-2 transition-colors flex items-center gap-1.5">
                                            <span>General Manager ({{ $picDepartments->count() }} PIC Aktif / {{ $departments->count() }})</span>
                                        </button>
                                        <button type="button" @click="officialsTab = 'dirbid'" 
                                                :class="officialsTab === 'dirbid' ? 'border-[#F5333F] text-[#F5333F] font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                                                class="pb-3 text-xs uppercase tracking-wider border-b-2 transition-colors flex items-center gap-1.5">
                                            <span>Direktur Bidang ({{ count($directors) }})</span>
                                        </button>
                                    </div>

                                    <div class="p-6 space-y-4 overflow-y-auto flex-1">
                                        {{-- 1. TAB GM (DEPARTEMEN) --}}
                                        <div x-show="officialsTab === 'gm'" class="space-y-4">
                                            <div class="p-3.5 bg-blue-50/70 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/50 rounded-2xl text-xs text-blue-900 dark:text-blue-200 flex items-start gap-3">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-none mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <div>
                                                    <span class="font-bold">Status PIC ICoFR:</span>
                                                    Departemen dengan tanda <span class="font-bold text-emerald-600 dark:text-emerald-400">PIC ICoFR</span> dicentang adalah yang memiliki kontrol ICoFR dan akan muncul pada form unduh sertifikasi berjenjang. Departemen <span class="font-semibold text-slate-600 dark:text-slate-400">Non-PIC</span> (yang tidak memiliki kontrol) disembunyikan agar form sertifikasi bersih dan tidak memunculkan data kosong.
                                                </div>
                                            </div>

                                            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                                                @foreach($departments as $dept)
                                                    <div class="py-3.5 first:pt-0 last:pb-0 space-y-2.5">
                                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $dept->name_dept }}</span>
                                                                @if($dept->is_icofr_pic)
                                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80">PIC Aktif</span>
                                                                @else
                                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">Non-PIC</span>
                                                                @endif
                                                            </div>
                                                            <label class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 cursor-pointer select-none hover:bg-slate-100 dark:hover:bg-slate-700/60 transition-colors">
                                                                <input type="checkbox" 
                                                                       name="departments[{{ $dept->id }}][is_icofr_pic]" 
                                                                       value="1" 
                                                                       {{ $dept->is_icofr_pic ? 'checked' : '' }} 
                                                                       class="w-4 h-4 text-[#F5333F] rounded border-slate-300 dark:border-slate-600 focus:ring-[#F5333F]">
                                                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">PIC ICoFR</span>
                                                            </label>
                                                        </div>
                                                        <div class="grid gap-2.5 sm:grid-cols-12 sm:items-center">
                                                            <div class="sm:col-span-5">
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase">Jabatan GM</label>
                                                                <input type="text" 
                                                                       name="departments[{{ $dept->id }}][leader_title]" 
                                                                       value="{{ $dept->leader_title ?: ('GM of ' . preg_replace('/^(Department|Dept\.)\s+of\s+/i', '', $dept->name_dept)) }}" 
                                                                       placeholder="Jabatan GM..." 
                                                                       class="w-full px-3 py-1.5 text-xs font-medium bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:border-[#F5333F]">
                                                            </div>
                                                            <div class="sm:col-span-7">
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Lengkap &amp; Gelar Pejabat</label>
                                                                <input type="text" 
                                                                       name="departments[{{ $dept->id }}][leader_name]" 
                                                                       value="{{ $dept->leader_name }}" 
                                                                       placeholder="{{ $dept->is_icofr_pic ? 'Nama pejabat GM...' : 'Bukan PIC ICoFR (opsional)' }}" 
                                                                       class="w-full px-3 py-1.5 text-xs font-semibold bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#F5333F] focus:border-[#F5333F]">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- 2. TAB DIREKTUR BIDANG (DIRBID) --}}
                                        <div x-show="officialsTab === 'dirbid'" style="display: none;" class="space-y-4">
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Data Direksi PT Semen Tonasa untuk Sertifikasi Berjenjang ICoFR.</p>

                                            <div class="space-y-3">
                                                @foreach($directors as $idx => $dir)
                                                    <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 rounded-2xl space-y-2.5">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-xs font-extrabold text-[#F5333F] uppercase tracking-wide">{{ $dir['jabatan'] }}</span>
                                                            <span class="text-[11px] font-medium text-slate-400">Direktorat: {{ $dir['direktorat'] }}</span>
                                                        </div>
                                                        <input type="hidden" name="directors[{{ $idx }}][jabatan]" value="{{ $dir['jabatan'] }}">
                                                        <input type="hidden" name="directors[{{ $idx }}][direktorat]" value="{{ $dir['direktorat'] }}">
                                                        
                                                        <div>
                                                            <label class="block mb-1 text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase">Nama Pejabat Direktur</label>
                                                            <input type="text" 
                                                                   name="directors[{{ $idx }}][nama]" 
                                                                   value="{{ $dir['nama'] }}" 
                                                                   placeholder="Contoh: {{ $dir['nama'] }}" 
                                                                   class="w-full px-3.5 py-2 text-sm font-semibold bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#F5333F]/20 focus:border-[#F5333F]">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-end gap-3">
                                        <button type="button" @click="showOfficialsModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                                            Batal
                                        </button>
                                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-red-600 to-[#F5333F] hover:from-red-700 hover:to-red-600 shadow-md shadow-red-500/20 active:scale-[0.99] flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                            Simpan Perubahan Pejabat
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ==================================================================== --}}
            {{-- TAB 1 : DASBOR PEMANTAUAN                                            --}}
            {{-- ==================================================================== --}}
            @if($isAdmin)
            <section x-show="activeTab === 'dashboard'" x-transition.opacity.duration.200ms style="display: none;">

                <div class="flex flex-col items-start justify-between gap-4 mb-6 lg:flex-row lg:items-center">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Dasbor Pemantauan</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ringkasan hasil CSA ICoFR periode <strong>{{ $selectedTW }} / {{ $selectedYear }}</strong>.</p>
                    </div>
                    
                    {{-- FORM FILTER PERIODE OTOMATIS & EXPORT EXCEL --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <form method="GET" action="{{ route('busdev.bpm-icofr.index') }}" class="flex items-center gap-2">
                            <div class="relative bg-white border border-slate-200 rounded-xl shadow-sm dark:bg-slate-900 dark:border-slate-800">
                                <select name="triwulan" onchange="this.form.submit()" class="py-2 pl-4 pr-10 text-sm font-bold border-none bg-transparent cursor-pointer focus:ring-0 text-slate-700 dark:text-slate-200">
                                    <option value="TW1" {{ $selectedTW == 'TW1' ? 'selected' : '' }}>Triwulan 1</option>
                                    <option value="TW2" {{ $selectedTW == 'TW2' ? 'selected' : '' }}>Triwulan 2</option>
                                    <option value="TW3" {{ $selectedTW == 'TW3' ? 'selected' : '' }}>Triwulan 3</option>
                                    <option value="TW4" {{ $selectedTW == 'TW4' ? 'selected' : '' }}>Triwulan 4</option>
                                </select>
                            </div>
                            
                            <div class="relative bg-white border border-slate-200 rounded-xl shadow-sm dark:bg-slate-900 dark:border-slate-800">
                                <select name="tahun" onchange="this.form.submit()" class="py-2 pl-4 pr-10 text-sm font-bold border-none bg-transparent cursor-pointer focus:ring-0 text-slate-700 dark:text-slate-200">
                                    @for($i = date('Y') - 2; $i <= date('Y') + 2; $i++)
                                        <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </form>

                        <a href="{{ route('busdev.bpm-icofr.export', ['tahun' => $selectedYear, 'triwulan' => $selectedTW]) }}" 
                           title="Unduh laporan Excel ringkasan periode ini"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-400 dark:hover:bg-emerald-900/40 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Export Laporan (.xlsx)
                        </a>
                    </div>
                </div>

                {{-- Donut + 4 kartu statistik --}}
                <div class="grid gap-4 mb-6 lg:grid-cols-3">

                    <div class="{{ $card }} p-6">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-white">Skor efektivitas kontrol</h4>
                        <p class="mt-1 text-xs text-slate-500">Persentase kontrol efektif dari yang sudah dinilai efektif atau tidak efektif.</p>
                        <div class="flex items-center gap-6 mt-5">
                            <div class="relative flex-none w-32 h-32">
                                <svg viewBox="0 0 128 128" class="w-full h-full" role="img" aria-label="Komposisi hasil penilaian CSA">
                                    <circle cx="64" cy="64" r="52" fill="none" stroke-width="14" class="stroke-slate-100 dark:stroke-slate-800"/>
                                    @foreach($donut as $d)
                                        @if($d['val'] > 0)
                                            <circle cx="64" cy="64" r="52" fill="none" stroke-width="14" stroke="{{ $d['color'] }}"
                                                    stroke-dasharray="{{ round($d['dash'], 2) }} {{ round($circ, 2) }}"
                                                    stroke-dashoffset="-{{ round($d['off'], 2) }}"
                                                    transform="rotate(-90 64 64)"/>
                                        @endif
                                    @endforeach
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $skorEfektif !== null ? $skorEfektif . '%' : '–' }}</span>
                                    <span class="text-[11px] text-slate-500">efektif</span>
                                </div>
                            </div>
                            <ul class="flex-1 space-y-2 text-sm">
                                @foreach($donut as $d)
                                    <li class="flex items-center justify-between gap-2">
                                        <span class="flex items-center gap-2 text-slate-600 dark:text-slate-300"><span class="w-2.5 h-2.5 rounded-full" style="background: {{ $d['color'] }}"></span>{{ $d['label'] }}</span>
                                        <span class="font-semibold text-slate-900 dark:text-white">{{ $d['val'] }}</span>
                                    </li>
                                @endforeach
                                <li class="flex items-center justify-between gap-2">
                                    <span class="flex items-center gap-2 text-slate-600 dark:text-slate-300"><span class="w-2.5 h-2.5 rounded-full bg-slate-200 dark:bg-slate-700"></span>Belum dinilai</span>
                                    <span class="font-semibold text-slate-900 dark:text-white">{{ $totBelum }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:col-span-2">
                        <div class="{{ $card }} p-5">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 bg-slate-100 dark:bg-slate-800 dark:text-slate-300">{!! $ic($P['clip']) !!}</span>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total kontrol RCM</p>
                            </div>
                            <p class="mt-4 text-3xl font-extrabold text-slate-900 dark:text-white">{{ $totalControls }}</p>
                            <p class="mt-1 text-xs text-slate-500">dari {{ $summary->count() }} siklus bisnis</p>
                        </div>
                        <div class="{{ $card }} p-5">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400">{!! $ic($P['check']) !!}</span>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Kontrol efektif</p>
                            </div>
                            <p class="mt-4 text-3xl font-extrabold text-emerald-600">{{ $totEfektif }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $binary > 0 ? $skorEfektif . '% dari kontrol yang dinilai' : 'Belum ada penilaian' }}</p>
                        </div>
                        <div class="{{ $card }} p-5">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl text-red-600 bg-red-50 dark:bg-red-500/10 dark:text-red-400">{!! $ic($P['alert']) !!}</span>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Temuan (defisiensi)</p>
                            </div>
                            <p class="mt-4 text-3xl font-extrabold text-red-600">{{ $totTidak }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $totTidak > 0 ? 'Perlu tindak lanjut remediasi' : 'Tidak ada temuan' }}</p>
                        </div>
                        <div class="{{ $card }} p-5">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl text-amber-600 bg-amber-50 dark:bg-amber-500/10 dark:text-amber-400">{!! $ic($P['bolt']) !!}</span>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Progres CSA</p>
                            </div>
                            <p class="mt-4 text-3xl font-extrabold text-slate-900 dark:text-white">{{ $grandProgress }}%</p>
                            <div class="h-1.5 mt-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800"><div class="h-full rounded-full bg-amber-500" style="width: {{ $grandProgress }}%"></div></div>
                            <p class="mt-1.5 text-xs text-slate-500">{{ $keyAllDinilai }} dari {{ $keyAllCount }} key control sudah memiliki status CSA</p>
                        </div>
                    </div>
                </div>

                {{-- AREA GRAFIK (CHARTS) --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    
                    {{-- Grafik 1: Progres Implementasi (Bar Chart) --}}
                    <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm dark:bg-slate-900 dark:border-slate-800 lg:col-span-2">
                        <h3 class="mb-1 text-sm font-bold text-slate-800 dark:text-slate-200">Progres Implementasi & Pemantauan (Fase 2)</h3>
                        <p class="mb-4 text-xs text-slate-500">Perbandingan persentase CSA, BPM, dan RCM antar Siklus Bisnis.</p>
                        <div class="relative w-full h-72">
                            <canvas id="implementasiChart"></canvas>
                        </div>
                    </div>

                    {{-- Grafik 2: Status RCM (Doughnut Chart) --}}
                    <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm dark:bg-slate-900 dark:border-slate-800">
                        <h3 class="mb-1 text-sm font-bold text-slate-800 dark:text-slate-200">Komposisi Kontrol RCM</h3>
                        <p class="mb-4 text-xs text-slate-500">Berdasarkan tingkat materialitas (Key vs Non-Key).</p>
                        <div class="relative flex items-center justify-center w-full h-64">
                            <canvas id="komposisiRcmChart"></canvas>
                        </div>
                    </div>
                    
                </div>

                {{-- Tabel progres per siklus (format sama dengan slide pimpinan) --}}
                <div class="mb-6 overflow-hidden {{ $card }}">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-white">Progres implementasi ICoFR per siklus</h4>
                        <p class="mt-0.5 text-xs text-slate-500">Kolom (a), (b), (d), (e) = proses selesai ÷ jumlah proses. Kolom (c) = key control yang sudah punya status CSA ÷ jumlah key control.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-xs font-semibold align-bottom text-slate-500 bg-slate-50 dark:bg-slate-800/50 dark:text-slate-400">
                                    <th class="px-6 py-3 text-left">Siklus</th>
                                    @foreach([
                                        ['Dokumentasi rancangan proses bisnis &amp; pengendalian (BPM &amp; RCM)', 'a'],
                                        ['Validasi rancangan pengendalian', 'b'],
                                        ['Implementasi Control Self Assessment (CSA)', 'c'],
                                        ['Pemutakhiran Business Process Mapping (BPM)', 'd'],
                                        ['Pemutakhiran Risk Control Matrix (RCM)', 'e'],
                                        ['Test of Design Effectiveness (TOD)', 'f'],
                                        ['Test of Operating Effectiveness (TOE)', 'g'],
                                        ['Remediasi atas defisiensi rancangan (TOD)', 'h'],
                                        ['Remediasi atas defisiensi rancangan (TOE)', 'i'],
                                    ] as [$head, $letter])
                                        <th class="px-3 py-3 text-center min-w-[130px]">
                                            <span class="block font-semibold leading-snug">{!! $head !!}</span>
                                            <span class="text-slate-400">({{ $letter }})</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                @foreach($summary as $row)
                                    <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                        <td class="px-6 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <span class="flex items-center justify-center w-8 h-8 text-sm font-bold rounded-lg text-[#F5333F] bg-red-50 dark:bg-red-500/10">{{ $row->code }}</span>
                                                @if(isset($row->id))
                                                    <a href="{{ route('busdev.bpm-icofr.show', $row->id) }}" class="font-semibold text-slate-900 hover:text-[#F5333F] dark:text-white">{{ $row->name }}</a>
                                                @else
                                                    <span class="font-semibold text-slate-900 dark:text-white">{{ $row->name }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        @foreach(['dokumentasi_pct', 'validasi_pct', 'csa_progress', 'bpm_pct', 'rcm_pct', 'tod_pct', 'toe_pct', 'rem_tod_pct', 'rem_toe_pct'] as $f)
                                            <td class="px-3 py-3.5 text-center">{!! $pctCell($row->$f ?? null) !!}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="px-6 py-3 text-xs border-t text-slate-500 border-slate-100 dark:border-slate-800">Kolom (f) sampai (i) tampil “–” karena data TOD dan TOE belum tersedia di sistem.</p>
                </div>

                {{-- Papan skor --}}
                <div class="overflow-hidden {{ $card }}">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-white">Papan skor hasil CSA</h4>
                        <p class="mt-0.5 text-xs text-slate-500">Status CSA diisi Busdev pada halaman detail siklus</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center whitespace-nowrap">
                            <thead>
                                <tr class="text-xs font-semibold text-slate-500 bg-slate-50 dark:bg-slate-800/50 dark:text-slate-400">
                                    <th rowspan="2" class="px-6 py-3 text-left border-b border-slate-200 dark:border-slate-700">Siklus</th>
                                    <th colspan="3" class="px-3 py-2 border-b border-l border-slate-200 dark:border-slate-700">Jumlah kontrol</th>
                                    <th colspan="5" class="px-3 py-2 border-b border-l border-slate-200 dark:border-slate-700">Kesimpulan hasil CSA</th>
                                </tr>
                                <tr class="text-xs font-semibold text-slate-500 bg-slate-50 dark:bg-slate-800/50 dark:text-slate-400">
                                    <th class="px-3 py-2 border-l border-slate-200 dark:border-slate-700">Key</th>
                                    <th class="px-3 py-2">Non key</th>
                                    <th class="px-3 py-2">Total</th>
                                    <th class="px-3 py-2 text-emerald-600 border-l border-slate-200 dark:border-slate-700">Efektif</th>
                                    <th class="px-3 py-2 text-red-600">Tidak efektif</th>
                                    <th class="px-3 py-2">N/A</th>
                                    <th class="px-3 py-2 text-amber-600">Berjalan</th>
                                    <th class="px-3 py-2">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                @foreach($summary as $row)
                                    <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                        <td class="px-6 py-3 text-left">
                                            <div class="flex items-center gap-2.5">
                                                <span class="font-bold text-slate-400">{{ $row->code }}</span>
                                                <span class="font-semibold text-slate-900 dark:text-white">{{ $row->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 border-l border-slate-100 dark:border-slate-800">{!! $cell($row->key_controls, 'text-slate-700 dark:text-slate-300') !!}</td>
                                        <td class="px-3 py-3">{!! $cell($row->non_key_controls, 'text-slate-700 dark:text-slate-300') !!}</td>
                                        <td class="px-3 py-3 font-bold text-slate-900 bg-slate-50/70 dark:bg-slate-800/40 dark:text-white">{{ $row->total_controls }}</td>
                                        <td class="px-3 py-3 border-l border-slate-100 dark:border-slate-800">{!! $cell($row->efektif, 'text-emerald-600') !!}</td>
                                        <td class="px-3 py-3">{!! $cell($row->tidak_efektif, 'text-red-600') !!}</td>
                                        <td class="px-3 py-3">{!! $cell($row->tidak_ada, 'text-slate-500') !!}</td>
                                        <td class="px-3 py-3">{!! $cell($row->in_progress, 'text-amber-600') !!}</td>
                                        <td class="px-3 py-3 font-bold text-slate-900 bg-slate-50/70 dark:bg-slate-800/40 dark:text-white">{{ $row->efektif + $row->tidak_efektif + $row->tidak_ada + $row->in_progress }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="text-sm font-bold border-t-2 border-slate-200 bg-slate-50 dark:bg-slate-800/50 dark:border-slate-700 text-slate-900 dark:text-white">
                                    <td class="px-6 py-3 text-left">Total keseluruhan</td>
                                    <td class="px-3 py-3 border-l border-slate-200 dark:border-slate-700">{{ $summary->sum('key_controls') }}</td>
                                    <td class="px-3 py-3">{{ $summary->sum('non_key_controls') }}</td>
                                    <td class="px-3 py-3">{{ $totalControls }}</td>
                                    <td class="px-3 py-3 text-emerald-600 border-l border-slate-200 dark:border-slate-700">{{ $totEfektif }}</td>
                                    <td class="px-3 py-3 text-red-600">{{ $totTidak }}</td>
                                    <td class="px-3 py-3 text-slate-500">{{ $totNA }}</td>
                                    <td class="px-3 py-3 text-amber-600">{{ $totProg }}</td>
                                    <td class="px-3 py-3">{{ $totDinilai + $totProg }}</td>
                                </tr>
                                @php
                                    $keyTotalAll = $summary->sum('key_controls');
                                    $kp = fn($v) => $keyTotalAll > 0 ? round($v / $keyTotalAll * 100) . '%' : '–';
                                    $kEf  = $summary->sum('key_efektif');
                                    $kTd  = $summary->sum('key_tidak_efektif');
                                    $kNa  = $summary->sum('key_tidak_ada');
                                    $kPr  = $summary->sum('key_in_progress');
                                @endphp
                                <tr class="text-sm font-bold border-t border-slate-200 bg-slate-100/40 dark:bg-slate-800/40 dark:border-slate-700 text-slate-900 dark:text-white">
                                    <td class="px-6 py-3 text-left">Khusus key control <span class="block text-xs font-normal text-slate-500">jumlah kontrol, hanya yang berstatus key</span></td>
                                    <td colspan="3" class="px-3 py-3 text-xs font-medium text-slate-500">{{ $keyTotalAll }} key control</td>
                                    <td class="px-3 py-3 text-emerald-600 border-l border-slate-200 dark:border-slate-700">{{ $kEf }}</td>
                                    <td class="px-3 py-3 text-red-600">{{ $kTd }}</td>
                                    <td class="px-3 py-3 text-slate-500">{{ $kNa }}</td>
                                    <td class="px-3 py-3 text-amber-600">{{ $kPr }}</td>
                                    <td class="px-3 py-3">{{ $kEf + $kTd + $kNa + $kPr }}</td>
                                </tr>
                                <tr class="text-sm font-bold bg-slate-100/70 dark:bg-slate-800/70 text-slate-900 dark:text-white">
                                    <td class="px-6 py-3 text-left">Total % terhadap key control <span class="block text-xs font-normal text-slate-500">baris di atas ÷ jumlah key control</span></td>
                                    <td colspan="3"></td>
                                    <td class="px-3 py-3 text-emerald-600 border-l border-slate-200 dark:border-slate-700">{{ $kp($kEf) }}</td>
                                    <td class="px-3 py-3 text-red-600">{{ $kp($kTd) }}</td>
                                    <td class="px-3 py-3 text-slate-500">{{ $kp($kNa) }}</td>
                                    <td class="px-3 py-3 text-amber-600">{{ $kp($kPr) }}</td>
                                    <td class="px-3 py-3">{{ $kp($kEf + $kTd + $kNa + $kPr) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>
            @endif

            {{-- ==================================================================== --}}
            {{-- TAB 2 : MASTER DOKUMEN (RCM)                                         --}}
            {{-- ==================================================================== --}}
            @if($isAdmin)
            <section x-show="activeTab === 'master'" x-transition.opacity.duration.200ms style="display: none;">

                <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Database hierarki ICoFR</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola siklus, proses bisnis, dan matriks risiko kontrol (RCM) perusahaan.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">{!! $ic($P['search'], 'w-4 h-4') !!}</span>
                            <input type="search" x-model="cycleSearch" placeholder="Cari siklus…" aria-label="Cari siklus" class="{{ $inp }} !w-52 !pl-10 !py-2.5">
                        </div>
                        <button type="button" @click="$dispatch('open-pic-modal'); showPicModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-blue-700 transition bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/50 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Kelola Master PIC</span>
                            <span class="px-1.5 py-0.5 text-[10px] font-black rounded-full bg-blue-200/80 text-blue-800 dark:bg-blue-800 dark:text-blue-200 font-mono">{{ $icofrPics->count() }}</span>
                        </button>
                        <button type="button" @click="showSyncSheetsModal = true" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-400 dark:hover:bg-emerald-900/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Sinkronisasi (Google Sheets)
                        </button>
                        <button type="button" @click="showAddProcessModal = true" class="inline-flex items-center gap-2 {{ $btnGhost }}">
                            {!! $ic($P['plus'], 'w-4 h-4') !!} Tambah proses
                        </button>
                        @if(Route::has('busdev.bpm-icofr.store-cycle'))
                            <button type="button" @click="showAddCycleModal = true" class="inline-flex items-center gap-2 {{ $btnPrimary }}">
                                {!! $ic($P['plus'], 'w-4 h-4') !!} Tambah siklus
                            </button>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($cycles as $cycle)
                        @php
                            $sum       = $summary->firstWhere('code', $cycle->code);
                            $ctrlCount = $sum->total_controls ?? 0;
                            $csa       = $sum->csa_progress ?? 0;
                            $csaColor  = $csa == 100 ? 'bg-emerald-500' : ($csa > 0 ? 'bg-amber-500' : 'bg-slate-300');
                        @endphp
                        <article x-show="@js(Str::lower($cycle->code . ' ' . $cycle->name)).includes(cycleSearch.toLowerCase())"
                                class="relative flex flex-col p-6 transition {{ $card }} hover:shadow-lg hover:-translate-y-0.5 hover:border-slate-300 dark:hover:border-slate-700 focus-within:ring-2 focus-within:ring-[#F5333F]/40">
                            <div class="flex items-start justify-between">
                                <span class="flex items-center justify-center w-12 h-12 text-lg font-extrabold rounded-xl text-[#F5333F] bg-red-50 dark:bg-red-500/10">{{ $cycle->code }}</span>
                                
                                <div class="flex items-center gap-2">
                                    @if($cycle->bpm_file_path)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full text-emerald-700 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400">{!! $ic('M5 13l4 4L19 7', 'w-3.5 h-3.5') !!}BPM tersedia</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full text-slate-500 bg-slate-100 dark:bg-slate-800 dark:text-slate-400">BPM belum diunggah</span>
                                    @endif

                                    {{-- Tombol Aksi: Edit & Delete --}}
                                    <div class="relative z-20 flex items-center gap-1 pl-2 ml-1 border-l border-slate-200 dark:border-slate-700">
                                        <button type="button" @click.stop.prevent="openEditCycle({ code: @js($cycle->code), name: @js($cycle->name), url: @js(route('busdev.bpm-icofr.update-cycle', $cycle->id)) })" title="Ubah Siklus" class="p-1.5 transition rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                            {!! $ic($P['pencil'], 'w-4 h-4') !!}
                                        </button>
                                        <form action="{{ route('busdev.bpm-icofr.destroy-cycle', $cycle->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus siklus ini? PERINGATAN: Seluruh proses bisnis dan matriks kontrol di dalamnya akan ikut terhapus selamanya!');">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus Siklus" class="p-1.5 transition rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                {!! $ic($P['trash'], 'w-4 h-4') !!}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('busdev.bpm-icofr.show', $cycle->id) }}"
                            class="mt-4 text-lg font-bold transition-colors text-slate-900 hover:text-[#F5333F] dark:text-white after:absolute after:inset-0 after:content-[''] focus:outline-none">
                                {{ $cycle->name }}
                            </a>

                            <dl class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <dt class="text-xs text-slate-500">Proses bisnis</dt>
                                    <dd class="text-xl font-bold text-slate-900 dark:text-white">{{ $cycle->processes->count() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Kontrol RCM</dt>
                                    <dd class="text-xl font-bold text-slate-900 dark:text-white">{{ $ctrlCount }}</dd>
                                </div>
                            </dl>

                            <div class="mt-4">
                                <div class="flex items-center justify-between text-xs text-slate-500"><span>Progres CSA</span><span class="font-semibold text-slate-700 dark:text-slate-300">{{ $csa }}%</span></div>
                                <div class="h-1.5 mt-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800"><div class="h-full rounded-full {{ $csaColor }}" style="width: {{ $csa }}%"></div></div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between pt-4 mt-5 border-t border-slate-100 dark:border-slate-800">
                                @if($cycle->bpm_file_path)
                                    <a href="{{ asset('storage/' . $cycle->bpm_file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 hover:text-emerald-700 hover:underline dark:text-emerald-400">
                                        {!! $ic($P['eye'], 'w-4 h-4') !!} Lihat BPM
                                    </a>
                                    <button type="button" @click="openBpm(@js(['id' => $cycle->id, 'name' => $cycle->name]))" title="Ganti file BPM" aria-label="Ganti file BPM {{ $cycle->name }}"
                                            class="p-1.5 transition rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-800">
                                        {!! $ic($P['upload'], 'w-4 h-4') !!}
                                    </button>
                                @else
                                    <button type="button" @click="openBpm(@js(['id' => $cycle->id, 'name' => $cycle->name]))"
                                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-[#F5333F] dark:text-slate-300">
                                        {!! $ic($P['upload'], 'w-4 h-4') !!} Unggah BPM
                                    </button>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="p-10 text-center border border-dashed col-span-full rounded-2xl border-slate-300 dark:border-slate-700">
                            <p class="text-sm text-slate-500">Belum ada siklus yang ditambahkan.</p>
                        </div>
                    @endforelse
                </div>

                <p x-show="cycleIndex.length > 0 && cyclesShown === 0" style="display: none;" class="py-12 text-sm text-center text-slate-500">
                    Tidak ada siklus yang cocok dengan pencarian Anda.
                </p>
            </section>
            @endif
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL : TAMBAH PROSES                                                --}}
        {{-- ==================================================================== --}}
        @if($isAdmin)
        <div x-show="showAddProcessModal" class="relative z-50" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="ttl-add-process">
            <div x-show="showAddProcessModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto sm:items-center sm:p-4">
                <div x-show="showAddProcessModal" @click.outside="showAddProcessModal = false" {!! $mt !!}
                     class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-t-3xl sm:rounded-3xl dark:bg-slate-900">
                    <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 id="ttl-add-process" class="text-lg font-bold text-slate-900 dark:text-white">Tambah proses bisnis</h3>
                            <p class="mt-1 text-sm text-slate-500">Daftarkan proses bisnis ke dalam siklus ICoFR. Sub proses ditambahkan nanti dari halaman detail siklus.</p>
                        </div>
                        <button type="button" @click="showAddProcessModal = false" aria-label="Tutup" class="p-1 transition text-slate-400 hover:text-slate-900 dark:hover:text-white">{!! $ic($P['x']) !!}</button>
                    </div>
                    <form action="{{ route('busdev.bpm-icofr.store-process') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div>
                            <label for="ap-cycle" class="{{ $lbl }}">Siklus utama</label>
                            <select id="ap-cycle" name="icofr_cycle_id" class="{{ $inp }}" required>
                                <option value="">Pilih siklus</option>
                                @foreach($cycles as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label for="ap-code" class="{{ $lbl }}">Kode</label>
                                <input id="ap-code" type="text" name="code" placeholder="01" class="{{ $inp }}" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="ap-name" class="{{ $lbl }}">Nama proses bisnis</label>
                                <input id="ap-name" type="text" name="name" placeholder="Contoh: PPh 21" class="{{ $inp }}" required>
                            </div>
                        </div>
                        <div class="flex flex-col-reverse gap-3 pt-5 border-t sm:flex-row sm:justify-end border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showAddProcessModal = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }}">Simpan proses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- ==================================================================== --}}
        {{-- MODAL : TAMBAH SIKLUS (muncul hanya bila route store-cycle sudah ada) --}}
        {{-- ==================================================================== --}}
        @if($isAdmin && Route::has('busdev.bpm-icofr.store-cycle'))
        <div x-show="showAddCycleModal" class="relative z-50" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="ttl-add-cycle">
            <div x-show="showAddCycleModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto sm:items-center sm:p-4">
                <div x-show="showAddCycleModal" @click.outside="showAddCycleModal = false" {!! $mt !!}
                     class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-t-3xl sm:rounded-3xl dark:bg-slate-900">
                    <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 id="ttl-add-cycle" class="text-lg font-bold text-slate-900 dark:text-white">Tambah siklus bisnis</h3>
                            <p class="mt-1 text-sm text-slate-500">Siklus baru akan muncul di daftar Master Dokumen.</p>
                        </div>
                        <button type="button" @click="showAddCycleModal = false" aria-label="Tutup" class="p-1 transition text-slate-400 hover:text-slate-900 dark:hover:text-white">{!! $ic($P['x']) !!}</button>
                    </div>
                    <form action="{{ route('busdev.bpm-icofr.store-cycle') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label for="ac-code" class="{{ $lbl }}">Kode</label>
                                <input id="ac-code" type="text" name="code" maxlength="5" placeholder="H" class="{{ $inp }}" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="ac-name" class="{{ $lbl }}">Nama siklus</label>
                                <input id="ac-name" type="text" name="name" placeholder="Contoh: Pengadaan" class="{{ $inp }}" required>
                            </div>
                        </div>
                        <div class="flex flex-col-reverse gap-3 pt-5 border-t sm:flex-row sm:justify-end border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showAddCycleModal = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }}">Simpan siklus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- ==================================================================== --}}
        {{-- MODAL : UBAH SIKLUS                                                  --}}
        {{-- ==================================================================== --}}
        @if($isAdmin)
        <div x-show="showEditCycleModal" class="relative z-50" style="display: none;" role="dialog" aria-modal="true">
            <div x-show="showEditCycleModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto sm:items-center sm:p-4">
                <div x-show="showEditCycleModal" @click.outside="showEditCycleModal = false" {!! $mt !!}
                     class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-t-3xl sm:rounded-3xl dark:bg-slate-900">
                    <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Ubah Siklus Bisnis</h3>
                            <p class="mt-1 text-sm text-slate-500">Perbaiki penamaan atau kode siklus.</p>
                        </div>
                        <button type="button" @click="showEditCycleModal = false" aria-label="Tutup" class="p-1 transition text-slate-400 hover:text-slate-900 dark:hover:text-white">{!! $ic($P['x']) !!}</button>
                    </div>
                    <form x-bind:action="cycleForm.url" method="POST" class="p-6 space-y-5">
                        @csrf @method('PUT')
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="{{ $lbl }}">Kode</label>
                                <input type="text" name="code" x-model="cycleForm.code" maxlength="5" class="{{ $inp }}" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="{{ $lbl }}">Nama siklus</label>
                                <input type="text" name="name" x-model="cycleForm.name" class="{{ $inp }}" required>
                            </div>
                        </div>
                        <div class="flex flex-col-reverse gap-3 pt-5 border-t sm:flex-row sm:justify-end border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showEditCycleModal = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }}">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- ==================================================================== --}}
        {{-- MODAL : UNGGAH BPM                                                   --}}
        {{-- ==================================================================== --}}
        @if($isAdmin)
        <div x-show="showUploadBpmModal" class="relative z-50" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="ttl-bpm">
            <div x-show="showUploadBpmModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto sm:items-center sm:p-4">
                <div x-show="showUploadBpmModal" @click.outside="showUploadBpmModal = false" {!! $mt !!}
                     class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-t-3xl sm:rounded-3xl dark:bg-slate-900">
                    <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 id="ttl-bpm" class="text-lg font-bold text-slate-900 dark:text-white">Unggah dokumen BPM</h3>
                            <p class="mt-1 text-sm text-slate-500">Business Process Mapping (PDF) untuk siklus <span class="font-semibold text-slate-900 dark:text-white" x-text="bpmData.name"></span>.</p>
                        </div>
                        <button type="button" @click="showUploadBpmModal = false" aria-label="Tutup" class="p-1 transition text-slate-400 hover:text-slate-900 dark:hover:text-white">{!! $ic($P['x']) !!}</button>
                    </div>
                    <form action="{{ route('busdev.bpm-icofr.upload-bpm') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5" x-data="{ fileName: '' }">
                        @csrf
                        <input type="hidden" name="cycle_id" :value="bpmData.id">
                        <div>
                            <label for="bpm-file" class="flex flex-col items-center gap-2 px-4 py-8 text-sm text-center transition border-2 border-dashed cursor-pointer rounded-2xl border-slate-300 text-slate-500 hover:border-[#F5333F] hover:bg-red-50/40 dark:border-slate-700 dark:hover:bg-red-500/5">
                                {!! $ic($P['upload'], 'w-8 h-8 text-slate-400') !!}
                                <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="fileName || 'Klik untuk memilih file PDF'"></span>
                                <span class="text-xs">Hanya format PDF</span>
                            </label>
                            <input id="bpm-file" type="file" name="bpm_file" accept=".pdf" required class="sr-only" @change="fileName = $event.target.files[0]?.name || ''">
                        </div>
                        <div class="flex flex-col-reverse gap-3 pt-5 border-t sm:flex-row sm:justify-end border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showUploadBpmModal = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }}">Unggah file</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- ==================================================================== --}}
        {{-- MODAL : IMPORT RCM (EXCEL)                                           --}}
        {{-- ==================================================================== --}}
        @if($isAdmin)
        <div x-show="showImportModal" class="relative z-50" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="ttl-import">
            <div x-show="showImportModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto sm:items-center sm:p-4">
                <div x-show="showImportModal" @click.outside="showImportModal = false" {!! $mt !!}
                     class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-t-3xl sm:rounded-3xl dark:bg-slate-900">
                    <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 id="ttl-import" class="text-lg font-bold text-slate-900 dark:text-white">Import RCM dari Excel</h3>
                            <p class="mt-1 text-sm text-slate-500">Gunakan sheet <span class="font-semibold">CRM GABUNG</span>. Mengunggah ulang file yang sama tidak membuat data ganda dan tidak menghapus status CSA.</p>
                        </div>
                        <button type="button" @click="showImportModal = false" aria-label="Tutup" class="p-1 transition text-slate-400 hover:text-slate-900 dark:hover:text-white">{!! $ic($P['x']) !!}</button>
                    </div>
                    <form action="{{ route('busdev.bpm-icofr.import-rcm') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5" x-data="{ fileName: '' }">
                        @csrf
                        <label for="rcm-file" class="flex flex-col items-center gap-2 px-4 py-8 text-sm text-center transition border-2 border-dashed cursor-pointer rounded-2xl border-slate-300 text-slate-500 hover:border-[#F5333F] hover:bg-red-50/40 dark:border-slate-700">
                            {!! $ic($P['upload'], 'w-8 h-8 text-slate-400') !!}
                            <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="fileName || 'Klik untuk memilih file .xlsx'"></span>
                            <span class="text-xs">Maksimal 20 MB</span>
                        </label>
                        <input id="rcm-file" type="file" name="rcm_file" accept=".xlsx,.xls" required class="sr-only" @change="fileName = $event.target.files[0]?.name || ''">
                        <div class="flex flex-col-reverse gap-3 pt-5 border-t sm:flex-row sm:justify-end border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showImportModal = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }}">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- ==================================================================== --}}
        {{-- MODAL : SINKRONISASI GOOGLE SHEETS                                   --}}
        {{-- ==================================================================== --}}
        @if($isAdmin)
        <div x-show="showSyncSheetsModal" class="relative z-50" style="display: none;" role="dialog" aria-modal="true">
            <div x-show="showSyncSheetsModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto sm:items-center sm:p-4">
                <div x-show="showSyncSheetsModal" @click.outside="showSyncSheetsModal = false" {!! $mt !!} class="w-full max-w-xl overflow-hidden bg-white shadow-2xl rounded-t-3xl sm:rounded-3xl dark:bg-slate-900 border border-slate-100 dark:border-slate-800">
                    <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                                </span>
                                Sinkronisasi Dua Arah Google Sheets
                            </h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Hubungkan data risiko &amp; kontrol (RCM) serta penilaian CSA langsung ke Spreadsheet.</p>
                        </div>
                        <button type="button" @click="showSyncSheetsModal = false" class="p-1 transition text-slate-400 hover:text-slate-900 dark:hover:text-white">{!! $ic($P['x']) !!}</button>
                    </div>

                    {{-- Tab Mode Switcher  --}}
                    <div class="px-6 pt-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                        <div class="flex items-center gap-2" role="tablist">
                            <button type="button" @click="syncMode = 'pull'" 
                                :class="syncMode === 'pull' ? 'border-emerald-600 text-emerald-700 dark:text-emerald-400 font-bold bg-white dark:bg-slate-900 shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 font-medium'"
                                class="flex items-center gap-2 px-4 py-2.5 text-xs rounded-t-xl border-t-2 border-x transition-all">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                1. Tarik Data (Sheets &rarr; Web)
                            </button>
                            <button type="button" @click="syncMode = 'push'" 
                                :class="syncMode === 'push' ? 'border-emerald-600 text-emerald-700 dark:text-emerald-400 font-bold bg-white dark:bg-slate-900 shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 font-medium'"
                                class="flex items-center gap-2 px-4 py-2.5 text-xs rounded-t-xl border-t-2 border-x transition-all">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                2. Kirim Data (Web &rarr; Sheets)
                            </button>
                        </div>
                    </div>

                    {{-- TAB 1: TARIK DATA (PULL) --}}
                    <form x-show="syncMode === 'pull'" action="{{ route('busdev.bpm-icofr.sync-sheets') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="{{ $lbl }}">URL Google Sheets RCM</label>
                            <input type="url" name="sheets_url" 
                                    value="{{ $icofrSheetsUrl ?: config('app.icofr_sheets_url') }}" 
                                    placeholder="https://docs.google.com/spreadsheets/d/..." 
                                    class="{{ $inp }}" required>
                            <p class="mt-1.5 text-[11px] text-slate-500">Pastikan izin akses Google Sheets diatur ke <em>"Anyone with the link (Viewer/Editor)"</em>.</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="{{ $lbl }}">Tahun Laporan</label>
                                <input type="number" name="tahun" value="{{ date('Y') }}" class="{{ $inp }}" required>
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Triwulan</label>
                                <select name="triwulan" class="{{ $inp }}" required>
                                    <option value="TW1">TW1</option>
                                    <option value="TW2">TW2</option>
                                    <option value="TW3">TW3</option>
                                    <option value="TW4">TW4</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-xs text-slate-600 dark:text-slate-300 space-y-1">
                            <span class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Informasi Tarik Data
                            </span>
                            <p>Sistem akan memperbarui nama kontrol, parameter risiko (Ref. Risk, Nilai Risiko, Fraud Risk, Key/Non Key), dan hasil penilaian CSA periode terpilih langsung ke database.</p>
                        </div>

                        <div class="flex flex-col-reverse gap-3 pt-4 border-t sm:flex-row sm:justify-end border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showSyncSheetsModal = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }}">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Tarik &amp; Sinkronkan Data
                            </button>
                        </div>
                    </form>

                    {{-- TAB 2: KIRIM DATA (PUSH) --}}
                    <form x-show="syncMode === 'push'" action="{{ route('busdev.bpm-icofr.push-sheets') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="{{ $lbl }} !mb-0">URL Webhook Google Apps Script</label>
                                <button type="button" @click="showScriptGuide = !showScriptGuide" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span x-text="showScriptGuide ? 'Tutup Panduan' : 'Panduan Pasang Skrip (1 Menit)'"></span>
                                </button>
                            </div>
                            <input type="url" name="webhook_url" 
                                    value="{{ $icofrWebhookUrl ?? '' }}" 
                                    placeholder="https://script.google.com/macros/s/.../exec" 
                                    class="{{ $inp }}" required>
                            <p class="mt-1.5 text-[11px] text-slate-500">URL Web App hasil deploy Apps Script pada Google Sheets Anda. Cukup diisi sekali.</p>
                        </div>

                        {{-- Collapsible Panduan Apps Script --}}
                        <div x-show="showScriptGuide" x-transition.opacity class="p-4 space-y-3 text-xs border rounded-2xl bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700">
                            <div class="flex items-center justify-between font-bold text-slate-800 dark:text-white">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Langkah Setup Apps Script (1 Menit)
                                </span>
                                <button type="button" 
                                    @click="navigator.clipboard.writeText(document.getElementById('gas-script-box-index').value); copiedScript = true; setTimeout(() => copiedScript = false, 2500);" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-100 rounded-lg hover:bg-emerald-200 dark:bg-emerald-900/50 dark:text-emerald-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span x-text="copiedScript ? 'Tersalin!' : 'Salin Skrip'"></span>
                                </button>
                            </div>
                            <ol class="space-y-1 pl-4 list-decimal text-slate-600 dark:text-slate-300 leading-relaxed text-[11px]">
                                <li>Di spreadsheet Anda, buka menu <strong>Ekstensi &gt; Apps Script</strong>.</li>
                                <li>Hapus skrip lama, lalu klik tombol <strong>Salin Skrip</strong> di atas dan <strong>Paste</strong> ke Apps Script.</li>
                                <li>Klik tombol biru <strong>Deploy &gt; New deployment</strong> (Pilih tipe <em>Web app</em>).</li>
                                <li>Pilih <em>Execute as: <strong>Me</strong></em> dan <em>Who has access: <strong>Anyone</strong></em>.</li>
                                <li>Klik <strong>Deploy</strong> lalu salin Web App URL yang muncul ke kotak Webhook di atas. Selesai!</li>
                            </ol>
                            <textarea id="gas-script-box-index" readonly class="w-full h-36 p-2 font-mono text-[10px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 focus:outline-none">function doPost(e) {
  try {
    var contents = JSON.parse(e.postData.contents);
    var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
    var data = contents.data;
    if (!data || !data.length) {
      return ContentService.createTextOutput(JSON.stringify({
        status: 'error', 
        message: 'Data kosong. Tidak ada data yang dikirim.'
      })).setMimeType(ContentService.MimeType.JSON);
    }
    
    var lastRow = Math.max(sheet.getLastRow(), 1);
    var lastCol = Math.max(sheet.getLastColumn(), 25);
    
    // 1. Deteksi Baris Header Inti (cari yang mengandung 'ref. control', 'no. ref. control', atau '(4)')
    var headerRowIndex = 2; // Default baris ke-2
    var scanRows = Math.min(lastRow, 10);
    var sampleGrid = sheet.getRange(1, 1, scanRows, lastCol).getValues();
    
    for (var r = 0; r < scanRows; r++) {
      var rowStr = sampleGrid[r].join(" ").toLowerCase();
      if (rowStr.indexOf("(4)") !== -1 || rowStr.indexOf("ref. control") !== -1 || rowStr.indexOf("ref control") !== -1) {
        headerRowIndex = r + 1;
        break;
      }
    }
    
    // 2. Gabungkan SEMUA teks header dari Baris 1 s/d headerRowIndex untuk setiap kolom
    // (Penting: agar kolom merge seperti Siklus (1), Proses (2), Sub Proses (3) di baris 1 tetap terdeteksi!)
    var headerGrid = sheet.getRange(1, 1, headerRowIndex, lastCol).getValues();
    var combinedHeaders = [];
    for (var c = 0; c < lastCol; c++) {
      var text = "";
      for (var r = 0; r < headerRowIndex; r++) {
        if (headerGrid[r][c] !== undefined && headerGrid[r][c] !== null) {
          text += " " + headerGrid[r][c].toString();
        }
      }
      combinedHeaders[c] = text.toLowerCase().replace(/\s+/g, " ").trim();
    }
    
    // Helper pencarian kolom berdasarkan kata kunci
    function findColumn(keywords) {
      for (var c = 0; c < combinedHeaders.length; c++) {
        var h = combinedHeaders[c];
        for (var k = 0; k < keywords.length; k++) {
          if (h.indexOf(keywords[k].toLowerCase()) !== -1) {
            return c + 1; // 1-indexed untuk getRange
          }
        }
      }
      return null;
    }
    
    // 3. Mapping Kolom Lengkap
    var colMap = {};
    colMap['siklus']                   = findColumn(['(1)', 'siklus']);
    colMap['proses']                   = findColumn(['(2)', 'proses']);
    colMap['sub_proses']               = findColumn(['(3)', 'sub proses']);
    
    // Bedakan Nama Pelaksana vs Unit Pelaksana
    colMap['nama_pic']                 = findColumn(['nama pelaksana', 'nama pic']);
    colMap['pelaksana_kontrol']        = null;
    for (var c = 0; c < combinedHeaders.length; c++) {
      var colIdx = c + 1;
      if (colIdx !== colMap['nama_pic']) {
        var h = combinedHeaders[c];
        if (h.indexOf('pelaksana kontrol') !== -1 || h.indexOf('pelaksana') !== -1 || h.indexOf('unit') !== -1) {
          colMap['pelaksana_kontrol'] = colIdx;
          break;
        }
      }
    }
    
    colMap['ref_risk']                 = findColumn(['ref. risiko', 'ref risiko', 'ref. risk', 'ref risk', 'kode risiko']);
    colMap['deskripsi_risiko']         = findColumn(['deskripsi risiko', 'uraian risiko', 'deskripsi risk']);
    colMap['nilai_risiko']             = findColumn(['nilai risiko', 'risk value', 'tingkat risiko']);
    colMap['risiko_kecurangan']        = findColumn(['risiko kecurangan', 'kecurangan', 'fraud']);
    
    colMap['ref_control']              = findColumn(['(4)', 'ref. control', 'no. ref. control', 'ref control']);
    
    // Deskripsi kontrol (hindari mencocokkan deskripsi risiko)
    colMap['deskripsi_kontrol']        = null;
    for (var c = 0; c < combinedHeaders.length; c++) {
      var h = combinedHeaders[c];
      if (h.indexOf('(5)') !== -1 || (h.indexOf('deskripsi') !== -1 && h.indexOf('risiko') === -1 && h.indexOf('risk') === -1)) {
        colMap['deskripsi_kontrol'] = c + 1;
        break;
      }
    }
    
    colMap['key_non_key']              = findColumn(['key/non key', 'key / non key', 'key/non', 'key non key', 'tipe kontrol']);
    colMap['frekuensi']                = findColumn(['(7)', 'frekuensi']);
    colMap['dokumen']                  = findColumn(['(10)', 'dokumen pendukung', 'dokumen']);
    colMap['obyektif']                 = findColumn(['(11)', 'obyektif csa', 'obyektif']);
    colMap['prosedur']                 = findColumn(['(12)', 'prosedur csa', 'prosedur']);
    colMap['hasil_csa']                = findColumn(['(13)', 'hasil csa']);
    colMap['keterangan_tidak_efektif'] = findColumn(['(14)', 'keterangan tidak']);
    colMap['root_cause']               = findColumn(['(15)', 'root cause']);
    colMap['kertas_kerja']             = findColumn(['(16)', 'kertas kerja']);
    colMap['remediasi']                = findColumn(['(17)', 'rencana remediasi', 'remediasi']);
    
    // Kolom Ref. Control wajib ada untuk identifikasi baris
    var ctrlCol = colMap['ref_control'] || 10;
    
    // 4. Bangun indeks baris yang sudah ada berdasarkan Kode Kontrol
    var rowLookup = {};
    if (lastRow > headerRowIndex) {
      var ctrlValues = sheet.getRange(headerRowIndex + 1, ctrlCol, lastRow - headerRowIndex, 1).getValues();
      for (var i = 0; i < ctrlValues.length; i++) {
        var code = (ctrlValues[i][0] || '').toString().trim();
        if (code) {
          rowLookup[code] = headerRowIndex + 1 + i;
        }
      }
    }
    
    // 5. Cari baris acuan yang sudah ada untuk menduplikasi border dan data validation (dropdown)
    var sampleFormatRow = lastRow > headerRowIndex ? Math.max(headerRowIndex + 1, lastRow) : headerRowIndex + 1;
    
    // Helper untuk menulis nilai dengan aman tanpa terhambat validasi data (dropdown) ketat
    function safeSetValue(cell, val) {
      if (val === undefined || val === null || val === '') return;
      
      var finalVal = val;
      var rule = null;
      try {
        rule = cell.getDataValidation();
      } catch (e) {}
      
      if (rule) {
        try {
          var criteria = rule.getCriteriaValues();
          if (criteria && criteria.length > 0 && Array.isArray(criteria[0])) {
            var list = criteria[0];
            var valClean = val.toString().toLowerCase().replace(/[^a-z0-9]/g, '');
            for (var i = 0; i < list.length; i++) {
              if (list[i]) {
                var itemClean = list[i].toString().toLowerCase().replace(/[^a-z0-9]/g, '');
                if (itemClean === valClean) {
                  finalVal = list[i]; // Cocokkan persis opsi dropdown (misal: "Belum DInilai")
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
        // Jika ditolak oleh validasi data ketat (Reject input), izinkan nilai (Allow invalid) agar tidak fatal
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
    
    data.forEach(function(item) {
      if (!item.ref_control) return;
      
      var code = item.ref_control.toString().trim();
      var targetRow = rowLookup[code];
      
      if (!targetRow) {
        // Baris baru ditambahkan di paling bawah
        targetRow = sheet.getLastRow() + 1;
        rowLookup[code] = targetRow;
        added++;
        
        // --- 6. SALIN FORMAT, GARIS TABEL (BORDER), & DATA VALIDATION (DROPDOWN) DARI BARIS DI ATASNYA ---
        if (sampleFormatRow <= sheet.getLastRow() && sampleFormatRow > headerRowIndex) {
          var srcRange = sheet.getRange(sampleFormatRow, 1, 1, lastCol);
          var dstRange = sheet.getRange(targetRow, 1, 1, lastCol);
          
          // Salin border dan format tampilan
          srcRange.copyTo(dstRange, SpreadsheetApp.CopyPasteType.PASTE_FORMAT, false);
          
          // Salin data validation (dropdown chips)
          srcRange.copyTo(dstRange, SpreadsheetApp.CopyPasteType.PASTE_DATA_VALIDATION, false);
        }
      } else {
        updated++;
      }
      
      // --- 7. TULIS NILAI KE SETIAP KOLOM YANG TERDETEKSI DENGAN SAFESETVALUE ---
      for (var key in colMap) {
        var colIndex = colMap[key];
        if (colIndex && item[key] !== undefined && item[key] !== null && item[key] !== '') {
          var cell = sheet.getRange(targetRow, colIndex);
          safeSetValue(cell, item[key]);
        }
      }
      
      // --- 8. PASTIKAN DROPDOWN CHIPS SELALU AKTIF ---
      // Pastikan kolom Key/Non Key menjadi dropdown
      if (colMap['key_non_key']) {
        var cellKey = sheet.getRange(targetRow, colMap['key_non_key']);
        if (!cellKey.getDataValidation()) {
          var ruleKey = SpreadsheetApp.newDataValidation()
            .requireValueInList(['Key', 'Non Key'], true)
            .setAllowInvalid(true)
            .build();
          cellKey.setDataValidation(ruleKey);
        }
      }
      
      // Pastikan kolom Hasil CSA menjadi dropdown
      if (colMap['hasil_csa']) {
        var cellCsa = sheet.getRange(targetRow, colMap['hasil_csa']);
        if (!cellCsa.getDataValidation()) {
          var ruleCsa = SpreadsheetApp.newDataValidation()
            .requireValueInList(['Efektif', 'Tidak Efektif', 'Tidak Ada Transaksi', 'Sedang Berjalan', 'Belum DInilai', 'Belum Dinilai'], true)
            .setAllowInvalid(true)
            .build();
          cellCsa.setDataValidation(ruleCsa);
        }
      }
    });
    
    // --- 9. OTOMATIS URUTKAN BARIS SPREADSHEET BERDASARKAN ABJAD: Siklus (A-Z), Proses, Sub-Proses, & Kode Kontrol ---
    try {
      var currentLastRow = sheet.getLastRow();
      if (currentLastRow > headerRowIndex) {
        var numDataRows = currentLastRow - headerRowIndex;
        var dataRange = sheet.getRange(headerRowIndex + 1, 1, numDataRows, lastCol);
        
        var sortSpecs = [];
        if (colMap['siklus'])       sortSpecs.push({column: colMap['siklus'], ascending: true});
        if (colMap['proses'])       sortSpecs.push({column: colMap['proses'], ascending: true});
        if (colMap['sub_proses'])   sortSpecs.push({column: colMap['sub_proses'], ascending: true});
        if (colMap['ref_control'])  sortSpecs.push({column: colMap['ref_control'], ascending: true});
        
        if (sortSpecs.length > 0) {
          dataRange.sort(sortSpecs);
        }
      }
    } catch (sortErr) {
      Logger.log("Info urutan data: " + sortErr);
    }

    return ContentService.createTextOutput(JSON.stringify({
      status: 'success',
      message: 'Berhasil sinkronisasi ke Spreadsheet: ' + updated + ' kontrol diperbarui, ' + added + ' kontrol baru ditambahkan serta seluruh baris telah diurutkan berdasarkan abjad A-Z!'
    })).setMimeType(ContentService.MimeType.JSON);
    
  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({
      status: 'error',
      message: err.toString()
    })).setMimeType(ContentService.MimeType.JSON);
  }
}</textarea>
                        </div>

                        <div>
                            <label class="{{ $lbl }}">Target Siklus Bisnis</label>
                            <select name="cycle_id" class="{{ $inp }}">
                                <option value="">Semua Siklus Bisnis (Seluruh Perusahaan)</option>
                                @foreach($cycles as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="{{ $lbl }}">Tahun Laporan</label>
                                <input type="number" name="tahun" value="{{ date('Y') }}" class="{{ $inp }}" required>
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Triwulan</label>
                                <select name="triwulan" class="{{ $inp }}" required>
                                    <option value="TW1">TW1</option>
                                    <option value="TW2">TW2</option>
                                    <option value="TW3">TW3</option>
                                    <option value="TW4">TW4</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 text-xs text-emerald-800 dark:text-emerald-300 space-y-1">
                            <span class="font-bold flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                Sinkronisasi Dua Arah Otomatis
                            </span>
                            <p>Data kontrol dan penilaian CSA terbaru di website (termasuk kontrol baru yang dibuat secara manual) akan langsung dikirim ke Google Spreadsheet secara real-time tanpa perlu unduh file.</p>
                        </div>

                        <div class="flex flex-col-reverse gap-3 pt-4 border-t sm:flex-row sm:justify-end border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showSyncSheetsModal = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold text-white transition rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/20 active:scale-[0.99]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Kirim &amp; Perbarui Spreadsheet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @include('busdev.bpm-icofr.partials.pic-modal')
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Ambil data asli dari Database (dikirim dari Controller)
    const summaryData = @json($summaryData);

    // Siapkan array kosong untuk sumbu X dan Y
    const labelsSiklus = [];
    const dataCSA = [];
    const dataBPM = [];
    const dataRCM = [];
    
    let totalKey = 0;
    let totalNonKey = 0;

    // Masukkan data dari database ke dalam array grafik
    summaryData.forEach(item => {
        labelsSiklus.push('Siklus ' + item.code);
        dataCSA.push(item.csa_progress); // Progres CSA
        dataBPM.push(item.bpm_pct);      // Progres Pemutakhiran BPM
        dataRCM.push(item.rcm_pct);      // Progres Pemutakhiran RCM
        
        totalKey += item.key_controls;
        totalNonKey += item.non_key_controls;
    });

    // =========================================================
    // RENDER GRAFIK 1: BAR CHART (PROGRES IMPLEMENTASI)
    // =========================================================
    const ctxBar = document.getElementById('implementasiChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: labelsSiklus,
            datasets: [
                {
                    label: 'Progres CSA (%)',
                    data: dataCSA,
                    backgroundColor: '#3b82f6', // Biru
                    borderRadius: 4,
                },
                {
                    label: 'Update BPM (%)',
                    data: dataBPM,
                    backgroundColor: '#10b981', // Hijau
                    borderRadius: 4,
                },
                {
                    label: 'Update RCM (%)',
                    data: dataRCM,
                    backgroundColor: '#f59e0b', // Kuning/Amber
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, max: 100 }
            },
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // =========================================================
    // RENDER GRAFIK 2: DOUGHNUT CHART (KOMPOSISI RCM)
    // =========================================================
    const ctxDonut = document.getElementById('komposisiRcmChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Key Control', 'Non-Key Control'],
            datasets: [{
                data: [totalKey, totalNonKey],
                backgroundColor: ['#8b5cf6', '#cbd5e1'], // Ungu dan Abu-abu
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
</x-app-layout>

<style>
    /* Sembunyikan scrollbar pada baris tab di layar kecil */
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>