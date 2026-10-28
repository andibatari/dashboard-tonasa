@section('title', 'Detail Jadwal Pasien MCU')

{{-- OUTER CONTAINER (ROOT ELEMENT LIVEWIRE) --}}
{{-- PERBAIKAN: Padding mobile dikurangi (py-4 px-2) agar konten lebih lega di HP --}}
<div wire:poll.3s class="py-4 px-2 md:py-6 md:px-6 md:max-w-6xl md:mx-auto min-h-screen"> 
    
    {{-- KARTU UTAMA --}}
    <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden">

        {{-- 1. HEADER --}}
        {{-- PERBAIKAN: Flexbox lebih rapi di HP, tombol cetak mengambil full lebar (w-full) di layar kecil --}}
        <div class="bg-gradient-to-r from-red-700 to-red-600 px-5 py-4 md:px-8 md:py-6 flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-xl backdrop-blur-sm shrink-0">
                    <i class="fas fa-notes-medical text-white text-xl"></i>
                </div>
                <h1 class="text-lg md:text-2xl font-black text-white tracking-wide leading-tight">Detail Pasien MCU</h1>
            </div>
            
            <button
                x-data="{ jadwalId: {{ $jadwal->id }}, mergedUrl: '{{ route('download.mcu.summary', ['jadwalId' => '__ID__']) }}' }"
                @click.prevent="window.open(mergedUrl.replace('__ID__', jadwalId), '_blank')"
                class="w-full md:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-white text-red-700 rounded-xl font-bold text-sm tracking-wide hover:bg-red-50 active:scale-95 transition-all duration-200 shadow-sm"
            >
                <i class="fas fa-file-pdf mr-2 text-red-600"></i> Cetak Hasil MCU
            </button>
        </div>

        <div class="p-4 md:p-8">
            {{-- 2. BAGIAN DETAIL PASIEN --}}
            {{-- PERBAIKAN: Gap dikurangi untuk mobile agar tidak terlalu jauh --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-6 mb-6 md:mb-8">
                
                {{-- KIRI: KODE PENDAFTARAN (QR) --}}
                <div class="md:col-span-4 flex flex-col items-center justify-center p-4 md:p-5 bg-slate-50 rounded-2xl border border-slate-100">
                    <h3 class="text-xs md:text-sm font-bold text-slate-500 uppercase tracking-widest mb-3 md:mb-4">Kode Pendaftaran</h3>
                    <div class="bg-white p-2.5 md:p-3 rounded-2xl shadow-sm border border-slate-200">
                        @if ($qrCodeImage)
                            <img src="data:image/png;base64,{{ $qrCodeImage }}" alt="QR Code Pasien" class="w-28 h-28 md:w-44 md:h-44 object-contain"> 
                        @else
                            <div class="w-28 h-28 md:w-44 md:h-44 flex items-center justify-center bg-slate-50 rounded-xl">
                                <p class="text-slate-400 font-medium text-center text-[10px] md:text-xs">QR Tidak Tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- KANAN: DATA PRIBADI --}}
                <div class="md:col-span-8 flex flex-col justify-center space-y-3 md:space-y-4"> 
                    @if ($patient)
                        <div>
                            <div class="flex flex-wrap items-center gap-2 md:gap-3 mb-1">
                                <h2 class="text-xl md:text-3xl font-black text-slate-800 leading-tight">{{ $patient->nama_lengkap ?? $patient->nama_karyawan }}</h2>
                                <span class="px-2.5 py-1 rounded-full text-[10px] md:text-xs font-bold border
                                    @if($jadwal->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200
                                    @elseif($jadwal->status === 'Present') bg-blue-50 text-blue-600 border-blue-200
                                    @else bg-emerald-50 text-emerald-600 border-emerald-200 @endif">
                                    <i class="fas fa-circle text-[8px] mr-1"></i> {{ $jadwal->status }}
                                </span>
                            </div>
                            <p class="text-xs md:text-sm font-medium text-slate-500 flex items-center gap-2">
                                <i class="fas fa-building text-slate-400"></i> {{ $patient->perusahaan_asal ?? 'PT Semen Tonasa' }}
                            </p>
                        </div>

                        {{-- Grid Info List Pasien --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 md:gap-3 mt-2">
                            <div class="flex items-center gap-3 p-2.5 md:p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="bg-white p-2 rounded-lg shadow-sm text-slate-400 shrink-0"><i class="fas fa-id-card w-4 text-center"></i></div>
                                <div class="min-w-0">
                                    <p class="text-[10px] md:text-xs text-slate-400 font-semibold mb-0.5 uppercase tracking-wider">SAP / NIK</p>
                                    <p class="text-xs md:text-sm font-bold text-slate-700 truncate">{{ $patient->no_sap ?? '-' }} / {{ $patient->nik_pasien ?? $patient->nik_karyawan }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-2.5 md:p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="bg-white p-2 rounded-lg shadow-sm text-slate-400 shrink-0"><i class="fas fa-calendar-alt w-4 text-center"></i></div>
                                <div class="min-w-0">
                                    <p class="text-[10px] md:text-xs text-slate-400 font-semibold mb-0.5 uppercase tracking-wider">Tgl. Lahir</p>
                                    <p class="text-xs md:text-sm font-bold text-slate-700 truncate">{{ \Carbon\Carbon::parse($patient->tanggal_lahir)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-2.5 md:p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="bg-white p-2 rounded-lg shadow-sm text-slate-400 shrink-0"><i class="fas fa-venus-mars w-4 text-center"></i></div>
                                <div class="min-w-0">
                                    <p class="text-[10px] md:text-xs text-slate-400 font-semibold mb-0.5 uppercase tracking-wider">Gender</p>
                                    <p class="text-xs md:text-sm font-bold text-slate-700 truncate">{{ $patient->jenis_kelamin }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-2.5 md:p-3 bg-red-50 rounded-xl border border-red-100">
                                <div class="bg-white p-2 rounded-lg shadow-sm text-red-500 shrink-0"><i class="fas fa-stethoscope w-4 text-center"></i></div>
                                <div class="min-w-0">
                                    <p class="text-[10px] md:text-xs text-red-400 font-semibold mb-0.5 uppercase tracking-wider">Paket MCU</p>
                                    <p class="text-xs md:text-sm font-bold text-red-700 truncate">{{ $jadwal->paketMcu->nama_paket ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-5 bg-red-50 rounded-2xl border border-red-100 text-center flex flex-col items-center justify-center h-full">
                            <i class="fas fa-exclamation-triangle text-3xl text-red-400 mb-2"></i>
                            <h2 class="text-lg font-bold text-red-700 mb-1">Data Pasien Tidak Ditemukan</h2>
                            <p class="text-red-500 text-xs md:text-sm">Pastikan data karyawan/peserta terdaftar di sistem.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. TABS NAVIGASI (MODERN PILL TABS) --}}
            {{-- PERBAIKAN: Padding tab disesuaikan untuk HP agar tombol tidak terlalu besar --}}
            <div class="mb-5 md:mb-6 overflow-x-auto pb-3 scrollbar-hide"> 
                <ul class="flex space-x-2 w-max" role="tablist">
                    <li role="presentation">
                        <button class="px-4 md:px-5 py-2 md:py-2.5 rounded-full font-bold text-xs md:text-sm transition-all duration-200 flex items-center gap-2
                            @if($activeTab === 'summary') bg-slate-800 text-white shadow-md @else bg-slate-100 text-slate-500 hover:bg-slate-200 @endif"
                            wire:click="$set('activeTab', 'summary')" type="button">
                            <i class="fas fa-list-ul"></i> Ringkasan
                        </button>
                    </li>

                    <li role="presentation">
                        <button class="px-4 md:px-5 py-2 md:py-2.5 rounded-full font-bold text-xs md:text-sm transition-all duration-200 flex items-center gap-2
                            @if($activeTab === 'resume') bg-red-600 text-white shadow-md shadow-red-200 @else bg-slate-100 text-slate-500 hover:bg-slate-200 @endif"
                            wire:click="$set('activeTab', 'resume')" type="button">
                            <i class="fas fa-file-medical"></i> Resume Dokter
                        </button> 
                    </li>

                    @if($polis->count() > 0)
                        <div class="w-px h-6 bg-slate-200 my-auto mx-1"></div> 
                        @foreach ($polis as $poli)
                            @php
                                $shortName = match(strtoupper($poli->nama_poli)) {
                                    'RESUME DOKTER' => 'Resume', 'LABORATORIUM' => 'LAB', 'FISIK' => 'FISIK', 'GIGI' => 'GIGI', 'EKG' => 'EKG', 'AUDIOMETRI' => 'AUDIOMETRI', 'SPIROMETRI' => 'SPIROMETRI', 'KEBUGARAN' => 'KEBUGARAN', 'THORAX PHOTO' => 'THORAX', 'TREADMILL' => 'TREADMILL', 'USG' => 'USG', default => $poli->nama_poli,
                                };
                            @endphp
                            <li role="presentation">
                                <button class="px-3 md:px-4 py-2 md:py-2.5 rounded-full font-bold text-xs md:text-sm transition-all duration-200 whitespace-nowrap
                                    @if($activeTab === 'poli-' . $poli->id) bg-blue-600 text-white shadow-md shadow-blue-200 @else bg-slate-100 text-slate-500 hover:bg-slate-200 @endif"
                                    wire:click="$set('activeTab', 'poli-{{ $poli->id }}')" type="button">
                                    {{ $shortName }}
                                </button>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            {{-- 4. KONTEN TABS --}}
            <div id="tab-content" class="min-h-[300px]"> 
                
                {{-- TAB RINGKASAN POLI --}}
                @if ($activeTab === 'summary')
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-4 py-3 md:px-5 md:py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-sm md:text-base font-bold text-slate-800">Status Pemeriksaan Poli</h3>
                        </div>
                        <div class="overflow-x-auto"> 
                            {{-- PERBAIKAN: whitespace-nowrap ditambahkan agar tabel tidak hancur di HP --}}
                            <table class="min-w-full text-xs md:text-sm text-left text-slate-600 whitespace-nowrap"> 
                                <thead class="text-[10px] md:text-xs text-slate-500 uppercase bg-slate-50 font-bold border-b border-slate-200">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 md:py-4 md:px-6">Nama Poli</th> 
                                        <th scope="col" class="py-3 px-4 md:py-4 md:px-6 text-center">Status</th> 
                                        <th scope="col" class="py-3 px-4 md:py-4 md:px-6 text-center">Tindakan Cepat</th> 
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($polis as $poli)
                                        @php
                                            $poliData = $jadwalPoliData[$poli->id] ?? (object)['status' => 'Pending', 'id' => null];
                                            $shortName = match(strtoupper($poli->nama_poli)) {
                                                'RESUME DOKTER' => 'Resume', 'LABORATORIUM' => 'LAB', 'FISIK' => 'FISIK', 'GIGI' => 'GIGI', 'EKG' => 'EKG', 'AUDIOMETRI' => 'AUDIOMETRI', 'SPIROMETRI' => 'SPIROMETRI', 'THORAX PHOTO' => 'THORAX', 'KEBUGARAN' => 'KEBUGARAN', 'TREADMILL' => 'TREADMILL', 'USG' => 'USG', default => $poli->nama_poli,
                                            };
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-3 px-4 md:py-4 md:px-6 font-bold text-slate-800">{{ $shortName }}</td>
                                            <td class="py-3 px-4 md:py-4 md:px-6 text-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] md:text-xs font-bold border
                                                    @if($poliData->status === 'Pending') bg-slate-100 text-slate-600 border-slate-200
                                                    @elseif($poliData->status === 'Waiting') bg-amber-50 text-amber-600 border-amber-200
                                                    @elseif($poliData->status === 'Calling') bg-blue-50 text-blue-600 border-blue-200 animate-pulse
                                                    @elseif($poliData->status === 'Done' || $poliData->status === 'Finished') bg-emerald-50 text-emerald-600 border-emerald-200
                                                    @else bg-red-50 text-red-600 border-red-200 @endif">
                                                    @if($poliData->status === 'Calling') <i class="fas fa-volume-up mr-1"></i> @endif
                                                    {{ $poliData->status }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 md:py-4 md:px-6 text-center">
                                                @if($poliData->id)
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button wire:click="panggilPasien({{ $poli->id }})" title="Panggil Pasien"
                                                            class="w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white transition-all active:scale-90">
                                                            <i class="fas fa-bullhorn text-[10px] md:text-xs"></i>
                                                        </button>
                                                        <button wire:click="markAsDone({{ $poli->id }})" title="Tandai Selesai"
                                                            class="w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center transition-all active:scale-90
                                                            @if($poliData->status === 'Finished' || $poliData->status === 'Done') bg-slate-100 text-slate-300 cursor-not-allowed @else text-emerald-600 bg-emerald-50 hover:bg-emerald-600 hover:text-white @endif"
                                                            @if($poliData->status === 'Finished' || $poliData->status === 'Done') disabled @endif>
                                                            <i class="fas fa-check text-[10px] md:text-xs"></i>
                                                        </button>
                                                        <button wire:click="markAsPending({{ $poli->id }})" title="Batalkan (Pending)"
                                                            class="w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center transition-all active:scale-90
                                                            @if($poliData->status === 'Pending') bg-slate-100 text-slate-300 cursor-not-allowed @else text-slate-500 bg-slate-100 hover:bg-slate-500 hover:text-white @endif"
                                                            @if($poliData->status === 'Pending') disabled @endif>
                                                            <i class="fas fa-undo text-[10px] md:text-xs"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-[10px] md:text-xs text-slate-400 italic">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-8 text-center text-xs md:text-sm text-slate-400">Belum ada daftar poli untuk paket MCU ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- TAB RESUME --}}
                @if ($activeTab === 'resume')
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                        <div class="px-4 py-3 md:px-5 md:py-4 border-b border-slate-100 bg-red-50/50 flex items-center gap-3">
                            <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-notes-medical text-sm"></i>
                            </div>
                            <h3 class="text-sm md:text-base font-bold text-red-800">Form Resume Medis</h3>
                        </div>

                        {{-- TAMBAHAN: KOTAK NOTIFIKASI BANNER --}}
                        <div class="px-4 pt-4 md:px-7 md:pt-7">
                            @if (session()->has('success'))
                                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl flex items-center shadow-sm">
                                    <i class="fas fa-check-circle text-xl mr-3"></i>
                                    <span class="font-medium text-sm">{{ session('success') }}</span>
                                </div>
                            @endif

                            @if (session()->has('error'))
                                <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl flex items-center shadow-sm">
                                    <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                                    <span class="font-medium text-sm">{{ session('error') }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <form wire:submit.prevent="saveResume" class="p-4 md:p-7 space-y-5 md:space-y-6">
                            
                            {{-- HASIL PEMERIKSAAN (GRID) --}}
                            <div>
                                <h4 class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 md:mb-4">A. Hasil Pemeriksaan Klinis</h4>
                                
                                {{-- PERBAIKAN: Menyesuaikan KEY dengan Flutter --}}
                                @php
                                $resumeFields = [
                                    'bmi' => ['label' => '1. BMI (Otomatis)', 'placeholder' => 'Sistem menghitung...'],
                                    'laboratorium' => ['label' => '2. Laboratorium', 'placeholder' => 'Misal: Asam Urat tinggi...'], 
                                    'ekg' => ['label' => '3. ECG/Jantung', 'placeholder' => 'Misal: Sinus Rhythm...'], 
                                    'gigi' => ['label' => '4. Gigi & Mulut', 'placeholder' => 'Misal: Karies gigi...'], 
                                    'mata' => ['label' => '5. Visus/Mata', 'placeholder' => 'Misal: VOD 6/6...'], 
                                    'spirometri' => ['label' => '6. Spirometri', 'placeholder' => 'Misal: Mild Restriction...'], 
                                    'audiometri' => ['label' => '7. Audiometri', 'placeholder' => 'Misal: Tuli Sensorineural...'], 
                                    'kebugaran' => ['label' => '8. Kebugaran', 'placeholder' => 'Misal: Kategori Cukup...'], 
                                    'temuan_lain' => ['label' => '9. Temuan Lain/Fisik', 'placeholder' => 'Misal: Tensi 140/90...'], 
                                    'thorax' => ['label' => '10. Thorax Photo', 'placeholder' => 'Misal: Cor Normal...'], 
                                    'treadmill' => ['label' => '11. Treadmill', 'placeholder' => 'Misal: Ischemic Negatif...'], 
                                    'usg' => ['label' => '12. USG', 'placeholder' => 'Misal: Ginjal Normal...'],
                                ];
                                @endphp

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
                                    @foreach ($resumeFields as $key => $field)
                                        <div>
                                            <label class="block text-[10px] md:text-xs font-bold text-slate-600 mb-1">{{ $field['label'] }}</label>
                                            
                                            {{-- Data key akan menggunakan snake_case, tapi label tetap bagus dan mudah dibaca! --}}
                                            <input type="text" wire:model.defer="resumeData.{{ $key }}"
                                                @if($key === 'bmi') readonly @endif
                                                class="block w-full rounded-xl border-slate-200 shadow-sm text-sm focus:border-red-500 focus:ring-red-500 placeholder-slate-300 transition-colors
                                                @if($key === 'bmi') bg-slate-50 font-black text-slate-500 cursor-not-allowed border-slate-100 @endif" 
                                                placeholder="{{ $field['placeholder'] }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            {{-- SARAN DOKTER --}}
                            <div>
                                <h4 class="text-[10px] md:text-xs font-bold text-blue-500 uppercase tracking-widest mb-2 md:mb-3">B. Rekomendasi Medis</h4>
                                <label class="block text-xs md:text-sm font-bold text-slate-700 mb-1.5">Saran & Tindak Lanjut</label>
                                <textarea wire:model.defer="resumeSaran" rows="4" 
                                    class="block w-full rounded-xl border-slate-200 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-300 resize-none"
                                    placeholder="Tuliskan saran dokter disini..."></textarea>
                            </div>

                            {{-- KESIMPULAN & TOMBOL --}}
                            <div class="bg-amber-50 border border-amber-100 p-4 md:p-5 rounded-2xl flex flex-col md:flex-row md:items-end justify-between gap-4 mt-2"> 
                                <div class="w-full md:w-1/2">
                                    <label class="block text-xs md:text-sm font-black text-amber-800 mb-1.5 uppercase tracking-wide">
                                        Kesimpulan Akhir (Kelayakan)
                                    </label>
                                    <select wire:model.defer="resumeKategori"
                                        class="block w-full rounded-xl border-amber-200 shadow-sm text-sm font-bold text-slate-700 focus:border-amber-500 focus:ring-amber-500 bg-white cursor-pointer py-2 md:py-2.5">
                                        <option value="" disabled selected>-- Tentukan Status Fit --</option>
                                        <option value="Fit To Work (K1)">🟢 Fit To Work (K1)</option>
                                        <option value="Fit With Note (K2)">🟡 Fit With Note (K2)</option>
                                        <option value="Fit With Restrictive (K3)">🟠 Fit With Restrictive (K3)</option>
                                        <option value="Temporary Unfit (K4)">🔴 Temporary Unfit (K4)</option>
                                        <option value="Unfit (K5)">⚫ Unfit (K5)</option>
                                    </select>
                                </div>

                                <div class="w-full md:w-auto">
                                    <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 md:py-3 bg-slate-800 rounded-xl font-bold text-sm text-white hover:bg-slate-700 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200" wire:loading.attr="disabled" wire:target="saveResume">
                                        <span wire:loading.remove wire:target="saveResume"><i class="fas fa-save mr-2"></i> Simpan Data</span>
                                        <span wire:loading wire:target="saveResume"><i class="fas fa-circle-notch fa-spin mr-2"></i> Loading...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- TABS POLI LAINNYA (UPLOAD FILE / FORM INTERAKTIF) --}}
                @foreach ($polis as $poli)
                    @if ($activeTab === 'poli-' . $poli->id)
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 md:px-5 md:py-4 border-b border-slate-100 bg-blue-50/50 flex items-center gap-3">
                                <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-file-upload text-sm"></i>
                                </div>
                                <h3 class="text-sm md:text-base font-bold text-blue-800">Berkas {{ $poli->nama_poli }}</h3>
                            </div>

                            <div class="p-4 md:p-7">
                               {{-- AMBIL DATA JADWAL POLI SAAT INI --}}
                                @php
                                    $currentPoliData = $jadwalPoliData[$poli->id] ?? null;
                                    $currentPoliId = $currentPoliData ? $currentPoliData->id : null;
                                @endphp

                                @if (strtoupper($poli->nama_poli) === 'GIGI')
                                    @livewire('poli-gigi-form', [ 'jadwalId' => $jadwal->id, 'poliData' => $currentPoliData ], key('gigi-'.$poli->id))
                                
                                @elseif (strtoupper($poli->nama_poli) === 'KEBUGARAN')
                                    @livewire('kebugaran-form', ['patient' => $patient, 'jadwalPoliId' => $currentPoliId, 'poliData' => $currentPoliData], key('kebugaran-'.$poli->id))
                                
                                @elseif (strtoupper($poli->nama_poli) === 'FISIK')
                                    @livewire('poli-fisik-form', [ 'patient' => $patient, 'jadwalId' => $currentPoliId, 'poliData' => $currentPoliData ], key('fisik-'.$poli->id))
                                
                                @elseif (strtoupper($poli->nama_poli) === 'MATA')
                                    @livewire('poli-mata-form', [ 'patient' => $patient, 'jadwalPoliId' => $currentPoliId, 'poliData' => $currentPoliData ], key('mata-'.$poli->id))
                                
                                @elseif (in_array(strtoupper($poli->nama_poli), $uploadablePoliNames))
                                    <div class="max-w-2xl bg-slate-50 rounded-2xl border border-dashed border-slate-300 p-5 md:p-6 text-center mx-auto">
                                        <div class="mb-4">
                                            <div class="w-14 h-14 md:w-16 md:h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm border border-slate-100">
                                                <i class="fas fa-cloud-upload-alt text-xl md:text-2xl text-blue-500"></i>
                                            </div>
                                            <h4 class="text-xs md:text-sm font-bold text-slate-700">Unggah Laporan PDF</h4>
                                            <p class="text-[10px] md:text-xs text-slate-400 mt-1">Maksimal ukuran file 10MB.</p>
                                        </div>

                                        <div class="flex flex-col sm:flex-row items-center justify-center gap-2 md:gap-3">
                                            <label for="file-{{ $poli->id }}" class="w-full sm:w-auto cursor-pointer bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold py-2 md:py-2.5 px-5 text-xs md:text-sm rounded-xl shadow-sm transition-all duration-200">
                                                <i class="fas fa-folder-open mr-2 text-blue-500"></i> Telusuri File
                                            </label>
                                            <input type="file" id="file-{{ $poli->id }}" wire:model="pdfFiles.{{ $poli->id }}" wire:key="upload-input-{{ $poli->id }}" accept="application/pdf" class="sr-only">
                                            
                                            <button wire:click="savePdf({{ $poli->id }})" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 md:py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-xs md:text-sm" wire:loading.attr="disabled" wire:target="pdfFiles.{{ $poli->id }}">
                                                <span wire:loading.remove wire:target="pdfFiles.{{ $poli->id }}"><i class="fas fa-cloud-upload-alt mr-2"></i> Upload Sekarang</span>
                                                <span wire:loading wire:target="pdfFiles.{{ $poli->id }}"><i class="fas fa-spinner fa-spin mr-2"></i> Memproses...</span>
                                            </button>
                                        </div>

                                        <div class="mt-4 pt-4 border-t border-slate-200/60">
                                            <span class="text-[10px] md:text-sm font-medium text-slate-600" wire:loading.remove wire:target="pdfFiles.{{ $poli->id }}">
                                                @if(isset($pdfFiles[$poli->id]))
                                                    <span class="text-blue-600"><i class="fas fa-file-pdf mr-1"></i> {{ $pdfFiles[$poli->id]->getClientOriginalName() }}</span> (Siap Diupload)
                                                @elseif(isset($uploadedFileNames[$poli->id]) && $uploadedFileNames[$poli->id])
                                                    <span class="text-emerald-600"><i class="fas fa-check-circle mr-1"></i> {{ $uploadedFileNames[$poli->id] }}</span>
                                                @else
                                                    <span class="text-slate-400 italic">Belum ada file dipilih</span>
                                                @endif
                                            </span>
                                            @error('pdfFiles.' . $poli->id) <p class="mt-2 text-[10px] md:text-xs font-bold text-red-500"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    @if (isset($jadwalPoliData[$poli->id]) && $jadwalPoliData[$poli->id]->file_path)
                                        @php
                                            $path = $jadwalPoliData[$poli->id]->file_path;
                                            $fileUrl = str_contains($path, 'http') ? $path : asset('storage/' . $path);
                                        @endphp

                                        <div class="mt-5 md:mt-6 flex flex-col sm:flex-row gap-2 md:gap-3">
                                            <a href="{{ $fileUrl }}" target="_blank"
                                                class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 md:py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs md:text-sm transition-colors border border-slate-200">
                                                <i class="fas fa-external-link-alt mr-2 text-slate-500"></i> Buka File
                                            </a>
                                            <a href="{{ $fileUrl }}" target="_blank" download 
                                                class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 md:py-2.5 bg-white hover:bg-emerald-50 text-emerald-700 rounded-xl font-bold text-xs md:text-sm transition-colors border border-emerald-200">
                                                <i class="fas fa-download mr-2 text-emerald-500"></i> Unduh File
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                            <i class="fas fa-keyboard text-xl text-slate-400"></i>
                                        </div>
                                        <p class="text-xs md:text-sm text-slate-600 font-medium">Poli ini memerlukan input form interaktif.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- STYLE DAN SCRIPT --}}
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabName = urlParams.get('tab');
            if (tabName) {
                setTimeout(() => { Livewire.dispatch('changeTab', { tabName: tabName }); }, 300);
            }
        });
    </script>
</div>