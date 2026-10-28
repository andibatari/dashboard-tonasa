<div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Dropdown Provinsi dengan Pencarian dan Tambah --}}
        <div>
            <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
            <div x-data="{ open: @entangle('isProvinsiDropdownOpen') }" @click.away="open = false" class="relative">
                <button type="button" @click="$wire.toggleProvinsiDropdown()" class="flex justify-between items-center w-full px-4 py-2 border rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 bg-white text-left">
                    <span>{{ $selectedProvinsiName ?: 'Pilih Provinsi' }}</span>
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg" x-cloak>
                    <div class="p-2 border-b flex items-center">
                        <input type="text" wire:model.live="searchProvinsi" placeholder="Cari Provinsi..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <button wire:click.prevent="toggleAddProvinsi" class="bg-gray-200 hover:bg-gray-300 text-gray-700 p-2 rounded-md ml-2 transition-colors duration-200">
                             <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </button>
                    </div>

                    @if ($isAddingNewProvinsi)
                        <div class="p-2 flex items-center border-b">
                            <input type="text" wire:model.live="newProvinsiName" placeholder="Nama Provinsi Baru" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <button wire:click.prevent="addNewProvinsi" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 ml-2 rounded-md text-sm">Simpan</button>
                        </div>
                    @endif
                    <ul class="py-1 max-h-60 overflow-y-auto">
                        @foreach($provinsis as $provinsi)
                            <li wire:click="selectProvinsi({{ $provinsi->id }}, '{{ $provinsi->nama_provinsi }}')" class="cursor-pointer hover:bg-gray-100 p-2">
                                {{ $provinsi->nama_provinsi }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @error('selectedProvinsi') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Dropdown Kabupaten dengan Pencarian dan Tambah --}}
        <div>
                <label for="kabupaten" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
                <div x-data="{ open: @entangle('isKabupatenDropdownOpen') }" @click.away="open = false" class="relative">
                    <button type="button" @click="$wire.toggleKabupatenDropdown()" class="flex justify-between items-center w-full px-4 py-2 border rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 bg-white text-left" {{ is_null($selectedProvinsi) ? 'disabled' : '' }}>
                        <span>{{ $selectedKabupatenName ?: 'Pilih Kabupaten' }}</span>
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg" x-cloak>
                        <div class="p-2 border-b flex items-center">
                            <input type="text" wire:model.live="searchKabupaten" placeholder="Cari Kabupaten..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <button wire:click.prevent="toggleAddKabupaten" class="bg-gray-200 hover:bg-gray-300 text-gray-700 p-2 rounded-md ml-2 transition-colors duration-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </button>
                        </div>

                        @if ($isAddingNewKabupaten)
                            <div class="p-2 flex items-center border-b">
                                <input type="text" wire:model.live="newKabupatenName" placeholder="Nama Kabupaten Baru" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <button wire:click.prevent="addNewKabupaten" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 ml-2 rounded-md text-sm">Simpan</button>
                            </div>
                        @endif
                        <ul class="py-1 max-h-60 overflow-y-auto">
                            @foreach($kabupatens as $kabupaten)
                                <li wire:click="selectKabupaten({{ $kabupaten->id }}, '{{ $kabupaten->nama_kabupaten }}')" class="cursor-pointer hover:bg-gray-100 p-2">
                                    {{ $kabupaten->nama_kabupaten }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @error('selectedKabupaten') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Kolom Kecamatan dengan Pencarian dan Tambah --}}
        <div>
            <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
            <div x-data="{ open: @entangle('isKecamatanDropdownOpen') }" @click.away="open = false" class="relative">
                <button type="button" @click="$wire.toggleKecamatanDropdown()" class="flex justify-between items-center w-full px-4 py-2 border rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 bg-white text-left" {{ is_null($selectedKabupaten) ? 'disabled' : '' }}>
                    <span>{{ $selectedKecamatanName ?: 'Pilih Kecamatan' }}</span>
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg" x-cloak>
                    <div class="p-2 border-b flex items-center">
                        <input type="text" wire:model.live="searchKecamatan" placeholder="Cari Kecamatan..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <button wire:click.prevent="toggleAddKecamatan" class="bg-gray-200 hover:bg-gray-300 text-gray-700 p-2 rounded-md ml-2 transition-colors duration-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </button>
                    </div>

                    @if ($isAddingNewKecamatan)
                        <div class="p-2 flex items-center border-b">
                            <input type="text" wire:model.live="newKecamatanName" placeholder="Nama Kecamatan Baru" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <button wire:click.prevent="addNewKecamatan" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 ml-2 rounded-md text-sm">Simpan</button>
                        </div>
                    @endif
                    <ul class="py-1 max-h-60 overflow-y-auto">
                        @foreach($kecamatans as $kecamatan)
                            <li wire:click="selectKecamatan({{ $kecamatan->id }}, '{{ $kecamatan->nama_kecamatan }}')" class="cursor-pointer hover:bg-gray-100 p-2">
                                {{ $kecamatan->nama_kecamatan }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @error('kecamatan_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        
    </div>
</div>
