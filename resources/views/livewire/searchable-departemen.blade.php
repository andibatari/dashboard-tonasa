{{-- 
  Menggunakan grid-cols-2 secara absolut agar di layar sekecil apapun (HP/Desktop), 
  dropdown Departemen dan Unit Kerja tetap berada dalam satu baris (berdampingan).
  Gap diperkecil menjadi gap-2 agar lebih rapat dan hemat ruang. 
--}}
<div class="grid grid-cols-2 gap-2 mb-3">
    
    {{-- Dropdown Departemen (Custom Dropdown Alpine.js) --}}
    <div x-data="{ open: @entangle('isDepartemenDropdownOpen') }" @click.away="open = false" class="relative">
        <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Departemen</label>
        
        {{-- Tombol pemicu dropdown custom. Menampilkan teks opsi terpilih atau placeholder --}}
        <button type="button" @click="$wire.toggleDepartemenDropdown()" class="flex justify-between items-center w-full rounded-lg border-slate-200 shadow-sm focus:border-red-500 focus:ring-red-500 p-2 text-left bg-white border text-xs">
            <span class="text-slate-800 font-bold truncate">{{ $selectedDepartemenName ?: 'Pilih Dept' }}</span>
            <svg class="h-4 w-4 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </button>

        {{-- Panel isi dropdown yang muncul saat state 'open' bernilai true --}}
        <div x-show="open" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden" x-cloak>
            
            {{-- Input pencarian Livewire. Debounce tidak mutlak diperlukan jika dataset kecil --}}
            <div class="p-1.5 border-b border-slate-100 flex items-center bg-slate-50">
                <input type="text" wire:model.live="searchDepartemen" placeholder="Cari..." class="block w-full rounded-md border-slate-200 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs py-1.5 px-2">
                <button wire:click.prevent="toggleAddDepartemen" class="bg-slate-200 hover:bg-slate-300 text-slate-700 p-1.5 rounded-md ml-1.5 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </button>
            </div>

            {{-- Form penambahan departemen baru secara dinamis, muncul jika toggle bernilai true --}}
            @if ($isAddingNewDepartemen)
                <div class="p-1.5 flex items-center border-b border-slate-100 bg-red-50">
                    <input type="text" wire:model.live="newDepartemenName" placeholder="Nama Baru" class="block w-full rounded-md border-red-200 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs py-1.5 px-2">
                    <button wire:click.prevent="addNewDepartemen" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1.5 px-2 ml-1.5 rounded-md text-[10px]">Save</button>
                </div>
            @endif

            {{-- Daftar opsi yang dirender dari array/collection --}}
            <ul class="py-1 max-h-48 overflow-y-auto">
                @foreach($departemens as $departemen)
                    <li wire:click="selectDepartemen({{ $departemen->id }}, '{{ addslashes($departemen->nama_departemen) }}')" class="cursor-pointer hover:bg-red-50 hover:text-red-600 px-3 py-2 text-xs font-medium text-slate-700 border-b border-slate-50 last:border-0 transition-colors">
                        {{ $departemen->nama_departemen }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    
    {{-- Dropdown Unit Kerja (Sama fungsinya seperti di atas, namun memiliki dependensi terhadap Departemen) --}}
    <div x-data="{ open: @entangle('isUnitKerjaDropdownOpen') }" @click.away="open = false" class="relative">
        <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Unit Kerja</label>
        
        {{-- Tombol akan di-disable jika ID Departemen belum dipilih --}}
        <button type="button" @click="$wire.toggleUnitKerjaDropdown()" class="flex justify-between items-center w-full rounded-lg border-slate-200 shadow-sm focus:border-red-500 focus:ring-red-500 p-2 text-left bg-white border text-xs disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed" @if(!$selectedDepartemenId) disabled @endif>
            <span class="text-slate-800 font-bold truncate">{{ $selectedUnitKerjaName ?: 'Pilih Unit' }}</span>
            <svg class="h-4 w-4 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </button>

        <div x-show="open" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden" x-cloak>
            <div class="p-1.5 border-b border-slate-100 flex items-center bg-slate-50">
                <input type="text" wire:model.live="searchUnitKerja" placeholder="Cari..." class="block w-full rounded-md border-slate-200 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs py-1.5 px-2">
                <button wire:click.prevent="$set('isAddingNewUnitKerja', true)" class="bg-slate-200 hover:bg-slate-300 text-slate-700 p-1.5 rounded-md ml-1.5 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </button>
            </div>
            
            @if ($isAddingNewUnitKerja)
            <div class="p-1.5 flex items-center border-b border-slate-100 bg-red-50">
                <input type="text" wire:model.live="newUnitKerjaName" placeholder="Nama Baru" class="block w-full rounded-md border-red-200 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs py-1.5 px-2">
                <button wire:click.prevent="addNewUnitKerja" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1.5 px-2 ml-1.5 rounded-md text-[10px]">Save</button>
            </div>
            @endif
            
            <ul class="py-1 max-h-48 overflow-y-auto">
                @foreach($unitKerjas as $unitKerja)
                    <li wire:click="selectUnitKerja({{ $unitKerja->id }}, '{{ addslashes($unitKerja->nama_unit_kerja) }}')" class="cursor-pointer hover:bg-red-50 hover:text-red-600 px-3 py-2 text-xs font-medium text-slate-700 border-b border-slate-50 last:border-0 transition-colors">
                        {{ $unitKerja->nama_unit_kerja }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>