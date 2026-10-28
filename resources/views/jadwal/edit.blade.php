@extends('layouts.app')
@section('title', 'Manajemen Jadwal / Edit Jadwal')

@section('content')
{{-- Layout disamakan persis dengan halaman Create agar ada konsistensi UI/UX bagi pengguna --}}
<div class="px-3 md:px-6 py-4 md:py-6 min-h-screen">
    
    <div class="mb-4">
        <a href="{{ route('jadwal.index') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-800 hover:-translate-x-1 transition-all duration-200">
            <i class="fas fa-arrow-left mr-1.5"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm p-4 md:p-8 border border-slate-100">
        <div class="mb-5 md:mb-8 border-b border-slate-100 pb-4 md:pb-6 flex items-center">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 shrink-0">
                <i class="fas fa-edit text-sm md:text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg md:text-2xl font-black text-slate-800 leading-tight">Edit Jadwal MCU</h1>
                <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-0.5">Perbarui tanggal, dokter, atau paket pemeriksaan pasien.</p>
            </div>
        </div>
        
        {{-- Memanggil komponen Livewire yang sama dengan halaman Create, namun mengirimkan parameter 'jadwalId' --}}
        {{-- Parameter ini memicu metode mount() di backend Livewire untuk memuat data (populate) ke dalam form input --}}
        <livewire:jadwal.create-karyawan-form :jadwalId="$jadwal->id" />
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('jadwal-created', (event) => {
            const { type, message } = event[0];
            Swal.fire({
                title: type === 'success' ? 'Berhasil Diperbarui!' : 'Gagal!',
                text: message,
                icon: type,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#2563eb', // Warna biru untuk membedakan aksi edit dengan aksi tambah
                customClass: { popup: 'rounded-2xl' }
            });
        });
    });
</script>
@endsection