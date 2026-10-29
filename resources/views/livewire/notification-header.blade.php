{{-- 
    Notification Bell Header (Terpadu: STMC Health & KMST Inovasi)
    Polling 10s untuk update status real-time
--}}
<div x-data="{ open: false, notifTab: 'all' }" wire:poll.10s="checkNotifications" class="relative">
    
    {{-- Tombol Lonceng --}}
    <button @click="open = !open" 
            class="p-1.5 sm:p-2 rounded-full text-gray-500 dark:text-gray-400 hover:text-[#F5333F] hover:bg-red-50 dark:hover:bg-gray-700/60 transition-colors relative focus:outline-none"
            aria-label="Notifikasi Sistem">
        
        <i class="fas fa-bell text-base sm:text-lg"></i>
        
        {{-- Badge Notifikasi Merah --}}
        @if ($unreadNotificationsCount > 0)
            <span class="absolute top-0 right-0 h-4 w-4 bg-[#F5333F] text-white text-[9px] font-black rounded-full flex items-center justify-center -mt-0.5 -mr-0.5 shadow-md animate-pulse border-2 border-white dark:border-gray-800">
                {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
            </span>
        @endif
    </button>

    {{-- Panel Dropdown Notifikasi --}}
    <div x-show="open" 
         @click.away="open = false; $wire.markNotificationsAsRead()" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-3 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden z-50 border border-gray-100 dark:border-gray-700 origin-top-right"
         style="display: none;">
         
         {{-- Header Dropdown --}}
         <div class="p-3.5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/80">
             <div class="flex items-center gap-2">
                 <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-wider">Notifikasi Terpadu</span>
                 @if ($unreadNotificationsCount > 0)
                     <span class="bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400 text-[10px] font-black px-2 py-0.5 rounded-full">
                         {{ $unreadNotificationsCount }} Baru
                     </span>
                 @endif
             </div>
             <button @click="open = false; $wire.markNotificationsAsRead()" class="text-[11px] font-semibold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                 Tandai Dibaca
             </button>
         </div>

         {{-- Tab Selector (MCU vs KMST) --}}
         <div class="flex border-b border-gray-100 dark:border-gray-700 text-xs font-bold bg-white dark:bg-gray-800">
             <button @click="notifTab = 'all'" 
                     :class="notifTab === 'all' ? 'text-[#F5333F] border-b-2 border-[#F5333F] bg-red-50/30 dark:bg-red-500/10' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400'"
                     class="flex-1 py-2 text-center transition-colors">
                 Semua
             </button>
             <button @click="notifTab = 'mcu'" 
                     :class="notifTab === 'mcu' ? 'text-[#F5333F] border-b-2 border-[#F5333F] bg-red-50/30 dark:bg-red-500/10' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400'"
                     class="flex-1 py-2 text-center transition-colors">
                 🩺 Klinik MCU
             </button>
             <button @click="notifTab = 'kmst'" 
                     :class="notifTab === 'kmst' ? 'text-[#F5333F] border-b-2 border-[#F5333F] bg-red-50/30 dark:bg-red-500/10' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400'"
                     class="flex-1 py-2 text-center transition-colors">
                 💡 KMST Inovasi
             </button>
         </div>
         
        {{-- List Notifikasi --}}
        <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/60">
            
            {{-- Bagian MCU --}}
            <template x-if="notifTab === 'all' || notifTab === 'mcu'">
                <div>
                    @forelse ($latestMcuNotifications as $notif)
                        <a href="{{ route('jadwal.index') }}?search_sap={{ $notif->karyawan->no_sap ?? '' }}" 
                           class="p-3 transition-colors flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/40 {{ $notif->is_read_admin ? 'opacity-70 bg-white dark:bg-gray-800' : 'bg-red-50/40 dark:bg-red-500/5' }}">
                            <div class="w-8 h-8 rounded-full bg-red-100 text-[#F5333F] dark:bg-red-500/20 dark:text-red-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fas fa-stethoscope text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[10px] font-bold text-red-600 dark:text-red-400 uppercase">Jadwal MCU</span>
                                    <span class="text-[9px] text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-700 dark:text-gray-200 mt-0.5 leading-snug">
                                    <strong class="font-bold">{{ $notif->karyawan->nama_karyawan ?? $notif->pesertaMcu->nama_lengkap ?? 'Pasien' }}</strong> mendaftar MCU tgl {{ \Carbon\Carbon::parse($notif->tanggal_mcu)->format('d M Y') }}.
                                </p>
                            </div>
                        </a>
                    @empty
                        <template x-if="notifTab === 'mcu'">
                            <div class="p-6 text-center text-xs text-gray-400">Belum ada antrean MCU baru</div>
                        </template>
                    @endforelse
                </div>
            </template>

            {{-- Bagian KMST Inovasi --}}
            <template x-if="notifTab === 'all' || notifTab === 'kmst'">
                <div>
                    @forelse ($latestKmstNotifications as $kmst)
                        <a href="{{ route('inovasi.portal') }}" 
                           class="p-3 transition-colors flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/40 bg-white dark:bg-gray-800">
                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fas fa-lightbulb text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase">KMST Inovasi</span>
                                    <span class="text-[9px] text-gray-400">{{ $kmst->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-700 dark:text-gray-200 mt-0.5 leading-snug">
                                    Tim <strong class="font-bold">{{ $kmst->nama_tim }}</strong> mendaftar kategori <span class="uppercase font-semibold text-gray-900 dark:text-white">[{{ $kmst->kategori }}]</span>.
                                </p>
                            </div>
                        </a>
                    @empty
                        <template x-if="notifTab === 'kmst'">
                            <div class="p-6 text-center text-xs text-gray-400">Belum ada pendaftaran inovasi baru</div>
                        </template>
                    @endforelse
                </div>
            </template>

            @if(count($latestMcuNotifications) === 0 && count($latestKmstNotifications) === 0)
                <div class="p-8 text-center flex flex-col items-center justify-center">
                    <i class="fas fa-bell-slash text-2xl text-gray-300 dark:text-gray-600 mb-2"></i>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tidak ada notifikasi baru</p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="p-2.5 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 text-center flex justify-between px-4">
            <a href="{{ route('jadwal.index') }}" class="text-[11px] font-bold text-gray-500 dark:text-gray-400 hover:text-[#F5333F]">
                Jadwal MCU &rarr;
            </a>
            <a href="{{ route('activity-logs.index') }}" class="text-[11px] font-bold text-gray-500 dark:text-gray-400 hover:text-[#F5333F]">
                Log Aktivitas &rarr;
            </a>
        </div>
    </div>
</div>
