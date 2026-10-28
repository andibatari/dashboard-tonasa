{{-- 
    wire:poll.10s: Menginstruksikan Livewire untuk mengirim request ke server setiap 10 detik.
    Fungsinya agar angka notifikasi di database terpantau secara real-time tanpa perlu me-refresh halaman.
--}}
<div x-data="{ open: false }" wire:poll.10s="checkNotifications" class="relative">
    
    {{-- Tombol Lonceng: Menggunakan rounded-full untuk bentuk sirkular --}}
    <button @click="open = !open" 
            class="p-2 rounded-full text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors duration-150 relative focus:outline-none">
        
        <i class="fas fa-bell text-lg md:text-xl"></i>
        
        {{-- Badge Notifikasi: Menampilkan jumlah notifikasi yang belum dibaca --}}
        @if ($unreadNotificationsCount > 0)
            <span class="absolute top-0 right-0 h-4 w-4 md:h-5 md:w-5 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center -mt-1 -mr-1 shadow-md animate-pulse border-2 border-white">
                {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
            </span>
        @endif
    </button>

    {{-- 
        Panel Dropdown Notifikasi:
        - w-[280px] md:w-80: Mengunci lebar absolut agar tidak tumpah.
        - -right-2 md:right-0: Menggeser posisi panel sedikit ke kanan pada mobile agar seimbang dengan padding layar.
        - z-50: Memastikan panel muncul paling depan.
    --}}
    <div x-show="open" 
         @click.away="open = false; $wire.markNotificationsAsRead()" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute -right-2 md:right-0 mt-3 w-[280px] md:w-80 bg-white rounded-2xl shadow-[0_10px_40px_rgb(0,0,0,0.15)] overflow-hidden z-50 border border-slate-100 origin-top-right"
         style="display: none;">
         
         {{-- Header Dropdown --}}
         <div class="p-3 border-b border-slate-100 bg-white flex justify-between items-center">
             <h4 class="text-xs font-black text-slate-800">Notifikasi Terbaru</h4>
             @if ($unreadNotificationsCount > 0)
                 <span class="bg-red-100 text-red-500 text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider">Baru</span>
             @endif
         </div>
         
        {{-- List Notifikasi: max-h-64 membatasi tinggi agar tidak memenuhi layar HP --}}
        <div class="max-h-64 overflow-y-auto custom-scrollbar">
            
            @if (count($latestNotifications) > 0)
                <div class="flex flex-col divide-y divide-slate-50">
                    @foreach ($latestNotifications as $notif)
                        <a href="{{ route('jadwal.index') }}?search_sap={{ $notif->karyawan->no_sap ?? '' }}" 
                           class="p-3 transition-all duration-300 flex items-start gap-2 group cursor-pointer {{ $notif->is_read_admin ? 'bg-white opacity-60' : 'bg-red-50/50' }}">
                            
                            {{-- Ikon bulat notifikasi --}}
                            <div class="w-8 h-8 rounded-full bg-red-100 text-red-500 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                <i class="fas fa-calendar-check text-[10px]"></i>
                            </div>
                            
                            {{-- Teks notifikasi --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] text-slate-600 leading-snug truncate-2-lines">
                                    <span class="font-black text-slate-800">{{ $notif->karyawan->nama_karyawan ?? 'Karyawan' }}</span> 
                                    mengambil antrean MCU tanggal {{ \Carbon\Carbon::parse($notif->tanggal_mcu)->format('d/m/y') }}.
                                </p>
                                <p class="text-[9px] text-slate-400 font-bold mt-1">
                                    <i class="far fa-clock mr-1"></i> {{ $notif->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center flex flex-col items-center justify-center bg-slate-50">
                    <i class="fas fa-bell-slash text-2xl text-slate-200 mb-2"></i>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tidak ada notifikasi</p>
                </div>
            @endif
        </div>

        {{-- Footer Dropdown --}}
        <div class="p-2.5 border-t border-slate-100 bg-slate-50 text-center hover:bg-slate-100 transition-colors">
            <a href="{{ route('jadwal.index') }}" class="block text-[10px] font-black text-slate-500 hover:text-red-600 uppercase tracking-wider">
                Buka Manajemen Jadwal &rarr;
            </a>
        </div>
    </div>
</div>