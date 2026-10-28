@section('title', 'Pemindai Kode QR')

{{-- ROOT ELEMENT: Dihapus min-h-screen dan padding bawahnya agar tidak melebihi tinggi layar (over-scroll) --}}
<div class="flex flex-col items-center justify-center w-full h-full pt-4 md:pt-10">
    
    {{-- KARTU UTAMA --}}
    <div class="bg-white rounded-[2rem] shadow-[0_15px_40px_rgba(0,0,0,0.06)] border border-slate-100 w-full max-w-lg overflow-hidden relative">
        {{-- Dekorasi Atas --}}
        <div class="h-1.5 bg-gradient-to-r from-red-600 to-red-800 w-full"></div>
        
        <div class="p-5 md:p-6">
            {{-- Header Scanner --}}
            <div class="text-center mb-4">
                <div class="bg-red-50 text-red-600 w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-2 shadow-sm border border-red-100">
                    <i class="fas fa-qrcode text-xl"></i>
                </div>
                <h2 class="text-lg font-black text-slate-800 tracking-tight">Scanner Registrasi</h2>
                <p class="text-xs text-slate-500 font-medium mt-1">Arahkan kamera ke QR Code pasien.</p>
            </div>

            {{-- Container Kamera: Ideal untuk Webcam Laptop --}}
            <div class="bg-slate-900 rounded-2xl overflow-hidden shadow-inner p-1.5 relative mx-auto w-full max-w-[450px]">
                <div class="absolute inset-0 border border-slate-700/50 rounded-2xl pointer-events-none z-10"></div>
                <div id="qr-reader" class="w-full h-auto rounded-xl overflow-hidden bg-black relative" wire:ignore></div>
            </div>
            
            {{-- Status Box --}}
            <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-center flex flex-col items-center">
                @if ($message)
                    <div class="flex items-center gap-2 @if(strpos($message, 'berhasil') !== false) text-emerald-600 bg-emerald-50 @else text-red-600 bg-red-50 @endif px-3 py-1.5 rounded-lg font-bold text-[11px]">
                        <i class="fas @if(strpos($message, 'berhasil') !== false) fa-check-circle @else fa-exclamation-circle @endif"></i>
                        {{ $message }}
                    </div>
                @endif
                
                @if (!$patient)
                    <div class="text-slate-400 font-medium text-[11px] flex items-center animate-pulse">
                        <i class="fas fa-camera text-sm mr-2"></i> Menunggu pindaian kamera...
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL POPUP MODERN --}}
    @if ($showPopup && $patient && $jadwal)
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
        <div class="bg-white rounded-[2rem] shadow-2xl max-w-sm w-full p-6 md:p-8 text-center transform scale-100 animate-slide-up relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-emerald-50 rounded-full blur-2xl"></div>

            <div class="w-16 h-16 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 relative z-10 shadow-sm border border-emerald-200">
                <i class="fas fa-check text-3xl"></i>
            </div>
            
            <h3 class="text-xl font-black text-slate-800 mb-1 relative z-10">Data Ditemukan</h3>
            <p class="text-xs text-slate-500 font-medium mb-5 relative z-10">Lanjutkan proses untuk pasien ini?</p>
            
            <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl text-left mb-6 text-sm relative z-10">
                <div class="space-y-2.5 text-slate-700">
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Nama Pasien</span>
                        <span class="font-black text-slate-800 text-sm">{{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? $this->jadwal->nama_pasien }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Identitas (NIK/SAP)</span>
                        <span class="font-mono font-bold text-slate-600 text-sm">{{ $patient->nik_pasien ?? $patient->no_sap }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 border-t border-slate-200 pt-2.5 mt-1">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Tgl Jadwal</span>
                            <span class="font-bold text-slate-600 bg-white px-2 py-1 rounded-lg border border-slate-200 inline-block w-max text-xs"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($this->jadwal->tanggal_mcu)->format('d M') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Status</span>
                            <span class="font-bold px-2 py-1 rounded-lg text-[11px] w-max border shadow-sm
                                @if($this->jadwal->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200 
                                @elseif($this->jadwal->status === 'Present') bg-blue-50 text-blue-600 border-blue-200 
                                @else bg-emerald-50 text-emerald-600 border-emerald-200 @endif">
                                {{ $this->jadwal->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2.5 relative z-10">
                <button wire:click="continueRegistration" class="w-full py-3 rounded-xl font-bold text-white bg-slate-800 hover:bg-slate-700 shadow-lg hover:-translate-y-0.5 transition-all text-xs">
                    Ya, Lanjutkan Registrasi
                </button>
                <button wire:click="cancelRegistration" class="w-full py-3 rounded-xl font-bold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-colors text-xs">
                    Batalkan
                </button>
            </div>
        </div>
    </div>
    @endif

    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

        /* PERBAIKAN WARNA TEKS SCANNER */
        #qr-reader { border: none !important; color: #f8fafc !important; }
        #qr-reader a { color: #60a5fa !important; text-decoration: none; font-weight: bold; }
        #qr-reader a:hover { color: #93c5fd !important; text-decoration: underline; }
        #qr-reader span { color: #cbd5e1 !important; margin-bottom: 5px; display: inline-block; font-size: 0.75rem; text-align: center;}
        #qr-reader__dashboard_section_csr span { color: #ef4444 !important; } 
    </style>
</div>

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.pause(); 
            Livewire.dispatch('qrCodeScanned', { uuid: decodedText });
        }
        function onScanFailure(error) { /* Diamkan console spam */ }

        let html5QrcodeScanner;
        function startScanner() {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 }, 
                    aspectRatio: 1.333334 // Rasio 4:3 Webcam
                });
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            
            setTimeout(() => {
                const btnStart = document.getElementById('html5-qrcode-button-camera-start');
                const btnStop = document.getElementById('html5-qrcode-button-camera-stop');
                const btnFile = document.getElementById('html5-qrcode-button-file-selection');
                if(btnStart) btnStart.className = "bg-blue-600 text-white font-bold py-1.5 px-3 rounded-lg m-1 text-xs hover:bg-blue-700 transition";
                if(btnStop) btnStop.className = "bg-red-600 text-white font-bold py-1.5 px-3 rounded-lg m-1 text-xs hover:bg-red-700 transition";
                if(btnFile) btnFile.className = "bg-slate-700 text-white font-bold py-1.5 px-3 rounded-lg m-1 mt-3 text-xs hover:bg-slate-600 transition block mx-auto";
            }, 500);
        }

        document.addEventListener('livewire:initialized', () => { startScanner(); });
        Livewire.on('qrScanResumed', () => { html5QrcodeScanner.resume(); });
    </script>
@endpush