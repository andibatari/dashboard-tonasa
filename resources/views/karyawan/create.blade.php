@extends('layouts.app')
@section('title', 'Tambah Karyawan')

@section('content')
<div class="px-3 md:px-6 py-4 md:py-6 min-h-screen">
    
    {{-- Tombol navigasi riwayat kembali --}}
    <div class="mb-4 md:mb-6">
        <a href="javascript:history.back()" class="inline-flex items-center px-3 py-1.5 text-xs md:text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-800 transition-all">
            <i class="fas fa-arrow-left mr-1.5"></i> Kembali
        </a>
    </div>

    {{-- Container utama pendaftaran, dipadatkan paddingnya (p-4 md:p-8) --}}
    <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden p-4 md:p-8 max-w-7xl mx-auto">
        <div class="mb-5 md:mb-8 border-b border-slate-100 pb-4 flex items-center">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center mr-3 shrink-0">
                <i class="fas fa-user-plus text-sm md:text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg md:text-2xl font-black text-slate-800 leading-tight">Tambah Karyawan Baru</h1>
                <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-0.5">Lengkapi form identitas karyawan ke dalam database.</p>
            </div>
        </div>
        
        {{-- Load Livewire Component Form Registrasi Utama --}}
        @livewire('create-karyawan-form')
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Menangkap event flash setelah proses simpan di backend selesai
    Livewire.on('karyawanSaved', () => {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data karyawan sukses tersimpan.',
            icon: 'success',
            confirmButtonText: 'Lanjutkan',
            confirmButtonColor: '#dc2626',
            customClass: { popup: 'rounded-2xl md:rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect user secara otomatis ke halaman daftar karyawan
                window.location.href = "{{ route('karyawan.index') }}";
            }
        });
    });
</script>
@endpush
@endsection