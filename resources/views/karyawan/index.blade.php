@extends('layouts.app')
@section('title', 'Manajemen Pasien')

@section('content')
    {{-- Wrapper utama dengan padding minimum di perangkat kecil untuk efisiensi ruang --}}
    <div class="px-3 md:px-6 py-4 md:py-6 min-h-screen">
        
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <div>
                <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">Manajemen Pasien</h1>
                <p class="text-[10px] md:text-sm font-medium text-slate-500 mt-0.5">Basis data Karyawan PTST & Peserta Umum.</p>
            </div>
        </div>

        {{-- Container Livewire dibungkus card --}}
        <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden p-4 md:p-8">
            {{-- Render komponen Livewire pencarian karyawan --}}
            @livewire('search-karyawan')
        </div>
    </div>
@endsection

@push('scripts')
{{-- Load SweetAlert2 untuk notifikasi flash session yang interaktif --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Memastikan skrip dijalankan setelah DOM terbentuk sempurna
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#dc2626',
                customClass: { popup: 'rounded-2xl md:rounded-3xl' }
            });
        @endif
    });
</script>
@endpush