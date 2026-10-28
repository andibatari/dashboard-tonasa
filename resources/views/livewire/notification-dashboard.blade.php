{{-- Padding utama diperkecil menjadi p-3 md:p-6 agar konten langsung mendominasi layar HP tanpa scroll kosong --}}
<div x-data="{ activeTab: 'broadcast' }" class="max-w-7xl mx-auto px-3 md:px-6 py-4 md:py-6">
    @section('title', 'Kontrol Notifikasi')

    <div class="mb-4 flex flex-col sm:flex-row gap-1.5 bg-white p-1.5 rounded-xl shadow-sm border border-slate-100">
        <button @click="activeTab = 'broadcast'" :class="activeTab === 'broadcast' ? 'bg-red-50 text-red-700 font-black shadow-sm' : 'text-slate-500 hover:bg-slate-50 font-bold'" class="flex-1 py-2.5 px-3 rounded-lg transition-colors flex items-center justify-center text-xs">
            <i class="fa-solid fa-bullhorn mr-1.5" :class="activeTab === 'broadcast' ? 'text-red-500' : 'text-slate-400'"></i> Broadcast
        </button>
        <button @click="activeTab = 'pengingat'" :class="activeTab === 'pengingat' ? 'bg-blue-50 text-blue-700 font-black shadow-sm' : 'text-slate-500 hover:bg-slate-50 font-bold'" class="flex-1 py-2.5 px-3 rounded-lg transition-colors flex items-center justify-center text-xs">
            <i class="fa-solid fa-calendar-check mr-1.5" :class="activeTab === 'pengingat' ? 'text-blue-500' : 'text-slate-400'"></i> Pengingat Manual
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 px-4 py-2.5 rounded-r-lg shadow-sm mb-4 text-xs font-bold"><i class="fas fa-check-circle mr-1 text-emerald-500"></i> {{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 px-4 py-2.5 rounded-r-lg shadow-sm mb-4 text-xs font-bold"><i class="fas fa-exclamation-triangle mr-1 text-rose-500"></i> {{ session('error') }}</div>
    @endif

    {{-- TAB 1: BROADCAST --}}
    <div x-show="activeTab === 'broadcast'" x-transition class="bg-white rounded-xl shadow-sm p-4 md:p-6 border border-slate-100">
        <h3 class="text-base font-black mb-1 flex items-center text-slate-800"><i class="fas fa-bullhorn text-red-500 mr-2"></i> Pengumuman Massal</h3>
        <p class="text-[10px] md:text-xs text-slate-500 mb-4 border-b border-slate-100 pb-3">Kirim notifikasi langsung ke layar HP karyawan.</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
            <div class="space-y-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Judul Pengumuman</label>
                    <input type="text" wire:model.defer="broadcastTitle" class="w-full bg-slate-50 border-slate-200 rounded-lg focus:bg-white focus:border-red-500 focus:ring-red-500 shadow-sm p-2 text-xs font-bold">
                    @error('broadcastTitle') <span class="text-[10px] font-bold text-rose-500 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Isi Pesan</label>
                    <textarea wire:model.defer="broadcastMessage" rows="3" class="w-full bg-slate-50 border-slate-200 rounded-lg focus:bg-white focus:border-red-500 focus:ring-red-500 shadow-sm p-2 text-xs resize-none"></textarea>
                    @error('broadcastMessage') <span class="text-[10px] font-bold text-rose-500 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Tautan / Link Lampiran (Opsional)</label>
                    <input type="url" wire:model.defer="broadcastLink" placeholder="Cth: https://forms.gle/..." class="w-full bg-slate-50 border-slate-200 rounded-lg focus:bg-white focus:border-red-500 focus:ring-red-500 shadow-sm p-2 text-xs font-medium">
                    @error('broadcastLink') <span class="text-[10px] font-bold text-rose-500 block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Target Penerima</label>
                    <select wire:model.live="broadcastTargetType" class="w-full bg-white border-slate-200 rounded-lg focus:border-red-500 focus:ring-red-500 shadow-sm p-2 text-xs font-bold cursor-pointer">
                        <option value="all">Semua Karyawan</option>
                        <option value="dept">Departemen Tertentu</option>
                        <option value="individual">Karyawan Spesifik (Manual)</option>
                    </select>
                </div>
                
                @if($broadcastTargetType === 'dept')
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Pilih Departemen</label>
                    <select wire:model.defer="broadcastTargetDeptId" class="w-full bg-white border-slate-200 rounded-lg focus:border-red-500 focus:ring-red-500 shadow-sm p-2 text-xs font-bold cursor-pointer">
                        <option value="">-- Silakan Pilih --</option>
                        @foreach($departemenOptions as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                    </select>
                </div>
                @endif
                
                @if($broadcastTargetType === 'individual')
                <div class="relative z-20">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Cari Karyawan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none"><i class="fas fa-search text-slate-400 text-[10px]"></i></div>
                        <input type="text" wire:model.live.debounce.300ms="searchEmployeeQuery" placeholder="Ketik Nama/SAP..." class="w-full pl-8 pr-8 bg-white border-slate-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 shadow-sm p-2 text-xs">
                        <div wire:loading wire:target="searchEmployeeQuery" class="absolute inset-y-0 right-0 pr-2.5 flex items-center"><i class="fas fa-circle-notch fa-spin text-blue-500 text-[10px]"></i></div>
                    </div>

                    @if(!empty($searchEmployeeQuery) && !empty($employeeSearchResults))
                    <ul class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-40 rounded-lg border border-slate-100 overflow-auto">
                        @foreach($employeeSearchResults as $emp)
                        <li wire:click="addEmployeeToBroadcast('{{ $emp['id'] }}', '{{ addslashes($emp['name']) }}', '{{ $emp['sap'] }}')" class="cursor-pointer hover:bg-blue-50 px-3 py-2 border-b border-slate-50 text-xs">
                            <div class="font-bold text-slate-800">{{ $emp['name'] }}</div>
                            <div class="text-[9px] text-slate-400 mt-0.5">SAP/NIK: {{ $emp['sap'] }}</div>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    @if(count($selectedIndividualEmployees) > 0)
                    <div class="mt-2 border border-blue-100 bg-blue-50 p-2 rounded-lg">
                        <div class="flex flex-wrap gap-1">
                            @foreach($selectedIndividualEmployees as $selected)
                            <span class="inline-flex items-center bg-white border border-blue-200 text-slate-700 text-[10px] font-bold px-2 py-1 rounded shadow-sm">
                                {{ $selected['name'] }}
                                <button type="button" wire:click="removeEmployeeFromBroadcast('{{ $selected['id'] }}')" class="ml-1.5 text-rose-500 hover:text-rose-700"><i class="fas fa-times"></i></button>
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                
                <div class="pt-3">
                    <button wire:click="sendBroadcast" wire:loading.attr="disabled" class="w-full flex items-center justify-center px-4 py-2.5 bg-red-600 rounded-lg font-bold text-white hover:bg-red-700 transition-all shadow-md active:scale-95 disabled:opacity-50 text-xs">
                        <span wire:loading.remove wire:target="sendBroadcast"><i class="fas fa-paper-plane mr-1.5"></i> Kirim Notifikasi</span>
                        <span wire:loading wire:target="sendBroadcast"><i class="fas fa-circle-notch fa-spin mr-1.5"></i> Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: PENGINGAT MCU MANUAL --}}
    <div x-show="activeTab === 'pengingat'" style="display: none;" x-transition class="bg-white rounded-xl shadow-sm p-4 md:p-6 border border-slate-100">
        <h3 class="text-base font-black mb-1 flex items-center text-slate-800"><i class="fas fa-calendar-check text-blue-500 mr-2"></i> Pengingat Manual</h3>
        
        <div class="bg-slate-50 p-3 md:p-4 rounded-xl border border-slate-100 mb-4 mt-3">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Mode Notifikasi</label>
                    <select wire:model.live="notificationMode" class="block w-full bg-white border-slate-200 rounded-lg shadow-sm p-2 text-xs font-bold cursor-pointer focus:border-blue-500 focus:ring-blue-500">
                        <option value="scheduled">Sdh Punya Jadwal</option>
                        <option value="submission">Blm Punya Jadwal</option>
                    </select>
                </div>
                
                @if ($notificationMode === 'scheduled')
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Target Tanggal</label>
                        <select wire:model.live="filterDate" class="block w-full bg-white border-slate-200 rounded-lg shadow-sm p-2 text-xs font-bold cursor-pointer">
                            <option value="today">Hari Ini</option>
                            <option value="tomorrow">Besok</option>
                            <option value="specific">Tanggal Lain</option>
                        </select>
                    </div>
                    @if ($filterDate === 'specific')
                        <div class="col-span-1">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Pilih Tgl</label>
                            <input type="date" wire:model.live="specificDate" class="block w-full bg-white border-slate-200 rounded-lg shadow-sm p-2 text-xs font-bold">
                        </div>
                    @else
                        <div class="col-span-1 hidden md:block"></div>
                    @endif
                    <div class="col-span-1 hidden md:block"></div>
                @elseif ($notificationMode === 'submission')
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Departemen</label>
                        <select wire:model.live="filterDepartemenId" class="block w-full bg-white border-slate-200 rounded-lg shadow-sm p-2 text-xs font-bold cursor-pointer">
                            <option value="">Semua Dept</option>
                            @foreach ($departemenOptions as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                        </select>
                    </div>
                    
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Unit Kerja</label>
                        <select wire:model.live="filterUnitKerjaId" class="block w-full bg-white border-slate-200 rounded-lg shadow-sm p-2 text-xs font-bold cursor-pointer">
                            <option value="">Semua Unit</option>
                            @if($unitKerjaOptions)
                                @foreach ($unitKerjaOptions as $unit) 
                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option> 
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Batas Akhir</label>
                        <input type="date" wire:model.live="specificDate" class="block w-full bg-white border-slate-200 rounded-lg shadow-sm p-2 text-xs font-bold">
                    </div>
                @endif
                
                <div class="col-span-2 md:col-span-1 md:text-right pt-1">
                    <button wire:click="sendNotifications" wire:loading.attr="disabled" class="w-full px-4 py-2 bg-blue-600 rounded-lg font-bold text-xs text-white hover:bg-blue-700 shadow-md disabled:opacity-50">
                        Kirim ({{ count(array_filter($selectedRecipients)) }})
                    </button>
                </div>
            </div>
        </div>

        <div class="relative mb-3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-search text-slate-400 text-[10px]"></i></div>
            <input type="text" wire:model.live="searchQuery" placeholder="Cari Nama/SAP..." class="block w-full pl-8 py-2 text-xs bg-slate-50 border-slate-200 rounded-lg focus:bg-white focus:border-blue-500 font-medium">
        </div>
        
        <div class="overflow-x-auto border border-slate-200 rounded-xl max-h-80 hide-scrollbar">
            <table class="min-w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-2 text-center w-10">
                            {{-- 🌟 PERBAIKAN: Gunakan wire:model.live untuk checkbox Master --}}
                            <input type="checkbox" 
                                wire:model.live="selectAll"
                                @if($jadwalsToNotify->isEmpty()) disabled @endif
                                class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-3.5 h-3.5 cursor-pointer disabled:opacity-50">
                        </th>
                        <th class="px-3 py-2 text-[9px] font-bold text-slate-500 uppercase tracking-wider">Karyawan</th>
                        <th class="px-3 py-2 text-[9px] font-bold text-slate-500 uppercase tracking-wider">Departemen</th>
                        <th class="px-3 py-2 text-[9px] font-bold text-slate-500 uppercase tracking-wider">SAP / NIK</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($jadwalsToNotify as $data)
                        <tr wire:key="row-{{ $data->id }}" class="hover:bg-slate-50">
                            <td class="px-3 py-2 text-center">
                                <input type="checkbox" 
                                    wire:model.live="selectedRecipients" 
                                    value="{{ $data->id }}" 
                                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-3.5 h-3.5 cursor-pointer">
                            </td>
                            <td class="px-3 py-2 text-xs font-bold text-slate-800">
                                {{ $notificationMode === 'scheduled' ? ($data->karyawan->nama_karyawan ?? $data->nama_pasien ?? 'N/A') : $data->nama_karyawan }}
                            </td>
                            <td class="px-3 py-2 text-[10px] font-bold text-slate-500">
                                {{ $notificationMode === 'scheduled' ? ($data->karyawan->departemen->nama_departemen ?? 'N/A') : ($data->departemen->nama_departemen ?? 'N/A') }}
                            </td>
                            <td class="px-3 py-2 text-[10px] font-mono">
                                @php
                                    $sapVal = $notificationMode === 'scheduled' ? ($data->no_sap ?? $data->karyawan->no_sap ?? '-') : ($data->no_sap ?? '-');
                                    $nikVal = $notificationMode === 'scheduled' ? ($data->karyawan->nik_karyawan ?? '-') : ($data->nik_karyawan ?? '-');
                                @endphp
                                <div class="font-bold text-slate-700">
                                    {{ $sapVal !== '-' ? $sapVal : $nikVal }}
                                </div>
                                <div class="text-[9px] text-slate-400 mt-0.5">
                                    {{ $sapVal !== '-' ? 'NIK: ' . $nikVal : 'SAP: -' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400 text-xs font-medium">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>