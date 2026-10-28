@extends('layouts.app')
@section('title', 'Tambah Jadwal MCU')

@section('content')
{{-- Kontainer utama dengan padding yang direduksi pada perangkat mobile (px-3 py-4) untuk memaksimalkan ruang layar --}}
<div class="px-3 md:px-6 py-4 md:py-6 min-h-screen">
    
    {{-- Navigasi kembali menggunakan tombol berukuran kecil (text-xs) agar proporsional di layar HP --}}
    <div class="mb-4">
        <a href="{{ route('jadwal.index') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-800 hover:-translate-x-1 transition-all duration-200">
            <i class="fas fa-arrow-left mr-1.5"></i> Kembali
        </a>
    </div>

    {{-- Wrapper konten dibungkus dengan kartu (card) berbayang halus --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm p-4 md:p-8 border border-slate-100">
        
        {{-- Header form dengan flexbox untuk menyejajarkan ikon dan teks judul --}}
        <div class="mb-5 md:mb-8 border-b border-slate-100 pb-4 md:pb-6 flex items-center">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center mr-3 shrink-0">
                <i class="fas fa-calendar-plus text-sm md:text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg md:text-2xl font-black text-slate-800 leading-tight">Buat Jadwal MCU Baru</h1>
                <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-0.5">Lengkapi informasi di bawah untuk menjadwalkan pemeriksaan.</p>
            </div>
        </div>
        
        {{-- Memanggil komponen Livewire yang menangani state form dan proses penyimpanan data ke database --}}
        @livewire('jadwal.create-karyawan-form')
    </div>
</div>

{{-- Memuat library eksternal SweetAlert2 untuk menampilkan popup notifikasi yang lebih interaktif --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Event listener 'livewire:init' memastikan skrip ini hanya berjalan setelah core Livewire selesai dimuat di browser
    document.addEventListener('livewire:init', () => {
        // Mendengarkan event 'jadwal-created' yang di-dispatch (dikirim) dari backend controller Livewire
        Livewire.on('jadwal-created', (event) => {
            // Mengekstrak properti 'type' dan 'message' dari payload event yang dikirimkan server
            const { type, message } = event[0];
            
            // Memanggil fungsi SweetAlert2 untuk memunculkan modal dialog berdasarkan tipe notifikasi (success/error)
            Swal.fire({
                title: type === 'success' ? 'Berhasil!' : 'Gagal!',
                text: message,
                icon: type,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#dc2626',
                // customClass digunakan untuk menyuntikkan class Tailwind ke dalam elemen SweetAlert agar seragam dengan tema aplikasi
                customClass: { popup: 'rounded-2xl' }
            });
        });
    });
</script>
@endsection