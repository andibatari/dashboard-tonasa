<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <a href="{{ route('busdev.bpm-icofr.index') }}" aria-label="Kembali"
               class="p-1.5 sm:p-2 transition-all bg-white border shadow-xs rounded-xl border-slate-200 text-slate-500 hover:text-[#F5333F] hover:bg-red-50 hover:border-red-200 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 dark:hover:text-white flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="min-w-0">
                <h2 class="text-xs sm:text-sm md:text-base font-extrabold tracking-tight text-slate-900 dark:text-white truncate">Detail Matriks RCM</h2>
                <p class="hidden sm:block text-[11px] font-medium text-slate-500 truncate">Kelola hierarki proses dan kontrol untuk siklus terkait.</p>
            </div>
        </div>
    </x-slot>

    @php
        // ---------- Ringkasan siklus ----------
        $allControls  = $cycle->processes->flatMap(fn($p) => $p->controls);
        $controlTotal = $allControls->count();
        $keyTotal     = $allControls->filter(fn($c) => $c->is_key_control)->count();

        // ---------- Sub proses ----------
        $allSubs      = $cycle->processes->flatMap(fn($p) => $p->subProcesses);
        $subNames     = $allSubs->pluck('name', 'id');
        $subTotal     = $allSubs->count();
        $subToProcess = $allSubs->mapWithKeys(fn($sp) => [$sp->id => $sp->icofr_process_id]);

        $allIndex     = $allControls->map(fn($c) => [
            'k' => $c->is_key_control ? 'key' : 'nonkey',
            't' => \Illuminate\Support\Str::lower($c->risk_code . ' ' . $c->risk_description . ' ' . $c->control_code . ' ' . $c->control_description . ' ' . ($c->unit->name_unit ?? '') . ' ' . $subNames->get($c->icofr_sub_process_id, '')),
        ])->values();

        // ---------- Data Tabel RCM (Spreadsheet View) ----------
        $tableControls = $allControls->map(function($c) use ($reportsByControl, $cycle) {
            $reports    = $reportsByControl[$c->id] ?? collect();
            $lastReport = $reports->first();
            $process    = $c->process;
            $unitName   = $c->unit->name_unit ?? ($c->executor_function ?? ($c->executor ?? '-'));

            return [
                'id'                  => $c->id,
                'control_code'        => $c->control_code,
                'control_description' => $c->control_description,
                'is_key_control'      => (bool) $c->is_key_control,
                'risk_code'           => $c->risk_code ?: ('R-' . $c->control_code),
                'risk_description'    => $c->risk_description ?: '-',
                'risk_value'          => $c->risk_value ?: '',
                'fraud_risk'          => $c->fraud_risk ?: 'Tidak',
                'frequency'           => $c->frequency ?: '-',
                'pic'                 => $unitName,
                'pic_name'            => $c->executor ?: '-',
                'pic_position'        => $c->executor_position ?: '-',
                'pic_unit'            => $c->executor_unit ?: ($c->unit->name_unit ?? ($c->executor_function ?? '-')),
                'process_id'          => $c->icofr_process_id,
                'process_code'        => $process->code ?? '-',
                'process_name'        => $process->name ?? '-',
                'sub_code'            => $c->subProcess->code ?? '',
                'sub_name'            => $c->subProcess->name ?? '',
                'csa_tw'              => $lastReport ? ($lastReport->triwulan . '/' . $lastReport->tahun) : '-',
                'csa_status'          => $lastReport ? $lastReport->hasil_csa : 'belum_dinilai',
                'csa_status_label'    => $lastReport ? (\App\Models\IcofrControl::HASIL_CSA[$lastReport->hasil_csa] ?? $lastReport->hasil_csa) : 'Belum dinilai',
                'csa_ket'             => $lastReport ? ($lastReport->keterangan_tidak_efektif ?? '') : '',
                'objective'           => $c->control_objective ?? '',
                'procedure'           => $c->control_procedure ?? '',
                'supporting_documents'=> $c->supporting_documents ?? '',
                'delete_url'          => route('busdev.bpm-icofr.destroy-control', $c->id),
                'raw_payload'         => [
                    'url'  => route('busdev.bpm-icofr.update-control', $c->id),
                    'data' => [
                        'icofr_process_id'     => (string) $c->icofr_process_id,
                        'icofr_sub_process_id' => (string) ($c->icofr_sub_process_id ?? ''),
                        'unit_id'              => (string) ($c->unit_id ?? ''),
                        'executor'             => $c->executor ?? '',
                        'executor_position'    => $c->executor_position ?? '',
                        'executor_unit'        => $c->executor_unit ?: ($c->unit->name_unit ?? ($c->executor_function ?? '')),
                        'risk_code'            => $c->risk_code,
                        'risk_description'     => $c->risk_description,
                        'risk_value'           => $c->risk_value ?? '',
                        'fraud_risk'           => $c->fraud_risk ?? 'Tidak',
                        'control_code'         => $c->control_code,
                        'control_description'  => $c->control_description,
                        'is_key_control'       => $c->is_key_control ? '1' : '0',
                        'control_objective'    => $c->control_objective ?? '',
                        'control_procedure'    => $c->control_procedure ?? '',
                        'supporting_documents' => $c->supporting_documents ?? '',
                        'frequency'            => $c->frequency ?? '',
                        'tahun'                => $lastReport ? $lastReport->tahun : date('Y'),
                        'triwulan'             => $lastReport ? $lastReport->triwulan : 'TW1',
                        'hasil_csa'            => $lastReport ? $lastReport->hasil_csa : 'belum_dinilai',
                        'keterangan_tidak_efektif' => $lastReport ? $lastReport->keterangan_tidak_efektif : '',
                    ]
                ]
            ];
        })->values();

        // ---------- Gaya & ikon UI Modern ----------
        $card = 'bg-white border border-slate-200/60 shadow-sm rounded-2xl dark:bg-[#161a23] dark:border-slate-800/80';
        $inp  = 'block w-full px-4 py-2.5 text-sm transition bg-slate-50/50 border border-slate-200 rounded-xl placeholder:text-slate-400 focus:bg-white focus:border-[#F5333F] focus:ring-4 focus:ring-red-500/10 focus:outline-none dark:bg-slate-900/50 dark:border-slate-700 dark:text-white dark:focus:ring-red-500/20';
        $lbl  = 'block mb-1.5 text-sm font-bold text-slate-700 dark:text-slate-300';
        $btnGhost   = 'px-5 py-2.5 text-sm font-bold text-slate-600 transition bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-900 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700 dark:hover:text-white';
        $btnPrimary = 'px-5 py-2.5 text-sm font-bold text-white transition-all bg-[#F5333F] rounded-xl shadow-lg shadow-red-500/30 hover:bg-red-600 hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F5333F]';
        $P = [
            'plus'   => 'M12 4v16m8-8H4',
            'search' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
            'check'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'alert'  => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'pencil' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'trash'  => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
            'x'      => 'M6 18L18 6M6 6l12 12',
            'eye'    => 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        ];
        $ic = fn($d, $cls = 'w-5 h-5') => '<svg class="' . $cls . '" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . $d . '"/></svg>';
    @endphp

    <div class="py-8"
         @keydown.escape.window="showModal = false; showSub = false; showProcess = false; showDetailModal = false; showSyncSheetsModal = false"
         x-data="{
            activeTab: 'hierarchy', // 'hierarchy' | 'spreadsheet'
            q: '',
            filterType: 'semua',

            // --- State Tabel Spreadsheet & Pagination ---
            tableControls: @js($tableControls),
            searchTable: '',
            filterTableKey: 'all',
            filterTableProcess: 'all',
            filterTableCsa: 'all',
            currentPage: 1,
            perPage: 10,
            showDetailModal: false,
            selectedControl: null,
            showSyncSheetsModal: false,
            syncMode: 'pull',
            showScriptGuide: false,
            copiedScript: false,

            get filteredTableRows() {
                const q = (this.searchTable || '').toLowerCase().trim();
                return this.tableControls.filter(r => {
                    const matchQ = !q || 
                        (r.control_code && r.control_code.toLowerCase().includes(q)) ||
                        (r.control_description && r.control_description.toLowerCase().includes(q)) ||
                        (r.risk_code && r.risk_code.toLowerCase().includes(q)) ||
                        (r.risk_description && r.risk_description.toLowerCase().includes(q)) ||
                        (r.risk_value && r.risk_value.toLowerCase().includes(q)) ||
                        (r.fraud_risk && r.fraud_risk.toLowerCase().includes(q)) ||
                        (r.pic && r.pic.toLowerCase().includes(q)) ||
                        (r.process_name && r.process_name.toLowerCase().includes(q)) ||
                        (r.sub_name && r.sub_name.toLowerCase().includes(q)) ||
                        (r.frequency && r.frequency.toLowerCase().includes(q));

                    const matchKey = this.filterTableKey === 'all' || 
                        (this.filterTableKey === 'key' && r.is_key_control) ||
                        (this.filterTableKey === 'nonkey' && !r.is_key_control);

                    const matchProc = this.filterTableProcess === 'all' || String(r.process_id) === String(this.filterTableProcess);
                    const matchCsa  = this.filterTableCsa === 'all' || r.csa_status === this.filterTableCsa;

                    return matchQ && matchKey && matchProc && matchCsa;
                });
            },
            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredTableRows.length / this.perPage));
            },
            get paginatedTableRows() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredTableRows.slice(start, start + this.perPage);
            },
            setPage(p) {
                if (p >= 1 && p <= this.totalPages) this.currentPage = p;
            },
            viewDetail(item) {
                this.selectedControl = item;
                this.showDetailModal = true;
            },

            // --- Modal Kontrol ---
            showModal: false,
            mode: 'add',
            formUrl: '',
            blank: { 
                icofr_process_id: '', 
                icofr_sub_process_id: '', 
                unit_id: '', 
                risk_code: '', 
                risk_description: '', 
                risk_value: '',
                fraud_risk: 'Tidak',
                control_code: '', 
                control_description: '', 
                is_key_control: '1', 
                control_objective: '', 
                control_procedure: '', 
                supporting_documents: '', 
                frequency: '', 
                tahun: '{{ date('Y') }}', 
                triwulan: 'TW1', 
                hasil_csa: 'belum_dinilai', 
                keterangan_tidak_efektif: '' 
            },
            form: { 
                icofr_process_id: '', 
                icofr_sub_process_id: '', 
                unit_id: '', 
                risk_code: '', 
                risk_description: '', 
                risk_value: '',
                fraud_risk: 'Tidak',
                control_code: '', 
                control_description: '', 
                is_key_control: '1', 
                control_objective: '', 
                control_procedure: '', 
                supporting_documents: '', 
                frequency: '', 
                tahun: '{{ date('Y') }}', 
                triwulan: 'TW1', 
                hasil_csa: 'belum_dinilai', 
                keterangan_tidak_efektif: '' 
            },
            subProcessOf: @js($subToProcess),
            showPicModal: false,
            pics: @js($icofrPics),
            get availablePicUnits() {
                return [...new Set(this.pics.map(p => p.unit).filter(Boolean))].sort();
            },
            get availablePicPositions() {
                if (!this.form.executor_unit) {
                    return [...new Set(this.pics.map(p => p.position).filter(Boolean))].sort();
                }
                return [...new Set(this.pics.filter(p => p.unit === this.form.executor_unit).map(p => p.position).filter(Boolean))].sort();
            },
            get availablePicNames() {
                let list = this.pics;
                if (this.form.executor_unit) {
                    list = list.filter(p => p.unit === this.form.executor_unit);
                }
                if (this.form.executor_position) {
                    list = list.filter(p => p.position === this.form.executor_position);
                }
                return [...new Set(list.map(p => p.name).filter(Boolean))].sort();
            },
            onUnitChange() {
                // Jangan paksa kosongkan jika sedang mengetik, tapi update saran jabatan
            },
            onPositionChange() {
                const match = this.pics.find(p => 
                    (!this.form.executor_unit || p.unit === this.form.executor_unit) && 
                    p.position === this.form.executor_position && 
                    p.name
                );
                if (match && !this.form.executor) {
                    this.form.executor = match.name;
                    if (!this.form.executor_unit && match.unit) {
                        this.form.executor_unit = match.unit;
                    }
                }
            },
            onNameChange() {
                const match = this.pics.find(p => p.name === this.form.executor);
                if (match) {
                    if (match.unit) this.form.executor_unit = match.unit;
                    if (match.position) this.form.executor_position = match.position;
                }
            },
            openAdd(pid, sid) {
                this.mode = 'add';
                this.formUrl = '{{ route("busdev.bpm-icofr.store-control") }}';
                this.form = { ...this.blank, icofr_process_id: pid || '', icofr_sub_process_id: sid || '' };
                this.showModal = true;
            },
            openEdit(p) { this.mode = 'edit'; this.formUrl = p.url; this.form = p.data; this.showModal = true },

            // --- Modal Sub Proses ---
            showSub: false,
            subMode: 'add',
            subUrl: '',
            subForm: { icofr_process_id: '', code: '', name: '' },
            openSubAdd(pid) {
                this.subMode = 'add';
                this.subUrl = '{{ Route::has("busdev.bpm-icofr.store-sub-process") ? route("busdev.bpm-icofr.store-sub-process") : "#" }}';
                this.subForm = { icofr_process_id: pid, code: '', name: '' };
                this.showSub = true;
            },
            openSubEdit(p) { this.subMode = 'edit'; this.subUrl = p.url; this.subForm = p.data; this.showSub = true },

            // --- Modal Edit Proses ---
            showProcess: false,
            processUrl: '',
            processForm: { code: '', name: '' },
            openProcessEdit(p) { 
                this.processUrl = p.url; 
                this.processForm = p.data; 
                this.showProcess = true; 
            },

            // --- Pencarian & Filter (Hierarchy) ---
            all: @js($allIndex),
            get filtering() { return this.q !== '' || this.filterType !== 'semua' },
            get shown() { return this.all.filter(c => this.matches(c.t, c.k)).length },
            matches(t, k) { return (this.filterType === 'semua' || this.filterType === k) && t.includes(this.q.toLowerCase()) }
         }">
        
        <div class="w-full">

            {{-- ============ Notifikasi Cerdas ============ --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition.opacity role="status"
                     class="flex items-start gap-3 px-4 py-3 mb-6 text-sm font-bold border shadow-sm rounded-xl text-emerald-800 bg-emerald-50 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800">
                    {!! $ic($P['check'], 'w-5 h-5 mt-0.5 flex-none text-emerald-500') !!}
                    <p class="flex-1">{{ session('success') }}</p>
                    <button type="button" @click="show = false" class="opacity-60 hover:opacity-100">{!! $ic($P['x'], 'w-4 h-4') !!}</button>
                </div>
            @endif
            @if($errors->any())
                <div role="alert" class="flex items-start gap-3 px-4 py-4 mb-6 text-sm border shadow-sm rounded-xl text-red-800 bg-red-50 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800">
                    {!! $ic($P['alert'], 'w-5 h-5 mt-0.5 flex-none text-red-500') !!}
                    <ul class="pl-4 space-y-1 list-disc">
                        @foreach($errors->all() as $error)<li class="font-medium">{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- ============ Kartu Info Siklus Mewah ============ --}}
            <div class="p-6 mb-6 lg:p-8 {{ $card }} bg-gradient-to-br from-white to-slate-50 dark:from-[#161a23] dark:to-[#0f1115]">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-5">
                        <div class="flex items-center justify-center flex-none w-16 h-16 shadow-inner rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20">
                            <span class="text-2xl font-black text-[#F5333F]">{{ $cycle->code }}</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ $cycle->name }}</h3>
                            @if($cycle->bpm_file_path)
                                <a href="{{ asset('storage/' . $cycle->bpm_file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 mt-2 text-sm font-bold text-emerald-600 transition-colors hover:text-emerald-700 hover:underline dark:text-emerald-400">
                                    {!! $ic($P['eye'], 'w-4 h-4') !!} Lihat File Business Process Mapping (BPM)
                                </a>
                            @else
                                <p class="inline-flex items-center gap-1.5 mt-2 text-sm font-medium text-slate-500">
                                    {!! $ic($P['alert'], 'w-4 h-4') !!} BPM belum diunggah.
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                        <div class="flex gap-6 px-6 py-4 bg-white border shadow-sm dark:bg-[#1A1A1A] rounded-2xl border-slate-200/60 dark:border-slate-800">
                            <div><p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Proses</p><p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ $cycle->processes->count() }}</p></div>
                            <div class="w-px bg-slate-200 dark:bg-slate-800"></div>
                            <div><p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sub</p><p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ $subTotal }}</p></div>
                            <div class="w-px bg-slate-200 dark:bg-slate-800"></div>
                            <div><p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total RCM</p><p class="mt-1 text-2xl font-black text-[#F5333F]">{{ $controlTotal }}</p></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ Sub-Menu Switcher (Hierarki BPM vs Data Spreadsheet RCM) ============ --}}
            <div class="p-2 mb-6 bg-white border border-slate-200/80 rounded-2xl shadow-sm dark:bg-[#161a23] dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar w-full sm:w-auto">
                    <button type="button" @click="activeTab = 'hierarchy'" 
                        :class="activeTab === 'hierarchy' 
                            ? 'bg-[#F5333F] text-white shadow-sm font-bold' 
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-semibold'"
                        class="px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2.5 transition-all whitespace-nowrap shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"></path></svg>
                        <span>Tampilan Hierarki BPM</span>
                        <span class="px-2 py-0.5 text-[11px] rounded-full font-mono" :class="activeTab === 'hierarchy' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'">
                            {{ $cycle->processes->count() }} Proses
                        </span>
                    </button>

                    <button type="button" @click="activeTab = 'spreadsheet'" 
                        :class="activeTab === 'spreadsheet' 
                            ? 'bg-[#F5333F] text-white shadow-sm font-bold' 
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-semibold'"
                        class="px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2.5 transition-all whitespace-nowrap shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5m7.5 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5m7.5 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125"></path></svg>
                        <span>Data Spreadsheet RCM</span>
                        <span class="px-2 py-0.5 text-[11px] rounded-full font-mono" :class="activeTab === 'spreadsheet' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'">
                            {{ $controlTotal }} Baris
                        </span>
                    </button>
                </div>

                {{-- Action Shortcuts --}}
                <div class="flex items-center gap-2">
                    <button type="button" @click="showSyncSheetsModal = true" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white transition bg-emerald-600 rounded-xl shadow-sm hover:bg-emerald-700 shadow-emerald-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                        Sinkronkan Google Sheets
                    </button>
                </div>
            </div>

            {{-- ==================================================================== --}}
            {{-- TAB 1: TAMPILAN HIERARKI BPM (KARTU PROSES & SUB PROSES)             --}}
            {{-- ==================================================================== --}}
            <div x-show="activeTab === 'hierarchy'" x-transition.opacity.duration.200ms class="space-y-6">

                {{-- Pencarian & Filter Modern --}}
                @if($controlTotal > 0)
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex p-1 bg-white border shadow-sm rounded-xl border-slate-200/80 dark:bg-[#161a23] dark:border-slate-800">
                            @foreach([['semua', 'Semua Data', $controlTotal], ['key', 'Key Control', $keyTotal], ['nonkey', 'Non-Key', $controlTotal - $keyTotal]] as [$k, $l, $n])
                                <button type="button" @click="filterType = '{{ $k }}'"
                                        :class="filterType === '{{ $k }}' ? 'bg-slate-900 text-white shadow dark:bg-slate-700' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'"
                                        class="flex items-center gap-2 px-4 py-2 text-sm font-bold transition-all rounded-lg">
                                    {{ $l }} <span class="px-2 py-0.5 text-[10px] rounded-md" :class="filterType === '{{ $k }}' ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800'">{{ $n }}</span>
                                </button>
                            @endforeach
                        </div>
                        <div class="relative w-full sm:w-80">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">{!! $ic($P['search'], 'w-4 h-4') !!}</span>
                            <input type="search" x-model="q" placeholder="Cari kode atau deskripsi kontrol..." class="{{ $inp }} !pl-11 !rounded-full shadow-sm">
                        </div>
                    </div>
                @endif

                {{-- Daftar Proses, Sub Proses & Kontrol --}}
                <div class="space-y-6">
                    @forelse($cycle->processes as $process)
                        @php
                            $subs    = $process->subProcesses->sortBy('code')->values();
                            $subIds  = $subs->pluck('id')->all();
                            $hasSubs = ! empty($subIds);

                            // Kunci pencarian AlpineJS
                            $ctrlIndex = $process->controls->mapWithKeys(fn($c) => ['c' . $c->id => [
                                'k' => $c->is_key_control ? 'key' : 'nonkey',
                                't' => \Illuminate\Support\Str::lower($c->risk_code . ' ' . $c->risk_description . ' ' . $c->control_code . ' ' . $c->control_description . ' ' . ($c->unit->name_unit ?? '') . ' ' . $subNames->get($c->icofr_sub_process_id, '')),
                            ]]);

                            // Pengelompokan Data
                            $groups = [];
                            foreach ($subs as $sub) {
                                $groups[] = ['sub' => $sub, 'controls' => $process->controls->where('icofr_sub_process_id', $sub->id)->values()];
                            }
                            $loose = $process->controls->filter(fn($c) => ! in_array($c->icofr_sub_process_id, $subIds))->values();
                            if ($loose->isNotEmpty() || empty($groups)) {
                                $groups[] = ['sub' => null, 'controls' => $loose];
                            }
                        @endphp

                        <section x-data="{ ctrls: @js($ctrlIndex), collapsed: false }"
                                 x-show="!filtering || Object.values(ctrls).some(c => matches(c.t, c.k))"
                                 class="overflow-hidden {{ $card }} shadow-md">

                            {{-- HEADER PROSES UTAMA (DENGAN ACCORDION CHEVRON) --}}
                            <header class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b bg-gradient-to-r from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-900/50 border-slate-200/80 dark:border-slate-800">
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="collapsed = !collapsed" title="Ciutkan/Buka Proses" 
                                        class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg transition-transform duration-200"
                                        :class="{ '-rotate-90': collapsed }">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <span class="px-3 py-1 text-xs font-black tracking-wider rounded-lg text-slate-700 bg-white shadow-sm ring-1 ring-inset ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700">
                                        PROSES {{ $process->code }}
                                    </span>
                                    <h4 class="text-base sm:text-lg font-black text-slate-900 dark:text-white cursor-pointer select-none" @click="collapsed = !collapsed">
                                        {{ $process->name }}
                                    </h4>
                                    
                                    {{-- Tombol Edit & Delete Proses Utama --}}
                                    <div class="flex items-center gap-1 pl-3 ml-2 border-l border-slate-300 dark:border-slate-700">
                                        <button type="button" @click="openProcessEdit({ url: '{{ Route::has('busdev.bpm-icofr.update-process') ? route('busdev.bpm-icofr.update-process', $process->id) : '#' }}', data: { code: '{{ $process->code }}', name: '{{ $process->name }}' } })" title="Ubah Proses" class="p-1.5 transition rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                            {!! $ic($P['pencil'], 'w-4 h-4') !!}
                                        </button>
                                        <form action="{{ Route::has('busdev.bpm-icofr.destroy-process') ? route('busdev.bpm-icofr.destroy-process', $process->id) : '#' }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus proses ini beserta seluruh sub-proses dan kontrol di dalamnya?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus Proses" class="p-1.5 transition rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                {!! $ic($P['trash'], 'w-4 h-4') !!}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                {{-- Tombol Tambah Sub Proses di level Header --}}
                                <button type="button" @click="openSubAdd({{ $process->id }})" class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold transition-all bg-white border rounded-xl border-slate-200 shadow-sm text-slate-700 hover:text-[#F5333F] hover:border-red-200 hover:bg-red-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700">
                                    {!! $ic($P['plus'], 'w-4 h-4') !!} Tambah Sub Proses
                                </button>
                            </header>

                            <div x-show="!collapsed" x-collapse>
                                {{-- Status Checkbox Proses DENGAN TOOLTIP INTERAKTIF --}}
                                @if(Route::has('busdev.bpm-icofr.update-process-status'))
                                    <form action="{{ route('busdev.bpm-icofr.update-process-status', $process->id) }}" method="POST" class="flex flex-wrap items-center px-6 py-3.5 text-xs border-b gap-x-8 gap-y-3 border-slate-100 bg-white/50 dark:bg-[#1a1f2c]/50 dark:border-slate-800">
                                        @csrf @method('PUT')
                                        
                                        <div class="relative flex items-center gap-2 group">
                                            <span class="text-xs font-black tracking-widest text-slate-400 uppercase">Status Dokumen:</span>
                                            <span class="flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-slate-300 rounded-full cursor-help dark:bg-slate-700 group-hover:bg-[#F5333F] transition-colors">?</span>
                                            
                                            <div class="absolute left-0 z-50 invisible p-4 mt-2 text-xs leading-relaxed transition-opacity opacity-0 bg-slate-900 text-slate-300 w-80 sm:w-96 rounded-xl shadow-2xl top-full group-hover:visible group-hover:opacity-100 ring-1 ring-white/10 pointer-events-none">
                                                <p class="mb-2 text-sm font-bold text-white">Panduan Pengisian Status</p>
                                                <ul class="space-y-2 list-none">
                                                    <li><strong class="text-[#F5333F]">Dokumentasi:</strong> Centang jika dokumen narasi/rancangan awal ICoFR untuk proses ini telah selesai disusun.</li>
                                                    <li><strong class="text-[#F5333F]">Validasi:</strong> Centang jika rancangan kontrol pada proses ini sudah divalidasi dan disetujui bersama PIC.</li>
                                                    <li><strong class="text-[#F5333F]">Update BPM:</strong> Centang jika flowchart BPM proses ini sudah diperbarui.</li>
                                                    <li><strong class="text-[#F5333F]">Update RCM:</strong> Centang jika matriks risiko dan kontrol proses ini sudah dimutakhirkan.</li>
                                                </ul>
                                            </div>
                                        </div>

                                        @foreach([
                                            'dokumentasi_done' => 'Dokumentasi',
                                            'validasi_done'    => 'Validasi',
                                            'bpm_updated'      => 'Update BPM',
                                            'rcm_updated'      => 'Update RCM',
                                        ] as $field => $label)
                                            <label class="flex items-center gap-2 font-bold cursor-pointer transition-colors hover:text-[#F5333F] text-slate-600 dark:text-slate-400">
                                                <input type="checkbox" name="{{ $field }}" value="1" @checked($process->$field) onchange="this.form.submit()"
                                                       class="w-4 h-4 transition-all rounded border-slate-300 text-[#F5333F] focus:ring-[#F5333F] dark:border-slate-600 dark:bg-slate-800">
                                                {!! $label !!}
                                            </label>
                                        @endforeach
                                    </form>
                                @endif

                                {{-- LOOPING SUB-PROSES --}}
                                @foreach($groups as $group)
                                    @php
                                        $sub        = $group['sub'];
                                        $gControls  = $group['controls'];
                                        $gKeys      = $gControls->map(fn($c) => 'c' . $c->id)->values();
                                        $subArg     = $sub ? $sub->id : "null";
                                        $subPayload = ($sub) ? [
                                            'url'  => Route::has('busdev.bpm-icofr.update-sub-process') ? route('busdev.bpm-icofr.update-sub-process', $sub->id) : '#',
                                            'data' => ['icofr_process_id' => (string) $process->id, 'code' => $sub->code, 'name' => $sub->name],
                                        ] : null;
                                    @endphp

                                    <div x-show="!filtering || @js($gKeys).some(id => matches(ctrls[id].t, ctrls[id].k))" class="bg-white dark:bg-[#1A1A1A]">

                                        {{-- HEADER SUB PROSES --}}
                                        @if($hasSubs || $sub)
                                            <div class="flex items-center justify-between gap-4 px-6 py-3.5 border-y border-slate-100 bg-slate-50/80 dark:bg-slate-800/40 dark:border-slate-800/80">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    @if($sub)
                                                        <span class="flex-none px-2.5 py-1 text-xs font-black rounded-lg text-[#F5333F] bg-red-50 dark:bg-red-500/10">SUB {{ rtrim($process->code, '. ') }}.{{ $sub->code }}</span>
                                                        <h5 class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ $sub->name }}</h5>
                                                    @else
                                                        <span class="flex-none px-2.5 py-1 text-xs font-bold rounded-lg text-slate-500 bg-slate-200/50 dark:bg-slate-700">Lainnya</span>
                                                        <h5 class="text-sm font-bold text-slate-500 italic">Kontrol tanpa sub-proses</h5>
                                                    @endif
                                                </div>
                                                
                                                <div class="flex items-center gap-2 flex-none">
                                                    <button type="button" @click="openAdd({{ $process->id }}, {{ $subArg }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold transition-colors rounded-lg text-[#F5333F] bg-red-50 hover:bg-[#F5333F] hover:text-white dark:bg-red-500/10 dark:hover:bg-[#F5333F]">
                                                        {!! $ic($P['plus'], 'w-3.5 h-3.5') !!} Tambah RCM
                                                    </button>

                                                    @if($subPayload)
                                                        <div class="flex items-center gap-1 pl-2 ml-2 border-l border-slate-200 dark:border-slate-700">
                                                            <button type="button" @click="openSubEdit(@js($subPayload))" title="Ubah sub proses" class="p-1.5 transition rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                                                {!! $ic($P['pencil'], 'w-4 h-4') !!}
                                                            </button>
                                                            <form action="{{ Route::has('busdev.bpm-icofr.destroy-sub-process') ? route('busdev.bpm-icofr.destroy-sub-process', $sub->id) : '#' }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus sub proses ini?');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" title="Hapus sub proses" class="p-1.5 transition rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                                    {!! $ic($P['trash'], 'w-4 h-4') !!}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- DAFTAR KONTROL DI DALAM SUB-PROSES --}}
                                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                                            @forelse($gControls as $control)
                                                @php
                                                    $allReports       = $reportsByControl[$control->id] ?? collect();
                                                    $lastEditedReport = $allReports->sortByDesc('updated_at')->first();
                                                    $defaultYear      = date('Y');
                                                    $defaultTW        = 'TW1';

                                                    $editPayload = [
                                                        'url'  => route('busdev.bpm-icofr.update-control', $control->id),
                                                        'data' => [
                                                            'icofr_process_id'     => (string) $control->icofr_process_id,
                                                            'icofr_sub_process_id' => (string) ($control->icofr_sub_process_id ?? ''),
                                                            'unit_id'              => (string) ($control->unit_id ?? ''),
                                                            'risk_code'            => $control->risk_code,
                                                            'risk_description'     => $control->risk_description,
                                                            'risk_value'           => $control->risk_value ?? '',
                                                            'fraud_risk'           => $control->fraud_risk ?? 'Tidak',
                                                            'control_code'         => $control->control_code,
                                                            'control_description'  => $control->control_description,
                                                            'is_key_control'       => $control->is_key_control ? '1' : '0',
                                                            'control_objective'    => $control->control_objective ?? '',
                                                            'control_procedure'    => $control->control_procedure ?? '',
                                                            'supporting_documents' => $control->supporting_documents ?? '',
                                                            'frequency'            => $control->frequency ?? '',
                                                            'tahun'                => $lastEditedReport ? $lastEditedReport->tahun : $defaultYear,
                                                            'triwulan'             => $lastEditedReport ? $lastEditedReport->triwulan : $defaultTW,
                                                            'hasil_csa'            => $lastEditedReport ? $lastEditedReport->hasil_csa : 'belum_dinilai',
                                                            'keterangan_tidak_efektif' => $lastEditedReport ? $lastEditedReport->keterangan_tidak_efektif : '',
                                                        ],
                                                    ];
                                                @endphp
                                                
                                                <div x-data="{ openDetail: false }" x-show="matches(ctrls['c{{ $control->id }}'].t, ctrls['c{{ $control->id }}'].k)"
                                                    class="grid gap-6 px-6 py-6 transition-colors border-b border-slate-100 dark:border-slate-800 md:grid-cols-12 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 group">

                                                    {{-- Kolom 1: Risiko (Ref. Risk, Deskripsi, Nilai Risiko, Fraud Risk) --}}
                                                    <div class="md:col-span-4">
                                                        <p class="mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase md:hidden">Risiko Teridentifikasi</p>
                                                        <div class="flex items-start gap-3">
                                                            <span class="inline-flex px-2 py-1 text-xs font-black rounded-lg text-red-700 bg-red-100 border border-red-200/60 dark:bg-red-500/20 shadow-sm shrink-0">
                                                                {{ $control->risk_code }}
                                                            </span>
                                                            <div class="space-y-2">
                                                                <p class="text-sm font-semibold leading-relaxed text-slate-800 dark:text-slate-200">
                                                                    {{ $control->risk_description }}
                                                                </p>

                                                                {{-- Badges Nilai Risiko & Risiko Kecurangan --}}
                                                                <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                                                    @if($control->risk_value)
                                                                        @php
                                                                            $vLow = strtolower($control->risk_value);
                                                                            $vCls = str_contains($vLow, 'tinggi') || str_contains($vLow, 'high')
                                                                                ? 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-400'
                                                                                : (str_contains($vLow, 'sedang') || str_contains($vLow, 'medium')
                                                                                    ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400'
                                                                                    : 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400');
                                                                        @endphp
                                                                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-md border {{ $vCls }}">
                                                                            Nilai: {{ $control->risk_value }}
                                                                        </span>
                                                                    @endif

                                                                    @if($control->fraud_risk)
                                                                        @php
                                                                            $fLow = strtolower($control->fraud_risk);
                                                                            $fCls = str_contains($fLow, 'ya') || str_contains($fLow, 'yes')
                                                                                ? 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 font-bold'
                                                                                : 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400';
                                                                        @endphp
                                                                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium rounded-md border {{ $fCls }}">
                                                                            Kecurangan: {{ $control->fraud_risk }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Kolom 2: Kontrol Utama & Detail Accordion --}}
                                                    <div class="md:col-span-6 border-t pt-4 md:pt-0 md:border-t-0 md:border-l border-slate-100 dark:border-slate-800 md:pl-6">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <p class="text-[11px] font-bold tracking-wider text-slate-400 uppercase md:hidden">Langkah Kontrol</p>
                                                            @if($control->is_key_control)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-extrabold tracking-wider rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-800 uppercase shadow-sm">
                                                                    Key Control
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-extrabold tracking-wider rounded-md bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900/50 uppercase">
                                                                    Non-Key
                                                                </span>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="flex items-start gap-3">
                                                            <span class="inline-flex px-2 py-1 text-xs font-black rounded-lg text-emerald-700 bg-emerald-100 border border-emerald-200/60 shadow-sm shrink-0">
                                                                {{ $control->control_code }}
                                                            </span>
                                                            
                                                            <div class="w-full space-y-2">
                                                                <p class="text-sm font-medium leading-relaxed text-slate-700 dark:text-slate-300">{{ $control->control_description }}</p>
                                                                
                                                                {{-- Badge Info Tambahan (Pelaksana & Frekuensi) --}}
                                                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-md bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                                                        PIC: {{ $control->unit->name_unit ?? $control->executor_function ?? '-' }}
                                                                    </span>
                                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-md bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                                                        Frekuensi: {{ $control->frequency ?: '-' }}
                                                                    </span>
                                                                    @if($lastEditedReport && $lastEditedReport->hasil_csa !== 'belum_dinilai')
                                                                        <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold rounded-md bg-amber-50 text-amber-700 border border-amber-200/70">
                                                                            CSA {{ $lastEditedReport->triwulan }}/{{ $lastEditedReport->tahun }}: {{ \App\Models\IcofrControl::HASIL_CSA[$lastEditedReport->hasil_csa] ?? $lastEditedReport->hasil_csa }}
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                {{-- Tombol Toggle Lihat Detail --}}
                                                                <button @click="openDetail = !openDetail" type="button" class="inline-flex items-center gap-1 mt-2 text-xs font-bold transition text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                                    <span x-text="openDetail ? 'Sembunyikan Prosedur CSA' : 'Lihat Detail Prosedur CSA'"></span>
                                                                    <svg :class="{'rotate-180': openDetail}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                                </button>

                                                                {{-- AREA DETAIL TERSEMBUNYI (Drop-down) --}}
                                                                <div x-show="openDetail" x-collapse style="display: none;" class="pt-4 mt-3 border-t border-slate-100 dark:border-slate-800/60">
                                                                    <dl class="grid gap-y-5 gap-x-6 sm:grid-cols-2 text-xs">
                                                                        <div class="sm:col-span-2">
                                                                            <dt class="font-bold tracking-widest text-[10px] text-slate-400 uppercase mb-1">Obyektif Penilaian (Tujuan CSA)</dt>
                                                                            <dd class="leading-relaxed text-slate-700 dark:text-slate-300">{{ $control->control_objective ?? 'Belum ada data obyektif.' }}</dd>
                                                                        </div>
                                                                        <div class="sm:col-span-2">
                                                                            <dt class="font-bold tracking-widest text-[10px] text-slate-400 uppercase mb-1">Prosedur Pengujian CSA (Langkah Kerja)</dt>
                                                                            <dd class="leading-relaxed whitespace-pre-wrap text-slate-700 dark:text-slate-300">{{ $control->control_procedure ?? 'Belum ada data prosedur.' }}</dd>
                                                                        </div>

                                                                        <div>
                                                                            <dt class="font-bold tracking-widest text-[10px] text-slate-400 uppercase mb-1">Dokumen Pendukung / Bukti</dt>
                                                                            <dd class="whitespace-pre-wrap text-slate-700 dark:text-slate-300">{{ $control->supporting_documents ?? '-' }}</dd>
                                                                        </div>
                                                                        
                                                                        <div class="sm:col-span-2 mt-4">
                                                                            <dt class="font-bold tracking-widest text-[10px] text-slate-400 uppercase mb-2">Riwayat Hasil Evaluasi / CSA Keseluruhan</dt>
                                                                            
                                                                            @if($allReports->isEmpty())
                                                                                <dd class="text-slate-500 italic text-xs p-3 bg-slate-50 rounded-lg dark:bg-slate-800/50">Belum ada riwayat penilaian CSA.</dd>
                                                                            @else
                                                                                <div class="overflow-hidden border rounded-xl border-slate-200 dark:border-slate-700">
                                                                                    <table class="w-full text-left text-xs">
                                                                                        <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                                                                            <tr>
                                                                                                <th class="px-4 py-2.5 font-bold border-b border-slate-200 dark:border-slate-700">Periode</th>
                                                                                                <th class="px-4 py-2.5 font-bold border-b border-slate-200 dark:border-slate-700">Hasil CSA</th>
                                                                                                <th class="px-4 py-2.5 font-bold border-b border-slate-200 dark:border-slate-700">Keterangan</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 bg-white dark:bg-slate-900/20">
                                                                                            @foreach($allReports as $rep)
                                                                                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                                                                                    <td class="px-4 py-2.5 font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                                                                                        {{ $rep->triwulan }} / {{ $rep->tahun }}
                                                                                                    </td>
                                                                                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                                                                                        @if($rep->hasil_csa == 'efektif')
                                                                                                            <span class="text-emerald-700 bg-emerald-100 px-2 py-1 rounded">Efektif</span>
                                                                                                        @elseif($rep->hasil_csa == 'tidak_efektif')
                                                                                                            <span class="text-red-700 bg-red-100 px-2 py-1 rounded">Tidak Efektif</span>
                                                                                                        @elseif($rep->hasil_csa == 'sedang_berjalan')
                                                                                                            <span class="text-amber-700 bg-amber-100 px-2 py-1 rounded">Sedang berjalan</span>
                                                                                                        @else
                                                                                                            <span class="text-slate-500 bg-slate-100 px-2 py-1 rounded">Belum Dinilai</span>
                                                                                                        @endif
                                                                                                    </td>
                                                                                                    <td class="px-4 py-2.5 text-slate-600 dark:text-slate-400 italic">
                                                                                                        {{ $rep->keterangan_tidak_efektif ?: '-' }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </dl>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Kolom 3: Aksi Edit/Delete --}}
                                                    <div class="flex items-start gap-2 pt-4 border-t md:col-span-2 md:justify-end md:pt-0 md:border-t-0">
                                                        <button type="button" @click="openEdit(@js($editPayload))" title="Ubah kontrol" class="p-2 transition-all bg-white border shadow-sm rounded-xl border-slate-200 text-slate-500 hover:text-blue-600 hover:bg-blue-50">
                                                            {!! $ic($P['pencil'], 'w-4 h-4') !!}
                                                        </button>
                                                        <form action="{{ route('busdev.bpm-icofr.destroy-control', $control->id) }}" method="POST" onsubmit="return confirm('Hapus kontrol ini?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" title="Hapus kontrol" class="p-2 transition-all bg-white border shadow-sm rounded-xl border-slate-200 text-slate-500 hover:text-red-600 hover:bg-red-50">
                                                                {!! $ic($P['trash'], 'w-4 h-4') !!}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="flex flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 dark:bg-slate-800/50">
                                                        <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-slate-900 dark:text-white">Kosong</p>
                                                        <p class="text-sm font-medium text-slate-500">Belum ada kontrol RCM yang didaftarkan.</p>
                                                    </div>
                                                    <button type="button" @click="openAdd({{ $process->id }}, {{ $subArg }})" class="mt-2 text-sm font-bold text-[#F5333F] hover:underline">Tambah Kontrol Pertama</button>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @empty
                        <div class="py-20 text-center {{ $card }}">
                            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-slate-50 dark:bg-slate-800/50">
                                <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002 2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Belum ada proses bisnis</h3>
                            <p class="max-w-sm mx-auto mt-1 text-sm font-medium text-slate-500">Silakan kembali ke halaman utama Implementasi ICoFR untuk menambahkan proses baru pada siklus ini.</p>
                            <a href="{{ route('busdev.bpm-icofr.index') }}" class="inline-block mt-6 px-6 py-2.5 text-sm font-bold text-white transition-all bg-[#F5333F] rounded-xl shadow-lg shadow-red-500/30 hover:bg-red-600 hover:-translate-y-0.5">Kembali ke Beranda</a>
                        </div>
                    @endforelse

                    @if($controlTotal > 0)
                        <div x-show="filtering && shown === 0" style="display: none;" class="py-16 text-center bg-transparent border-2 border-dashed rounded-3xl border-slate-200 dark:border-slate-800">
                            <p class="text-base font-bold text-slate-500">Tidak ada kontrol yang cocok dengan filter atau pencarian Anda.</p>
                            <button type="button" @click="q = ''; filterType = 'semua'" class="mt-2 text-sm font-bold text-[#F5333F] hover:underline">Reset Pencarian</button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ==================================================================== --}}
            {{-- TAB 2: TABEL SPREADSHEET RCM (Persis Gambar 4 dengan Pagination)      --}}
            {{-- ==================================================================== --}}
            <div x-show="activeTab === 'spreadsheet'" x-transition.opacity.duration.200ms style="display: none;" class="space-y-4">
                
                {{-- Action Bar: Tambah, Sync, & Pencarian --}}
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 p-4 bg-white border border-slate-200/80 rounded-2xl shadow-sm dark:bg-[#161a23] dark:border-slate-800">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="openAdd({{ $cycle->processes->first()->id ?? 'null' }}, null)" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white transition bg-[#F5333F] rounded-xl shadow-sm hover:bg-red-600 shadow-red-500/20">
                            {!! $ic($P['plus'], 'w-4 h-4') !!}
                            Tambah Baris RCM
                        </button>
                        <button type="button" @click="showSyncSheetsModal = true" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white transition bg-emerald-600 rounded-xl shadow-sm hover:bg-emerald-700 shadow-emerald-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                            Sinkronkan Google Sheets
                        </button>
                    </div>

                    {{-- Filter & Search Box --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <select x-model="filterTableKey" @change="currentPage = 1" class="px-3 py-2 text-xs font-medium bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-[#F5333F]">
                            <option value="all">Semua Tipe Kontrol</option>
                            <option value="key">Key Control</option>
                            <option value="nonkey">Non-Key</option>
                        </select>

                        <select x-model="filterTableProcess" @change="currentPage = 1" class="px-3 py-2 text-xs font-medium bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-[#F5333F]">
                            <option value="all">Semua Proses Utama</option>
                            @foreach($cycle->processes as $p)
                                <option value="{{ $p->id }}">Proses {{ $p->code }}</option>
                            @endforeach
                        </select>

                        <div class="relative w-full sm:w-64">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                {!! $ic($P['search'], 'w-4 h-4') !!}
                            </span>
                            <input type="text" x-model="searchTable" @input="currentPage = 1" placeholder="Cari dalam spreadsheet..." 
                                class="w-full pl-9 pr-3.5 py-2 text-xs font-medium bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-[#F5333F] focus:border-[#F5333F]">
                        </div>
                    </div>
                </div>

                {{-- Tabel Data Responsif Modern --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden dark:bg-[#161a23] dark:border-slate-800">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-slate-700/80 text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                    <th class="px-3.5 py-3.5 text-center">No / Kode</th>
                                    <th class="px-3.5 py-3.5">Proses &amp; Sub</th>
                                    <th class="px-3.5 py-3.5">Ref. Risiko</th>
                                    <th class="px-3.5 py-3.5 min-w-[200px]">Deskripsi Risiko</th>
                                    <th class="px-3.5 py-3.5 text-center">Nilai Risiko</th>
                                    <th class="px-3.5 py-3.5 text-center">Kecurangan</th>
                                    <th class="px-3.5 py-3.5 min-w-[260px]">Deskripsi Kontrol</th>
                                    <th class="px-3.5 py-3.5 text-center">Key / Non Key</th>
                                    <th class="px-3.5 py-3.5">Frekuensi</th>
                                    <th class="px-3.5 py-3.5 min-w-[130px]">Nama Pelaksana</th>
                                    <th class="px-3.5 py-3.5 min-w-[130px]">Jabatan Pelaksana</th>
                                    <th class="px-3.5 py-3.5 min-w-[130px]">Unit Pelaksana</th>
                                    <th class="px-3.5 py-3.5 text-center">Hasil CSA</th>
                                    <th class="px-3.5 py-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <template x-for="(row, idx) in paginatedTableRows" :key="row.id">
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                        {{-- Kode Kontrol --}}
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-black rounded-lg text-emerald-700 bg-emerald-100/80 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-400" x-text="row.control_code"></span>
                                        </td>
                                        {{-- Proses --}}
                                        <td class="px-3.5 py-3 whitespace-nowrap">
                                            <span class="font-bold text-slate-800 dark:text-slate-200" x-text="'P' + row.process_code"></span>
                                            <span class="text-[10px] text-slate-400 block" x-text="row.sub_code ? ('Sub ' + row.sub_code) : '-'"></span>
                                        </td>
                                        {{-- Ref. Risiko --}}
                                        <td class="px-3.5 py-3 whitespace-nowrap text-center">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-black rounded-md text-red-700 bg-red-100 border border-red-200/60 dark:bg-red-950/40 dark:text-red-400" x-text="row.risk_code"></span>
                                        </td>
                                        {{-- Deskripsi Risiko --}}
                                        <td class="px-3.5 py-3">
                                            <p class="text-xs font-medium text-slate-700 dark:text-slate-300 line-clamp-2" :title="row.risk_description" x-text="row.risk_description"></p>
                                        </td>
                                        {{-- Nilai Risiko --}}
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                            <template x-if="row.risk_value && row.risk_value !== '-'">
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md border"
                                                    :class="{
                                                        'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-400': row.risk_value.toLowerCase().includes('tinggi') || row.risk_value.toLowerCase().includes('high'),
                                                        'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400': row.risk_value.toLowerCase().includes('sedang') || row.risk_value.toLowerCase().includes('medium'),
                                                        'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400': row.risk_value.toLowerCase().includes('rendah') || row.risk_value.toLowerCase().includes('low'),
                                                    }"
                                                    x-text="row.risk_value">
                                                </span>
                                            </template>
                                            <template x-if="!row.risk_value || row.risk_value === '-'">
                                                <span class="text-slate-400 text-xs">-</span>
                                            </template>
                                        </td>
                                        {{-- Risiko Kecurangan --}}
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 text-[10px] rounded-md border font-medium"
                                                :class="row.fraud_risk.toLowerCase().includes('ya') ? 'bg-rose-50 text-rose-700 border-rose-200 font-bold dark:bg-rose-950/40 dark:text-rose-400' : 'bg-slate-50 text-slate-500 border-slate-200 dark:bg-slate-800 dark:text-slate-400'"
                                                x-text="row.fraud_risk || 'Tidak'">
                                            </span>
                                        </td>
                                        {{-- Deskripsi Kontrol --}}
                                        <td class="px-3.5 py-3">
                                            <p class="text-xs font-medium text-slate-800 dark:text-slate-200 line-clamp-2" :title="row.control_description" x-text="row.control_description"></p>
                                        </td>
                                        {{-- Key / Non Key --}}
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                            <template x-if="row.is_key_control">
                                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-400 uppercase">
                                                    Key
                                                </span>
                                            </template>
                                            <template x-if="!row.is_key_control">
                                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 uppercase">
                                                    Non Key
                                                </span>
                                            </template>
                                        </td>
                                        {{-- Frekuensi --}}
                                        <td class="px-3.5 py-3 whitespace-nowrap text-slate-600 dark:text-slate-400 font-medium" x-text="row.frequency"></td>
                                        {{-- PIC --}}
                                        <td class="px-3.5 py-3 text-slate-900 dark:text-white font-bold" x-text="row.pic_name || '-'"></td>
                                        <td class="px-3.5 py-3 text-slate-700 dark:text-slate-300 font-medium" x-text="row.pic_position || '-'"></td>
                                        <td class="px-3.5 py-3 text-slate-600 dark:text-slate-400 font-medium whitespace-nowrap" x-text="row.pic_unit || '-'"></td>
                                        {{-- Hasil CSA --}}
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md"
                                                :class="{
                                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-400': row.csa_status === 'efektif',
                                                    'bg-red-100 text-red-800 dark:bg-red-950/50 dark:text-red-400': row.csa_status === 'tidak_efektif',
                                                    'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-400': row.csa_status === 'sedang_berjalan',
                                                    'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400': row.csa_status === 'belum_dinilai' || row.csa_status === 'tidak_ada'
                                                }"
                                                x-text="row.csa_status_label">
                                            </span>
                                        </td>
                                        {{-- Aksi --}}
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" @click="viewDetail(row)" title="Lihat Detail Prosedur CSA" 
                                                    class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition">
                                                    {!! $ic($P['eye'], 'w-3.5 h-3.5') !!}
                                                </button>
                                                <button type="button" @click="openEdit(row.raw_payload)" title="Ubah Kontrol" 
                                                    class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition">
                                                    {!! $ic($P['pencil'], 'w-3.5 h-3.5') !!}
                                                </button>
                                                <form :action="row.delete_url" method="POST" class="inline" onsubmit="return confirm('Hapus kontrol ini secara permanen?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" title="Hapus Kontrol" 
                                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                                                        {!! $ic($P['trash'], 'w-3.5 h-3.5') !!}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredTableRows.length === 0">
                                    <td colspan="12" class="px-6 py-12 text-center text-slate-400 italic">
                                        Tidak ada data yang sesuai dengan pencarian atau filter yang dipilih.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Baris Pagination (Footer) --}}
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                            <span>
                                Menampilkan <strong class="text-slate-900 dark:text-white" x-text="filteredTableRows.length > 0 ? ((currentPage - 1) * perPage + 1) : 0"></strong>
                                sampai <strong class="text-slate-900 dark:text-white" x-text="Math.min(currentPage * perPage, filteredTableRows.length)"></strong>
                                dari <strong class="text-slate-900 dark:text-white" x-text="filteredTableRows.length"></strong> baris
                            </span>
                            <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                            <div class="flex items-center gap-1.5">
                                <span>Per halaman:</span>
                                <select x-model.number="perPage" @change="currentPage = 1" class="px-2 py-1 text-xs font-semibold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-[#F5333F]">
                                    <option :value="10">10</option>
                                    <option :value="25">25</option>
                                    <option :value="50">50</option>
                                    <option :value="100">100</option>
                                </select>
                            </div>
                        </div>

                        {{-- Navigasi Tombol Halaman --}}
                        <div class="flex items-center gap-1" x-show="totalPages > 1">
                            <button type="button" @click="setPage(currentPage - 1)" :disabled="currentPage === 1"
                                class="px-2.5 py-1 text-xs font-bold rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                &laquo; Prev
                            </button>
                            <span class="px-3 py-1 text-xs font-bold text-slate-700 dark:text-slate-300">
                                <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
                            </span>
                            <button type="button" @click="setPage(currentPage + 1)" :disabled="currentPage === totalPages"
                                class="px-2.5 py-1 text-xs font-bold rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                Next &raquo;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL 1 : TAMBAH / UBAH KONTROL RCM                                  --}}
        {{-- ==================================================================== --}}
        <div x-show="showModal" class="relative z-50" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="ttl-control">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-4">
                <div x-show="showModal" @click.outside="showModal = false"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
                     class="w-full max-w-3xl max-h-[92vh] overflow-y-auto bg-white shadow-2xl rounded-t-3xl sm:rounded-3xl dark:bg-[#161a23]">

                    <div class="sticky top-0 z-10 flex items-start justify-between px-8 py-6 bg-white/90 backdrop-blur-md border-b border-slate-100 dark:bg-[#161a23]/90 dark:border-slate-800">
                        <div>
                            <h3 id="ttl-control" class="text-xl font-black text-slate-900 dark:text-white" x-text="mode === 'add' ? 'Tambah Matriks RCM' : 'Ubah Matriks RCM'"></h3>
                            <p class="mt-1 text-sm font-medium text-slate-500" x-text="mode === 'add' ? 'Masukkan detail risiko dan langkah kontrol mitigasi.' : 'Perbarui informasi detail risiko dan kontrol mitigasi.'"></p>
                        </div>
                        <button type="button" @click="showModal = false" aria-label="Tutup" class="p-2 transition bg-slate-100 rounded-full text-slate-500 hover:text-slate-900 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:hover:text-white">{!! $ic($P['x']) !!}</button>
                    </div>

                    <form :action="formUrl" method="POST" class="p-8 space-y-8">
                        @csrf
                        <template x-if="mode === 'edit'">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        {{-- 1. Pengelompokan & Penanggung Jawab --}}
                        <div class="p-6 border rounded-2xl bg-slate-50/50 border-slate-200/60 dark:bg-slate-900/30 dark:border-slate-800 space-y-5">
                            <h4 class="text-sm font-black tracking-widest text-slate-400 uppercase">1. Pengelompokan & Penanggung Jawab</h4>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="f-process" class="{{ $lbl }}">Proses Bisnis Utama</label>
                                    <select id="f-process" name="icofr_process_id" x-model="form.icofr_process_id" class="{{ $inp }}" required
                                            @change="$nextTick(() => { if (form.icofr_sub_process_id && subProcessOf[form.icofr_sub_process_id] != form.icofr_process_id) form.icofr_sub_process_id = '' })">
                                        <option value="">Pilih proses utama</option>
                                        @foreach($cycle->processes as $p)
                                            <option value="{{ $p->id }}">Proses {{ $p->code }} - {{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="f-unit" class="{{ $lbl }}">Unit PIC (opsional)</label>
                                    <select id="f-unit" name="unit_id" x-model="form.unit_id" class="{{ $inp }}">
                                        <option value="">Pilih departemen / seksi</option>
                                        @foreach($units as $u)
                                            <option value="{{ $u->id }}">{{ $u->name_unit }} ({{ $u->code_unit }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($subTotal > 0)
                                    <div class="sm:col-span-2">
                                        <label for="f-sub" class="{{ $lbl }}">Sub-Proses Bisnis</label>
                                        <select id="f-sub" name="icofr_sub_process_id" x-model="form.icofr_sub_process_id" class="{{ $inp }}">
                                            <option value="">Tanpa sub-proses (Lainnya)</option>
                                            @foreach($cycle->processes as $p)
                                                @if($p->subProcesses->isNotEmpty())
                                                    <optgroup label="Proses {{ $p->code }} - {{ $p->name }}" :hidden="form.icofr_process_id != {{ $p->id }}" :disabled="form.icofr_process_id != {{ $p->id }}">
                                                        @foreach($p->subProcesses->sortBy('code') as $sp)
                                                            <option value="{{ $sp->id }}">Sub {{ rtrim($p->code, '. ') }}.{{ $sp->code }} - {{ $sp->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- 2. Risiko & Mitigasi Kontrol --}}
                        <div class="grid gap-6 sm:grid-cols-2">
                            {{-- 2. IDENTIFIKASI RISIKO --}}
                            <div class="p-6 space-y-4 border rounded-2xl bg-red-50/50 border-red-100 dark:bg-red-500/5 dark:border-red-900/30">
                                <h4 class="text-sm font-black tracking-widest text-red-600 uppercase dark:text-red-500">2. Identifikasi Risiko</h4>
                                <div>
                                    <label for="f-rcode" class="{{ $lbl }}">Kode / Ref. Risiko</label>
                                    <input id="f-rcode" type="text" name="risk_code" x-model="form.risk_code" placeholder="Misal: R1 atau R2" class="{{ $inp }}" required>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="f-rval" class="{{ $lbl }}">Nilai Risiko</label>
                                        <select id="f-rval" name="risk_value" x-model="form.risk_value" class="{{ $inp }}">
                                            <option value="">Pilih Nilai</option>
                                            <option value="Tinggi">Tinggi</option>
                                            <option value="Sedang">Sedang</option>
                                            <option value="Rendah">Rendah</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="f-rfraud" class="{{ $lbl }}">Risiko Kecurangan</label>
                                        <select id="f-rfraud" name="fraud_risk" x-model="form.fraud_risk" class="{{ $inp }}">
                                            <option value="Tidak">Tidak</option>
                                            <option value="Ya">Ya</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label for="f-rdesc" class="{{ $lbl }}">Deskripsi Risiko</label>
                                    <textarea id="f-rdesc" name="risk_description" x-model="form.risk_description" rows="3" placeholder="Jelaskan apa yang bisa salah dalam proses ini..." class="{{ $inp }} resize-none" required></textarea>
                                </div>
                            </div>

                            {{-- 3. MITIGASI KONTROL --}}
                            <div class="p-6 space-y-4 border rounded-2xl bg-emerald-50/50 border-emerald-100 dark:bg-emerald-500/5 dark:border-emerald-900/30">
                                <h4 class="text-sm font-black tracking-widest text-emerald-600 uppercase dark:text-emerald-500">3. Mitigasi Kontrol</h4>
                                <div class="flex gap-4">
                                    <div class="w-1/3">
                                        <label for="f-ccode" class="{{ $lbl }}">Kode</label>
                                        <input id="f-ccode" type="text" name="control_code" x-model="form.control_code" placeholder="C1 atau C2" class="{{ $inp }}" required>
                                    </div>
                                    <div class="w-2/3">
                                        <label class="{{ $lbl }}">Tipe Kontrol</label>
                                        <div class="flex h-[42px] p-1 bg-white border border-emerald-200 rounded-xl dark:bg-slate-900 dark:border-emerald-800">
                                            <label class="relative flex items-center justify-center flex-1 cursor-pointer">
                                                <input type="radio" name="is_key_control" value="1" x-model="form.is_key_control" class="peer sr-only" required>
                                                <span class="absolute inset-0 transition-all rounded-lg peer-checked:bg-emerald-500 peer-checked:shadow-sm"></span>
                                                <span class="relative z-10 text-xs font-bold transition-colors text-slate-500 peer-checked:text-white">KEY</span>
                                            </label>
                                            <label class="relative flex items-center justify-center flex-1 cursor-pointer">
                                                <input type="radio" name="is_key_control" value="0" x-model="form.is_key_control" class="peer sr-only" required>
                                                <span class="absolute inset-0 transition-all rounded-lg peer-checked:bg-rose-500 peer-checked:shadow-sm"></span>
                                                <span class="relative z-10 text-xs font-bold transition-colors text-slate-500 peer-checked:text-white">NON-KEY</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="f-cdesc" class="{{ $lbl }}">Deskripsi Kontrol</label>
                                    <textarea id="f-cdesc" name="control_description" x-model="form.control_description" rows="5" placeholder="Langkah pengecekan atau SOP untuk mencegah risiko..." class="{{ $inp }} resize-none" required></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Detail Pengujian CSA --}}
                        <div class="p-6 space-y-4 border rounded-2xl bg-blue-50/50 border-blue-100 dark:bg-blue-500/5 dark:border-blue-900/30">
                            <h4 class="text-sm font-black tracking-widest text-blue-600 uppercase dark:text-blue-500">4. Detail Pengujian CSA (Opsional)</h4>
                            
                            <div>
                                <label class="{{ $lbl }}">Frekuensi Kontrol</label>
                                <select name="frequency" x-model="form.frequency" class="{{ $inp }}">
                                    <option value="">Pilih frekuensi</option>
                                    <option value="Harian">Harian</option>
                                    <option value="Mingguan">Mingguan</option>
                                    <option value="Bulanan">Bulanan</option>
                                    <option value="Triwulan">Triwulan</option>
                                    <option value="Semester">Semester</option>
                                    <option value="Tahunan">Tahunan</option>
                                    <option value="Ad Hoc / Sesuai Transaksi">Ad Hoc / Sesuai Transaksi</option>
                                </select>
                            </div>

                            <div>
                                <label class="{{ $lbl }}">Obyektif CSA</label>
                                <textarea name="control_objective" x-model="form.control_objective" rows="2" placeholder="Tujuan dari dilakukannya CSA ini..." class="{{ $inp }} resize-none"></textarea>
                            </div>
                            
                            <div>
                                <label class="{{ $lbl }}">Prosedur CSA</label>
                                <textarea name="control_procedure" x-model="form.control_procedure" rows="3" placeholder="Langkah-langkah pengujian yang harus dilakukan staf..." class="{{ $inp }} resize-none"></textarea>
                            </div>

                            <div>
                                <label class="{{ $lbl }}">Dokumen Pendukung</label>
                                <textarea name="supporting_documents" x-model="form.supporting_documents" rows="2" placeholder="Misal: Rekening Koran, Jurnal SAP, Faktur Pajak" class="{{ $inp }} resize-none"></textarea>
                            </div>
                        </div>

                        {{-- 5. Input / Update Hasil CSA Manual --}}
                        <div class="p-6 border rounded-2xl bg-emerald-50/50 border-emerald-100 dark:bg-emerald-900/10 dark:border-emerald-800/30">
                            <h4 class="text-sm font-black tracking-widest text-emerald-600 uppercase dark:text-emerald-500">5. Input / Update Hasil CSA Manual</h4>
                            
                            <div class="grid gap-4 mt-4 sm:grid-cols-2">
                                <div>
                                    <label class="{{ $lbl }}">Tahun</label>
                                    <input type="number" name="tahun" x-model="form.tahun" class="{{ $inp }}">
                                </div>
                                <div>
                                    <label class="{{ $lbl }}">Triwulan</label>
                                    <select name="triwulan" x-model="form.triwulan" class="{{ $inp }}">
                                        <option value="TW1">TW1</option>
                                        <option value="TW2">TW2</option>
                                        <option value="TW3">TW3</option>
                                        <option value="TW4">TW4</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="{{ $lbl }}">Hasil Evaluasi / CSA</label>
                                <select name="hasil_csa" x-model="form.hasil_csa" class="{{ $inp }}">
                                    @foreach(\App\Models\IcofrControl::HASIL_CSA as $val => $text)
                                        <option value="{{ $val }}">{{ $text }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-4" x-show="form.hasil_csa === 'tidak_efektif'">
                                <label class="{{ $lbl }}">Keterangan Tidak Efektif (Wajib jika defisiensi)</label>
                                <textarea name="keterangan_tidak_efektif" x-model="form.keterangan_tidak_efektif" rows="2" class="{{ $inp }}"></textarea>
                            </div>
                        </div>

                        {{-- TOMBOL BATAL & SIMPAN --}}
                        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-slate-50 border-slate-200 dark:bg-slate-800/50 dark:border-slate-700">
                            <button type="button" @click="showModal = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }}">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL 2 : TAMBAH / UBAH SUB PROSES                                   --}}
        {{-- ==================================================================== --}}
        <div x-show="showSub" class="relative z-50" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="ttl-sub">
            <div x-show="showSub" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-4">
                <div x-show="showSub" @click.outside="showSub = false"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
                    class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-t-3xl sm:rounded-3xl dark:bg-[#161a23]">

                    <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 id="ttl-sub" class="text-xl font-black text-slate-900 dark:text-white" x-text="subMode === 'add' ? 'Tambah Sub Proses' : 'Ubah Sub Proses'"></h3>
                            <p class="mt-1 text-sm font-medium text-slate-500">Buat sub-proses untuk merapikan hierarki kontrol.</p>
                        </div>
                        <button type="button" @click="showSub = false" aria-label="Tutup" class="p-2 transition bg-slate-100 rounded-full text-slate-500 hover:text-slate-900 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:hover:text-white">{!! $ic($P['x']) !!}</button>
                    </div>

                    <form :action="subUrl" method="POST" class="p-8 space-y-8">
                        @csrf
                        <template x-if="subMode === 'edit'">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div>
                            <label for="sp-process" class="{{ $lbl }}">Berada di Bawah Proses Utama:</label>
                            <select id="sp-process" name="icofr_process_id" x-model="subForm.icofr_process_id" :disabled="subMode === 'edit'" class="{{ $inp }} bg-slate-100 cursor-not-allowed" required>
                                @foreach($cycle->processes as $p)
                                    <option value="{{ $p->id }}">Proses {{ $p->code }} - {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-3">
                            <div>
                                <label for="sp-code" class="{{ $lbl }}">Kode</label>
                                <input id="sp-code" type="text" name="code" x-model="subForm.code" maxlength="10" placeholder="01.A" class="{{ $inp }}" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="sp-name" class="{{ $lbl }}">Nama Sub Proses</label>
                                <input id="sp-name" type="text" name="name" x-model="subForm.name" placeholder="Contoh: Pemotongan PPh 21" class="{{ $inp }}" required>
                            </div>
                        </div>

                        <div class="flex flex-col-reverse gap-3 pt-6 border-t sm:flex-row sm:justify-end border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showSub = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }}" x-text="subMode === 'add' ? 'Simpan Sub Proses' : 'Perbarui Sub Proses'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL 3 : UBAH PROSES UTAMA                                          --}}
        {{-- ==================================================================== --}}
        <div x-show="showProcess" class="relative z-50" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="ttl-process">
            <div x-show="showProcess" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-4">
                <div x-show="showProcess" @click.outside="showProcess = false"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
                     class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-t-3xl sm:rounded-3xl dark:bg-[#161a23]">

                    <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 id="ttl-process" class="text-xl font-black text-slate-900 dark:text-white">Ubah Proses Utama</h3>
                            <p class="mt-1 text-sm font-medium text-slate-500">Perbaiki penamaan atau kode proses bisnis.</p>
                        </div>
                        <button type="button" @click="showProcess = false" aria-label="Tutup" class="p-2 transition bg-slate-100 rounded-full text-slate-500 hover:text-slate-900 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:hover:text-white">{!! $ic($P['x']) !!}</button>
                    </div>

                    <form x-bind:action="processUrl" method="POST" class="p-6 space-y-6">
                        @csrf @method('PUT')
                        <div class="grid gap-5 sm:grid-cols-3">
                            <div>
                                <label class="{{ $lbl }}">Kode</label>
                                <input type="text" name="code" x-model="processForm.code" class="{{ $inp }}" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="{{ $lbl }}">Nama Proses</label>
                                <input type="text" name="name" x-model="processForm.name" class="{{ $inp }}" required>
                            </div>
                        </div>
                        <div class="flex flex-col-reverse gap-3 pt-6 border-t sm:flex-row sm:justify-end border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showProcess = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }}">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL 4 : DETAIL PROSEDUR CSA & KONTROL (POPUP DARI TABEL SPREADSHEET)--}}
        {{-- ==================================================================== --}}
        <div x-show="showDetailModal" class="relative z-50" style="display: none;" role="dialog" aria-modal="true">
            <div x-show="showDetailModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div x-show="showDetailModal" @click.outside="showDetailModal = false"
                     class="w-full max-w-2xl bg-white shadow-2xl rounded-3xl dark:bg-[#161a23] border border-slate-200 dark:border-slate-800 overflow-hidden max-h-[90vh] flex flex-col">
                    
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 text-xs font-black rounded-lg text-emerald-700 bg-emerald-100 border border-emerald-200/60" x-text="selectedControl?.control_code"></span>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white" x-text="'Detail Kontrol & CSA - ' + (selectedControl?.control_code || '')"></h3>
                                <p class="text-xs text-slate-500" x-text="'Proses: ' + (selectedControl?.process_name || '-') + (selectedControl?.sub_name ? (' / ' + selectedControl.sub_name) : '')"></p>
                            </div>
                        </div>
                        <button type="button" @click="showDetailModal = false" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-full">
                            {!! $ic($P['x']) !!}
                        </button>
                    </div>

                    <div class="p-6 space-y-5 overflow-y-auto flex-1 text-xs">
                        <div class="p-4 rounded-2xl bg-red-50/60 border border-red-100 dark:bg-red-950/20 dark:border-red-900/30 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 text-[10px] font-black rounded bg-red-100 text-red-700" x-text="selectedControl?.risk_code"></span>
                                <div class="flex items-center gap-1.5">
                                    <template x-if="selectedControl?.risk_value">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-white text-slate-700 border" x-text="'Nilai: ' + selectedControl.risk_value"></span>
                                    </template>
                                    <template x-if="selectedControl?.fraud_risk">
                                        <span class="px-2 py-0.5 text-[10px] font-medium rounded bg-white text-slate-600 border" x-text="'Kecurangan: ' + selectedControl.fraud_risk"></span>
                                    </template>
                                </div>
                            </div>
                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200" x-text="selectedControl?.risk_description"></p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">Deskripsi Langkah Kontrol</p>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium" x-text="selectedControl?.control_description"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <p class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">Unit / PIC Pelaksana</p>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="selectedControl?.pic"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">Frekuensi</p>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="selectedControl?.frequency"></p>
                            </div>
                        </div>

                        <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <p class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">Obyektif Penilaian (Tujuan CSA)</p>
                                <p class="text-xs text-slate-700 dark:text-slate-300 mt-0.5 leading-relaxed" x-text="selectedControl?.objective || 'Belum ada data obyektif.'"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">Prosedur Pengujian CSA (Langkah Kerja)</p>
                                <p class="text-xs text-slate-700 dark:text-slate-300 mt-0.5 leading-relaxed whitespace-pre-wrap" x-text="selectedControl?.procedure || 'Belum ada data prosedur.'"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">Dokumen Pendukung / Bukti</p>
                                <p class="text-xs text-slate-700 dark:text-slate-300 mt-0.5 leading-relaxed whitespace-pre-wrap" x-text="selectedControl?.supporting_documents || '-'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex justify-end">
                        <button type="button" @click="showDetailModal = false" class="{{ $btnGhost }} !py-2 !text-xs">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL 5 : SINKRONISASI DARI SPREADSHEET                              --}}
        {{-- ==================================================================== --}}
        <div x-show="showSyncSheetsModal" class="relative z-50" style="display: none;" role="dialog" aria-modal="true">
            <div x-show="showSyncSheetsModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto sm:items-center sm:p-4">
                <div x-show="showSyncSheetsModal" @click.outside="showSyncSheetsModal = false" class="w-full max-w-xl overflow-hidden bg-white shadow-2xl rounded-t-3xl sm:rounded-3xl dark:bg-slate-900 border border-slate-100 dark:border-slate-800">
                    <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                                </span>
                                Sinkronisasi Dua Arah Google Sheets
                            </h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Sinkronkan kontrol dan penilaian CSA antara Web dan Spreadsheet tanpa unduh manual.</p>
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
                            <p>Sistem akan memperbarui nama kontrol, parameter risiko baru (Ref. Risk, Nilai Risiko, Fraud Risk, Key/Non Key), dan hasil penilaian CSA periode terpilih langsung ke database.</p>
                        </div>

                        <div class="flex flex-col-reverse gap-3 pt-4 border-t sm:flex-row sm:justify-end border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showSyncSheetsModal = false" class="{{ $btnGhost }}">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }}">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Tarik &amp; Sinkronkan Data
                            </button>
                        </div>
                    </form>

                    {{-- TAB 2: KIRIM DATA (PUSH)  --}}
                    <form x-show="syncMode === 'push'" action="{{ route('busdev.bpm-icofr.push-sheets') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="cycle_id" value="{{ $cycle->id }}">

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

                        {{-- Collapsible Panduan Apps Script  --}}
                        <div x-show="showScriptGuide" x-transition.opacity class="p-4 space-y-3 text-xs border rounded-2xl bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700">
                            <div class="flex items-center justify-between font-bold text-slate-800 dark:text-white">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Langkah Setup Apps Script (1 Menit)
                                </span>
                                <button type="button" 
                                    @click="navigator.clipboard.writeText(document.getElementById('gas-script-box-show').value); copiedScript = true; setTimeout(() => copiedScript = false, 2500);" 
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
                                <li>Klik <strong>Deploy</strong> lalu salin Web App URL yang muncul ke input di atas. Selesai!</li>
                            </ol>
                            <textarea id="gas-script-box-show" readonly class="w-full h-36 p-2 font-mono text-[10px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 focus:outline-none">
