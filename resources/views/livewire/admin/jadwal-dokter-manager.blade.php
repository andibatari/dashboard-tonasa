<div class="p-4 sm:p-6 md:p-5 bg-white rounded-2xl md:rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100" 
     x-data="{ showModal: false }" 
     @open-modal.window="showModal = true">
     
    {{-- Header Premium (Responsive) --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Jadwal Praktik</h2>
            <p class="text-xs md:text-sm font-medium text-slate-500 mt-1">Manajemen jadwal operasional dokter STMC</p>
        </div>
        <div class="inline-flex items-center gap-2 px-4 py-2 md:px-5 md:py-2.5 bg-red-50 text-red-600 rounded-xl md:rounded-2xl text-xs md:text-sm font-bold border border-red-100 shadow-sm w-full md:w-auto justify-center">
            <i class="fas fa-stethoscope"></i> {{ date('F Y') }}
        </div>
    </div>

    {{-- Kalender Wrapper (Bisa di-scroll horizontal jika layarnya sangat sempit) --}}
    <div wire:ignore class="calendar-wrapper premium-calendar w-full overflow-x-auto pb-2">
        <div id="calendar" class="min-w-[500px] sm:min-w-full"></div>
    </div>

    {{-- Modal Premium (Responsive) --}}
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md" 
         style="display: none;">
        
        <div class="bg-white p-6 md:p-8 rounded-[2rem] w-full max-w-sm shadow-2xl border border-white transform transition-all max-h-[90vh] overflow-y-auto">
            <div class="w-12 h-12 md:w-14 md:h-14 bg-red-50 rounded-2xl flex items-center justify-center mb-5 md:mb-6 border border-red-100">
                <i class="fas fa-calendar-plus text-red-500 text-xl md:text-2xl"></i>
            </div>
            
            <h3 class="font-black text-xl md:text-2xl text-slate-800 mb-1">Set Jadwal</h3>
            <p class="text-xs md:text-sm text-slate-500 mb-5 md:mb-6">Pilih dokter yang bertugas untuk tanggal ini.</p>

            <label class="block text-[10px] md:text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 pl-1">Pilih Dokter</label>
            <select wire:model.live="dokter_id" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl md:rounded-2xl p-3 md:p-4 mb-4 focus:border-red-500 focus:bg-white focus:ring-0 transition-all font-bold text-slate-700 cursor-pointer appearance-none text-sm">
                <option value="">-- Silakan Pilih --</option>
                @foreach($dokters as $dokter)
                    <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap }}</option>
                @endforeach
            </select>

            <label class="block text-[10px] md:text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 pl-1">Warna Dokter</label>
            <div class="flex items-center gap-3 bg-slate-50 border-2 border-slate-100 rounded-xl md:rounded-2xl p-2 md:p-3 mb-6 md:mb-8">
                <input type="color" wire:model="color" class="w-10 h-8 md:w-12 md:h-10 rounded-lg md:rounded-xl cursor-pointer border-0 bg-transparent p-0">
                <span class="text-[10px] md:text-xs text-slate-500 font-medium leading-tight">Warna ini akan disimpan secara permanen.</span>
            </div>

            <div class="flex gap-2 md:gap-3">
                <button @click="showModal = false" class="flex-1 px-3 py-3 md:px-4 md:py-4 font-bold text-slate-600 bg-slate-100 rounded-xl md:rounded-2xl hover:bg-slate-200 transition-colors text-xs md:text-sm">Kembali</button>
                <button wire:click="saveJadwal" @click="showModal = false" class="flex-1 px-3 py-3 md:px-4 md:py-4 font-bold text-white bg-red-600 rounded-xl md:rounded-2xl shadow-lg shadow-red-500/30 hover:bg-red-700 hover:shadow-red-500/40 transition-all text-xs md:text-sm transform active:scale-95">Simpan</button>
            </div>
        </div>
    </div>

    {{-- CSS Penimpa Gaya FullCalendar & Responsivitas --}}
    <style>
        .premium-calendar { font-family: 'Inter', sans-serif; }
        .premium-calendar .fc-theme-standard td, 
        .premium-calendar .fc-theme-standard th, 
        .premium-calendar .fc-theme-standard .fc-scrollgrid { border-color: #f1f5f9 !important; }
        
        .premium-calendar .fc-toolbar-title {
            font-weight: 900 !important; color: #0f172a !important; font-size: 1.5rem !important; letter-spacing: -0.025em;
        }
        .premium-calendar .fc-button-primary {
            background-color: #fff !important; color: #475569 !important; border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important; padding: 8px 16px !important; font-weight: 700 !important;
            text-transform: capitalize !important; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important; transition: all 0.2s;
        }
        .premium-calendar .fc-button-primary:hover { background-color: #f8fafc !important; color: #0f172a !important; border-color: #cbd5e1 !important; }
        .premium-calendar .fc-button-primary:disabled { opacity: 0.5; }
        .premium-calendar .fc-button-active { background-color: #f1f5f9 !important; color: #0f172a !important; }
        
        .premium-calendar .fc-col-header-cell-cushion {
            font-weight: 700 !important; color: #64748b !important; text-transform: uppercase; font-size: 0.75rem; padding: 12px 0 !important; letter-spacing: 0.05em;
        }
        .premium-calendar .fc-daygrid-day-number { font-weight: 700 !important; color: #334155 !important; padding: 8px 12px !important; font-size: 0.875rem; }
        .premium-calendar .fc-day-today { background-color: #fff1f2 !important; }
        .premium-calendar .fc-daygrid-day:hover { background-color: #f8fafc; cursor: pointer; transition: background-color 0.2s; }
        
        .premium-calendar .fc-event {
            border: none !important; border-radius: 6px !important; padding: 4px 8px !important; margin: 2px 4px !important;
            font-weight: 700 !important; font-size: 0.75rem !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important; transition: transform 0.1s;
        }
        .premium-calendar .fc-event:hover { transform: scale(1.02); opacity: 0.9; }
        .premium-calendar .fc-h-event .fc-event-main { color: #fff !important; overflow: hidden; text-overflow: ellipsis; }

        /* ======== MOBILE RESPONSIVE TWEAKS ======== */
        @media (max-width: 640px) {
            .premium-calendar .fc-toolbar { flex-direction: column; gap: 12px; }
            .premium-calendar .fc-toolbar-title { font-size: 1.25rem !important; }
            .premium-calendar .fc-button-primary { padding: 6px 12px !important; font-size: 0.75rem !important; border-radius: 8px !important; }
            .premium-calendar .fc-col-header-cell-cushion { font-size: 0.65rem; padding: 8px 0 !important; }
            .premium-calendar .fc-daygrid-day-number { font-size: 0.75rem; padding: 4px 6px !important; }
            .premium-calendar .fc-event { font-size: 0.65rem !important; padding: 2px 4px !important; margin: 1px 2px !important; border-radius: 4px !important;}
            /* Memaksa list event tetap rapi di layar kecil */
            .premium-calendar .fc-daygrid-event-harness { margin-top: 1px !important; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            // Deteksi ukuran layar
            var isMobile = window.innerWidth < 640;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                // Tinggi dinamis: lebih pendek di HP agar tidak memenuhi layar
                height: isMobile ? 500 : 650, 
                headerToolbar: {
                    // Menyederhanakan toolbar di HP
                    left: isMobile ? 'prev,next' : 'prev,next today',
                    center: 'title',
                    right: isMobile ? 'today' : 'dayGridMonth'
                },
                buttonText: {
                    today: 'Hari Ini',
                    month: 'Bulan'
                },
                events: "{{ route('api.jadwal-dokter') }}",
                dateClick: function(info) {
                    @this.set('selectedDate', info.dateStr);
                    window.dispatchEvent(new CustomEvent('open-modal'));
                }
            });
            calendar.render();

            Livewire.on('refreshCalendar', () => {
                calendar.refetchEvents();
            });
        });
    </script>
</div>