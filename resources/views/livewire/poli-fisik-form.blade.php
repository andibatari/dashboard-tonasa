<div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center shadow-sm">
            <i class="fas fa-check-circle text-lg mr-2 text-emerald-500"></i>
            <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center shadow-sm">
            <i class="fas fa-exclamation-circle text-lg mr-2 text-red-500"></i>
            <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif
    
    <form wire:submit.prevent="simpanHasil" class="space-y-4 sm:space-y-6">
        
        {{-- DOKTER PEMERIKSA --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide flex items-center"><i class="fas fa-user-md text-blue-500 mr-2"></i>Dokter Pemeriksa</h3>
            </div>
            <div class="p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                    <label class="text-xs sm:text-sm font-semibold text-slate-600 sm:w-1/4">Pilih Dokter</label>
                    <select wire:model.defer="dokterId" class="block w-full sm:w-2/3 rounded-md border border-slate-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm cursor-pointer">
                        <option value="">-- Pilih Dokter Pemeriksa Fisik --</option>
                        @isset($listDokter)
                            @foreach ($listDokter as $dokter)
                                <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap ?? $dokter->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                @error('dokterId') <span class="text-red-500 text-[10px] sm:text-xs mt-1 block font-medium sm:ml-[25%]">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- 1. ANAMNESA --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">1. Anamnesa</h3>
            </div>
            <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                
                {{-- Kiri: Keluhan & Riwayat Medis --}}
                <div class="space-y-3 sm:space-y-4 border-b border-slate-100 pb-4 md:border-b-0 md:pb-0 md:border-r md:pr-6">
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-slate-600 mb-1.5">Keluhan Utama</label>
                        <textarea wire:model.defer="dataFisik.anamnesa.keluhan_utama" rows="2" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm resize-none" placeholder="Contoh: Batuk 2 minggu..."></textarea>
                    </div>

                    <div x-data="{ rk: '' }">
                        <label class="block text-xs sm:text-sm font-semibold text-slate-600 mb-1.5">Riwayat Kesehatan</label>
                        <select wire:model.defer="dataFisik.anamnesa.riwayat_kesehatan" x-init="rk = $el.value" x-on:change="rk = $event.target.value" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm cursor-pointer">
                            <option value="Tidak ada">Tidak ada</option><option value="Astma">Astma</option><option value="Tuberculosis">Tuberculosis</option><option value="Penyakit Jantung dan Pembuluh Darah">Penyakit Jantung & Pembuluh Darah</option><option value="Hipertensi">Hipertensi</option><option value="Demam Rematik">Demam Rematik</option><option value="Hemoroid/Ambeyen">Hemoroid/Ambeyen</option><option value="Diabetes Melitus">Diabetes Melitus</option><option value="Kejang">Kejang</option><option value="Gangguan Jiwa">Gangguan Jiwa</option><option value="Trauma Pada Kepala">Trauma Pada Kepala</option><option value="Tukak Lambung">Tukak Lambung</option><option value="Hepatitis">Hepatitis</option><option value="Batu Ginjal/ Batu Saluran Kemih">Batu Ginjal / Saluran Kemih</option><option value="Obat – Obatan / Drugs">Obat – Obatan / Drugs</option><option value="Alergi Debu, Makanan, Obat dll">Alergi Debu, Makanan, Obat dll</option><option value="Dalam Perawatan rumah Sakit">Dalam Perawatan Rumah Sakit</option><option value="Lainnya">Lainnya (Sebutkan)</option>
                        </select>
                        <input x-show="rk === 'Lainnya'" x-transition type="text" wire:model.defer="dataFisik.anamnesa.riwayat_kesehatan_lainnya" class="mt-2 block w-full rounded-md border border-blue-300 bg-blue-50/50 px-3 py-1.5 text-xs sm:text-sm placeholder-blue-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Sebutkan lainnya...">
                    </div>

                    <div x-data="{ rpk: '' }">
                        <label class="block text-xs sm:text-sm font-semibold text-slate-600 mb-1.5">Riwayat Peny. Keluarga</label>
                        <select wire:model.defer="dataFisik.anamnesa.riwayat_penyakit_keluarga" x-init="rpk = $el.value" x-on:change="rpk = $event.target.value" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm cursor-pointer">
                            <option value="Tidak ada">Tidak ada</option><option value="Astma">Astma</option><option value="Tuberculosis">Tuberculosis</option><option value="Penyakit Jantung dan Pembuluh Darah">Penyakit Jantung & Pembuluh Darah</option><option value="Hipertensi">Hipertensi</option><option value="Diabetes Melitus">Diabetes Melitus</option><option value="Lainnya">Lainnya (Sebutkan)</option>
                        </select>
                        <input x-show="rpk === 'Lainnya'" x-transition type="text" wire:model.defer="dataFisik.anamnesa.riwayat_penyakit_keluarga_lainnya" class="mt-2 block w-full rounded-md border border-blue-300 bg-blue-50/50 px-3 py-1.5 text-xs sm:text-sm placeholder-blue-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Sebutkan lainnya...">
                    </div>
                </div>

                {{-- Kanan: Riwayat Sosial --}}
                <div class="space-y-3 sm:space-y-4">
                    <h4 class="font-bold text-xs sm:text-sm text-slate-700 flex items-center">
                        <i class="fas fa-users text-blue-500 mr-2"></i> Riwayat Sosial
                    </h4>
                    
                    <div x-data="{ merokok: '{{ $dataFisik['anamnesa']['merokok'] ?? 'Tidak' }}' }" class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                        <div class="flex items-center justify-between gap-2 w-full sm:w-1/2">
                            <label class="text-xs sm:text-sm font-semibold text-slate-600">Merokok</label>
                            <select x-model="merokok" wire:model.defer="dataFisik.anamnesa.merokok" class="w-1/2 sm:w-2/3 rounded-md border border-slate-300 bg-white px-2 py-1.5 text-xs sm:text-sm text-slate-900 focus:border-blue-500 focus:ring-1 shadow-sm cursor-pointer">
                                <option value="Tidak">Tidak</option><option value="Ya">Ya</option>
                            </select>
                        </div>
                        <div x-show="merokok === 'Ya'" x-transition class="flex items-center gap-2 w-full sm:w-1/2">
                            <label class="text-[11px] sm:text-xs font-semibold text-slate-500">Jumlah</label>
                            <div class="flex flex-1 rounded-md overflow-hidden border border-slate-300 focus-within:border-blue-500 focus-within:ring-1">
                                <input type="number" wire:model.defer="dataFisik.anamnesa.merokok_jumlah" class="w-full border-0 px-2 py-1.5 text-xs sm:text-sm text-center focus:ring-0">
                                <span class="bg-slate-100 px-2 flex items-center justify-center text-[10px] sm:text-xs text-slate-500 font-bold border-l border-slate-300 whitespace-nowrap">btg/hr</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-xs sm:text-sm font-semibold text-slate-600 flex-1">Minum Alkohol</label>
                        <select wire:model.defer="dataFisik.anamnesa.minum_alkohol" class="w-1/2 rounded-md border border-slate-300 bg-white px-2 py-1.5 text-xs sm:text-sm text-slate-900 focus:border-blue-500 focus:ring-1 shadow-sm cursor-pointer">
                            <option value="Tidak">Tidak</option><option value="Ya">Ya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-slate-600 mb-1.5">Olahraga <span class="font-normal text-slate-400 text-[10px] sm:text-xs">(Sebutkan jika ada)</span></label>
                        <input type="text" wire:model.defer="dataFisik.anamnesa.olahraga" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm placeholder-slate-400 focus:border-blue-500 focus:ring-1 shadow-sm" placeholder="Contoh: Sepak bola, lari...">
                    </div>
                </div>

            </div>
        </div>

        {{-- 2. TANDA VITAL & ANTROPOMETRI --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">2. Tanda Vital & Antropometri</h3>
            </div>
            <div class="p-4 sm:p-5">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase mb-1">TB <span class="lowercase">(cm)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.tinggi_badan" min="10" class="block w-full rounded-md border border-slate-300 px-2 py-1.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-1 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase mb-1">BB <span class="lowercase">(kg)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.berat_badan" min="5" class="block w-full rounded-md border border-slate-300 px-2 py-1.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-1 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase mb-1">Sistol <span class="lowercase">(mmHg)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.tekanan_darah_sistol" class="block w-full rounded-md border border-slate-300 px-2 py-1.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-1 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase mb-1">Diastol <span class="lowercase">(mmHg)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.tekanan_darah_diastol" class="block w-full rounded-md border border-slate-300 px-2 py-1.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-1 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase mb-1">Nadi <span class="lowercase">(x/mnt)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.nadi" class="block w-full rounded-md border border-slate-300 px-2 py-1.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-1 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase mb-1">Nafas <span class="lowercase">(x/mnt)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.pernafasan" class="block w-full rounded-md border border-slate-300 px-2 py-1.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-1 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase mb-1">Suhu <span class="lowercase">(°C)</span></label>
                        <input type="number" step="0.1" wire:model.defer="dataFisik.tanda_vital.suhu" class="block w-full rounded-md border border-slate-300 px-2 py-1.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-1 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase mb-1">SpO2 <span class="lowercase">(%)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.spo2" class="block w-full rounded-md border border-slate-300 px-2 py-1.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-1 shadow-sm">
                    </div>
                </div>

                <div class="mt-4 bg-blue-50 border border-blue-100 p-3 rounded-lg flex items-center justify-between shadow-sm">
                    <span class="text-[11px] sm:text-xs font-bold text-slate-700 uppercase">Kalkulasi BMI</span>
                    <span class="text-xs sm:text-sm font-black text-blue-700 bg-white px-3 py-1 rounded border border-blue-200">
                        {{ $bmiData['bmi'] }} <span class="font-normal text-slate-300 mx-1">|</span> {{ $bmiData['kategori'] }}
                    </span>
                </div>
            </div>
        </div>

        {{-- 3. KEPALA DAN LEHER --}}
        @php
            $kepalaOpts = [
                'anemi' => ['label' => 'Anemi', 'opts' => ['Tidak', 'Ya', 'Dalam batas normal']],
                'ikterus' => ['label' => 'Ikterus', 'opts' => ['Tidak', 'Ya', 'Dalam batas normal']],
                'dyspnoe' => ['label' => 'Dyspnoe', 'opts' => ['Tidak', 'Ya', 'Dalam batas normal']],
                'cyanosis' => ['label' => 'Cyanosis', 'opts' => ['Tidak', 'Ya', 'Dalam batas normal']],
                'refleks_pupil' => ['label' => 'Refleks Pupil', 'opts' => ['RCL +/-', 'RCTL +/-', 'Normal']],
                'hidung' => ['label' => 'Hidung', 'opts' => ['Dalam batas normal', 'Hipertrofi konka', 'Polip']],
                'tonsil_kanan' => ['label' => 'Tonsil Kanan', 'opts' => ['T0', 'T1', 'T2', 'T3', 'T4', 'Normal']],
                'tonsil_kiri' => ['label' => 'Tonsil Kiri', 'opts' => ['T0', 'T1', 'T2', 'T3', 'T4', 'Normal']],
                'serumen' => ['label' => 'Serumen', 'opts' => ['Tidak Ada', 'Ada', 'Normal', 'Dalam batas normal']],
                'membran_timpani' => ['label' => 'Membran Timpani', 'opts' => ['Normal', 'Dalam batas normal', 'Tidak Ada', 'Ada']],
            ];
            $leherOpts = [
                'jvp' => ['label' => 'JVP', 'opts' => ['Dalam batas normal', 'Pembesaran', 'Tidak ada']],
                'tiroid' => ['label' => 'Tiroid', 'opts' => ['Dalam batas normal', 'Pembesaran', 'Tidak ada']],
                'kelenjar_getah_bening' => ['label' => 'KGB', 'opts' => ['Dalam batas normal', 'Pembesaran', 'Tidak ada']],
            ];
        @endphp
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">3. Kepala & Leher</h3>
            </div>
            <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                
                <div class="bg-slate-50/50 p-3 sm:p-4 rounded-xl border border-slate-100">
                    <h4 class="font-bold text-xs sm:text-sm text-slate-700 mb-3 border-b border-slate-200 pb-2"><i class="fas fa-head-side-medical text-blue-500 mr-2"></i> Area Kepala</h4>
                    <div class="space-y-2.5">
                        @foreach ($kepalaOpts as $key => $data)
                            <div x-data="{ opt: '' }">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-xs sm:text-sm font-medium text-slate-600 flex-1">{{ $data['label'] }}</label>
                                    <select wire:model.defer="dataFisik.kepala.{{ $key }}" x-init="opt = $el.value" x-on:change="opt = $event.target.value" class="w-1/2 sm:w-5/12 rounded-md border-slate-300 py-1 px-2 text-[11px] sm:text-xs focus:border-blue-500 focus:ring-1">
                                        @foreach($data['opts'] as $option) <option value="{{ $option }}">{{ $option }}</option> @endforeach
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <input x-show="opt === 'Lainnya'" type="text" wire:model.defer="dataFisik.kepala.{{ $key }}_lainnya" class="mt-1.5 w-full rounded-md border-blue-300 bg-blue-50/50 py-1 px-2 text-[11px] sm:text-xs" placeholder="Sebutkan...">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-slate-50/50 p-3 sm:p-4 rounded-xl border border-slate-100 h-fit">
                    <h4 class="font-bold text-xs sm:text-sm text-slate-700 mb-3 border-b border-slate-200 pb-2"><i class="fas fa-user-tie text-blue-500 mr-2"></i> Area Leher</h4>
                    <div class="space-y-2.5">
                        @foreach ($leherOpts as $key => $data)
                            <div x-data="{ opt: '' }">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-xs sm:text-sm font-medium text-slate-600 flex-1">{{ $data['label'] }}</label>
                                    <select wire:model.defer="dataFisik.leher.{{ $key }}" x-init="opt = $el.value" x-on:change="opt = $event.target.value" class="w-1/2 sm:w-5/12 rounded-md border-slate-300 py-1 px-2 text-[11px] sm:text-xs focus:border-blue-500 focus:ring-1">
                                        @foreach($data['opts'] as $option) <option value="{{ $option }}">{{ $option }}</option> @endforeach
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <input x-show="opt === 'Lainnya'" type="text" wire:model.defer="dataFisik.leher.{{ $key }}_lainnya" class="mt-1.5 w-full rounded-md border-blue-300 bg-blue-50/50 py-1 px-2 text-[11px] sm:text-xs" placeholder="Sebutkan...">
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- 4. DADA, PARU, ABDOMEN --}}
        @php
            $abdomenOpts = [
                'peristaltik' => ['label' => 'Peristaltik', 'opts' => ['Dalam batas normal', 'Meningkat', 'Menurun']],
                'nyeri_tekan' => ['label' => 'Nyeri Tekan', 'opts' => ['Tidak Ada', 'Ada', 'Dalam batas normal']],
                'massa' => ['label' => 'Massa', 'opts' => ['Tidak Ada', 'Ada', 'Dalam batas normal']],
                'hati' => ['label' => 'Hati', 'opts' => ['Dalam batas normal', 'Hepatomegali']],
                'limpa' => ['label' => 'Limpa', 'opts' => ['Dalam batas normal', 'Splenomegali']],
            ];
        @endphp

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">4. Dada, Paru, Abdomen & Ekstremitas</h3>
            </div>
            <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                
                {{-- Dada & Paru --}}
                <div class="space-y-4">
                    <div class="bg-slate-50/50 p-3 sm:p-4 rounded-xl border border-slate-100">
                        <h4 class="font-bold text-xs sm:text-sm text-slate-700 mb-3 border-b border-slate-200 pb-2"><i class="fas fa-heartbeat text-red-500 mr-2"></i> Dada (Jantung)</h4>
                        <div class="space-y-2.5">
                            <div x-data="{ opt_a: '', opt_b: '' }" class="flex items-center justify-between gap-2">
                                <label class="text-xs sm:text-sm font-medium text-slate-600 w-1/3">Bunyi Jtg I</label>
                                <div class="flex gap-1.5 w-2/3">
                                    <select wire:model.defer="dataFisik.dada.bunyi_jantung_1_a" class="w-1/2 rounded-md border-slate-300 py-1 px-1 text-[10px] sm:text-[11px]"><option>Murni</option><option>Bising</option><option>Lainnya</option></select>
                                    <select wire:model.defer="dataFisik.dada.bunyi_jantung_1_b" class="w-1/2 rounded-md border-slate-300 py-1 px-1 text-[10px] sm:text-[11px]"><option>Reguler</option><option>Ireguler</option><option>Lainnya</option></select>
                                </div>
                            </div>
                            <div x-data="{ opt_a: '', opt_b: '' }" class="flex items-center justify-between gap-2">
                                <label class="text-xs sm:text-sm font-medium text-slate-600 w-1/3">Bunyi Jtg II</label>
                                <div class="flex gap-1.5 w-2/3">
                                    <select wire:model.defer="dataFisik.dada.bunyi_jantung_2_a" class="w-1/2 rounded-md border-slate-300 py-1 px-1 text-[10px] sm:text-[11px]"><option>Murni</option><option>Bising</option><option>Lainnya</option></select>
                                    <select wire:model.defer="dataFisik.dada.bunyi_jantung_2_b" class="w-1/2 rounded-md border-slate-300 py-1 px-1 text-[10px] sm:text-[11px]"><option>Reguler</option><option>Ireguler</option><option>Lainnya</option></select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50/50 p-3 sm:p-4 rounded-xl border border-slate-100">
                        <h4 class="font-bold text-xs sm:text-sm text-slate-700 mb-3 border-b border-slate-200 pb-2"><i class="fas fa-lungs text-blue-500 mr-2"></i> Paru</h4>
                        <div class="space-y-2.5">
                            <div x-data="{ opt: '' }">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-xs sm:text-sm font-medium text-slate-600 flex-1">Nafas Dasar</label>
                                    <select wire:model.defer="dataFisik.paru.bunyi_nafas" x-init="opt = $el.value" x-on:change="opt = $event.target.value" class="w-1/2 sm:w-5/12 rounded-md border-slate-300 py-1 px-2 text-[11px] sm:text-xs"><option>Vesikular</option><option>Bronkial</option><option>Bronkovesikular</option><option>Lainnya</option></select>
                                </div>
                                <input x-show="opt === 'Lainnya'" type="text" wire:model.defer="dataFisik.paru.bunyi_nafas_lainnya" class="mt-1.5 w-full rounded-md border-blue-300 bg-blue-50/50 py-1 px-2 text-[11px] sm:text-xs" placeholder="Sebutkan...">
                            </div>
                            <div x-data="{ opt: '' }">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-xs sm:text-sm font-medium text-slate-600 flex-1">Nafas Tambahan</label>
                                    <select wire:model.defer="dataFisik.paru.bunyi_nafas_tambahan" x-init="opt = $el.value" x-on:change="opt = $event.target.value" class="w-1/2 sm:w-5/12 rounded-md border-slate-300 py-1 px-2 text-[11px] sm:text-xs"><option>Tidak ada</option><option>Ronkhi</option><option>Wheezing</option><option>Stridor</option><option>Lainnya</option></select>
                                </div>
                                <input x-show="opt === 'Lainnya'" type="text" wire:model.defer="dataFisik.paru.bunyi_nafas_tambahan_lainnya" class="mt-1.5 w-full rounded-md border-blue-300 bg-blue-50/50 py-1 px-2 text-[11px] sm:text-xs" placeholder="Sebutkan...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Abdomen & Ekstremitas --}}
                <div class="space-y-4">
                    <div class="bg-slate-50/50 p-3 sm:p-4 rounded-xl border border-slate-100">
                        <h4 class="font-bold text-xs sm:text-sm text-slate-700 mb-3 border-b border-slate-200 pb-2"><i class="fas fa-procedures text-blue-500 mr-2"></i> Abdomen</h4>
                        <div class="space-y-2.5">
                            @foreach ($abdomenOpts as $key => $data)
                                <div x-data="{ opt: '' }">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="text-xs sm:text-sm font-medium text-slate-600 flex-1">{{ $data['label'] }}</label>
                                        <select wire:model.defer="dataFisik.abdomen.{{ $key }}" x-init="opt = $el.value" x-on:change="opt = $event.target.value" class="w-1/2 sm:w-5/12 rounded-md border-slate-300 py-1 px-2 text-[11px] sm:text-xs">
                                            @foreach($data['opts'] as $option) <option value="{{ $option }}">{{ $option }}</option> @endforeach
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <input x-show="opt === 'Lainnya'" type="text" wire:model.defer="dataFisik.abdomen.{{ $key }}_lainnya" class="mt-1.5 w-full rounded-md border-blue-300 bg-blue-50/50 py-1 px-2 text-[11px] sm:text-xs" placeholder="Sebutkan...">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-slate-50/50 p-3 sm:p-4 rounded-xl border border-slate-100">
                        <h4 class="font-bold text-xs sm:text-sm text-slate-700 mb-3 border-b border-slate-200 pb-2"><i class="fas fa-child text-blue-500 mr-2"></i> Ekstremitas</h4>
                        <div class="space-y-2.5">
                            <div x-data="{ opt: '' }">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-xs sm:text-sm font-medium text-slate-600 flex-1">Kondisi</label>
                                    <select wire:model.defer="dataFisik.ekstremitas.ekstremitas" x-init="opt = $el.value" x-on:change="opt = $event.target.value" class="w-1/2 sm:w-5/12 rounded-md border-slate-300 py-1 px-2 text-[11px] sm:text-xs"><option>Dalam Batas Normal</option><option>Edema Kanan</option><option>Edema Kiri</option><option>Lainnya</option></select>
                                </div>
                                <input x-show="opt === 'Lainnya'" type="text" wire:model.defer="dataFisik.ekstremitas.ekstremitas_lainnya" class="mt-1.5 w-full rounded-md border-blue-300 bg-blue-50/50 py-1 px-2 text-[11px] sm:text-xs" placeholder="Sebutkan...">
                            </div>

                            <div x-data="{ ref_kanan: '' }">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-[11px] sm:text-xs font-medium text-slate-600 flex-1">Fisiologis Kanan</label>
                                    <select wire:model.defer="dataFisik.ekstremitas.refleks_fisiologis_kanan" x-init="ref_kanan = $el.value" x-on:change="ref_kanan = $event.target.value" class="w-5/12 rounded-md border-slate-300 py-1 px-2 text-[10px] sm:text-[11px]"><option>0</option><option>+1</option><option>+2</option><option>+3</option><option>+4</option><option>Lainnya</option></select>
                                </div>
                                <input x-show="ref_kanan === 'Lainnya'" type="text" wire:model.defer="dataFisik.ekstremitas.refleks_fisiologis_kanan_lainnya" class="mt-1.5 w-full rounded-md border-blue-300 bg-blue-50/50 py-1 px-2 text-[10px] sm:text-[11px]" placeholder="Sebutkan...">
                            </div>
                            <div x-data="{ ref_kiri: '' }">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-[11px] sm:text-xs font-medium text-slate-600 flex-1">Fisiologis Kiri</label>
                                    <select wire:model.defer="dataFisik.ekstremitas.refleks_fisiologis_kiri" x-init="ref_kiri = $el.value" x-on:change="ref_kiri = $event.target.value" class="w-5/12 rounded-md border-slate-300 py-1 px-2 text-[10px] sm:text-[11px]"><option>0</option><option>+1</option><option>+2</option><option>+3</option><option>+4</option><option>Lainnya</option></select>
                                </div>
                                <input x-show="ref_kiri === 'Lainnya'" type="text" wire:model.defer="dataFisik.ekstremitas.refleks_fisiologis_kiri_lainnya" class="mt-1.5 w-full rounded-md border-blue-300 bg-blue-50/50 py-1 px-2 text-[10px] sm:text-[11px]" placeholder="Sebutkan...">
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <label class="text-[11px] sm:text-xs font-medium text-slate-600 flex-1">Patologis Kanan</label>
                                <select wire:model.defer="dataFisik.ekstremitas.refleks_patologis_kanan" class="w-5/12 rounded-md border-slate-300 py-1 px-2 text-[10px] sm:text-[11px]"><option>Tidak Ada</option><option>Ada</option></select>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <label class="text-[11px] sm:text-xs font-medium text-slate-600 flex-1">Patologis Kiri</label>
                                <select wire:model.defer="dataFisik.ekstremitas.refleks_patologis_kiri" class="w-5/12 rounded-md border-slate-300 py-1 px-2 text-[10px] sm:text-[11px]"><option>Tidak Ada</option><option>Ada</option></select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. RIWAYAT PAJANAN PEKERJAAN --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">5. Riwayat Pajanan Pekerjaan</h3>
            </div>
            
            <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                {{-- Kiri: Fisik & Kimia --}}
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-[11px] sm:text-xs text-indigo-800 mb-2 border-b pb-1">A. Bahaya Fisik</h4>
                        <div class="space-y-1.5">
                            @foreach (['kebisingan' => 'Kebisingan', 'suhu_panas' => 'Suhu panas', 'suhu_dingin' => 'Suhu dingin', 'radiasi_non_pengion' => 'Rad. bukan pengion', 'radiasi_pengion' => 'Rad. pengion', 'getaran_lokal' => 'Getaran lokal', 'getaran_seluruh_tubuh' => 'Getaran tubuh', 'ketinggian' => 'Ketinggian', 'lain_fisik' => 'Lain - lain'] as $key => $label)
                                <div class="flex items-center justify-between bg-slate-50 px-2 sm:px-3 py-1 sm:py-1.5 rounded border border-slate-100">
                                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-600 flex-1 truncate pr-2">{{ $label }}</span>
                                    <select wire:model.defer="dataFisik.pajanan.fisik.{{ $key }}" class="w-16 sm:w-20 rounded border-slate-300 py-0.5 px-1 text-[10px] sm:text-[11px] font-bold focus:ring-1 cursor-pointer"><option>Tidak</option><option>Ya</option></select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-[11px] sm:text-xs text-emerald-800 mb-2 border-b pb-1">B. Bahaya Kimia</h4>
                        <div class="space-y-1.5">
                            @foreach (['debu_anorganik' => 'Debu anorganik (Silika)', 'debu_organic' => 'Debu organic (Kapas)', 'asap' => 'Asap', 'logam_berat' => 'Logam berat', 'iritan_asam' => 'Iritan asam', 'iritan_basa' => 'Iritan basa', 'cairan_pembersih' => 'Cairan pembersih', 'pestisida' => 'Pestisida', 'uap_logam' => 'Uap logam', 'lain_kimia' => 'Lain - lain'] as $key => $label)
                                <div class="flex items-center justify-between bg-slate-50 px-2 sm:px-3 py-1 sm:py-1.5 rounded border border-slate-100">
                                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-600 flex-1 truncate pr-2" title="{{ $label }}">{{ $label }}</span>
                                    <select wire:model.defer="dataFisik.pajanan.kimia.{{ $key }}" class="w-16 sm:w-20 rounded border-slate-300 py-0.5 px-1 text-[10px] sm:text-[11px] font-bold focus:ring-1 cursor-pointer"><option>Tidak</option><option>Ya</option></select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kanan: Biologi, Psikologi, Ergonomis --}}
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-[11px] sm:text-xs text-rose-800 mb-2 border-b pb-1">C. Bahaya Biologi</h4>
                        <div class="space-y-1.5">
                            @foreach (['bakteri' => 'Bakteri / Virus / Jamur', 'darah' => 'Darah / Cairan tubuh', 'nyamuk' => 'Nyamuk / Serangga', 'limbah' => 'Limbah', 'lain_biologi' => 'Lain - lain'] as $key => $label)
                                <div class="flex items-center justify-between bg-slate-50 px-2 sm:px-3 py-1 sm:py-1.5 rounded border border-slate-100">
                                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-600 flex-1 truncate pr-2">{{ $label }}</span>
                                    <select wire:model.defer="dataFisik.pajanan.biologi.{{ $key }}" class="w-16 sm:w-20 rounded border-slate-300 py-0.5 px-1 text-[10px] sm:text-[11px] font-bold focus:ring-1 cursor-pointer"><option>Tidak</option><option>Ya</option></select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-[11px] sm:text-xs text-purple-800 mb-2 border-b pb-1">D. Psikologi & Ergonomis</h4>
                        <div class="space-y-1.5">
                            @foreach (['beban_kerja' => 'Beban kerja tdk sesuai', 'pekerjaan_tidak_sesuai' => 'Pekerjaan tdk sesuai', 'bekerja_giliran' => 'Bekerja shift', 'konflik_teman_sekerja' => 'Konflik kerja', 'gerakan_berulang' => 'Gerakan berulang', 'angkat_berat' => 'Angkat/angkut berat', 'duduk_lama' => 'Duduk lama > 4 jam', 'berdiri_lama' => 'Berdiri lama > 4 jam', 'posisi_tidak_ergonomis' => 'Posisi tdk ergonomis', 'bekerja_layar_monitor' => 'Layar monitor > 4 jam'] as $key => $label)
                                <div class="flex items-center justify-between bg-slate-50 px-2 sm:px-3 py-1 sm:py-1.5 rounded border border-slate-100">
                                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-600 flex-1 truncate pr-2" title="{{ $label }}">{{ $label }}</span>
                                    <select wire:model.defer="dataFisik.pajanan.{{ in_array($key, ['gerakan_berulang','angkat_berat','duduk_lama','berdiri_lama','posisi_tidak_ergonomis','bekerja_layar_monitor']) ? 'ergonomis' : 'psikologi' }}.{{ $key }}" class="w-16 sm:w-20 rounded border-slate-300 py-0.5 px-1 text-[10px] sm:text-[11px] font-bold focus:ring-1 cursor-pointer"><option>Tidak</option><option>Ya</option></select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. DIAGNOSA & KESIMPULAN --}}
        <div class="bg-amber-50 rounded-xl border border-amber-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-amber-200 bg-amber-100/70">
                <h3 class="text-xs sm:text-sm font-bold text-amber-900 uppercase tracking-wide">6. Kesimpulan & Diagnosa</h3>
            </div>
            <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-amber-800 mb-1.5">Ringkasan Hasil Klinis</label>
                    <textarea wire:model.defer="kesimpulan" rows="3" class="block w-full rounded-md border border-amber-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 placeholder-amber-400 shadow-sm resize-none" placeholder="Tuliskan ringkasan..."></textarea>
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-amber-800 mb-1.5">Saran & Tindak Lanjut</label>
                    <textarea wire:model.defer="keterangan" rows="3" class="block w-full rounded-md border border-amber-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 placeholder-amber-400 shadow-sm resize-none" placeholder="Saran perbaikan..."></textarea>
                </div>
            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 pb-2">
            @if($fisikResult && $fisikResult->file_path)
                <a href="{{ asset('storage/' . $fisikResult->file_path) }}" target="_blank"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-emerald-500 text-emerald-600 text-xs sm:text-sm font-bold rounded-lg shadow-sm hover:bg-emerald-50 transition-all">
                    <i class="fas fa-file-pdf mr-2"></i> Laporan PDF
                </a>
            @endif
            
            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white text-xs sm:text-sm font-bold rounded-lg shadow-sm transition-all">
                <span wire:loading.remove wire:target="simpanHasil"><i class="fas fa-save mr-2"></i> Simpan Data</span>
                <span wire:loading wire:target="simpanHasil"><i class="fas fa-spinner fa-spin mr-2"></i> Memproses...</span>
            </button>
        </div>
    </form>
</div>