/**
 * ==============================================================================
 * GOOGLE APPS SCRIPT: INTEGRASI KONTROL ICOFR & MASTER PIC PELAKSANA KONTROL
 * PT SEMEN TONASA (VERSI TERPADU: DROPDOWN BERJENJANG UNIT & JABATAN OTOMATIS)
 * ==============================================================================
 */

function doPost(e) {
  try {
    var contents = JSON.parse(e.postData.contents);
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var action = contents.action || 'sync_controls';
    var data = contents.data;
    
    if (!data || !data.length) {
      return ContentService.createTextOutput(JSON.stringify({
        status: 'error', 
        message: 'Data kosong. Tidak ada data yang dikirim.'
      })).setMimeType(ContentService.MimeType.JSON);
    }

    // =========================================================================
    // AKSI 1: SINKRONISASI SHEET "Master_PIC" (DARI TOMBOL PUSH MASTER PIC DI WEB)
    // =========================================================================
    if (action === 'sync_pics') {
      var picSheet = ss.getSheetByName("Master_PIC");
      if (!picSheet) {
        picSheet = ss.insertSheet("Master_PIC");
      }
      
      picSheet.clear();
      
      // 1. Tulis Header Resmi
      var headers = [["No", "Nama Pelaksana Kontrol", "Jabatan Pelaksana Kontrol", "Unit Pelaksana Kontrol", "Departemen"]];
      picSheet.getRange(1, 1, 1, 5).setValues(headers)
        .setFontWeight("bold")
        .setBackground("#003366")
        .setFontColor("#FFFFFF")
        .setHorizontalAlignment("center");
      
      picSheet.setFrozenRows(1);
      
      // 2. Tulis Seluruh Data PIC
      var picRows = [];
      for (var p = 0; p < data.length; p++) {
        var it = data[p];
        picRows.push([
          it.no || (p + 1),
          it.name || "",
          it.position || "",
          it.unit || "",
          it.department || ""
        ]);
      }
      
      if (picRows.length > 0) {
        var dataRange = picSheet.getRange(2, 1, picRows.length, 5);
        dataRange.setValues(picRows);
        dataRange.setBorder(true, true, true, true, true, true, "#D1D5DB", SpreadsheetApp.BorderStyle.SOLID);
      }
      
      // 3. Rapikan Lebar Kolom
      for (var col = 1; col <= 5; col++) {
        picSheet.autoResizeColumn(col);
      }
      
      // 4. OTOMATIS PASANG DROPDOWN UNIT & JABATAN PADA SELURUH SHEET KONTROL
      try {
        pasangSemuaDropdown(ss);
      } catch (ddErr) {
        Logger.log("Info dropdown: " + ddErr);
      }
      
      return ContentService.createTextOutput(JSON.stringify({
        status: 'success',
        message: 'Berhasil membuat Sheet Master_PIC dengan ' + picRows.length + ' PIC, dan seluruh baris Kolom Unit & Jabatan telah dipasangi Dropdown otomatis!'
      })).setMimeType(ContentService.MimeType.JSON);
    }

    // =========================================================================
    // AKSI 2: SINKRONISASI KONTROL & HASIL CSA KE SHEET AKTIF
    // =========================================================================
    var sheet = ss.getActiveSheet();
    var lastRow = Math.max(sheet.getLastRow(), 1);
    var lastCol = Math.max(sheet.getLastColumn(), 26);
    
    // 1. Deteksi Baris Header Inti
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
            return c + 1; // 1-indexed
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
    
    // 3 Kolom PIC Pelaksana Kontrol
    colMap['nama_pelaksana']           = findColumn(['nama pelaksana', 'nama pic']);
    colMap['jabatan_pelaksana']        = findColumn(['jabatan pelaksana', 'jabatan pic']);
    colMap['unit_pelaksana']           = findColumn(['unit pelaksana']);
    
    // Kompatibilitas mundur
    colMap['nama_pic']                 = colMap['nama_pelaksana'] || findColumn(['nama pelaksana', 'nama pic']);
    colMap['pelaksana_kontrol']        = colMap['unit_pelaksana'] || findColumn(['pelaksana kontrol', 'unit']);
    
    colMap['ref_risk']                 = findColumn(['ref. risiko', 'ref risiko', 'ref. risk', 'ref risk', 'kode risiko']);
    colMap['deskripsi_risiko']         = findColumn(['deskripsi risiko', 'uraian risiko', 'deskripsi risk']);
    colMap['nilai_risiko']             = findColumn(['nilai risiko', 'risk value', 'tingkat risiko']);
    colMap['risiko_kecurangan']        = findColumn(['risiko kecurangan', 'kecurangan', 'fraud']);
    
    colMap['ref_control']              = findColumn(['(4)', 'ref. control', 'no. ref. control', 'ref control']);
    
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
    
    var ctrlCol = colMap['ref_control'] || 10;
    
    // 4. Bangun indeks baris berdasarkan Kode Kontrol
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
    
    var sampleFormatRow = lastRow > headerRowIndex ? Math.max(headerRowIndex + 1, lastRow) : headerRowIndex + 1;
    
    function safeSetValue(cell, val) {
      if (val === undefined || val === null || val === '') return;
      var finalVal = val;
      var rule = null;
      try { rule = cell.getDataValidation(); } catch (e) {}
      
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
        
        if (sampleFormatRow <= sheet.getLastRow() && sampleFormatRow > headerRowIndex) {
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
        if (colIndex && item[key] !== undefined && item[key] !== null && item[key] !== '') {
          var cell = sheet.getRange(targetRow, colIndex);
          safeSetValue(cell, item[key]);
        }
      }
      
      // Pasang validasi dropdown Key/Non Key & CSA jika belum ada
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
    
    // 6. Urutkan berdasarkan Siklus, Proses, Sub Proses, Ref Control (A-Z)
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

    // 7. Otomatis pasang/perbarui Dropdown Unit & Jabatan
    try {
      pasangSemuaDropdown(ss);
    } catch (ddErr2) {
      Logger.log("Info dropdown: " + ddErr2);
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
}

/**
 * ==============================================================================
 * MENU TAMBAHAN GOOGLE SHEETS (DITAMPILKAN SAAT SPREADSHEET DIBUKA)
 * ==============================================================================
 */
function onOpen() {
  var ui = SpreadsheetApp.getUi();
  ui.createMenu("⚡ ICoFR Semen Tonasa")
    .addItem("🔄 Pasang Dropdown Unit & Jabatan Sekarang", "pasangSemuaDropdown")
    .addToUi();
}

/**
 * ==============================================================================
 * FUNGSI UTAMA: MEMASANG DROPDOWN UNIT & JABATAN PADA SELURUH BARIS KONTROL
 * ==============================================================================
 * Fungsi ini dapat dijalankan langsung dengan menekan tombol Run (Jalankan) di Apps Script,
 * atau melalui menu "⚡ ICoFR Semen Tonasa" > "Pasang Dropdown Unit & Jabatan Sekarang".
 */
function pasangSemuaDropdown(ss) {
  if (!ss) ss = SpreadsheetApp.getActiveSpreadsheet();
  var masterSheet = ss.getSheetByName("Master_PIC");
  if (!masterSheet) {
    Logger.log("Sheet Master_PIC belum ada.");
    return;
  }
  
  var masterData = masterSheet.getDataRange().getValues(); // [No, Nama, Jabatan, Unit, Dept]
  if (masterData.length <= 1) return;
  
  var uniqueUnits = [];
  var allPositions = [];
  var unitToPositions = {};
  
  for (var i = 1; i < masterData.length; i++) {
    var name = String(masterData[i][1] || "").trim();
    var pos  = String(masterData[i][2] || "").trim();
    var unit = String(masterData[i][3] || "").trim();
    
    if (unit && uniqueUnits.indexOf(unit) === -1) {
      uniqueUnits.push(unit);
    }
    if (pos && allPositions.indexOf(pos) === -1) {
      allPositions.push(pos);
    }
    if (unit && pos) {
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
  for (var s = 0; s < sheets.length; s++) {
    var sh = sheets[s];
    var sName = sh.getName();
    if (sName === "Master_PIC" || sName === "Dropdownlist") continue;
    
    var lastRow = Math.max(sh.getLastRow(), 20);
    if (lastRow < 3) continue;
    
    var cols = getPicColumns(sh);
    var numRows = lastRow - 2;
    
    // 1. PASANG DROPDOWN UNIT PADA SEMUA BARIS (KOLOM F)
    sh.getRange(3, cols.colUnit, numRows, 1).setDataValidation(unitRule);
    
    // 2. PASANG DROPDOWN JABATAN PADA KOLOM E (SESUAIKAN DENGAN UNIT JIKA SUDAH ADA)
    var currentUnitValues = sh.getRange(3, cols.colUnit, numRows, 1).getValues();
    for (var r = 0; r < numRows; r++) {
      var rowNum = 3 + r;
      var curUnit = String(currentUnitValues[r][0] || "").trim();
      var cellJabatan = sh.getRange(rowNum, cols.colJabatan);
      
      if (curUnit && unitToPositions[curUnit] && unitToPositions[curUnit].length > 0) {
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
  
  Logger.log("Berhasil memasang dropdown Unit dan Jabatan!");
}

/**
 * Helper mendeteksi nomor kolom PIC Pelaksana Kontrol (Unit, Jabatan, Nama)
 */
function getPicColumns(sheet) {
  var lastCol = Math.max(sheet.getLastColumn(), 26);
  var scanRows = Math.min(sheet.getLastRow(), 3);
  var headerGrid = sheet.getRange(1, 1, scanRows, lastCol).getValues();
  var cols = { colUnit: 6, colJabatan: 5, colNama: 4 }; // Default F, E, D
  
  for (var c = 0; c < lastCol; c++) {
    var txt = "";
    for (var r = 0; r < headerGrid.length; r++) {
      if (headerGrid[r][c] !== undefined && headerGrid[r][c] !== null) {
        txt += " " + headerGrid[r][c].toString();
      }
    }
    txt = txt.toLowerCase().trim();
    if (txt.indexOf("unit pelaksana") !== -1) {
      cols.colUnit = c + 1;
    } else if (txt.indexOf("jabatan pelaksana") !== -1) {
      cols.colJabatan = c + 1;
    } else if (txt.indexOf("nama pelaksana") !== -1 || txt.indexOf("nama pic") !== -1) {
      cols.colNama = c + 1;
    }
  }
  return cols;
}

/**
 * ==============================================================================
 * SISTEM DROPDOWN BERJENJANG OTOMATIS SAAT USER MENGEDIT SPREADSHEET (onEdit)
 * ==============================================================================
 * Memilih Unit di Kolom F -> Filter Jabatan di Kolom E -> Auto-fill Nama di Kolom D
 * Memilih Jabatan di Kolom E -> Auto-fill Unit di Kolom F & Nama di Kolom D
 */
function onEdit(e) {
  if (!e) return;
  var range = e.range;
  var sheet = range.getSheet();
  var sheetName = sheet.getName();
  
  if (sheetName === "Master_PIC" || sheetName === "Dropdownlist") return;
  
  var row = range.getRow();
  var col = range.getColumn();
  if (row <= 2) return; // Abaikan baris header
  
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var masterSheet = ss.getSheetByName("Master_PIC");
  if (!masterSheet) return;
  
  var masterData = masterSheet.getDataRange().getValues(); // [No, Nama, Jabatan, Unit, Dept]
  var cols = getPicColumns(sheet);
  
  // 1. KETIKA USER MEMILIH "UNIT PELAKSANA" (KOLOM F)
  if (col === cols.colUnit) {
    var selectedUnit = String(e.value || "").trim();
    var cellJabatan = sheet.getRange(row, cols.colJabatan);
    var cellNama = sheet.getRange(row, cols.colNama);
    
    if (!selectedUnit) {
      // Jika dikosongkan, beri seluruh jabatan sebagai pilihan
      var allPositions = [];
      for (var a = 1; a < masterData.length; a++) {
        var aPos = String(masterData[a][2] || "").trim();
        if (aPos && allPositions.indexOf(aPos) === -1) allPositions.push(aPos);
      }
      allPositions.sort();
      var defaultRule = SpreadsheetApp.newDataValidation()
        .requireValueInList(allPositions, true)
        .setAllowInvalid(true)
        .build();
      cellJabatan.setDataValidation(defaultRule).setValue("");
      cellNama.setValue("");
      return;
    }
    
    // Cari seluruh jabatan yang berada di bawah Unit yang dipilih
    var matchingPositions = [];
    for (var i = 1; i < masterData.length; i++) {
      var pUnit = String(masterData[i][3] || "").trim();
      var pPos  = String(masterData[i][2] || "").trim();
      if (pUnit.toLowerCase() === selectedUnit.toLowerCase() && pPos !== "") {
        if (matchingPositions.indexOf(pPos) === -1) {
          matchingPositions.push(pPos);
        }
      }
    }
    
    // Pasang daftar validasi dropdown Jabatan
    if (matchingPositions.length > 0) {
      var rule = SpreadsheetApp.newDataValidation()
        .requireValueInList(matchingPositions, true)
        .setAllowInvalid(true)
        .build();
      cellJabatan.setDataValidation(rule);
      
      // Jika hanya ada 1 jabatan (misal: Unit of Warehouse -> SM of Warehouse), langsung pilihkan otomatis
      if (matchingPositions.length === 1) {
        cellJabatan.setValue(matchingPositions[0]);
        for (var j = 1; j < masterData.length; j++) {
          if (String(masterData[j][2]).trim().toLowerCase() === matchingPositions[0].toLowerCase()) {
            cellNama.setValue(masterData[j][1] || "");
            break;
          }
        }
      } else {
        // Cek apakah jabatan yang sedang terisi masih valid untuk unit baru ini
        var curJab = String(cellJabatan.getValue() || "").trim();
        var isValid = matchingPositions.some(function(mp) {
          return mp.toLowerCase() === curJab.toLowerCase();
        });
        if (!isValid) {
          cellJabatan.setValue("");
          cellNama.setValue("");
        }
      }
    } else {
      cellJabatan.clearDataValidations();
    }
  }
  
  // 2. KETIKA USER MEMILIH "JABATAN PELAKSANA" (KOLOM E)
  if (col === cols.colJabatan) {
    var selectedJabatan = String(e.value || "").trim();
    var cellUnit = sheet.getRange(row, cols.colUnit);
    var currentUnit = String(cellUnit.getValue() || "").trim();
    var cellNama = sheet.getRange(row, cols.colNama);
    
    if (selectedJabatan) {
      for (var k = 1; k < masterData.length; k++) {
        var pName = String(masterData[k][1] || "").trim();
        var pPos  = String(masterData[k][2] || "").trim();
        var pUnit = String(masterData[k][3] || "").trim();
        
        if (pPos.toLowerCase() === selectedJabatan.toLowerCase()) {
          // Jika unit kosong, otomatis isi Unit-nya juga
          if (!currentUnit && pUnit) {
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

</textarea>
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
                                Sinkronisasi Siklus Terpilih
                            </span>
                            <p>Data kontrol dari siklus <strong>{{ $cycle->code }} - {{ $cycle->name }}</strong> (termasuk kontrol baru yang dibuat manual di web) akan otomatis diperbarui dan ditambahkan ke baris Spreadsheet tanpa merusak formula lain.</p>
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

    @include('busdev.bpm-icofr.partials.pic-modal')
</div>
</x-app-layout>